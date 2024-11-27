<?php
session_start();
require '../../../vendor/autoload.php'; // Incluye PhpSpreadsheet

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

// Verificar la conexión
if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
}

// Función para obtener datos del reporte 1
function obtenerDatosReporte1($conn, $inicio, $fin)
{
    $sql = "
        SELECT 
            materia.nombre AS asignatura,
            COUNT(asesoria.id_asesoria) AS cantidad
        FROM materia
        LEFT JOIN asesoria ON asesoria.id_materia = materia.id_materia
            AND asesoria.fecha BETWEEN '$inicio' AND '$fin'
        GROUP BY materia.nombre
        ORDER BY cantidad DESC
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener datos del reporte 2
function obtenerDatosReporte2($conn, $inicio, $fin)
{
    $sql = "
        SELECT 
            CONCAT(usuario.nombre, ' ', usuario.apellido) AS profesor,
            SUM(CASE WHEN asesoria.estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
            SUM(CASE WHEN asesoria.estado = 'Aprobada' THEN 1 ELSE 0 END) AS aceptadas,
            SUM(CASE WHEN asesoria.estado = 'Rechazada' THEN 1 ELSE 0 END) AS rechazadas
        FROM profesor
        LEFT JOIN usuario ON profesor.id_usuario = usuario.id_usuario
        LEFT JOIN asesoria ON profesor.id_profesor = asesoria.id_profesor
            AND asesoria.fecha BETWEEN '$inicio' AND '$fin'
        GROUP BY profesor.id_profesor, usuario.nombre, usuario.apellido
        ORDER BY profesor
    ";
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
        $datos = obtenerDatosReporte1($conn, $inicio, $fin);

        // Configuración de encabezados específicos para el reporte 1
        $sheet->setCellValue('A1', 'Universidad Politécnica del Estado de Morelos.');
        $sheet->mergeCells('A1:D1'); // Combinar celdas A - D en la fila 1

        $sheet->setCellValue('A2', 'Asesorías por asignatura');
        $sheet->mergeCells('A2:D2'); // Combinar celdas A - D en la fila 2

        $sheet->setCellValue('A3', "Periodo: $periodo $anio");
        $sheet->mergeCells('A3:D3'); // Combinar celdas A - D en la fila 3

        $sheet->setCellValue('A4', 'Asignatura');
        $sheet->setCellValue('B4', 'Cantidad');

        $row = 5;
        foreach ($datos as $dato) {
            $sheet->setCellValue('A' . $row, $dato['asignatura']);
            $sheet->setCellValue('B' . $row, $dato['cantidad']);
            $row++;
        }

        // Aplicar estilos solo al reporte 1
        $sheet->getStyle('A1:D3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => ['bold' => true],
        ]);
    } elseif ($reporte === 'reporte2') {
        $datos = obtenerDatosReporte2($conn, $inicio, $fin);

        // Configuración de encabezados específicos para el reporte 2
        $sheet->setCellValue('A1', 'Universidad Politécnica del Estado de Morelos.');
        $sheet->mergeCells('A1:D1'); // Combinar celdas A - D en la fila 1

        $sheet->setCellValue('A2', 'Asesorías por profesor');
        $sheet->mergeCells('A2:D2'); // Combinar celdas A - D en la fila 2

        $sheet->setCellValue('A3', "Periodo: $periodo $anio");
        $sheet->mergeCells('A3:D3'); // Combinar celdas A - D en la fila 3

        $sheet->setCellValue('A4', 'Profesor');
        $sheet->setCellValue('B4', 'Pendientes');
        $sheet->setCellValue('C4', 'Aceptadas');
        $sheet->setCellValue('D4', 'Rechazadas');

        $row = 5;
        foreach ($datos as $dato) {
            $sheet->setCellValue('A' . $row, $dato['profesor']);
            $sheet->setCellValue('B' . $row, $dato['pendientes']);
            $sheet->setCellValue('C' . $row, $dato['aceptadas']);
            $sheet->setCellValue('D' . $row, $dato['rechazadas']);
            $row++;
        }
    } else {
        die("Reporte no válido.");
    }

    // Ajustar el ancho de las columnas
    foreach (range('A', 'D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Aplicar estilo general para ambos reportes
    $sheet->getStyle('A1:D' . $row)->applyFromArray([
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
    <link rel="stylesheet" href="../../../Static/css/styles.css"">
</head>
<body>
    <header>
        <div class="container">
            <h1>
                <img src="../../../Static/img/logo.png" alt="Logo UPEMOR"> Generación de Reportes
            </h1>
        </div>
    </header>

    <main>
        <h1 style="text-align: center;">Generar Reportes</h1>
        <form method="POST" action="?action=generar_reporte" class="login-form">
            <label for="reporte">Selecciona el reporte:</label>
            <select name="reporte" id="reporte" required>
                <option value="reporte1">Reporte por asignatura</option>
                <option value="reporte2">Reporte por profesor</option>
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
