<?php 
//$acta = -1;
//$status_acta = -1;
//$monto_pagado = 0;
////----------------
//$status_prov = -1;
////----------------
//$consulta_xx = "SELECT * FROM vista_detalle_actas WHERE anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE'].";";
//$tabla_xx = mysql_query($consulta_xx);
////----------------
//if ($registro_acta = mysql_fetch_object($tabla_xx))
//	{
//	$COT = $registro_acta->COT ;
//	$acta = $registro_acta->acta ;
//	$status_acta = $registro_acta->status_acta ;
//	}
////----------------
//$consulta_xx2 = "SELECT monto_pagado FROM vista_detalle_actas WHERE monto_pagado>0 AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE'].";";
//$tabla_xx2 = mysql_query($consulta_xx2);
////----------------
//if ($registro_acta2 = mysql_fetch_object($tabla_xx2))
//	{
//	$monto_pagado = $registro_acta->monto_pagado ;
//	}
////----------------
$consulta_xxx = "SELECT * FROM vista_sumario_expedientes WHERE anno_expediente_fisc=".$_SESSION['ANNO_PRO']." AND num_expediente_fisc=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_xxx = mysql_query($consulta_xxx);
//----------------
if ($registro_prov = mysql_fetch_object($tabla_xxx))
	{
	$status_prov = $registro_prov->status;
	$fiscal = $registro_prov->cedula_ponente;
	$jefe = $registro_prov->cedula_jefe;
	}
//----------------
?>