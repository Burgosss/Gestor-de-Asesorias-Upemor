<?php
include '../../Static/connect/db.php';

// Verificar si se enviaron los parámetros necesarios
if (!isset($_GET['id_profesor']) || !isset($_GET['dia'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros faltantes.']);
    exit();
}

$id_profesor = $_GET['id_profesor'];
$dia = $_GET['dia'];

// Convertir la fecha a un formato adecuado para obtener el nombre del día en español
$fecha = date('Y-m-d', strtotime($dia));
$dias = [
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado',
    'Sunday' => 'Domingo'
];
$dia_nombre = $dias[date('l', strtotime($fecha))]; // Convertir el día a español

// Consulta para obtener la disponibilidad del profesor para el día seleccionado
$sql = "
    SELECT hora_inicio, hora_fin 
    FROM disponibilidad 
    WHERE id_profesor = '$id_profesor' AND dia = '$dia_nombre'
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta.']);
    exit();
}

// Construir el resultado
$disponibilidades = [];
while ($row = mysqli_fetch_assoc($result)) {
    $disponibilidades[] = $row;
}

// Devolver el resultado en formato JSON
header('Content-Type: application/json');
echo json_encode($disponibilidades);
?>
