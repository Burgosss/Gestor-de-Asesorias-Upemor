<?php
// Iniciar sesión y conectar a la base de datos
session_start();
include '../../Static/connect/db.php';


// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si se envió el ID del profesor o si ya está almacenado en sesión
if (!isset($_POST['id_profesor']) && !isset($_SESSION['id_profesor'])) {
    header("Location: gestionAsesoriasAlumno.php");
    exit();
}

// Guardar el ID del profesor en la sesión
if (isset($_POST['id_profesor'])) {
    $_SESSION['id_profesor'] = $_POST['id_profesor'];
}
$id_profesor = $_SESSION['id_profesor'];

if (isset($_POST['id_materia'])) {
    $_SESSION['id_materia'] = $_POST['id_materia'];
}
$id_materia = $_SESSION['id_materia'];

// Verificar si el profesor existe en la base de datos
$sqlProfesor = "SELECT * FROM profesor WHERE id_profesor = '$id_profesor'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);

if (!$resultProfesor || mysqli_num_rows($resultProfesor) == 0) {
    header("Location: gestionAsesoriasAlumno.php");
    exit();
}

// Manejar la solicitud de asesoría enviada por el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitar_asesoria'])) {
    $concepto = mysqli_real_escape_string($conn, $_POST['concepto']);
    $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
    $hora = mysqli_real_escape_string($conn, $_POST['hora']);
    $observaciones = mysqli_real_escape_string($conn, $_POST['observaciones']);

    // Verificar si el horario ya está ocupado
    $sqlCheck = "
        SELECT * 
        FROM asesoria 
        WHERE id_profesor = '$id_profesor' AND fecha = '$fecha' AND hora = '$hora' 
        AND (estado = 'Reservada' OR estado = 'Aprobada')
    ";
    $resultCheck = mysqli_query($conn, $sqlCheck);

    if (!$resultCheck || mysqli_num_rows($resultCheck) > 0) {
        $_SESSION['error'] = 'El horario seleccionado ya está reservado o aprobado. Por favor, elige otro horario.';
        header("Location: solicitarAsesoria.php");
        exit();
    }

    // Insertar la nueva solicitud de asesoría
    $sqlInsert = "
        INSERT INTO asesoria (concepto, fecha, hora, estado, observaciones, id_materia, id_alumno, id_profesor)
        VALUES ('$concepto', '$fecha', '$hora', 'Reservada', '$observaciones', '$id_materia', 
                (SELECT id_alumno FROM alumno WHERE id_usuario = '$id_usuario'), '$id_profesor')
    ";
    $resultInsert = mysqli_query($conn, $sqlInsert);

    if ($resultInsert) {
        $_SESSION['success'] = 'Asesoría solicitada con éxito. Espera la confirmación de tu profesor.';
        header("Location: ../alumnoIndex.php");
        exit();
    } else {
        $_SESSION['error'] = 'Error al solicitar la asesoría. Inténtalo de nuevo.';
        header("Location: solicitarAsesoria.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Asesoría</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.7/index.global.min.css" rel="stylesheet">
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
        <!-- Mostrar mensajes de error o éxito -->
        <?php
        if (isset($_SESSION['error'])) {
            echo "<script>alert('" . addslashes($_SESSION['error']) . "');</script>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo "<script>alert('" . addslashes($_SESSION['success']) . "');</script>";
            unset($_SESSION['success']);
        }
        ?>

        <!-- Regresar al índice del alumno -->
        <h2 style="margin-left: 5%;"><a href="../alumnoIndex.php">Regresar</a></h2>
        <h1 style="display: flex; justify-content: left;">Calendario de asesorías</h1>        

        <p>El calendario muestra las asesorías del profesor seleccionado con estado "Aprobadas" o "Reservadas".
            Solicita una asesoría rellenando el siguiente formulario. Recuerda que para asegurar tu asesoría
            el profesor debe de aceptarla.
        </p>

        <div class="Contenedor2Columnas">
            <!-- Calendario -->
            <div>
                <div id="calendar"></div>
                <div id="availability-info" style="margin-top: 20px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                    <p>Haz clic en una fecha para ver la disponibilidad.</p>
                </div>
            </div>
            
            <!-- Formulario para solicitar asesoría -->
            <div>
                <h2 style="display: flex; justify-content: center;">Solicitar Asesoría</h2>
                <form method="POST" class="login-form">
                    <label for="concepto">Concepto:</label>
                    <input type="text" id="concepto" name="concepto" required>
                    
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" required>
                    
                    <label for="hora">Hora:</label>
                    <select id="hora" name="hora" required>
                        <?php
                        for ($h = 7; $h <= 20; $h++) { // Comienza en 7 y termina en 20
                            $hora = str_pad($h, 2, '0', STR_PAD_LEFT);
                            echo "<option value='{$hora}:00'>{$hora}:00</option>";
                            if (!($h == 21)) { // Añade :30 solo si no es la última hora (20:30)
                                echo "<option value='{$hora}:30'>{$hora}:30</option>";
                            }
                        }
                        ?>
                    </select>


                    <label for="observaciones">Observaciones:</label>
                    <textarea id="observaciones" name="observaciones" rows="3" placeholder="Opcional"></textarea>
                    
                    <input type="hidden" name="id_profesor" value="<?php echo $id_profesor; ?>">
                    <button type="submit" name="solicitar_asesoria">Solicitar Asesoría</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Script para el calendario -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.7/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var infoContainer = document.getElementById('availability-info');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                events: '../Controlador/getCitas.php?id_profesor=<?php echo $id_profesor; ?>',
                eventColor: '#007bff',
                eventTextColor: '#fff',
                dateClick: function(info) {
                    infoContainer.innerHTML = '<p>Cargando disponibilidad...</p>';
                    fetch(`../Controlador/getDisponibilidad.php?id_profesor=<?php echo $id_profesor; ?>&dia=${info.dateStr}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                let html = '<h3>Horarios disponibles:</h3><ul>';
                                data.forEach(item => {
                                    html += `<li>${item.hora_inicio} - ${item.hora_fin}</li>`;
                                });
                                html += '</ul>';
                                infoContainer.innerHTML = html;
                            } else {
                                infoContainer.innerHTML = '<p>No hay horarios disponibles para este día.</p>';
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar la disponibilidad:', error);
                            infoContainer.innerHTML = '<p>Error al cargar la disponibilidad.</p>';
                        });
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
