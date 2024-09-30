<?php
$usuario=$_POST['usuario'];
$contrasena=$_POST['contrasena'];
session_start();
$_SESSION['usuario']=$usuario;

$conexion=mysqli_connect("localhost","root","","plataformaweb",);

include('db.php');
$consulta="SELECT*FROM usuarios where usuario = '$usuario' and contrasena='$contrasena'";
$resultado=mysqli_query($conexion,$consulta);

$filas=mysqli_num_rows($resultado);

if($filas){
    header("location:home.php");

}else{
    ?>
    <?php
    include("index.php");
    ?>
    <h1>ERROR EN LA AUTENTIFICACION</1>
    <?php
}
mysqli_free_result($resultado);
mysqli_close($conexion);