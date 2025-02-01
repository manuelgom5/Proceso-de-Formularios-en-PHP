<?php
    /* AÚN ME FALTA AÑADIR COMENTARIOS AL CÓDIGO Y MEJORAR LA SALIDA PARA QUE QUEDE MÁS CLARO
        LOS PRECIOS  */
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    inicio_html("Ejercicio 3 Pizzería", ["/estilos/formulario.css", "/estilos/general.css"]);

    $precioBase = 5;

    $tipoPizzas = [
        "v"     =>  Array("nombre" => "Vegetariana", "precio" => $precioBase + 3),
        "nv"    => Array("nombre" => "No Vegetariana", "precio" => $precioBase + 2)
    ];

    $ingredientes = [
        "vegetarianos" => Array(
            "pep"   => Array("nombre" => "Pepino", "precio" => 1),
            "cal"   => Array("nombre" => "Calabacín", "precio" => 1.5),
            "pim-v" => Array("nombre" => "Pimiento verde", "precio" => 1.25),
            "pim-r" => Array("nombre" => "Pimiento rojo", "precio" => 1.75),
            "tom"   => Array("nombre" => "Tomate", "precio" => 1.5),
            "ace"   => Array("nombre" => "Aceitunas", "precio" => 3),
            "ceb"   => Array("nombre" => "Cebolla", "precio" => 1)
        ),
        "no-vegetarianos" => Array(
            "atn"   => Array("nombre" => "Atún", "precio" => 2),
            "car"   => Array("nombre" => "Carne picada", "precio" => 2.5),
            "pepe"  => Array("nombre" => "Peperoni", "precio" => 1.75),
            "morc"  => Array("nombre" => "Morcilla", "precio" => 2.25),
            "anch"  => Array("nombre" => "Anchoas", "precio" => 1.5),
            "salm"  => Array("nombre" => "Salmón", "precio" => 3),
            "gamb"  => Array("nombre" => "Gambas", "precio" => 4),
            "lang"  => Array("nombre" => "Langostinos", "precio" => 4),
            "mej"   => Array("nombre" => "Mejillones", "precio" => 2) 
        )
    ];

    $precioExtraQueso = 3;
    $precioBordesRellenos = 2;
?>

<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
    <fieldset>

        <legend>Pedidos a nuestra pizzería</legend>

        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" size="50"/>

        <label for="direccion">Dirección</label>
        <input type="text" name="direccion" id="direccion" size="75"/>

        <label for="tlf">Teléfono</label>
        <input type="tel" name="tlf" id="tlf" size="9"/>


        <label for="tipo">Tipo</label>
        <div>
            <?php foreach ($tipoPizzas as $clave => $valor) {
                echo "<input type='radio' id='$clave' name='tipo' value='$clave'/>";
                echo "<label for='$clave'>{$valor['nombre']}</label>";
            } ?>
        </div>
    
        <label for="ingredientesVeg">Ingredientes Vegetarianos</label>
        <select name="ingredientesVeg[]" id="ingredientesVeg" multiple>
            <?php foreach ($ingredientes["vegetarianos"] as $clave => $valor) {
                echo "<option value='$clave'>{$valor['nombre']} - {$valor['precio']}€</option>";
            } ?>
        </select>


        <label for="ingredientesNoVeg">Ingredientes No Vegetarianos</label>
        <select name="ingredientesNoVeg[]" id="ingredientesNoVeg" multiple>
            <?php foreach($ingredientes["no-vegetarianos"] as $clave => $valor) {
                echo "<option value='$clave'>{$valor['nombre']} - {$valor['precio']}€</option>";
            } ?>
        </select>
        
        <label for="extraQueso">Extra de Queso</label>
        <input type="checkbox" name="extraQueso" id="extraQueso"/>

        <label for="bordesRellenos">Bordes Rellenos</label>
        <input type="checkbox" name="bordesRellenos" id="bordesRellenos"/>

        <label for="numPizzas">Número de pizzas</label>
        <input type="number" name="numPizzas" id="numPizzas" min="1" max="5"/>

    </fieldset>
    <input type="submit" name="operacion" id="operacion" value="Confirmar"/>
</form>

