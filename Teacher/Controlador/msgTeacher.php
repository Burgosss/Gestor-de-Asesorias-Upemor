<?php
session_start();
include '../../Static/connect/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del profesor
$sqlProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = '$id_usuario'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);

if (mysqli_num_rows($resultProfesor) == 1) {
    $rowProfesor = mysqli_fetch_assoc($resultProfesor);
    $id_profesor = $rowProfesor['id_profesor'];

    // Manejar envío de mensaje
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destinatario'], $_POST['contenido'])) {
        $id_alumno = intval($_POST['destinatario']); // ID del alumno desde el formulario
        $contenido = mysqli_real_escape_string($conn, $_POST['contenido']);

        // Verificar que el ID del alumno es válido
        $sqlAlumno = "SELECT id_alumno FROM alumno WHERE id_alumno = '$id_alumno'";
        $resultAlumno = mysqli_query($conn, $sqlAlumno);

        if (mysqli_num_rows($resultAlumno) == 1) {
            // Insertar el mensaje
            $sqlInsert = "
                INSERT INTO mensaje (contenido, fecha_hora, id_profesor, id_alumno, remitente)
                VALUES ('$contenido', NOW(), '$id_profesor', '$id_alumno', '$id_profesor')
            ";
            if (mysqli_query($conn, $sqlInsert)) {
                header("Location: ../Vista/mensajeriaProfesor.php?id_alumno=$id_alumno");
                exit();
            } else {
                echo "Error al enviar el mensaje: " . mysqli_error($conn);
            }
        } else {
            echo "Error: El alumno seleccionado no existe.";
        }
    } else {
        echo "Error: Datos incompletos en el formulario.";
    }
} else {
    echo "Error: No se encontró el profesor asociado a esta cuenta.";
}
?>
