<?php
session_start(); 
//------------
include "../conexion.php";
include "../funciones/auxiliar_php.php";

// ORIGEN DEL MODULO
$_SESSION['ORIGEN'] = 4;
//------------
include "../funciones/valida.php";
//------------
?>