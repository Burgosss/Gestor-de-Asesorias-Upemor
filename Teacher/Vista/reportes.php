<?php
session_start();
require '../../vendor/autoload.php'; // Incluye PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
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

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configuración de encabezados
    $encabezados = [
        'A1' => 'Alumno',
        'B1' => 'Cuatrimestre',
        'C1' => 'Materia',
        'D1' => 'ID Asesoría',
        'E1' => 'Concepto',
        'F1' => 'Fecha',
        'G1' => 'Hora',
        'H1' => 'Profesor',
        'I1' => 'Calificación P1',
        'J1' => 'Calificación P2',
        'K1' => 'Hora Salida',
        'L1' => 'Recomendaciones'
    ];

    foreach ($encabezados as $cell => $text) {
        $sheet->setCellValue($cell, $text);
    }

    // Estilo para el encabezado
    $sheet->getStyle('A1:L1')->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['argb' => Color::COLOR_WHITE],
            'size' => 12
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF4CAF50']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000']
            ]
        ]
    ]);

    // Rellenar los datos
    $row = 2;
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
            <h2 style="margin-left: 5%;"><a href="agregarObservaciones.php">< Regresar</a></h2>
            <h1 style="text-align: center;">Generar reportes</h1>
        </div>

        <div class="recuadro_indicaciones">
            <p>
            En esta sección puedes generar reportes fácilmente. 
            Selecciona el reporte deseado y haz clic en "Generar reporte" para obtenerlo.
            </p>
        </div>

        <div>
            <h2 style="text-align: center;">Generar Reporte de Asesorías</h2>
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
