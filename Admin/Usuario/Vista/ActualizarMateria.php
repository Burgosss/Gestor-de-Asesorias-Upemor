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

$id_materia = $_GET['id_materia'];

$query = "SELECT * FROM materia WHERE id_materia = $id_materia;";
$result = mysqli_query($conn, $query);


if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $nombre = $row['nombre'];
    $cuatrimestre = $row['cuatrimestre'];
    $creditos = $row['Creditos'];
    $descripcion = $row['Descripcion'];

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Alumno</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <script src="../../../Static/js/validaciones.js"></script>

</head>
<body>
    <header>
        <div class="container">
            <h1>Actualizar Materia</h1>
            <nav>
                <ul>
                    <li><a href="EstadoMaterias.php" class="active">Regresar</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="login-form">
            <h2>Datos de la Materia</h2>
            <form action="../Controlador/UpdateMateria.php?id_materia=<?php echo $id_materia; ?>" method="POST" onsubmit="return validateMateriaForm()">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" required>

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

                <label for="creditos">Creditos</label>
                <select id="creditos" name="creditos" required>
                    <option value="4" <?php if($creditos == '4') echo 'selected' ?>;>4 Créditos</option>
                    <option value="5" <?php if($creditos == '5') echo 'selected' ?>;>5 Créditos</option>
                    <option value="6" <?php if($creditos == '6') echo 'selected' ?>;>6 Créditos</option>
                    <option value="7" <?php if($creditos == '7') echo 'selected' ?>;>7 Créditos</option>
                    <option value="8" <?php if($creditos == '8') echo 'selected' ?>;>8 Créditos</option>

                </select>  

                <label for="descripcion">Descripcion</label>
                <input type="text" id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>" required>

            


                <div id="error-messages" class="error-message"></div>
                <input type="submit" name="update" value="Actualizar" onclick="return validateMateriaForm();">
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