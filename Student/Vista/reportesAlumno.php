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
            COUNT(asesoria.id_asesoria) AS total
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
            COUNT(asesoria.id_asesoria) AS total_asesorias
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
            $sheet->setCellValue('A' . $row, $dato['profesor']);
            $sheet->setCellValue('B' . $row, $dato['aceptadas'] ?? 0);
            $sheet->setCellValue('C' . $row, $dato['finalizadas'] ?? 0);
            $sheet->setCellValue('D' . $row, $dato['rechazadas'] ?? 0);
            $sheet->setCellValue('E' . $row, $dato['total'] ?? 0);
            $row++;
        }
    } elseif ($reporte === 'reporte2') {
        $datos = obtenerDatosReporte2($conn, $id_alumno, $inicio, $fin);

        // Encabezados del reporte 2
        $sheet->setCellValue('A1', 'Universidad Politécnica del Estado de Morelos');
        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A2', 'Asesorías por asignatura');
        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A3', 'Alumno: ' . $nombre_alumno);
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A4', "Periodo: $periodo $anio");
        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('A5', 'Asignatura');
        $sheet->setCellValue('B5', 'Total de asesorías');

        $row = 6;
        foreach ($datos as $dato) {
            $sheet->setCellValue('A' . $row, $dato['asignatura']);
            $sheet->setCellValue('B' . $row, $dato['total_asesorias'] ?? 0);
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
    $sheet->getStyle('A1:E' . $row)->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
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
                <img src="../../Static/img/logo.png" alt="Logo UPEMOR"> Generación de Reportes
            </h1>
        </div>
    </header>

    <main>
        <h1 style="text-align: center;">Generar Reportes</h1>
        <form method="POST" action="?action=generar_reporte" style="text-align: center;">
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
            <p>&copy; 2024 UPEMOR. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>

