<?php
// Inicia la sesión
session_start();
include '../../Static/connect/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si el usuario es un alumno
$sqlAlumno = "SELECT id_alumno FROM alumno WHERE id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);

if (mysqli_num_rows($resultAlumno) == 1) {
    $rowAlumno = mysqli_fetch_assoc($resultAlumno);
    $id_alumno = $rowAlumno['id_alumno'];

    // Verificar si se recibió el ID de la asesoría
    if (!isset($_GET['id_asesoria'])) {
        header("Location: ../../login/login.html");
        exit();
    }

    $id_asesoria = $_GET['id_asesoria'];

    // Consultar los materiales asociados a la asesoría
    $sqlMaterial = "
        SELECT id_material, nombre_archivo, ruta_archivo, fecha_subida
        FROM material
        WHERE id_asesoria = '$id_asesoria'
    ";
    $resultMaterial = mysqli_query($conn, $sqlMaterial);

} else {
    header("Location: ../../login/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Didáctico</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="container"> 
            <h1>
                <img src="../../Static/img/logo.png" alt="Logo UPEMOR"> Upemor - Sistema de Gestión de Asesorías
            </h1>
            <nav>
                <ul>
                    <li><a href="../alumnoIndex.php">Inicio</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div> 
             <!-- Enlace para regresar -->
            <h2 style="margin-left: 5%;"><a href="../alumnoIndex.php">Regresar</a></h2>
            <h1 style="display: flex; justify-content: center;">Material Didáctico</h1>
        </div>
       
        <div class="recuadro_indicaciones"> 
            <!-- Descripción -->
            <p style="margin-left: 5%;">
                Descarga el material compartido por tu profesor haciendo click en "Descargar".
            </p>
        </div>
        
        <div style="margin: 5%; text-align: center;">
            <!-- Tabla de materiales -->
            <table border="1" style="width: 90%; margin: auto; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Nombre del Archivo</th>
                        <th>Fecha de Subida</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultMaterial) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($resultMaterial)): ?>
                            <tr>
                                <td><?php echo $row['nombre_archivo']; ?></td>
                                <td><?php echo $row['fecha_subida']; ?></td>
                                <td>
                                    <a href="<?php echo $row['ruta_archivo']; ?>" download>Descargar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No hay material didáctico disponible para esta asesoría.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Pie de página -->
    <footer>
        <div class="container">
            <p>&copy; 2024 UPEMOR. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
