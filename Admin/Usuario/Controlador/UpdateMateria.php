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
    $id_materia = $_GET['id_materia'];
    $nombre = $_POST['nombre'];
    $cuatrimestre = $_POST['cuatrimestre'];

    $checkQuery = "SELECT nombre FROM materia WHERE nombre = '$nombre' AND id_materia != $id_materia";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Datos duplicados
        header("Location:../Vista/EstadoMaterias.php?error=materia_registrada");
        exit();
    }

    $query = "UPDATE materia SET nombre = '$nombre', cuatrimestre = '$cuatrimestre' WHERE id_materia = $id_materia";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: ../Vista/EstadoMaterias.php");
    } else {
        echo "Error al actualizar los datos.";
    }
}
?>
<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>