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

BorrarRegistros($conexion, 'sg_cuadro_4_4', $inicio, $fin);

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


    $programa = $reg->descripcion;

    //************************ACTAS ACEPTADAS TOTAL*************************************************
    $sql = "SELECT Count(expedientes_fiscalizacion.numero) AS cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND vista_actas_siger.impuesto_omitido <= vista_actas_siger.ajuste_voluntario";
    $aa_cont = contar($con, $sql);
    $aa_actas = $aa_cont;

    $sql = "SELECT sum(vista_actas_siger.ajuste_voluntario) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND vista_actas_siger.impuesto_omitido <= vista_actas_siger.ajuste_voluntario";
    $aa_importe = contar($con, $sql);
    
    //************************ACTAS ACEPTADAS PARCIAL*************************************************
    $sql = "SELECT Count(expedientes_fiscalizacion.numero) AS cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND vista_actas_siger.impuesto_omitido > vista_actas_siger.ajuste_voluntario";
    $ap_cont = contar($con, $sql);
    $ap_actas = $ap_cont;

    $sql = "SELECT sum(vista_actas_siger.impuesto_omitido) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND vista_actas_siger.impuesto_omitido > vista_actas_siger.ajuste_voluntario";
    $total_importe = contar($con, $sql);

    $sql = "SELECT sum(vista_actas_siger.ajuste_voluntario) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND vista_actas_siger.impuesto_omitido > vista_actas_siger.ajuste_voluntario";
    $ap_pagado = contar($con, $sql);
    $ap_no_pagado = $total_importe - $ap_pagado;

    //************************ACTAS NO ACEPTADAS*************************************************
    $sql = "SELECT Count(expedientes_fiscalizacion.numero) AS cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND vista_actas_siger.impuesto_omitido > 0 AND vista_actas_siger.ajuste_voluntario = 0";
    $ana_cont = contar($con, $sql);
    $ana_actas = $ana_cont;

    $sql = "SELECT sum(vista_actas_siger.impuesto_omitido) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND vista_actas_siger.impuesto_omitido > 0 AND vista_actas_siger.ajuste_voluntario = 0";
    $ana_no_pagado = contar($con, $sql);

	//AGREGAMOS EL REGISTRO
	/*if ($fiscales > 0 AND $reg->clasificacion <> 'OP')
	{*/
		$insert = "INSERT INTO sg_cuadro_4_4 (programa, aa_cont, aa_actas, aa_importe, ap_cont, ap_actas, ap_pagado, ap_no_pagado, ana_cont, ana_actas, ana_no_pagado, periodo_inicio, periodo_fin) VALUES ('".$programa."', ".$aa_cont.", ".$aa_actas.", ".round($aa_importe, 2).", ".$ap_cont.", ".$ap_actas.", ".round($ap_pagado, 2).", ".round($ap_no_pagado, 2).", ".$ana_cont.", ".$ana_actas.", ".round($ana_no_pagado, 2).", '".$inicio."', '".$fin."')";
        //echo $insert.'<br>';
		$result = $conexion->query($insert);

		if ($conexion->affected_rows){
			$mensaje = "Informe Generado Satisfactoriamente";
			$permitido = true;
		}
	/*}*/
}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

function dia_anterior($fecha) 
{ 
    $sol = (strtotime($fecha) - 3600); 
    return date('Y-m-d', $sol); 
}  

?>