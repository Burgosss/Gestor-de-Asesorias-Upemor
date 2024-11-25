<?php
// Inicia la sesión
session_start();
include '../../Static/connect/db.php';
include '../Controlador/getEstado.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario

// Verificar que el usuario sea un profesor
$resultProfesor = obtenerIdProfesor($conn, $id_usuario);

if (mysqli_num_rows($resultProfesor) == 1) {
    $rowProfesor = mysqli_fetch_assoc($resultProfesor);
    $id_profesor = $rowProfesor['id_profesor']; // Obtener el ID del profesor
} else {
    // Redirigir si no es un profesor
    header("Location: ../../login/login.html");
    exit();
}

// Manejar la acción de agregar observaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se están agregando observaciones
    if (isset($_POST['observaciones'])) {
        $id_asesoria = $_POST['id_asesoria'];
        $observaciones = mysqli_real_escape_string($conn, $_POST['observaciones']); // Escapar los datos ingresados

        // Actualizar observaciones y cambiar el estado a "Finalizada"
        $sqlObservaciones = "
            UPDATE asesoria 
            SET observaciones = '$observaciones', estado = 'Finalizada' 
            WHERE id_asesoria = '$id_asesoria' AND id_profesor = '$id_profesor'
        ";
        mysqli_query($conn, $sqlObservaciones);
    }

    // Si se está eliminando una asesoría
    if (isset($_POST['eliminar'])) {
        $id_asesoria = $_POST['id_asesoria'];
        $sqlEliminar = "DELETE FROM asesoria WHERE id_asesoria = '$id_asesoria' AND id_profesor = '$id_profesor'";
        mysqli_query($conn, $sqlEliminar);
    }
}

// Obtener las asesorías aprobadas del profesor
$sqlAsesoriasAprobadas = "
    SELECT 
        a.id_asesoria, a.concepto, a.fecha, a.hora, a.estado, a.observaciones,
        u.nombre AS alumno_nombre, u.apellido AS alumno_apellido,
        m.nombre AS materia
    FROM asesoria a
    JOIN alumno al ON a.id_alumno = al.id_alumno
    JOIN usuario u ON al.id_usuario = u.id_usuario
    JOIN materia m ON a.id_materia = m.id_materia
    WHERE a.id_profesor = '$id_profesor' AND a.estado = 'Aprobada'
";
$resultAsesoriasAprobadas = mysqli_query($conn, $sqlAsesoriasAprobadas);

// Obtener las asesorías finalizadas del profesor
$sqlAsesoriasFinalizadas = "
    SELECT 
        a.id_asesoria, a.concepto, a.fecha, a.hora, a.estado, a.observaciones,
        u.nombre AS alumno_nombre, u.apellido AS alumno_apellido,
        m.nombre AS materia
    FROM asesoria a
    JOIN alumno al ON a.id_alumno = al.id_alumno
    JOIN usuario u ON al.id_usuario = u.id_usuario
    JOIN materia m ON a.id_materia = m.id_materia
    WHERE a.id_profesor = '$id_profesor' AND a.estado = 'Finalizada'
";
$resultAsesoriasFinalizadas = mysqli_query($conn, $sqlAsesoriasFinalizadas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Observaciones y Gestionar Finalizadas</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
    <script>
        // Función para confirmar la eliminación
        function confirmarEliminacion(idAsesoria) {
            const confirmacion = confirm("¿Estás seguro de que deseas eliminar esta asesoría?");
            if (confirmacion) {
                // Redirige con el ID de la asesoría para eliminarla
                window.location.href = `agregarObservaciones.php?eliminar=${idAsesoria}`;
            }
        }
    </script>
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
                    <li><a href="../ProfesorIndex.php">Inicio</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <!-- Botón para regresar -->
        <div>
            <h2><a href="../Vista/CitasProfesor.php">< Regresar</a></h2>
            <h1 style="display: flex; justify-content: center;">Agregar observaciones y finalizar asesorías</h1>
        </div>

        <!-- Tabla de asesorías aprobadas -->
        <div>
            <h2>Asesorías Aprobadas</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Alumno</th>
                        <th>Materia</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultAsesoriasAprobadas)) { ?>
                        <tr>
                            <td><?php echo $row['concepto']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo $row['hora']; ?></td>
                            <td><?php echo $row['alumno_nombre'] . ' ' . $row['alumno_apellido']; ?></td>
                            <td><?php echo $row['materia']; ?></td>
                            <td><?php echo $row['observaciones']; ?></td>
                            <td style="text-align: center; vertical-align: middle;">
                                <a href="notasReunion.php?id_asesoria=<?php echo $row['id_asesoria']; ?>" class="boton_general">Agregar notas de reunión</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Tabla de asesorías finalizadas -->
        <div>
            <h2>Asesorías Finalizadas</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Alumno</th>
                        <th>Materia</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultAsesoriasFinalizadas)) { ?>
                        <tr>
                            <td><?php echo $row['concepto']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo $row['hora']; ?></td>
                            <td><?php echo $row['alumno_nombre'] . ' ' . $row['alumno_apellido']; ?></td>
                            <td><?php echo $row['materia']; ?></td>
                            <td><?php echo $row['observaciones']; ?></td>
                            <td style="text-align: center; vertical-align: middle;">
                                <a href="notasReunion.php?id_asesoria=<?php echo $row['id_asesoria']; ?>" class="boton_general">Notas de reunión</a>
                                <br><br>
                                <button onclick="confirmarEliminacion(<?php echo $row['id_asesoria']; ?>)" class="boton_general">Eliminar</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>


