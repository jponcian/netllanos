<?php
session_start(); 
//------------
include "../conexion.php";
include "../auxiliar.php";

// ORIGEN DEL MODULO
$_SESSION['ORIGEN'] = 12;
//------------
include "../funciones/valida.php";
//------------
?>