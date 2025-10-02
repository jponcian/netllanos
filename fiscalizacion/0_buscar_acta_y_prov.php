<?php 
$acta = -1;
$status_acta = -1;
$monto_pagado = 0;
//----------------
$status_prov = -1;
//----------------
$consulta_xx = "SELECT * FROM vista_detalle_actas WHERE anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE']." LIMIT 1;";
$tabla_xx = mysql_query($consulta_xx);
//----------------
if ($registro_acta = mysql_fetch_object($tabla_xx))
	{
	$COT = $registro_acta->COT ;
	$acta = $registro_acta->acta ;
	$status_acta = $registro_acta->status_acta ;
	}
//----------------
$consulta_xx2 = "SELECT monto_pagado FROM vista_detalle_actas WHERE monto_pagado>0 AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE'].";";
$tabla_xx2 = mysql_query($consulta_xx2);
//----------------
if ($registro_acta2 = mysql_fetch_object($tabla_xx2))
	{
	$monto_pagado = $registro_acta2->monto_pagado ;
	}
//----------------
$consulta_xxx = "SELECT * FROM expedientes_fiscalizacion WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_xxx = mysql_query($consulta_xxx);
//----------------
if ($registro_prov = mysql_fetch_object($tabla_xxx))
	{
	$status_prov = $registro_prov->status;
	$fecha_not = $registro_prov->fecha_notificacion;
	$fiscal = $registro_prov->ci_fiscal1;
	$supervisor = $registro_prov->ci_supervisor;
	}
//----------------
$consulta_xxx = "SELECT serie FROM liquidacion, a_sancion where liquidacion.id_sancion = a_sancion.id_sancion and serie<>29 and serie<>36 and anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." and origen_liquidacion=4;";
$tabla_xxx = mysql_query($consulta_xxx);
//----------------
if ($registro = mysql_fetch_object($tabla_xxx))
	{
	$serie = $tabla_xxx->serie;
	}
//----------------
?>