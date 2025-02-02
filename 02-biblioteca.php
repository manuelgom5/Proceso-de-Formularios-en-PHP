<?php
    //  Incluye las funciones necesarias desde un archivo externo
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/funciones.php");

    //  Inicia el HTML con título y enlaces a archivos de estilo
    inicio_html("Ejercicio 2 Biblioteca", ["/estilos/formulario.css", "/estilos/general.css",
        "/estilos/tablas.css"]);

    //  Función para crear la cabecera de la tabla
    function cabeceraTabla() { ?>
        <table border='1'>
            <thead>
                <tr>
                    <td>ISBN</td>
                    <td>TÍTULO</td>
                    <td>AUTOR</td>
                    <td>GÉNERO</td>
                </tr>
            </thead>
            <tbody>
<?php  
    }

    //  Función para filtrar y mostrar los datos de los libros según los criterios proporcionados
    function datosTabla($isbn = null, $autor = null, $titulo = null, $genero = null) {
        global $resultados;
        
        //  Si se proporciona un ISBN, solo se buscará ese libro
        if ($isbn) {
            $resultados = isset($resultados[$isbn]) ? [$isbn => $resultados[$isbn]] : [];
        } else {
            //  Filtra los resultados si se proporciona un autor
            if ($autor) {
                $resultados = array_filter($resultados, function ($libro) use ($autor) {
                    return in_array($libro['autor'], $autor);
                });
            }
            
            //  Filtra los resultados si se proporciona un título
            if ($titulo) {
                $resultados = array_filter($resultados, function($libro) use ($titulo) {
                    return stripos($libro['titulo'], $titulo) !== false;
                });
            }

            //  Filtra los resultados si se proporciona un género
            if ($genero) {
                $resultados = array_filter($resultados, function($libro) use ($genero) {
                    return in_array($libro['genero'], $genero);
                });
            }
        }
    }

    //  Diccionario con autores y géneros
    $diccionario = [
        "autores"   =>  [
            "kf"    =>  "Ken Follet",
            "mh"    =>  "Max Hastings",
            "ia"    =>  "Isaac Asimov",
            "cs"    =>  "Carl Sagan",
            "sj"    =>  "Steve Jacobson",
            "grm"   =>  "George R.R. Martín"
        ],
        "generos"   =>  [
            "nh"    =>  "Novela histórica",
            "bio"   =>  "Biografía",
            "fan"   =>  "Fantasía",
            "dc"    =>  "Divulgación científica"
        ]
    ];

    //  Base de datos de libros con ISBN, autor, título y género
    $biblioteca = [
        "123-4-56789-012-3" =>  [
            "autor"     =>  "Ken Follet",
            "titulo"    =>  "Los pilares de la tierra",
            "genero"    =>  "Novela histórica"
        ],
        "987-6-54321-098-7" =>  [
            "autor"     =>  "Ken Follet",
            "titulo"    =>  "La caída de los gigantes",
            "genero"    =>  "Novela histórica"
        ],
        "345-1-91827-019-4" =>  [
            "autor"     =>  "Max Hastings",
            "titulo"    =>  "La guerra de Churchill",
            "genero"    =>  "Biografía"
        ],
        "908-2-10928-374-5" =>  [
            "autor"     =>  "Isaac Asimov",
            "titulo"    =>  "Fundación",
            "genero"    =>  "Fantasía"
        ],
        "657-4-39856-543-3" =>  [
            "autor"     =>  "Isaac Asimov",
            "titulo"    =>  "Yo, robot",
            "genero"    =>  "Fantasía"
        ],
        "576-4-23442-998-5" =>  [
            "autor"     =>  "Carl Sagan",
            "titulo"    =>  "Cosmos",
            "genero"    =>  "Divulgación científica"
        ],
        "398-4-92438-323-2" =>  [
            "autor"     =>  "Carl Sagan",
            "titulo"    =>  "La diversidad de la ciencia",
            "genero"    =>  "Divulgación científica"
        ],
        "984-5-39874-209-4"  =>  [
            "autor"     =>  "Steve Jacobson",
            "titulo"    =>  "Jobs",
            "genero"    =>  "Biografía"
        ],
        "564-7-54937-300-6" =>  [
            "autor"     =>  "George R.R. Martín",
            "titulo"    =>  "Juego de tronos",
            "genero"    =>  "Fantasía"
        ],
        "677-2-10293-833-8" =>  [
            "autor"     =>  "George R.R. Martín",
            "titulo"    =>  "Sueño de primavera",
            "genero"    =>  "Fantasía"
        ]
    ];
