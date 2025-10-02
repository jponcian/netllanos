<?php

	error_reporting(0);
	include("../conexion.php");

	//VARIABLES A UTILIZAR
	$sector = $_POST['sector'];
	$anno = $_POST['anno'];
	$numero = $_POST['numero'];
	$sancion = $_POST['sancion'];
	$info=array();
	$permitido = false;
	//$numero = date("Y-m-d");

	$registro = "SELECT date_format(periodoinicio, '%Y/%m/%d') as periodoinicio, date_format(periodofinal, '%Y/%m/%d') as periodofinal FROM liquidacion WHERE origen_liquidacion = 4 AND sector = ".$sector." AND anno_expediente = ".$anno." AND num_expediente = ".$numero." AND id_sancion = ".$sancion;
    $result = $conexionsql->query($registro);
    $rowcount=mysqli_num_rows($result);
    $valor = $result->fetch_object();

    $fechaincio = voltea_fecha($valor->periodoinicio);
    $fechafin = voltea_fecha($valor->periodofinal);

    if ($rowcount > 0)
    {
    	$permitido = true;
    }

	$info = array(
		"fechainico" => $fechaincio,
		"fechafin" => $fechafin,
		"permitido" => $permitido
	);

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
