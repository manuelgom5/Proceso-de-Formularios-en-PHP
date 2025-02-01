<?php
    //  Definimos el precio del desayuno incluido
    define("DESAYUNO_INCLUIDO", 20);

    //  Incluimos el archivo con las funciones necesarias para la página
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Generamos el encabezado HTML de la página, incluyendo los archivos CSS necesarios
    inicio_html("Ejercicio 5 Agencia Turísmo", ["/estilos/general.css", 
        "/estilos/formulario.css", "/estilos/tablas.css", "/estilos/bh.css"]);

    //  Definimos un array con todos los datos de los viajes: 
    //  destino, compañía aérea, hotel, número de días y extras
    $datosViaje = [
        "destino" => [
            "par" => Array("nombre" => "París", "precio" => 100),
            "lond" => Array("nombre" => "Londrés", "precio" => 120),
            "est" => Array("nombre" => "Estocolmo", "precio" => 200),
            "edi" => Array("nombre" => "Edinburgo", "precio" => 175),
            "pra" => Array("nombre" => "Praga", "precio" => 125),
            "vie" => Array("nombre" => "Viena", "precio" => 150)
        ],
        "compania" => [
            "mia" => Array("nombre" => "MiAir", "precio" => "Incluído"),
            "aif" => Array("nombre" => "AirFly", "precio" => 50),
            "vuc" => Array("nombre" => "VuelaConmigo", "precio" => 75),
            "apa" => Array("nombre" => "ApedalesAir", "precio" => 150)
        ],
        "hotel" => [
            "3*" => "Incluído",
            "4*" => 40,
            "5*" => 100
        ],
        "numDias" => [
            "primeraOp" => 5,
            "segundaOp" => 10,
            "terceraOp" => 15
        ],
        "extras" => [
            "vgc" => Array("nombre" => "Visita guiada en la ciudad", "precio" => 200),
            "bt" => Array("nombre" => "Bus turístico", "precio" => 30),
            "mf" => Array("nombre" => "2º Maleta facturada", "precio" => 20),
            "sv" => Array("nombre" => "Seguro de viaje", "precio" => 30)
        ]
    ];

    //  Comprobamos si el formulario ha sido enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errores = [];  //  Inicializamos un array para almacenar los errores
        $datosFormulario = [];  //  Inicializamos un array para almacenar los datos del formulario

        //  Recogemos y validamos los datos del formulario
        //  Responsable del grupo
        $responsable = filter_input(INPUT_POST, "responsable", FILTER_SANITIZE_SPECIAL_CHARS);
        !empty($responsable) && strlen($responsable) > 2 && strlen($responsable) < 40
            ? $datosFormulario['responsable'] = $responsable
            : $errores['responsable'] = "Debes introducir el nombre del responsable del grupo.";

        //  Teléfono
        $tlf = filter_input(INPUT_POST, "tlf", FILTER_SANITIZE_NUMBER_INT);
        $tlf = filter_var($tlf, FILTER_VALIDATE_INT);
        preg_match("/^[0-9]{9}$/", $tlf) 
            ? $datosFormulario['tlf'] = $tlf
            : $errores['tlf'] = "Por favor, introduce un número de teléfono correcto.";

        //  Correo electrónico
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        !empty($email) 
            ? $datosFormulario['email'] = $email
            : $errores['email'] = "Introduce un email válido.";

        //  Número de personas
        $numPersonas = filter_input(INPUT_POST, "numPersonas", FILTER_SANITIZE_NUMBER_INT);
        $numPersonas = filter_var($numPersonas, FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 5, "max_range" => 10]]);
        isset($numPersonas) && !empty($numPersonas)
            ? $datosFormulario['numPersonas'] = $numPersonas
            : $errores['numPersonas'] = "Por favor, introduce el número de personas.";
    
        //  Número de días
        $numDias = filter_input(INPUT_POST, "numDias", FILTER_SANITIZE_SPECIAL_CHARS);
        isset($numDias) && in_array($numDias, array_keys($datosViaje['numDias']))
            ? $datosFormulario['numDias'] = $datosViaje["numDias"][$numDias]
            : $errores['numDias'] = "Por favor, introduce el número de días que durará el viaje.";

        //  Destino
        $destino = filter_input(INPUT_POST, "destino", FILTER_SANITIZE_SPECIAL_CHARS);
        $destino && in_array($destino, array_keys($datosViaje['destino']))
            ? $datosFormulario['destino'] = $datosViaje['destino'][$destino]
            : $errores['destino'] = "Por favor, introduce el destino de tu viaje.";
        //  Calculamos el precio del destino basado en el número de días y personas
        $datosFormulario['destino']['precio'] *= $datosFormulario['numDias'] * 
            $datosFormulario['numPersonas'];

        //  Compañía aérea
        $compania = filter_input(INPUT_POST, "compania", FILTER_SANITIZE_SPECIAL_CHARS);
        $compania && in_array($compania, array_keys($datosViaje['compania']))
            ? $datosFormulario['compania'] = $datosViaje['compania'][$compania]
            : $errores['compania'] = "Por favor, introduce una compañía aérea.";
        //  Calculamos el precio de la compañía aérea basado en el número de personas 
        $datosFormulario['compania']['precio'] *= $datosFormulario['numPersonas'];

        //  Hotel
        $hotel = filter_input(INPUT_POST, "hotel", FILTER_SANITIZE_SPECIAL_CHARS);
        $hotel && in_array($hotel, array_keys($datosViaje['hotel']))
            ? $datosFormulario['hotel'] = ["estrellas" => $hotel,
                "precio" => $datosViaje['hotel'][$hotel]]
            : $errores['hotel'] = "Por favor, introduce el hotel.";
        //  Ajustar el precio del hotel si es 4* o 5*
        if ($datosFormulario['hotel']['estrellas'] == '4*' 
            || $datosFormulario['hotel']['estrellas'] == '5*') {
            $datosFormulario['hotel']['precio'] *= $datosFormulario['numPersonas'] 
                * $datosFormulario['numDias'];
        }

        //  Desayuno
        $desayuno = filter_input(INPUT_POST, "desayuno", FILTER_VALIDATE_BOOLEAN);
        isset($desayuno) ?
            $datosFormulario['desayuno'] = DESAYUNO_INCLUIDO 
                * $datosFormulario['numPersonas'] * $datosFormulario['numDias']
            : $datosFormulario['desayuno'] = 0;

        //  Extras
        $extras = filter_input(INPUT_POST, "extras",
            FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (is_array($extras) && isset($extras)) {
            $contador = 0;
            foreach ($extras as $extra) {
                $datosFormulario['extras'][] = $datosViaje['extras'][$extra];
                //  Calculamos el precio total de los extras (excepto la visita guiada en la ciudad)
                if ($datosFormulario['extras'][$contador]['nombre'] !== 'Visita guiada en la ciudad') {
                    $datosFormulario['extras'][$contador]['precio'] *= $datosFormulario['numPersonas'] 
                        * $datosFormulario['numDias'];
                }
                $contador++;
            }
        } else {
            $datosFormulario['extras'] = [];
        }
    
        //  Calcular presupuesto final
        $costeFinal = $datosFormulario['destino']['precio'] + $datosFormulario['compania']['precio']
            + $datosFormulario['hotel']['precio'] + $datosFormulario['desayuno']
            + (!empty($datosFormulario['extras']) 
                ? array_sum(array_column($datosFormulario['extras'], 'precio')) : 0);
    }
