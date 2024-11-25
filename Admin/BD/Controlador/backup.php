<?php
session_start();
include '../../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si el usuario es administrador
$sqlAdmin = "SELECT * FROM admin WHERE id_usuario = '$id_usuario'";
$resultAdmin = mysqli_query($conn, $sqlAdmin);

if (mysqli_num_rows($resultAdmin) == 1) {
    // Datos de conexión y configuración
    $backupPath = 'C:/xampp/backup/';
    $database = 'gestorasesorias';
    $fecha = date('Ymd_His');
    $backup_file = $backupPath . "Gestorasesorias_{$fecha}.sql";
    $tables = [];
    $error = false;

    // Crear directorio si no existe
    if (!file_exists($backupPath)) {
        mkdir($backupPath, 0777, true);
    }

    // Obtener todas las tablas de la base de datos
    $result = mysqli_query($conn, 'SHOW TABLES');
    if ($result) {
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }

        // Crear contenido del respaldo
        $sql = 'SET FOREIGN_KEY_CHECKS=0;' . "\n\n";
        $sql .= 'CREATE DATABASE IF NOT EXISTS ' . $database . ";\n\n";
        $sql .= 'USE ' . $database . ";\n\n";

        foreach ($tables as $table) {
            $resultTable = mysqli_query($conn, "SELECT * FROM $table");
            if ($resultTable) {
                $numFields = mysqli_num_fields($resultTable);
                $sql .= "DROP TABLE IF EXISTS $table;";
                $row2 = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE $table"));
                $sql .= "\n\n" . $row2[1] . ";\n\n";

                for ($i = 0; $i < $numFields; $i++) {
                    while ($row = mysqli_fetch_row($resultTable)) {
                        $sql .= "INSERT INTO $table VALUES(";
                        for ($j = 0; $j < $numFields; $j++) {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace("\n", "\\n", $row[$j]);
                            $sql .= isset($row[$j]) ? "\"$row[$j]\"" : "\"\"";
                            $sql .= $j < ($numFields - 1) ? ',' : '';
                        }
                        $sql .= ");\n";
                    }
                }
                $sql .= "\n\n";
            } else {
                $error = true;
                break;
            }
        }

        $sql .= 'SET FOREIGN_KEY_CHECKS=1;';

        // Guardar el respaldo en el archivo
        if (!$error) {
            if (file_put_contents($backup_file, $sql)) {
                header("Location: ../Vista/Respaldo.php?success=1"); // Notificar éxito
                exit();

            } else {
                header("Location: ../Vista/Respaldo.php?error=1"); // Notificar error
                exit();
            }
        } else {
            header("Location: ../Vista/Respaldo.php?error=1"); // Notificar error general
            exit();
        }
    } else {
        header("Location: ../Vista/Respaldo.php?error=1"); // Notificar error general
        exit();
    }
} else {
    header("Location: ../../../login/login.html");
}
?>
