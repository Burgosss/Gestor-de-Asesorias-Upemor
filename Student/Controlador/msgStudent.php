<?php
session_start();
include '../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];  


$sqlAlumno = "SELECT * FROM alumno WHERE id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);

if (mysqli_num_rows($resultAlumno) == 1) {
?> 

<?php

// Obtener ID del alumno
$sqlAlumno = "SELECT id_alumno FROM alumno WHERE id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);
$rowAlumno = mysqli_fetch_assoc($resultAlumno);
$id_alumno = $rowAlumno['id_alumno'];

// Manejar envío de mensaje
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destinatario'], $_POST['contenido'])) {
    $destinatario = mysqli_real_escape_string($conn, $_POST['destinatario']);
    $contenido = mysqli_real_escape_string($conn, $_POST['contenido']);

    // Obtener ID del profesor según la matrícula
    $sqlProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = (SELECT id_usuario FROM usuario WHERE usuario = '$destinatario')";
    $resultProfesor = mysqli_query($conn, $sqlProfesor);
    if (mysqli_num_rows($resultProfesor) == 1) {
        $rowProfesor = mysqli_fetch_assoc($resultProfesor);
        $id_profesor = $rowProfesor['id_profesor'];

        // Insertar el mensaje
        $sqlInsert = "INSERT INTO mensaje (contenido, fecha_hora, id_profesor, id_alumno, remitente) VALUES ('$contenido', NOW(), '$id_profesor', '$id_alumno', '$id_alumno')";
        if (mysqli_query($conn, $sqlInsert)) {
            header("Location: ../Vista/mensajeriaAlumno.php?id_profesor=$id_profesor");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Profesor no encontrado.";
    }
}

// Lógica para obtener mensajes
if (isset($_GET['id_profesor'])) {
    $id_profesor = intval($_GET['id_profesor']);
    
    // Obtener mensajes entre alumno y profesor
    $sqlMensajes = "SELECT * FROM mensaje WHERE (id_alumno = '$id_alumno' AND id_profesor = '$id_profesor') OR (id_alumno = (SELECT id_alumno FROM alumno WHERE id_usuario = '$id_usuario') AND id_profesor = '$id_profesor') ORDER BY fecha_hora";
    $mensajes = mysqli_query($conn, $sqlMensajes);
}

?>

<?php }else{
        header("Location: ../../login/login.html");
    } ?>