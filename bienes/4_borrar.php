<?php
session_start();
include "../conexion.php";
$f = 0;
$consulta = "SELECT * FROM vista_bienes_reasignaciones_pendientes WHERE borrado=0 AND por_reasignar=1 ORDER BY descripcion_bien, numero_bien";
$tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);
while ($registro = mysqli_fetch_object($tabla)) {
	if ($_POST[$registro->id_bien] == $registro->id_bien && $_POST['CMDELIMINAR'] == 'Eliminar') {
		$consulta_a = "DELETE FROM bn_bienes_x_reasignar WHERE id_bien=" . $registro->id_bien . ";";
		mysqli_query($_SESSION['conexionsqli'], $consulta_a);
		$consulta_a = "UPDATE bn_bienes SET por_reasignar=0 WHERE id_bien=" . $registro->id_bien . ";";
		mysqli_query($_SESSION['conexionsqli'], $consulta_a);
		$f++;
	}
}

//-------------- 
