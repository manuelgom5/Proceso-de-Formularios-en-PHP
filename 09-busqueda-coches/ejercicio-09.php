<!-- Manuel Gómez Ruiz -->
<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Inicializa la estructura HTML y carga los archivos CSS necesarios
    inicio_html("Examen", ["/estilos/general.css", "/estilos/formulario.css",
        "/estilos/bh.css", "/estilos/tablas.css"]);

    //  Definición de los tipos de vehículos y marcas disponibles
    $tipos = [
        "tm" => "Turismo",
        "ft" => "Furgoneta"
    ];

    $marcas = [
        "ft" => "Fiat",
        "ol" => "Opel",
        "ms" => "Mercedes"
    ];

    //  Validación de los datos recibidos por POST
    if ($_SERVER['REQUEST_METHOD'] === "POST") { 
        $datosValidados = [];
        $errores = [];

        //  Validación del correo electrónico
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $email = trim($email);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        !empty($email) && strlen($email) > 8 && strlen($email) < 50
            ? $datosValidados['email'] = $email 
            : $errores['email'] = "Por favor, introduce un email válido.";
        
        //  Validación del tipo de vehículo
        $tipo = filter_input(INPUT_POST, "tipo", FILTER_SANITIZE_SPECIAL_CHARS);
        isset($tipo) && array_key_exists($tipo, $tipos) 
            ? $datosValidados['tipo'] = $tipos[$tipo]
            : $errores['tipo'] = "Por favor, selecciona un tipo de coche válido.";

        //  Validación de la marca del vehículo
        $marca = filter_input(INPUT_POST, "marca", FILTER_SANITIZE_SPECIAL_CHARS);
        isset($marca) && array_key_exists($marca, $marcas) 
            ? $datosValidados['marca'] = $marcas[$marca]
            : $errores['marca'] = "Por favor, selecciona una marca de coche válida." ;

        //  Validación de la antigüedad del coche
        $antiguedad = filter_input(INPUT_POST, "antiguedad", FILTER_SANITIZE_NUMBER_INT);
        $antiguedad = filter_var(
            $antiguedad, 
            FILTER_VALIDATE_INT, 
            ['options' => ['min_range' => 1, 'max_range' => 5]] 
        );

        isset($antiguedad) && !empty($antiguedad) 
            ? $datosValidados['antiguedad'] = $antiguedad
            : $errores['antiguedad'] = 'El valor de antigüedad debe ser numérico '
                . 'y estar comprendido entre 1 y 5.';

        //  Validación de la ITV (checkbox)
        $itv = filter_input(INPUT_POST, "itv", FILTER_VALIDATE_BOOLEAN);
        isset($itv) ? $datosValidados['itv'] = "Si" : $datosValidados['itv'] = "No";

        //  Límite de tamaño para el archivo CSV
        $limiteCsv = filter_input(INPUT_POST, "limiteCsv", FILTER_VALIDATE_INT);
    }
    ?>

        <!-- Formulario HTML -->
        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
            <!-- Generamos los campos del formulario para email, tipo de coche, marca, 
                 antigüedad, ITV y archivo -->
            <fieldset>

                <input type="hidden" name="limiteCsv" id="limiteCsv" value="<?= 1024*200 ?>"/>
                <legend>Búsqueda de vehículos</legend>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= isset($email) 
                    ? htmlspecialchars($email) : '' ?>" size="50" autofocus/>

                <label for="tipo">Tipo</label>
                <div>
                    <?php foreach ($tipos as $clave => $valor) {
                        echo "<input type='radio' name='tipo' id='$clave' value='$clave' "
                            . (isset($tipo) && $tipo == $clave ? "checked" : "" ) . "/>";
                        echo "<label for='$clave'>$valor</label>";
                    } ?>
                </div>

                <label for="marca">Marca</label>
                <select name="marca" id="marca" size="1">
                    <?php foreach ($marcas as $clave => $valor) {
                        echo "<option name='marca' id='$clave' value='$clave' " . 
                            (isset($marca) && $marca == $clave ? "selected" : "") 
                            . ">$valor</option>";
                    } ?>
                </select>

                <label for="antiguedad">Antigüedad</label>
                <input type="number" name="antiguedad" id="antiguedad" min="1" max="5"
                    value="<?= isset($antiguedad) ? htmlspecialchars($antiguedad) : '' ?>"/>

                <label for="itv">Con ITV</label>
                <input type="checkbox" name="itv" id="itv" <?= isset($itv) ? 'checked' : '' ?>/>

                <label for="archivo">Archivo</label>
                <input type="file" name="archivo" id="archivo" accept="text/csv"/>

            </fieldset>
            <input type="submit" name="operacion" id="operacion" value="Enviar"/>
        </form><br>
