<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    inicio_html("Ejercicio 4 Concesionario", ["/estilos/general.css",
        "/estilos/formulario.css", "/estilos/tablas.css", "/estilos/bh.css"]);

    $datosCoche = [
        "modelo" => Array (
            "mry" => Array("nombre" => "Monroy", "precio" => 20000),
            "mcm" => Array("nombre" => "Muchopami", "precio" => 21000),
            "zpo" => Array("nombre" => "Zapatoveloz", "precio" => 22000),
            "gpn" => Array("nombre" => "Guperino", "precio" => 25500),
            "aoo" => Array("nombre" => "Alomejor", "precio" => 29750),
            "tla" => Array("nombre" => "Telapegas", "precio" => 32550)
        ),
        "motor" => Array (
            "gsn" => Array("nombre" => "Gasolina", "precio" => 0),
            "dee" => Array("nombre" => "Diesel", "precio" => 2000),
            "hbd" => Array("nombre" => "Híbrido", "precio" => 5000),
            "eei" => Array("nombre" => "Eléctrico", "precio" => 10000)
        ),
        "pintura" => Array (
            "git" => Array("nombre" => "Gris triste", "precio" => 0),
            "rjs" => Array("nombre" => "Rojo sangre", "precio" => 250),
            "rjp" => Array("nombre" => "Rojo pasión", "precio" => 150),
            "aun" => Array("nombre" => "Azul noche", "precio" => 175),
            "crl" => Array("nombre" => "Caramelo", "precio" => 300),
            "mng" => Array("nombre" => "Mango", "precio" => 275)
        ),
        "extras" => Array (
            "gps" => Array("nombre" => "Navegador GPS", "precio" => 500),
            "cale" => Array("nombre" => "Calefacción Asientos", "precio" => 250),
            "aat" => Array("nombre" => "Antena aleta tiburón", "precio" => 50),
            "aal" => Array("nombre" => "Acceso y arranque sin llave", "precio" => 150),
            "arp" => Array("nombre" => "Arranque en pendiente", "precio" => 200),
            "cai" => Array("nombre" => "Cargador inalámbrico", "precio" => 300),
            "coc" => Array("nombre" => "Control de crucero", "precio" => 500),
            "dam" => Array("nombre" => "Detectar ángulo muerto", "precio" => 350),
            "fla" => Array("nombre" => "Faros led automáticos", "precio" => 400),
            "fre" => Array("nombre" => "Frenada emergencia", "precio" => 375)
        ),
        "mesesFinanciacion" => Array(
            "opcion1" => 24,
            "opcion2" => 60,
            "opcion3" => 120
        )
    ];
?>

<form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <fieldset>

        <legend>Configuración nuevo coche</legend>
        
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" size="40"/>

        <label for="tlf">Teléfono</label>
        <input type="tel" name="tlf" id="tlf" size="9"/>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" size="50"/>

        <label for="modelo">Modelo</label>
        <select name="modelo" id="modelo" size="1">
            <?php foreach($datosCoche['modelo'] as $clave => $valor) {
                echo "<option value='$clave'>{$valor['nombre']} - "
                    . "{$valor['precio']}&euro;</option>";
            } ?>
        </select>

        <label for="motor">Motor</label>
        <div>
            <?php foreach ($datosCoche['motor'] as $clave => $valor) {
                echo "<input type='radio' name='motor' id='$clave' value='$clave'/>";
                echo "<label for='$clave'>{$valor['nombre']} - "
                    . "{$valor['precio']}&euro;</label>";
            } ?>
        </div>

        <label for="pintura">Pintura</label>
        <select name="pintura" id="pintura" size="1">
            <?php foreach ($datosCoche['pintura'] as $clave => $valor) {
                echo $clave === "git" 
                    ? "<option value='$clave'>{$valor['nombre']} - Sin coste</option>"
                    : "<option value='$clave'>{$valor['nombre']} - "
                        . "{$valor['precio']}&euro;</option>";
            } ?>
        </select>

        <label for="extras[]">Extras</label>
        <div>
            <?php foreach ($datosCoche['extras'] as $clave => $valor) {
                echo "<input type='checkbox' name='extras[]' id='$clave' value='$clave'/>";
                echo "<label for='$clave'>{$valor['nombre']} - {$valor['precio']}&euro;</label>";
            } ?>
        </div>

        <label for="tiempoFinanciacion">Pago financiado</label>
        <div>
            <?php foreach ($datosCoche['mesesFinanciacion'] as $clave => $valor) {
                echo "<input type='radio' name='tiempoFinanciacion' id='$clave' value='"
                    . $valor . "'/>";
                echo "<label for='$clave'>" . $valor/12 . " años</label>";
            } ?>
        </div>
    </fieldset>
    <input type="submit" name="operacion" id="operacion" value="Confirmar"/>
</form>

