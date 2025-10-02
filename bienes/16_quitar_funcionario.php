<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != 'SI') {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

$id_bien = isset($_POST['id_bien']) ? intval($_POST['id_bien']) : 0;
if ($id_bien <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    exit();
}

// Usar la conexión mysqli guardada en sesión si existe
if (!isset($_SESSION['conexionsqli'])) {
    echo json_encode(['status' => 'error', 'message' => 'Conexión no disponible']);
    exit();
}
$conn = $_SESSION['conexionsqli'];

try {
    $sql = "UPDATE bn_bienes SET inf_ci_asignado = 0 WHERE id_bien = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_bien);
    $ok = $stmt->execute();
    if ($ok) {
        echo json_encode(['status' => 'success', 'message' => 'Funcionario eliminado del bien.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el registro.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Excepción: ' . $e->getMessage()]);
}

?>