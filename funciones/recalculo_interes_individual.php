<?php
//--------------
setlocale(LC_ALL, 'sp_ES','sp', 'es');
//mysql_query("SET NAMES 'latin1'");
date_default_timezone_set('America/Caracas');
//--------------
session_start();
include "../conexion.php";
include "../auxiliar.php";

$consultaX = "SELECT * FROM `liquidacion` WHERE x`rif` = 'V111241003' AND `id_tributo` = '99';"; 
$tablaX = mysql_query($consultaX);
while ($registroX = mysql_fetch_object($tablaX))
	{
	$consultaXX = "UPDATE `liquidacion` SET monto_bs ='".funcion_interes($registroX->monto_pagado,voltea_fecha($registroX->fecha_pago),voltea_fecha($registroX->fecha_vencimiento))."' WHERE `id_liquidacion` = '".$registroX->id_liquidacion."'";
	$tablaXX = mysql_query($consultaXX);echo $consultaXX.'</br>';
	}

?>