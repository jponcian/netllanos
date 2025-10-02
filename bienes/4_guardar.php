<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

header('Content-Type: application/json');

$id_bien = isset($_POST['id_bien']) ? intval($_POST['id_bien']) : 0;

$usuario = $_SESSION['CEDULA_USUARIO'];
$id_area_destino = isset($_POST['OAREA2']) ? intval($_POST['OAREA2']) : 0;

// Seguridad: si el usuario NO es administrador y su división no es 9, forzamos el destino a área 17
if (!(isset($_SESSION['ADMINISTRADOR']) && $_SESSION['ADMINISTRADOR'] > 0) && !(isset($_SESSION['DIVISION_USUARIO']) && intval($_SESSION['DIVISION_USUARIO']) === 9)) {
    $id_area_destino = 17;
}

if ($id_bien <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'ID de bien inválido']);
    exit();
}

// Marcar bien como pendiente por reasignar (puedes ajustar el campo/tabla según tu modelo)
$sql = "UPDATE bn_bienes SET por_reasignar = '1', usuario = '$usuario' WHERE id_bien = $id_bien";
$res = mysqli_query($_SESSION['conexionsqli'], $sql);

if ($res) {
    // Insertar en bn_bienes_x_reasignar si no existe
    $sql_check = "SELECT 1 FROM bn_bienes_x_reasignar WHERE id_bien = $id_bien LIMIT 1";
    $existe = mysqli_query($_SESSION['conexionsqli'], $sql_check);
    if (!mysqli_fetch_row($existe)) {
        $sql_insert = "INSERT INTO bn_bienes_x_reasignar (id_bien, id_area_destino, usuario, fecha) VALUES ($id_bien, $id_area_destino, '$usuario', NOW())";
        mysqli_query($_SESSION['conexionsqli'], $sql_insert);
    }
    echo json_encode(['status' => 'ok', 'msg' => 'Bien marcado como pendiente por reasignar']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar el bien']);
}
