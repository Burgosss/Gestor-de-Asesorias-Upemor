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

$query = "SELECT usuario.* FROM usuario INNER JOIN profesor ON usuario.id_usuario = profesor.id_usuario WHERE usuario.id_usuario = $id_usuario;";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $usuario = $row['usuario'];
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $genero = $row['genero'];
    $fec_nac = $row['fec_nac'];
    $correo = $row['correo_electronico'];
}
?>

<?php
// Obtener todas las materias disponibles
$sqlMaterias = "SELECT * FROM materia";
$resultMaterias = mysqli_query($conn, $sqlMaterias);

// Obtener las materias actuales del profesor
$sqlMateriasProfesor = "SELECT id_materia FROM profesor_materia WHERE id_profesor = (SELECT id_profesor FROM profesor WHERE id_usuario = $id_usuario)";
$resultMateriasProfesor = mysqli_query($conn, $sqlMateriasProfesor);

// Convertir las materias del profesor en un array
$materiasProfesor = [];
while ($materia = mysqli_fetch_assoc($resultMateriasProfesor)) {
    $materiasProfesor[] = $materia['id_materia'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Profesor</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <script src="../../../Static/js/validaciones.js"></script>

</head>
<body>
    <header>
        <div class="container">
            <h1>Actualizar Profesor</h1>
            <nav>
                <ul>
                    <li><a href="EstadoProfesores.php" class="active">Regresar</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="login-form">
            <h2>Datos del Profesor</h2>
            <form action="../Controlador/UpdateProfesor.php?id_usuario=<?php echo $id_usuario; ?>" method="POST" onsubmit="return validateFormUpdateAdmin()">
                
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

                <label>Materias:</label>
                <div>
                    <?php while ($materia = mysqli_fetch_assoc($resultMaterias)) { ?>
                        <div>
                            <label>
                                <?php echo $materia['nombre']; ?> (Cuatrimestre: <?php echo $materia['cuatrimestre']; ?>)
                            </label>
                            <input type="checkbox" name="materias[]" value="<?php echo $materia['id_materia']; ?>" 
                            <?php echo in_array($materia['id_materia'], $materiasProfesor) ? 'checked' : ''; ?>>
                        </div>
                    <?php } ?>
                </div>

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