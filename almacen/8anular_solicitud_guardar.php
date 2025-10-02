<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['solicitud']) && isset($_POST['btn-anular-solicitud']) && $_POST['btn-anular-solicitud'] == "Anular Solicitud") {
	$id_solicitud = intval($_GET['solicitud']);
	$notas = trim($_POST['ONOTAS']);
	$usuario = isset($_SESSION['CEDULA_USUARIO']) ? $_SESSION['CEDULA_USUARIO'] : '';
	$consulta = "UPDATE alm_solicitudes SET fecha_anulacion=CURDATE(), status=99, anulador='$usuario', usuario='$usuario', notas_anulacion='$notas' WHERE id_solicitud=$id_solicitud;";
	$_SESSION['conexionsqli']->query($consulta);
	echo 'Procesada Exitosamente';
	exit;
}
exit;
