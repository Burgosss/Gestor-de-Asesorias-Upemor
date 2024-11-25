<?php
session_start();
include '../../../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];  


$sqlAdmin = "SELECT * FROM admin WHERE id_usuario = '$id_usuario'";
$resultAdmin = mysqli_query($conn, $sqlAdmin);

if (mysqli_num_rows($resultAdmin) == 1) {
?>  
<?php

if (isset($_POST['update'])) {
    $id_usuario = $_GET['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $fec_nac = $_POST['fec_nac'];
    $cuatrimestre = $_POST['cuatrimestre'];


    $query = "UPDATE usuario SET  nombre = '$nombre', apellido = '$apellido', genero = '$genero', fec_nac = '$fec_nac' WHERE id_usuario = $id_usuario";
    $updt= "UPDATE alumno SET cuatrimestre = '$cuatrimestre' WHERE id_usuario='$id_usuario';";

    $execute=mysqli_query($conn,$updt);
    $result = mysqli_query($conn, $query);

    if($execute){
        if ($result) {
            header("Location: ../Vista/EstadoAlumnos.php");
        } else {
            echo "ERROR. Actualizar Usuario";
        }
    }else{
        echo "ERROR. Actualizar Alumno.";
    }
    
}
?>
<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>