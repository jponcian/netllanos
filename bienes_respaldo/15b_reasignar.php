<?php
session_start();
include_once "../conexion.php";
include_once "../auxiliar.php";
//----------------
$info = array();
$id = ($_POST['id']);
$dir = ($_GET['dir']);
$tipo = 'info';
//-------
$consultx = "SELECT descripcion_bien, bn_bienes.id_area, descripcion, revisado FROM bn_bienes, bn_areas WHERE bn_bienes.id_area=bn_areas.id_area AND numero_bien = '$id';";
$tablx = $_SESSION['conexionsqli']->query($consultx);
//--------
if ($tablx->num_rows > 0) {
	$registro = $tablx->fetch_object();
	$mensaje = $registro->descripcion_bien;
	if ($dir <> $registro->id_area) {
		$mensaje = 'El Bien pertenece a: ' . $registro->descripcion;
		$tipo = 'alerta';
	}
	if ($registro->revisado == 1) {
		$mensaje = 'El Bien ya ha sido Procesado!';
		$tipo = 'alerta';
	}
} else {
	$mensaje = 'No existe el Numero de Bien!';
	$tipo = 'alerta';
}
//----------
$consultx = "UPDATE bn_bienes SET revisado = 1 WHERE numero_bien = '$id';";  //echo $consultx;
$tablx = $_SESSION['conexionsqli']->query($consultx);
//----------
$info = array("tipo" => $tipo, "msg" => $mensaje);
echo json_encode($info);
?>