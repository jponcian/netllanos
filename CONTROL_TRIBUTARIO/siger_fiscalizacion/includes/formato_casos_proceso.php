<?php
//*****SCRIPT PARA GENERAR EL INFORME MENSUAL********//
//                                                   //
//    Elaborado por Gustavo García para el SENIAT    //
//                                                   //
///////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");
include("../funciones/func.contador.php");

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
$año = date("Y");

BorrarRegistros($conexion, 'formato_casos_proceso', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS

$sqltipo = "SELECT anno, formato_cp as desc_formato, SUM(proceso_auditoria) AS proceso_auditoria, SUM(nivel_supervisor) AS nivel_supervisor, SUM(lapso_allanamiento) AS lapso_allanamiento, SUM(otras_causas) AS otras_causas FROM casos_en_proceso WHERE borrado = 0 GROUP BY anno, formato_cp";
//echo $sqltipo.'<br>';
$programa = "";
$tabla_tipo = $conexion->query($sqltipo);
while ($reg = $tabla_tipo->fetch_object())
{

    $insert = "INSERT INTO formato_casos_proceso (anno, programa, proceso_auditoria, nivel_supervisor, lapso_allanamiento, otras_causas, periodo_inicio, periodo_fin) VALUES (
    '".$reg->anno."', 
    '".$reg->desc_formato."', 
    ".$reg->proceso_auditoria.", 
    ".$reg->nivel_supervisor.", 
    ".$reg->lapso_allanamiento.", 
    ".$reg->otras_causas.", 
    '".$inicio."', 
    '".$fin."')";
    //echo $insert.'<br>';
    $result = $conexion->query($insert);

	if ($conexion->affected_rows){
		$mensaje = "Informe Generado Satisfactoriamente";
		$permitido = true;
	}

}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

?>