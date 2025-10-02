<?php
session_start();

include "../conexion.php";
include "../funciones/auxiliar_php.php";


$txt100 = $_POST['txt100'];
$txt101 = $_POST['txt101'];
$txt102 = $_POST['txt102'];
$txt103 = $_POST['txt103'];
$txt104 = $_POST['txt104'];
$txt105 = $_POST['txt105'];
$txt106 = $_POST['txt106'];
$txt107 = $_POST['txt107'];
$txt108 = $_POST['txt108'];

$json = array();
$mensaje = "";
$permitido = false;

$añoprov= $_POST['añoProvidencia'];
$numprov= $_POST['numProvidencia'];

//BUSCAMOS EL SECTOR
$sqlsector = "SELECT id_sector, nombre FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
$result_sector = mysql_query($sqlsector);
$valor_sector = mysql_fetch_object($result_sector);
$sector = $valor_sector->nombre;
//-------------------------------

$sql = "INSERT INTO fis_anexo2_art (Anno, NroAutorizacion, Sede, art100, art101, art102, art103, art104, art105, art106, art107, art108) VALUES  ('".$añoprov."','".$numprov."','".$sector."','".$txt100."','".$txt101."','".$txt102."','".$txt103."','".$txt104."','".$txt105."','".$txt106."','".$txt107."','".$txt108."')";

if($tabla = mysql_query($sql))
{
    if (mysql_result)
    {
		$mensaje = '!!!...Registo guardado con exito...!';
    	$permitido = true;
    }
} else {
	$mensaje = '!!!...Problemas al guardar el registro Anexo 2 Articulado...!';
	$permitido = false;
}

$json = array('permitido' => $permitido,
				'mensaje' => $mensaje);

echo json_encode($json);        

?>