<?php
include "../conexion.php";
include "../funciones/auxiliar_php.php";

mysql_query("SET NAMES 'utf8'");

//OBTENEMOS LOS DATOS
$rif= $_POST['rif'];
$info=array();
$encontrado = false;

$consulta_x = "SELECT rif, contribuyente, direccion FROM vista_contribuyentes_direccion WHERE rif='".$rif."';";
$tabla_x = mysql_query($consulta_x);
if ($registro_x = mysql_fetch_object($tabla_x))
{
	$encontrado=true;
	$rif = $registro_x->rif;
	$contribuyente = $registro_x->contribuyente;
	$direccion = $registro_x->direccion;
}
else
{
	$encontrado = false;
	$rif = "";
	$contribuyente = "";
	$direccion = "";
}	

$info = array("permitido" => $encontrado,
	"rif" => $rif,
	"contribuyente" => $contribuyente,
	"direccion" => $direccion);

echo json_encode($info);

?>
