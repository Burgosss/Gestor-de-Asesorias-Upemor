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
        <div class="container">
            <h2>Bienvenido, Alumno</h2>
            <p>Desde este panel, puedes consultar tus citas, agendar nuevas citas y enviar mensajes a tus profesores. También tienes la opción de editar tu información de perfil.</p>
        </div>

        <table border="1" style="width: 100%; text-align: left; width:80%">
                <thead>
                    <tr>
                        <th>Opciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Consultar Citas</td>
                        <td><a href="Vista/consultarAsesoria.php">Ir a Consultar Citas</a></td>
                    </tr>
                    <tr>
                        <td>Agendar Citas</td>
                        <td><a href="Vista/gestionAsesoriasAlumno.php">Ir a Agendar Citas</a></td>
                    </tr>
                    <tr>
                        <td>Enviar Mensajes</td>
                        <td><a href="Vista/MensajeriaAlumno.php">Ir a Enviar Mensajes</a></td>
                    </tr>
                    <tr>
                        <td>Editar Perfil</td>
                        <td><a href="Vista/PerfilAlumno.php">Ir a Editar Perfil</a></td>
                    </tr>
                    <tr>
                        <td>Reportes</td>
                        <td><a href="Vista/reportesAlumno.php">Ir a Editar Perfil</a></td>
                    </tr>
                </tbody>
            </table>

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