<?php
$host = 'db'; // Nombre del servicio en docker-compose
$user = 'root';
$pass = '1234';
$db   = 'gestortareas'; // Todo en minúsculas como en DBeaver

$conexion = new mysqli($host, $user, $pass, $db);
// ... resto del código
?>