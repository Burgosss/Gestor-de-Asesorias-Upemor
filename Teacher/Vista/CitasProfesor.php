<?php
// Inicia la sesión
session_start();
include '../../Static/connect/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario actual

// Verificar si el usuario es un profesor
$sqlAdmin = "SELECT * FROM profesor WHERE id_usuario = '$id_usuario'";
$resultAdmin = mysqli_query($conn, $sqlAdmin);

if (mysqli_num_rows($resultAdmin) == 1) { 
    $rowProfesor = mysqli_fetch_assoc($resultAdmin);
    $id_profesor = $rowProfesor['id_profesor']; // Obtener el ID del profesor
?>  

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistema de Gestión de Asesorías</title>
        <!-- Estilos -->
        <link rel="stylesheet" href="../../Static/css/styles.css">
        <link rel="stylesheet" href="../../Static/css/header.css">
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
                        <li><a href="../ProfesorIndex.php">Inicio</a></li>
                        <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                        <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Contenido principal -->
        <div>
            <h2 style="margin-left: 5%;"><a href="../ProfesorIndex.php">< Regresar</a></h2>
            <h1 style="text-align: center;">Gestión de asesorías.</h1>
        </div>
         
        <div class="Contenedor2ColumnasV3">
            <!-- Columna del calendario -->
            <div style="margin-left: 5%; margin-right:5%; margin-bottom:5%;">
                <p style="margin-left: 5%"> 
                    A continuación se muestran las asesorías reservadas, consulta la opción 
                    "Aceptar o rechazar asesorías" para gestionarlas.
                </p> 
                <div id="calendar"></div>
            </div>   
            
            <!-- Columna de opciones -->
            <div style="margin-right:10%; margin-bottom:5%;">
                <h2 style="text-align: center;">Selecciona una opción.</h2>
                <table border="1">
                    <th>Opciones</th>
                    <tr>
                        <td><a href="../Vista/gestionarDisponibilidad.php">Gestionar disponibilidad de horarios.</a></td>
                    </tr>
                    <tr>
                        <td><a href="../Vista/gestionarAsesorias.php">Aprobar o rechazar asesorías.</a></td>
                    </tr>
                    <tr>
                        <td><a href="../Vista/agregarObservaciones.php">Agregar observaciones y finalizar asesorias.</a></td>
                    </tr>
                    <tr>
                        <td><a href="../Vista/compartirMaterial.php">Compartir archivos con el alumno.</a></td>
                    </tr>
                    <tr>
                        <td><a href="../Vista/formato.php">Formato de control de asesorías.</a></td>
                    </tr>
                    <tr>
                        <td><a href="../Vista/reportes.php">Reportes.</a></td>
                    </tr>

                </table>
                <div id="cuadro" style="display: none;"></div>
            </div>
        </div>

        <!-- FullCalendar JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.7/index.global.min.js"></script>
        <script>
            // Configuración del calendario
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth', // Vista inicial del calendario
                    locale: 'es', // Idioma en español
                    headerToolbar: {
                        left: 'prev,next today', // Botones de navegación
                        center: 'title', // Título del calendario
                        right: 'dayGridMonth,timeGridWeek,timeGridDay' // Botones para cambiar entre vistas
                    },
                    events: '../Controlador/getCitas.php?id_profesor=<?php echo $id_profesor; ?>',
                    eventColor: '#007bff', // Color de los eventos
                    eventTextColor: '#fff', // Color del texto de los eventos
                    eventClick: function(info) { // Configuración al hacer clic en un evento
                        const cuadro = document.getElementById('cuadro');
                        cuadro.innerHTML = `
                            <p><strong>Concepto:</strong> ${info.event.title}</p>
                            <p><strong>Fecha:</strong> ${info.event.start.toLocaleDateString('es-ES')}</p>
                            <p><strong>Hora:</strong> ${info.event.start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}</p>
                            <button onclick="document.getElementById('cuadro').style.display='none'">Cerrar</button>
                        `;
                        cuadro.style.display = "flex";
                    }
                });
                calendar.render(); // Renderizar el calendario
            });
        </script>

    

        <!-- Pie de página -->
        <footer>
            <div class="container">
                <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
                <p><a href="#">Política de privacidad</a> | <a href="#">Términos y condiciones</a></p>
            </div>
        </footer>
    </body>
</html>

<?php 
} else {
    // Redirigir al inicio de sesión si el usuario no es profesor
    header("Location: ../../../login/login.html");
}
?>
