<?php

	error_reporting(0);
	include("../conexion.php");

	//VARIABLES A UTILIZAR
	$accion = $_POST['accion'];//$_POST['accion'];
	$sector = $_POST['sector'];
	$anno = $_POST['anno'];
	$numero = $_POST['numero'];
	$sancion = $_POST['sancion'];
	$fechaincio = $_POST['periodoinicio'];
	$fechafin = $_POST['periodofin'];
	$info=array();
	$fechainicio = voltea_fecha($fechaincio);
	$fechafin = voltea_fecha($fechafin);
	$mensaje = 'Error al modificar el periodo';
	$permitido = false;
	//$numero = date("Y-m-d");


	if ($accion = 'periodo')
	{
		$registros = "UPDATE liquidacion SET periodoinicio='".$fechainicio."', periodofinal='".$fechafin."' WHERE origen_liquidacion = 4 AND sector = ".$sector." AND anno_expediente = ".$anno." AND num_expediente = ".$numero." AND id_sancion = ".$sancion;
		//echo $registros;
	    
	    if ($resultregistros = $conexionsql->query($registros))
	    {
			$mensaje = 'Periodo modificado con éxito!!!';
			$permitido = true;
	    }

		$info = array(
			"mensaje" => $mensaje,
			"permitido" => true
		);

		echo json_encode($info);
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