<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $datosFormulario = [];
        $errores = [];

        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        strlen($nombre) > 2
            ? $datosFormulario['nombre'] = $nombre
            : $errores['nombre'] = "Por favor, introduce un nombre válido.";
        
        $tlf = filter_input(INPUT_POST, 'tlf', FILTER_SANITIZE_NUMBER_INT);
        $tlf = filter_var($tlf, FILTER_VALIDATE_INT);
        preg_match("/^[0-9]{9}$/", $tlf)
            ? $datosFormulario['tlf'] = $tlf
            : $errores['tlf'] = "Por favor, introduce un teléfono válido.";

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        strlen($email) > 10 && strlen($email) < 50
            ? $datosFormulario['email'] = $email
            : $errores['email'] = "Por favor, introduce un email válido.";

        $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_SPECIAL_CHARS);
        in_array($modelo, array_keys($datosCoche['modelo']))
            ? $datosFormulario['modelo'] = $datosCoche['modelo'][$modelo]
            : $errores['modelo'] = "Por favor, introduce el modelo del coche.";

        $motor = filter_input(INPUT_POST, 'motor', FILTER_SANITIZE_SPECIAL_CHARS);
        in_array($motor, array_keys($datosCoche['motor']))
            ? $datosFormulario['motor'] = $datosCoche['motor'][$motor]
            : $errores['motor'] = "Por favor, introduce el tipo de motor.";
        
        $pintura = filter_input(INPUT_POST, 'pintura', FILTER_SANITIZE_SPECIAL_CHARS);
        in_array($pintura, array_keys($datosCoche['pintura'])) 
            ? $datosFormulario['pintura'] = $datosCoche['pintura'][$pintura]
            : $errores['pintura'] = "Por favor, introduce el tipo de pintura.";

        if ($datosFormulario['pintura']['precio'] == 0) {
            $datosFormulario['pintura']['precio'] = "Sin coste.";
        }

        $extras = filter_input(INPUT_POST, 'extras', 
            FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (!empty($extras)) {
            foreach ($extras as $extra) {
                if (array_key_exists($extra, $datosCoche['extras'])) {
                    $datosFormulario['extras'][] = $datosCoche['extras'][$extra];
                }
            }
        } else {
            $datosFormulario['extras'] = [];
        }
        
        $pagoFinanciado = filter_input(INPUT_POST,
            "tiempoFinanciacion", FILTER_SANITIZE_SPECIAL_CHARS);
        in_array($pagoFinanciado, array_values($datosCoche['mesesFinanciacion']))
            ? $datosFormulario['tiempoFinanciacion'] = (int) $pagoFinanciado
            : $errores['tiempoFinanciacion'] = "Selecciona el tiempo de financiación.";

        $costoCoche = $datosFormulario['modelo']['precio'] + $datosFormulario['motor']['precio']
            + (is_numeric($datosFormulario['pintura']['precio']) 
                ? $datosFormulario['pintura']['precio'] : 0)  + (!empty($datosFormulario['extras'])
                ? array_sum(array_column($datosFormulario['extras'], 'precio')) : 0);

        if (empty($errores)) {
            echo "<h2>Factura del coche</h2>";
            echo "<h3>Nombre {$datosFormulario['nombre']}</h3>";
            echo "<h3>Teléfono {$datosFormulario['tlf']}</h3>";
            echo "<h3>Email {$datosFormulario['email']}</h3>";
            echo "<table>
                <fieldset>
                    <tr>
                        <th>Modelo</th>
                        <th>Motor</th>
                        <th>Pintura</th>
                        <th>Extras</th>
                        <th>Tiempo de financiación</th>
                        <th>Costo por mes</th>
                        <th>Costo total</th>
                    </tr><tr>";
            
            echo "<td>{$datosFormulario['modelo']['nombre']} - "
                . "{$datosFormulario['modelo']['precio']}&euro;</td>";

            echo "<td>{$datosFormulario['motor']['nombre']} - "
                . "{$datosFormulario['motor']['precio']}&euro;</td>";

            echo "<td>{$datosFormulario['pintura']['nombre']} - ";
            echo $datosFormulario['pintura']['precio'] !== 0 
                ? "{$datosFormulario['pintura']['precio']}&euro;</td>"
                : "Sin coste</td>";

            echo "<td>";
            if (!empty($datosFormulario['extras'])) {
                foreach ($datosFormulario['extras'] as $extra) {
                    echo "<p>{$extra['nombre']} - {$extra['precio']}&euro;</p>";
                }
            } else {
                echo "No hay extras seleccionados";
            }
            echo "</td>";

            echo "<td>" . $datosFormulario['tiempoFinanciacion']/12 . " años</td>";
            echo "<td>" . (number_format($costoCoche/$datosFormulario['tiempoFinanciacion'], 1))
                . "&euro;</td>";
            echo "<td>$costoCoche&euro;</td>";
            ?>
            </tr>
        </fieldset>
        </table><br><br>
    <?php
        } else {
            foreach ($errores as $clave => $valor) {
                echo "<h3>$valor</h3>";
            }
        }
        fin_html();
    }
?>