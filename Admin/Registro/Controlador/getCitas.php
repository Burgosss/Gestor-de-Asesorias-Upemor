<?php
include '../../../Static/connect/db.php';

// Consulta para obtener las asesorías
$sql = "SELECT concepto, fecha, hora FROM asesoria";
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
