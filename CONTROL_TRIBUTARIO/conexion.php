<?php
session_start();
//PARA CONECTAR A LA BASE DE DATOS CON MYSQLI

//Creamos la conexion

//Variables a utilizar
if ($_SESSION['ADMINISTRADOR']>0)
	{
	if (trim($_SESSION['BDD'])=='')
		{	echo 'Error al seleccionar la base de datos!';}
	else
		{	
		$database=$_SESSION['BDD']; 
		}
	}
else
	{
	$database = 'losllanos';
	}

	$hostname = 'localhost';
	$username = 'root';
	$password = '';
	
	//Conectamos a MySQLi	
	$conexionsql = new mysqli($hostname, $username,$password, $database);
	//$siger = new mysqli($hostname, $username,$password, $databasesiger);
	
	//Evitar problemas con los acentos y ñ
	$conexionsql->query("SET NAMES 'utf8'");
	//$siger->query("SET NAMES 'utf8'");
?>