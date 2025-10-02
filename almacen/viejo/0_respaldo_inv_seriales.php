<?php
// ACTUALIZAR EL INVENTARIO
//$consulta_x = "UPDATE timbre_inv_detallado SET cantidad = (serial_hasta-serial_desde)+1;";
//$tabla_x = mysql_query($consulta_x);
			
// RESPALDO MENSUAL DE SERIALES
$consultax1 = "SELECT fecha FROM timbre_inv_detallado_mensual WHERE fecha='".date('m/y')."';"; 
$tablax1 = mysql_query($consultax1);
$registrox1 = mysql_fetch_object($tablax1);
//-----------------------
if ($registrox1->fecha <> '')
	{		}
else
	{
	$consultax1 = "INSERT INTO timbre_inv_detallado_mensual ( fecha, codigo, serial_desde, serial_hasta, cantidad ) SELECT '".date('m/y')."' AS fecha, codigo, serial_desde, serial_hasta, cantidad FROM timbre_inv_detallado;";
	$tablax1 = mysql_query($consultax1);
	}
?>