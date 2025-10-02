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
$año = date("Y");

BorrarRegistros($conexion, 'operativos', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS
$sqltipo = "SELECT
a_tipo_programa.id_programa,
a_tipo_programa.tipo,
a_tipo_programa.clasificacion,
a_tipo_programa.descripcion AS programa,
z_sectores.nombre AS sector,
CONCAT_WS('-', expedientes_fiscalizacion.anno, LPAD(expedientes_fiscalizacion.numero, 3, '0')) AS providencia,
a_tipo_providencia.Siglas1 AS impuesto,
expedientes_fiscalizacion.fecha_emision AS emision,
expedientes_fiscalizacion.fecha_notificacion AS notificacion,
contribuyentes.contribuyente,
contribuyentes.rif,
CONCAT_WS(' ', z_empleados.Nombres, z_empleados.Apellidos) AS fiscal,
expedientes_fiscalizacion.anno,
expedientes_fiscalizacion.numero,
expedientes_fiscalizacion.fecha_conclusion
FROM
expedientes_fiscalizacion
INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector
INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa
INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo
INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif
INNER JOIN z_empleados ON z_empleados.cedula = expedientes_fiscalizacion.ci_fiscal1
WHERE
expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."'";
//echo $sqltipo.'<br>';
$tabla_tipo = $con->query($sqltipo);
while ($reg = $tabla_tipo->fetch_object())
{

	$tipo_operativo = tipo_operativo($reg->clasificacion);
	$programa = $reg->programa;
	$sector = $reg->sector;
	$providencia = $reg->providencia;
	$impuesto = $reg->impuesto;
	if (substr($impuesto, -1) == "/") 
	{
		$impuesto = substr($impuesto, 0,-1);
	}
	$emision = $reg->emision;
	$notificacion = $reg->notificacion;
	$contribuyente = $reg->contribuyente;
	$rif = $reg->rif;
	$sector_comercial = buscar_sector($con, $reg->anno, $reg->numero, $reg->sector);
	$fiscal = $reg->fiscal;
	$maquina_fiscal = posee_maquina($con, $reg->anno, $reg->numero, $reg->sector);
	$sancionado = operativos_sancionado($con, $reg->anno, $reg->numero, $reg->sector);
	$clausurado = operativos_clausurado($con, $reg->anno, $reg->numero, $reg->sector);
	$produccion_potencial = operativos_pp($con, $reg->anno, $reg->numero, $reg->sector);
	$produccion_efectiva = prod_efect_operativos($con, $reg->anno, $reg->numero, $reg->sector, $reg->tipo);
	$produccion_potencial = round($produccion_potencial, 2);
	$produccion_efectiva = round($produccion_efectiva, 2);
	if ($reg->fecha_conclusion <> Null)
	{
		if ($produccion_potencial > 0)
		{
			$sancionado = "x";
			$conforme = "";
		} else {
			$sancionado = "";
			$conforme = "x";
		}
	} else {
		$sancionado = "";
		$conforme = "";
	}

	if ($reg->fecha_conclusion <> Null)
	{
		$proceso = "";
	} else {
		$proceso = "x";
	}
	
	/*
	$lista = $programa." - ".$sector." - ".$providencia." - ".$impuesto." - ".$emision." - ".$notificacion." - ".$contribuyente." - ".$rif." - ".$sector_comercial." - ".$fiscal." - ".$sanciona." - ".$conforme." - ".$sanciona." - ".$conforme." - ".$proceso." - ".$reparo." - ".$impuesto_omitido." - ".$intereses." - ".$multas." - ".$allanado_total." - ".$allanado_parcial;
	echo $lista.'<br>';
	*/	

	//AGREGAMOS EL REGISTRO
	if ($programa != "")
	{

		$insert = "INSERT INTO operativos (tipo_operativo, programa, sector, num_prov, impuesto, emision_prov, notificacion_prov, sp_nombre, sp_rif, sp_sector_econ, fiscal_actuantes, maquina_fiscal, sancionado, clausurado, conforme, proceso, produccion_potencial, produccion_efectiva, periodo_inicio, periodo_fin) VALUES ('".$tipo_operativo."', '".$programa."', '".$sector."', '".$providencia."', '".$impuesto."', '".$emision."', '".$notificacion."', '".$contribuyente."', '".$rif."', '".$sector_comercial."', '".$fiscal."', '".$maquina_fiscal."', '".$sancionado."', '".$clausurado."', '".$conforme."', '".$proceso."', ".$produccion_potencial.", ".$produccion_efectiva.", '".$inicio."', '".$fin."')";
		//echo $insert.'<br>';
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