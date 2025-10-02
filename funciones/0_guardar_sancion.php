<?php
// PARA REVISAR SI YA EXISTE LA SANCION
$consulta3 = "SELECT id_liquidacion, anno_expediente, num_expediente, sector, origen_liquidacion FROM liquidacion WHERE rif='".$rif."' AND id_sancion=".$_POST['OSANCION']." AND periodoinicio='".voltea_fecha($_POST['OINICIO'])."' AND periodofinal='".voltea_fecha($_POST['OFIN'])."';"; //echo $consulta3;
$tabla3 = mysql_query ($consulta3); 
if ($registro3 = mysql_fetch_object($tabla3))
	{echo "<script type=\"text/javascript\">alert('¡¡¡Sancion Duplicada!!!');</script>";
	//----------- DEPENDENCIA
	$consulta_x = 'SELECT nombre FROM z_sectores WHERE id_sector='.$registro3->sector.';'; 
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//----------- ORIGEN O AREA
	$consulta_xx = 'SELECT Descripcion FROM a_origen_liquidacion WHERE Codigo='.$registro3->origen_liquidacion.';'; 
	$tabla_xx = mysql_query ($consulta_xx);
	$registro_acta = mysql_fetch_object($tabla_xx);
	//-----------
	echo "<script type=\"text/javascript\">alert('¡¡¡Dependencia=> ".$registro_x->nombre.'  /  Area=> '.$registro_acta->Descripcion.'\n Año=> '.$registro3->anno_expediente.' / Expediente o Providencia=> '.$registro3->num_expediente."!!!');</script>";}
else
	{
	$consulta4 = "INSERT INTO liquidacion (reiteracion, reiteracion_resolucion, sector , origen_liquidacion , anno_expediente , num_expediente , rif , periodoinicio , periodofinal , id_sancion , id_tributo , porcion , monto_ut , monto_bs , especial, fecha_vencimiento, fecha_presentacion, fecha_pago, monto_pagado, planilla, usuario ) VALUES ( ".$reiteracion.", '".$id_resolucion."', '".$_SESSION['SEDE']."',  '".$_SESSION['ORIGEN']."',  '".$_SESSION['ANNO_PRO']."',  '".$_SESSION['NUM_PRO']."',  '".$rif."',  '".voltea_fecha($_POST['OINICIO'])."',  '".voltea_fecha($_POST['OFIN'])."',  '".$_POST['OSANCION']."',  '".$_POST['OTRIBUTO']."',  '1',  '".$ut_aplicadas."',  '".$monto."',  '".$_POST['OESPECIAL']."', '".voltea_fecha($_POST['OVENCIMIENTO'])."', '".voltea_fecha($_POST['OPRESENTACION'])."', '".voltea_fecha($_POST['OPAGO'])."', 0".$_POST['OMONTO'].", '".$_POST['OPLANILLA']."', ".$_SESSION['CEDULA_USUARIO'].");"; //echo $consulta4;
	//-----------------
	if ($tabla4 = mysql_query ($consulta4))
		{
		echo "<script type=\"text/javascript\">alert('¡¡¡Sancion Aplicada Exitosamente!!!');</script>";
		$consulta4 = "UPDATE liquidacion SET monto_ut=monto_bs/".$_SESSION['VALOR_UT_ACTUAL']." WHERE monto_ut=0 and id_sancion >= '60000' AND id_sancion <= '100000';";
		$tabla4 = mysql_query ($consulta4);
		}
	else
		{
		echo "<script type=\"text/javascript\">alert('¡¡¡Error en la Carga!!!');</script>";		
		}
	}
//-------------
?>