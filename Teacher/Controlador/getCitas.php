<?php
include '../../Static/connect/db.php';

// Validar si se ha enviado el ID del profesor
if (!isset($_GET['id_profesor'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID del profesor no proporcionado.']);
    exit();
}

$id_profesor = mysqli_real_escape_string($conn, $_GET['id_profesor']);

// Consulta para obtener las asesorías del profesor con estado "Reservada" o "Aceptada"
$sql = "
    SELECT concepto, fecha, hora 
    FROM asesoria 
    WHERE id_profesor = '$id_profesor' 
    AND (estado = 'Reservada' OR estado = 'Aceptada')
    ORDER BY fecha, hora
";
$result = mysqli_query($conn, $sql);

$events = array();
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = array(
            'title' => $row['concepto'],
            'start' => $row['fecha'] . 'T' . $row['hora'],
        );
    }
}

// Devolver los eventos en formato JSON
header('Content-Type: application/json');
echo json_encode($events);
?>
