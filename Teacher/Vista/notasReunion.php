<?php
// Inicia la sesión
session_start();
include '../../Static/connect/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

// Verificar que se recibió el ID de la asesoría
if (!isset($_GET['id_asesoria'])) {
    header("Location: agregarObservaciones.php");
    exit();
}

$id_asesoria = $_GET['id_asesoria'];

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calificacionP1 = isset($_POST['calificacionP1']) ? floatval($_POST['calificacionP1']) : null;
    $calificacionP2 = isset($_POST['calificacionP2']) ? floatval($_POST['calificacionP2']) : null;
    $hora_salida = isset($_POST['hora_salida']) ? $_POST['hora_salida'] : null;
    $recomendaciones = isset($_POST['recomendaciones']) ? mysqli_real_escape_string($conn, $_POST['recomendaciones']) : null;

    // Verificar si ya existen notas de reunión para esta asesoría
    $sqlCheck = "SELECT idnotas_reunion FROM notas_reunion WHERE id_asesoria = '$id_asesoria'";
    $resultCheck = mysqli_query($conn, $sqlCheck);

    if (mysqli_num_rows($resultCheck) > 0) {
        // Si existen, actualizar los datos
        $rowCheck = mysqli_fetch_assoc($resultCheck);
        $idnotas_reunion = $rowCheck['idnotas_reunion'];
        $sqlUpdate = "
            UPDATE notas_reunion
            SET calificacionP1 = '$calificacionP1', calificacionP2 = " . ($calificacionP2 !== null ? "'$calificacionP2'" : "NULL") . ", 
                hora_salida = '$hora_salida', recomendaciones = '$recomendaciones'
            WHERE idnotas_reunion = '$idnotas_reunion'
        ";
        mysqli_query($conn, $sqlUpdate);
    } else {
        // Si no existen, insertar los datos
        $sqlInsert = "
            INSERT INTO notas_reunion (calificacionP1, calificacionP2, hora_salida, recomendaciones, id_asesoria)
            VALUES ('$calificacionP1', " . ($calificacionP2 !== null ? "'$calificacionP2'" : "NULL") . ", 
                    '$hora_salida', '$recomendaciones', '$id_asesoria')
        ";
        mysqli_query($conn, $sqlInsert);
    }

    // Actualizar el estado de la asesoría a "Finalizada"
    $sqlUpdateAsesoria = "UPDATE asesoria SET estado = 'Finalizada' WHERE id_asesoria = '$id_asesoria'";
    mysqli_query($conn, $sqlUpdateAsesoria);

    echo "<script>alert('Notas de reunión guardadas con éxito.'); window.location.href = 'agregarObservaciones.php';</script>";
    exit();
}

// Obtener los datos existentes de notas de reunión, si es que ya existen
$sqlNotas = "SELECT * FROM notas_reunion WHERE id_asesoria = '$id_asesoria'";
$resultNotas = mysqli_query($conn, $sqlNotas);
$notas = mysqli_fetch_assoc($resultNotas);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas de Reunión</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="container">
            <h1>
                <img src="../../Static/img/logo.png" alt="Logo UPEMOR"> Upemor - Sistema de Gestión de Asesorías
            </h1>
            <nav>
                <ul>
                    <li><a href="../ProfesorIndex.php">Inicio</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div>
            <h2 style="margin-left: 5%;"><a href="agregarObservaciones.php">< Regresar</a></h2>
            <h1 style="text-align: center;">Notas de Reunión</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p>
                Completa los datos de la reunión y haz clic en "Guardar" para finalizar.
            </p>
        </div>

        <div>
            <form method="POST" class="login-form">
                <label for="calificacionP1">Calificación Parcial 1:</label>
                <input type="number" step="0.01" name="calificacionP1" id="calificacionP1" value="<?php echo $notas['calificacionP1'] ?? ''; ?>" required>
                <br><br>

                <label for="calificacionP2">Calificación Parcial 2 (opcional):</label>
                <input type="number" step="0.01" name="calificacionP2" id="calificacionP2" value="<?php echo $notas['calificacionP2'] ?? ''; ?>">
                <br><br>

                <label for="hora_salida">Hora de Salida:</label>
                <input type="time" name="hora_salida" id="hora_salida" value="<?php echo $notas['hora_salida'] ?? ''; ?>" required>
                <br><br>

                <label for="recomendaciones">Recomendaciones:</label>
                <textarea name="recomendaciones" id="recomendaciones" rows="4" cols="50" required><?php echo $notas['recomendaciones'] ?? ''; ?></textarea>
                <br><br>

                <button type="submit" class="boton_general">Guardar</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
