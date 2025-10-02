<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
	header('HTTP/1.1 401 Unauthorized');
	exit();
}

if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
	header('HTTP/1.1 500 Internal Server Error');
	echo json_encode(['error' => 'Error de conexión a la base de datos.']);
	exit();
}
$mysqli = $_SESSION['conexionsqli'];

$combo = isset($_GET['combo']) ? $_GET['combo'] : '';
$data = array();

switch ($combo) {
	case 'sede':
		if (isset($_SESSION['ADMINISTRADOR']) && $_SESSION['ADMINISTRADOR'] > 0) {
			$consulta = 'SELECT id_sector as id, nombre FROM z_sectores WHERE id_sector<=5';
		} else {
			$consulta = 'SELECT id_sector as id, nombre FROM z_sectores WHERE id_sector<=5';
		}
		if ($result = $mysqli->query($consulta)) {
			while ($registro = $result->fetch_assoc()) {
				$data[] = array('id' => $registro['id'], 'nombre' => $registro['nombre']);
			}
			$result->free();
		}
		break;

	case 'division':
		$id_sede = isset($_GET['id_sede']) ? intval($_GET['id_sede']) : 0;
		if ($id_sede > 0) {
			$stmt = $mysqli->prepare('SELECT division as id, descripcion as nombre FROM z_jefes_detalle WHERE id_sector=?');
			$stmt->bind_param('i', $id_sede);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($registro = $result->fetch_assoc()) {
				$data[] = array('id' => $registro['id'], 'nombre' => $registro['nombre']);
			}
			$stmt->close();
		}
		break;

	case 'area':
		$id_division = isset($_GET['id_division']) ? intval($_GET['id_division']) : 0;
		if ($id_division > 0) {
			$stmt = $mysqli->prepare('SELECT id_area as id, descripcion as nombre FROM bn_areas WHERE division=? ORDER BY descripcion ASC');
			$stmt->bind_param('i', $id_division);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($registro = $result->fetch_assoc()) {
				$data[] = array('id' => $registro['id'], 'nombre' => $registro['nombre']);
			}
			$stmt->close();
		}
		break;

	case 'categoria':
		$consulta = 'SELECT id_categoria as id, CONCAT(codigo, " ", descripcion) as nombre FROM bn_categorias ORDER BY codigo';
		if ($result = $mysqli->query($consulta)) {
			while ($registro = $result->fetch_assoc()) {
				$data[] = array('id' => $registro['id'], 'nombre' => $registro['nombre']);
			}
			$result->free();
		}
		break;

	case 'formato_categoria':
		$id_categoria = isset($_GET['id_categoria']) ? intval($_GET['id_categoria']) : 0;
		if ($id_categoria > 0) {
			$stmt = $mysqli->prepare('SELECT formato FROM bn_categorias WHERE id_categoria=?');
			$stmt->bind_param('i', $id_categoria);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($registro = $result->fetch_assoc()) {
				$data = array('formato' => $registro['formato']);
			}
			$stmt->close();
		}
		break;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
?>