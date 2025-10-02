<?php

	include "../conexion.php";
	//include "../../funciones/auxiliar_php.php";
	
	global $conexionsql;
	//VARIABLES A UTILIZAR
	$accion = $_GET['accion'];
	$sede = $_GET['sede'];
	$admin = $_GET['admin'];
	$mensaje = "";

	function extraer_iniciales($cadena)
	{
		$array = explode(" ", $cadena);

		$iniciales="";
		for ($i = 0; $i < count($array); $i++) {
			$a = substr($array[$i], 0, 1);
			if (preg_match('/'.$a.'\b/', 'A B C D E F G H I J K L M N Ñ O P Q R S T U V W X Y Z')) 
			{
			    $iniciales= $iniciales.$a;
			}
		}
		return $iniciales;
	}

	//--------------
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
	
	if ($sede>0)
	{
		$sede= ' AND sector='.$sede;
	} else {
		$sede= '';
	}

	if ($accion == 1)
	{
		//OBTENEMOS EL VALOR DE LAS VARIABLES
		$numero = $_GET['num'];
		$anno = $_GET['anno'];

		$sql = "SELECT * FROM ct_salida_expediente WHERE Anno_Providencia=$anno AND NroAutorizacion=$numero".$sede;

	}

	if ($accion == 2)
	{
		//OBTENEMOS EL VALOR DE LAS VARIABLES
		$numero = $_GET['num'];
		$anno = $_GET['anno'];

		$sql = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=$anno AND NroMemo=$numero".$sede;

	}

	if ($accion == 3)
	{
		//OBTENEMOS EL VALOR DE LAS VARIABLES
		$destino = $_GET['destino'];
		$ini = voltea_fecha($_GET['ini']);
		$fin = voltea_fecha($_GET['fin']);

		$sql = "SELECT * FROM ct_salida_expediente WHERE Division='$destino'$sede AND FechaEmision BETWEEN '$ini' AND '$fin'";
		
	}

	if ($accion == 4)
	{
		//OBTENEMOS EL VALOR DE LAS VARIABLES
		$rif = $_GET['rif'];

		$sql = "SELECT * FROM ct_salida_expediente WHERE Rif='$rif'";
		
	}

	$resultado = $conexionsql->query($sql);
	$cantidad = $resultado->num_rows;
	//echo "Cantidad: ".$cantidad." - ".$sql;

	if ($cantidad > 0)
	{
		//echo "imprimir_registro(".$sql.",".$valor->Status.");";
		echo '<br />';
		echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center" style="font-size:9px">';
		echo '<tr><td bgcolor="#999999"><b>Año Memo';
		echo '</td><td bgcolor="#999999"><b>N° Memo</td>';			
		echo '</td><td bgcolor="#999999"><b>Providencia</td>';			
		echo '</td><td bgcolor="#999999"><b>Emision</td>';			
		echo '</td><td bgcolor="#999999"><b>Rif</td>';			
		echo '</td><td bgcolor="#999999"><b>Nombre</td>';
		echo '</td><td bgcolor="#999999"><b>Recepcion</td>';			
		echo '</td><td bgcolor="#999999"><b>Resultado</td>';			
		echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
		echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
		echo '</td><td bgcolor="#999999"><b>Multa Reparo</td>';			
		echo '</td><td bgcolor="#999999"><b>Intereses</td>';			
		echo '</td><td bgcolor="#999999"><b>Monto Pagado</td>';			
		echo '</td><td bgcolor="#999999"><b>Estatus</td>';			
		echo '</td><td bgcolor="#999999"><b>Sede</td>';			
		echo '</b>';

		$result = $conexionsql->query($sql);
		while ($registro = $result->fetch_object())
		{

			if ($registro->Status == 11 or $registro->Status == 21 or $registro->Status == 31) 
			{
				$exp_resultado = 'CONFORME';
			}

			if ($registro->Status == 12 or $registro->Status == 22 or $registro->Status == 32 or $registro->Status == 42) 
			{
				if ($registro->Anno_memo > 2017)
				{
					$exp_resultado = 'NOTIFICACION - SANCIONADO';	
				}
				else
				{
					$exp_resultado = 'SANCIONADO';					
				}
			}

			if ($registro->Status == 23 or $registro->Status == 33 or $registro->Status == 43) 
			{
				if ($registro->Anno_memo > 2017)
				{
					$exp_resultado = 'NOTIFICACION - ALLANADO';	
				}
				else
				{
					$exp_resultado = 'ALLANADO';					
				}
			}

			if ($registro->Status == 25 or $registro->Status == 35) 
			{
				$exp_resultado = 'NO ALLANADO';
			}

			if ($registro->Status == 24 or $registro->Status == 34 or $registro->Status == 44) 
			{
				if ($registro->Anno_memo > 2017)
				{
					$exp_resultado = 'NOTIFICACION - ALLANADO PARCIAL';	
				}
				else
				{
					$exp_resultado = 'ALLANADO PARCIAL';					
				}
			}

			if ($registro->Status == 91) 
			{
				$exp_resultado = 'PLANILLAS PAGADAS';
			}

			if ($registro->Status == 92 or $registro->Status == 94) 
			{
				$exp_resultado = 'NO PAGADAS/PAGADAS PARCIAL';
			}

			//echo "...Este es el Estatus=: ".$valor['Status']; 
			if ($color=="#EFEFEF") {
				$color="#D0D6DF";
			} else {
				$color="#EFEFEF";
			}

			//DETERMINAMOS EL SECTOR
			$sql_sector = "SELECT * FROM z_sectores WHERE id_sector=".$registro->sector;
			$result_sector = $conexionsql->query($sql_sector);
			$reg_sector = $result_sector->fetch_object();

			echo '<tr bgcolor="'.$color.'"><td>';
			echo $registro->Anno_memo;
			echo '</td><td>';
			echo $registro->NroMemo;
			echo '</td><td>';
			echo $registro->Anno_Providencia."-".$registro->NroAutorizacion;
			echo '</td><td>';
			echo date("d-m-Y",strtotime($registro->FechaEmision));
			echo '</td><td>';
			echo $registro->Rif;
			echo '</td><td>';
			echo $registro->Nombre;
			echo '</td><td>';
			echo date("d-m-Y",strtotime($registro->FechaRecepcion));
			echo '</td><td>';
			echo $exp_resultado;
			echo '</td><td>';
			echo $registro->Monto_Reparo;
			echo '</td><td>';
			echo $registro->Impto_Omitido;
			echo '</td><td>';
			echo $registro->Multa_Reparo;
			echo '</td><td>';
			echo $registro->Intereses;
			echo '</td><td>';
			echo $registro->Monto_Pagado;
			echo '</td><td>';
			echo $registro->Division;
			echo '</td><td>';
			echo extraer_iniciales($reg_sector->nombre);				
			echo '</td></tr>';
		}
		echo '</table>';
	} else {
		echo "<p align='center' style='color: red;'>!!!... No existen registros similares en la base de datos ...!!!<p>";
	}
?>

