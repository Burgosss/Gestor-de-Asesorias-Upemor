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

    // Manejar la eliminación de asesorías
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
        $id_asesoria = $_POST['id_asesoria'];

        // Obtener los materiales relacionados a la asesoría
        $sqlObtenerMateriales = "SELECT ruta_archivo FROM material WHERE id_asesoria = '$id_asesoria'";
        $resultMateriales = mysqli_query($conn, $sqlObtenerMateriales);

        // Eliminar cada archivo físico
        while ($row = mysqli_fetch_assoc($resultMateriales)) {
            $rutaArchivo = $row['ruta_archivo'];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo); // Eliminar el archivo del servidor
            }
        }

        // Eliminar los materiales relacionados de la base de datos
        $sqlEliminarMateriales = "DELETE FROM material WHERE id_asesoria = '$id_asesoria'";
        mysqli_query($conn, $sqlEliminarMateriales);

        // Eliminar la asesoría
        $sqlEliminarAsesoria = "DELETE FROM asesoria WHERE id_asesoria = '$id_asesoria' AND id_alumno = '$id_alumno'";
        mysqli_query($conn, $sqlEliminarAsesoria);
    }

    // Obtener las asesorías con estado "Reservada"
    $sqlAsesoriasReservadas = "
        SELECT 
            a.id_asesoria, a.concepto, a.fecha, a.hora, a.estado,
            CONCAT(u.nombre, ' ', u.apellido) AS profesor_nombre,
            m.nombre AS asignatura
        FROM asesoria a
        JOIN profesor p ON a.id_profesor = p.id_profesor
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN materia m ON a.id_materia = m.id_materia
        WHERE a.id_alumno = '$id_alumno' AND a.estado = 'Reservada'
        ORDER BY a.fecha DESC, a.hora DESC
    ";
    $resultAsesoriasReservadas = mysqli_query($conn, $sqlAsesoriasReservadas);

    // Obtener las asesorías con otros estados
    $sqlAsesoriasOtrosEstados = "
        SELECT 
            a.id_asesoria, a.concepto, a.fecha, a.hora, a.estado,
            CONCAT(u.nombre, ' ', u.apellido) AS profesor_nombre,
            m.nombre AS asignatura
        FROM asesoria a
        JOIN profesor p ON a.id_profesor = p.id_profesor
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN materia m ON a.id_materia = m.id_materia
        WHERE a.id_alumno = '$id_alumno' AND a.estado != 'Reservada'
        ORDER BY a.fecha DESC, a.hora DESC
    ";
    $resultAsesoriasOtrosEstados = mysqli_query($conn, $sqlAsesoriasOtrosEstados);
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
    <title>Gestión de Asesorías - Alumno</title>
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
            <h2 style="margin-left: 5%;"><a href="../alumnoIndex.php">Regresar</a></h2>
            <h1 style="text-align: center;">Consulta de asesorías</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p>
                Aquí puedes consultar tus asesorías registradas, eliminarlas o acceder al material didáctico asociado a cada una.
            </p>
        </div>

        <!-- Tabla de asesorías con estado "Reservada" -->
        <h2 style="text-align: center;">Asesorías Reservadas</h2>
        <table border="1" style="margin: 5% auto; width: 90%; text-align: center;">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Profesor</th>
                    <th>Asignatura</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Material Didáctico</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultAsesoriasReservadas)) { ?>
                    <tr>
                        <td><?php echo $row['concepto']; ?></td>
                        <td><?php echo $row['profesor_nombre']; ?></td>
                        <td><?php echo $row['asignatura']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['hora']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                        <td>
                            <a href="verMaterialDidactico.php?id_asesoria=<?php echo $row['id_asesoria']; ?>">Ver Material</a>
                        </td>
                        <td>
                            <button onclick="confirmarEliminacion(<?php echo $row['id_asesoria']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tabla de asesorías con otros estados -->
        <h2 style="text-align: center;">Otras Asesorías</h2>
        <table border="1" style="margin: 5% auto; width: 90%; text-align: center;">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Profesor</th>
                    <th>Asignatura</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Material Didáctico</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultAsesoriasOtrosEstados)) { ?>
                    <tr>
                        <td><?php echo $row['concepto']; ?></td>
                        <td><?php echo $row['profesor_nombre']; ?></td>
                        <td><?php echo $row['asignatura']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['hora']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                        <td>
                            <a href="verMaterialDidactico.php?id_asesoria=<?php echo $row['id_asesoria']; ?>">Ver Material</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>

    <script>
        function confirmarEliminacion(idAsesoria) {
            const confirmacion = confirm("¿Estás seguro de que deseas eliminar esta asesoría?");
            if (confirmacion) {
                window.location.href = `consultarAsesoria.php?eliminar=${idAsesoria}`;
            }
        }
    </script>

    <footer>
        <div class="container">
            <p>&copy; 2024 UPEMOR. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
