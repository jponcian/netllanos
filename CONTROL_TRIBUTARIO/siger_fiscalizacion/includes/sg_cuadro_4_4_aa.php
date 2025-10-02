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
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_fig += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_fig = $aa_cont_fig;
            $aa_importe_fig += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_fig += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_fig = $ap_cont_fig;
            $total_importe_fig += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_fig += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_fig = $total_importe_fig - $ap_pagado_fig;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_fig += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_fig = $ana_cont_fig;
            $ana_no_pagado_fig += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
			break;

		case "PT":
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_PT += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_PT = $aa_cont_PT;
            $aa_importe_PT += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_PT += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_PT = $ap_cont_PT;
            $total_importe_PT += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_PT += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_PT = $total_importe_PT - $ap_pagado_PT;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_PT += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_PT = $ana_cont_PT;
            $ana_no_pagado_PT += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
			break;

		case "ITF":
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_ITF += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_ITF = $aa_cont_ITF;
            $aa_importe_ITF += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_ITF += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_ITF = $ap_cont_ITF;
            $total_importe_ITF += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_ITF += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_ITF = $total_importe_ITF - $ap_pagado_ITF;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_ITF += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_ITF = $ana_cont_ITF;
            $ana_no_pagado_ITF += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
			break;

		case "FPN":
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_FPN += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_FPN = $aa_cont_FPN;
            $aa_importe_FPN += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_FPN += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_FPN = $ap_cont_FPN;
            $total_importe_FPN += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_FPN += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_FPN = $total_importe_FPN - $ap_pagado_FPN;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_FPN += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_FPN = $ana_cont_FPN;
            $ana_no_pagado_FPN += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
            //echo 'FPN: '.$aa_cont_FPN.'<br>';
			break;

		case "FPR":
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_FPR += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_FPR = $aa_cont_FPR;
            $aa_importe_FPR += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_FPR += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_FPR = $ap_cont_FPR;
            $total_importe_FPR += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_FPR += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_FPR = $total_importe_FPR - $ap_pagado_FPR;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_FPR += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_FPR = $ana_cont_FPR;
            $ana_no_pagado_FPR += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
			break;

		case "VN":
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_VDFN += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_VDFN = $aa_cont_VDFN;
            $aa_importe_VDFN += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_VDFN += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_VDFN = $ap_cont_VDFN;
            $total_importe_VDFN += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_VDFN += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_VDFN = $total_importe_VDFN - $ap_pagado_VDFN;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_VDFN += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_VDFN = $ana_cont_VDFN;
            $ana_no_pagado_VDFN += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
			break;

		case "VR":
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_VDFR += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_VDFR = $aa_cont_VDFR;
            $aa_importe_VDFR += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_VDFR += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_VDFR = $ap_cont_VDFR;
            $total_importe_VDFR += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_VDFR += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_VDFR = $total_importe_VDFR - $ap_pagado_VDFR;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_VDFR += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_VDFR = $ana_cont_VDFR;
            $ana_no_pagado_VDFR += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
			break;

		case "OP":
            //************************ACTAS ACEPTADAS TOTAL*************************************************
            $aa_cont_OP += actas_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $aa_actas_OP = $aa_cont_OP;
            $aa_importe_OP += actas_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);           
            //************************ACTAS ACEPTADAS PARCIAL*************************************************
            $ap_cont_OP += actas_aceptadas_parcial($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_actas_OP = $ap_cont_OP;
            $total_importe_OP += actas_aceptadas_parcial_importe($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_pagado_OP += actas_aceptadas_parcial_pagado($con, $reg->sector, $reg->anno, $reg->numero);
            $ap_no_pagado_OP = $total_importe_OP - $ap_pagado_OP;
            //************************ACTAS NO ACEPTADAS*************************************************
            $ana_cont_OP += actas_no_aceptadas($con, $reg->sector, $reg->anno, $reg->numero);
            $ana_actas_OP = $ana_cont_OP;
            $ana_no_pagado_OP += actas_no_aceptadas_importe($con, $reg->sector, $reg->anno, $reg->numero);
			break;


	}
}

		if ($aa_cont_fig == null) { $aa_cont_fig = 0; }
		if ($aa_actas_fig == null) { $aa_actas_fig = 0; }
		if ($aa_importe_fig == null) { $aa_importe_fig = 0; }
		if ($ap_cont_fig == null) { $ap_cont_fig = 0; }
		if ($ap_actas_fig == null) { $ap_actas_fig = 0; }
		if ($ap_pagado_fig == null) { $ap_pagado_fig = 0; }
		if ($ap_no_pagado_fig == null) { $ap_no_pagado_fig = 0; }
		if ($ana_cont_fig == null) { $ana_cont_fig = 0; }
		if ($ana_actas_fig == null) { $ana_actas_fig = 0; }
		if ($ana_no_pagado_fig == null) { $ana_no_pagado_fig = 0; }

		if ($aa_cont_PT == null) { $aa_cont_PT = 0; }
		if ($aa_actas_PT == null) { $aa_actas_PT = 0; }
		if ($aa_importe_PT == null) { $aa_importe_PT = 0; }
		if ($ap_cont_PT == null) { $ap_cont_PT = 0; }
		if ($ap_actas_PT == null) { $ap_actas_PT = 0; }
		if ($ap_pagado_PT == null) { $ap_pagado_PT = 0; }
		if ($ap_no_pagado_PT == null) { $ap_no_pagado_PT = 0; }
		if ($ana_cont_PT == null) { $ana_cont_PT = 0; }
		if ($ana_actas_PT == null) { $ana_actas_PT = 0; }
		if ($ana_no_pagado_PT == null) { $ana_no_pagado_PT = 0; }

		if ($aa_cont_ITF == null) { $aa_cont_ITF = 0; }
		if ($aa_actas_ITF == null) { $aa_actas_ITF = 0; }
		if ($aa_importe_ITF == null) { $aa_importe_ITF = 0; }
		if ($ap_cont_ITF == null) { $ap_cont_ITF = 0; }
		if ($ap_actas_ITF == null) { $ap_actas_ITF = 0; }
		if ($ap_pagado_ITF == null) { $ap_pagado_ITF = 0; }
		if ($ap_no_pagado_ITF == null) { $ap_no_pagado_ITF = 0; }
		if ($ana_cont_ITF == null) { $ana_cont_ITF = 0; }
		if ($ana_actas_ITF == null) { $ana_actas_ITF = 0; }
		if ($ana_no_pagado_ITF == null) { $ana_no_pagado_ITF = 0; }

		if ($aa_cont_FPN == null) { $aa_cont_FPN = 0; }
		if ($aa_actas_FPN == null) { $aa_actas_FPN = 0; }
		if ($aa_importe_FPN == null) { $aa_importe_FPN = 0; }
		if ($ap_cont_FPN == null) { $ap_cont_FPN = 0; }
		if ($ap_actas_FPN == null) { $ap_actas_FPN = 0; }
		if ($ap_pagado_FPN == null) { $ap_pagado_FPN = 0; }
		if ($ap_no_pagado_FPN == null) { $ap_no_pagado_FPN = 0; }
		if ($ana_cont_FPN == null) { $ana_cont_FPN = 0; }
		if ($ana_actas_FPN == null) { $ana_actas_FPN = 0; }
		if ($ana_no_pagado_FPN == null) { $ana_no_pagado_FPN = 0; }

		if ($aa_cont_FPR == null) { $aa_cont_FPR = 0; }
		if ($aa_actas_FPR == null) { $aa_actas_FPR = 0; }
		if ($aa_importe_FPR == null) { $aa_importe_FPR = 0; }
		if ($ap_cont_FPR == null) { $ap_cont_FPR = 0; }
		if ($ap_actas_FPR == null) { $ap_actas_FPR = 0; }
		if ($ap_pagado_FPR == null) { $ap_pagado_FPR = 0; }
		if ($ap_no_pagado_FPR == null) { $ap_no_pagado_FPR = 0; }
		if ($ana_cont_FPR == null) { $ana_cont_FPR = 0; }
		if ($ana_actas_FPR == null) { $ana_actas_FPR = 0; }
		if ($ana_no_pagado_FPR == null) { $ana_no_pagado_FPR = 0; }

		if ($aa_cont_VDFN == null) { $aa_cont_VDFN = 0; }
		if ($aa_actas_VDFN == null) { $aa_actas_VDFN = 0; }
		if ($aa_importe_VDFN == null) { $aa_importe_VDFN = 0; }
		if ($ap_cont_VDFN == null) { $ap_cont_VDFN = 0; }
		if ($ap_actas_VDFN == null) { $ap_actas_VDFN = 0; }
		if ($ap_pagado_VDFN == null) { $ap_pagado_VDFN = 0; }
		if ($ap_no_pagado_VDFN == null) { $ap_no_pagado_VDFN = 0; }
		if ($ana_cont_VDFN == null) { $ana_cont_VDFN = 0; }
		if ($ana_actas_VDFN == null) { $ana_actas_VDFN = 0; }
		if ($ana_no_pagado_VDFN == null) { $ana_no_pagado_VDFN = 0; }

		if ($aa_cont_VDFR == null) { $aa_cont_VDFR = 0; }
		if ($aa_actas_VDFR == null) { $aa_actas_VDFR = 0; }
		if ($aa_importe_VDFR == null) { $aa_importe_VDFR = 0; }
		if ($ap_cont_VDFR == null) { $ap_cont_VDFR = 0; }
		if ($ap_actas_VDFR == null) { $ap_actas_VDFR = 0; }
		if ($ap_pagado_VDFR == null) { $ap_pagado_VDFR = 0; }
		if ($ap_no_pagado_VDFR == null) { $ap_no_pagado_VDFR = 0; }
		if ($ana_cont_VDFR == null) { $ana_cont_VDFR = 0; }
		if ($ana_actas_VDFR == null) { $ana_actas_VDFR = 0; }
		if ($ana_no_pagado_VDFR == null) { $ana_no_pagado_VDFR = 0; }

		if ($aa_cont_OP == null) { $aa_cont_OP = 0; }
		if ($aa_actas_OP == null) { $aa_actas_OP = 0; }
		if ($aa_importe_OP == null) { $aa_importe_OP = 0; }
		if ($ap_cont_OP == null) { $ap_cont_OP = 0; }
		if ($ap_actas_OP == null) { $ap_actas_OP = 0; }
		if ($ap_pagado_OP == null) { $ap_pagado_OP = 0; }
		if ($ap_no_pagado_OP == null) { $ap_no_pagado_OP = 0; }
		if ($ana_cont_OP == null) { $ana_cont_OP = 0; }
		if ($ana_actas_OP == null) { $ana_actas_OP = 0; }
		if ($ana_no_pagado_OP == null) { $ana_no_pagado_OP = 0; }

	//AGREGAMOS EL REGISTRO
    $programa = "101-Fiscalización Integrales Generales";
    guardar($conexion, $programa, $aa_cont_fig, $aa_actas_fig, $aa_importe_fig, $ap_cont_fig, $ap_actas_fig, $ap_pagado_fig, $ap_no_pagado_fig, $ana_cont_fig, $ana_actas_fig, $ana_no_pagado_fig, $inicio, $fin);
    $programa = "102-Fiscalizaciones en Materia de Precios de Transferencia";
    guardar($conexion, $programa, $aa_cont_PT, $aa_actas_PT, $aa_importe_PT, $ap_cont_PT, $ap_actas_PT, $ap_pagado_PT, $ap_no_pagado_PT, $ana_cont_PT, $ana_actas_PT, $ana_no_pagado_PT, $inicio, $fin);
    $programa = "103-Impuesto a las Grandes Transacciones Financieras";
    guardar($conexion, $programa, $aa_cont_ITF, $aa_actas_ITF, $aa_importe_ITF, $ap_cont_ITF, $ap_actas_ITF, $ap_pagado_ITF, $ap_no_pagado_ITF, $ana_cont_ITF, $ana_actas_ITF, $ana_no_pagado_ITF, $inicio, $fin);
    $programa = "104-Fiscalizaciones Puntuales Nacionales";
    guardar($conexion, $programa, $aa_cont_FPN, $aa_actas_FPN, $aa_importe_FPN, $ap_cont_FPN, $ap_actas_FPN, $ap_pagado_FPN, $ap_no_pagado_FPN, $ana_cont_FPN, $ana_actas_FPN, $ana_no_pagado_FPN, $inicio, $fin);
    $programa = "105-Fiscalizaciones Puntuales Regionales";
    guardar($conexion, $programa, $aa_cont_FPR, $aa_actas_FPR, $aa_importe_FPR, $ap_cont_FPR, $ap_actas_FPR, $ap_pagado_FPR, $ap_no_pagado_FPR, $ana_cont_FPR, $ana_actas_FPR, $ana_no_pagado_FPR, $inicio, $fin);
    $programa = "106-Verificación Nacional";
    guardar($conexion, $programa, $aa_cont_VDFN, $aa_actas_VDFN, $aa_importe_VDFN, $ap_cont_VDFN, $ap_actas_VDFN, $ap_pagado_VDFN, $ap_no_pagado_VDFN, $ana_cont_VDFN, $ana_actas_VDFN, $ana_no_pagado_VDFN, $inicio, $fin);
    $programa = "107-Verificación Regional";
    guardar($conexion, $programa, $aa_cont_VDFR, $aa_actas_VDFR, $aa_importe_VDFR, $ap_cont_VDFR, $ap_actas_VDFR, $ap_pagado_VDFR, $ap_no_pagado_VDFR, $ana_cont_VDFR, $ana_actas_VDFR, $ana_no_pagado_VDFR, $inicio, $fin);
    $programa = "108-Otros Programas";
    guardar($conexion, $programa, $aa_cont_OP, $aa_actas_OP, $aa_importe_OP, $ap_cont_OP, $ap_actas_OP, $ap_pagado_OP, $ap_no_pagado_OP, $ana_cont_OP, $ana_actas_OP, $ana_no_pagado_OP, $inicio, $fin);

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

