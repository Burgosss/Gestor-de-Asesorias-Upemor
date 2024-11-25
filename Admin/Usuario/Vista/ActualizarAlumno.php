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
$id_usuario = $_GET['id_usuario'];

$query = "SELECT usuario.*, alumno.cuatrimestre FROM usuario INNER JOIN alumno ON usuario.id_usuario = alumno.id_usuario WHERE usuario.id_usuario = $id_usuario;";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $usuario = $row['usuario'];
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $genero = $row['genero'];
    $fec_nac = $row['fec_nac'];
    $correo = $row['correo_electronico'];
    $cuatrimestre = $row['cuatrimestre'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Alumno</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <script src="../../../Static\js\validaciones.js"></script>
</head> 
<body>
    <header>
        <div class="container">
            <h1>Actualizar Alumno</h1>
            <nav>
                <ul>
                    <li><a href="EstadoAlumnos.php" class="active">Regresar</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="login-form">
            <h2>Datos del alumno</h2>
            <form action="../Controlador/UpdateAlumno.php?id_usuario=<?php echo $id_usuario; ?>" method="POST" onsubmit="return validateFormUpdateAdmin()">
                
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

                <label for="cuatrimestre">Cuatrimestre:</label>
                <select name="cuatrimestre" required>
                    <option value="1" <?php if($cuatrimestre == '1') echo 'selected'; ?>>Cuatrimestre 1</option>
                    <option value="2" <?php if($cuatrimestre == '2') echo 'selected'; ?>>Cuatrimestre 2</option>
                    <option value="3" <?php if($cuatrimestre == '3') echo 'selected'; ?>>Cuatrimestre 3</option>
                    <option value="4" <?php if($cuatrimestre == '4') echo 'selected'; ?>>Cuatrimestre 4</option>
                    <option value="5" <?php if($cuatrimestre == '5') echo 'selected'; ?>>Cuatrimestre 5</option>
                    <option value="6" <?php if($cuatrimestre == '6') echo 'selected'; ?>>Cuatrimestre 6</option>
                    <option value="7" <?php if($cuatrimestre == '7') echo 'selected'; ?>>Cuatrimestre 7</option>
                    <option value="8" <?php if($cuatrimestre == '8') echo 'selected'; ?>>Cuatrimestre 8</option>
                    <option value="9" <?php if($cuatrimestre == '9') echo 'selected'; ?>>Cuatrimestre 9</option>
                </select>
                
                <div id="error-messages" class="error-message"></div>
                <input type="submit" name="update" value="Actualizar" onclick="return validateFormUpdateAdmin();">
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
        header("Location: ../../../login/login.html");
    }
?>