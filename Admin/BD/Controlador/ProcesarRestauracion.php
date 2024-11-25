<?php
session_start();
include '../../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
    // Verificar el tipo de archivo
    $fileType = $_FILES['backup_file']['type'];
    if ($fileType !== 'application/octet-stream' && pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION) !== 'sql') {
        echo "<script>alert('Solo se permiten archivos .sql'); window.location.href = '../Vista/restauracion.php';</script>";
        exit();
    }

    // Leer el contenido del archivo cargado
    $backupFile = $_FILES['backup_file']['tmp_name'];
    $sqlContent = file_get_contents($backupFile);

    if (!$sqlContent) {
        echo "<script>alert('Error al leer el archivo de respaldo.'); window.location.href = '../Vista/restauracion.php';</script>";
        exit();
    }

    // Separar las consultas SQL y ejecutarlas
    $queries = explode(";", $sqlContent);
    $error = false;

    mysqli_begin_transaction($conn);
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if (!mysqli_query($conn, $query)) {
                $error = true;
                break;
            }
        }
    }

    if ($error) {
        mysqli_rollback($conn);
        echo "<script>alert('Error al restaurar la base de datos. Verifique el archivo de respaldo.'); window.location.href = '../Vista/restauracion.php';</script>";
    } else {
        mysqli_commit($conn);
        echo "<script>alert('Restauración completada exitosamente.'); window.location.href = '../Vista/restauracion.php';</script>";
    }
} else {
    echo "<script>alert('Debe seleccionar un archivo de respaldo válido.'); window.location.href = '../Vista/restauracion.php';</script>";
}
?>
