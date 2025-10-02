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

$consulta_x = "SELECT * FROM expedientes_sucesiones WHERE numero=0".$numero." AND anno=0".$anno." and sector=".$sector.";";
$tabla_x = mysql_query($consulta_x);
if ($registro_x = mysql_fetch_object($tabla_x))
{
	$encontrado=true;
	$fecha_registro = voltea_fecha($registro_x->fecha_registro);
	$rif = $registro_x->rif;
	$fecha_fall = voltea_fecha($registro_x->fecha_fall);
	$cedula = $registro_x->cedula;
	$sucesion = $registro_x->sucesion;
	$coordinador = $registro_x->coordinador;
	$funcionario = $registro_x->funcionario;
	$indice = $registro_x->indice;
}
else
{
	$encontrado = false;
	$fecha_registro = "";
	$rif = "";
	$fecha_fall = "";
	$cedula = "";
	$sucesion = "";
	$coordinador = "";
	$funcionario = "";
	$indice = "";
}	

$info = array("permitido" => $encontrado,
	"fecha_registro" => $fecha_registro,
	"rif" => $rif,
	"fecha_fall" => $fecha_fall,
	"cedula" => $cedula,
	"sucesion" => $sucesion,
	"coordinador" => $coordinador,
	"funcionario" => $funcionario,
	"indice" => $indice);

echo json_encode($info);

?>
