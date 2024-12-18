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

<?php

$query = "SELECT usuario.* FROM usuario INNER JOIN admin ON usuario.id_usuario = admin.id_usuario WHERE usuario.id_usuario = $id_usuario;";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $usuario = $row['usuario'];
    $password = $row['password'];
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $genero = $row['genero'];
    $fec_nac = $row['fec_nac'];
    $correo = $row['correo_electronico'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Admin</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <script src="../../../Static/js/validaciones.js"></script>

</head>
<body>
    <header>
    <div class="container">
        <h1><img src="../../../Static/img/logo.png" alt="Logo UPEMOR">Opciones de usuarios.</h1>
        <nav>
            <ul>
                <li><a href="../../adminIndex.php" >Inicio</a></li>
                <li><a href="PerfilAdmin.php" class="active">Perfil</a></li>
                <li><a href="../../Registro/Registro.php">Registro</a></li>
                <li><a href="../../Usuario/Usuario.php">Opciones</a></li>
                <li><a href="../../../login/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
    </header>

    <main>
        
        <div class="login-form">
            <h2>Datos del Administrador</h2>
            <form action="../Controlador/ProfileAdmin.php?id_usuario=<?php echo $id_usuario; ?>" method="POST" onsubmit="return validateFormUpdate()">

                <label for="password">Contraseña:</label>
                <input type="text" name="password" id="password" value="<?php echo $password; ?>" required>

                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" required>

                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" value="<?php echo $apellido; ?>" required>

                <label for="genero">Género:</label>
                <select name="genero" id="genero" required>
                    <option value="Masculino" <?php if($genero == 'Masculino') echo 'selected'; ?>>Masculino</option>
                    <option value="Femenino" <?php if($genero == 'Femenino') echo 'selected'; ?>>Femenino</option>
                    <option value="Otro" <?php if($genero == 'Otro') echo 'selected'; ?>>Otro</option>
                </select>

                <label for="fec_nac">Fecha de Nacimiento:</label>
                <input type="date" name="fec_nac" id="fec_nac" value="<?php echo $fec_nac; ?>" required>

                <div id="error-messages" class="error-message"></div>
                <input type="submit" name="update" value="Actualizar" onclick="return validateFormUpdate();">
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
    }else{
        header("Location: ../../login/login.html");
    }
?>