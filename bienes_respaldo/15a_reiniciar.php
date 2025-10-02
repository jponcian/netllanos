<?php
session_start();
include_once "../conexion.php";
include_once "../auxiliar.php";
//----------------
$id = ($_POST['id']);
if ($id > 0) {
	$filtro = ' WHERE id_area=' . $id;
} else {
	$filtro = '';
}
//-------
$consultx = "UPDATE bn_bienes SET revisado = 0 $filtro;"; // echo $id;
$tablx = $_SESSION['conexionsqli']->query($consultx);
?>