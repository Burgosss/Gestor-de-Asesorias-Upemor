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

// Verificar que el usuario sea un alumno
$sqlAlumno = "SELECT * FROM alumno WHERE id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);

if (mysqli_num_rows($resultAlumno) == 1) {
    // Obtener la lista de profesores que tienen al menos una materia asignada
    $sqlProfesores = "
        SELECT DISTINCT p.id_profesor, u.nombre, u.apellido 
        FROM profesor p
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN profesor_materia pm ON p.id_profesor = pm.id_profesor
    ";
    $resultProfesores = mysqli_query($conn, $sqlProfesores);

    // Manejar la selección de profesor y mostrar sus materias
    $materias = [];
    if (isset($_POST['id_profesor'])) {
        $id_profesor = $_POST['id_profesor'];
        $sqlMaterias = "
            SELECT m.id_materia, m.nombre 
            FROM profesor_materia pm
            JOIN materia m ON pm.id_materia = m.id_materia
            WHERE pm.id_profesor = '$id_profesor'
        ";
        $resultMaterias = mysqli_query($conn, $sqlMaterias);

        // Almacenar las materias en un arreglo
        while ($row = mysqli_fetch_assoc($resultMaterias)) {
            $materias[] = $row;
        }
    }
} else {
    // Si no es un alumno, redirigir al inicio de sesión
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
        <!-- Enlace para regresar -->
        <h2 style="margin-left: 5%;"><a href="../alumnoIndex.php">Regresar</a></h2>
        <h1 style="display: flex; justify-content: center;">Gestionar disponibilidad de horario de asesorías</h1>

        <!-- Descripción -->
        <p>
            Bienvenido/a al menú de agendar citas. Antes de empezar, asegúrate de seleccionar un profesor. 
            Recuerda que tu profesor debe impartir al menos una asignatura para solicitar una asesoría. 
            En caso de que tu profesor no aparezca, comunícate con tu profesor.
        </p>

        <!-- Selección de profesor -->
        <h2>Seleccione un Profesor</h2>
        <form method="POST" class="login-form">
            <label for="id_profesor">Profesor:</label>
            <select name="id_profesor" id="id_profesor" onchange="this.form.submit()">
                <option value="" disabled selected>Seleccione un profesor</option>
                <?php while ($row = mysqli_fetch_assoc($resultProfesores)) { ?>
                    <option value="<?php echo $row['id_profesor']; ?>" <?php echo isset($id_profesor) && $id_profesor == $row['id_profesor'] ? 'selected' : ''; ?>>
                        <?php echo $row['nombre'] . ' ' . $row['apellido']; ?>
                    </option>
                <?php } ?>
            </select>
        </form>

        <!-- Mostrar materias del profesor seleccionado -->
        <?php if (!empty($materias)) { ?>
            <h2>Materias del Profesor</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materias as $materia) { ?>
                        <tr>
                            <td><?php echo $materia['nombre']; ?></td>
                            <td>
                                <form method="POST" action="solicitarAsesoria.php">
                                    <input type="hidden" name="id_materia" value="<?php echo $materia['id_materia']; ?>">
                                    <input type="hidden" name="id_profesor" value="<?php echo $id_profesor; ?>">
                                    <button type="submit">Solicitar Asesoría</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </main>

    <!-- Pie de página -->
    <footer>
        <div class="container">
            <p>&copy; 2024 UPEMOR. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
