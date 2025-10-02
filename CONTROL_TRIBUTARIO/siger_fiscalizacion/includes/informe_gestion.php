<?php
//*****SCRIPT PARA GENERAR EL INFORME DE GESTION*****//
//                                                   //
//    Elaborado por Gustavo García para el SENIAT    //
//                                                   //
///////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");

//Variables a utilizar
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];

BorrarRegistros($conexion, 'informe_gestion', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS
$sqltipo = "SELECT descripcion, id_programa, tipo, clasificacion FROM a_tipo_programa";
$tabla_tipo = $con->query($sqltipo);
while ($reg = $tabla_tipo->fetch_object())
{
	switch ($reg->clasificacion) 
	{
		case "FIN":
			$clasificacion = "FISCALIZACION INTEGRAL NACIONAL";
			break;
		case "FIR":
			$clasificacion = "FISCALIZACION INTEGRAL REGIONAL";
			break;
		case "FPN":
			$clasificacion = "FISCALIZACION PUNTUAL NACIONAL";
			break;
		case "FPR":
			$clasificacion = "FISCALIZACION PUNTUAL REGIONAL";
			break;
		case "VN":
			$clasificacion = "VERIFICACION NACIONAL";
			break;
		case "VR":
			$clasificacion = "VERIFICACION REGIONAL";
			break;
		case "OP":
			$clasificacion = "OTROS PROGRAMAS";
			break;
	}
	
	$descripcion = $reg->descripcion;
	$fiscales = cantidad_fiscales($con, $inicio, $fin,$reg->id_programa, $reg->tipo);
	$visitados = visitados($con, $inicio, $fin, $reg->id_programa, $reg->tipo);
	$multados = multados($con, $inicio, $fin, $reg->id_programa, $reg->tipo);
	$clausurados = clausurados($con, $inicio, $fin, $reg->id_programa, $reg->tipo);
	if ($clausurados > $multados) { $clausurados = $multados; }
	$iniciadas = iniciados($con, $inicio, $fin, $reg->id_programa, $reg->tipo);
	$actas_notificadas = actas($con, $inicio, $fin, $reg->id_programa, $reg->tipo);
	$prod_potencial = prod_potencial($con, $inicio, $fin, $reg->id_programa, $reg->tipo);
	$prod_potencial = round($prod_potencial, 2);
	$prod_efectiva = prod_efectiva($con, $inicio, $fin, $reg->id_programa, $reg->tipo);
	$prod_efectiva = round($prod_efectiva, 2);

	//AGREGAMOS EL REGISTRO
	if ($fiscales > 0 AND $reg->clasificacion <> 'OP')
	{

		$insert = "INSERT INTO informe_gestion (clasificacion, descripcion, fiscales, visitados, multados, clausurados, iniciadas, actas_notificadas, prod_potencial, prod_efectiva, periodo_inicio, periodo_fin) VALUES ('".$clasificacion."', '".$descripcion."', ".$fiscales.", ".$visitados.", ".$multados.", ".$clausurados.", ".$iniciadas.", ".$actas_notificadas.", ".$prod_potencial.", ".$prod_efectiva.", '".$inicio."', '".$fin."')";
		$result = $conexion->query($insert);
        if ($conexion->affected_rows){
            $mensaje = "Informe Generado Satisfactoriamente";
            $permitido = true;
        }
	}
}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

?>