<?php

include "conexion.php";

//VARIABLES
$rif = $_POST['rif'];
$mensaje="!!!... El Contribuyente NO se encuentra registrado ...!!!";
$permitido=false;
$info=array();

$consulta = "SELECT rif, contribuyente AS NombreRazon FROM contribuyentes WHERE rif='".$rif."'";
$tabla = $conexionsql->query($consulta);

if ($registro = $tabla->fetch_object())
{
	$nombre=$registro->NombreRazon;
	$mensaje="Contribuyente registrado";
	$permitido=true;
}
else
{
	$mensaje="!!!... El Contribuyente NO se encuentra registrado ...!!!";
	$permitido=false;
}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje,
				"nombrerazon"=>$nombre);

echo json_encode($info);


?>