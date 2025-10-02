<?php
//*****SCRIPT PARA GENERAR EL INFORME DE GESTION*****//
//                                                   //
//    Elaborado por Gustavo García para el SENIAT    //
//                                                   //
///////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");
include("../funciones/func.contador.php");

//Variables a utilizar
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];


//RECORREMOS LOS TIPOS DE PROGRAMAS AÑOS ANTERIORES
$sqltipo = "SELECT anno, numero, sector, descripcion, rif, contribuyente, ci_fiscal, fiscal, ci_supervisor, supervisor, emision, notificacion, nombre_sector, tipo, clasificacion, proceso_auditoria, nivel_supervisor, lapso_allanamiento, otras_causas, desc_formato FROM casos_en_proceso
WHERE borrado = 1 AND year(notificacion) < $anno AND fecha_borrado BETWEEN '".$inicio."' AND '".$fin."'";
$tabla_tipo = $conexion->query($sqltipo);
while ($reg = $tabla_tipo->fetch_object())
{
	switch ($reg->clasificacion) 
	{
		case "FIN":
		case "FIR":
			$reparo_fig += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_fig += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_fig += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_fig += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_fig += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_fig > $multa_reparo_fig)
			{
				$multa_vdf_fig = $multa_vdf_fig - $multa_reparo_fig;
			} else {
				$multa_vdf_fig = 0;
			}
			break;

		case "PT":
			$reparo_PT += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_PT += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_PT += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_PT += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_PT += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_PT > $multa_reparo_PT)
			{
				$multa_vdf_PT = $multa_vdf_PT - $multa_reparo_PT;
			} else {
				$multa_vdf_PT = 0;
			}
			break;

		case "ITF":
			$reparo_ITF += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_ITF += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_ITF += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_ITF += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_ITF += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_ITF > $multa_reparo_ITF)
			{
				$multa_vdf_ITF = $multa_vdf_ITF - $multa_reparo_ITF;
			} else {
				$multa_vdf_ITF = 0;
			}
			break;

		case "FPN":
			$reparo_FPN += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_FPN += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_FPN += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_FPN += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_FPN += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_FPN > $multa_reparo_FPN)
			{
				$multa_vdf_FPN = $multa_vdf_FPN - $multa_reparo_FPN;
			} else {
				$multa_vdf_FPN = 0;
			}
			break;

		case "FPR":
			$reparo_FPR += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_FPR += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_FPR += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_FPR += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_FPR += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_FPR > $multa_reparo_FPR)
			{
				$multa_vdf_FPR = $multa_vdf_FPR - $multa_reparo_FPR;
			} else {
				$multa_vdf_FPR = 0;
			}
			break;

		case "VN":
			$reparo_VDFN += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_VDFN += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_VDFN += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_VDFN += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_VDFN += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_VDFN > $multa_reparo_VDFN)
			{
				$multa_vdf_VDFN = $multa_vdf_VDFN - $multa_reparo_VDFN;
			} else {
				$multa_vdf_VDFN = 0;
			}
			break;

		case "VR":
			$reparo_VDFR += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_VDFR += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_VDFR += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_VDFR += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_VDFR += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_VDFR > $multa_reparo_VDFR)
			{
				$multa_vdf_VDFR = $multa_vdf_VDFR - $multa_reparo_VDFR;
			} else {
				$multa_vdf_VDFR = 0;
			}
			break;

		case "OP":
			$reparo_OP += monto_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$impuesto_OP += monto_impuesto($con, $reg->sector, $reg->anno, $reg->numero);
			$intereses_OP += monto_intereses($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_reparo_OP += monto_multa_reparo($con, $reg->sector, $reg->anno, $reg->numero);
			$multa_vdf_OP += monto_multa_vdf($con, $reg->sector, $reg->anno, $reg->numero);
			if ($multa_vdf_OP > $multa_reparo_OP)
			{
				$multa_vdf_OP = $multa_vdf_OP - $multa_reparo_OP;
			} else {
				$multa_vdf_OP = 0;
			}
			break;


	}
}

		if ($reparo_fig == null) { $reparo_fig = 0; }
		if ($impuesto_fig == null) { $impuesto_fig = 0; }
		if ($intereses_fig == null) { $intereses_fig = 0; }
		if ($multa_reparo_fig == null) { $multa_reparo_fig = 0; }
		if ($multa_vdf_fig == null) { $multa_vdf_fig = 0; }

		if ($reparo_PT == null) { $reparo_PT = 0; }
		if ($impuesto_PT == null) { $impuesto_PT = 0; }
		if ($intereses_PT == null) { $intereses_PT = 0; }
		if ($multa_reparo_PT == null) { $multa_reparo_PT = 0; }
		if ($multa_vdf_PT == null) { $multa_vdf_PT = 0; }

		if ($reparo_ITF == null) { $reparo_ITF = 0; }
		if ($impuesto_ITF == null) { $impuesto_ITF = 0; }
		if ($intereses_ITF == null) { $intereses_ITF = 0; }
		if ($multa_reparo_ITF == null) { $multa_reparo_ITF = 0; }
		if ($multa_vdf_ITF == null) { $multa_vdf_ITF = 0; }

		if ($reparo_FPN == null) { $reparo_FPN = 0; }
		if ($impuesto_FPN == null) { $impuesto_FPN = 0; }
		if ($intereses_FPN == null) { $intereses_FPN = 0; }
		if ($multa_reparo_FPN == null) { $multa_reparo_FPN = 0; }
		if ($multa_vdf_FPN == null) { $multa_vdf_FPN = 0; }

		if ($reparo_FPR == null) { $reparo_FPR = 0; }
		if ($impuesto_FPR == null) { $impuesto_FPR = 0; }
		if ($intereses_FPR == null) { $intereses_FPR = 0; }
		if ($multa_reparo_FPR == null) { $multa_reparo_FPR = 0; }
		if ($multa_vdf_FPR == null) { $multa_vdf_FPR = 0; }

		if ($reparo_VDFN == null) { $reparo_VDFN = 0; }
		if ($impuesto_VDFN == null) { $impuesto_VDFN = 0; }
		if ($intereses_VDFN == null) { $intereses_VDFN = 0; }
		if ($multa_reparo_VDFN == null) { $multa_reparo_VDFN = 0; }
		if ($multa_vdf_VDFN == null) { $multa_vdf_VDFN = 0; }

		if ($reparo_VDFR == null) { $reparo_VDFR = 0; }
		if ($impuesto_VDFR == null) { $impuesto_VDFR = 0; }
		if ($intereses_VDFR == null) { $intereses_VDFR = 0; }
		if ($multa_reparo_VDFR == null) { $multa_reparo_VDFR = 0; }
		if ($multa_vdf_VDFR == null) { $multa_vdf_VDFR = 0; }

		if ($reparo_OP == null) { $reparo_OP = 0; }
		if ($impuesto_OP == null) { $impuesto_OP = 0; }
		if ($intereses_OP == null) { $intereses_OP = 0; }
		if ($multa_reparo_OP == null) { $multa_reparo_OP = 0; }
		if ($multa_vdf_OP == null) { $multa_vdf_OP = 0; }
		

	//AGREGAMOS EL REGISTRO
	$programa = "101-Fiscalización Integrales Generales";
	guardar($conexion, $programa, $reparo_fig, $impuesto_fig, $intereses_fig, $multa_reparo_fig, $multa_vdf_fig, $inicio, $fin);
	$programa = "102-Fiscalizaciones en Materia de Precios de Transferencia";
	guardar($conexion, $programa, $reparo_PT, $impuesto_PT, $intereses_PT, $multa_reparo_PT, $multa_vdf_PT, $inicio, $fin);
	$programa = "103-Impuesto a las Grandes Transacciones Financieras";
	guardar($conexion, $programa, $reparo_ITF, $impuesto_ITF, $intereses_ITF, $multa_reparo_ITF, $multa_vdf_ITF, $inicio, $fin);
	$programa = "104-Fiscalizaciones Puntuales Nacionales";
	guardar($conexion, $programa, $reparo_FPN, $impuesto_FPN, $intereses_FPN, $multa_reparo_FPN, $multa_vdf_FPN, $inicio, $fin);
	$programa = "105-Fiscalizaciones Puntuales Regionales";
	guardar($conexion, $programa, $reparo_FPR, $impuesto_FPR, $intereses_FPR, $multa_reparo_FPR, $multa_vdf_FPR, $inicio, $fin);
	$programa = "106-Verificación Nacional";
	guardar($conexion, $programa, $reparo_VDFN, $impuesto_VDFN, $intereses_VDFN, $multa_reparo_VDFN, $multa_vdf_VDFN, $inicio, $fin);
	$programa = "107-Verificación Regional";
	guardar($conexion, $programa, $reparo_VDFR, $impuesto_VDFR, $intereses_VDFR, $multa_reparo_VDFR, $multa_vdf_VDFR, $inicio, $fin);
	$programa = "108-Otros Programas";
	guardar($conexion, $programa, $reparo_OP, $impuesto_OP, $intereses_OP, $multa_reparo_OP, $multa_vdf_OP, $inicio, $fin);

	if ($conexion->affected_rows){
		$mensaje = "Informe Generado Satisfactoriamente";
		$permitido = true;
	}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

