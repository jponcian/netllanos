<?php 
//----------------
$status_prov = -1;
//----------------
$consulta_xx = "SELECT * FROM expedientes_especiales WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_xx = mysql_query($consulta_xx);
//----------------
if ($registro = mysql_fetch_object($tabla_xx))
	{
	$status_prov = $registro->Status ;
	$fecha_registro = $registro->FechaRegistro ;
	}
//----------------
?>