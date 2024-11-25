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
    // Manejar el cambio de peso máximo permitido
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['peso_maximo'])) {
        $peso_maximo = floatval($_POST['peso_maximo']);
        $sqlUpdatePeso = "UPDATE pesoarchivos SET peso = '$peso_maximo' WHERE idpesoArchivos = 1";
        mysqli_query($conn, $sqlUpdatePeso);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Manejar la adición de nuevas extensiones
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_extension'])) {
        $nueva_extension = mysqli_real_escape_string($conn, $_POST['nueva_extension']);
        $sqlAddExtension = "INSERT INTO extensionesArchivos (extension) VALUES ('$nueva_extension')";
        mysqli_query($conn, $sqlAddExtension);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Manejar la eliminación de extensiones
    if (isset($_GET['eliminar_extension'])) {
        $id_extension = intval($_GET['eliminar_extension']);
        $sqlDeleteExtension = "DELETE FROM extensionesArchivos WHERE idextensionesArchivos = $id_extension";
        if (mysqli_query($conn, $sqlDeleteExtension)) {
            echo "<script>alert('Extensión eliminada correctamente.'); window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
            exit();
        } else {
            echo "<script>alert('Error al eliminar la extensión.');</script>";
        }
    }

    // Obtener el peso máximo permitido
    $sqlPesoMaximo = "SELECT peso FROM pesoarchivos WHERE idpesoArchivos = 1";
    $resultPesoMaximo = mysqli_query($conn, $sqlPesoMaximo);
    $peso_maximo = mysqli_fetch_assoc($resultPesoMaximo)['peso'];

    // Obtener todas las extensiones permitidas
    $sqlExtensiones = "SELECT * FROM extensionesArchivos";
    $resultExtensiones = mysqli_query($conn, $sqlExtensiones);
} else {
    header("Location: ../../../login/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Archivos</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <script>
        function confirmarEliminacion(idExtension) {
            if (confirm("¿Estás seguro de que deseas eliminar esta extensión?")) {
                window.location.href = `${window.location.pathname}?eliminar_extension=${idExtension}`;
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1>
                <img src="../../../Static/img/logo.png" alt="Logo UPEMOR">Administración de Archivos
            </h1>
            <nav>
                <ul>
                    <li><a href="../Registro.php" class="active">Regresar</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <h1 style="text-align: center;">Administración de Archivos Permitidos</h1>

        <div class="recuadro_indicaciones">
            <p style="text-align: center;">Desde esta sección puedes gestionar las extensiones y el peso máximo permitido para los archivos en el sistema.</p>
        </div>

        <!-- Cambio de peso máximo permitido -->
        <div style="margin: 2% 5%;">
            <h2>Peso Máximo Permitido</h2>
            <form method="POST">
                <label for="peso_maximo">Peso máximo (MB):</label>
                <input type="number" step="0.01" name="peso_maximo" id="peso_maximo" value="<?php echo $peso_maximo; ?>" required>
                <button type="submit">Actualizar</button>
            </form>
        </div>

        <!-- Adición de nuevas extensiones -->
        <div style="margin: 2% 5%;">
            <h2>Añadir Nueva Extensión</h2>
            <form method="POST">
                <label for="nueva_extension">Extensión (ejemplo: pdf):</label>
                <input type="text" name="nueva_extension" id="nueva_extension" placeholder=".ext" required>
                <button type="submit">Añadir</button>
            </form>
        </div>

        <!-- Lista de extensiones permitidas -->
        <div style="margin: 2% 5%;">
            <h2>Extensiones Permitidas</h2>
            <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Extensión</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rowExtension = mysqli_fetch_assoc($resultExtensiones)) { ?>
                        <tr>
                            <td><?php echo $rowExtension['idextensionesArchivos']; ?></td>
                            <td><?php echo $rowExtension['extension']; ?></td>
                            <td>
                                <button onclick="confirmarEliminacion(<?php echo $rowExtension['idextensionesArchivos']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 UPEMOR. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
