<?php
session_start();
include "../conexion.php";
//----------------
$info = array();
$tipo = 'info';
$id = $_POST['id']; 
$consulta_x = "SELECT id_articulo FROM alm_inventario WHERE id_articulo=$id and cantidad<>0"; 
$tabla_x = mysql_query ($consulta_x);
if (mysql_num_rows($tabla_x)>0)
	{$mensaje="El Articulo no se puede eliminar, el inventario debe estar en cero..."; $tipo='alerta';}
else
	{mysql_query("DELETE FROM alm_inventario WHERE id_articulo=$id");	$mensaje="Registro Eliminado Correctamente"; }
//--------------
$info = array ("msj"=>$mensaje, "tipo"=>$tipo);
echo json_encode($info);	
?>