?>

    <!-- Formulario de búsqueda -->
    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
        <fieldset>
            <legend>Buscador de libros en nuestra biblioteca</legend>

            <label for="isbn">Isbn</label>
            <input type="text" name="isbn" id="isbn" size="17" autofocus/>

            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" size="40"/>

            <label for="autor">Autor</label>
            <select name="autor[]" id="autor" multiple>
                <?php 
                    //  Genera las opciones del select para los autores
                    foreach ($diccionario["autores"] as $clave => $valor) {
                        echo "<option value='$clave'>$valor</option>";
                    } ?>
            </select>

            <label for="genero">Género</label>
            <select name="genero[]" id="genero" multiple>
                <?php 
                    //  Genera las opciones del select para los géneros
                    foreach ($diccionario["generos"] as $clave => $valor) {
                        echo "<option value='$clave'>$valor</option>";
                    } ?>
            </select>
            
        </fieldset>
        <input type="submit" name="operacion" id="operacion" value="Buscar"/>
    </form>

<?php
    //  Si se recibe una solicitud POST (enviado por el formulario)
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $resultados = $biblioteca;  //  Inicializa los resultados con todos los libros
    
        //  Filtra el ISBN ingresado, si es válido
        $isbn   = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
        $isbn   = preg_match("/^\d{3}-\d-\d{5}-\d{3}-\d$/", $isbn) && 
            array_key_exists($isbn, $biblioteca) ? $isbn : NULL;

        //  Filtra el título ingresado
        $titulo = filter_input(INPUT_POST, "titulo", FILTER_SANITIZE_SPECIAL_CHARS);

        //  Filtra los autores ingresados
        $autores  = filter_input(INPUT_POST, "autor", 
            FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if ($autores) {
            foreach ($autores as &$autor) {
                if (array_key_exists($autor, $diccionario['autores'])) {
                    $autor = $diccionario['autores'][$autor];
                }
            }
            unset($autor);
        }

        //  Filtra los géneros seleccionados
        $generos = filter_input(INPUT_POST, "genero", 
            FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if ($generos) {
            foreach ($generos as &$genero) {
                if (array_key_exists($genero, $diccionario['generos'])) {
                    $genero = $diccionario['generos'][$genero];
                }
                unset($genero);
            }
        }

        //  Muestra la cabecera de la tabla
        cabeceraTabla();

        //  Filtra y muestra los libros basados en los criterios de búsqueda
        datosTabla($isbn, $autores, $titulo, $generos);

        echo "<br>";

        //  Si se encontraron resultados, los muestra en la tabla
        if (($isbn || $autores || $titulo || $generos) && $resultados) {
            foreach($resultados as $clave => $valor) { ?>
                <tr>
                    <td><?= htmlspecialchars($clave) ?></td>
                    <td><?= htmlspecialchars($valor['titulo']) ?></td>
                    <td><?= htmlspecialchars($valor['autor']) ?></td>
                    <td><?= htmlspecialchars($valor['genero']) ?></td>
                </tr>
        <?php
            }
        } else {
            //  Si no se encuentran resultados, muestra un mensaje y todos los libros
            echo "<h2>No encontramos ningún libro con esos datos.</h2>";
            foreach ($biblioteca as $clave => $valor) { ?>
                <tr>
                    <td><?= htmlspecialchars($clave) ?></td>
                    <td><?= htmlspecialchars($valor['titulo'])?></td>
                    <td><?= htmlspecialchars($valor['autor'])?></td>
                    <td><?= htmlspecialchars($valor['genero'])?></td>
                </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>

<?php
    //  Finaliza la página HTML
    fin_html();
    }
?>