<?php
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $datosFormulario = [];
        $errores = [];

        $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_SPECIAL_CHARS);
        preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/', $nombre) && strlen($nombre) > 2
            && strlen($nombre) < 50
            ? $datosFormulario['nombre'] = $nombre
            : $errores['nombre'] = "Por favor, introduce tu nombre.";

        $direccion = filter_input(INPUT_POST, "direccion", FILTER_SANITIZE_SPECIAL_CHARS);
        strlen($direccion) > 2 && strlen($direccion) < 75
            ? $datosFormulario['direccion'] = $direccion
            : $errores['direccion'] = "Por favor, introduce tu dirección.";

        $tlf = filter_input(INPUT_POST, "tlf", FILTER_SANITIZE_NUMBER_INT);
        $tlf = filter_var($tlf, FILTER_VALIDATE_INT);
        preg_match("/^[0-9]{9}$/", $tlf)
            ? $datosFormulario['tlf'] = $tlf
            : $errores['tlf'] = "Por favor, introduce un teléfono válido.";
        
        $tipo = filter_input(INPUT_POST, "tipo", FILTER_SANITIZE_SPECIAL_CHARS);
        !empty($tipo) && array_key_exists($tipo, $tipoPizzas)
            ? $datosFormulario['tipo'] = $tipoPizzas[$tipo]
            : $errores['tipo'] = "Por favor, marca el tipo de pizza.";
        
        $ingredientesVeg = filter_input(INPUT_POST,
            "ingredientesVeg", FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (!empty($ingredientesVeg)) {
            foreach ($ingredientesVeg as $clave) {
                if (array_key_exists($clave, $ingredientes['vegetarianos'])) {
                    $datosFormulario['ingredientes'][] = $ingredientes['vegetarianos'][$clave];
                }
            }
        } else {
            $datosFormulario['ingredientes'] = [];
        }

        $ingredientesNoVeg = filter_input(INPUT_POST,
            "ingredientesNoVeg", FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (!empty($ingredientesNoVeg)) {
            foreach ($ingredientesNoVeg as $clave) {
                if (array_key_exists($clave, $ingredientes['no-vegetarianos'])) {
                    $datosFormulario['ingredientes'][] = $ingredientes['no-vegetarianos'][$clave];
                }
            }
        }

        if (!isset($datosFormulario['ingredientes'])) {
            $errores['ingredientes'] = "Por favor, introduce los ingredientes de tu pizza.";
        }

        $extraQueso = filter_input(INPUT_POST, "extraQueso", FILTER_SANITIZE_SPECIAL_CHARS);
        $extraQueso = filter_var($extraQueso, FILTER_VALIDATE_BOOLEAN);
        $extraQueso ? $datosFormulario['queso'] = "Extra de Queso - $precioExtraQueso&euro;" : "";

        $bordesRellenos = filter_input(INPUT_POST, "bordesRellenos", FILTER_SANITIZE_SPECIAL_CHARS);
        $bordesRellenos = filter_var($bordesRellenos, FILTER_VALIDATE_BOOLEAN);
        $bordesRellenos
            ? $datosFormulario['bordes'] = "Bordes Rellenos - $precioBordesRellenos&euro;" : "";

        $numPizzas = filter_input(INPUT_POST, "numPizzas", FILTER_SANITIZE_NUMBER_INT);
        $numPizzas = filter_var($numPizzas, FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 5]]);
        $numPizzas
            ? $datosFormulario['cantidad'] = $numPizzas
            : $errores['cantidad'] = "El número de pizzas debe estar comprendido entre 1 y 5.";

        if (empty($errores)) {
            echo "<h2>Tu pedido</h2>";
            echo "<h3>Nombre => {$datosFormulario['nombre']}</h3>";
            echo "<h3>Dirección => {$datosFormulario['direccion']}</h3>";
            echo "<h3>Teléfono => {$datosFormulario['tlf']}</h3>";
            echo "<h4>Tipo => {$datosFormulario['tipo']['nombre']} - "
                . "{$datosFormulario['tipo']['precio']}&euro;</h4>";
            $precioBase = $datosFormulario['tipo']['precio'] * $datosFormulario['cantidad'];
            echo "<h4>Ingredientes:</h4><ul>";
            $precioIngredientes = 0;
            foreach ($datosFormulario['ingredientes'] as $clave => $valor) {
                echo "<li>{$valor['nombre']} => {$valor['precio']}&euro;</li>";
                $precioIngredientes += $valor['precio'];
            }
            echo "</ul>";
            $precioIngredientes *= $datosFormulario['cantidad'];
            echo "<p>Precio ingredientes por pizza => " 
                . $precioIngredientes/$datosFormulario['cantidad'] . "&euro;</p>";
            echo "<p>Precio total ingredientes => $precioIngredientes&euro;</p>";

            echo "<p>Cantidad de pizzas => {$datosFormulario['cantidad']}</p>";
            echo implode("", [
                "{$datosFormulario['bordes']}<br>" ?? '',
                "{$datosFormulario['queso']}<br>" ?? ''
            ]);

            $precioFinal = $precioBase + $precioIngredientes + (!empty($datosFormulario['bordes']) 
                ? $bordesRellenos * $datosFormulario['cantidad'] : 0) 
                + (!empty($datosFormulario['queso']) ? $extraQueso * $datosFormulario['cantidad'] : 0);
            
            echo "<p>Total a pagar: $precioFinal&euro;</p>";
        } else {
            foreach ($errores as $clave) {
                echo "<h3>$clave</h3>";
            }
        }
        fin_html();
    }
?>