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
                    <li><a href="../../Usuario/PerfilAdmin.php">Perfil</a></li>
                    <li><a href="../../../login/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-options">
            <h2>Generar Copia de Seguridad</h2>
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
