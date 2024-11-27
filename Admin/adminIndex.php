<?php
session_start();
include '../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];  


$sqlAdmin = "SELECT * FROM admin WHERE id_usuario = '$id_usuario'";
$resultAdmin = mysqli_query($conn, $sqlAdmin);

if (mysqli_num_rows($resultAdmin) == 1) {
?>  
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../Static/css/styles.css">
</head>

<body>
    <header>
        <div class="container">
            <h1><img src="../Static/img/logo.png" alt="Logo UPEMOR">Panel de Administrador</h1>
            <nav>
                <ul>
                    <li><a href=# class="active">Inicio</a></li>
                    <li><a href="BD/Vista/Respaldo.php">Respaldo</a></li>
                    <li><a href="BD/Vista/Restauracion.php">Restauración</a></li>
                    <li><a href="Usuario/Vista/PerfilAdmin.php">Perfil</a></li>
                    <li><a href="../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-options">
            <h2>Opciones de Administración</h2>
            <ul>
                <li><a href="Registro/Registro.php">Registro</a></li>
                <li><a href="Usuario/Usuario.php">Usuarios y Materias</a></li>
                <li><a href="Registro/Vista/archivos.php">Gestion de Archivos</a></li>
                <li><a href="Registro/Vista/reportesAdmin.php">Generar reportes.</a></li>
            </ul>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 UPEMOR. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
<?php 
    } else {
        header("Location: ../login/login.html");
    }
?>
