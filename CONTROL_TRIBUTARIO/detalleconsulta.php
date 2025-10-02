<?php

	include "conexion.php";
	include "../funciones/auxiliar_php.php";
	
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

		$sql = "SELECT * FROM ct_destruccion_facturas WHERE year(fecha_emision)=$anno AND numero_acta=$numero".$sede;

	}

	if ($accion == 2)
	{
		//OBTENEMOS EL VALOR DE LAS VARIABLES
		$ini = voltea_fecha($_GET['ini']);
		$fin = voltea_fecha($_GET['fin']);

		$sql = "SELECT * FROM ct_destruccion_facturas WHERE fecha_emision BETWEEN '$ini' AND '$fin'".$sede;
		
	}

	if ($accion == 3)
	{
		//OBTENEMOS EL VALOR DE LAS VARIABLES
		$rif = $_GET['rif'];

		$sql = "SELECT * FROM ct_destruccion_facturas WHERE rif='$rif'";
		
	}

	$resultado = $conexionsql->query($sql);
	$cantidad = $resultado->num_rows;
	//echo "Cantidad: ".$cantidad." - ".$sql;

	if ($cantidad > 0)
	{
		//echo "imprimir_registro(".$sql.",".$valor->Status.");";
		echo '<br />';
		echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center" style="font-size:9px">';
		echo '<tr><td bgcolor="#999999"><b>Año Acta';
		echo '</td><td bgcolor="#999999"><b>N° Acta</td>';			
		echo '</td><td bgcolor="#999999"><b>Emision</td>';			
		echo '</td><td bgcolor="#999999"><b>Rif</td>';			
		echo '</td><td bgcolor="#999999"><b>Nombre</td>';
		echo '</td><td bgcolor="#999999"><b>N° Solicitud</td>';			
		echo '</td><td bgcolor="#999999"><b>Fecha Solic.</td>';			
		echo '</td><td bgcolor="#999999"><b>Tipo Sol.</td>';			
		echo '</td><td bgcolor="#999999"><b>C.I. Func.</td>';			
		echo '</td><td bgcolor="#999999"><b>Funcionario</td>';			
		echo '</td><td bgcolor="#999999"><b>Sede</td>';			
		echo '</b>';

		$result = $conexionsql->query($sql);
		while ($registro = $result->fetch_object())
		{

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
			echo date("Y", strtotime($registro->fecha_emision));
			echo '</td><td>';
			echo $registro->numero_acta;
			echo '</td><td>';
			echo date("d-m-Y",strtotime($registro->fecha_emision));
			echo '</td><td>';
			echo $registro->rif;
			echo '</td><td>';
			echo $registro->nombre;
			echo '</td><td>';
			echo $registro->num_solicitud;
			echo '</td><td>';
			echo date("d-m-Y",strtotime($registro->fecha_solicitud));
			echo '</td><td>';
			echo $registro->tipo_solicitud;
			echo '</td><td>';
			echo $registro->ced_funcionario;
			echo '</td><td>';
			echo $registro->nom_funcionario;
			echo '</td><td>';
			echo extraer_iniciales($reg_sector->nombre);				
			echo '</td></tr>';
		}
		echo '</table>';
	} else {
		echo "<p align='center' style='color: red;'>!!!... No existen registros similares en la base de datos ...!!!<p>";
	}
?>

