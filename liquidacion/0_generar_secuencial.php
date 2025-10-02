<?php
include_once "../conexion.php";
include_once "../funciones/auxiliar_php.php";
//-------------- DIGITO DE LA REGION
$consulta = "SELECT gerencia FROM z_region";
$tabla1 = mysql_query($consulta);
$registro1 = mysql_fetch_object($tabla1);
//--------------
$region = sprintf("%002s",$registro1->gerencia);
//--------------

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++ PLANILLAS POR GENERAR SECUENCIAL DIFERENTES A FRACCIONAMIENTO
	$consulta = "SELECT liquidacion.id_liq_primitiva, liquidacion.id_liquidacion, z_sectores.Cod_Oficina_liqui, a_sancion.serie, liquidacion.secuencial FROM liquidacion, z_sectores, a_sancion WHERE liquidacion.sector = z_sectores.id_sector AND (a_sancion.id_sancion_ajuste = liquidacion.id_sancion OR a_sancion.id_sancion = liquidacion.id_sancion) AND serie<>41 AND liquidacion.status > 10 AND secuencial=999999 AND liquidacion.status <> 90";
	$tabla1 = mysql_query($consulta);
	while ($registro1 = mysql_fetch_object($tabla1))
		{
		$Serie = $registro1->serie; 
		//-- POR SI LA SERIE ES CERO
		if ($Serie<1) { $Serie = serie_liquidacion($registro1->id_liq_primitiva);}
		//--------------
		$Oficina = $registro1->Cod_Oficina_liqui;
		// BUSQUEDA DEL MAYOR POR LA SERIE	EN EL SISTEMA VIEJO
		include "00_SECUENCIAL_VIEJO.php";
		//------------------------------------
		// BUSQUEDA DEL MAYOR POR LA SERIE	
		$consulta = "SELECT Max(secuencial) as Secuencial FROM liquidacion WHERE secuencial<>999999 AND status > 10 AND mid(liquidacion,12,2) = ".$Serie." AND left(liquidacion,4) = year(DATE(now())) AND liquidacion.status <> 90 GROUP BY mid(liquidacion,12,2);";
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		if ($registro->Secuencial>0)
			{$SECUENCIAL=$registro->Secuencial+1;}
		else 
			{$SECUENCIAL=1;}
		// FIN
		//---- COMPARACION DE LOS SECUENCIALES
		if ($SECUENCIAL < $SECUENCIAL_VIEJO)
			{$SECUENCIAL = $SECUENCIAL_VIEJO;}
		//---------------
		// ARREGLO DEL NUMERO DE LIQUIDACION
		$FORMATO=sprintf("%006s", $SECUENCIAL);
		// FIN DE ARREGLO DEL NUMERO DE LIQUIDACION
		$LIQUIDACION = date('Y') . $region . $Oficina . "01" . "2" . sprintf("%002s", $Serie).$FORMATO;
		///-------- ACTUALIZACION DEL NUMERO
		$consulta = "UPDATE Liquidacion SET Secuencial = ".$SECUENCIAL.", liquidacion = ".$LIQUIDACION." WHERE id_liquidacion=".$registro1->id_liquidacion.";"; 
		//echo $consulta;
		$tabla = mysql_query($consulta);
		}
	
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ SECUENCIALES PARA FRACCIONAMIENTO
	// BUSQUEDA DEL MAYOR POR LA SERIE	EN EL SISTEMA VIEJO
	include "00_SECUENCIAL_VIEJO_FRACC.php";
	//------------------------------------
	// BUSQUEDA DEL MAYOR POR LA SERIE
	$consulta = "SELECT Max(liquidacion.secuencial) as Secuencial FROM liquidacion, a_sancion WHERE (a_sancion.id_sancion_ajuste = liquidacion.id_sancion OR a_sancion.id_sancion = liquidacion.id_sancion) AND a_sancion.serie = 41 AND left(liquidacion.liquidacion,4) = year(DATE(now())) AND liquidacion.status <> 90 GROUP BY a_sancion.serie;";
	$tabla = mysql_query($consulta);
	$registro = mysql_fetch_object($tabla);
	if (($registro->Secuencial)>0)
		{$SECUENCIAL=$registro->Secuencial+1;}
	else 
		{$SECUENCIAL=1;}
	// FIN
	//---- COMPARACION DE LOS SECUENCIALES
	if ($SECUENCIAL < $SECUENCIAL_VIEJO_FRACCIONAMIENTO)
		{$SECUENCIAL = $SECUENCIAL_VIEJO_FRACCIONAMIENTO;}
	//---------------
	// CANTIDAD DE PLANILLAS POR FRACCIONAMIENTO
	$consulta1 = "SELECT id_liquidacion, Cod_Oficina_liqui, serie, secuencial FROM liquidacion, z_sectores, a_sancion WHERE liquidacion.sector = z_sectores.id_sector AND (a_sancion.id_sancion_ajuste = liquidacion.id_sancion OR a_sancion.id_sancion = liquidacion.id_sancion) AND serie=41 AND liquidacion.status > 10 AND secuencial=999999 AND liquidacion.status <> 90 AND num_expediente=".$_SESSION['NUM_PRO']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND origen_liquidacion=".$_SESSION['ORIGEN']." AND sector =".$_SESSION['SEDE']." ORDER BY id_liquidacion;"; 
	$tabla1 = mysql_query($consulta1);
	
	$Fraccion=1;
	while ($registro1 = mysql_fetch_object($tabla1))
		{
		$Oficina = $registro1->Cod_Oficina_liqui;
		//------------------
		$FORMATO=sprintf("%006s", $SECUENCIAL);
		$LIQUIDACION = date('Y') . $region . $Oficina . sprintf("%002s", $Fraccion) . "2" . "41" . $FORMATO;
		// ACTUALIZACION DEL REGISTRO
		$consulta = "UPDATE Liquidacion SET Secuencial = ".$SECUENCIAL.", liquidacion = ".$LIQUIDACION." WHERE id_liquidacion=".$registro1->id_liquidacion.";";
		$tabla = mysql_query($consulta);
		$Fraccion++;
		}
	// +++++++++++++++++++++++++++++++++ FIN
?>