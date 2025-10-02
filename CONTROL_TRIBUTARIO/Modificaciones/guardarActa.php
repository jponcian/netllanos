<?php

	error_reporting(0);
	include("../conexion.php");

	//VARIABLES A UTILIZAR
	$accion = $_POST['accion'];//$_POST['accion'];
	$sector = $_POST['sector'];
	$anno = $_POST['anno'];
	$numero = $_POST['numero'];
	$tipo = $_POST['tipo'];
	$procedimiento = $_POST['procedimiento'];
	$info=array();
	$mensaje = 'Error al modificar el acta';
	$permitido = false;
	//$numero = date("Y-m-d");


	if ($accion = 'acta')
	{
		$registros = "UPDATE fis_actas, fis_actas_detalle SET fis_actas.acta = $tipo, fis_actas_detalle.COT = $procedimiento WHERE fis_actas.id_acta = fis_actas_detalle.id_acta AND fis_actas.id_sector = ".$sector." AND fis_actas.anno_prov = ".$anno." AND fis_actas.num_prov = ".$numero;
		//echo $registros;
	    
	    if ($resultregistros = $conexionsql->query($registros))
	    {
			$mensaje = 'Acta modificada con éxito!!!';
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
