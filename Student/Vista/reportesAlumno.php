<?php
session_start();
require '../../vendor/autoload.php'; // Incluye PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = ""; // Cambia esto si tienes una contraseña establecida
$dbname = "gestorasesorias";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si el usuario es un alumno
$sqlAlumno = "SELECT id_alumno, CONCAT(usuario.nombre, ' ', usuario.apellido) AS nombre_alumno
              FROM alumno
              JOIN usuario ON alumno.id_usuario = usuario.id_usuario
              WHERE usuario.id_usuario = '$id_usuario'";
$resultAlumno = mysqli_query($conn, $sqlAlumno);
$alumno = mysqli_fetch_assoc($resultAlumno);

if (!$alumno) {
    header("Location: ../../login/login.html");
    exit();
}

$id_alumno = $alumno['id_alumno'];
$nombre_alumno = $alumno['nombre_alumno'];

// Función para obtener datos del reporte 1
function obtenerDatosReporte1($conn, $id_alumno, $inicio, $fin)
{
    $sql = "
        SELECT 
            CONCAT(usuario.nombre, ' ', usuario.apellido) AS profesor,
            SUM(CASE WHEN asesoria.estado = 'Aprobada' THEN 1 ELSE 0 END) AS aceptadas,
            SUM(CASE WHEN asesoria.estado = 'Finalizada' THEN 1 ELSE 0 END) AS finalizadas,
            SUM(CASE WHEN asesoria.estado = 'Rechazada' THEN 1 ELSE 0 END) AS rechazadas,
            SUM(CASE WHEN asesoria.estado IN ('Aprobada', 'Finalizada', 'Rechazada') THEN 1 ELSE 0 END) AS total
        FROM profesor
        JOIN usuario ON profesor.id_usuario = usuario.id_usuario
        LEFT JOIN asesoria ON profesor.id_profesor = asesoria.id_profesor
            AND asesoria.id_alumno = $id_alumno
            AND asesoria.fecha BETWEEN '$inicio' AND '$fin'
        GROUP BY profesor.id_profesor, usuario.nombre, usuario.apellido
        ORDER BY profesor";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener datos del reporte 2
function obtenerDatosReporte2($conn, $id_alumno, $inicio, $fin)
{
    $sql = "
        SELECT 
            materia.nombre AS asignatura,
            SUM(CASE WHEN asesoria.estado = 'Aprobada' THEN 1 ELSE 0 END) AS aceptadas,
            SUM(CASE WHEN asesoria.estado = 'Finalizada' THEN 1 ELSE 0 END) AS finalizadas,
            SUM(CASE WHEN asesoria.estado = 'Rechazada' THEN 1 ELSE 0 END) AS rechazadas,
            SUM(CASE WHEN asesoria.estado IN ('Aprobada', 'Finalizada', 'Rechazada') THEN 1 ELSE 0 END) AS total
        FROM materia
        LEFT JOIN asesoria ON materia.id_materia = asesoria.id_materia
            AND asesoria.id_alumno = $id_alumno
            AND asesoria.fecha BETWEEN '$inicio' AND '$fin'
        GROUP BY materia.id_materia, materia.nombre
        ORDER BY materia.nombre ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Generar el archivo Excel
if (isset($_GET['action']) && $_GET['action'] === 'generar_reporte') {
    $reporte = $_POST['reporte'] ?? null;
    $periodo = $_POST['periodo'] ?? null;
    $anio = $_POST['anio'] ?? date('Y');

    // Definir los rangos de fecha según el período seleccionado
    $rangoFechas = [
        'Enero-Abril' => ["$anio-01-01", "$anio-04-30"],
        'Mayo-Agosto' => ["$anio-05-01", "$anio-08-31"],
        'Septiembre-Diciembre' => ["$anio-09-01", "$anio-12-31"],
    ];

    if (!isset($rangoFechas[$periodo])) {
        die("Período no válido.");
    }

    [$inicio, $fin] = $rangoFechas[$periodo];
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    if ($reporte === 'reporte1') {
        $datos = obtenerDatosReporte1($conn, $id_alumno, $inicio, $fin);

        // Encabezados del reporte 1
        $sheet->setCellValue('A1', 'Universidad Politécnica del Estado de Morelos');
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A2', 'Asesorías por profesor');
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A3', 'Alumno: ' . $nombre_alumno);
        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A4', "Periodo: $periodo $anio");
        $sheet->mergeCells('A4:E4');
        $sheet->setCellValue('A5', 'Profesor');
        $sheet->setCellValue('B5', 'Aceptadas');
        $sheet->setCellValue('C5', 'Finalizadas');
        $sheet->setCellValue('D5', 'Rechazadas');
        $sheet->setCellValue('E5', 'Total');

        $row = 6;
        foreach ($datos as $dato) {
            $aceptadas = $dato['aceptadas'] ?? 0;
            $finalizadas = $dato['finalizadas'] ?? 0;
            $rechazadas = $dato['rechazadas'] ?? 0;
            $total = $aceptadas + $finalizadas + $rechazadas;

            $sheet->setCellValue('A' . $row, $dato['profesor']);
            $sheet->setCellValue('B' . $row, $aceptadas);
            $sheet->setCellValue('C' . $row, $finalizadas);
            $sheet->setCellValue('D' . $row, $rechazadas);
            $sheet->setCellValue('E' . $row, $total);
            $row++;
        }
    } elseif ($reporte === 'reporte2') {
        $datos = obtenerDatosReporte2($conn, $id_alumno, $inicio, $fin);

        // Encabezados del reporte 2
        $sheet->setCellValue('A1', 'Universidad Politécnica del Estado de Morelos');
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A2', 'Asesorías por asignatura');
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A3', 'Alumno: ' . $nombre_alumno);
        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A4', "Periodo: $periodo $anio");
        $sheet->mergeCells('A4:E4');
        $sheet->setCellValue('A5', 'Asignatura');
        $sheet->setCellValue('B5', 'Aceptadas');
        $sheet->setCellValue('C5', 'Finalizadas');
        $sheet->setCellValue('D5', 'Rechazadas');
        $sheet->setCellValue('E5', 'Total');

        $row = 6;
        foreach ($datos as $dato) {
            $aceptadas = $dato['aceptadas'] ?? 0;
            $finalizadas = $dato['finalizadas'] ?? 0;
            $rechazadas = $dato['rechazadas'] ?? 0;
            $total = $aceptadas + $finalizadas + $rechazadas;

            $sheet->setCellValue('A' . $row, $dato['asignatura']);
            $sheet->setCellValue('B' . $row, $aceptadas);
            $sheet->setCellValue('C' . $row, $finalizadas);
            $sheet->setCellValue('D' . $row, $rechazadas);
            $sheet->setCellValue('E' . $row, $total);
            $row++;
        }
    } else {
        die("Reporte no válido.");
    }

    // Ajustar el ancho de las columnas
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Aplicar estilo general
    $sheet->getStyle('A1:E' . ($row - 1))->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ]);

    // Aplicar estilo al encabezado
    $sheet->getStyle('A5:E5')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFCCCCCC'],
        ],
    ]);

    $writer = new Xlsx($spreadsheet);
    $filename = "{$reporte}_{$periodo}_{$anio}.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generación de Reportes</title>
    <link rel="stylesheet" href="../../Static/css/styles.css">
