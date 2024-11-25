<?php  // Conexión a la base de datos
session_start();
include '../../../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];   // Verificar inicio de sesión


$sqlAdmin = "SELECT * FROM admin WHERE id_usuario = '$id_usuario'";
$resultAdmin = mysqli_query($conn, $sqlAdmin);

if (mysqli_num_rows($resultAdmin) == 1) { 
?>  

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Asesorías</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <link rel="stylesheet" href="../../../Static/css/header.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.7/index.global.min.css" rel="stylesheet">
</head>
<body>

    <header>
        <div class="container"> 
            <h1>
                <img src="../../../Static/img/logo.png" alt="Logo UPEMOR">Upemor - Sistema de Gestión de Asesorías
            </h1>
            <nav>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="login/login.html">Iniciar sesión</a></li>
                    <li><a href="#">Asesorías</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="contenedor-principal">
        <!-- Contenedor para el calendario -->
        <div id="calendar">
        </div>
    
        <!-- Formulario para registrar una asesoría -->
        <section class="formulario-asesoria">
            <h2>Registrar Asesoría</h2>
            <form form method="post" name="frmCita" id="frmCita" action="../Controlador/CrearCita.php">
                
                <label for="concepto" >Concepto:</label>
                <input type="text" id="concepto" name="concepto" required maxlength="45">
                <br> <br>
    
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
                <br> <br>
    
                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" required>
                <br> <br>
    
                <label for="observaciones">Observaciones:</label>
                <input type="text" id="observaciones" name="observaciones" required maxlength="100">
                <br> <br>
    
                <label for="id_materia">Materia (ID):</label>
                <select id="id_materia" name="id_materia" required>
                    <option value="">Selecciona una materia</option>
                    <?php
                    // Consulta para obtener las materias
                    $sqlMaterias = "SELECT id_materia, nombre FROM materia";
                    $resultMaterias = mysqli_query($conn, $sqlMaterias);

                    // Verificar si se obtuvieron resultados
                    if ($resultMaterias && mysqli_num_rows($resultMaterias) > 0) {
                    while ($materia = mysqli_fetch_assoc($resultMaterias)) {
                        echo '<option value="' . $materia['id_materia'] . '">' . $materia['nombre'] . '</option>';
                    }
                    } else {
                        echo '<option value="">No hay materias disponibles</option>';
                    }
                    ?>
                </select>
                <br> <br>
    
                <label for="id_alumno">Alumno (ID):</label>
                <select id="id_alumno" name="id_alumno" required>
                    <option value="">Selecciona un alumno</option>
                    <?php
                    $sqlAlumnos = "SELECT alumno.id_alumno, usuario.nombre, usuario.apellido 
                                FROM alumno 
                                JOIN usuario ON alumno.id_usuario = usuario.id_usuario";
                    $resultAlumnos = mysqli_query($conn, $sqlAlumnos);
                    if ($resultAlumnos && mysqli_num_rows($resultAlumnos) > 0) {
                        while ($alumno = mysqli_fetch_assoc($resultAlumnos)) {
                            echo '<option value="' . $alumno['id_alumno'] . '">' . $alumno['nombre'] . ' ' . $alumno['apellido'] . '</option>';
                        }
                    } else {
                        echo '<option value="">No hay alumnos disponibles</option>';
                    }
                    ?>
                </select>
                <br> <br>
    
                <label for="id_profesor">Profesor (ID):</label>
                <select id="id_profesor" name="id_profesor" required>
                    <option value="">Selecciona un profesor</option>
                    <?php
                    $sqlProfesores = "SELECT profesor.id_profesor, usuario.nombre, usuario.apellido 
                                    FROM profesor 
                                    JOIN usuario ON profesor.id_usuario = usuario.id_usuario";
                    $resultProfesores = mysqli_query($conn, $sqlProfesores);
                    if ($resultProfesores && mysqli_num_rows($resultProfesores) > 0) {
                        while ($profesor = mysqli_fetch_assoc($resultProfesores)) {
                            echo '<option value="' . $profesor['id_profesor'] . '">' . $profesor['nombre'] . ' ' . $profesor['apellido'] . '</option>';
                        }
                    } else {
                        echo '<option value="">No hay profesores disponibles</option>';
                    }
                    ?>
                </select>
                <br> <br>
    
                <button type="submit">Registrar Asesoría</button>
            </form>
        </section>
    </div>

    <!-- FullCalendar JavaScript -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.7/index.global.min.js"></script>
    <script>
        // Código de configuración para el calendario
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es', // Para mostrar el calendario en español
                events: '../Controlador/getCitas.php', // Llamada al archivo PHP que devuelve las asesorías
                eventColor: '#007bff', // Color de los eventos
                eventTextColor: '#fff' // Color del texto de los eventos
            });
            calendar.render();
        });
    </script>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
            <p><a href="#">Política de privacidad</a> | <a href="#">Términos y condiciones</a></p>
        </div>
    </footer>
</body>
</html>

<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>
