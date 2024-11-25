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
    $name = $_POST['nombre'];
    $period = $_POST['cuatrimestre'];
    $credit = $_POST['creditos'];
    $descri = $_POST['descripcion'];

    $checkQuery = "SELECT nombre FROM materia WHERE nombre = '$name'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Datos duplicados
        header("Location:../Vista/RegistrarMateria.php?error=materia_registrada");
        exit();
    }

    $sql = "INSERT INTO materia(nombre, cuatrimestre, creditos, descripcion) VALUES ('$name', '$period','$credit','$descri')";


    if($execute=mysqli_query($conn,$sql)){
        sleep(2);
        header("Location:../Vista/RegistrarMateria.php?success=registro_exitoso");
        
    }else{
        echo "ERROR. Crear materia";
    }
?>
<?php
    }else{
        header("Location: ../../../login/login.html");
    }
?>