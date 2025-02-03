#   2º CFGS Desarrollo de Aplicaciones Web
#   Desarrollo Web Entorno Cliente
#   Programación PHP – Proceso de formularios RA3

##  Enunciados de rafaprofegc
##  Resueltos por Manuel Gómez -> manuelgom5
##  Fecha: 28/01/2025

**En todos los scripts PHP se devuelve el resultado en código HTML para su visualización en el navegador. En todos los casos, al procesar la respuesta hay que realizar el saneamiento y la validación de datos donde corresponda.**

`1. Crear un script PHP que presente un formulario donde se introduce un número entero y devuelve una respuesta con el número convertido en varios sistemas: binario, octal, hexadecimal, decimal.`

[Sistemas Numéricos](./01-sistemas-numericos.php)

`2. Crear un script PHP para consultar los libros de una biblioteca.`

a) El formulario de entrada de datos incluye:

b) La respuesta es una tabla con todos los libros que se ajusten al criterio de
búsqueda.

| Campo        | Tipo de Campo             | Valores                                      |
|--------------|---------------------------|----------------------------------------------|
| **Isbn**     | Texto                     | Formato ###-#-#####-###-#                    |
| **Título**   | Texto                     |                                              |
| **Autor**    | Lista de selección múltiple | Ken Follet, Max Hastings, Isaac Asimov, Carl Sagan, Steve Jacobson, George R.R. Martin |
| **Género**   | Lista de selección múltiple | Novela histórica, Divulgación científica, Biografía, Fantasía |

c) Los libros están almacenados en un array asociativo con el isbn como clave.
Pueden usarse los siguientes:

| ISBN                 | Autor             | Título                        | Género                  |
|----------------------|-------------------|-------------------------------|-------------------------|
| 123-4-56789-012-3    | Ken Follet        | Los pilares de la tierra      | Novela histórica        |
| 987-6-54321-098-7    | Ken Follet        | La caída de los gigantes      | Novela histórica        |
| 345-1-91827-019-4    | Max Hastings      | La guerra de Churchill        | Biografía               |
| 908-2-10928-374-5    | Isaac Asimov      | Fundación                     | Fantasía                |
| 657-4-39856-543-3    | Isaac Asimov      | Yo, robot                     | Fantasía                |
| 576-4-23442-998-5    | Carl Sagan        | Cosmos                        | Divulgación científica  |
| 398-4-92438-323-2    | Carl Sagan        | La diversidad de la ciencia   | Divulgación científica  |
| 984-5-39874-209-4    | Steve Jacobson    | Jobs                          | Biografía               |
| 564-7-54937-300-6    | George R.R. Martin| Juego de tronos               | Fantasía                |
| 677-2-10293-833-8    | George R.R. Martin| Sueño de primavera            | Fantasía                |

[Consultar libros biblioteca](./02-biblioteca.php)

`3. Crear un script PHP para gestionar pizzas pedidas por Internet:`

a) Todas las pizzas tienen tomate frito y queso como ingredientes básicos, con un
precio inicial de 5 €.

b) Hay pizzas vegetarianas y no vegetarianas. La vegetariana tiene un incremento de 3 €. Las no vegetarianas tienen un incremento de 2 €.

c) El usuario puede añadir todos los ingredientes que quiera dentro de cada clase
de pizza.

d) Crear un formulario para recoger pedidos de pizzas y generar una respuesta con
todos los detalles de la pizza elegida, su coste desglosado y el coste total.

e) Los campos del formulario son:

| Campo                    | Tipo de Campo            | Valores                                              |
|--------------------------|--------------------------|------------------------------------------------------|
| **Nombre**               | Texto                    |                                                      |
| **Dirección**            | Texto                    |                                                      |
| **Tlf**                  | Teléfono                 |                                                      |
| **Tipo**                 | Grupo de botones         | Vegetariana – No vegetariana                         |
| **Ingredientes Veg**     | Lista selección múltiple | Pepino – 1 €, Calabacín – 1.5 €, Pimiento verde – 1.25 €, Pimiento rojo – 1.75 €, Tomate – 1.5 €, Aceitunas – 3 €, Cebolla – 1 € |
| **Ingredientes No Veg**  | Lista selección múltiple | Atún – 2 €, Carne picada – 2.5 €, Peperoni – 1.75 €, Morcilla – 2.25 €, Anchoas – 1.5 €, Salmón – 3 €, Gambas – 4 €, Langostinos – 4 €, Mejillones – 2 € |
| **Extra de queso**       | Checkbox                 |                                                      |
| **Bordes rellenos**      | Checkbox                 |                                                      |
| **Nº de pizzas**         | Número                   | Entre 1 y 5                                           |

