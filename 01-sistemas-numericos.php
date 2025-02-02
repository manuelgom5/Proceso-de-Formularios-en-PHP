<?php
    //  Incluye las funciones necesarias desde un archivo externo
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Inicia el HTML con título y enlaces a archivos de estilo
    inicio_html("Ejercicio 1", ["/estilos/general.css", "/estilos/formulario.css"]);

    //  Función para mostrar el formulario y los resultados
    function mostrarFormulario($resultado = null) { ?>
        <form method="post">
            <fieldset>
                <legend>Conversión de números a diferentes sistemas numéricos</legend>

                <label for="numero">Número entero</label>
                <input type="number" name="numero" id="numero" required/>
            </fieldset>
            <input type="submit" value="Convertir"/>
        </form>
    <?php
        //  Si hay resultados, los mostramos
        if ($resultado) {
            echo "<h3>Resultados de la conversión:</h3>";
            echo "<p>Decimal: {$resultado['decimal']}</p>";
            echo "<p>Binario: {$resultado['binario']}</p>";
            echo "<p>Octal: {$resultado['octal']}</p>";
            echo "<p>Hexadecimal: {$resultado['hexadecimal']}</p>";
        }
    }

    //  Si el formulario ha sido enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //  Obtener el número introducido y validarlo
        $numero = filter_input(INPUT_POST, 'numero', FILTER_VALIDATE_INT);

        //  Si el número es válido, realizar las conversiones
        if ($numero !== false) {
            $resultado = [
                'decimal' => $numero,
                'binario' => decbin($numero),
                'octal' => decoct($numero),
                'hexadecimal' => dechex($numero)
            ];
            //  Llamar a la función para mostrar el formulario con los resultados
            mostrarFormulario($resultado);
        } else {
            echo "<p>Por favor, introduce un número entero válido.</p>";
            mostrarFormulario();
        }
    } else {
        //  Si no se ha enviado el formulario, mostrar solo el formulario
        mostrarFormulario();
    }

    fin_html();
?>