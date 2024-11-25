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
if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Verificar si el profesor tiene asesorías pendientes
    $queryCheckAsesorias = "
        SELECT a.estado
        FROM profesor p
        LEFT JOIN asesoria a ON p.id_profesor = a.id_profesor
        WHERE p.id_usuario = $id_usuario
        AND a.estado IN ('Aprobada', 'Reservada')
        LIMIT 1
    ";
    $resultCheck = mysqli_query($conn, $queryCheckAsesorias);

    if (mysqli_num_rows($resultCheck) > 0) {
        // Si tiene asesorías pendientes, redirigir con error
        header('Location: ../Vista/EstadoProfesores.php?error=asesorias_pendientes');
        exit();
    }

    // Obtener el id del profesor
    $getProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = $id_usuario";
    $getProfesorQuery = mysqli_query($conn, $getProfesor);
    $rowProfesor = mysqli_fetch_assoc($getProfesorQuery);

    if (!$rowProfesor) {
        echo "El profesor no existe.";
        exit();
    }

    $id_profesor = $rowProfesor['id_profesor'];

    // Eliminar registros en las tablas relacionadas
    $deleteProfesorMateria = "DELETE FROM profesor_materia WHERE id_profesor = $id_profesor";
    $deleteDisponibilidad = "DELETE FROM disponibilidad WHERE id_profesor = $id_profesor";
    $deleteMensaje = "DELETE FROM mensaje WHERE id_profesor = $id_profesor";
    $deleteAsesoria = "DELETE FROM asesoria WHERE id_profesor = $id_profesor";

    // Ejecutar las eliminaciones
    mysqli_query($conn, $deleteProfesorMateria);
    mysqli_query($conn, $deleteDisponibilidad);
    mysqli_query($conn, $deleteMensaje);
    mysqli_query($conn, $deleteAsesoria);

    // Eliminar el profesor y usuario
    $deleteUsuario = "DELETE FROM usuario WHERE id_usuario = $id_usuario";
    $deleteProfesor = "DELETE FROM profesor WHERE id_usuario = $id_usuario";

    if (mysqli_query($conn, $deleteProfesor) && mysqli_query($conn, $deleteUsuario)) {
        sleep(2); // Pausa para asegurar que las eliminaciones se completen
        header('Location: ../Vista/EstadoProfesores.php');
    } else {
        echo "ERROR. No se pudo eliminar el profesor o el usuario.";
    }
}
?>
<?php 
} else {
    header("Location: ../../../login/login.html");
}
?>
