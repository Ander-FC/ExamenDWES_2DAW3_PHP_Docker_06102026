<?php
// Datos para conectarse a la base de datos CAE
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$baseDatos = "CAE";

// Creamos la conexión con MySQL
$conexion = new mysqli($servidor, $usuario, $contrasena, $baseDatos);

// Mostramos el error si no se pudo conectar
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
