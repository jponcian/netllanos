<?php
$i = 0;
$origenes = '(';

//------------------------ BUSQUEDA
$consulta_x = "SELECT origen FROM z_empleados_origenes WHERE cedula=".$_SESSION['CEDULA_USUARIO']." ORDER BY origen;";
	//-------- POR SI ES ADMINISTRADOR
	if ($_SESSION['ADMINISTRADOR'] > 0)
		{		$consulta_x = "SELECT Codigo as origen FROM a_origen_liquidacion;";		}
	//--------------------------------
$tabla_x = mysql_query ($consulta_x);
while ($registro_x = mysql_fetch_object($tabla_x))
	{
	if ($i > 0)
		{	$origenes = $origenes . ' ,';	}
	//-------------
	$origenes = $origenes . $registro_x->origen;
	$i++;
	//------- POR SI ES ESPECIAL
	}
//-------------------------
$origenes = $origenes.')';

//--------------- POR SI ES AJUSTE ESPECIAL
$consulta_x = "SELECT origen FROM z_empleados_origenes WHERE cedula=".$_SESSION['CEDULA_USUARIO']." and origen=7;";
$tabla_x = mysql_query ($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
if ($registro_x->origen==7)
	{	$origenUT = '7';	}	else	{	$origenUT = '16';	}
//-------------------------

//--------------- POR SI ES FRACCIONAMIENTO ESPECIAL
$consulta_x = "SELECT origen FROM z_empleados_origenes WHERE cedula=".$_SESSION['CEDULA_USUARIO']." and origen=8;";
$tabla_x = mysql_query ($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
if ($registro_x->origen==8)
	{	$origenF = '8';		}	else	{	$origenF = '17';	}
//-------------------------
?>