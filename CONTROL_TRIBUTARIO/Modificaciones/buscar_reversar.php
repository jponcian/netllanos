<?php

	error_reporting(0);
	include("../conexion.php");

	//VARIABLES A UTILIZAR
	$sector = $_POST['sector'];
	$anno = $_POST['anno'];
	$numero = $_POST['numero'];
	$info=array();
	//$numero = date("Y-m-d");

	$registros = "SELECT expedientes_fiscalizacion.status as estatus FROM expedientes_fiscalizacion WHERE expedientes_fiscalizacion.sector = ".$sector."  AND expedientes_fiscalizacion.anno = ".$anno."  AND expedientes_fiscalizacion.numero = ".$numero;
    $resultregistros = $conexionsql->query($registros);
    $valor = $resultregistros->fetch_object(); 
    $estatus = $valor->estatus;

	$info = array("estatus"=>$estatus);

	echo json_encode($info);

function buscar($sector,$anno,$numero)
	{
	$fecha="";
	//----------
	$consulta_y = "SELECT fecha_notificacion FROM expedientes_fiscalizacion WHERE sector=".$sector." AND anno=".$anno." AND numero=".$numero;
	$tabla_y = $conexionsql->query($consulta_y);
	$registro_y = $tabla_y->fetch_object();
	$fecha = $registro_y->fecha_notificacion;
	//----------
	echo $fecha;
	}

function voltea_fecha($a)
	{
	if ($a=='') 
		{
		if (substr($a,2,1)=='-' or substr($a,2,1)=='/')	{ return '00/00/0000'; }
		else 
			{ 
			if (substr($a,4,1)=='-' or substr($a,4,1)=='/')	{ return '00/00/0000'; }
				else
					{ return '0000/00/00'; }
			}
		//-----------
		}
	else
		{
		if (substr($a,2,1)=='-' or substr($a,4,1)=='-')	{$caracter='-';}
			else {$caracter='/';}
		//-----------
		$a = explode($caracter,$a);
		$aux = $a[2];
		$a[2] = $a[0];
		$a[0] = $aux;
		return implode($caracter,$a);
		}
	}

//--------------

?>
