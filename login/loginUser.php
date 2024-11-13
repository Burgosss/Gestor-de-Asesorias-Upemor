<?php
include '../Static/connect/db.php';
session_start();

$user = $_POST['username'];
$pass = $_POST['password'];

if (!empty($user) && !empty($pass)) {
    $sql = "SELECT * FROM usuario WHERE usuario = '$user' AND password = '$pass';";
    $execute = mysqli_query($conn, $sql);

    if (mysqli_num_rows($execute) == 1) {
        $row = mysqli_fetch_assoc($execute);
        $id_usuario = $row['id_usuario'];

        $_SESSION['id_usuario'] = $id_usuario;

        $sqlAlumno = "SELECT * FROM alumno WHERE id_usuario = '$id_usuario'";
        $resultAlumno = mysqli_query($conn, $sqlAlumno);

        if (mysqli_num_rows($resultAlumno) == 1) {
            header("Location: ../student/Alumnoindex.php");
            exit();
        }

        $sqlProfesor = "SELECT * FROM profesor WHERE id_usuario = '$id_usuario'";
        $resultProfesor = mysqli_query($conn, $sqlProfesor);

        if (mysqli_num_rows($resultProfesor) == 1) {
            header("Location: ../teacher/profesorindex.php");
            exit();
        }

        $sqlAdmin = "SELECT * FROM admin WHERE id_usuario = '$id_usuario'";
        $resultAdmin = mysqli_query($conn, $sqlAdmin);

        if (mysqli_num_rows($resultAdmin) == 1) {
            header("Location: ../admin/adminIndex.php");
            exit();
        }

        header("Location:login.html");
        exit();
    } else {
        header("Location: login.html");
        exit();
    }
} else {
    header("Location: login.html");
    exit();
}
?>
