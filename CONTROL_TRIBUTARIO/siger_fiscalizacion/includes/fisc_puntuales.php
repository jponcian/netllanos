<?php
//*****SCRIPT PARA GENERAR EL INFORME DE GESTION*****//
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
$año = date("Y");

BorrarRegistros($conexion, 'fiscalizaciones_puntuales', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS
/*$sqltipo = "SELECT
a_tipo_programa.clasificacion as clasificacion,
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
expedientes_fiscalizacion.fecha_conclusion,
vista_actas_siger.fecha_notificacion as notificacion_acta,
vista_actas_siger.acta as tipo_acta,
vista_actas_siger.reparo,
vista_actas_siger.impuesto_omitido,
vista_actas_siger.multa_actual as multa_reparo,
vista_actas_siger.interes,
vista_actas_siger.ajuste_voluntario
FROM
expedientes_fiscalizacion
INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector
INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa
INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo
INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif
INNER JOIN z_empleados ON z_empleados.cedula = expedientes_fiscalizacion.ci_fiscal1
INNER JOIN vista_actas_siger ON vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero AND vista_actas_siger.sector = expedientes_fiscalizacion.sector
WHERE expedientes_fiscalizacion.anno = ".$año." AND ((vista_actas_siger.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND
expedientes_fiscalizacion.fecha_conclusion = '0000-00-00') OR
(expedientes_fiscalizacion.fecha_conclusion BETWEEN '".$inicio."' AND '".$fin."'))";*/

//$sqltipo = "CALL RPT_Fiscalizaciones_Puntuales( $año, $inicio, $fin )";
$sqltipo = "SELECT
a_tipo_programa.clasificacion as clasificacion,
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
expedientes_fiscalizacion.fecha_conclusion,
vista_actas_siger.fecha_notificacion as notificacion_acta,
vista_actas_siger.acta as tipo_acta,
vista_actas_siger.reparo,
vista_actas_siger.impuesto_omitido,
vista_actas_siger.multa_actual as multa_reparo,
vista_actas_siger.interes,
vista_actas_siger.ajuste_voluntario
FROM
expedientes_fiscalizacion
INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector
INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa
INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo
INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif
INNER JOIN z_empleados ON z_empleados.cedula = expedientes_fiscalizacion.ci_fiscal1
LEFT JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero
WHERE
expedientes_fiscalizacion.anno = ".$año." AND
(a_tipo_programa.clasificacion = 'FPN' OR
a_tipo_programa.clasificacion = 'FPR') AND
(expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' OR
expedientes_fiscalizacion.fecha_conclusion BETWEEN '".$inicio."' AND '".$fin."')";

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
	$sancionado = "";
	$conforme = "";
	if ($reg->fecha_conclusion <> Null)
	{
		if ($reg->tipo_acta == 0 or $reg->tipo_acta == 2)
		{
			$sancionado = "x";
			$conforme = "";
		} else {
			$sanciona = "";
			$conforme = "x";		
		}
	}
	if ($reg->fecha_conclusion <> Null)
	{
		$proceso = "";
	} else {
		$proceso = "x";
	}
	$reparo = round($reg->reparo, 2);
	$impuesto_omitido = round($reg->impuesto_omitido, 2);
	$intereses = round($reg->interes, 2);
	$multas = round($reg->multa_reparo, 2);
	if ($reg->ajuste_voluntario < $reg->impuesto_omitido)
	{
		$allanado_total = 0;
		$allanado_parcial = round($reg->ajuste_voluntario, 2);		
	} else {
		$allanado_total = round($reg->ajuste_voluntario, 2);
		$allanado_parcial = 0;				
	}
	$not_acta = $reg->notificacion_acta;
	$conclusion = $reg->fecha_conclusion;
	
	/*
	$lista = $programa." - ".$sector." - ".$providencia." - ".$impuesto." - ".$emision." - ".$notificacion." - ".$contribuyente." - ".$rif." - ".$sector_comercial." - ".$fiscal." - ".$sanciona." - ".$conforme." - ".$sanciona." - ".$conforme." - ".$proceso." - ".$reparo." - ".$impuesto_omitido." - ".$intereses." - ".$multas." - ".$allanado_total." - ".$allanado_parcial;
	echo $lista.'<br>';
	*/	

	//AGREGAMOS EL REGISTRO
	if ($programa != "")
	{
		
		$insert = "INSERT INTO fiscalizaciones_puntuales (tipo_operativo, programa, sector, num_prov, impuesto, emision_prov, notificacion_prov, sp_nombre, sp_rif, sp_sector_econ, fiscal_actuantes, sancionado, conforme, proceso, reparo, impuesto_omitido, intereses, multas, allanado_total, allanado_parcial, notificacion_acta, conclusion, periodo_inicio, periodo_fin) VALUES ('".$tipo_operativo."', '".$programa."', '".$sector."', '".$providencia."', '".$impuesto."', '".$emision."', '".$notificacion."', '".$contribuyente."', '".$rif."', '".$sector_comercial."', '".$fiscal."', '".$sancionado."', '".$conforme."', '".$proceso."', ".$reparo.", ".$impuesto_omitido.", ".$intereses.", ".$multas.", ".$allanado_total.", ".$allanado_parcial.", '".$not_acta."', '".$conclusion."', '".$inicio."', '".$fin."')";
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