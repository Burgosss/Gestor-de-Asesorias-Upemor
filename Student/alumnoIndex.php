<?php
session_start();
include '../Static/connect/db.php';

if (isset($_SESSION['success'])) {
    echo "<script>alert('{$_SESSION['success']}');</script>";
    unset($_SESSION['success']); // Elimina la alerta para que no se repita.
}

if (isset($_SESSION['error'])) {
    echo "<script>alert('{$_SESSION['error']}');</script>";
    unset($_SESSION['error']); // Elimina la alerta para que no se repita.
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];  


$sqlAlumno = "SELECT * FROM alumno WHERE id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);

if (mysqli_num_rows($resultAlumno) == 1) {
?> 


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Alumno</title>
    <link rel="stylesheet" href="../Static/css/styles.css"> 
</head>

<style>
    main {
        background-image: url('../Static/img/background_student.webp'); /* Ruta de tu imagen */
        background-size: cover; /* Hace que la imagen cubra todo el fondo */
    }
</style>

<body>
    <header>
        <div class="container">
            <h1>Panel del Alumno</h1>
            <nav>
                <ul>
                    <li><a href="../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="recuadro_indicaciones">
            <h2>Bienvenido, Alumno</h2>
            <p>Desde este panel, puedes consultar tus citas, agendar nuevas citas y enviar mensajes a tus profesores. También tienes la opción de editar tu información de perfil.</p>
        </div>

        <div class="admin-options">
            <h2>Opciones de Administración</h2>
            <ul>
                <li><a href="Vista/consultarAsesoria.php">Consultar Citas</a></li>
                <li><a href="Vista/gestionAsesoriasAlumno.php">Agendar Citas</a></li>
                <li><a href="Vista/MensajeriaAlumno.php">Enviar Mensajes</a></li>
                <li><a href="Vista/PerfilAlumno.php">Editar Perfil</a></li>
                <li><a href="Vista/reportesAlumno.php">Generar Reportes</a></li>
            </ul>
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