[Pedido pizzería](./03-pizzeria/03-pizzeria.php)

`4. Crear un script para gestionar la configuración de un coche nuevo en el que se tiene que elegir un modelo y una serie de características:`

a) Crear un formulario para recoger los datos del vehículo y generar una respuesta
con todos los detalles del vehículo elegido, su precio desglosado y el precio total.

b) Si en el formulario se eligió pago financiado presentar el plan de pago que
consistirá en la cuota de entrada, todas las mensualidades (a ver quién se atreve a
poner las fechas de pago) y la cuota final.

c) Los campos del formulario son:

| Campo                     | Tipo de Campo            | Valores                                                        |
|---------------------------|--------------------------|----------------------------------------------------------------|
| **Nombre**                | Texto                    |                                                                |
| **Tlf**                   | Teléfono                 |                                                                |
| **Email**                 | Email                    |                                                                |
| **Modelo**                | Lista selección única    | Monroy – 20000 €, Muchopami – 21000 €, Zapatoveloz – 22000 €, Guperino – 25500 €, Alomejor – 29750 €, Telapegas – 32550 € |
| **Motor**                 | Grupo de botones         | Gasolina – 0 €, Diesel – 2000 €, Híbrido – 5000 €, Electrico – 10000 € |
| **Pintura**               | Lista selección única    | Gris triste – Sin coste, Rojo sangre – 250 €, Rojo pasión – 150 €, Azul noche – 175 €, Caramelo – 300 €, Mango – 275 € |
| **Extras**                | Checkbox (1 por cada)    | Navegador GPS – 500 €, Calefacción asientos – 250 €, Antena aleta tiburón – 50 €, Acceso y arranque sin llave – 150 €, Arranque en pendiente – 200 €, Cargador inalámbrico – 300 €, Control de crucero – 500 €, Detectar ángulo muerto – 350 €, Faros led automáticos – 400 €, Frenada emergencia – 375 € |
| **Pago financiado**       | Grupo botones            | Cuota inicial – 25%, Cuota final – 25%, Meses de financiación: 2 años, 5 años y 10 años. |

[Compra de coches en concesionario](./04-concesionario.php)

`5. Crear un script para confeccionar un presupuesto de un viaje turístico a una ciudad de Europa:`

a) Crear un formulario para recoger los datos del viaje y generar una respuesta con
todos los detalles elegidos, su coste desglosado y el total.

b) Los campos del formulario son:

| Campo                     | Tipo de campo          | Valores                                           |
|---------------------------|------------------------|---------------------------------------------------|
| Persona responsable del grupo | Texto                  |                                                   |
| Tlf                        | Teléfono               |                                                   |
| Email                      | Email                  |                                                   |
| Destino                    | Lista de selección única | París – 100 € / persona / día<br>Londrés – 120 € / p/d<br>Estocolmo – 200 € / p/d<br>Edinburgo – 175 € / p/d<br>Praga – 125 € / p/d<br>Viena – 150 € / p/d |
| Compañía aérea             | Lista de selección única | MiAir – Incluido<br>AirFly – Suplemento de 50 €/p<br>VuelaConmigo – Sup 75 €/p<br>ApedalesAir – Sup 150 €/p |
| Hotel                      | Lista de selección única | 3* - Incluido<br>4* - Sup 40€/p/d<br>5* - Sup 100€/p/d |
| Desayuno incluido          | Checkbox               | Suplemento 20 €/p/d                               |
| Número de personas         | Número                 | Entre 5 y 10                                      |
| Número de días             | Grupo de botones       | 5 días<br>10 días<br>15 días                      |
| Extras                     | Checkbox (por cada una)| Visita guiada en la ciudad – 200 €<br>Bus turístico – 30 €/p/d<br>2ª Maleta facturada – 20 €/p/d<br>Seguro de viaje – 30 €/p/d |

[Presupuesto viaje turístico](./05-agencia.php)

`6. Crear un script PHP con un sticky form en el que se registran propuestas de los donantes de una ONG a actuaciones en proyectos de ayuda al desarrollo en un país del tercer mundo.`

