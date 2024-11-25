<?php
session_start();
include '../../../Static/connect/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
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
    <title>Restauración de Base de Datos</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
</head>

<body>
    <header>
        <div class="container">
            <h1><img src="../../../Static/img/logo.png" alt="Logo UPEMOR">Restauración de Base de Datos</h1>
            <nav>
                <ul>
                    <li><a href="../../adminIndex.php">Inicio</a></li>
                    <li><a href="Respaldo.php">Respaldo</a></li>
                    <li><a href="Restauracion.php" class="active">Restauración</a></li>
                    <li><a href="../../Usuario/Vista/PerfilAdmin.php">Perfil</a></li>
                    <li><a href="../../../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-options">
            <h2>Restaurar Base de Datos</h2>
            <form action="../Controlador/ProcesarRestauracion.php" method="POST" enctype="multipart/form-data" class="login-form">
                <label for="backup_file">Seleccione el archivo de respaldo (.sql):</label>
                <input type="file" name="backup_file" id="backup_file" required>
                
                <button type="submit">Restaurar</button>
            </form>
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
    header("Location: ../../login/login.html");
}
?>
