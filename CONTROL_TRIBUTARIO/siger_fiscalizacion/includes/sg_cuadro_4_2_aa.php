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
WHERE borrado = 1 AND year(notificacion) < year(date(now()))"; //echo $sqltipo.'<br>';
$tabla_tipo = $conexion->query($sqltipo);
while ($reg = $tabla_tipo->fetch_object())
{
	switch ($reg->clasificacion) 
	{
		case "FIN":
		case "FIR":
			$concluidos_fig += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_fig += contar_concluidos_sancionados($con, "FIS", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_fig = $concluidos_fig - $conc_sancionado_fig;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '101-Fiscalización Integrales Generales'";
			$proceso_inicio_fig = contar($conexion, $sql);
			$proc_final_fig = $proceso_inicio_fig - $concluidos_fig;

			$sanc_fig = $conc_sancionado_fig;
			$nosanc_fig = $conc_conforme_fig;
			$proceso_final_fig = $proc_final_fig;
			break;

		case "FPN":
			$concluidos_FPN += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_FPN += contar_concluidos_sancionados($con, "FIS", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_FPN = $concluidos_FPN - $conc_sancionado_FPN;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '104-Fiscalizaciones Puntuales Nacionales'";
			$proceso_inicio_FPN = contar($conexion, $sql);
			$proc_final_FPN = $proceso_inicio_FPN - $concluidos_FPN;

			$sanc_FPN = $conc_sancionado_FPN;
			$nosanc_FPN = $conc_conforme_FPN;
			$proceso_final_FPN = $proc_final_FPN;
			break;


		case "PT":
			$concluidos_PT += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_PT += contar_concluidos_sancionados($con, "FIS", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_PT = $concluidos_PT - $conc_sancionado_PT;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '102-Fiscalizaciones en Materia de Precios de Transferencia'";
			$proceso_inicio_PT = contar($conexion, $sql);
			$proc_final_PT = $proceso_inicio_PT - $concluidos_PT;

			$sanc_PT = $conc_sancionado_PT;
			$nosanc_PT = $conc_conforme_PT;
			$proceso_final_PT = $proc_final_PT;
			break;


		case "ITF":
			$concluidos_ITF += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_ITF += contar_concluidos_sancionados($con, "FIS", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_ITF = $concluidos_ITF - $conc_sancionado_ITF;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '102-Fiscalizaciones en Materia de Precios de Transferencia'";
			$proceso_inicio_ITF = contar($conexion, $sql);
			$proc_final_ITF = $proceso_inicio_ITF - $concluidos_ITF;

			$sanc_ITF = $conc_sancionado_ITF;
			$nosanc_ITF = $conc_conforme_ITF;
			$proceso_final_ITF = $proc_final_ITF;
			break;


		case "FPR":
			$concluidos_FPR += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_FPR += contar_concluidos_sancionados($con, "FIS", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_FPR = $concluidos_FPR - $conc_sancionado_FPR;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '105-Fiscalizaciones Puntuales Regionales'";
			$proceso_inicio_FPR = contar($conexion, $sql);
			$proc_final_FPR = $proceso_inicio_FPR - $concluidos_FPR;

			$sanc_FPR = $conc_sancionado_FPR;
			$nosanc_FPR = $conc_conforme_FPR;
			$proceso_final_FPR = $proc_final_FPR;
			break;


		case "VN":
			$concluidos_VDFN += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_VDFN += contar_concluidos_sancionados($con, "VER", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_VDFN = $concluidos_VDFN - $conc_sancionado_VDFN;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '106-Verificación Nacional'";
			$proceso_inicio_VDFN = contar($conexion, $sql);
			$proc_final_VDFN = $proceso_inicio_VDFN - $concluidos_VDFN;

			$sanc_VDFN = $conc_sancionado_VDFN;
			$nosanc_VDFN = $conc_conforme_VDFN;
			$proceso_final_VDFN = $proc_final_VDFN;
			break;


		case "VR":
			$concluidos_VDFR += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_VDFR += contar_concluidos_sancionados($con, "VER", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_VDFR = $concluidos_VDFR - $conc_sancionado_VDFR;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '107-Verificación Regional'";
			$proceso_inicio_VDFR = contar($conexion, $sql);
			$proc_final_VDFR = $proceso_inicio_VDFR - $concluidos_VDFR;

			$sanc_VDFR = $conc_sancionado_VDFR;
			$nosanc_VDFR = $conc_conforme_VDFR;
			$proceso_final_VDFR = $proc_final_VDFR;
			break;


		case "OP":
			$concluidos_OP += contar_concluidos($conexion, $reg->desc_formato, $reg->sector, $reg->anno, $reg->numero, $inicio, $fin);
			$conc_sancionado_OP += contar_concluidos_sancionados($con, "VER", $reg->sector, $reg->anno, $reg->numero);
			$conc_conforme_OP = $concluidos_OP - $conc_sancionado_OP;
            
			$dia = dia_anterior($inicio);
			$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '108-Otros Programas'";
			$proceso_inicio_OP = contar($conexion, $sql);
			$proc_final_OP = $proceso_inicio_OP - $concluidos_OP;

			$sanc_OP = $conc_sancionado_OP;
			$nosanc_OP = $conc_conforme_OP;
			$proceso_final_OP = $proc_final_OP;
			break;

	}
}

		if ($sup_fig == null) { $sup_fig = 0; }
		if ($fis_fig == null) { $fis_fig = 0; }
		if ($proceso_inicio_fig == null) { $proceso_inicio_fig = 0; }
		if ($sanc_fig == null) { $sanc_fig = 0; }
		if ($nosanc_fig == null) { $nosanc_fig = 0; }
		if ($proceso_final_fig == null) { $proceso_final_fig = 0; }

		if ($sup_PT == null) { $sup_PT = 0; }
		if ($fis_PT == null) { $fis_PT = 0; }
		if ($proceso_inicio_PT == null) { $proceso_inicio_PT = 0; }
		if ($sanc_PT == null) { $sanc_PT = 0; }
		if ($nosanc_PT == null) { $nosanc_PT = 0; }
		if ($proceso_final_PT == null) { $proceso_final_PT = 0; }

		if ($sup_ITF == null) { $sup_ITF = 0; }
		if ($fis_ITF == null) { $fis_ITF = 0; }
		if ($proceso_inicio_ITF == null) { $proceso_inicio_ITF = 0; }
		if ($sanc_ITF == null) { $sanc_ITF = 0; }
		if ($nosanc_ITF == null) { $nosanc_ITF = 0; }
		if ($proceso_final_ITF == null) { $proceso_final_ITF = 0; }

		if ($sup_FPN == null) { $sup_FPN = 0; }
		if ($fis_FPN == null) { $fis_FPN = 0; }
		if ($proceso_inicio_FPN == null) { $proceso_inicio_FPN = 0; }
		if ($sanc_FPN == null) { $sanc_FPN = 0; }
		if ($nosanc_FPN == null) { $nosanc_FPN = 0; }
		if ($proceso_final_FPN == null) { $proceso_final_FPN = 0; }

		if ($sup_FPR == null) { $sup_FPR = 0; }
		if ($fis_FPR == null) { $fis_FPR = 0; }
		if ($proceso_inicio_FPR == null) { $proceso_inicio_FPR = 0; }
		if ($sanc_FPR == null) { $sanc_FPR = 0; }
		if ($nosanc_FPR == null) { $nosanc_FPR = 0; }
		if ($proceso_final_FPR == null) { $proceso_final_FPR = 0; }

		if ($sup_VDFN == null) { $sup_VDFN = 0; }
		if ($fis_VDFN == null) { $fis_VDFN = 0; }
		if ($proceso_inicio_VDFN == null) { $proceso_inicio_VDFN = 0; }
		if ($sanc_VDFN == null) { $sanc_VDFN = 0; }
		if ($nosanc_VDFN == null) { $nosanc_VDFN = 0; }
		if ($proceso_final_VDFN == null) { $proceso_final_VDFN = 0; }

		if ($sup_VDFR == null) { $sup_VDFR = 0; }
		if ($fis_VDFR == null) { $fis_VDFR = 0; }
		if ($proceso_inicio_VDFR == null) { $proceso_inicio_VDFR = 0; }
		if ($sanc_VDFR == null) { $sanc_VDFR = 0; }
		if ($nosanc_VDFR == null) { $nosanc_VDFR = 0; }
		if ($proceso_final_VDFR == null) { $proceso_final_VDFR = 0; }

		if ($sup_OP == null) { $sup_OP = 0; }
		if ($fis_OP == null) { $fis_OP = 0; }
		if ($proceso_inicio_OP == null) { $proceso_inicio_OP = 0; }
		if ($sanc_OP == null) { $sanc_OP = 0; }
		if ($nosanc_OP == null) { $nosanc_OP = 0; }
		if ($proceso_final_OP == null) { $proceso_final_OP = 0; }

		//echo "concluidos_FPN: ".$concluidos_FPN." conc_sancionado_FPN: ".$conc_sancionado_FPN." conc_conforme_FPN: ".$conc_conforme_FPN.'<br>';

	//AGREGAMOS EL REGISTRO
	$programa = "101-Fiscalización Integrales Generales";
	guardar($conexion, $programa, $sup_fig, $fis_fig, $proceso_inicio_fig, $sanc_fig, $nosanc_fig, $proceso_final_fig, $inicio, $fin, $concluidos_fig, "FISCALIZACION INTEGRAL");
	$programa = "102-Fiscalizaciones en Materia de Precios de Transferencia";
	guardar($conexion, $programa, $sup_PT, $fis_PT, $proceso_inicio_PT, $sanc_PT, $nosanc_PT, $proceso_final_PT, $inicio, $fin, $concluidos_PT, "PRECIO DE TRANSFERENCIA");
	$programa = "103-Impuesto a las Grandes Transacciones Financieras";
	guardar($conexion, $programa, $sup_ITF, $fis_ITF, $proceso_inicio_ITF, $sanc_ITF, $nosanc_ITF, $proceso_final_ITF, $inicio, $fin, $concluidos_ITF, "TRANSACCIONES FINANCIERAS");
	$programa = "104-Fiscalizaciones Puntuales Nacionales";
	guardar($conexion, $programa, $sup_FPN, $fis_FPN, $proceso_inicio_FPN, $sanc_FPN, $nosanc_FPN, $proceso_final_FPN, $inicio, $fin, $concluidos_FPN, "FISCALIZACION PUNTUAL NACIONAL");
	$programa = "105-Fiscalizaciones Puntuales Regionales";
	guardar($conexion, $programa, $sup_FPR, $fis_FPR, $proceso_inicio_FPR, $sanc_FPR, $nosanc_FPR, $proceso_final_FPR, $inicio, $fin, $concluidos_FPR, "FISCALIZACION PUNTUAL REGIONAL");
	$programa = "106-Verificación Nacional";
	guardar($conexion, $programa, $sup_VDFN, $fis_VDFN, $proceso_inicio_VDFN, $sanc_VDFN, $nosanc_VDFN, $proceso_final_VDFN, $inicio, $fin, $concluidos_VDFN, "VERIFICACION NACIONAL");
	$programa = "107-Verificación Regional";
	guardar($conexion, $programa, $sup_VDFR, $fis_VDFR, $proceso_inicio_VDFR, $sanc_VDFR, $nosanc_VDFR, $proceso_final_VDFR, $inicio, $fin, $concluidos_VDFR, "VERIFICACION REGIONAL");
	$programa = "108-Otros Programas";
	guardar($conexion, $programa, $sup_OP, $fis_OP, $proceso_inicio_OP, $sanc_OP, $nosanc_OP, $proceso_final_OP, $inicio, $fin, $concluidos_OP, "OTROS PROGRAMAS");

	if ($conexion->affected_rows){
		$mensaje = "Informe Generado Satisfactoriamente";
		$permitido = true;
	}

function guardar($conexion, $programa, $supervisores, $fiscales, $proceso_inicio, $conc_sancionado, $conc_conforme, $proc_final, $inicio, $fin, $concluidos, $desc_formato)
{

	$dia = dia_anterior($inicio);
	$sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = $dia AND sg_cuadro_4_2.programa = '".$programa."'";
	$proceso_inicio = contar($conexion, $sql);
	$proc_final = $proceso_inicio - $concluidos;
	if ($proceso_inicio > 0)
	{			
		$supervisores = contar_supervisores($conexion, $desc_formato, $inicio, $fin);
		$fiscales = contar_fiscales($conexion, $desc_formato, $inicio, $fin);
	}

	$insert = "INSERT INTO sg_cuadro_4_2 (programa, coordinadores, supervisores, fiscales, resguardo, emitidas, notificadas, anuladas, proceso_inicio, meta, conc_sancionado, conc_conforme, proc_final, periodo_inicio, periodo_fin) VALUES ('".$programa."', 1, ".$supervisores.", ".$fiscales.", 0, 0, 0, 0, ".$proceso_inicio.", 0, ".$conc_sancionado.", ".$conc_conforme.", ".$proc_final.", '".$inicio."', '".$fin."')";
	//echo $insert.'<br>';
	$result = $conexion->query($insert);
}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

function dia_anterior($fecha) 
{ 
    $sol = (strtotime($fecha) - 3600); 
    return date('Y-m-d', $sol); 
} 

function contar_supervisores($con, $programa, $inicio, $fin)
{
	$sql = "SELECT count(DISTINCT ci_supervisor) AS cantidad FROM casos_en_proceso WHERE borrado = 0 AND year(notificacion) < $anno  AND desc_formato = '".$programa."'";		
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function contar_fiscales($con, $programa, $inicio, $fin)
{
	$sql = "SELECT count(DISTINCT ci_fiscal) AS cantidad FROM casos_en_proceso WHERE borrado = 0 AND year(notificacion) < $anno  AND desc_formato = '".$programa."'";		
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function contar_concluidos($con, $programa, $sector, $anno, $numero, $inicio, $fin)
{
	$sql = "SELECT count(fecha_borrado) as cantidad FROM casos_en_proceso WHERE borrado = 1 AND year(notificacion) < $anno AND desc_formato = 'FISCALIZACION PUNTUAL NACIONAL' AND sector = ".$sector." AND anno = ".$anno." AND numero = ".$numero." AND fecha_borrado BETWEEN '".$inicio."' AND '".$fin."'";
	//echo "Concluidos SQL: ".$sql.'<br>';			
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	//echo "Concluidos: ".$cantidad.'<br>';
	return $cantidad;
}

function contar_concluidos_sancionados($con, $siglas, $sector, $anno, $numero)
{
	//*****************VERIFICAR SI EL REGISTRO ESTA SANCIONADO POR NUMERO DE PROVIDENCIA ***************************************
	if ($siglas == 'FIS')
	{
		$sql = "SELECT count(ct_salida_expediente.FechaEmision) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE fis_actas.acta <> 1 AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND expedientes_fiscalizacion.sector = ".$sector." AND year(expedientes_fiscalizacion.fecha_notificacion) < $anno";
	} else {
		$sql = "SELECT count(ct_salida_expediente.FechaEmision) as cantidad FROM expedientes_fiscalizacion INNER JOIN vista_ct_multas ON vista_ct_multas.sector = expedientes_fiscalizacion.sector AND vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE vista_ct_multas.Multas > 0 AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND expedientes_fiscalizacion.sector = ".$sector." AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno"; 
	}
	//echo $sql.'<br>';
	$resultado = $con->query($sql);
	$reg = $resultado->fetch_object();
	$cantidad = $reg->cantidad;
	//echo "Sancionado: ".$cantidad.'<br>';
	return $cantidad;	
}
?>