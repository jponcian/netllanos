<?php
session_start();
include "../conexion.php";

if (isset($_POST['id'])) {
    $id_detalle = $_POST['id'];
    $usuario = $_SESSION['CEDULA_USUARIO'];

    $consulta = "DELETE FROM alm_ingresos_detalle_tmp WHERE id_detalle = ? AND usuario = ?";
    
    $mysqli = $_SESSION['conexionsqli'];
    if ($stmt = $mysqli->prepare($consulta)) {
        $stmt->bind_param("ii", $id_detalle, $usuario);
        $stmt->execute();
        $stmt->close();
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