a) En cada petición hay que presentar los datos enviados en formato de tabla y
volver a generar el formulario con los datos de la petición anterior.

b) La página es autogenerada.

c) Los campos del formulario son:

| Campo            | Tipo de campo        | Valores                                        |
|------------------|----------------------|------------------------------------------------|
| Email            | Email                |                                                |
| Autorizo registro| Checkbox             |                                                |
| Cantidad         | Número               |                                                |
| Proyecto         | Lista de selección única | Agua potable<br>Escuela de primaria<br>Placas solares<br>Centro médico |
| Propuesta        | Área de texto        |                                                |


[Propuestas donantes ONG](./06-ong.php)

`7. Crear un script PHP con un formulario en el que se registran solicitudes de empleo en una empresa de empleo temporal donde se pueden subir archivos PDF con el curriculum vitae de los solicitantes de empleo.`

a) Los archivos se guardan en la carpeta curriculums de la raíz de documentos del
servidor. Si no está creada se crea con los permisos necesarios para poder crear
nuevos archivos.

b) La página es autogenerada.

c) Se valida que el archivo subido es PDF. Al guardarse el archivo se renombra con el dni de la persona que subió el archivo.

d) Los campos del formulario son:

| Campo                               | Tipo de Campo  | Valores/Formato                            |
|-------------------------------------|----------------|--------------------------------------------|
| **DNI**                             | Texto          | Formato 8 dígitos y 1 letra (ej. 12345678A) |
| **Curriculum**                      | Archivo (File) | Solo archivos PDF                         |
| **Nombre**                          | Texto          | Nombre completo del solicitante           |
| **Aceptación registro datos personales**      | Checkbox       | Debe estar marcado para continuar         |

e) Si el solicitante no acepta el registro de los datos personales, se cancela la subida del archivo y se devuelve el formulario con los datos enviados y un mensaje de error indicando que tiene que aceptar los datos.

[Solicitudes de Empleo](./07-solicitud-empleo.php)

`8. Crear un script PHP con un formulario en el que los usuarios pueden subir fotografías en diferentes formatos de imagen.`

a) Los archivos se guardan en la carpeta fotos/<login> de la raíz de
documentos del servidor, siendo <login> el del usuario que sube las fotos. Si
no está creada se crea con los permisos necesarios para poder crear nuevos
archivos.

b) La página es autogenerada.

c) Se valida que el archivo subido es JPG, PNG o WEBP. Al guardarse el archivo se
emplea el mismo nombre del archivo original del usuario.

d) Se impone un límite de formulario para el tamaño de los archivos de imagen a 150
KB. Opcionalmente, se pueden establecer los siguientes límites:

- Archivos jpg → 250 KB.
- Archivos png → 225 KB.
- Archivos webp → 200 KB.

e) Los campos del formulario son:

| Campo   | Tipo de campo | Valores                                |
|---------|----------------|----------------------------------------|
| Login   | Texto          | Solo letras minúsculas y dígitos numéricos |
| Foto    | File           |                                        |
| Título  | Text           |                                        |


f) La subida de archivos es cíclica y después de hacer una tiene que visualizarse la
lista de archivos subida por el usuario hasta ese momento

[Formulario para subir fotografías](./08-subir-fotografias.php)

`9. Búsqueda y filtrado de vehículos. Crea un formulario HTML que permita a un usuario buscar vehículos de acuerdo con ciertos criterios y procesar un archivo CSV para mostrar los resultados correspondientes.`

a) Formulario de búsqueda:

-   El formulario debe permitir al usuario introducir su correo electro

| Campo              | Descripción                                                                                      | Tipo                  | Validación                                        |
|--------------------|--------------------------------------------------------------------------------------------------|-----------------------|---------------------------------------------------|
| **Correo electrónico** | Permite al usuario ingresar su correo electrónico.                                               | **Email**             | Debe ser una dirección de correo válida.          |
| **Tipo de vehículo**  | Permite al usuario seleccionar el tipo de vehículo. Opciones: "Turismo", "Furgoneta".             | **Grupo de botones**     | Debe seleccionar una opción válida.               |
| **Marca del vehículo**| Permite al usuario seleccionar la marca del vehículo. Opciones: "Fiat", "Opel", "Mercedes".      | **Lista de selección única** | Debe seleccionar una opción válida.               |
| **Antigüedad**        | Permite al usuario ingresar la antigüedad del vehículo (valor numérico entre 1 y 5).             | **Número**            | El valor debe estar entre 1 y 5.                  |
| **ITV**               | Permite al usuario marcar si el vehículo tiene ITV.                                             | **Checkbox**          | Si está marcado, el vehículo tiene ITV, de lo contrario no. |
| **Archivo CSV**       | Permite al usuario subir un archivo CSV con los datos de los vehículos. Debe ser de tipo `text/csv` y no superar los 200KB. | **File input**        | Debe ser un archivo CSV válido.                   |

