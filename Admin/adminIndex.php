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

<style>
    main {
        background-image: url('../Static/img/background_admin.webp'); /* Ruta de tu imagen */
        background-size: cover; /* Hace que la imagen cubra todo el fondo */
    }
</style>


<body>
    <header>
        <div class="container">
            <h1><img src="../Static/img/logo.png" alt="Logo UPEMOR">Panel de Administrador</h1>
            <nav>
                <ul>
                    <li><a href=# class="active">Inicio</a></li>
                    <li><a href="../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="recuadro_indicaciones">
            <h2>Bienvenido, Administrador</h2>
            <p>Desde este panel, puedes consultar las diferente opciones que tienes, haz
                clic en una para comenzar.</p>
        </div>

        <div class="Contenedor2Columnas">
            <div> 
                <div class="admin-options">
                    <h2>Opciones de Usuarios</h2>
                    <ul>
                        <li><a href="Usuario/Vista/PerfilAdmin.php">Editar perfil</a></li>
                        <li><a href="Registro/Registro.php">Registrar Usuarios y Materias</a></li>
                        <li><a href="Usuario/Usuario.php">Consultas, Eliminaciones y Actualizaciones</a></li>
                    </ul>
                </div>
            </div>
            <div> 
                <div class="admin-options">
                    <h2>Opciones de Administración</h2>
                    <ul>
                        <li><a href="Registro/Vista/archivos.php">Gestion de Archivos</a></li>
                        <li><a href="Registro/Vista/reportesAdmin.php">Generar reportes.</a></li>
                        <li><a href="BD/Vista/Respaldo.php">Respaldar la base de datos</a></li>
                        <li><a href="BD/Vista/Restauracion.php">Restaurar la base de datos</a></li>
                     </ul>
                </div>
            </div>

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
