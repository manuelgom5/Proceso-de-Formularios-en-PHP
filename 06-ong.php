<?php
    //  Funciones necesarias desde archivo externo
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Estructura HTML, incluyendo hojas de estilo
    inicio_html("Ejercicio 6 ONG", ["/estilos/general.css", 
        "/estilos/formulario.css", "/estilos/bh.css", "/estilos/tablas.css"]);

    //  Lista de proyectos con claves y descripciones
    $listaProyectos = [
        "ap" => "Agua potable",
        "ep" => "Escuela de primaria",
        "pc" => "Placas solares",
        "cm" => "Centro médico"
    ];
?>

<?php
    //  Verificar si se ha enviado el formulario con POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //  Array para almacenar los errores de validación
        $errores = [];

        //  Validación del campo de email
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (empty($email)) {
            $errores['email'] = "Por favor, introduce un email válido.";
        }

        //  Validación del consentimiento de registro
        $consentimientoDatos = filter_input(INPUT_POST, "consentimiento", FILTER_VALIDATE_BOOLEAN);
        isset($consentimientoDatos) ? $consentimientoDatos = "Si" : $consentimientoDatos = "No";

        //  Validación del campo de cantidad
        $cantidad = filter_input(INPUT_POST, "cantidad", FILTER_SANITIZE_NUMBER_INT);
        $cantidad = filter_var($cantidad, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if (empty($cantidad)) {
            $errores['cantidad'] = "Introduce una cantidad válida.";
        }

        //  Validación del campo de proyecto
        $proyecto = filter_input(INPUT_POST, "proyecto", FILTER_SANITIZE_SPECIAL_CHARS);
        !empty($proyecto) && array_key_exists($proyecto, $listaProyectos)
            ? $proyecto = $listaProyectos[$proyecto] 
            : $errores['proyecto'] = "Por favor, introduce un proyecto.";

        //  Validación del campo de propuesta
        $propuesta = filter_input(INPUT_POST, "propuesta", FILTER_SANITIZE_SPECIAL_CHARS);
        $propuesta = trim($propuesta);
        if (strlen($propuesta) < 10 || strlen($propuesta) > 100) {
            $errores['propuesta'] = "Por favor, introduce un mensaje sobre la propuesta "
                . "(entre 10 y 200 caracteres).";
        }
    }   
?>

<!-- Formulario de propuesta de donantes de ONG -->
<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
    <fieldset>
        <legend>Propuestas donantes de ONG</legend>

        <!-- Campo para introducir el email -->
        <label for="email">Email</label>
        <input type="email" name="email" id="email" size="40" 
            value="<?= isset($email) ? htmlspecialchars($email) : "" ?>"/>

        <!-- Campo para marcar el consentimiento -->
        <label for="consentimiento">Autorizo registro</label>
        <input type="checkbox" name="consentimiento" id="consentimiento" 
            <?= isset($consentimientoDatos) ? "checked" : "unchecked" ?>/>

        <!-- Campo para marcar el consentimiento -->
        <label for="cantidad">Cantidad</label>
        <input type="number" name="cantidad" id="cantidad" 
            value="<?= isset($cantidad) ? htmlspecialchars($cantidad) : "" ?>"/>

        <!-- Campo para seleccionar el proyecto-->
        <label for="proyecto">Proyecto</label>
        <select name="proyecto" id="proyecto" size="1">
            <?php
                //  Recorrer la lista de proyectos y crear opciones en el select
                foreach ($listaProyectos as $clave => $valor) {
                echo "<option value='$clave' " 
                    . (isset($proyecto) && $valor === $proyecto ? "selected" : "") 
                    . ">$valor</option>";
            } ?>
        </select>

        <!-- Campo para la propuesta -->
        <label for="propuesta">Propuesta</label>
        <textarea id="propuesta" name="propuesta" rows="5" columns="20">
                <?= isset($propuesta) ? htmlspecialchars(trim($propuesta)) : "" ?>
        </textarea>
    </fieldset>
    <!-- Botón para enviar el formulario -->
    <input type="submit" name="operacion" id="operacion" value="Mostrar tabla"/>
</form>

<?php
    //  Si el formulario se ha enviado y no hay errores
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($errores)) {
            //  Definir los encabezados para la tabla que se mostrará
            $cabecera = ["Email", "Registro", "Cantidad", "Proyecto", "Propuesta"];
    
            //  Guardar los datos de la propuesta en un archivo CSV
            $nombreFichero = 'propuestas.csv';
            $ficheroExiste = !file_exists($nombreFichero);  //  Comprobar si el archivo ya existe
            $archivo = fopen($nombreFichero, 'a+'); //  Abrir en modo de escritura

            //  Si el archivo no existe, agregar una cabecera
            if ($ficheroExiste) {
                fputcsv($archivo, $cabecera);
            }

            //  Crear un array con la nueva propuesta Y un array que almacenará todas las propuestas
            $nuevaPropuesta = [$email, $consentimientoDatos, $cantidad,
                $proyecto, $propuesta];

            //  Verificar si ya existe una propuesta igual en el archivo
            $duplicados = fopen($nombreFichero, 'r');
            $hayDuplicados = false;
            while (($linea = fgetcsv($duplicados)) !== false) {
                if ($linea == $nuevaPropuesta) {
                    $hayDuplicados = true;
                    break;
                }
            }

            //  Si no hay duplicados, agregar la nueva propuesta al archivo
            if (!$hayDuplicados) {
                fputcsv($archivo, $nuevaPropuesta);
                echo "<p>La propuesta se ha registrado correctamente.</p>";
            }
            fclose($archivo);   //  Cerrar el archivo

            //  Abrir el archivo para mostrar los datos en una tabla
            $archivo = fopen($nombreFichero, 'r');
            $linea = fgetcsv($archivo); //  Leer la primera línea (cabecera)
            echo "<table><thead><tr>";
            //  Mostrar la cabecera de la tabla
            foreach ($linea as $campo) {
                echo "<th>$campo</th>";
            }
            echo "</tr></thead><tbody>";

            //  Leer y mostrar cada fila del archivo CSV
            while (($linea = fgetcsv($archivo)) !== FALSE) {
                echo "<tr>";
                foreach ($linea as $campo) {
                    echo "<td>$campo</td>"; //  Mostrar cada campo de la fila
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
            
            fclose($archivo);
        ?>
    <?php
        } else {
            //  Mostrar los errores si existen
            foreach ($errores as $error) {
                echo "<h3>$error</h3>";
            }
        } 
    }

    //  Finalizar el HTML
    fin_html();
?>