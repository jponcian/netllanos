<?php
session_start();
include "../conexion.php";
header('Content-Type: application/json');
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Error de conexión a la base de datos.']);
    exit();
}
$mysqli = $_SESSION['conexionsqli'];

// Consultar área para edición
if (isset($_GET['accion']) && $_GET['accion'] === 'consultar' && isset($_GET['id'])) {
    $id_area = intval($_GET['id']);
    $sql = "SELECT a.id_area, a.descripcion, a.division, d.id_sector FROM bn_areas a JOIN z_jefes_detalle d ON a.division = d.division WHERE a.id_area=? AND a.borrado=0";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id_area);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(array());
    }
    exit();
}

$id_area = isset($_POST['ID_AREA']) ? intval($_POST['ID_AREA']) : 0;
$descripcion = isset($_POST['OAREA']) ? trim($_POST['OAREA']) : '';
$division = isset($_POST['ODIVISION']) ? intval($_POST['ODIVISION']) : 0;
$usuario = $_SESSION['CEDULA_USUARIO'];

if (!$descripcion || !$division) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Todos los campos son obligatorios.']);
    exit();
}

if ($id_area) {
    // Actualizar
    $stmt = $mysqli->prepare("UPDATE bn_areas SET descripcion=?, division=?, usuario=? WHERE id_area=?");
    $stmt->bind_param('sisi', $descripcion, $division, $usuario, $id_area);
    if ($stmt->execute()) {
        echo json_encode(['tipo' => 'exito', 'msj' => 'Área actualizada exitosamente.']);
    } else {
        echo json_encode(['tipo' => 'alerta', 'msj' => 'Error al actualizar: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    // Insertar
    $stmt = $mysqli->prepare("INSERT INTO bn_areas (descripcion, division, usuario) VALUES (?, ?, ?)");
    $stmt->bind_param('sis', $descripcion, $division, $usuario);
    if ($stmt->execute()) {
        echo json_encode(['tipo' => 'exito', 'msj' => 'Área registrada exitosamente.']);
    } else {
        echo json_encode(['tipo' => 'alerta', 'msj' => 'Error al guardar: ' . $stmt->error]);
    }
    $stmt->close();
}
