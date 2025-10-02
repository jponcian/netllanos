<?php
session_start(); 
//------------
include "../conexion.php";
include "../auxiliar.php";

// ORIGEN DEL MODULO
$_SESSION['ORIGEN'] = 3;
//------------
include "../funciones/valida.php";
//------------
?>