function guardar($conexion, $programa, $aa_cont, $aa_actas, $aa_importe, $ap_cont, $ap_actas, $ap_pagado, $ap_no_pagado, $ana_cont, $ana_actas, $ana_no_pagado, $inicio, $fin)
{
		$insert = "INSERT INTO sg_cuadro_4_4 (programa, aa_cont, aa_actas, aa_importe, ap_cont, ap_actas, ap_pagado, ap_no_pagado, ana_cont, ana_actas, ana_no_pagado, periodo_inicio, periodo_fin) VALUES ('".$programa."', ".$aa_cont.", ".$aa_actas.", ".round($aa_importe, 2).", ".$ap_cont.", ".$ap_actas.", ".round($ap_pagado, 2).", ".round($ap_no_pagado, 2).", ".$ana_cont.", ".$ana_actas.", ".round($ana_no_pagado, 2).", '".$inicio."', '".$fin."')";
        //echo $insert.'<br>';
		$result = $conexion->query($insert);
}

function actas_aceptadas($con, $sector, $anno, $numero)
{
    $sql = "SELECT count(expedientes_fiscalizacion.numero) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND vista_actas_siger.impuesto_omitido <= vista_actas_siger.ajuste_voluntario";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function actas_aceptadas_importe($con, $sector, $anno, $numero)
{
    $sql = "SELECT sum(vista_actas_siger.ajuste_voluntario) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND vista_actas_siger.impuesto_omitido <= vista_actas_siger.ajuste_voluntario";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function actas_aceptadas_parcial($con, $sector, $anno, $numero)
{
    $sql = "SELECT count(expedientes_fiscalizacion.numero) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND vista_actas_siger.impuesto_omitido > vista_actas_siger.ajuste_voluntario";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function actas_aceptadas_parcial_importe($con, $sector, $anno, $numero)
{
    $sql = "SELECT sum(vista_actas_siger.impuesto_omitido) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND vista_actas_siger.impuesto_omitido > vista_actas_siger.ajuste_voluntario";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function actas_aceptadas_parcial_pagado($con, $sector, $anno, $numero)
{
    $sql = "SELECT sum(vista_actas_siger.ajuste_voluntario) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND vista_actas_siger.impuesto_omitido > vista_actas_siger.ajuste_voluntario";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function actas_no_aceptadas($con, $sector, $anno, $numero)
{
    $sql = "SELECT count(expedientes_fiscalizacion.numero) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND vista_actas_siger.impuesto_omitido > 0 AND vista_actas_siger.ajuste_voluntario = 0";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}

function actas_no_aceptadas_importe($con, $sector, $anno, $numero)
{
    $sql = "SELECT sum(vista_actas_siger.impuesto_omitido) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND vista_actas_siger.impuesto_omitido > 0 AND vista_actas_siger.ajuste_voluntario = 0";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$cantidad = $reg->cantidad;
	return $cantidad;
}


?>