</head>
<body>
<header>
        <div class="container"> 
            <h1>
                <img src="../../Static/img/logo.png" alt="Logo UPEMOR"> Upemor - Sistema de Gestión de Asesorías
            </h1>
            <nav>
                <ul>
                    <li><a href="../alumnoIndex.php">Inicio</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div>
            <h1 style="text-align: center;">Generar reportes.</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p>
            En esta sección puedes generar reportes de asesorías por profesor, así como por asignatura.
            Selecciona un tipo de reporte, el período y haz clic en "Generar Reporte" para continuar.
            </p>
        </div>

        <h1 style="text-align: center;">Generar Reportes</h1>
        <form method="POST" action="?action=generar_reporte" style="text-align: center;" class="login-form">
            <label for ="reporte">Selecciona el reporte:</label>
            <select name="reporte" id="reporte" required>
                <option value="reporte1">Reporte por profesor</option>
                <option value="reporte2">Reporte por asignatura</option>
            </select>
            <br><br>
            <label for="periodo">Período:</label>
            <select name="periodo" id="periodo" required>
                <option value="Enero-Abril">Enero - Abril</option>
                <option value="Mayo-Agosto">Mayo - Agosto</option>
                <option value="Septiembre-Diciembre">Septiembre - Diciembre</option>
            </select>
            <br><br>
            <label for="anio">Año:</label>
            <input type="number" name="anio" id="anio" min="2000" max="2100" value="<?php echo date('Y'); ?>" required>
            <br><br>
            <button type="submit" class="boton_general">Generar Reporte</button>
        </form>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> UPEMOR. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
