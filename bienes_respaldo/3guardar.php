<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

header('Content-Type: application/json');

if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
	echo json_encode(array('tipo' => 'alerta', 'msj' => 'Acceso no autorizado.'));
	exit();
}

$acceso = 74;
include "../validacion_usuario.php";

if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
	echo json_encode(array('tipo' => 'alerta', 'msj' => 'Error de conexión a la base de datos.'));
	exit();
}
$mysqli = $_SESSION['conexionsqli'];

// --- Consultar bien nacional (AJAX para editar) ---
if (isset($_GET['accion']) && $_GET['accion'] === 'consultar' && isset($_GET['id'])) {
	$id_bien = intval($_GET['id']);
	$sql = "SELECT b.id_bien, b.id_area, b.id_categoria, b.numero_bien, b.descripcion_bien, b.conservacion, b.valor, a.division as id_division, d.id_sector as id_sede
			FROM bn_bienes b
			JOIN bn_areas a ON b.id_area = a.id_area
			JOIN z_jefes_detalle d ON a.division = d.division
			WHERE b.id_bien=? AND b.borrado=0";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i', $id_bien);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) {
		echo json_encode($row);
	} else {
		echo json_encode(array());
	}
	exit();
}

// --- Recibir y validar datos ---
$id_area = isset($_POST['OAREA']) ? intval($_POST['OAREA']) : 0;
$id_categoria = isset($_POST['OCATEGORIA']) ? intval($_POST['OCATEGORIA']) : 0;
$numero_bien = isset($_POST['OBIEN']) ? trim(mayuscula($_POST['OBIEN'])) : '';
$descripcion = isset($_POST['ODESCRIPCION']) ? trim(mayuscula($_POST['ODESCRIPCION'])) : '';
$conservacion = isset($_POST['OCONSERVACION']) ? trim(mayuscula($_POST['OCONSERVACION'])) : '';
$valor = isset($_POST['OVALOR']) ? floatval($_POST['OVALOR']) : 0;
$usuario = $_SESSION['CEDULA_USUARIO'];
$id_bien = isset($_POST['ID_BIEN']) ? intval($_POST['ID_BIEN']) : 0;

// Si no hay datos de formulario, no hacer nada
if (!$id_area && !$id_categoria && !$numero_bien && !$descripcion && !$conservacion && !$valor && !$id_bien) {
	exit();
}

// --- Validaciones del servidor ---
if ($id_area == 0 || $id_categoria == 0 || empty($numero_bien) || empty($descripcion) || empty($conservacion) || $valor < 0) {
	echo json_encode(array('tipo' => 'alerta', 'msj' => 'Todos los campos son obligatorios.'));
	exit();
}

// --- Verificar si el número de bien ya existe (excepto si es edición y el mismo bien) ---
$stmt = $mysqli->prepare("SELECT id_bien FROM bn_bienes WHERE numero_bien=? AND borrado=0" . ($id_bien ? " AND id_bien<>?" : ""));
if ($id_bien) {
	$stmt->bind_param('si', $numero_bien, $id_bien);
} else {
	$stmt->bind_param('s', $numero_bien);
}
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	echo json_encode(array('tipo' => 'alerta', 'msj' => 'Ya existe un bien registrado con ese número.'));
	$stmt->close();
	exit();
}
$stmt->close();

if ($id_bien) {
	// --- Actualizar bien existente ---
	$stmt = $mysqli->prepare("UPDATE bn_bienes SET id_area=?, id_categoria=?, numero_bien=?, descripcion_bien=?, conservacion=?, valor=?, usuario=? WHERE id_bien=?");
	$stmt->bind_param('iisssdsi', $id_area, $id_categoria, $numero_bien, $descripcion, $conservacion, $valor, $usuario, $id_bien);
	if ($stmt->execute()) {
		echo json_encode(array('tipo' => 'exito', 'msj' => 'Bien Nacional actualizado exitosamente.'));
	} else {
		echo json_encode(array('tipo' => 'alerta', 'msj' => 'Error al actualizar en la base de datos: ' . $stmt->error));
	}
	$stmt->close();
} else {
	// --- Insertar en la base de datos ---
	$stmt = $mysqli->prepare("INSERT INTO bn_bienes (id_area, id_categoria, numero_bien, descripcion_bien, conservacion, valor, usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param('iisssds', $id_area, $id_categoria, $numero_bien, $descripcion, $conservacion, $valor, $usuario);
	if ($stmt->execute()) {
		echo json_encode(array('tipo' => 'exito', 'msj' => 'Bien Nacional registrado exitosamente.'));
	} else {
		echo json_encode(array('tipo' => 'alerta', 'msj' => 'Error al guardar en la base de datos: ' . $stmt->error));
	}
	$stmt->close();
}
?>