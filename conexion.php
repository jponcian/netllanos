<?php
date_default_timezone_set('America/La_Paz');
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

error_reporting(0);
//--------------
$_SESSION['conexionsql'] = mysql_connect("localhost", "root", "");
//--------------
if ($_SESSION['ADMINISTRADOR'] > 0) {
	if (trim($_SESSION['BDD']) == '') {
		echo 'Error al seleccionar la base de datos!';
	} else {
		mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
		//mysql_query("SET NAMES 'utf8'");
	}
} else {
	$_SESSION['BDD'] = "losllanos";
	mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
	//mysql_query("SET NAMES 'utf8'");
}

//--------------
$_SESSION['conexionsqli'] = new mysqli("localhost", "root", "", $_SESSION['BDD']);
if ($_SESSION['conexionsqli']) {
	$_SESSION['conexionsqli']->set_charset('utf8mb4');
}

//--------------

//-------PARA GUARDAR EL RECORRIDO
$valor = print_r($_SESSION, true);
$consulta_zzz = "INSERT INTO z_bitacora (usuario, variables, servidor, direccionurl, ip) VALUES (" . $_SESSION['CEDULA_USUARIO'] . ", '$valor','" . $_SERVER["HTTP_HOST"] . "','" . $_SERVER["REQUEST_URI"] . "', '" . $_SERVER['REMOTE_ADDR'] . "');";
//echo $consulta_zzz;
$tabla_zzz = mysql_query($consulta_zzz);
//----------

mysql_query("SET NAMES 'utf8'");

//---- RESPALDO DE LA BASE DE DATOS
include('backup/hacer_respaldo.php');
//---------------------------------
if ($_SESSION['ADMINISTRADOR'] == '1') {
	$_SESSION['ENCUESTA'] = 'NO';
	//----------------

}
