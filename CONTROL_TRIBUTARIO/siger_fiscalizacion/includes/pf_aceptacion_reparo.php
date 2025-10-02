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
$mensaje = "No se encontraron Resoluciones pagadas en el periodo";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];
$año = date("Y");

BorrarRegistros($conexion, 'pf_aceptacion_reparo', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS
$sqltipo = "SELECT
expedientes_fiscalizacion.anno AS anno_prov,
expedientes_fiscalizacion.numero AS num_prov,
expedientes_fiscalizacion.sector AS sector_prov,
expedientes_fiscalizacion.rif,
concat(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,lpad(expedientes_fiscalizacion.numero,5,'0'),'/',fis_actas.anno,'/',lpad(fis_actas.numero,4,'0')) AS numacta,
fis_actas.fecha_notificacion,
a_tipo_programa.clasificacion,
fis_actas.acta,
fis_actas_detalle.impuesto_omitido,
fis_actas_detalle.fecha_pago,
fis_actas_detalle.multa_actual,
fis_actas_detalle.interes,
concat(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,lpad(expedientes_fiscalizacion.numero,5,'0'),'/',resoluciones.anno,'/',lpad(resoluciones.numero,4,'0')) AS numresolucion,
resoluciones.fecha AS fecha_resol,
resoluciones.fecha_liq_not,
resoluciones.fecha_liq_pago
FROM fis_actas INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = fis_actas.id_sector AND expedientes_fiscalizacion.anno = fis_actas.anno_prov AND expedientes_fiscalizacion.numero = fis_actas.num_prov INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector INNER JOIN resoluciones ON resoluciones.id_sector = fis_actas.id_sector AND resoluciones.anno_expediente = fis_actas.anno_prov AND resoluciones.num_expediente = fis_actas.num_prov
WHERE resoluciones.fecha_liq_pago <> '0000-00-00' AND resoluciones.fecha_liq_pago BETWEEN '".$inicio."' AND '".$fin."' AND
fis_actas.acta = 0
GROUP BY fis_actas.id_sector, fis_actas.anno_prov, fis_actas.num_prov"; 
//echo $sqltipo.'<br>';
$tablaActas = $con->query($sqltipo);
while ($reg = $tablaActas->fetch_object())
{
    $rif = $reg->rif;
    $numacta = $reg->numacta;
    $notificacion = $reg->fecha_notificacion;
	if ($reg->fecha_pago == null or $reg->fecha_pago == '0000-00-00')
	{
		$fecha_pago = $notificacion;
	} else {
		$fecha_pago = $reg->fecha_pago;
	}
    
	if ($reg->clasificacion === 'FIN' or $reg->clasificacion === 'FIR') 
	{ 
		$fisc_integral = 1; 
		$fisc_interes_estrategico = 0; 
		$fisc_interes_no_estrategico = 0; 
	} else if ($reg->clasificacion === 'AB') { 
		$fisc_integral = 0; 
		$fisc_interes_estrategico = 0; 
		$fisc_interes_no_estrategico = 1; 
	} else {
		$fisc_integral = 0; 
		$fisc_interes_estrategico = 1; 
		$fisc_interes_no_estrategico = 0; 
	}

    $notificacion_res = $reg->fecha_liq_not;
    $pago_res = $reg->fecha_liq_pago;
    $num_resolucion = $reg->numresolucion;
    $emision_res = $reg->fecha_resol;
    $impuesto_determinado = round($reg->impuesto_omitido, 2);
    $multa_vdf = round(multas_df($con, $reg->anno_prov, $reg->num_prov, $reg->sector_prov), 2);
    $intereses = round($reg->interes, 2);
    $otras_multas = round($reg->multa_actual, 2);

	$agregar = "INSERT INTO pf_aceptacion_reparo (rif, num_acta, notificacion, pago, fisc_integral, fisc_interes_estrategico, fisc_interes_no_estrategico, num_resolucion, emision_res, notificacion_res, pago_res, impuesto_determinado, multa_vdf, intereses, otras_multas, periodo_inicio, periodo_fin)
	VALUES ('".$rif."', '".$numacta."', '".$notificacion."', '".$fecha_pago."', ".$fisc_integral.", ".$fisc_interes_estrategico.", ".$fisc_interes_no_estrategico.", '".$num_resolucion."', '".$emision_res."', '".$notificacion_res."', '".$pago_res."', ".$impuesto_determinado.", ".$multa_vdf.", ".$intereses.", ".$otras_multas.", '".$inicio."', '".$fin."')";
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


function multas_df($con, $anno, $numero, $sector)
{
    $sqlmulta = "SELECT Multas FROM vista_ct_multas WHERE anno_expediente = ".$anno." AND num_expediente = ".$numero." AND sector = ".$sector;
    $tabla_multa = $con->query($sqlmulta);
    $regm = $tabla_multa->fetch_object();
    $monto = $regm->Multas;
    return $monto;
}

function notificacion($con, $anno, $numero, $sector)
{
	$sql = "SELECT liquidacion.fecha_not FROM liquidacion WHERE liquidacion.sector = ".$sector." AND liquidacion.anno_expediente = ".$anno." AND liquidacion.num_expediente = ".$numero." GROUP BY liquidacion.sector, liquidacion.anno_expediente, liquidacion.num_expediente";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$fecha_notificacion = $reg->fecha_not;
	return $fecha_notificacion;
}

function pago($con, $anno, $numero, $sector)
{
	$sql = "SELECT liquidacion.fecha_pag FROM liquidacion WHERE liquidacion.sector = ".$sector." AND liquidacion.anno_expediente = ".$anno." AND liquidacion.num_expediente = ".$numero." GROUP BY liquidacion.sector, liquidacion.anno_expediente, liquidacion.num_expediente";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$fecha_pago = $reg->fecha_pag;
	return $fecha_pago;
}

?>