b) Validación de los datos:

-   El **correo electrónico** debe ser válido.
-   El **tipo de vehículo** debe ser uno de los disponibles en el formulario.
-   La **marca** seleccionada debe ser una de las opciones disponibles.
-   La **antigüedad** debe ser un número entre 1 y 5.
-   El archivo subido debe ser de tipo CSV y debe estar dentro del límite de tamaño (200KB).

c) Procesamiento del archivo CSV:

-   El archivo CSV debe ser procesado para extrar la información de los vehículos.
-   El archivo debe contener las siguientes columnas: **Tipo, Marca, Antigüedad y ITV**.
-   El archivo debe ser validado para asegurarse de que es un archivo CSV válido (en cuanto
    a extensión y tipo MIME).

d) Filtrado de datos:

-   Una vez procesado el archivo CSV, los datos deben ser filtrados según los parámetros de búsqueda proporcionados por el usuario en el formulario.

-   Si hay vehículos que coinciden con los parámetros de búsqueda (tipo, marca, antigüedad, ITV), se deben mostrar en una tabla. Si no hay coincidencias, se debe mostrar un mensaje indicando que no se encontraron vehículos con esos parámetros..

e) Errores:

-   Si algún dato no es válido (por ejemplo, un correo electrónico incorrecto o un archivo CSV con un formato no permitido), se deben mostrar los errores correspondientes en pantalla.

**Requerimiento:**

-   El formulario debe estar bien estructurado y presentar los campos correctamente.
-   Se debe validar tanto en el servidor (con PHP) como en el lado del cliente.
-   El archivo CSV debe ser procesado correctamente, y los datos deben ser mostrados en una tabla si cumplen con los criterios de búsqueda.

**Salida esperada:**

-   Si no hay errores en el formulario ni en el archivo CSV, se debe mostrar una tabla con los resultados filtrados.
-   Si hay errores, deben mostrarse los mensajes de error correspondientes y el formulario debe permitir corregir los datos ingresados.

[Solución ejercicio](./09-busqueda-coches/ejercicio-09.php)

[Archivo CSV](./09-busqueda-coches/vehiculos.csv)

`10. Crear un script PHP para gestionar la inscripción en cursos de formación profesional.`

a) Crear un formulario para recoger los datos del solicitante y generar una respuesta con todos los detalles de la inscripción, incluyendo los cursos seleccionados, el número de clases presenciales y el presupuesto total.

b) Si el solicitante se encuentra en situación de desempleo, deberá adjuntar un documento acreditativo en formato PDF para acceder a un descuento del 10% en el precio total.

c) Los campos del formulario son:

| Campo                   | Descripción                                                           | Tipo                    | Validación                                           |
|-------------------------|---------------------------------------------------------------------|-------------------------|------------------------------------------------------|
| **Email**               | Permite al usuario ingresar su correo electrónico.                 | **Email**               | Debe ser una dirección de correo válida.            |
| **Cursos**              | Permite seleccionar múltiples cursos.                              | **Checkbox (múltiple)** | Debe seleccionar al menos un curso.                 |
| **Nº Clases Presenciales** | Número de clases presenciales entre 5 y 10.                      | **Número**              | Debe ser un número entre 5 y 10.                    |
| **Situación de desempleo** | Indica si el usuario está en situación de desempleo.              | **Checkbox**            | Si se marca, se requiere subir la tarjeta de desempleo. |
| **Tarjeta de desempleo** | Archivo que acredita la situación de desempleo (solo si aplica).   | **Archivo (PDF)**       | Debe ser un archivo PDF válido.                     |

d) Una vez enviado el formulario, se generará una tabla con los datos del solicitante y un desglose del presupuesto total, incluyendo el descuento si corresponde.

e) Si el solicitante ha subido correctamente la tarjeta de desempleo, el sistema almacenará el archivo en una carpeta específica del servidor bajo el nombre del correo electrónico del usuario.

[Solución ejercicio Examen](./)