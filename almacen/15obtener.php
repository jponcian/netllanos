<?php
session_start();
include "../conexion.php";
header('Content-Type: application/json');
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo json_encode(null);
    exit();
}
$mysqli = $_SESSION['conexionsqli'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(null);
    exit();
}
$stmt = $mysqli->prepare("SELECT id_articulo, id_categoria, descripcion, unidad, cantidad, precio FROM alm_inventario WHERE id_articulo = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$articulo = $result->fetch_assoc();
echo json_encode($articulo);
