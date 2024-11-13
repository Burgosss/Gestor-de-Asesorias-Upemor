<?php
session_start();
include '../../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
    $backupFile = $_FILES['backup_file']['tmp_name'];

    // Ejecuta el archivo SQL usando `mysql` para restaurar la base de datos
    $command = "mysql -u usuario -pcontraseña nombre_base_datos < $backupFile";
    exec($command, $output, $result);

    if ($result == 0) {
        echo "<script>alert('Restauración completada exitosamente.'); window.location.href = '../Vista/restauracion.php';</script>";
    } else {
        echo "<script>alert('Error al restaurar la base de datos.'); window.location.href = '../Vista/restauracion.php';</script>";
    }
} else {
    echo "<script>alert('Debe seleccionar un archivo de respaldo válido.'); window.location.href = '../Vista/restauracion.php';</script>";
}
?>
