<?php
session_start();
include "../conexion.php";

header('Content-Type: application/json');

// Verificar sesión y conexión
if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. Sesión no válida.']);
    exit();
}
if (!isset($_SESSION['conexionsqli'])) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión con la base de datos.']);
    exit();
}

$conn = $_SESSION['conexionsqli'];

// Validar datos de entrada
$funcionario_ci = isset($_POST['OFUNCIONARIO']) ? intval($_POST['OFUNCIONARIO']) : 0;
$bienes_ids = isset($_POST['bienes']) && is_array($_POST['bienes']) ? $_POST['bienes'] : [];

if ($funcionario_ci <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de funcionario no válido.']);
    exit();
}

if (empty($bienes_ids)) {
    echo json_encode(['status' => 'error', 'message' => 'No se seleccionaron bienes para asignar.']);
    exit();
}

// Preparar la consulta para evitar inyección SQL
$sql = "UPDATE bn_bienes SET inf_ci_asignado = ? WHERE id_bien = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit();
}

$i = 0;
foreach ($bienes_ids as $id_bien) {
    $id_bien_int = intval($id_bien);
    $stmt->bind_param("ii", $funcionario_ci, $id_bien_int);
    if ($stmt->execute()) {
        $i++;
    }
}

$stmt->close();

if ($i > 0) {
    $message = ($i == 1) ? "¡1 bien ha sido asignado exitosamente!" : "¡$i bienes han sido asignados exitosamente!";
    echo json_encode(['status' => 'success', 'message' => $message]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo asignar ningún bien. Verifique los datos.']);
}

?>