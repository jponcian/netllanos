<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "../conexion.php";
$mysqli = isset($_SESSION['conexionsqli']) ? $_SESSION['conexionsqli'] : null;
if (!$mysqli) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Sin conexión a la base de datos en sesión']);
    exit;
}
if (!isset($_POST['id']) || !isset($_POST['cantidad'])) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Parámetros incompletos']);
    exit;
}
$id = intval($_POST['id']);
$cantidad = intval($_POST['cantidad']);
if ($cantidad < 1) $cantidad = 1;

// usuario seguro
$user = isset($_SESSION['CEDULA_USUARIO']) ? $mysqli->real_escape_string($_SESSION['CEDULA_USUARIO']) : '';

// verificar existencia
$q = "SELECT id_detalle FROM alm_solicitudes_detalle_tmp WHERE id_detalle = " . $id . " AND usuario = '" . $user . "';";
$res = $mysqli->query($q);
if (!$res || $res->num_rows == 0) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Registro no encontrado', 'sql' => $q]);
    exit;
}

$upd = "UPDATE alm_solicitudes_detalle_tmp SET cantidad = '" . $cantidad . "' WHERE id_detalle = " . $id . " AND usuario = '" . $user . "';";
// intentar ejecutar y registrar en log para depuración
$ok = $mysqli->query($upd);
$log = date('Y-m-d H:i:s') . " | user=" . $user . " | id=" . $id . " | cantidad=" . $cantidad . " | sql=" . $upd . " | ok=" . ($ok ? '1' : '0') . " | err=" . ($mysqli->error ? $mysqli->error : '') . PHP_EOL;
@file_put_contents(__DIR__ . '/tmp/update_debug.log', $log, FILE_APPEND);
if ($ok) {
    echo json_encode(['tipo' => 'success', 'msj' => 'Cantidad actualizada']);
} else {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'No se pudo actualizar', 'error' => $mysqli->error, 'sql' => $upd]);
}
