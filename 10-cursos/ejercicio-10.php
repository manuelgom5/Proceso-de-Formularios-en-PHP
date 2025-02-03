<?php
    //  Ruta donde se almacenarán las tarjetas de desempleo
    define("RUTA_TARJETAS", $_SERVER['DOCUMENT_ROOT'] . "/tema-2/repasoExamen/10-cursos/tarjetas/");

    //  Funciones auxiliares de la página
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Función de inicio para generar la cabecera HTML con los estilos
    inicio_html("Ejercicio 10", ["/estilos/general.css", "/estilos/formulario.css",
        "/estilos/tablas.css"]);

    //  Datos de los cursos disponibles
    $datosCursos = [
        "of" => ["nombre" => "Ofimatica", "precio" => 100],
        "pr" => ["nombre" => "Programación", "precio" => 200],
        "rep" => ["nombre" => "Reparación de ordenadores", "precio" => 150]
    ];
?>

<!-- Formulario de inscripción -->
<form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data">
    <fieldset>
        <legend>Introduce tus datos</legend>

        <!-- Campo para email -->
        <label for="email">Email</label>
        <input type="email" name="email" id="email" size="75" autofocus/>

        <!-- Selección de cursos con checkbox -->
        <label for="cursos">Cursos</label>
        <div>
            <?php foreach ($datosCursos as $clave => $valor) {
                echo "<input type='checkbox' name='cursos[]' id='$clave' value='$clave'/>";
                echo "<label for='$clave'>{$valor['nombre']} - {$valor['precio']}&euro;</label>";
            } ?>
        </div>

        <!-- Campo para el número de clases presenciales -->
        <label for="numClases">Nº Clases Presenciales</label>
        <input type="number" name="numClases" id="numClases" min="5" max="10"/>

        <!-- Situación de desempleo -->
        <label for="situacion">Situación de desempleo</label>
        <input type="checkbox" name="situacion" id="situacion"/>

        <!-- Campo para cargar la tarjeta de desempleo -->
        <label for="tarjeta">Tarjeta de desempleo</label>
        <input type="file" name="tarjeta" id="tarjeta" accept="application/pdf"/>
    </fieldset>
    <!-- Botón de submit para enviar el formulario -->
    <input type="submit" name="operacion" value="Registrar"/>
</form>

