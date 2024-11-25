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

    if (isset($_GET['id_usuario'])) {
        $id_usuario=$_GET['id_usuario'];
        $getAlumno="SELECT id_alumno FROM alumno where id_usuario=$id_usuario";
        $getAlumnoQuery = mysqli_query($conn, $getAlumno);
        $rowAlumno = mysqli_fetch_assoc($getAlumnoQuery);

        if (!$rowAlumno) {
            echo "El alumno no existe.";
            exit();
        }

        $id_alumno = $rowAlumno['id_alumno'];
 
        // 1. Verificar si el alumno tiene asesorías con estados no permitidos
        $queryCheckAsesorias = "SELECT estado FROM asesoria WHERE id_alumno = $id_usuario AND estado IN ('Aprobada', 'Reservada')";
        $resultCheck = mysqli_query($conn, $queryCheckAsesorias);

        if (mysqli_num_rows($resultCheck) > 0) {
            header('Location: ../Vista/EstadoAlumnos.php?error=asesorias_pendientes');
            exit();
        }

        // 2. Eliminar mensajes asociados al alumno
        $deleteMessages = "DELETE FROM mensaje WHERE id_alumno = $id_alumno;";
        mysqli_query($conn, $deleteMessages);

        // 3. Eliminar asesorías asociadas al alumno
        $deleteAsesorias = "DELETE FROM asesoria WHERE id_alumno = $id_alumno";
        mysqli_query($conn, $deleteAsesorias);

        $deleteSon="delete from usuario where id_usuario=$id_usuario;";
        $deleteFather="delete from alumno where id_usuario=$id_usuario;";
        if($execute=mysqli_query($conn,$deleteFather)){
            if($exec=mysqli_query($conn,$deleteSon)){
                sleep(2);
                header('Location:../Vista/EstadoAlumnos.php');
            }else{
                echo "Eliminar usuario";
            }
            
        }else{
            echo "ERROR. Eliminar alumno";
        }
    }
?>
<?php 
    }else{
        header("Location: ../../../login/login.html");
    }
?>