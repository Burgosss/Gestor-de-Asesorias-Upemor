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
    if (isset($_GET['id_materia'])) {
        $id_materia=$_GET['id_materia'];
        $delete="delete from materia where id_materia=$id_materia;";
        if($exec=mysqli_query($conn,$delete)){
            sleep(2);
            header('Location:../Vista/EstadoMaterias.php');
        }else{
            echo "ERROR. Eliminar materia";
        }
            
        }
?>
<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>