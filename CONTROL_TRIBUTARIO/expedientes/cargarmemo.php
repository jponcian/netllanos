<?php

	include "../conexion.php";
//include "../auxiliar.php";


	//VARIABLES A UTILIZAR
	$memo = $_POST['memo'];
	$anno = $_POST['anno'];
	$sector = $_POST['sector'];
	$fecha1 = $anno."/01/01";
	$fecha2 = $anno."/12/31";
	$info = array();
	$permitido = false;
	$mensaje = "";

	//BUSCAMOS EL ACTA
	$consulta = "SELECT id,Anno_memo,NroMemo FROM ct_salida_expediente WHERE Anno_memo=$anno AND NroMemo=$memo AND sector=$sector AND FechaEmision BETWEEN '$fecha1' AND '$fecha2'";
	$resultado = $conexionsql->query($consulta);
	$existe = $resultado->num_rows;
	$valor = $resultado->fetch_object();
	
	if ($existe>0)
	{
		$id = 1;
		$nummemo = $valor->NroMemo;
		$Anno_memo = $valor->Anno_memo;
		$permitido = true;
		$mensaje = "El Memorando Nro. ".$nummemo." del año ".$anno." ha sido cargado satisfactoriamente";
	}
	else
	{
		$id = 0;
		$permitido = false;
		$mensaje = "El Numero de Memorando no se encuentra registrado";
		$cargo = "";	
	}

	$info = array("permitido"=>$permitido,
					"mensaje"=>$mensaje,
					"ID"=>$id);

	echo json_encode($info);

?>