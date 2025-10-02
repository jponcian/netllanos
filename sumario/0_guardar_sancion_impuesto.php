<?php
$sancion = 0;
//---------------
$consulta_xx = "SELECT expedientes_sumario.rif, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta, fis_actas_detalle.tributo, sumario_resultado.monto_confirmado FROM fis_actas_detalle INNER JOIN fis_actas ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN sumario_resultado ON sumario_resultado.id_detalle_acta = fis_actas_detalle.id_detalle INNER JOIN expedientes_sumario ON expedientes_sumario.id_expediente = sumario_resultado.id_expediente WHERE fis_actas.anno_prov=".$_POST['OANNO']." AND fis_actas.num_prov=".$_POST['ONUMERO']." AND fis_actas.id_sector=".$_POST['OSEDE'].";";
$tabla_xx = mysql_query($consulta_xx);
//----------------
while ($registro_acta = mysql_fetch_object($tabla_xx))
	{
	$montoajustado = monto_ajustado($_POST['OID'], $registro_acta->periodo_desde, $registro_acta->periodo_hasta);
	//echo "Monto ajustado: ".$montoajustado;
	// ---------------
	switch ($registro_acta->tributo)
	{
	case 1: // I.V.A.
		$sancion = 2001;
		$tributo = 23;
		//-----------------------
	break;
	case 3: // I.S.L.R.
		if (strtoupper(substr($rif,0,1)) == "J") 	
			{
			$sancion = 2004;
			$tributo = 20;
			}
		if (strtoupper(substr($rif,0,1)) == "V") 	
			{
			$sancion = 2005;
			$tributo = 21;
			}
		//-----------------------
	break;
	case 9: // SUCESIONES
		$sancion = 2003;
		$tributo = 22;
		//-----------------------
	break;
	}

	//-------------------------------------------------------------
	if ($sancion > 0)
		{
		$consulta4 = "INSERT INTO liquidacion ( sector , origen_liquidacion , anno_expediente , num_expediente , rif , periodoinicio , periodofinal , id_sancion , id_tributo , porcion , monto_ut , monto_bs , especial, usuario ) VALUES ( '".$_POST['OSEDE']."',  '5',  '".$_POST['OANNO']."',  '".$_POST['ONUMERO']."',  '".$registro_acta->rif."',  '".$registro_acta->periodo_desde."',  '".$registro_acta->periodo_hasta."',  '".$sancion."',  '".$tributo."',  '1',  '0',  '".formato_moneda2($registro_acta->monto_confirmado + $montoajustado)."',  '1', ".$_SESSION['CEDULA_USUARIO'].");";
		//-----------------
		$tabla4 = mysql_query ($consulta4);
		}
	}
?>