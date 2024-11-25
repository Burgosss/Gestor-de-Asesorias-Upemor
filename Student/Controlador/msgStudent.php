<?php
session_start();
include '../../Static/connect/db.php';

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del alumno
$sqlAlumno = "SELECT id_alumno FROM alumno WHERE id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);

if (mysqli_num_rows($resultAlumno) == 1) {
    $rowAlumno = mysqli_fetch_assoc($resultAlumno);
    $id_alumno = $rowAlumno['id_alumno'];

    // Manejar envío de mensaje
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destinatario'], $_POST['contenido'])) {
        $destinatario = mysqli_real_escape_string($conn, $_POST['destinatario']); // ID del profesor
        $contenido = mysqli_real_escape_string($conn, $_POST['contenido']); // Contenido del mensaje

        // Insertar el mensaje
        $sqlInsert = "
            INSERT INTO mensaje (contenido, fecha_hora, id_profesor, id_alumno, remitente)
            VALUES ('$contenido', NOW(), '$destinatario', '$id_alumno', '$id_alumno')
        ";

        if (mysqli_query($conn, $sqlInsert)) {
            // Redirigir a la página de mensajería del alumno con el profesor
            header("Location: ../Vista/mensajeriaAlumno.php?id_profesor=$destinatario");
            exit();
        } else {
            echo "Error al enviar el mensaje: " . mysqli_error($conn);
        }
    } else {
        echo "Datos incompletos. Por favor, selecciona un profesor y escribe un mensaje.";
    }
} else {
    echo "Alumno no encontrado.";
}
?>
