<?php
session_start();
include '../../Static/connect/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

// Validar si se recibió el ID de la asesoría
if (!isset($_GET['id_asesoria'])) {
    header("Location: compartirMaterial.php");
    exit();
}

$id_asesoria = $_GET['id_asesoria'];

// Obtener configuraciones de la base de datos
$sqlPesoMaximo = "SELECT peso FROM pesoarchivos WHERE idpesoArchivos = 1";
$resultPesoMaximo = mysqli_query($conn, $sqlPesoMaximo);
$peso_maximo = mysqli_fetch_assoc($resultPesoMaximo)['peso'] * 1024 * 1024; // Convertir a bytes

$sqlExtensiones = "SELECT extension FROM extensionesArchivos";
$resultExtensiones = mysqli_query($conn, $sqlExtensiones);
$extensiones_permitidas = [];
while ($row = mysqli_fetch_assoc($resultExtensiones)) {
    $extensiones_permitidas[] = $row['extension'];
}

// Manejar la subida del archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES['archivo']['name'];
        $ruta_temporal = $_FILES['archivo']['tmp_name'];
        $tamanio_archivo = $_FILES['archivo']['size'];
        $extension_archivo = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        $directorio_destino = '../../files/';

        // Validar extensión
        if (!in_array($extension_archivo, $extensiones_permitidas)) {
            echo "<script>alert('Extensión no permitida. Las extensiones permitidas son: " . implode(", ", $extensiones_permitidas) . ".');</script>";
        } 
        // Validar tamaño
        elseif ($tamanio_archivo > $peso_maximo) {
            echo "<script>alert('El archivo excede el tamaño máximo permitido de " . ($peso_maximo / 1024 / 1024) . " MB.');</script>";
        } 
        // Si pasa las validaciones
        else {
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }

            $ruta_final = $directorio_destino . time() . '_' . $nombre_archivo;
            if (move_uploaded_file($ruta_temporal, $ruta_final)) {
                $sqlInsert = "
                    INSERT INTO material (id_asesoria, nombre_archivo, ruta_archivo, fecha_subida)
                    VALUES ('$id_asesoria', '$nombre_archivo', '$ruta_final', NOW())
                ";
                if (mysqli_query($conn, $sqlInsert)) {
                    echo "<script>alert('Material subido con éxito.'); window.location.href = 'subirMaterial.php?id_asesoria=$id_asesoria';</script>";
                } else {
                    echo "<script>alert('Error al guardar en la base de datos.');</script>";
                }
            } else {
                echo "<script>alert('Error al mover el archivo.');</script>";
            }
        }
    } else {
        echo "<script>alert('Por favor, selecciona un archivo válido.');</script>";
    }
}

// Manejar la eliminación de un archivo
if (isset($_GET['eliminar'])) {
    $id_material = $_GET['eliminar'];
    $sqlArchivo = "SELECT ruta_archivo FROM material WHERE id_material = '$id_material'";
    $resultArchivo = mysqli_query($conn, $sqlArchivo);

    if ($resultArchivo && $rowArchivo = mysqli_fetch_assoc($resultArchivo)) {
        $ruta_archivo = $rowArchivo['ruta_archivo'];
        if (file_exists($ruta_archivo)) {
            unlink($ruta_archivo);
        }

        $sqlDelete = "DELETE FROM material WHERE id_material = '$id_material'";
        mysqli_query($conn, $sqlDelete);
        echo "<script>alert('Archivo eliminado con éxito.'); window.location.href = 'subirMaterial.php?id_asesoria=$id_asesoria';</script>";
    } else {
        echo "<script>alert('Error al eliminar el archivo.');</script>";
    }
}

// Obtener los archivos relacionados con la asesoría
$sqlMateriales = "SELECT id_material, nombre_archivo, fecha_subida FROM material WHERE id_asesoria = '$id_asesoria'";
$resultMateriales = mysqli_query($conn, $sqlMateriales);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Material</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
</head>

<body>
    <header>
        <div class="container"> 
            <h1>
                <img src="../../Static/img/logo.png" alt="Logo UPEMOR">Upemor - Sistema de Gestión de Asesorías
            </h1>
            <nav>
                <ul>
                    <li><a href="../ProfesorIndex.php">Inicio</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div>
            <h2 style="margin-left: 5%;"><a href="../Vista/compartirMaterial.php">< Regresar</a></h2>
            <h1 style="text-align: center;">Subir Archivo</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p>
                A continuación selecciona un archivo para compartir con el alumno. Una vez elegido, selecciona la
                opción "Subir archivo" para compartirlo.
            </p>
        </div>

        <div>
            <form method="POST" enctype="multipart/form-data">
                <label for="archivo">Selecciona un archivo:</label>
                <input type="file" name="archivo" id="archivo" required>
                <br><br>
                <button type="submit">Subir Archivo</button>
                <br><br>
                <!-- Mostrar extensiones permitidas -->
                <p>Extensiones permitidas: <?php echo implode(", ", $extensiones_permitidas); ?></p>
                <!-- Mostrar peso máximo permitido -->
                <p>Peso máximo del archivo: <?php echo number_format($peso_maximo / 1024 / 1024, 2); ?> MB</p>
            </form>
        </div>

        <div>
            <h2 style="text-align: center;">Archivos actuales</h2>
            <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Nombre del Archivo</th>
                        <th>Fecha de Subida</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rowMaterial = mysqli_fetch_assoc($resultMateriales)) { ?>
                        <tr>
                            <td><?php echo $rowMaterial['nombre_archivo']; ?></td>
                            <td><?php echo $rowMaterial['fecha_subida']; ?></td>
                            <td>
                                <button onclick="confirmarEliminacion(<?php echo $rowMaterial['id_material']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Función para confirmar la eliminación
        function confirmarEliminacion(idMaterial) {
            if (confirm("¿Estás seguro de que deseas eliminar este archivo?")) {
                // Redirige al archivo PHP con el parámetro GET
                window.location.href = `subirMaterial.php?id_asesoria=<?php echo $id_asesoria; ?>&eliminar=${idMaterial}`;
            }
        }
    </script>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>

</html>

