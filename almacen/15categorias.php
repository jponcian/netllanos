<?php
session_start();
include "../conexion.php";
header('Content-Type: application/json');
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo json_encode([]);
    exit();
}
$mysqli = $_SESSION['conexionsqli'];
$result = $mysqli->query("SELECT id_categoria, codigo, descripcion FROM bn_categorias WHERE TRIM(descripcion)<>'' ORDER BY descripcion");
$categorias = [];
while ($row = $result->fetch_assoc()) {
    $categorias[] = $row;
}
echo json_encode($categorias);
