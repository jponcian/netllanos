<?php
session_start();
include_once "../conexion.php";
include_once "../auxiliar.php";
//----------------
$id = ($_POST['id']);
$revisado = ($_POST['revisado']);
if ($revisado == 0) {
	$consultx = "UPDATE bn_bienes SET revisado = 1 WHERE id_bien=$id;";
} else {
	$consultx = "UPDATE bn_bienes SET revisado = 0 WHERE id_bien=$id;";
}
//-------
$tablx = $_SESSION['conexionsqli']->query($consultx);
?>