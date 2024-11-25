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
    <title>Registro de Alumnos</title>
    <link rel="stylesheet" href="../../../Static/css/styles.css">
    <script src="../../../Static/js/validaciones.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>
                <img src="../../../Static/img/logo.png" alt="Logo UPEMOR"> Registro de Alumnos
            </h1>
            <nav>
                <ul>
                    <li><a href="../../AdminIndex.php" >Inicio</a></li>
                    <li><a href="../Registro.php" class="active">Registros</a></li>
                    <li><a href="../../Usuario/Vista/PerfilAdmin.php">Perfil</a></li>
                    <li><a href="../../../login/logout.php">Cerrar Sesión</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <main>

        <div class="login-form">
            <h2>Registrar Alumno</h2>
            <form method="post" name="frmalum" id="frmalum" action="../Controlador/CrearAlumno.php" onsubmit="return validateForm()">
                
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

                <label for="cuatrimestre">Cuatrimestre</label>
                <select id="cuatrimestre" name="cuatrimestre" required>
                    <option value="" disabled selected>Selecciona un cuatrimestre</option>
                    <option value="1">Cuatrimestre 1</option>
                    <option value="2">Cuatrimestre 2</option>
                    <option value="3">Cuatrimestre 3</option>
                    <option value="4">Cuatrimestre 4</option>
                    <option value="5">Cuatrimestre 5</option>
                    <option value="6">Cuatrimestre 6</option>
                    <option value="7">Cuatrimestre 7</option>
                    <option value="8">Cuatrimestre 8</option>
                    <option value="9">Cuatrimestre 9</option>
                </select>

                <label for="correo">Correo Electronico</label>
                <input type="text" id="correo" name="correo" placeholder="Ingresa el correo electronico" required>
                
                <!-- Mostrar mensajes de éxito o error -->
                <?php if (isset($_GET['success'])): ?>
                        <p style="color: green;">¡Alumno registrado con exito!</p>
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
    } else {
        header("Location: ../../../login/login.html");
    }
?>
