<?php
session_start();
require '../../vendor/autoload.php'; // Incluye PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/login.html");
    exit();
}

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

// Función para obtener datos de la base de datos
function obtenerDatos($conn, $inicio, $fin)
{
    $sql = "
        SELECT 
            CONCAT(alumno_usuario.nombre, ' ', alumno_usuario.apellido) AS nombre_alumno,
            alumno.cuatrimestre,
            materia.nombre AS materia,
            asesoria.id_asesoria, asesoria.concepto, asesoria.fecha, asesoria.hora,
            CONCAT(profesor_usuario.nombre, ' ', profesor_usuario.apellido) AS nombre_profesor,
            notas_reunion.calificacionP1, notas_reunion.calificacionP2,
            notas_reunion.hora_salida, notas_reunion.recomendaciones
        FROM asesoria
        JOIN alumno ON asesoria.id_alumno = alumno.id_alumno
        JOIN usuario AS alumno_usuario ON alumno.id_usuario = alumno_usuario.id_usuario
        JOIN profesor ON asesoria.id_profesor = profesor.id_profesor
        JOIN usuario AS profesor_usuario ON profesor.id_usuario = profesor_usuario.id_usuario
        JOIN materia ON asesoria.id_materia = materia.id_materia
        LEFT JOIN notas_reunion ON asesoria.id_asesoria = notas_reunion.id_asesoria
        WHERE asesoria.estado = 'Finalizada'
        AND asesoria.fecha BETWEEN '$inicio' AND '$fin'
        ORDER BY asesoria.fecha DESC, asesoria.hora DESC
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
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

    $datos = obtenerDatos($conn, $inicio, $fin);

    if (empty($datos)) {
        die("No se encontraron registros para el período seleccionado.");
    }

    // Obtener el nombre del asesor (profesor)
    $nombre_profesor = $datos[0]['nombre_profesor'] ?? '';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Agregar las filas solicitadas
    // Fila 1
    $sheet->setCellValue('A1', 'UPEMOR');
    $sheet->mergeCells('A1:B1');

    $sheet->setCellValue('C1', 'Evidencia de asesoría académica individual');
    $sheet->mergeCells('C1:L1');

    // Aplicar estilo a "Evidencia de asesoría académica individual"
    $sheet->getStyle('C1:L1')->applyFromArray([
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT
        ]
    ]);

    // Fila 2
    $sheet->setCellValue('A2', 'DIAA-R1');
    $sheet->mergeCells('A2:L2');

    // Fila 3
    $sheet->setCellValue('A3', 'Desarrollo integral del estudiante - Dirección de desarrollo académico');
    $sheet->mergeCells('A3:L3');

    // Fila 4
    $sheet->setCellValue('A4', 'Ingeniería en Tecnologías de la Información.');
    $sheet->mergeCells('A4:L4');

    // Fila 5
    $sheet->setCellValue('A5', 'Nombre del asesor: ' . $nombre_profesor);
    $sheet->mergeCells('A5:L5');

    // Fila 6
    $sheet->setCellValue('A6', 'Periodo: ' . $periodo . ' ' . $anio);
    $sheet->mergeCells('A6:L6');

    // Configuración de encabezados (comienza en la fila 7)
    $encabezados = [
        'A7' => 'Alumno',
        'B7' => 'Cuatrimestre',
        'C7' => 'Materia',
        'D7' => 'ID Asesoría',
        'E7' => 'Concepto',
        'F7' => 'Fecha',
        'G7' => 'Hora',
        'H7' => 'Profesor',
        'I7' => 'Calificación P1',
        'J7' => 'Calificación P2',
        'K7' => 'Hora Salida',
        'L7' => 'Recomendaciones'
    ];

    foreach ($encabezados as $cell => $text) {
        $sheet->setCellValue($cell, $text);
    }

    // Aplicar estilo al encabezado (fila 7)
    $sheet->getStyle('A7:L7')->applyFromArray([
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF800080'] // Color morado
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);

    // Aplicar alineación centrada y bordes a todo el texto
    $sheet->getStyle('A1:L' . (7 + count($datos)))->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);

    // Rellenar los datos (comenzando en la fila 8)
    $row = 8;
    foreach ($datos as $dato) {
        $sheet->setCellValue('A' . $row, $dato['nombre_alumno']);
        $sheet->setCellValue('B' . $row, $dato['cuatrimestre']);
        $sheet->setCellValue('C' . $row, $dato['materia']);
        $sheet->setCellValue('D' . $row, $dato['id_asesoria']);
        $sheet->setCellValue('E' . $row, $dato['concepto']);
        $sheet->setCellValue('F' . $row, $dato['fecha']);
        $sheet->setCellValue('G' . $row, $dato['hora']);
        $sheet->setCellValue('H' . $row, $dato['nombre_profesor']);
        $sheet->setCellValue('I' . $row, $dato['calificacionP1']);
        $sheet->setCellValue('J' . $row, $dato['calificacionP2']);
        $sheet->setCellValue('K' . $row, $dato['hora_salida']);
        $sheet->setCellValue('L' . $row, $dato['recomendaciones']);
        $row++;
    }

    // Ajustar el ancho de las columnas
    foreach (range('A', 'L') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Guardar el archivo Excel y enviarlo como descarga
    $writer = new Xlsx($spreadsheet);
    $filename = "reporte_asesorias_{$periodo}_{$anio}.xlsx";

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
                    <li><a href="../ProfesorIndex.php">Inicio</a></li>
                    <li><a href="../../login/logout.php">Cerrar Sesión</a></li>
                    <li><a href="https://www.upemor.edu.mx/">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>

        <div>
            <h2 style="margin-left: 5%;"><a href="citasProfesor.php">< Regresar</a></h2>
            <h1 style="text-align: center;">Formato de control de asesorías.</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p>
            En esta sección puedes generar el formato de control de asesorías, selecciona 
            un periodo, año, y haz clic en "Generar reporte"
            </p>
        </div>

        <div>
            <h2 style="text-align: center;">Generar reporte</h2>
            <p style="text-align: center;">Selecciona el período y el año para generar el reporte.</p>
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
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión de Asesorías. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
