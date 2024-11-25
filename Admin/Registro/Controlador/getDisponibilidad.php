<?php
// Conexión a la base de datos
include '../../../Static/connect/db.php';

// Obtener el ID del profesor desde la URL (en el calendario)
$id_profesor = $_GET['id_profesor']; 

// Consulta para obtener la disponibilidad del profesor
$sql = "SELECT dia, hora_inicio, hora_fin FROM disponibilidad WHERE id_profesor = '$id_profesor'";

// Ejecutar la consulta
$result = mysqli_query($conn, $sql);

// Array para almacenar los eventos (horarios disponibles)
$events = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = [
            'title' => 'Disponible', // Título del evento
            'start' => $row['dia'] . 'T' . $row['hora_inicio'], // Fecha y hora de inicio
            'end' => $row['dia'] . 'T' . $row['hora_fin'], // Fecha y hora de fin
            'color' => '#28a745' // Color del evento (verde para indicar disponibilidad)
        ];
    }
}

// Devolver la respuesta en formato JSON
echo json_encode($events);
?>
