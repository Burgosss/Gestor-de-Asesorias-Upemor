<?php
session_start();
include '../../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
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
    <link rel="stylesheet" href="../../Static/css/styles.css">
</head>

<body>
    <header>
        <div class="container">
            <h1><img src="../../Static/img/logo.png" alt="Logo UPEMOR">Panel de Administrador</h1>
            <nav>
                <ul>
                    <li><a href="../adminIndex.php" class="active">Inicio</a></li>
                    <li><a href="#">Citas</a></li>
                    <li><a href="Vista/PerfilAdmin.php">Perfil</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-options">
            <h2>Usuarios y Materias</h2>
            <ul>
                <li><a href="Vista/LeerAlumnos.php">Ver Alumnos</a></li>
                <li><a href="Vista/LeerProfesores.php">Ver Profesores</a></li>
                <li><a href="Vista/LeerMaterias.php">Ver Materias</a></li>
                <li><a href="Vista/EstadoAlumnos.php">Estado Alumnos</a></li>
                <li><a href="Vista/EstadoProfesores.php">Estado Profesor</a></li>
                <li><a href="Vista/EstadoMaterias.php">Estado Materias</a></li>

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
    }else{
        header("Location: ../../login/login.html");
    }
?>
