<?php

$conexion = mysqli_connect("localhost", "root", "", "plataformaweb");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>
