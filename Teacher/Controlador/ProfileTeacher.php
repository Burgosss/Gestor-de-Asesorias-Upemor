<?php
session_start();
include '../../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];  


$sqlProfesor = "SELECT * FROM profesor WHERE id_usuario = '$id_usuario'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);

if (mysqli_num_rows($resultProfesor) == 1) {
?>  
<?php

if (isset($_POST['update'])) {
    $id_usuario = $_GET['id_usuario'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $fec_nac = $_POST['fec_nac'];

    $query = "UPDATE usuario SET password = '$password', nombre = '$nombre', apellido = '$apellido', genero = '$genero', fec_nac = '$fec_nac' WHERE id_usuario = $id_usuario";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: ../Vista/PerfilProfesor.php");
    } else {
        echo "Error al actualizar los datos.";
    }
}
?>
<?php 
    }else{
        header("Location: ../../login/login.html");
    }
?>