<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//--------
$info = array();
$tipo = 'info';
//-------------

//PARA GUARDAR
// Usar solo MySQLi
$mysqli = $_SESSION['conexionsqli'];
$consulta = "SELECT id_articulo FROM alm_solicitudes_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
$tablax = $mysqli->query($consulta);
if ($registrox = $tablax->fetch_assoc()) {
	//--- DIVISION DEL FUNCIONARIO
	$division = $_SESSION['DIVISION_USUARIO'];

	// PARA BUSCAR LA ULTIMA SOLICITUD
	$consultax = 'SELECT max(solicitud) as numero FROM alm_solicitudes WHERE year(fecha)=year(date(now())) and status<>99;';

	$tablax2 = $mysqli->query($consultax);
	$registrox2 = $tablax2->fetch_object();
	$numero = ($registrox2 && $registrox2->numero) ? $registrox2->numero + 1 : 1;

	//PARA GUARDAR LA SOLICITUD
	$consulta = "INSERT INTO alm_solicitudes ( solicitud, fecha, division, funcionario, status, usuario ) SELECT '0" . $numero . "' as num, date(now()) AS Expr1, '" . $division . "' AS Expr2, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr3, '0' AS Expr4, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr5;";
	$mysqli->query($consulta);

	// PARA BUSCAR LA ULTIMA SOLICITUD
	$consultax = "SELECT id_solicitud FROM alm_solicitudes WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . " ORDER BY id_solicitud DESC;";
	$tablax3 = $mysqli->query($consultax);
	$registrox3 = $tablax3->fetch_object();
	$solicitud = $registrox3->id_solicitud;

	// PARA GUARDAR LOS ARTICULOS
	$consultay = "SELECT * FROM alm_solicitudes_detalle_tmp WHERE usuario= '" . $_SESSION['CEDULA_USUARIO'] . "';";
	$tablay = $mysqli->query($consultay);
	while ($registroy = $tablay->fetch_object()) {
		// PARA AGREGAR
		$consultai = "INSERT INTO alm_solicitudes_detalle (id_solicitud, id_articulo, cant_solicitada, usuario) VALUES ('" . $solicitud . "', '" . $registroy->id_articulo . "', '" . $registroy->cantidad . "', '" . $_SESSION['CEDULA_USUARIO'] . "');";
		$mysqli->query($consultai);
	}

	// PARA ELIMINAR EL TEMPORAL
	$consultad = "DELETE FROM alm_solicitudes_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
	$mysqli->query($consultad);

	//--------------------
	//$_SESSION['VARIABLE'] = $solicitud;
	$_SESSION['BOTON'] = '<a href="../almacen/formatos/x_solicitud.php?solicitud=' . $solicitud . '" target="_blank">Ver la Solicitud</a>';
	// MENSAJE DE GUARDADO
	$_SESSION['MOSTRAR'] = 'SI';
	$_SESSION['MENSAJE'] = 'La Solicitud fue Registrada Exitosamente!';
	//--------------------
	// Preparar respuesta JSON con URL del PDF para mostrar en línea
	$info = array();
	$info['tipo'] = 'success';
	$info['msj'] = 'La Solicitud fue Registrada Exitosamente!';
	$info['solicitud'] = $solicitud;
	$info['pdf'] = '../almacen/formatos/x_solicitud.php?solicitud=' . $solicitud;
	echo json_encode($info);
	exit();
} else {
	$mensaje = 'No hay Articulos Registrados!';
	$tipo = 'alerta';
	//--------------------
}

$info = array("msj" => $mensaje, "tipo" => $tipo);
echo json_encode($info);
