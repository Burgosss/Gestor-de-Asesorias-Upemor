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
    $lname = $_POST['apellido'];
    $genre = $_POST['genero'];
    $birth = $_POST['fec_nac'];
    $apellidos = explode(" ", trim($lname));
    $mail = $_POST['correo'];



    if (count($apellidos) > 1) {
        $userInitials = strtoupper(substr($apellidos[0], 0, 1)) . strtoupper(substr($apellidos[1], 0, 1));
    } else {
        $userInitials = strtoupper(substr($apellidos[0], 0, 1)) . 'X';
    }

    $user = $userInitials . strtoupper(substr($name, 0, 1)) . 'O24' . rand(1000, 9999);
    $password = rand(1000, 9999);

    $sql = "INSERT INTO usuario(usuario, password, nombre, apellido, genero, fec_nac, correo_electronico) 
            VALUES ('$user', '$password', '$name', '$lname', '$genre', '$birth', '$mail')";

    
    
    if($execute=mysqli_query($conn,$sql)){
        $sqlId="SELECT id_usuario FROM usuario where usuario='$user' and password='$password' and nombre='$name' AND apellido='$lname' AND correo_electronico='$mail' AND fec_nac='$birth'";
        
        if($result=mysqli_query($conn,$sqlId)){
            $row = mysqli_fetch_assoc($result);
            $id=$row['id_usuario'];
            $sqlP="INSERT INTO PROFESOR (id_usuario) VALUES ('$id')";
            if ($executeP=mysqli_query($conn,$sqlP)) {
                sleep(2);
                header("Location:../Vista/RegistrarProfesor.php");
            }else{
                echo"ERROR. Crear profesor";
            }

        }else{
            echo "ERROR. Obtener id del usuario";
        }
        
    }else{
        echo "ERROR. Crear usuario";
    }
?>
<?php }else{
        header("Location: ../../../login/login.html");
    } ?>