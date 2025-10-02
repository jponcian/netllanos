<?php session_start();
include "../conexion.php";
include "../funciones/auxiliar_php.php";

mysql_query("SET NAMES 'utf8'");

//OBTENEMOS LOS DATOS
$rif=strtoupper($_POST['rif']);
$fechareg=voltea_fecha($_POST['fechareg']);
$fechafall=voltea_fecha($_POST['fechafall']); 
$ced=strtoupper($_POST['ced']);
$sucesion=strtoupper($_POST['sucesion']); 
$coord=$_POST['coord']; 
$func=$_POST['func'];
$accion=$_POST['accion'];
$sector=$_POST['sector'];
$anno=$_POST['anno'];
$numero=$_POST['numero'];
$indice=$_POST['indice'];
$info=array();
$procesado = false;
$mensaje = "Error al procesar el registro";
if ($accion == "Modificar")
{
	$consulta_x = "UPDATE expedientes_sucesiones SET anno=".$anno.", numero=".$numero.", funcionario=".$func.", rif='".$rif."', coordinador=".$coord.", usuario=".$anno.", sector=".$sector.", cedula='".$ced."', sucesion='".$sucesion."', fecha_fall='".$fechafall."' WHERE indice=".$indice.";";
}
else
{
	$consulta_x = "INSERT INTO expedientes_sucesiones (anno, numero, funcionario, rif, fecha_registro, coordinador, usuario,sector, status, cedula, sucesion, fecha_fall ) VALUES (".$anno.", ".$numero.", ".$func.", '".$rif."', date(now()), ".$coord.", ".$_SESSION['CEDULA_USUARIO'].", ".$sector.", 0, '".$ced."', '".$sucesion."', '".$fechafall."');";
	
}

if ($tabla_x = mysql_query($consulta_x))
{
	$procesado=true;
	if ($accion == "Modificar")
	{
		$mensaje = "!!!...Expediente actualizado satisfactoriamente...!!!";
	} else {
		$mensaje = "Expediente registrado satisfactoriamente";
	}
}
else
{
	$procesado=false;
}	

$info = array("procesado" => $procesado,
	"mensaje" => $mensaje);

echo json_encode($info);

?>
