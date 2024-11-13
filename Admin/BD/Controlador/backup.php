<?php
session_start();
include '../../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
    exit();
}

function generarCopiaSeguridad($rutaArchivo)
{
    // Comando mysqldump para generar la copia de seguridad
    $comando = "mysqldump -u root -p gestorasesorias > " . $rutaArchivo;
    system($comando, $output);

    return $output === 0;
}

function descargarArchivo($rutaArchivo)
{
    if (file_exists($rutaArchivo)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($rutaArchivo));
        readfile($rutaArchivo);
        exit;
    } else {
        echo "<p style='color:red;'>Error: No se pudo generar el archivo de respaldo.</p>";
    }
}

if (isset($_POST['backup'])) {
    $backupFilePath = 'C:/Gestorasesorias.sql';

    // Generar copia de seguridad
    if (generarCopiaSeguridad($backupFilePath)) {
        // Descargar el archivo generado
        descargarArchivo($backupFilePath);
    } else {
        echo "<p style='color:red;'>Error al crear la copia de seguridad de la base de datos.</p>";
    }
}
?>
