<?php
session_start(); 
//------------
include "../conexion.php";
include "../auxiliar.php";

// ORIGEN DEL MODULO
$_SESSION['ORIGEN'] = 2;
//------------
include "../funciones/valida.php";
//------------
?>