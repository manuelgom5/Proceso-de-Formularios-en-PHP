<?php
    //  Definimos la constante para la ruta donde se almacenaran las fotos
    define("DIRECTORIO_FOTOGRAFIAS", $_SERVER['DOCUMENT_ROOT'] . "/fotos");

    //  Incluimos un archivo con funciones comunes
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Iniciamos el HTML con el título y estilos
    inicio_html("Ejercicio 8 Subir Fotografías", ["/estilos/general.css",
        "/estilos/formulario.css"]);

    //  Establecemos límites por tipo de archivo en bytes, si no se pasan por 
    //  POST se asignan los valores por defecto
    $limite_jpg = filter_input(INPUT_POST, "limite_jpg", FILTER_SANITIZE_NUMBER_INT) ?? 250 * 1024; 
    $limite_png = filter_input(INPUT_POST, "limite_png", FILTER_SANITIZE_NUMBER_INT) ?? 225 * 1024;
    $limite_webp = filter_input(INPUT_POST, "limite_webp", FILTER_SANITIZE_NUMBER_INT) ?? 200 * 1024;

    //  Array asociativo con los límites por tipo de archivo
    $limitePorTipo = [
        "jpeg" => $limite_jpg,
        "png" => $limite_png,
        "webp" => $limite_webp
    ];

    //  Comprobamos si el formulario ha sido enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        //  Array donde guardamos los datos validados y los errores
        $datosValidados = [];
        $errores = [];

        //  Validamos el campo "login": debe tener entre 2 y 30 caracteres 
        //  y solo letras minúsculas y números
        $login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_SPECIAL_CHARS);
        isset($login) && strlen($login) > 2 && strlen($login) < 30 
            && preg_match("/^[a-z0-9]+$/", $login) 
                ? $datosValidados['login'] = $login 
                : $errores['login'] = "Por favor, introduce un nombre de usuario válido.";

        //  Validamos el campo "titulo": debe tener entre 2 y 50 caracteres
        $titulo = filter_input(INPUT_POST, "titulo", FILTER_SANITIZE_SPECIAL_CHARS);
        isset($titulo) && strlen($titulo) > 2 && strlen($titulo) < 50 
            ? $datosValidados['titulo'] = $titulo 
            : $errores['titulo'] = "Por favor, introduce un título para la fotografía.";


        // Si no hay errores, intentamos crear el directorio para el usuario
        if (empty($errores)) {
            if (!is_dir(DIRECTORIO_FOTOGRAFIAS . "/$login") 
                && !mkdir(DIRECTORIO_FOTOGRAFIAS . "/$login", 0750)) {
                $errores['directorio'] = "El directorio no se pudo crear.";
            }
        }
    }
?>

<!-- Formulario para subir fotos -->
<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="<?= 1024*150 ?>"/>
    <input type="hidden" name="limite_jpg" id="limite_jpg" value="<?= 1024*250 ?>"/>
    <input type="hidden" name="limite_png" id="limite_png" value="<?= 1024*225 ?>"/>
    <input type="hidden" name="limite_webp" id="limite_webp" value="<?= 1024*200 ?>"/>
    <fieldset>
        <legend>Formulario para subida de fotografías</legend>

        <!-- Campo para ingresar el login -->
        <label for="login">Login</label>
        <input type="text" name="login" id="login" size="30" 
            value="<?= isset($login) ? htmlspecialchars($login) : "" ?>" autofocus/>

        <!-- Campo para subir la foto -->
        <label for="foto">Foto</label>
        <input type="file" name="foto" id="foto" accept="image/jpeg, image/png, image/webp"/>

        <!-- Campo para ingresar el título -->
        <label for="titulo">Título</label>
        <input type="text" name="titulo" id="titulo" size="50" 
            value="<?= isset($titulo) ? htmlspecialchars($titulo) : "" ?>"/>
    </fieldset>
    <input type="submit" name="operacion" id="operacion" value="Subir"/>
</form>

<?php
    //  Comprobamos si el formulario ha sido enviado y si no hay errores
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto']) && empty($errores)) {
        //  Validamos los posibles errores en la subida de la foto
        if ($_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            $errores['foto'] = "Introduce una imagen.";
        } elseif ($_FILES['foto']['error'] === UPLOAD_ERR_INI_SIZE) {
            $errores['foto'] = "La imagen supera el upload_max_filesize.";
        } elseif ($_FILES['foto']['error'] === UPLOAD_ERR_FORM_SIZE) {
            $errores['foto'] = "La imagen supera el MAX_FILE_SIZE.";
        } else {
            //  Obtenemos la extensión del archivo subido
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            
            //  Verificamos si el tamaño del archivo supera el límite permitido por el tipo
            if (isset($limitePorTipo[$extension]) 
                && $limitePorTipo[$extension] < $_FILES['foto']['size']) {
                $errores['foto'] = "El tamaño del archivo es demasiado grande.";
            } else {
                //  Comprobamos que el tipo MIME del archivo sea uno permitido
                $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];
    
                $tipoMime1 = mime_content_type($_FILES['foto']['tmp_name']);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $tipoMime2 = finfo_file($finfo, $_FILES['foto']['tmp_name']);
                finfo_close($finfo);
                $tipoMime3 = $_FILES['foto']['type'];
    
                //  Verificamos que los tres tipos MIME coincidan y que el tipo osea permitido
                if ($tipoMime1 == $tipoMime2 && $tipoMime2 == $tipoMime3 
                    && in_array($tipoMime1, $tiposPermitidos)) {
                    //  Movemos el archivo subido al directorio correspondiente
                    $ruta = DIRECTORIO_FOTOGRAFIAS . "/$login/" . $_FILES['foto']['name'];
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
                        //  Mostramos los archivos subidos en el directorio
                        $archivos = scandir(DIRECTORIO_FOTOGRAFIAS . "/$login");
                        $archivos = array_diff($archivos, array('.', '..'));
                            
                        echo "<h3>Archivos subidos:</h3>";
                        foreach ($archivos as $archivo) {
                            echo $archivo . "<br>";
                        }
                    } else {
                        $errores['foto'] = "Error al guardar el archivo.";
                    }
                } else {
                    $errores['foto'] = "Extensión del archivo no adecuada.";
                }
            }
        } 
    }

    //  Mostramos los errores si los hay
    foreach($errores as $clave => $valor) {
        echo "<h3>$valor</h3>";
    }

    //  Finalizamos el HTML
    fin_html();
?>