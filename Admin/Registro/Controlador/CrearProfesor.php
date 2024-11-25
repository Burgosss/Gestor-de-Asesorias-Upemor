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
    $mail = $_POST['correo'];
    $materias = $_POST['materias']; // Array con las materias seleccionadas

    // Generar usuario y contraseña
    $apellidos = explode(" ", trim($lname));
    $userInitials = (count($apellidos) > 1) 
        ? strtoupper(substr($apellidos[0], 0, 1)) . strtoupper(substr($apellidos[1], 0, 1))
        : strtoupper(substr($apellidos[0], 0, 1)) . 'X';
    $user = $userInitials . strtoupper(substr($name, 0, 1)) . 'O24' . rand(1000, 9999);
    $password = rand(1000, 9999);

    // Verificar si el correo ya existe
    $checkEmailQuery = "SELECT id_usuario FROM usuario WHERE correo_electronico = '$mail'";
    $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkEmailResult) > 0) {
        // Correo ya registrado, redirigir con mensaje de error
        header("Location:../Vista/RegistrarProfesor.php?error=correo_registrado");
        exit();
    }

    // Insertar en tabla usuario
    $sql = "INSERT INTO usuario(usuario, password, nombre, apellido, genero, fec_nac, correo_electronico) 
            VALUES ('$user', '$password', '$name', '$lname', '$genre', '$birth', '$mail')";

    if (mysqli_query($conn, $sql)) {
        $id_usuario = mysqli_insert_id($conn);

        // Insertar en tabla profesor
        $sqlProfesor = "INSERT INTO profesor (id_usuario) VALUES ('$id_usuario')";
        if (mysqli_query($conn, $sqlProfesor)) {
            $id_profesor = mysqli_insert_id($conn);

            // Insertar relaciones profesor-materia
            foreach ($materias as $id_materia) {
                $sqlRelacion = "INSERT INTO profesor_materia (id_profesor, id_materia) VALUES ('$id_profesor', '$id_materia')";
                if (!mysqli_query($conn, $sqlRelacion)) {
                    echo "Error al insertar relación profesor-materia.";
                    exit();
                }
            }

            // Redirigir con éxito
            header("Location: ../Vista/RegistrarProfesor.php?success=registro_exitoso");
            exit();
        } else {
            echo "Error al insertar el profesor.";
        }
    } else {
        echo "Error al insertar el usuario.";
    }
?>
<?php }else{
        header("Location: ../../../login/login.html");
    } ?>