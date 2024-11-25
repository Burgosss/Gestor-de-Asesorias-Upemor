<?php
// Archivo de consultas SQL

function obtenerIdProfesor($conn, $id_usuario) {
    $sql = "SELECT id_profesor FROM profesor WHERE id_usuario = '$id_usuario'";
    return mysqli_query($conn, $sql);
}

function actualizarEstadoAsesoria($conn, $id_asesoria, $accion, $id_profesor) {
    $sql = "UPDATE asesoria SET estado = '$accion' WHERE id_asesoria = '$id_asesoria' AND id_profesor = '$id_profesor'";
    return mysqli_query($conn, $sql);
}

function obtenerAsesoriasPendientes($conn, $id_profesor) {
    $sql = "
        SELECT 
            a.id_asesoria, a.concepto, a.fecha, a.hora, a.estado, 
            u.nombre AS alumno_nombre, u.apellido AS alumno_apellido, 
            m.nombre AS materia
        FROM asesoria a
        JOIN alumno al ON a.id_alumno = al.id_alumno
        JOIN usuario u ON al.id_usuario = u.id_usuario
        JOIN materia m ON a.id_materia = m.id_materia
        WHERE a.id_profesor = '$id_profesor' AND a.estado = 'Reservada'
    ";
    return mysqli_query($conn, $sql);
}
?>
