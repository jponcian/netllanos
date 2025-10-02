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

BorrarRegistros($conexion, 'pf_allanamientos', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS
//$sqltipo = "CALL Allanamientos( $inicio, $fin )"
$sqltipo = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector, a_tipo_providencia.Siglas1 AS tipo_impuesto, expedientes_fiscalizacion.rif, concat(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,lpad(expedientes_fiscalizacion.numero,5,'0'),'/',fis_actas.anno,'/',lpad(fis_actas.numero,4,'0')) AS numacta, fis_actas.fecha AS fecha_emision, fis_actas.fecha_notificacion, Sum(fis_actas_detalle.impuesto_omitido) AS importe, a_tipo_programa.clasificacion, sum(fis_actas_detalle.monto_pagado) as monto_pagado, fis_actas_detalle.fecha_pago FROM fis_actas INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = fis_actas.id_sector AND expedientes_fiscalizacion.anno = fis_actas.anno_prov AND expedientes_fiscalizacion.numero = fis_actas.num_prov INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE fis_actas_detalle.fecha_pago BETWEEN '".$inicio."' AND '".$fin."' GROUP BY expedientes_fiscalizacion.sector, expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero"; 
//echo $sqltipo.'<br>';
$tablaActas = $con->query($sqltipo);
while ($reg = $tablaActas->fetch_object())
{
    $rif = $reg->rif;
    $numacta = $reg->numacta;

    if (substr($reg->tipo_impuesto, -1) == "/") 
	{
		$tributo = substr($reg->tipo_impuesto, 0,-1);
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
    
	if ($reg->monto_pagado == 0)
	{
		$actas_no_aceptada = 1;
		$actas_parcial_aceptada = 0;
		$actas_aceptada = 0;
	} else if ($reg->importe < $reg->monto_pagado) {
		$actas_no_aceptada = 0;
		$actas_parcial_aceptada = 1;
		$actas_aceptada = 0;
	} else {
		$actas_no_aceptada = 0;
		$actas_parcial_aceptada = 0;
		$actas_aceptada = 1;
	}

    $emision_acta = $reg->fecha_emision;
    $notificacion_acta = $reg->fecha_notificacion;
	$fecha_pago_acta = $reg->fecha_pago;

	$fecha_not_resolucion = fecha_not_resolucion($con, $reg->anno, $reg->numero, $reg->sector);

	$med_cautelar_sol = 0;
	$med_cautelar_acordada = 0;
    $impuesto = $reg->importe;

	//VERIFICAMOS SI YA ESTA REGISTRADA
	$existe = buscar($reg->numacta, $conexion);

	if ($existe == '')
	{

		$agregar = "INSERT INTO pf_allanamientos (rif, num_acta, tipo_impuesto, fisc_integral, fisc_interes_estrategico, fisc_interes_no_estrategico, actas_aceptada, actas_parcial_aceptada, actas_no_aceptada, emision, notificacion, pago, fecha_not_resolucion, med_cautelar_sol, med_cautelar_acordada, impuesto_determinado, periodo_inicio, periodo_fin) VALUES ('".$rif."', '".$numacta."', '".$tributo."', ".$fisc_integral.", ".$fisc_interes_estrategico.", ".$fisc_interes_no_estrategico.", ".$actas_aceptada.", ".$actas_parcial_aceptada.", ".$actas_no_aceptada.", '".$emision_acta."', '".$notificacion_acta."', '".$fecha_pago_acta."', '".$fecha_not_resolucion."', ".$med_cautelar_sol.", ".$med_cautelar_acordada.", ".$impuesto.", '".$inicio."', '".$fin."')";
	} else {
		$agregar = "UPDATE pf_allanamientos SET pago = '".$fecha_pago_acta."', fecha_not_resolucion = '".$fecha_not_resolucion."', importe = ".$impuesto.", periodo_inicio = '".$inicio."', periodo_fin = '".$fin."' WHERE num_acta = '".$existe."'";
	}
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

function fecha_not_resolucion($con, $anno, $numero, $sector)
{
	$sql = "SELECT liquidacion.fecha_not FROM liquidacion WHERE liquidacion.sector = ".$sector." AND liquidacion.anno_expediente = ".$anno." AND liquidacion.num_expediente = ".$numero." GROUP BY liquidacion.sector, liquidacion.anno_expediente, liquidacion.num_expediente";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$fecha_notificacion = $reg->fecha_not;
	return $fecha_notificacion;
}

function buscar($numacta, $conexion)
{
	$existe = '';
	$sql = "SELECT num_acta FROM pf_allanamientos WHERE num_acta = '".$numacta."'";
	$tabla = $conexion->query($sql);
	$reg = $tabla->fetch_object();
	$existe = $reg->num_acta;
	return $existe;
}

?>