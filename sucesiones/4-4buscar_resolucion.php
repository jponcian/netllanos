<?php
include "../conexion.php";
include "../funciones/auxiliar_php.php";

mysql_query("SET NAMES 'utf8'");

//OBTENEMOS LOS DATOS
$sector= $_POST['sector'];
$anno= $_POST['anno'];
$numero= $_POST['numero'];

$info=array();
$encontrado = false;

//$resolucion = funcion_resolucion($sector,3,$anno,$numero);
$sql = "SELECT numero FROM resoluciones WHERE id_origen = 3 AND anno_expediente = ".$anno." AND num_expediente = ".$numero." AND id_sector = ".$sector;
$tabla = mysql_query($sql);
$reg = mysql_fetch_object($tabla);
$numresolucion = $reg->numero;

if ($numresolucion > 0)
{
	$encontrado = true;
	$num = $numresolucion;
} else {
	$encontrado = false;
	$num = 0;
}

$info = array("permitido" => $encontrado,
	"resolucion" => $num);

echo json_encode($info);

?>
