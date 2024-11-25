<?php
session_start();
include '../../../Static/connect/db.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../../login/login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];  


$sqlAdmin = "SELECT * FROM admin WHERE id_usuario = '$id_usuario'";
$resultAdmin = mysqli_query($conn, $sqlAdmin);

if (mysqli_num_rows($resultAdmin) == 1) {
?>  
<?php
if (isset($_POST['update'])) {
    $id_usuario = $_GET['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $fec_nac = $_POST['fec_nac'];
    $materiasSeleccionadas = isset($_POST['materias']) ? $_POST['materias'] : []; // Si no hay materias, asignar un arreglo vacío

    // Actualizar datos del usuario
    $query = "UPDATE usuario SET nombre = '$nombre', apellido = '$apellido', genero = '$genero', fec_nac = '$fec_nac' WHERE id_usuario = $id_usuario";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Obtener ID del profesor
        $sqlProfesor = "SELECT id_profesor FROM profesor WHERE id_usuario = $id_usuario";
        $resultProfesor = mysqli_query($conn, $sqlProfesor);
        $profesor = mysqli_fetch_assoc($resultProfesor);
        $id_profesor = $profesor['id_profesor'];

        // Obtener materias actuales del profesor
        $sqlMateriasActuales = "SELECT id_materia FROM profesor_materia WHERE id_profesor = $id_profesor";
        $resultMateriasActuales = mysqli_query($conn, $sqlMateriasActuales);
        $materiasActuales = [];
        while ($row = mysqli_fetch_assoc($resultMateriasActuales)) {
            $materiasActuales[] = $row['id_materia'];
        }

        // Detectar materias a eliminar
        $materiasAEliminar = array_diff($materiasActuales, $materiasSeleccionadas);

        foreach ($materiasAEliminar as $id_materia) {
            // Verificar si la materia tiene asesorías pendientes
            $queryCheckAsesorias = "
                SELECT 1
                FROM asesoria
                WHERE id_materia = $id_materia
                AND id_profesor = $id_profesor
                AND estado IN ('Aprobada', 'Reservada')
                LIMIT 1
            ";
            $resultCheck = mysqli_query($conn, $queryCheckAsesorias);

            if (mysqli_num_rows($resultCheck) > 0) {
                // Si tiene asesorías pendientes, redirigir con error
                header("Location: ../Vista/EstadoProfesores.php?error=asesorias_pendientes");
                exit();
            }

            // Eliminar asesorías asociadas a la materia (si no tiene pendientes)
            $sqlEliminarAsesorias = "DELETE FROM asesoria WHERE id_materia = $id_materia AND id_profesor = $id_profesor";
            mysqli_query($conn, $sqlEliminarAsesorias);

            // Eliminar relación en profesor_materia
            $sqlEliminarRelacion = "DELETE FROM profesor_materia WHERE id_materia = $id_materia AND id_profesor = $id_profesor";
            mysqli_query($conn, $sqlEliminarRelacion);
        }

        // Detectar materias a agregar
        $materiasAAgregar = array_diff($materiasSeleccionadas, $materiasActuales);
        foreach ($materiasAAgregar as $id_materia) {
            $sqlAgregarRelacion = "INSERT INTO profesor_materia (id_profesor, id_materia) VALUES ($id_profesor, $id_materia)";
            mysqli_query($conn, $sqlAgregarRelacion);
        }

        // Redirigir al perfil del profesor con éxito
        header("Location: ../Vista/EstadoProfesores.php");
    } else {
        echo "Error al actualizar los datos.";
    }
}
?>
<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>