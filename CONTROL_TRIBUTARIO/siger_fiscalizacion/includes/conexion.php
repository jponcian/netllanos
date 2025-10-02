<?php
//CONEXION A MYSQL con MySQLi
set_time_limit(0);

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'db_siger_fiscalizacion';

//Conectamos a MySQLi	
$conexion = new mysqli($hostname, $username,$password, $database);
$conexion->query("SET NAMES 'utf8'");

//Establecemos la conexion a la base de datos
$con = new mysqli($hostname, $username,$password, "losllanos");
$con->query("SET NAMES 'utf8'");

?>