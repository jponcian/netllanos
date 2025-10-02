<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//--------
$info = array();
$tipo = 'info';
//-------------

//PARA GUARDAR
$consulta = "SELECT id_articulo FROM alm_solicitudes_detalle_tmp WHERE usuario = ".$_SESSION['CEDULA_USUARIO'].";";
$tablax = mysql_query($consulta);
if ($registrox = mysql_fetch_array($tablax))
	{
	//--- DIVISION DEL FUNCIONARIO
	$division = $_SESSION['DIVISION_USUARIO'];
	
	// PARA BUSCAR LA ULTIMA SOLICITUD
	$consultax = 'SELECT max(solicitud) as numero FROM alm_solicitudes WHERE year(fecha)=year(date(now())) and status<>99;';
	$tablax = mysql_query ($consultax);
	if ($registrox = mysql_fetch_object($tablax))
		{	$numero = $registrox->numero+1;	}
	else
		{	$numero = 1;	}

	//PARA GUARDAR LA SOLICITUD
	$consulta = "INSERT INTO alm_solicitudes ( solicitud, fecha, division, funcionario, status, usuario ) SELECT '0".$numero."' as num, date(now()) AS Expr1, '".$division."' AS Expr2, '".$_SESSION['CEDULA_USUARIO']."' AS Expr3, '0' AS Expr4, '".$_SESSION['CEDULA_USUARIO']."' AS Expr5;";
	$tabla = mysql_query ($consulta);echo $consulta;
	
	// PARA BUSCAR LA ULTIMA SOLICITUD
	$consultax = "SELECT id_solicitud FROM alm_solicitudes WHERE usuario = ".$_SESSION['CEDULA_USUARIO']." ORDER BY id_solicitud DESC;";
	$tablax = mysql_query ($consultax);
	$registrox = mysql_fetch_object($tablax);
	$solicitud = $registrox->id_solicitud;
	
	// PARA GUARDAR LOS ARTICULOS
	$consultay = "SELECT * FROM alm_solicitudes_detalle_tmp WHERE usuario= '".$_SESSION['CEDULA_USUARIO']."';";
	$tablay = mysql_query ($consultay);
	while ($registroy = mysql_fetch_object($tablay))
		{		
		// PARA AGREGAR
		$consultai = "INSERT INTO alm_solicitudes_detalle (id_solicitud, id_articulo, cant_solicitada, usuario) VALUES ('".$solicitud."', '".$registroy->id_articulo."', '".$registroy->cantidad."', '".$_SESSION['CEDULA_USUARIO']."');";
		$tablai = mysql_query ($consultai);
		} 
				
	// PARA ELIMINAR EL TEMPORAL
	$consultad = "DELETE FROM alm_solicitudes_detalle_tmp WHERE usuario = ".$_SESSION['CEDULA_USUARIO'].";"; 
	$tablad = mysql_query ($consultad);
			
	//--------------------
	//$_SESSION['VARIABLE'] = $solicitud;
	$_SESSION['BOTON'] = '<a href="../almacen/formatos/x_solicitud.php?solicitud='.$solicitud.'" target="_blank">Ver la Solicitud</a>';
	// MENSAJE DE GUARDADO
	$_SESSION['MOSTRAR'] = 'SI';
	$_SESSION['MENSAJE'] = 'La Solicitud fue Registrada Exitosamente!';
	//--------------------
	// REDIRECCION
	header("Location: menuprincipal.php");
	exit();
	}
else
	{
	$mensaje = 'No hay Articulos Registrados!';
	$tipo = 'alerta';
	//--------------------
	}
	
$info = array ("msj"=>$mensaje, "tipo"=>$tipo);
echo json_encode($info);
?>