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
    if (isset($_GET['id_usuario'])) {
        $id_usuario=$_GET['id_usuario'];
        $deleteSon="delete from usuario where id_usuario=$id_usuario;";
        $deleteFather="delete from alumno where id_usuario=$id_usuario;";
        if($execute=mysqli_query($conn,$deleteFather)){
            if($exec=mysqli_query($conn,$deleteSon)){
                sleep(2);
                header('Location:../Vista/EstadoAlumnos.php');
            }else{
                echo "Eliminar usuario";
            }
            
        }else{
            echo "ERROR. Eliminar alumno";
        }
    }
?>
<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>