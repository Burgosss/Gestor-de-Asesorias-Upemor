    <?php
    session_start();
    include '../../../Static/connect/db.php';


    if (!isset($_SESSION['id_usuario'])) {
        header("Location: ../../login/login.html");
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
        <title>Copias de Seguridad</title>
        <link rel="stylesheet" href="../../../Static/css/styles.css">
    </head>
    <body>
        <header>
            <div class="container">
                <h1><img src="../../../Static/img/logo.png" alt="Logo UPEMOR">Copias de Seguridad</h1>
                <nav>
                    <ul>
                        <li><a href="../../adminIndex.php">Inicio</a></li>
                        <li><a href="Respaldo.php" class="active">Respaldo</a></li>
                        <li><a href="Restauracion.php">Restauración</a></li>
                        <li><a href="../../Usuario/Vista/PerfilAdmin.php">Perfil</a></li>
                        <li><a href="../../../login/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
            <div class="admin-options">
                <h2>Generar Copia de Seguridad</h2>
                    <!-- Mostrar mensajes de éxito o error -->
                    <?php if (isset($_GET['success'])): ?>
                        <p style="color: green;">¡Copia de seguridad creada exitosamente!</p>
                    <?php elseif (isset($_GET['error'])): ?>
                        <p style="color: red;">Hubo un error al generar la copia de seguridad.</p>
                    <?php endif; ?>

                <form method="POST" action="../Controlador/backup.php">
                    <button type="submit" name="backup">Crear y Descargar Copia de Seguridad</button>
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
            header("Location: ../../login/login.html");
        }
    ?>
