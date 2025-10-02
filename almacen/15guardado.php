<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$info = array();
$tipo = 'info';

// Validar conexión mysqli
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    $info = array("msj" => "Error de conexión a la base de datos.", "tipo" => "alerta");
    echo json_encode($info);
    exit();
}
$mysqli = $_SESSION['conexionsqli'];

// Sanitizar y validar entradas
$id_categoria = isset($_POST['OCAT']) ? intval($_POST['OCAT']) : 0;
$descripcion = isset($_POST['ODESCRIPCION']) ? trim($_POST['ODESCRIPCION']) : '';
$unidad = isset($_POST['OTIPO']) ? trim($_POST['OTIPO']) : '';
$cantidad = isset($_POST['OCANTIDAD']) ? floatval($_POST['OCANTIDAD']) : 0;
$precio = isset($_POST['OPRECIO']) ? floatval($_POST['OPRECIO']) : 0;
$usuario = isset($_SESSION['CEDULA_USUARIO']) ? $_SESSION['CEDULA_USUARIO'] : '';

if ($id_categoria <= 0 || $descripcion == '' || $unidad == '' || $usuario == '') {
    $info = array("msj" => "Datos incompletos o inválidos.", "tipo" => "alerta");
    echo json_encode($info);
    exit();
}

// Preparar consulta segura
$stmt = $mysqli->prepare("INSERT INTO alm_inventario (id_categoria, tipo, descripcion, unidad, cantidad, precio, usuario) VALUES (?, '2', ?, ?, ?, ?, ?)");
if (!$stmt) {
    $info = array("msj" => "Error en la preparación de la consulta.", "tipo" => "alerta");
    echo json_encode($info);
    exit();
}
$stmt->bind_param("issdds", $id_categoria, $descripcion, $unidad, $cantidad, $precio, $usuario);

if ($stmt->execute()) {
    $mensaje = 'Registro Agregado Exitosamente';
    $tipo = 'info';
} else {
    $mensaje = 'Error al agregar el registro: ' . $stmt->error;
    $tipo = 'alerta';
}
$stmt->close();

$info = array("msj" => $mensaje, "tipo" => $tipo);
echo json_encode($info);
