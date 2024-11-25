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
    <title>Ver Alumnos</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>
                <img src="../../../Static/img/logo.png" alt="Logo UPEMOR"> Lista de Alumnos
            </h1>
            <nav>
                <ul>
                    <li><a href="../../adminIndex.php">Inicio</a></li>
                    <li><a href="../../BD/Vista/Respaldo.php">Respaldo</a></li>
                    <li><a href="../../BD/Vista/Restauracion.php">Restauración</a></li>
                    <li><a href="../Usuario.php" class="active">Usuarios</a></li>
                    <li><a href="../Vista/PerfilAdmin.php">Perfil</a></li>
                    <li><a href="../../../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div>
            <h2>Alumnos Registrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Género</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Cuatrimestre</th>
                        <th>Correo</th>
                    </tr>
                </thead>

                <?php 
                    $sql = "SELECT usuario.*, alumno.cuatrimestre FROM usuario INNER JOIN alumno ON usuario.id_usuario = alumno.id_usuario;";
                    
                    $exec = mysqli_query($conn, $sql);
                    while ($rows = mysqli_fetch_array($exec)) {
                ?>
                    <tbody>
                        <tr> 
                            <td><?php echo $rows['id_usuario'] ?></td>
                            <td><?php echo $rows['usuario'] ?></td>
                            <td><?php echo $rows['nombre'] ?></td>
                            <td><?php echo $rows['apellido'] ?></td>
                            <td><?php echo $rows['genero'] ?></td>
                            <td><?php echo $rows['fec_nac'] ?></td>
                            <td><?php echo $rows['cuatrimestre'] ?></td>
                            <td><?php echo $rows['correo_electronico'] ?></td>
                        </tr>
                    </tbody>
                <?php } ?>
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
<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>