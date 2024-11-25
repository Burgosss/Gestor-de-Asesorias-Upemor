<?php
session_start();
include '../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];  


$sqlProfesor = "SELECT * FROM profesor WHERE id_usuario = '$id_usuario'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);

if (mysqli_num_rows($resultProfesor) == 1) {
?>  

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Profesor</title>
    <link rel="stylesheet" href="../Static/css/styles.css"> 
</head>
<body>
    <header>
        <div class="container">
            <h1><img src="../Static/img/logo.png" alt="Logo UPEMOR"> Panel del Profesor</h1>
            <nav>
                <ul>
                    <li><a href="#" class="active">Inicio</a></li>
                    <li><a href="Vista/CitasProfesor.php" >Gestion de Asesorias</a></li>
                    <li><a href="Vista/MensajeriaProfesor.php">Enviar Mensajes</a></li>
                    <li><a href="Vista/PerfilProfesor.php">Perfil</a></li>
                    <li><a href="../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Bienvenido, Profesor</h2>
            <p>Desde este panel, puedes visualizar las solicitudes de citas de tus alumnos, enviar mensajes y editar tu información de perfil.</p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías Académicas. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>

<?php 
    }else{
        header("Location: ../login/login.html");
    }
?>