function dia_anterior($fecha) 
{ 
    $sol = (strtotime($fecha) - 3600); 
    return date('Y-m-d', $sol); 
}  

function guardar($conexion, $programa, $reparo, $impuesto, $intereses, $multa_reparo, $multa_vdf, $inicio, $fin)
{
	$insert = "INSERT INTO sg_cuadro_4_3 (programa, reparo, impuesto, rebaja, intereses, multa_reparo, multa_vdf, periodo_inicio, periodo_fin) VALUES ('". $programa."', ". round($reparo, 2).", ". round($impuesto, 2).", 0, ". round($intereses, 2).", ". round($multa_reparo, 2).", ". round($multa_vdf, 2).", '". $inicio."', '". $fin."')";
	//echo $insert.'<br>';
	$result = $conexion->query($insert);
}

function monto_reparo($con, $sector, $anno, $numero)
{
	$sql = "SELECT sum(vista_actas_siger.reparo) as reparo FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero;
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$reparo = $reg->reparo;
	return $reparo;
}

function monto_impuesto($con, $sector, $anno, $numero)
{
	$sql = "SELECT sum(vista_actas_siger.impuesto_omitido) as impuesto FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero;
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$impuesto = $reg->impuesto;
	return $impuesto;
}

function monto_intereses($con, $sector, $anno, $numero)
{
	$sql = "SELECT sum(vista_actas_siger.interes) as intereses FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero;
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$intereses = $reg->intereses;
	return $intereses;
}

function monto_multa_reparo($con, $sector, $anno, $numero)
{
	$sql = "SELECT sum(vista_actas_siger.multa_actual) as multa FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero;
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$multa = $reg->multa;
	return $multa;
}

function monto_multa_vdf($con, $sector, $anno, $numero)
{
	$sql = "SELECT sum(vista_ct_multas.Multas) as multavdf FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_ct_multas ON vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero AND vista_ct_multas.sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero;
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$multavdf = $reg->multavdf;
	return $multavdf;
}


?>