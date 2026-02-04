<?php
$host = "localhost";
$bd = "ristorante";
$user = "root";
$pass = "12345";

$conexion = new mysqli($host, $user, $pass, $bd);

if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
