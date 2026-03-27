<?php
$host = "db";
$user = "root";       // Tu usuario de MySQL
$pass = "";           // Tu contraseña
$db   = "gestorTareas"; // El nombre que sale en tu DBeaver

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>