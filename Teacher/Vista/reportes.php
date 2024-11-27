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

// Obtener el ID del profesor asociado al usuario
$sqlProfesor = "SELECT profesor.id_profesor, CONCAT(usuario.nombre, ' ', usuario.apellido) AS nombre_profesor 
                FROM profesor 
                JOIN usuario ON profesor.id_usuario = usuario.id_usuario 
                WHERE usuario.id_usuario = $id_usuario";
$resultProfesor = $conn->query($sqlProfesor);
$profesor = $resultProfesor->fetch_assoc();

$id_profesor = $profesor['id_profesor'];
$nombre_profesor = $profesor['nombre_profesor'];

// Verificar la conexión
if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
}

// Función para obtener datos del reporte
function obtenerDatosReporte($conn, $id_profesor, $inicio, $fin)
{
    $sql = "
        SELECT 
            materia.nombre AS asignatura,
            SUM(CASE WHEN asesoria.estado = 'Aprobada' THEN 1 ELSE 0 END) AS aceptadas,
            SUM(CASE WHEN asesoria.estado = 'Finalizada' THEN 1 ELSE 0 END) AS finalizadas,
            COUNT(asesoria.id_asesoria) AS total
        FROM asesoria
        JOIN materia ON asesoria.id_materia = materia.id_materia
        WHERE asesoria.id_profesor = $id_profesor
        AND asesoria.fecha BETWEEN '$inicio' AND '$fin'
        GROUP BY materia.nombre
        ORDER BY materia.nombre ASC
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Generar el archivo Excel
if (isset($_GET['action']) && $_GET['action'] === 'generar_reporte') {
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

    $datos = obtenerDatosReporte($conn, $id_profesor, $inicio, $fin);

    if (empty($datos)) {
        die("No se encontraron registros para el período seleccionado.");
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configuración del encabezado
    $sheet->setCellValue('A1', 'Universidad Politécnica del Estado de Morelos');
    $sheet->mergeCells('A1:D1');
    $sheet->setCellValue('A2', 'Asignaturas por alumno');
    $sheet->mergeCells('A2:D2');
    $sheet->setCellValue('A3', 'Profesor: ' . $nombre_profesor);
    $sheet->mergeCells('A3:D3');
    $sheet->setCellValue('A4', "Periodo: $periodo $anio");
    $sheet->mergeCells('A4:D4');
    $sheet->setCellValue('A5', 'Asignatura');
    $sheet->setCellValue('B5', 'Aceptadas');
    $sheet->setCellValue('C5', 'Finalizadas');
    $sheet->setCellValue('D5', 'Total');

    // Aplicar estilo al encabezado
    $sheet->getStyle('A1:D5')->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
        'font' => [
            'bold' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ]);

    // Rellenar los datos
    $row = 6;
    foreach ($datos as $dato) {
        $sheet->setCellValue('A' . $row, $dato['asignatura']);
        $sheet->setCellValue('B' . $row, $dato['aceptadas']);
        $sheet->setCellValue('C' . $row, $dato['finalizadas']);
        $sheet->setCellValue('D' . $row, $dato['total']);
        $row++;
    }

    // Ajustar el ancho de las columnas
    foreach (range('A', 'D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = "reporte_asignaturas_{$periodo}_{$anio}.xlsx";

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
        <h1 style="text-align: center;">Generar Reporte</h1>
        <form method="POST" action="?action=generar_reporte" style="text-align: center;">
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
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
