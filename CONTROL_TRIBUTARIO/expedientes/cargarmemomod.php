<?php
session_start();

	include "../conexion.php";
//include "../auxiliar.php";


	//VARIABLES A UTILIZAR
	$memo = $_POST['memo'];
	$anno = $_POST['anno'];
	$fecha1 = $anno."/01/01";
	$fecha2 = $anno."/12/31";
	$sector = $_POST['sector'];
	$info = array();
	$permitido = false;
	$mensaje = "";

	//BUSCAMOS EL ACTA
	$consulta = "SELECT id,Anno_memo,NroMemo,sector FROM ct_salida_expediente WHERE Anno_memo=$anno AND NroMemo=$memo AND sector=$sector AND FechaEmision BETWEEN '$fecha1' AND '$fecha2'";
	$resultado = $conexionsql->query($consulta);
	$existe = $resultado->num_rows;
	$valor = $resultado->fetch_object();
	
	if ($existe>0)
	{
		$nummemo = $valor->NroMemo;
		$Anno_memo = $valor->Anno_memo;
		$sector = $valor->sector;
		$permitido = true;
		$mensaje = "El Memorando Nro. ".$nummemo." del año ".$anno." ha sido cargado satisfactoriamente";
		$consultaid = "SELECT MAX(id) as ultimoID FROM ct_salida_expediente";
		//echo $consultaid;
		$resultid = $conexionsql->query($consultaid);
		$valorid = $resultid->fetch_object();
		$id = $valorid->ultimoID;
		$_SESSION[VALOR_ID]=$id;	
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