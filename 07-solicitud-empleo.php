<?php
    //  Define la ruta donde se almacenarán los currículums
    define("DIRECTORIO_CURRICULUMS", $_SERVER['DOCUMENT_ROOT'] . "/curriculums");

    //  Incluye el archivo de funciones (para la cabecera y pie de página)
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Inicio de la página HTML, cargando estilos específicos para el formulario
    inicio_html("Solicitud Empleo", ["/estilos/formulario.css", "/estilos/general.css"]);
?>

<?php
    //  Verifica si el formulario ha sido enviado por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $datosFormulario = [];  //  Array para almacenar los datos del formulario
        $errores = [];  //  Array para almacenar los errores de validación

        //  Validación del DNI
        $dni = filter_input(INPUT_POST, "dni", FILTER_SANITIZE_SPECIAL_CHARS);
        isset($dni) && preg_match("/^[0-9]{8}(?!I|Ñ|O|U)[A-Z]$/", $dni) 
            ? $datosFormulario['dni'] = $dni 
            : $errores['dni'] = "Por favor, introduce un DNI válido.";

        //  Validación del nombre
        $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_SPECIAL_CHARS);
        $nombre = trim($nombre);    //  Elimina espacios al principio y al final
        $nombre = preg_replace("/\s+/", " ", $nombre);  //  Reemplaza espacios consecutivos
        isset($nombre) && strlen($nombre) > 2 && strlen($nombre) < 50 &&
            preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre) 
            ? $datosFormulario['nombre'] = $nombre 
            : $errores['nombre'] = "Por favor, introduce un nombre válido.";

        //  Validación del consentimiento
        $consentimiento = filter_input(INPUT_POST, "consentimiento", FILTER_VALIDATE_BOOLEAN);
        isset($consentimiento) ? $datosFormulario['consentimiento'] = "Si" 
            : $errores['consentimiento'] = "Error. Para continuar debes aceptar el "
                . "registro de los datos personales.";

        //  Límite de tamaño de archivo PDF en bytes
        $limitePdf = filter_input(INPUT_POST, "limite_pdf", FILTER_SANITIZE_NUMBER_INT);
        $limitePdf = filter_var($limitePdf, FILTER_VALIDATE_INT);

        //  Si no hay errores, se intenta crear el directorio donde se almacenarán los currículum 
        if (empty($errores)) {
            if (!is_dir(DIRECTORIO_CURRICULUMS) && !mkdir(DIRECTORIO_CURRICULUMS, 0750)) {
                $errores['directorio'] = "Error al crear el directorio.";
            }
        }
    }
?>

<!-- Formulario HTML para la Solicitud de empleo -->
<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
    <!-- Define el límite máximo del archivo (en bytes) -->
    <input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="<?= 1024*1024 ?>"/>
    <!-- Define el límite de tamaño para los archivos PDF (en bytes) -->
    <input type="hidden" name="limite_pdf" id="limite_pdf" value="<?= 1024*500 ?>"/>
    <fieldset>
        <legend>Solicitud de empleo</legend>

        <!-- Campo para ingresar el DNI -->
        <label for="dni">DNI</label>
        <input type="text" name="dni" id="dni" size="9" autofocus 
            value="<?= isset($dni) ? htmlspecialchars($dni) : "" ?>"/>

        <!-- Campo para subir el currículum (PDF) -->
        <label for="curriculum">Curriculum</label>
        <input type="file" name="curriculum" id="curriculum" accept="application/pdf"/>

        <!-- Campo para ingresar el nombre -->
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" size="40" 
            value="<?= isset($nombre) ? htmlspecialchars($nombre) : "" ?>"/>

        <!-- Checkbox para aceptar el registro de datos personales -->
        <label for="consentimiento">Aceptación registro datos personales</label>
        <input type="checkbox" name="consentimiento" id="consentimiento" 
            <?= isset($consentimiento) ? "checked" : "unchecked" ?>/>
    </fieldset>
    <input type="submit" name="operacion" id="operacion" value="Enviar"/>
</form>

<?php
    //  Procesa los datos si el formulario fue enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //  Verifica si se ha subido un archivo
        if (isset($_FILES['curriculum']) && empty($errores)) {
            //  Revisa si ha ocurrido algún error con el archivo
            if ($_FILES['curriculum']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores['archivo'] = "Error. No se ha subido el archivo.";
            } elseif ($_FILES['curriculum']['error'] === UPLOAD_ERR_INI_SIZE) {
                $errores['archivo'] = "Error. El tamaño del archivo supera a upload_max_filesize.";
            } elseif ($_FILES['curriculum']['error'] === UPLOAD_ERR_FORM_SIZE) {
                $errores['archivo'] = "Error. El tamaño del archivo supera a MAX_FILE_SIZE.";
            } elseif ($_FILES['curriculum']['size'] > $limitePdf) {
                $errores['archivo'] = "Error. El tamaño del archivo supera los $limitePdf bytes.";
            } else {
                //  Verifica que el archivo sea un PDF
                $tiposPermitidos = ["application/pdf"];
    
                //  Verificación del tipo MIME
                $tipoMime1 = mime_content_type($_FILES['curriculum']['tmp_name']);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $tipoMime2 = finfo_file($finfo, $_FILES['curriculum']['tmp_name']);
                finfo_close($finfo);
                $tipoMime3 = $_FILES['curriculum']['type'];
    
                //  Si el tipo MIME es correcto, guarda el archivo en el directorio correspondiente
                if ($tipoMime1 == $tipoMime2 && $tipoMime2 == $tipoMime3 
                    && in_array($tipoMime1, $tiposPermitidos)) {
                    $extension = pathinfo($_FILES['curriculum']['name'], PATHINFO_EXTENSION);
                    $ruta = DIRECTORIO_CURRICULUMS . "/$dni." . strtolower($extension);
                    if (!move_uploaded_file($_FILES['curriculum']['tmp_name'], $ruta)) {
                        $errores['archivo'] = "Error al guardar el archivo.";
                    }
                } else {
                    $errores['archivo'] = "La extensión del archivo no es la adecuada.";
                }
            }
        }

        //  Muestra los errores o el mensaje de éxito
        if (!empty($errores)) {
            foreach ($errores as $clave => $valor) {
                echo "<h3>$valor</h3>";
            }
        } else {
            echo "<h3>Solicitud registrada con éxito.</h3>";
        }

    }

    //  Finaliza el HTML
    fin_html();
?>