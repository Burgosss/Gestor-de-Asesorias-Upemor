<?php
session_start();
include '../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del profesor
$sqlProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = '$id_usuario'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);
$rowProfesor = mysqli_fetch_assoc($resultProfesor);
$id_profesor = $rowProfesor['id_profesor'];

if (mysqli_num_rows($resultProfesor) == 1) {
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajería Profesor</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
    <script>
        function toggleChatForm() {
            var chatBox = document.getElementById("chat-box");
            var newChatForm = document.getElementById("new-chat-form");
            chatBox.style.display = (chatBox.style.display === "none") ? "block" : "none";
            newChatForm.style.display = (newChatForm.style.display === "none") ? "block" : "none";
        }
    </script>
</head>
<body>

<header>
    <div class="container">
        <h1><img src="../../Static/img/logo.png" alt="Logo UPEMOR">Mensajería para Profesores</h1>
        <nav>
            <ul>
                <li><a href="../ProfesorIndex.php" class="active">Inicio</a></li>
                <li><a href="#">Citas</a></li>
                <li><a href="MensajeriaProfesor.php">Nuevo Mensaje</a></li>
                <li><a href="PerfilProfesor.php">Perfil</a></li>
                <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<main style="display: flex;">
    <!-- Columna Izquierda: Bandeja de Chats -->
    <div style="width: 30%; padding: 20px; border-right: 1px solid #ccc;">
        <h2>Chats</h2>
        <?php
        // Obtener chats activos del profesor
        $sqlChats = "SELECT DISTINCT id_alumno FROM mensaje WHERE id_profesor = '$id_profesor'";
        $resultChats = mysqli_query($conn, $sqlChats);
        ?>
        <?php if ($resultChats && mysqli_num_rows($resultChats) > 0): ?>
            <ul>
                <?php while ($chat = $resultChats->fetch_assoc()): 
                    $id_alumno = $chat['id_alumno'];
                    // Obtener el nombre del alumno
                    $sqlNombreAlumno = "SELECT nombre FROM usuario INNER JOIN alumno ON usuario.id_usuario = alumno.id_usuario WHERE alumno.id_alumno = '$id_alumno'";
                    $resultNombreAlumno = mysqli_query($conn, $sqlNombreAlumno);
                    $nombreAlumno = mysqli_fetch_assoc($resultNombreAlumno)['nombre'];
                ?>
                    <li><a href="../Vista/mensajeriaProfesor.php?id_alumno=<?php echo $id_alumno; ?>">Chat con Alumno <?php echo $nombreAlumno; ?></a></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No tienes chats activos con alumnos.</p>
        <?php endif; ?>
    </div>

    <!-- Columna Derecha: Área de Mensajes -->
    <div style="width: 70%; padding: 20px;">
        <?php if (isset($_GET['id_alumno'])): 
            $id_alumno = intval($_GET['id_alumno']);
            // Obtener el nombre del alumno
            $sqlNombreAlumno = "SELECT nombre FROM usuario INNER JOIN alumno ON usuario.id_usuario = alumno.id_usuario WHERE alumno.id_alumno = '$id_alumno'";
            $resultNombreAlumno = mysqli_query($conn, $sqlNombreAlumno);
            $nombreAlumno = mysqli_fetch_assoc($resultNombreAlumno)['nombre'];

            // Obtener mensajes entre profesor y alumno
            $sqlMensajes = "SELECT * FROM mensaje WHERE (id_profesor = '$id_profesor' AND id_alumno = '$id_alumno') ORDER BY fecha_hora";
            $mensajes = mysqli_query($conn, $sqlMensajes);
        ?>
            <h2>Chat con Alumno <?php echo $nombreAlumno; ?></h2>
            <div id="chat-box" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                <?php if ($mensajes && mysqli_num_rows($mensajes) > 0): ?>
                    <?php while ($mensaje = $mensajes->fetch_assoc()): ?>
                        <div style="margin-bottom: 10px;">
                            <strong><?php echo ($mensaje['remitente'] == $id_profesor) ? "Tú" : "Alumno {$nombreAlumno}"; ?>:</strong>
                            <p><?php echo htmlspecialchars($mensaje['contenido']); ?></p>
                            <small><?php echo $mensaje['fecha_hora']; ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay mensajes en este chat.</p>
                <?php endif; ?>
            </div>

             <!-- Botón para "Nuevo Chat" -->
             <button onclick="toggleChatForm()">Nuevo Mensaje</button>

            <!-- Formulario de envío de mensaje -->
            <div id="new-chat-form" style="display: none;">
                <form action="../Controlador/msgTeacherRep.php" method="POST" class="login-form">
                    <input type="hidden" name="destinatario" value="<?php echo $id_alumno; ?>">
                    <textarea name="contenido" placeholder="Escribe tu mensaje aquí..." required></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </div>

        <?php else: ?>
            <h2>Nuevo Mensaje</h2>
            <form action="../Controlador/msgTeacherRep.php" method="POST" class="login-form">
                <label for="destinatario">Matrícula del Alumno</label>
                <input type="text" name="destinatario" id="destinatario" required pattern="[A-Za-z0-9]+" title="Solo letras y números permitidos">
                <textarea name="contenido" placeholder="Escribe tu mensaje aquí..." required></textarea>
                <button type="submit">Enviar</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2024 UPEMOR. Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>

<?php } else {
    header("Location: ../../login/login.html");
} ?>
