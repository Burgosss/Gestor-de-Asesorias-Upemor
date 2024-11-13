<?php
session_start();
include '../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del profesor
$sqlProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = '$id_usuario'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);
$rowProfesor = mysqli_fetch_assoc($resultProfesor);
$id_profesor = $rowProfesor['id_profesor'];

if (mysqli_num_rows($resultProfesor) == 1) {
?>
<?php

$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del profesor
$sqlProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = '$id_usuario'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);

if (mysqli_num_rows($resultProfesor) == 1) {
    $rowProfesor = mysqli_fetch_assoc($resultProfesor);
    $id_profesor = $rowProfesor['id_profesor'];

    // Manejar envío de mensaje
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destinatario'], $_POST['contenido'])) {
        $destinatario = mysqli_real_escape_string($conn, $_POST['destinatario']);
        $contenido = mysqli_real_escape_string($conn, $_POST['contenido']);

        // Obtener ID del alumno según la matrícula
        $sqlAlumno = "SELECT id_alumno FROM alumno WHERE id_usuario = (SELECT id_usuario FROM usuario WHERE usuario = '$destinatario')";
        $resultAlumno = mysqli_query($conn, $sqlAlumno);
        if (mysqli_num_rows($resultAlumno) == 1) {
            $rowAlumno = mysqli_fetch_assoc($resultAlumno);
            $id_alumno = $rowAlumno['id_alumno'];

            // Insertar el mensaje
            $sqlInsert = "INSERT INTO mensaje (contenido, fecha_hora, id_profesor, id_alumno, remitente) VALUES ('$contenido', NOW(), '$id_profesor', '$id_alumno', '$id_profesor')";
            if (mysqli_query($conn, $sqlInsert)) {
                header("Location: ../Vista/mensajeriaProfesor.php?id_alumno=$id_alumno");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Alumno no encontrado.";
        }
    }

    // Lógica para obtener mensajes
    if (isset($_GET['id_alumno'])) {
        $id_alumno = intval($_GET['id_alumno']);
        
        // Obtener mensajes entre profesor y alumno
        $sqlMensajes = "SELECT * FROM mensaje WHERE (id_profesor = '$id_profesor' AND id_alumno = '$id_alumno') OR (id_profesor = '$id_profesor' AND id_alumno = '$id_alumno') ORDER BY fecha_hora";
        $mensajes = mysqli_query($conn, $sqlMensajes);
    }
} else {
    header("Location: ../../login/login.html");
}
?>
<?php } else {
    header("Location: ../../login/login.html");
} ?>
