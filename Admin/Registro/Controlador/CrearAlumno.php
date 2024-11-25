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
$period = $_POST['cuatrimestre'];
$mail = $_POST['correo'];
$apellidos = explode(" ", trim($lname));

if (count($apellidos) > 1) {
    $userInitials = strtoupper(substr($apellidos[0], 0, 1)) . strtoupper(substr($apellidos[1], 0, 1));
} else {
    $userInitials = strtoupper(substr($apellidos[0], 0, 1)) . 'X';
}

$user = $userInitials . strtoupper(substr($name, 0, 1)) . 'O24' . rand(1000, 9999);
$password = rand(1000, 9999);

// Verificar si el correo ya existe
$checkEmailQuery = "SELECT id_usuario FROM usuario WHERE correo_electronico = '$mail'";
$checkEmailResult = mysqli_query($conn, $checkEmailQuery);

if (mysqli_num_rows($checkEmailResult) > 0) {
    // Correo ya registrado, redirigir con mensaje de error
    header("Location:../Vista/RegistrarAlumno.php?error=correo_registrado");
    exit();
}

// Insertar datos en la tabla usuario
$sql = "INSERT INTO usuario(usuario, password, nombre, apellido, genero, fec_nac, correo_electronico) 
        VALUES ('$user', '$password', '$name', '$lname', '$genre', '$birth', '$mail')";

if ($execute = mysqli_query($conn, $sql)) {
    $sqlId = "SELECT id_usuario FROM usuario WHERE usuario = '$user' AND password = '$password' AND nombre = '$name' AND apellido = '$lname' AND correo_electronico = '$mail' AND fec_nac = '$birth'";
    
    if ($result = mysqli_query($conn, $sqlId)) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id_usuario'];
        $sqlA = "INSERT INTO alumno (cuatrimestre, id_usuario) VALUES ('$period', '$id')";
        if ($executeA = mysqli_query($conn, $sqlA)) {
            header("Location:../Vista/RegistrarAlumno.php?success=registro_exitoso");
        } else {
            echo "ERROR. Crear alumno";
        }
    } else {
        echo "ERROR. Obtener id del usuario";
    }
} else {
    echo "ERROR. Crear usuario";
}
?>
<?php }else{
        header("Location: ../../../login/login.html");
    } ?>