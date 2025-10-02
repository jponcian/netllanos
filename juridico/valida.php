<?php
session_start(); 
//------------
include "../conexion.php";
include "../auxiliar.php";	//
//mantenimiento();

//------------
// ORIGEN DEL MODULO
$_SESSION['ORIGEN'] = 6;
include "../funciones/valida.php";
//------------
?>