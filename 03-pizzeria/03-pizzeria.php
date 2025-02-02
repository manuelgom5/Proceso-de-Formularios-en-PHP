<?php
    //  Ruta para almacenar los pedidos
    define("RUTA_PEDIDOS", $_SERVER['DOCUMENT_ROOT'] . "/tema-2/repasoExamen/03-pizzeria/pedidos");

    //  Archivo de funciones con inicio_html y fin_html
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Función que inicia el HTML de la página, con el título y las hojas de estilo
    inicio_html("Ejercicio 3 Pizzería", ["/estilos/formulario.css", "/estilos/general.css",
        "/estilos/tablas.css"]);

    //  Precio base de la pizzas
    $precioBase = 5;

    //  Tipos de pizzas disponibles (vegetariana y no vegetariana), con su precio
    $tipoPizzas = [
        "v"     =>  Array("nombre" => "Vegetariana", "precio" => $precioBase + 3),
        "nv"    => Array("nombre" => "No Vegetariana", "precio" => $precioBase + 2)
    ];

    //  Ingredientes disponibles para las pizas
    $listaIngredientes = [
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

    //  Precio adicional por extras
    $extrasDisponibles = [
        "queso" => ["nombre" => "Extra de Queso", "precio" => 3],
        "bordes" => ["nombre" => "Bordes Rellenos", "precio" => 2]
    ];
?>

<!-- Formulario HTML para que el usuario ingrese su pedido -->
<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
    <fieldset>
        <legend>Pedidos a nuestra pizzería</legend>

        <!-- Campos para ingresar nombre, dirección y teléfono -->
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" size="50"/>

        <label for="direccion">Dirección</label>
        <input type="text" name="direccion" id="direccion" size="75"/>

        <label for="tlf">Teléfono</label>
        <input type="tel" name="tlf" id="tlf" size="9"/>

        <!-- Selecciona el tipo de pizza -->
        <label for="tipo">Tipo</label>
        <div>
            <?php foreach ($tipoPizzas as $clave => $valor) {
                echo "<input type='radio' id='$clave' name='tipo' value='$clave'/>";
                echo "<label for='$clave'>{$valor['nombre']}</label>";
            } ?>
        </div>
    
        <!-- Selecciona los ingredientes que desea-->
        <label for="ingredientesVeg">Ingredientes Vegetarianos</label>
        <select name="ingredientesVeg[]" id="ingredientesVeg" multiple>
            <?php foreach ($listaIngredientes["vegetarianos"] as $clave => $valor) {
                echo "<option value='$clave'>{$valor['nombre']} - {$valor['precio']}€</option>";
            } ?>
        </select>


        <label for="ingredientesNoVeg">Ingredientes No Vegetarianos</label>
        <select name="ingredientesNoVeg[]" id="ingredientesNoVeg" multiple>
            <?php foreach($listaIngredientes["no-vegetarianos"] as $clave => $valor) {
                echo "<option value='$clave'>{$valor['nombre']} - {$valor['precio']}€</option>";
            } ?>
        </select>
        
        <!-- Checkbox para extra de queso y bordes rellenos -->
        <label for="extraQueso">Extra de Queso</label>
        <input type="checkbox" name="extraQueso" id="extraQueso" value="queso"/>

        <label for="bordesRellenos">Bordes Rellenos</label>
        <input type="checkbox" name="bordesRellenos" id="bordesRellenos" value="bordes"/>

        <!-- Campo para seleccionar la cantidad de pizzas -->
        <label for="numPizzas">Número de pizzas</label>
        <input type="number" name="numPizzas" id="numPizzas" min="1" max="5"/>

    </fieldset>
    <input type="submit" name="operacion" id="operacion" value="Confirmar"/>
</form>

<?php
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        //  Arrays que almacenarán los datos del formulario y los errores de validación
        $datosFormulario = [];
        $errores = [];

        //  Validación y sanitización del nombre
        $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_SPECIAL_CHARS);
        preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/', $nombre) && strlen($nombre) > 2
            && strlen($nombre) < 50
            ? $datosFormulario['nombre'] = $nombre
            : $errores['nombre'] = "Por favor, introduce tu nombre.";

        //  Validación y sanitización de la dirección
        $direccion = filter_input(INPUT_POST, "direccion", FILTER_SANITIZE_SPECIAL_CHARS);
        strlen($direccion) > 2 && strlen($direccion) < 75
            ? $datosFormulario['direccion'] = $direccion
            : $errores['direccion'] = "Por favor, introduce tu dirección.";

        //  Validación del teléfono
        $tlf = filter_input(INPUT_POST, "tlf", FILTER_SANITIZE_NUMBER_INT);
        $tlf = filter_var($tlf, FILTER_VALIDATE_INT);
        preg_match("/^[0-9]{9}$/", $tlf)
            ? $datosFormulario['tlf'] = $tlf
            : $errores['tlf'] = "Por favor, introduce un teléfono válido.";
        
        //  Validación del tipo de pizza
        $tipo = filter_input(INPUT_POST, "tipo", FILTER_SANITIZE_SPECIAL_CHARS);
        !empty($tipo) && array_key_exists($tipo, $tipoPizzas)
            ? $datosFormulario['tipo'] = $tipoPizzas[$tipo]
            : $errores['tipo'] = "Por favor, marca el tipo de pizza.";
        
        //  Procesar los ingredientes vegetarianos
        $ingredientesVeg = filter_input(INPUT_POST,
            "ingredientesVeg", FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (!empty($ingredientesVeg)) {
            foreach ($ingredientesVeg as $clave) {
                if (array_key_exists($clave, $listaIngredientes['vegetarianos'])) {
                    $datosFormulario['ingredientes'][] = $listaIngredientes['vegetarianos'][$clave];
                }
            }
        } else {
            $datosFormulario['ingredientes'] = [];
        }

        //  Procesar los ingredientes no vegetarianos
        $ingredientesNoVeg = filter_input(INPUT_POST,
            "ingredientesNoVeg", FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (!empty($ingredientesNoVeg)) {
            foreach ($ingredientesNoVeg as $clave) {
                if (array_key_exists($clave, $listaIngredientes['no-vegetarianos'])) {
                    $datosFormulario['ingredientes'][] = $listaIngredientes['no-vegetarianos'][$clave];
                }
            }
        }

        //  Verifica si se han seleccionado ingredientes, en caso contrario, guardar error
        if (!isset($datosFormulario['ingredientes'])) {
            $errores['ingredientes'] = "Por favor, introduce los ingredientes de tu pizza.";
        }

        //  Inicializamos el campo 'extras' como un array vacío
        $datosFormulario['extras'] = [];
        //  Verificar si se ha seleccionado extra de queso
        $extraQueso = filter_input(INPUT_POST, "extraQueso", FILTER_SANITIZE_SPECIAL_CHARS);
        array_key_exists($extraQueso, $extrasDisponibles) 
            ? $datosFormulario['extras'][] = $extrasDisponibles[$extraQueso] : "";

        //  Verificar si se ha seleccionado bordes rellenos
        $bordesRellenos = filter_input(INPUT_POST, "bordesRellenos", FILTER_SANITIZE_SPECIAL_CHARS);
        array_key_exists($bordesRellenos, $extrasDisponibles)
            ? $datosFormulario['extras'][] = $extrasDisponibles[$bordesRellenos] : "";

        //  Validación y procesamiento del número de pizzas
        $numPizzas = filter_input(INPUT_POST, "numPizzas", FILTER_SANITIZE_NUMBER_INT);
        $numPizzas = filter_var($numPizzas, FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 5]]);
        $numPizzas
            ? $datosFormulario['cantidad'] = $numPizzas
            : $errores['cantidad'] = "El número de pizzas debe estar comprendido entre 1 y 5.";

        //  Cálculo del precio de los ingredientes por pizza
        $precioIngredientes = 0;
        foreach ($datosFormulario['ingredientes'] as $clave => $valor) {
            $precioIngredientes += $valor['precio'];
        }

        //  Calcular el precio del pedido
        $precioFinal = ($datosFormulario['tipo']['precio'] + $precioIngredientes 
            + (!empty($datosFormulario['extras']['bordes']) 
            ? $datosFormulario['extras']['bordes']['precio'] : 0) 
            + (!empty($datosFormulario['extras']['queso']) 
                ? $datosFormulario['extras']['queso']['precio']: 0))
            * $datosFormulario['cantidad'];
        $precioFinal .= "€";

        //  Si no existe, creamos el directorio con el nombre igual al número de teléfono
        if (!is_dir(RUTA_PEDIDOS . "/{$datosFormulario['tlf']}")) {
            if (!mkdir(RUTA_PEDIDOS . "/{$datosFormulario['tlf']}", 0750)) {
                $errores['directorio'] = "Error al crear el directorio.";
            }
        }

        //  Si no ha ocurrido ningún error, creamos los ficheros con los pedidos
        if (empty($errores)) {
            //  Ruta de los ficheros con los datos del pedido y ingredientes
            $rutaFicheroPedidos = RUTA_PEDIDOS . 
                "/{$datosFormulario['tlf']}/pedido.csv";
            $rutaFicheroIngredientes = RUTA_PEDIDOS . 
                "/{$datosFormulario['tlf']}/ingredientes.csv";

            //  Creamos el identificador para el registro del pedido
            $idFichero = time() . "-" . $datosFormulario['tlf'];

            //  Comprobamos si existe el fichero, en caso contrario, crea la cabecera
            $existePedidos = !file_exists($rutaFicheroPedidos);
            $ficheroPedidos = fopen($rutaFicheroPedidos, "a+");
            if ($existePedidos) {
                fputcsv($ficheroPedidos, [
                    "ID", "Nombre", "Fecha", "Direccion", "Teléfono", "Tipo", 
                    "Cantidad", "Extras", "Total"
                ]);
            }

            //  Convertimos el array de extras en una cadena separada por comas
            $extrasStr = implode(", ", array_map(
                function($extra) {
                    return "{$extra['nombre']} ({$extra['precio']}€)";
                }, $datosFormulario['extras']
            ));

            //  Introducimos los datos del pedido, verificando si el archivo 
            //  de pedidos no existe, y si el pedido está duplicado
            if (file_exists($rutaFicheroPedidos)) {
                $revisarDuplicados = fopen($rutaFicheroPedidos, 'r');
                $pedidoExistente = false;
                while (($pedido = fgetcsv($revisarDuplicados)) !== false) {
                    if ($pedido[1] == $datosFormulario['nombre'] 
                        && $pedido[3] == $datosFormulario['direccion']
                        && $pedido[4] == $datosFormulario['tlf']
                        && $pedido[5] == $datosFormulario['tipo']['nombre']
                        && $pedido[6] == $datosFormulario['cantidad']
                        && $pedido[7] == $extrasStr) {
                        $pedidoExistente = true;
                        break;
                    }
                }
                fclose($revisarDuplicados);
            }

            //  Si no existe un pedido igual escribimos los datos del pedido en el archivo
            if (!$pedidoExistente) {
                fputcsv($ficheroPedidos, [
                    $idFichero, $datosFormulario['nombre'], date('Y-m-d H:i:s'),
                    $datosFormulario['direccion'], $datosFormulario['tlf'],
                    $datosFormulario['tipo']['nombre'], $datosFormulario['cantidad'],
                    $extrasStr ? $extrasStr : "No seleccionados", $precioFinal
                ]);
            }

            fclose($ficheroPedidos);

            //  Verificamos si el archivo de ingredientes ya existe, en caso contrario,
            //  añadimos su cabecera
            $existeIngredientes = !file_exists($rutaFicheroIngredientes);
            $ficheroIngredientes = fopen($rutaFicheroIngredientes, "a+");
            if ($existeIngredientes) {
                fputcsv($ficheroIngredientes, ["PEDIDO_ID", "Ingrediente", "Precio (€)"]);
            }

            //  Si el pedido no existía antes, añadimos los ingredientes
            if (!$pedidoExistente) {
                foreach($datosFormulario['ingredientes'] as $ingrediente) {
                    fputcsv($ficheroIngredientes, [
                        $idFichero, $ingrediente['nombre'], 
                        ($ingrediente['precio']*$datosFormulario['cantidad']) . "€"
                    ]);
                }
            }

            fclose($ficheroIngredientes);

            //  Ahora debemos mostrar los datos en dos tablas distintas
            echo "<h2>Pedidos del número {$datosFormulario['tlf']}</h2>";
            echo "<table border='1'><thead><tr>";

            //  Abrimos el archivo de pedidos en modo lectura y mostramos su cabecera
            $ficheroPedidos = fopen($rutaFicheroPedidos, 'r');
            $cabecera = fgetcsv($ficheroPedidos);
            foreach ($cabecera as $campo) {
                echo "<th>$campo</th>";
            }

            //  Leemos las demás filas de los pedidos y las mostramos en la tabla
            echo "</tr></thead><tbody>";
            if ($ficheroPedidos) {
                while (($fila = fgetcsv($ficheroPedidos)) !== false) {
                    echo "<tr>";
                    foreach ($fila as $campo) {
                        echo "<td>" . htmlspecialchars($campo) . "</td>";
                    }
                    echo "</tr>";
                }
            }
            fclose($ficheroPedidos);
            echo "</tbody></table>";

            //  Abrimos el archivo de ingredientes en modo lectura y mostramos su cabecera
            $ficheroIngredientes = fopen($rutaFicheroIngredientes, 'r');
            $cabecera = fgetcsv($ficheroIngredientes);

            //  Hacemos lo mismo pero con los ingredientes
            if ($datosFormulario['ingredientes'] && $ficheroIngredientes) {
                echo "<h2>Ingredientes</h2>";
                echo "<table border='1'><thead>";
                echo "<tr>";
                foreach ($cabecera as $campo) {
                    echo "<th>$campo</th>";
                }
                echo "</tr></thead><tbody>";

                //  Ahora mostramos los datos de la tabla si existes
                if ($ficheroIngredientes) {
                    while (($fila = fgetcsv($ficheroIngredientes)) !== false) {
                        echo "<tr>";
                        foreach ($fila as $campo) {
                            echo "<td>" . htmlspecialchars($campo) . "</td>";
                        }
                        echo "</tr>";
                    }
                }
                echo "</tbody></table>";
            }
            fclose($ficheroIngredientes);
        } else {
            foreach ($errores as $error) {
                echo "<h3>$error</h3>";
            }
        }
    }
    fin_html();
?>