<?php
    //  Validación y manejo de archivo subido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['archivo'])) {
            //  Verifica si hubo un error en la carga del archivo
            if ($_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores['archivo'] = "Un error ocurrió durante la subida, intentelo más tarde.";
            } elseif ($_FILES['archivo']['error'] === UPLOAD_ERR_INI_SIZE) {
                $errores['archivo'] = "El tamaño del archivo es superior a la"
                    . "directiva upload_max_filesize.";
            } elseif ($_FILES['archivo']['error'] === UPLOAD_ERR_FORM_SIZE) {
                $errores['archivo'] = "El tamaño del archivo es mayor que MAX_FILE_SIZE.";
            } elseif ($_FILES['archivo']['size'] > $limiteCsv) {
                $errores['archivo'] = "El tamaño del archivo es mayor que el"
                    . "límite de $limiteCsv bytes.";
            } else {
                //  Validación de la extensión y tipo MIME del archivo
                $tiposPermitidos = ["text/csv"];
                $extensionesVálida = ["csv"];
                    
                //  Se valida que el archivo tenga la extensión y MIME correctos
                $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
                $tipoMime1 = mime_content_type($_FILES['archivo']['tmp_name']);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $tipoMime2 = finfo_file($finfo, $_FILES['archivo']['tmp_name']);
                finfo_close($finfo);
                $tipoMime3 = $_FILES['archivo']['type'];

                //  Si el archivo es válido, se lee y procesa
                if (in_array($extension, $extensionesVálida) && $tipoMime1 == $tipoMime2
                    && in_array($tipoMime3, $tiposPermitidos)) {
                    $datos = [];
                    if (($archivo = fopen($_FILES['archivo']['tmp_name'], "r")) !== FALSE) {
                        $cabecera = fgetcsv($archivo);
                        while (($fila = fgetcsv($archivo)) !== FALSE) {
                            $datos[] = ['tipo' => trim($fila[0]), 'marca' => trim($fila[1]),
                                'antiguedad' => trim($fila[2]), 'itv' => trim($fila[3])];
                        }
                        fclose($archivo);
                    } else {
                        $errores['permisos'] = "No ha sido posible leer el archivo.";
                    }
                } else {
                    $errores['extension'] = "La extensión o contenido del archivo no es válido.";
                }
            }
        } else {
            $errores['archivo'] = "Por favor, introduce un archivo.";
        }

        //  Si hay errores, se muestran en pantalla, si no, se procesan y muestran los datos válidos
        if (!empty($errores)) {
            foreach ($errores as $clave => $valor) {
                echo "<h3>$valor</h3>";
            }
        } else {
            //  Filtra y muestra los datos que coinciden con los parámetros de búsqueda
            foreach ($datos as $clave => $valor) {
                if ($valor['tipo'] != $datosValidados['tipo']
                    || $valor['marca'] != $datosValidados['marca']
                    || $valor['antiguedad'] != $datosValidados['antiguedad']
                    || $valor['itv'] != $datosValidados['itv']) {
                        unset($datos[$clave]);
                }
            }

            //  Muestra los resultados en una tabla si hay coincidencias
            if ($datos) {
                echo "<table><tr>";
                foreach ($cabecera as $campo) {
                    echo "<th>$campo</th>";
                }
                echo "</tr>";
            
                foreach ($datos as $clave => $valor) {
                    echo "<tr>";
                    foreach ($valor as $value => $index) {
                        echo "<td>$index</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<h3>No disponemos de un coche con esos parámetros.</h3>";
            }
        }

    }

    fin_html();
?>