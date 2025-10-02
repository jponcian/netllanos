<?php
//*****       SCRIPT PARA RIS NOTIFICADAS       *****//
//                                                   //
//    Elaborado por Gustavo García para el SENIAT    //
//                                                   //
///////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");

//mysql_query("SET NAMES 'utf8'");

//Variables a utilizar
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];

BorrarRegistros($conexion, 'sg_fuerza_fiscal', $inicio, $fin);

//FISCALES ACTUANTES
$sqlFiscales = "SELECT count(DISTINCTROW ci_fiscal1) as cantidad, sector FROM expedientes_fiscalizacion WHERE expedientes_fiscalizacion.fecha_emision BETWEEN '".$inicio."' AND '".$fin."' group by sector";
$tablaFiscales = $con->query($sqlFiscales);
while ($fiscal = $tablaFiscales->fetch_object())
{
	$agregar = "INSERT INTO sg_fuerza_fiscal (descripcion, sector, activo, reposo, vacacion, traslado, comision, periodo_inicio, periodo_fin) VALUES ('FISCALES', ".$fiscal->sector.", ".$fiscal->cantidad.", 0, 0, 0, 0,'".$inicio."','".$fin."')";
	$tablaAgregar = $conexion->query($agregar);
}

//SUPERVISORES
$sqlSupervisores = "SELECT count(DISTINCTROW ci_supervisor) as cantidad, sector FROM expedientes_fiscalizacion WHERE expedientes_fiscalizacion.fecha_emision BETWEEN '".$inicio."' AND '".$fin."' group by sector";
$tablaSupervisores = $con->query($sqlSupervisores);
while ($super = $tablaSupervisores->fetch_object())
{
	$agregar = "INSERT INTO sg_fuerza_fiscal (descripcion, sector, activo, reposo, vacacion, traslado, comision, periodo_inicio, periodo_fin) VALUES ('SUPERVISORES', ".$super->sector.", ".$super->cantidad.", 0, 0, 0, 0,'".$inicio."','".$fin."')";
	//echo $agregar.'<br>';
    $tablaAgregar = $conexion->query($agregar);

	if ($conexion->affected_rows){
		$mensaje = "Informe Generado Satisfactoriamente";
		$permitido = true;
	}
}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

?>