?>

<!-- Formulario HTML para el viaje -->
<form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <fieldset>
        <legend>Presupuesto viaje turístico</legend>

        <!-- Campos del formulario para ingresar los datos del viaje -->
        <label for="responsable">Persona responsable del grupo</label>
        <input type="text" name="responsable" id="responsable" size="40"/>

        <label for="tlf">Teléfono</label>
        <input type="tel" name="tlf" id="tlf" size="9"/>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" size="40"/>

        <!-- Selección de destino -->
        <label for="destino">Destino</label>
        <select name="destino" id="destino" size="1">
            <?php foreach ($datosViaje['destino'] as $clave => $valor) {
                echo "<option value='$clave'>{$valor['nombre']} - "
                    . "{$valor['precio']}&euro; / p / d</option>";
            } ?>
        </select>


        <!-- Selección de compañía aérea -->
        <label for="compania">Compañía aérea</label>
        <select name="compania" id="compania" size="1">
            <?php foreach ($datosViaje['compania'] as $clave => $valor) {
                echo is_numeric($valor['precio'])
                    ? "<option value='$clave'>{$valor['nombre']} - "
                        . "{$valor['precio']}&euro; / p</option>"
                    : "<option value='$clave'>{$valor['nombre']} - "
                        . "{$valor['precio']}</option>";
            } ?>
        </select>
        
        <!-- Selección de hotel -->
        <label for="hotel">Hotel</label>
        <select name="hotel" id="hotel" size="1">
            <?php foreach ($datosViaje['hotel'] as $clave => $valor) {
                echo is_numeric($valor)
                    ? "<option value='$clave'>$clave - $valor&euro; / p / d</option>"
                    : "<option value='$clave'>$clave - $valor</option>";
            } ?>
        </select>

        <!-- Checkbox para desayuno incluido -->
        <label for="desayuno">Desayuno incluído</label>
        <input type="checkbox" name="desayuno" id="desayuno"/>

        <!-- Número de personas -->
        <label for="numPersonas">Número de personas</label>
        <input type="number" name="numPersonas" id="numPersonas" min="5" max="10"/>

        <!-- Selección de número de días -->
        <label for="numDias">Número de días</label>
        <div>
            <?php foreach ($datosViaje['numDias'] as $clave => $valor) {
                echo "<input type='radio' name='numDias' id='$clave' value='$clave'/>";
                echo "<label for='$clave'>$valor días</label>";
            } ?>
        </div>

        <!-- Selección de extras -->
        <label for="extras[]">Extras</label>
        <div>
            <?php foreach ($datosViaje['extras'] as $clave => $valor) {
                echo "<input type='checkbox' name='extras[]' id='$clave' value='$clave'/>"
                    . "<label for='$clave'>{$valor['nombre']} - {$valor['precio']} &euro;";
                echo $clave === "vgc" ? "</label>" : "/ p / d</label>";
            } ?>
        </div>

    </fieldset>
    <!-- Botón para enviar el formulario -->
    <input type="submit" name="operacion" id="operacion" value="Enviar"/>
