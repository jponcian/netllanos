<?php
session_start(); 
//------------
include "../conexion.php";
include "../auxiliar.php";	//mantenimiento();

//------------
// ORIGEN DEL MODULO
$_SESSION['ORIGEN'] = 5;
include "../funciones/valida.php";
//------------
?>