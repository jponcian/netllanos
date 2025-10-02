<?php

//CONECTAR A LA BD
include "conexion.php";
$id = 1;
$nombre = "INVERSIONES KEYLAY, C.A.";
$query = "UPDATE destruccion_facturas SET nombre=? WHERE id=?";
$sentencia = $conexionsql->prepare($query);

$sentencia->bind_param('si', $nombre,$id);

if ($sentencia->execute())
{
	$validar="";
}
else
{
	echo "Error: Fallo ejecucion";
}

$registros = "SELECT nombre FROM destruccion_facturas WHERE id=1";
$resultregistros = $conexionsql->query($registros);
$valor = $resultregistros->fetch_object();

echo $valor->nombre;
echo '<br/>';
echo date("A");
?>

