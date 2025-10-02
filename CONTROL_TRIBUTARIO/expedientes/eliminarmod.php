<?php

	include "../conexion.php";
//include "../auxiliar.php";


	$id = $_POST['id'];
	$json  = array();
	$mensaje = "El registro no pudo eliminarse";
	$proceso = false;

	$sqldelete = "UPDATE ct_tmp_mod_salida_expediente SET borrado=1 WHERE id=$id";
	$resultado = $conexionsql->query($sqldelete);

	if ($conexionsql->affected_rows > 0)
	{
		$proceso = true;
		$mensaje = "Registro eliminado satisfactoriamente";
	} else {
		$proceso = false;
		$mensaje = "El registro no pudo eliminarse";
	}

	$json = array(
			'proceso' => $proceso,
			'mensaje' => $mensaje,
		);

	echo json_encode($json);


?>