<?php
session_start();
include '../../Static/connect/db.php';
include '../Controlador/gestDispControl.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar que el usuario sea un profesor
$sqlProfesor = "SELECT * FROM profesor WHERE id_usuario = '$id_usuario'";
$resultProfesor = mysqli_query($conn, $sqlProfesor);

if (mysqli_num_rows($resultProfesor) == 1) {
    $rowProfesor = mysqli_fetch_assoc($resultProfesor);
    $id_profesor = $rowProfesor['id_profesor'];

    // Obtener las asesorías correspondientes al profesor
    $sqlAsesorias = "
        SELECT 
            u.nombre AS alumno_nombre, 
            u.apellido AS alumno_apellido, 
            a.concepto, 
            a.fecha, 
            a.hora, 
            a.estado, 
            m.nombre AS materia, 
            al.cuatrimestre,
            a.id_asesoria
        FROM asesoria a
        JOIN alumno al ON a.id_alumno = al.id_alumno
        JOIN usuario u ON al.id_usuario = u.id_usuario
        JOIN materia m ON a.id_materia = m.id_materia
        WHERE a.id_profesor = '$id_profesor' 
        AND (a.estado = 'Aprobada' OR a.estado = 'Finalizada')
        ORDER BY a.fecha DESC, a.hora DESC
    ";


    $resultAsesorias = mysqli_query($conn, $sqlAsesorias);
} else {
    header("Location: ../../login/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir Material Didáctico</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
</head>

<body>
    <header>
        <div class="container"> 
            <h1>
                <img src="../../Static/img/logo.png" alt="Logo UPEMOR">Upemor - Sistema de Gestión de Asesorías
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
            <h2 style="margin-left: 5%;"><a href="../ProfesorIndex.php">< Regresar</a></h2>
            <h1 style="text-align: center;">Compartir Material Didáctico</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p> 
                A continuación se muestran todas sus asesorías, haz click en "Compartir Material" para 
                compartir archivos con el alumno.
            </p>
        </div>

        <div>
            <!-- Tabla de asesorías -->
            <div style="margin: 5%;">
                <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Cuatrimestre</th>
                            <th>Materia</th>
                            <th>Concepto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($resultAsesorias)) { ?>
                            <tr>
                                <td><?php echo $row['alumno_nombre'] . ' ' . $row['alumno_apellido']; ?></td>
                                <td><?php echo $row['cuatrimestre']; ?></td>
                                <td><?php echo $row['materia']; ?></td>
                                <td><?php echo $row['concepto']; ?></td>
                                <td><?php echo $row['fecha']; ?></td>
                                <td><?php echo $row['estado']; ?></td>
                                <td>
                                    <a href="subirMaterial.php?id_asesoria=<?php echo $row['id_asesoria']; ?>">Compartir Material</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
             
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
            <p><a href="#">Política de privacidad</a> | <a href="#">Términos y condiciones</a></p>
        </div>
    </footer>
</body>
</html>


