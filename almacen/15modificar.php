<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
header('Content-Type: application/json');

// Respuesta base
$info = array();
$tipo = 'info';

// Validar conexión mysqli
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo json_encode(["msj" => "Error de conexión a la base de datos.", "tipo" => "alerta"]);
    exit();
}
$mysqli = $_SESSION['conexionsqli'];

// Tomar entradas
$id = isset($_POST['ID_ARTICULO']) ? intval($_POST['ID_ARTICULO']) : 0;
$id_categoria = isset($_POST['OCAT']) ? intval($_POST['OCAT']) : 0;
$descripcion = isset($_POST['ODESCRIPCION']) ? trim($_POST['ODESCRIPCION']) : '';
$unidad = isset($_POST['OTIPO']) ? trim($_POST['OTIPO']) : '';
$cantidad = isset($_POST['OCANTIDAD']) ? floatval($_POST['OCANTIDAD']) : 0;
$precio = isset($_POST['OPRECIO']) ? floatval($_POST['OPRECIO']) : 0;
$usuario = isset($_SESSION['CEDULA_USUARIO']) ? $_SESSION['CEDULA_USUARIO'] : '';

// Validaciones mínimas
if ($id <= 0) {
    echo json_encode(["msj" => "ID de artículo inválido.", "tipo" => "alerta"]);
    exit();
}
if ($id_categoria <= 0 || $descripcion === '' || $unidad === '' || $usuario === '') {
    echo json_encode(["msj" => "Datos incompletos o inválidos.", "tipo" => "alerta"]);
    exit();
}

// Actualizar con prepared statement
$sql = "UPDATE alm_inventario 
		SET id_categoria = ?, descripcion = ?, unidad = ?, cantidad = ?, precio = ?, usuario = ?
		WHERE id_articulo = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(["msj" => "Error preparando la consulta.", "tipo" => "alerta"]);
    exit();
}
$stmt->bind_param("issddsi", $id_categoria, $descripcion, $unidad, $cantidad, $precio, $usuario, $id);

if ($stmt->execute()) {
    $mensaje = 'Registro Modificado Exitosamente';
    $tipo = 'info';
} else {
    $mensaje = 'Error al modificar el registro: ' . $stmt->error;
    $tipo = 'alerta';
}
$stmt->close();

echo json_encode(["msj" => $mensaje, "tipo" => $tipo]);
