<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

header('Content-Type: application/json');

// 1. Verificación de Sesión y Conexión
if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Acceso no autorizado.']);
    exit();
}

if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Error de conexión a la base de datos.']);
    exit();
}
$mysqli = $_SESSION['conexionsqli'];

// 3. Obtener y Validar el ID del bien
$id_bien = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_bien <= 0) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'ID de bien no válido.']);
    exit();
}

// 4. Lógica de Negocio: Verificar si el bien tiene reasignaciones
$stmt_verif = $mysqli->prepare("SELECT id_bien FROM bn_reasignaciones_detalle WHERE id_bien = ?");
$stmt_verif->bind_param('i', $id_bien);
$stmt_verif->execute();
$result_verif = $stmt_verif->get_result();

if ($result_verif->num_rows > 0) {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Este bien posee reasignaciones registradas y no puede ser eliminado.']);
    exit();
}

// 5. Ejecutar la eliminación (marcado como borrado)
$stmt_delete = $mysqli->prepare("UPDATE bn_bienes SET borrado = 1 WHERE id_bien = ?");
$stmt_delete->bind_param('i', $id_bien);

if ($stmt_delete->execute()) {
    echo json_encode(['tipo' => 'exito', 'msj' => 'El Bien Nacional ha sido eliminado exitosamente.']);
} else {
    echo json_encode(['tipo' => 'alerta', 'msj' => 'Error al intentar eliminar el bien.']);
}

$stmt_verif->close();
$stmt_delete->close();
?>