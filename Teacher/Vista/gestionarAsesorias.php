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

$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario actual

// Verificar que el usuario sea un profesor
$resultProfesor = obtenerIdProfesor($conn, $id_usuario);

if (mysqli_num_rows($resultProfesor) == 1) {
    $rowProfesor = mysqli_fetch_assoc($resultProfesor);
    $id_profesor = $rowProfesor['id_profesor'];
} else {
    // Si no es un profesor, redirigir al inicio de sesión
    header("Location: ../../../login/login.html");
    exit();
}

// Manejar la acción de aprobación o rechazo de asesorías
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_asesoria = $_POST['id_asesoria'];
    $accion = $_POST['accion']; // Puede ser "Aprobada" o "Rechazada"

    // Actualizar el estado de la asesoría si la acción es válida
    if ($accion === 'Aprobada' || $accion === 'Rechazada') {
        actualizarEstadoAsesoria($conn, $id_asesoria, $accion, $id_profesor);
    }
}

// Obtener las asesorías pendientes para el profesor
$resultAsesorias = obtenerAsesoriasPendientes($conn, $id_profesor);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Asesorías</title>
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
                    <li><a href="../ProfesorIndex.php">Inicio</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <!-- Enlace para regresar -->
        <div> 
            <h2><a href="../Vista/CitasProfesor.php">< Regresar</a></h2>
            <h1 style="display: flex; justify-content: center;">Aprobar o rechazar asesorías.</h1>
        </div>

        <!-- Indicaciones -->
        <div class="recuadro_indicaciones">
            <p>
                Desde esta sección podrás aprobar o rechazar las asesorías solicitadas por los alumnos. Haz clic en "Aprobar" o "Rechazar" para tomar una acción.
            </p>
        </div>
        <br>

        <!-- Tabla de asesorías pendientes -->
        <div>
            <h2>Asesorías Pendientes</h2>
            <table border="1" >
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Alumno</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody >
                    <?php while ($row = mysqli_fetch_assoc($resultAsesorias)) { ?>
                        <tr >
                            <td><?php echo $row['concepto']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo $row['hora']; ?></td>
                            <td><?php echo $row['alumno_nombre'] . ' ' . $row['alumno_apellido']; ?></td>
                            <td><?php echo $row['materia']; ?></td>
                            <td><?php echo $row['estado']; ?></td>
                            <td style="text-align: center; vertical-align: middle;">
                                <!-- Botones para aprobar o rechazar la asesoría -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_asesoria" value="<?php echo $row['id_asesoria']; ?>">
                                    <button type="submit" name="accion" value="Aprobada" class="boton_general">Aprobar</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_asesoria" value="<?php echo $row['id_asesoria']; ?>">
                                    <button type="submit" name="accion" value="Rechazada" class="boton_general">Rechazar</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Pie de página -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
            <p><a href="#">Política de privacidad</a> | <a href="#">Términos y condiciones</a></p>
        </div>
    </footer>
</body>
</html>
