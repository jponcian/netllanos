<?php

	error_reporting(0);
	include("../conexion.php");

	//VARIABLES A UTILIZAR
	$accion = $_POST['accion'];//$_POST['accion'];
	$sector = $_POST['sector'];
	$anno = $_POST['anno'];
	$numero = $_POST['numero'];
	$fecha = $_POST['fecha'];
	$info=array();
	$fecha = voltea_fecha($fecha);
	$mensaje = 'Error al modificar la providencia';
	$permitido = false;
	//$numero = date("Y-m-d");


	if ($accion = 'providencia')
	{
		$registros = "UPDATE expedientes_fiscalizacion SET fecha_notificacion='".$fecha."' WHERE sector=".$sector." AND anno=".$anno." AND numero=".$numero;
	    
	    if ($resultregistros = $conexionsql->query($registros))
	    {
			$mensaje = 'Providencia modificada con éxito!!!';
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
