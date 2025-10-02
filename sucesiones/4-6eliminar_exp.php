<?php session_start();
include "../conexion.php";
include "../auxiliar.php";

//OBTENEMOS LOS DATOS
$indice=$_POST['indice'];
$info=array();
$procesado = false;
$mensaje = "Error al procesar el registro";

$consulta_x = "DELETE FROM expedientes_sucesiones WHERE indice=".$indice.";";

if ($tabla_x = mysql_query($consulta_x))
{
	$procesado=true;
	$mensaje = "!!!...Expediente eliminado satisfactoriamente...!!!";
}
else
{
	$procesado=false;
}	

$info = array("procesado" => $procesado,
	"mensaje" => $mensaje);

echo json_encode($info);

?>
