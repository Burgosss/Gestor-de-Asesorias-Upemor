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
                <img src="../../../Static/img/logo.png" alt="Logo UPEMOR"> Lista de Profesores
            </h1>
            <nav>
                <ul>
                    <li><a href="../Usuario.php" class="active">Regresar</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div>
            <h2>Profesores Registrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Contraseña</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Género</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Correo</th>
                        <th>Eliminar</th>
                        <th>Actualizar</th>
                    </tr>
                </thead>

                <?php 
                    $sql = "SELECT usuario.* FROM usuario INNER JOIN profesor ON usuario.id_usuario = profesor.id_usuario;";
                    $exec=mysqli_query($conn,$sql);
                    while($rows=mysqli_fetch_array($exec)){
                ?>
                    <tbody>
                        <tr> 
                            <td><?php echo $rows['id_usuario'] ?></td>
                            <td><?php echo $rows['usuario'] ?> </td>
                            <td><?php echo $rows['password'] ?> </td>
                            <td><?php echo $rows['nombre'] ?> </td>
                            <td><?php echo $rows['apellido'] ?> </td>
                            <td><?php echo $rows['genero'] ?> </td>
                            <td><?php echo $rows['fec_nac'] ?> </td>
                            <td><?php echo $rows['correo_electronico'] ?></td>
                            <td><a href="../Controlador/DeleteProfesor.php?id_usuario=<?php echo $rows['id_usuario'];?>">Eliminar</a></td>
                            <td><a href="ActualizarProfesor.php?id_usuario=<?php echo $rows['id_usuario'];?>">Actualizar</a></td>
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