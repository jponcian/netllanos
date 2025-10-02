<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['solicitud']) && $_POST['CMDANULAR'] == "Anular Solicitud") {
	$id_solicitud = intval($_GET['solicitud']);
	$notas = trim($_POST['ONOTAS']);
	$usuario = $_SESSION['CEDULA_USUARIO'] ?? '';
	$consulta = "UPDATE alm_solicitudes SET fecha_anulacion=CURDATE(), status=99, anulador='$usuario', usuario='$usuario', notas_anulacion='$notas' WHERE id_solicitud=$id_solicitud;";
	$_SESSION['conexionsqli']->query($consulta);

	$consultax = "SELECT * FROM vista_alm_detalle_solicitud WHERE id_solicitud=$id_solicitud ORDER BY descripcion;";
	$tablax = $_SESSION['conexionsqli']->query($consultax);
	if ($tablax) {
		while ($registrox = $tablax->fetch_object()) {
			// Si necesitas actualizar detalles, hazlo aquí
		}
	}
	exit;
}
exit;
