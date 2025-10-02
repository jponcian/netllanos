<?php
session_start();

$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");

$agregar= "SELECT count(Anno) as num FROM CS_Salida_Archivo WHERE Anno=2015 and Numero=422";
$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
$num=odbc_result($rs,"num");
$encontrado_access = 0;
if ($num > 0)
{
	$encontrado_access = 1;
	$agregar= "SELECT * FROM CS_Salida_Archivo WHERE Anno=2015 and Numero=422";
	$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
	$valor = odbc_fetch_array($rs);
	$nummemo=$_GET['nummemo'];
	$FechaEmision=$_GET['fechamemo'];
	$Anno_memo=date("Y");
	$Rif=$valor['Rif'];
	$Nombre=$valor['NombreRazon'];
	$Tipo = $valor['Tipo'];
	$FechaNotificacion=$valor['FechaNotificacion'];
	$FechaRecepcion=date("Y-m-d");
	$Division='TRAMITACION';
	$Contenido = 'PROV-INFORME FISCAL';
	$status=$estatus;
	$fp = $_GET['fp'];
}

?>