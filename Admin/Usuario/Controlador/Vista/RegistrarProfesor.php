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
// Obtén las materias desde la base de datos
$sqlMaterias = "SELECT * FROM materia";
$resultMaterias = mysqli_query($conn, $sqlMaterias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Profesores</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <script src="../../../Static/js/validaciones.js"></script>

</head>
<body>
    <header>
        <div class="container">
            <h1>
                <img src="../../../Static/img/logo.png" alt="Logo UPEMOR">Registro de Profesores
            </h1>
            <nav>
                <ul>
                    <li><a href="../../AdminIndex.php">Inicio</a></li>
                    <li><a href="../Registro.php" class="active">Registros</a></li>
                    <li><a href="../../Usuario/Vista/PerfilAdmin.php">Perfil</a></li>
                    <li><a href="../../../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="login-form">
            <h2>Registrar Profesor</h2>
            <form method="post" name="frmprof" id="frmprof" action="../Controlador/Crearprofesor.php" onsubmit="return validateForm()">
                
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingresa el nombre" required>

                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" placeholder="Ingresa el apellido" required>

                <label for="genero">Género</label>
                <select id="genero" name="genero" required>
                    <option value="" disabled selected>Selecciona el género</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>

                <label for="fec_nac">Fecha de nacimiento</label>
                <input type="date" id="fec_nac" name="fec_nac" required>

                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="Ingresa el correo electrónico" required>

                <!-- Materias -->
                <label>Materias:</label>
                <div>
                    <?php while ($materia = mysqli_fetch_assoc($resultMaterias)) { ?>
                        <div>
                        <label><?php echo $materia['nombre']; ?> (Cuatrimestre: <?php echo $materia['cuatrimestre']; ?>)</label>
                            <input type="checkbox" name="materias[]" value="<?php echo $materia['id_materia'] ; ?>">
                        </div>
                    <?php } ?>
                </div>

                <!-- Mostrar mensajes de éxito o error -->
                <?php if (isset($_GET['success'])): ?>
                        <p style="color: green;">Profesor registrado con exito!</p>
                    <?php elseif (isset($_GET['error'])): ?>
                        <p style="color: red;">El correo ingresado ya esta registrado.</p>
                    <?php endif; ?>

                <div id="error-messages" class="error-message"></div>
                <button type="submit" onclick="return validateForm();">Registrar</button>             
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
