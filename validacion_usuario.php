<?php
error_reporting(0);
if ($_SESSION['ADMINISTRADOR']!=1)
	{
	if ($acceso==999)
		{
		header ("Location: menuprincipal.php?opcion=mant"); 
		exit();
		}
	else
		{
		//----------- PARA VALIDAR SI TIENE ACCESO A LA OPCION
		$consulta_x = "SELECT acceso FROM z_empleados_accesos WHERE cedula=".$_SESSION['CEDULA_USUARIO']." and acceso = ".$acceso.";";
		$tabla_x = mysql_query ($consulta_x);
		if ($registro_x = mysql_fetch_object($tabla_x))
		//-------------
			{
			}
		else
			{
			header ("Location: menuprincipal.php?opcion=no"); 
			exit();
			}
		}
	}
?>