</form>

<?php
    //  Mostrar el presupuesto final después de enviar el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($errores)) {
            echo "<h2>Presupuesto viaje turístico.</h2>";
            echo "<h3>Teléfono: {$datosFormulario['tlf']}.</h3>";
            echo "<h3>Email: {$datosFormulario['email']}.</h3>";
            echo "<h3>Número de personas: {$datosFormulario['numPersonas']}.</h3>";
            echo "<h3>Número de días: {$datosFormulario['numDias']}.</h3>";

            //  Mostrar la tabla con los detalles del viaje
            echo "<table border='1'>
                <fielset>
                    <tr>
                        <th>Destino</th>
                        <th>Compañía aérea</th>
                        <th>Hotel</th>
                        <th>Desayuno</th>
                        <th>Extras</th>
                        <th>Costo</th>
                    </tr><tr>";

            //  Iteramos sobre los datos del destino seleccionado y mostramos la información
            echo "<td>{$datosFormulario['destino']['nombre']} - "
                . "{$datosFormulario['destino']['precio']}&euro;</td>";

            //  Iteramos sobre los datos de la compañía aérea seleccionada y mostramos la información
            echo "<td>{$datosFormulario['compania']['nombre']} - "
                . "{$datosFormulario['compania']['precio']}&euro;</td>";

            //  Mostramos el hotel seleccionado y su precio
            echo "<td>{$datosFormulario['hotel']['estrellas']} - "
                . "{$datosFormulario['hotel']['precio']}&euro;</td>";

            //  Mostramos el costo del desayuno si se ha seleccionado
            echo "<td>" . ($datosFormulario['desayuno'] > 0
                ? "{$datosFormulario['desayuno']}&euro;" : "No incluido") . "</td>";
            
            //  Mostramos los extras seleccionados y sus costos
            echo "<td>";
            if (!empty($datosFormulario['extras'])) {
                foreach ($datosFormulario['extras'] as $extra) {
                    echo "<p>{$extra['nombre']} - {$extra['precio']}&euro;</p>";
                }
            } else {
                echo "No hay extras seleccionados";
            } ?>
                    <!-- Finalmente, mostramos el costo total calculado -->
                    <td><?= "$costeFinal&euro;"; ?></td>
                    </tr>
                </fieldset>
            </table>
            <br><br>
        <?php
        } else {
            foreach ($errores as $error) {
                echo "<h3>$error</h3>";
            }
        }
    }
    fin_html();
?>