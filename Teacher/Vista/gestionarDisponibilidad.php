<?php
session_start();
include '../../Static/connect/db.php';
include '../Controlador/gestDispControl.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario

// Obtener el ID del profesor asociado al usuario
$id_profesor = obtenerIdProfesor($id_usuario);

// Si se ha enviado el formulario para agregar disponibilidad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dia'], $_POST['hora_inicio'], $_POST['hora_fin'])) {
    $dia = $_POST['dia'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    agregarOActualizarDisponibilidad($id_profesor, $dia, $hora_inicio, $hora_fin); // Llamada al controlador para agregar disponibilidad
}

// Si se ha solicitado eliminar una disponibilidad
if (isset($_GET['eliminar'])) {
    $id_disponibilidad = $_GET['eliminar'];
    eliminarDisponibilidad($id_disponibilidad); // Llamada al controlador para eliminar la disponibilidad
}

// Obtener las disponibilidades actuales del profesor
$resultDisponibilidad = obtenerDisponibilidades($id_profesor);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Disponibilidad</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
</head>
<body>
    <!-- Encabezado -->
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
            <!-- Botón para regresar -->
            <h2 style="margin-left: 5%;"><a href="../Vista/CitasProfesor.php">< Regresar</a></h2>
            <h1 style="display: flex; justify-content: center;">Gestionar disponibilidad de horario de asesorías.</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p>
                Desde esta sección podrás administrar la disponibilidad de horarios según el día de la semana.
                Selecciona un día, así como el periodo de tiempo en el cual te encuentras disponible y haz clic en
                "Agregar Disponibilidad" para agregar o actualizar los horarios. Vuelve siempre que lo desees para cambiar los datos.
            </p>
        </div>

        <div>
            <!-- Contenedor de dos columnas -->
            <div class="Contenedor2ColumnasV2">
                <!-- Columna: Formulario para agregar disponibilidad -->
                <div>
                    <form method="post" class="login-form">
                        <label for="dia">Día:</label>
                        <select name="dia" id="dia" required>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                        </select>
                        <br><br>

                        <label for="hora_inicio">Hora de Inicio:</label>
                        <select name="hora_inicio" id="hora_inicio" required>
                            <?php
                            // Generar horarios en intervalos de 30 minutos
                            for ($h = 0; $h < 24; $h++) {
                                $hora = str_pad($h, 2, '0', STR_PAD_LEFT); // Formato de dos dígitos
                                echo "<option value='{$hora}:00'>{$hora}:00</option>";
                                echo "<option value='{$hora}:30'>{$hora}:30</option>";
                            }
                            ?>
                        </select>
                        <br><br>

                        <label for="hora_fin">Hora de Fin:</label>
                        <select name="hora_fin" id="hora_fin" required>
                            <?php
                            // Generar horarios en intervalos de 30 minutos
                            for ($h = 0; $h < 24; $h++) {
                                $hora = str_pad($h, 2, '0', STR_PAD_LEFT); // Formato de dos dígitos
                                echo "<option value='{$hora}:00'>{$hora}:00</option>";
                                echo "<option value='{$hora}:30'>{$hora}:30</option>";
                            }
                            ?>
                        </select>
                        <br><br>

                        <button type="submit">Agregar Disponibilidad</button>
                    </form>
                </div>

                <!-- Columna: Mostrar disponibilidad actual -->
                <div>
                    <h3 style="display: flex; justify-content: center;">Disponibilidad Actual</h3>
                    <table border="1">
                        <tr>
                            <th>Día</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Acciones</th>
                        </tr>
                        <?php while ($disponibilidad = mysqli_fetch_assoc($resultDisponibilidad)) { ?>
                            <tr>
                                <td><?php echo $disponibilidad['dia']; ?></td>
                                <td><?php echo $disponibilidad['hora_inicio']; ?></td>
                                <td><?php echo $disponibilidad['hora_fin']; ?></td>
                                <td>
                                    <button  onclick="confirmarEliminacion(<?php echo $disponibilidad['iddisponibilidad']; ?>)">
                                        Eliminar
                                    </button>
                                </td>

                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        function confirmarEliminacion(idDisponibilidad) {
            const confirmacion = confirm("¿Estás seguro de que deseas eliminar esta disponibilidad?");
            if (confirmacion) {
                window.location.href = `gestionarDisponibilidad.php?eliminar=${idDisponibilidad}`;
            }
        }
    </script>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
            <p><a href="#">Política de privacidad</a> | <a href="#">Términos y condiciones</a></p>
        </div>
    </footer>
</body>
</html>
