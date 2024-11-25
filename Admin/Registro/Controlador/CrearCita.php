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
    $concepto = $_POST['concepto'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $estado = 'Reservada';
    $observaciones = $_POST['observaciones'];
    $id_materia = $_POST['id_materia'];
    $id_alumno = $_POST['id_alumno'];
    $id_profesor = $_POST['id_profesor'];

    $sql = "INSERT INTO asesoria (concepto, fecha, hora, estado, observaciones, id_materia, id_alumno, id_profesor) 
            VALUES ('$concepto', '$fecha', '$hora', '$estado', '$observaciones', '$id_materia', '$id_alumno', '$id_profesor')";

    if ($execute = mysqli_query($conn, $sql)) {
        // Redirigir tras la inserción exitosa
        sleep(2);
        header("Location: ../Vista/calendario.php");
    } else {
        echo "ERROR: No se pudo registrar la asesoría";
    }
} else {
    // Si no es admin, redirigir al inicio de sesión
    header("Location: ../../../login/login.html");
}
?>
