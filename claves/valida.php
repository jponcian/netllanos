<?php
session_start(); 
//------------
include "../conexion.php";
include "../auxiliar.php";

//------------
if (isset($_POST['OUSUARIO'])) {
  $_SESSION['CEDULA_USUARIO'] = (get_magic_quotes_gpc()) ? $_POST['OUSUARIO'] : addslashes($_POST['OUSUARIO']);
	}
if (isset($_POST['OCLAVE'])) {
  $_SESSION['VAR_CLAVE'] = (get_magic_quotes_gpc()) ? $_POST['OCLAVE'] : addslashes($_POST['OCLAVE']);
	}

if ((trim($_SESSION['CEDULA_USUARIO'])=='') or (trim($_SESSION['VAR_CLAVE'])==''))
	{
	header("Location: index.php?errorusuario=vacio");
	exit();
	}

//----------- VALIDAR LA CEDULA
$consulta_x = "SELECT * FROM z_empleados WHERE cedula = ".$_SESSION['CEDULA_USUARIO'].";";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

if ($registro_x['cedula']<>$_SESSION['CEDULA_USUARIO'])
	{
	header ("Location: index.php?errorusuario=sist");
	exit();
	}

//------------ VALIDAR LA CLAVE
$consulta_x = "SELECT cedula FROM z_empleados WHERE cedula = ".$_SESSION['CEDULA_USUARIO']." AND clave='".$_SESSION['VAR_CLAVE']."'"; 
$tabla_x = mysql_query ($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

//---------
if ($registro_x['cedula']==$_SESSION['CEDULA_USUARIO'])
	{
	//-------- CLAVE VERIFICADA
	$_SESSION['VERIFICADO'] = 'SI';
	$_SESSION['BDD'] = 'losllanos';
	//-----------------	
	header ("Location: menuprincipal.php");
	exit();
	}
else 	
	{ 
	header("Location: index.php?errorusuario=sist");
	exit();
	} 
	//------------
?>