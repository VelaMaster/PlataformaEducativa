<?php
$conexion = mysqli_connect("127.0.0.1:3306", "root", "", "peis");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>