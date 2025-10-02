<?php 
//----------------
$status_prov = -1;
//----------------
$consulta_xx = "SELECT status, FechaRegistro FROM expedientes_rif WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE_USUARIO'].";";
$tabla_xx = mysql_query($consulta_xx);
//----------------
if ($registro_acta = mysql_fetch_object($tabla_xx))
	{
	$status_prov = $registro_acta->status ;
	$fecha_registro = $registro_acta->FechaRegistro ;
	}
//----------------
?>