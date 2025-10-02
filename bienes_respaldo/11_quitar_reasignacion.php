<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

header('Content-Type: application/json');

$id_bien = isset($_POST['id_bien']) ? intval($_POST['id_bien']) : 0;

if ($id_bien <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'ID de bien inválido']);
    exit();
}

// Eliminar de bn_bienes_x_reasignar
$sql_del = "DELETE FROM bn_bienes_x_reasignar WHERE id_bien = $id_bien";
$res_del = mysqli_query($_SESSION['conexionsqli'], $sql_del);

// Poner por_reasignar=0 en bn_bienes
$sql_upd = "UPDATE bn_bienes SET por_reasignar = 0 WHERE id_bien = $id_bien";
$res_upd = mysqli_query($_SESSION['conexionsqli'], $sql_upd);

if ($res_del && $res_upd) {
    echo json_encode(['status' => 'ok', 'msg' => 'Bien removido de la lista de reasignados']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'No se pudo quitar el bien']);
}
