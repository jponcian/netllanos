<?php
session_start();
include "../conexion.php";
//----------------
$id = $_POST['id']; 
mysql_query("DELETE FROM alm_solicitudes_detalle_tmp WHERE id_detalle=$id");	
?>