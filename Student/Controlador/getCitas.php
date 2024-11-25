<?php
include '../../Static/connect/db.php';

// Validar si se ha enviado el ID del profesor
if (!isset($_GET['id_profesor'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID de profesor no proporcionado."]);
    exit();
}

$id_profesor = $_GET['id_profesor'];

// Consulta para obtener las asesorías con estado "Aprobada" o "Reservada"
$sql = "
    SELECT 
        concepto AS title, 
        CONCAT(fecha, 'T', hora) AS start 
    FROM asesoria 
    WHERE id_profesor = '$id_profesor' AND (estado = 'Aprobada' OR estado = 'Reservada')
";

$result = mysqli_query($conn, $sql);
$events = [];

// Construir el array de eventos
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'title' => $row['title'],
        'start' => $row['start']
    ];
}

// Establecer el encabezado como JSON y devolver los eventos
header('Content-Type: application/json');
echo json_encode($events);
?>
