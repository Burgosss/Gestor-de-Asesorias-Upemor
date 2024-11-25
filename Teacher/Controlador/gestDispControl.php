<?php
include '../../Static/connect/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

function obtenerIdProfesor($id_usuario) {
    global $conn;
    $sqlProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = '$id_usuario'";
    $resultProfesor = mysqli_query($conn, $sqlProfesor);
    $profesor = mysqli_fetch_assoc($resultProfesor);
    return $profesor['id_profesor'];
}

function agregarOActualizarDisponibilidad($id_profesor, $dia, $hora_inicio, $hora_fin) {
    global $conn;
    $sqlCheck = "SELECT * FROM disponibilidad WHERE dia = '$dia' AND id_profesor = '$id_profesor'";
    $resultCheck = mysqli_query($conn, $sqlCheck);

    if (mysqli_num_rows($resultCheck) > 0) {
        // Si ya existe, actualizar la disponibilidad
        $sqlUpdate = "UPDATE disponibilidad 
                      SET hora_inicio = '$hora_inicio', hora_fin = '$hora_fin' 
                      WHERE dia = '$dia' AND id_profesor = '$id_profesor'";
        mysqli_query($conn, $sqlUpdate);
    } else {
        // Si no existe, insertar nueva disponibilidad
        $sqlInsert = "INSERT INTO disponibilidad (dia, hora_inicio, hora_fin, id_profesor) 
                      VALUES ('$dia', '$hora_inicio', '$hora_fin', '$id_profesor')";
        mysqli_query($conn, $sqlInsert);
    }
}

function eliminarDisponibilidad($id_disponibilidad) {
    global $conn;
    $sqlDelete = "DELETE FROM disponibilidad WHERE iddisponibilidad = $id_disponibilidad";
    mysqli_query($conn, $sqlDelete);
}

function obtenerDisponibilidades($id_profesor) {
    global $conn;
    $sqlDisponibilidad = "SELECT * FROM disponibilidad WHERE id_profesor = '$id_profesor'";
    return mysqli_query($conn, $sqlDisponibilidad);
}
