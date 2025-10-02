<?php
session_start();
include "../conexion.php";
header('Content-Type: application/json');
$mysqli = $_SESSION['conexionsqli'];
$combo = isset($_GET['combo']) ? $_GET['combo'] : '';

if ($combo === 'division' && isset($_GET['id_sector'])) {
    $id_sector = intval($_GET['id_sector']);
    $stmt = $mysqli->prepare("SELECT division, descripcion FROM z_jefes_detalle WHERE id_sector=? ORDER BY descripcion");
    $stmt->bind_param("i", $id_sector);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = ['id' => $row['division'], 'nombre' => $row['descripcion']];
    }
    echo json_encode($data);
    exit();
}

echo json_encode([]);