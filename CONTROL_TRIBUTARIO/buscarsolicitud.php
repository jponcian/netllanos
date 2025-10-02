<?php

	//CONECTAR A LA BD
	include "conexion.php";

	//VARIABLES A UTILIZAR
	$numero = $_POST['solicitud'];
	$sector = $_POST['sector'];
	$info = array();
	$mensaje = "";
	$permitido = true;

	//VERIFICAMOS SI HAY DOCUMENTOS A DESTRUIR
	$sqlsolicitud = "SELECT num_solicitud FROM ct_destruccion_facturas WHERE num_solicitud=".$numero." AND sector=".$sector;
	$resultado = $conexionsql->query($sqlsolicitud);
	$existe = $resultado->num_rows;

	if ($existe > 0)
	{
		$mensaje = "!!!...El Numero de Solicitudo ya ha sido registrada, por favor verifique...!!!";
		$permitido = false;
	}
	else
	{
		$mensaje = "";
		$permitido = true;		
	}

	$info = array("permitido"=>$permitido,
					"mensaje"=>$mensaje);

	echo json_encode($info);

?>