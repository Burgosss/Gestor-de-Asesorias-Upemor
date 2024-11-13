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
    /* INICIA SESION SOLO SI NO SE HA INICIADO ANTES */ 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
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

    // Manejar envío de mensaje
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destinatario'], $_POST['contenido'])) {
        $id_alumno = intval($_POST['destinatario']);
        $contenido = mysqli_real_escape_string($conn, $_POST['contenido']);

        // Insertar el mensaje en la base de datos
        $sqlInsert = "INSERT INTO mensaje (contenido, fecha_hora, id_profesor, id_alumno, remitente) 
                    VALUES ('$contenido', NOW(), '$id_profesor', '$id_alumno', '$id_profesor')";
        if (mysqli_query($conn, $sqlInsert)) {
            header("Location: ../Vista/mensajeriaProfesor.php?id_alumno=$id_alumno");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Datos del formulario no válidos.";
    }

    // Lógica para obtener mensajes
    if (isset($_GET['id_alumno'])) {
        $id_alumno = intval($_GET['id_alumno']);
        
        // Obtener mensajes entre profesor y alumno
        $sqlMensajes = "SELECT * FROM mensaje 
                        WHERE id_profesor = '$id_profesor' 
                        AND id_alumno = '$id_alumno' 
                        ORDER BY fecha_hora";
        $mensajes = mysqli_query($conn, $sqlMensajes);
    }
    ?>
<?php } else {
    header("Location: ../../login/login.html");
} ?>
