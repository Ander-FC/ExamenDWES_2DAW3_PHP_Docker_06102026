<?php // conexion a la base de datos m
include "conexion.php";

$profesion = "soldadura"; // valor por defecto
if (isset($_GET["tipo"])) { // comprobamos si se ha enviado el parámetro "tipo" en la URL
    $profesion = $_GET["tipo"]; // almacenamos el valor del parámetro en la variable $profesion
}

// Guardamos la solicitud cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos los datos del formulario
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $dni = $_POST["dni"];
    $f_nac = $_POST["f_nac"];
    $tlf = $_POST["tlf"];
    $email = $_POST["email"];
    $profesion = $_POST["profesion"];
    $jornadaParcial = (int) $_POST["jornadaParcial"];
    $idiomas = ""; 

    // Unimos los idiomas seleccionados en un solo texto
    if (isset($_POST["idiomas"])) {
        $idiomas = implode(", ", $_POST["idiomas"]); // implode convierte el array de idiomas en una cadena separada por comas, para que se pueda almacenar en la base de datos 
    }

    // Preparamos la consulta para insertar la solicitud
    $insercion = $conexion->prepare(
        "INSERT INTO SOLICITUD (nombre, apellidos, dni, f_nac, tlf, email, profesion, jornadaParcial, idiomas)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)" // las ? son marcadores de posición para los valores que se van a insertar en la base de datos
    );

    if (!$insercion) {
        die("Error al preparar el guardado: " . $conexion->error);
    }

    // Vinculamos los datos del formulario a la consulta
    $insercion->bind_param( // bind_param vincula los valores de las variables a los marcadores de posición en la consulta SQL
        "sssssssis", // "s" para string, "i" para integer
        $nombre,
        $apellidos,
        $dni,
        $f_nac,
        $tlf,
        $email,
        $profesion,
        $jornadaParcial,
        $idiomas
    );

    // Ejecutamos el INSERT para guardar la solicitud
    if (!$insercion->execute()) {
        die("Error al guardar la solicitud: " . $insercion->error);
    }
}
?>
<html>
    <head>
        <title>Examen de Desarrollo web en entorno servidor</title>
        <link rel="icon" type="image/png" sizes="32x32" href="../imagenes/favicon.jpeg">
        <link rel="stylesheet" type="text/css" href="../estilos/estilos.css">

    </head>
    <body>
        <h1>Centro de Ayuda al Empleo</h1>
        <h2>    
            <?php
                $tipo = "Soldadura";
                if ($profesion == 'informatica'){ // comprobamos el valor de la variable $profesion 
                    $tipo = 'Informática';
                } elseif ($profesion == 'socio'){
                    $tipo = "Asistencia Sociosanitaria";
                }
                echo "Solicitudes de $tipo";
            ?>
        </h2>

        
        <?php 
        // Aquí tenéis que crear la tabla de solicitantes de ese tipo

        $consulta = $conexion->prepare( // preparamos la consulta SQL para seleccionar los solicitantes de la profesión especificada
            "SELECT nombre, apellidos, dni, f_nac, tlf, email, jornadaParcial, idiomas
            FROM SOLICITUD
            WHERE profesion = ?" // filtra las filas para que solo se muestren las solicitudes de la profesión especificada
        );
        $consulta->bind_param("s", $profesion); // vinculamos la profesión al marcador ?
        if (!$consulta->execute()) {
            die("Error al consultar las solicitudes: " . $consulta->error);
        }
        $resultado = $consulta->get_result(); // guardamos las filas encontradas
        ?>
        <table> <!-- Tabla de solicitantes -->
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>DNI</th>
                <th>Fecha de nacimiento</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Jornada</th>
                <th>Idiomas</th>
            </tr>
            <?php while ($solicitud = $resultado->fetch_assoc()) { ?> <!-- recorremos las filas del resultado y mostramos los datos en la tabla -->
                <tr>
                    <td><?php echo htmlspecialchars($solicitud["nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($solicitud["apellidos"]); ?></td>
                    <td><?php echo htmlspecialchars($solicitud["dni"]); ?></td>
                    <td><?php echo htmlspecialchars($solicitud["f_nac"]); ?></td>
                    <td><?php echo htmlspecialchars($solicitud["tlf"]); ?></td>
                    <td><?php echo htmlspecialchars($solicitud["email"]); ?></td>
                    <td><?php echo $solicitud["jornadaParcial"] ? "Parcial" : "Completa"; ?></td>
                    <td><?php echo htmlspecialchars($solicitud["idiomas"]); ?></td>
                </tr>
            <?php } ?>
        </table>
        <button onclick="location.href='../html/index.html'">Volver al formulario</button>
    </body>
</html>
