<?php
session_start();
include "../conexion.php";
header('Content-Type: application/json');
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Error de conexión a la base de datos.']);
    exit();
}
$mysqli = $_SESSION['conexionsqli'];
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'ID inválido.']);
    exit();
}
$stmt = $mysqli->prepare("UPDATE bn_areas SET borrado=1 WHERE id_area=?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['tipo' => 'exito', 'msj' => 'Área eliminada exitosamente.']);
} else {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Error al eliminar: ' . $stmt->error]);
}
$stmt->close();
