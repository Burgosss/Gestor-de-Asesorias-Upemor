<?php
session_start();
include '../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del alumno
$sqlAlumno = "SELECT id_alumno FROM alumno WHERE id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);
$rowAlumno = mysqli_fetch_assoc($resultAlumno);
$id_alumno = $rowAlumno['id_alumno'];

if (mysqli_num_rows($resultAlumno) == 1) {
?> 

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajería Alumno</title>
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
        <h1><img src="../../Static/img/logo.png" alt="Logo UPEMOR">Mensajería para Alumnos</h1>
        <nav>
            <ul>
                <li><a href="../AlumnoIndex.php" class="active">Inicio</a></li>
                <li><a href="#">Citas</a></li>
                <li><a href="MensajeriaAlumno.php">Nuevo Mensaje</a></li>
                <li><a href="PerfilAlumno.php">Perfil</a></li>
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
        // Obtener chats activos del alumno
        $sqlChats = "SELECT DISTINCT id_profesor FROM mensaje WHERE id_alumno = '$id_alumno'";
        $resultChats = mysqli_query($conn, $sqlChats);
        ?>
        <?php if ($resultChats && mysqli_num_rows($resultChats) > 0): ?>
            <ul>
                <?php while ($chat = $resultChats->fetch_assoc()): 
                    $id_profesor = $chat['id_profesor'];
                    // Obtener el nombre del profesor
                    $sqlNombreProfesor = "SELECT nombre FROM usuario INNER JOIN profesor ON usuario.id_usuario = profesor.id_usuario WHERE profesor.id_profesor = '$id_profesor'";
                    $resultNombreProfesor = mysqli_query($conn, $sqlNombreProfesor);
                    $nombreProfesor = mysqli_fetch_assoc($resultNombreProfesor)['nombre'];
                ?>
                    <li><a href="?id_profesor=<?php echo $id_profesor; ?>">Chat con Profesor <?php echo $nombreProfesor; ?></a></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No tienes chats activos con profesores.</p>
        <?php endif; ?>
    </div>

    <!-- Columna Derecha: Área de Mensajes -->
    <div style="width: 70%; padding: 20px;">
        <?php if (isset($_GET['id_profesor'])): 
            $id_profesor = intval($_GET['id_profesor']);
            
            // Obtener id_usuario del profesor
            $execute = "SELECT id_usuario FROM profesor WHERE id_profesor = '$id_profesor'";
            $resultUsuarioP = mysqli_query($conn, $execute);
            $rowUsuarioP = mysqli_fetch_assoc($resultUsuarioP);
            $id_usuarioP = $rowUsuarioP['id_usuario'];

            // Obtener el nombre del profesor
            $exec = "SELECT nombre FROM usuario WHERE id_usuario = '$id_usuarioP'";
            $resultChatActivo = mysqli_query($conn, $exec);
            $chatActivo = mysqli_fetch_assoc($resultChatActivo)['nombre'];

            // Obtener mensajes entre alumno y profesor
            $sqlMensajes = "SELECT * FROM mensaje WHERE (id_alumno = '$id_alumno' AND id_profesor = '$id_profesor') OR (id_alumno = '$id_alumno' AND id_profesor = '$id_profesor') ORDER BY fecha_hora";
            $mensajes = mysqli_query($conn, $sqlMensajes);
        ?>
            <h2>Chat con Profesor <?php echo htmlspecialchars($chatActivo); ?></h2>
            <div id="chat-box" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                <?php if ($mensajes && mysqli_num_rows($mensajes) > 0): ?>
                    <?php while ($mensaje = $mensajes->fetch_assoc()): ?>
                        <div style="margin-bottom: 10px;">
                            <strong><?php echo ($mensaje['remitente'] == $id_alumno) ? "Tú" : "Profesor {$chatActivo}"; ?>:</strong>
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

            <!-- Nuevo mensaje dentro del chat seleccionado -->
            <div id="new-chat-form" style="display: none;">
                <form action="../Controlador/msgStudentRep.php" method="POST" class="login-form">
                    <input type="hidden" name="destinatario_id" value="<?php echo $id_profesor; ?>">
                    <textarea name="contenido" placeholder="Escribe tu mensaje aquí..." required></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </div>

        <?php else: ?>
            <h2>Nuevo Mensaje</h2>
            <form action="../Controlador/msgStudent.php" method="POST" class="login-form">
                <label for="destinatario">Matrícula del Profesor</label>
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
