<?php

// -------------- ACTUALIZACION DE LA PROVIDENCIA
$consulta="UPDATE expedientes_fiscalizacion SET status=7, fecha_transferencia = date(now()) WHERE id_expediente=".$registro_datos->id_expediente.";";
$tabla = mysql_query($consulta); 
// --------------

//--------------- POR SI LA PROVIDENCIA TIENE ACTA DE REPARO
$consulta_xx = "SELECT tributo, COT, periodo_desde, periodo_hasta, multa_actual, fecha_vencimiento, fecha_pago, monto_pagado, planilla, interes, UT_actual, impuesto_omitido, monto_pagado FROM vista_detalle_actas WHERE acta = 0 AND anno_prov=".$registro_datos->anno." AND num_prov=".$registro_datos->numero." AND id_sector=".$registro_datos->sector.";";
$tabla_xx = mysql_query($consulta_xx);
//----------------
while ($registro_acta = mysql_fetch_object($tabla_xx))
{
	// ---------------
	switch ($registro_acta->tributo)
	{
	case 1: // I.V.A.
		if ($registro_acta->COT == "111") 				{$sancion = 1503;}
		//-----------------------
		if ($registro_acta->COT == "113") 				{$sancion = 568;}
		//-----------------------
		if (substr($registro_acta->COT,0,4) == "112#") 	{$sancion = 1503;}
		//-----------------------
		if ($registro_acta->COT == "112") 				{$sancion = 11503;}
		//-----------------------
		if (substr($registro_acta->COT,0,4) == "114#") 	{$sancion = 15449;}
		//-----------------------
		if (substr($registro_acta->COT,0,4) == "115#") 	{$sancion = 15449;}
		//-----------------------
	break;
	case 3: // I.S.L.R.
		if ($registro_acta->COT == "111") 	
			{
			$consulta_x = "SELECT fis_actas_detalle.monto_pagado, fis_actas_detalle.impuesto_pagado FROM fis_actas INNER JOIN fis_actas_detalle ON fis_actas.id_acta = fis_actas_detalle.id_acta WHERE (fis_actas_detalle.`status` = 0 OR fis_actas_detalle.`status` = 1) AND fis_actas_detalle.monto_pagado >= fis_actas_detalle.impuesto_omitido AND fis_actas_detalle.monto_pagado > 0 AND acta = 0 AND anno_prov=".$registro_datos->anno." AND num_prov=".$registro_datos->numero." AND id_sector=".$registro_datos->sector.";";
			$tabla_x = mysql_query($consulta_x);
			if ($registro_x = mysql_fetch_object($tabla_x))
				{$sancion = 1502;}
			else
				{$sancion = 1507;}
			}
		//-----------------------
		if ($registro_acta->COT == "112") 	
			{
			$consulta_x = "SELECT fis_actas_detalle.monto_pagado, fis_actas_detalle.impuesto_pagado FROM fis_actas INNER JOIN fis_actas_detalle ON fis_actas.id_acta = fis_actas_detalle.id_acta WHERE (fis_actas_detalle.`status` = 0 OR fis_actas_detalle.`status` = 1) AND fis_actas_detalle.monto_pagado >= fis_actas_detalle.impuesto_omitido AND fis_actas_detalle.monto_pagado > 0 AND acta = 0 AND anno_prov=".$registro_datos->anno." AND num_prov=".$registro_datos->numero." AND id_sector=".$registro_datos->sector.";";
			$tabla_x = mysql_query($consulta_x);
			if ($registro_x = mysql_fetch_object($tabla_x))
				{$sancion = 11502;}
			else
				{$sancion = 11507;}
			}
		//-----------------------
		if (substr($registro_acta->COT,0,4) == "112#") {$sancion = 556;}
		//-----------------------
		if ($registro_acta->COT == 113) {$sancion = 567;}
		//-----------------------
		if (substr($registro_acta->COT,0,4) == "114#") {$sancion = 15567;}
		//-----------------------
		if (substr($registro_acta->COT,0,4) == "115#") {$sancion = 15567;}
		//-----------------------
	break;
	case 9: // SUCESIONES
		if (($registro_acta->COT == 111) or (substr($registro_acta->COT,0,4) == "112#") or ($registro_acta->COT == 113)) 
			{$sancion = 1504;}
		else
			{$sancion = 11504;}
		//-----------------------
	break;
	case 11: // CASINOS
		if (($registro_acta->COT == 111) or (substr($registro_acta->COT,0,4) == "112#") or ($registro_acta->COT == 113)) 
			{
			$consulta_x = "SELECT fis_actas_detalle.monto_pagado, fis_actas_detalle.impuesto_pagado FROM fis_actas INNER JOIN fis_actas_detalle ON fis_actas.id_acta = fis_actas_detalle.id_acta WHERE (fis_actas_detalle.`status` = 0 OR fis_actas_detalle.`status` = 1) AND fis_actas_detalle.monto_pagado >= fis_actas_detalle.impuesto_omitido AND fis_actas_detalle.monto_pagado > 0 AND acta = 0 AND anno_prov=".$registro_datos->anno." AND num_prov=".$registro_datos->numero." AND id_sector=".$registro_datos->sector.";";
			$tabla_x = mysql_query($consulta_x);
			if ($registro_x = mysql_fetch_object($tabla_x))
				{$sancion = 1509;}
			else
				{$sancion = 1508;}
			}
		else
			{
			$consulta_x = "SELECT fis_actas_detalle.monto_pagado, fis_actas_detalle.impuesto_pagado FROM fis_actas INNER JOIN fis_actas_detalle ON fis_actas.id_acta = fis_actas_detalle.id_acta WHERE (fis_actas_detalle.`status` = 0 OR fis_actas_detalle.`status` = 1) AND fis_actas_detalle.monto_pagado >= fis_actas_detalle.impuesto_omitido AND fis_actas_detalle.monto_pagado > 0 AND acta = 0 AND anno_prov=".$registro_datos->anno." AND num_prov=".$registro_datos->numero." AND id_sector=".$registro_datos->sector.";";
			$tabla_x = mysql_query($consulta_x);
			if ($registro_x = mysql_fetch_object($tabla_x))
				{$sancion = 11509;}
			else
				{$sancion = 11508;}
			}
	//-----------------------
	break;
	case 13: // ENVITE Y AZAR
		if (($registro_acta->COT == 111) or (substr($registro_acta->COT,0,4) == "112#") or ($registro_acta->COT == 113)) 
			{	$sancion = 683;		}
		else
			{	$sancion = 10683;	}
	//-----------------------
	break;
	}

	//-------------------------------------------------------------
	if ($registro_acta->monto_pagado > 0 and $registro_acta->acta==0 and $registro_acta->impuesto_omitido>0)
		{	
		//---------------- PARA LA MULTA
		$consulta4 = "INSERT INTO liquidacion ( sector , origen_liquidacion , anno_expediente , num_expediente , rif , periodoinicio , periodofinal , id_sancion , id_tributo , porcion , monto_ut , monto_bs , fecha_vencimiento, fecha_pago, monto_pagado, planilla, usuario ) VALUES ( '".$registro_datos->sector."',  '".$_SESSION['ORIGEN']."',  '".$registro_datos->anno."',  '".$registro_datos->numero."',  '".$registro_datos->rif."',  '".$registro_acta->periodo_desde."',  '".$registro_acta->periodo_hasta."',  '".$sancion."',  '".$registro_acta->tributo."',  '1',  '".formato_moneda2($registro_acta->UT_actual)."',  '".formato_moneda2($registro_acta->multa_actual)."', '".$registro_acta->fecha_vencimiento."', '".$registro_acta->fecha_pago."', 0".formato_moneda2($registro_acta->monto_pagado).", '".$registro_acta->planilla."', ".$_SESSION['CEDULA_USUARIO'].");"; //echo $consulta4;
		//-----------------
		$tabla4 = mysql_query ($consulta4);
	
		//---------------- PARA EL INTERES
		$consulta4 = "INSERT INTO liquidacion ( sector , origen_liquidacion , anno_expediente , num_expediente , rif , periodoinicio , periodofinal , id_sancion , id_tributo , id_tributo2 , porcion , monto_ut , monto_bs , fecha_vencimiento, fecha_pago, monto_pagado, planilla, usuario ) VALUES ( '".$registro_datos->sector."',  '".$_SESSION['ORIGEN']."',  '".$registro_datos->anno."',  '".$registro_datos->numero."',  '".$registro_datos->rif."',  '".$registro_acta->periodo_desde."',  '".$registro_acta->periodo_hasta."',  '2009', 99 , '".$registro_acta->tributo."',  '1',  '0',  '".formato_moneda2($registro_acta->interes)."', '".$registro_acta->fecha_vencimiento."', '".$registro_acta->fecha_pago."', 0".formato_moneda2($registro_acta->monto_pagado).", '".$registro_acta->planilla."', ".$_SESSION['CEDULA_USUARIO'].");";
		$tabla4 = mysql_query ($consulta4); //echo $consulta4;
		}
	//----------------------------------------------------------------
	
	//TRANSFERIMOS EL EXPEDIENTE A SUMARIO
	if ($registro_acta->monto_pagado<$registro_acta->impuesto_omitido and $registro_acta->acta==0 and $registro_acta->impuesto_omitido>0)
		{
		$consulta="UPDATE expedientes_fiscalizacion SET status=8, fecha_transferencia = date(now()) WHERE id_expediente=".$registro_datos->id_expediente.";";
		$tabla = mysql_query($consulta); 
		} 
}
		
// -------------- ACTUALIZACION DE LA LIQUIDACION
$consulta="UPDATE liquidacion SET status = 10, fecha_transferencia_a_liq = date(now()), usuario_transferencia_a_liq=".$_SESSION['CEDULA_USUARIO'].", usuario=".$_SESSION['CEDULA_USUARIO']." WHERE sector=".$registro_datos->sector." AND anno_expediente=".$registro_datos->anno." AND num_expediente=".$registro_datos->numero." AND origen_liquidacion=".$_SESSION['ORIGEN'].";";
$tabla = mysql_query ($consulta);
// --------------

echo "<script type=\"text/javascript\">alert('Providencia Transferida Exitosamente!!!');</script>";
//-------------
?>