<?php
    //  Verificar si el formulario fue enviado por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $datosFormulario = [];  //  Inicialización del array para los datos procesados
        $errores = [];  //  Inicialización del array para los errores

        //  Validar y filtrar el email
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        !empty($email) 
            ? $datosFormulario['email'] = $email 
            : $errores['email'] = "Por favor, introduce un email válido.";

        //  Filtrar y verificar los cursos seleccionados
        $cursos = filter_input(INPUT_POST, "cursos", 
            FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (!empty($cursos)) {
            foreach ($cursos as &$curso) {
                if (array_key_exists($curso, $datosCursos)) {
                    $curso = $datosCursos[$curso];
                    $datosFormulario['cursos'][] = $curso;
                }
                unset($curso);
            }
        } else {
            $errores['cursos'] = "Por favor, seleccione algún curso.";
        }

        //  Validar el número de clases
        $numClases = filter_input(INPUT_POST, "numClases", FILTER_SANITIZE_NUMBER_INT);
        $numClases = filter_var($numClases, FILTER_VALIDATE_INT, 
            ['options' => ['min_range' => 5, 'max_range' => 10]]);
        empty($numClases)
            ? $errores['clases'] = "Introduce un valor entre 5 y 10 para el número de clases."
            : $datosFormulario['clases'] = $numClases;

        //  Verificar si la situación de desempleo está marcada
        $situacion = filter_input(INPUT_POST, "situacion", FILTER_VALIDATE_BOOLEAN);
        isset($situacion)
            ? $datosFormulario['situacion'] = "Desempleo"
            : $datosFormulario['situacion'] = "Con empleo";

        //  Comprobar si la tarjeta de desempleo se ha cargado correctamente
        $descuentoValido = false;
        if ($datosFormulario['situacion'] == 'Desempleo' && isset($_FILES['tarjeta'])) {
            if ($_FILES['tarjeta']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores['tarjeta'] = "Por favor, introduce un fichero.";
            } elseif ($_FILES['tarjeta']['error'] !== UPLOAD_ERR_OK) {
                $errores['tarjeta'] = "Error al carga el archivo.";
            } elseif ($_FILES['tarjeta']['error'] === UPLOAD_ERR_INI_SIZE) {
                $errores['tarjeta'] = "El fichero ha excedido el tamaño de upload_max_filesize.";
            } elseif ($_FILES['tarjeta']['error'] === UPLOAD_ERR_FORM_SIZE) {
                $errores['tarjeta'] = "El fichero ha excedido el tamaño de MAX_FILE_SIZE.";
            } else {
                //  Comprobar que el archivo es un PDF
                $tiposPermitidos = ['application/pdf'];
                $extension = pathinfo($_FILES['tarjeta']['name'], PATHINFO_EXTENSION);
                $tipoMime1 = mime_content_type($_FILES['tarjeta']['tmp_name']);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $tipoMime2 = finfo_file($finfo, $_FILES['tarjeta']['tmp_name']);
                finfo_close($finfo);
                $tipoMime3 = $_FILES['tarjeta']['type'];

                //  Si el archivo es válido, moverlo al directorio correspondiente
                if ($tipoMime1 == $tipoMime2 && $tipoMime2 == $tipoMime3 
                    && in_array($tipoMime1, $tiposPermitidos) && $extension == 'pdf') {

                    if (!is_dir(RUTA_TARJETAS) && !mkdir(RUTA_TARJETAS, 0750, true)) {
                        $errores['tarjeta'] = "No se ha podido crear los directorios necesarios.";
                    }
                    
                    $nombreFichero = RUTA_TARJETAS . "{$datosFormulario['email']}.$extension";

                    !move_uploaded_file($_FILES['tarjeta']['tmp_name'], $nombreFichero)
                        ? $errores['tarjeta'] = "Error al mover el fichero al directorio."
                        : $descuentoValido = true;  //  Si todo es correcto, se aplica descuento
                } else {
                    $errores['tarjeta'] = "La extensión o contenido del archivo no es válida.";
                }
            }
        }

        //  Si no hay errores, mostrar la tabla con los datos
        if (empty($errores)) {
    ?>
    <br>
    <table>
        <thead>
            <tr>
            <?php
                //  Mostrar los encabezados de la tabla con los datos del formulario
                foreach ($datosFormulario as $clave => $valor) {
                    echo "<th>" . ucwords($clave) . "</th>";    
                }
                echo "<th>Precio Final</th>";
            ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    //  Cálculo del precio final con posibles descuentos
                    $datosFormulario['clasesPrecio'] = $datosFormulario['clases'] * 10;
                    if ($descuentoValido) {
                        //  Aplicar descuento a los cursos y al precio de las clases
                        foreach ($datosFormulario['cursos'] as $clave => &$valor) {
                            $valor['precio'] *= 0.9;    //  Aplicar un descuento del 10%
                            unset($valor);
                        }
                        $datosFormulario['clasesPrecio'] *= 0.9;    //  Descuento en las clases
                    }
                    $precioFinal = 0;
                    echo "<td>{$datosFormulario['email']}</td>";
                    echo "<td>";
                    foreach ($datosFormulario['cursos'] as $clave => $valor) {
                        echo "<p>{$valor['nombre']} => {$valor['precio']}&euro;";
                        $precioFinal += $valor['precio'];   //  Acumular el precio de los cursos
                    }
                    echo "</td>";
                    echo "<td>{$datosFormulario['clases']} clases => "
                        . "{$datosFormulario['clasesPrecio']}&euro;";
                    //  Acumular el precio de las clases
                    $precioFinal += $datosFormulario['clasesPrecio'];
                    echo "<td>{$datosFormulario['situacion']}</td>";
                    echo "<td>$precioFinal&euro;</td>"; //  Mostrar el precio final
                ?>
            </tr>
        </tbody>
    </table>

<?php
        } else {
            //  Si hay errores, mostrar los mensajes de error
            foreach ($errores as $error) {
                echo "<h3>$error</h3>";
            }
        }
        //  Finalizar la página HTML
        fin_html();
    }
?>