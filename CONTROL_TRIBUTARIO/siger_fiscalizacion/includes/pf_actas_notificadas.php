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

BorrarRegistros($conexion, 'pf_actas_notificadas', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS
$sqltipo = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector, expedientes_fiscalizacion.rif, concat(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,lpad(expedientes_fiscalizacion.numero,5,'0'),'/',fis_actas.anno,'/',lpad(fis_actas.numero,4,'0')) AS numacta, a_tipo_providencia.Siglas1 as impuesto, a_tipo_programa.clasificacion, fis_actas.acta, fis_actas.fecha, fis_actas.fecha_notificacion, fis_actas_detalle.impuesto_omitido, fis_actas_detalle.multa_actual, fis_actas_detalle.interes FROM fis_actas INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = fis_actas.id_sector AND expedientes_fiscalizacion.anno = fis_actas.anno_prov AND expedientes_fiscalizacion.numero = fis_actas.num_prov INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector WHERE fis_actas.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' GROUP BY fis_actas.id_sector, fis_actas.anno_prov, fis_actas.num_prov"; 
//echo $sqltipo.'<br>';
$tablaActas = $con->query($sqltipo);
while ($reg = $tablaActas->fetch_object())
{
    $rif = $reg->rif;
    $numacta = $reg->numacta;
    
	if ($reg->clasificacion === 'FIN' or $reg->clasificacion === 'FPN') 
	{ 
		$tipo_programacion = 0; 
	} else {
		$tipo_programacion = 1; 		
	}

    if (substr($reg->impuesto, -1) == "/") 
	{
		$tributo = substr($reg->impuesto, 0,-1);
	}

	if ($reg->acta == 0) 
	{ 
		$reparo_not = 1;
		$reparo_inf = 0;
		$conformidad = 0;
	} elseif ($reg->acta == 1) {
		$reparo_not = 0;
		$reparo_inf = 0;
		$conformidad = 1;		
	} else {
		$reparo_not = 0;
		$reparo_inf = 1;
		$conformidad = 0;
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

    $emision = $reg->fecha;
    $notificacion = $reg->fecha_notificacion;
    $med_cautelar_sol = "";
    $med_cautelar_acordada = "";
    $impuesto = round($reg->impuesto_omitido, 2);
    $multas = round(multas_df($con, $reg->anno, $reg->numero, $reg->sector), 2);
    $intereses = round($reg->interes, 2);
    $otras_multas = round($reg->multa_actual, 2);

	$agregar = "INSERT INTO pf_actas_notificadas (rif, num_acta, tipo_programacion, tipo_impuesto, reparo_not, reparo_inf, conformidad, fisc_integral, fisc_interes_estrategico, fisc_interes_no_estrategico, emision, notificacion, med_cautelar_sol, med_cautelar_acordada, impuesto, multa, intereses, otras_multas, periodo_inicio, periodo_fin) VALUES ('".$rif."', '".$numacta."', ".$tipo_programacion.", '".$tributo."', ".$reparo_not.", ".$reparo_inf.", ".$conformidad.", ".$fisc_integral.", ".$fisc_interes_estrategico.", ".$fisc_interes_no_estrategico.", '".$emision."', '".$notificacion."', 0, 0, ".$impuesto.", ".$multas.", ".$intereses.", ".$otras_multas.", '".$inicio."', '".$fin."')";
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

?>