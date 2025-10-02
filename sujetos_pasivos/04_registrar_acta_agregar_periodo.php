<?php
session_start();
include "../conexion.php";
//--------
$info = array();
$tipo = 'info';
//-------------

//PARA GUARDAR
if (trim($_POST['OARTICULO'])<>'' and trim($_POST['OCANTIDAD'])>0)
	{
	//PARA GUARDAR EL DETALLE
	$consulta = "INSERT INTO alm_solicitudes_detalle_tmp ( id_articulo, cantidad, usuario ) SELECT '".(trim($_POST['OARTICULO']))."' AS Expr1, '".($_POST['OCANTIDAD'])."' AS Expr2, '".$_SESSION['CEDULA_USUARIO']."' AS Expr3;";
	$tabla = mysql_query ($consulta);
	// MENSAJE DE GUARDADO
	$mensaje = 'El Articulo fue Agregado Exitosamente!';
	//--------------------
	}
else
	{
	$mensaje = 'Por favor rellene todos los Campos!';
	$tipo = 'alerta';
	//--------------------
	}

$info = array ("msj"=>$mensaje, "tipo"=>$tipo);
echo json_encode($info);
?>