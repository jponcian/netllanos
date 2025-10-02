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

BorrarRegistros($conexion, 'sg_cuadro_4_2', $inicio, $fin);

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
    $coordinadores = 1;
    $sql = "SELECT count(DISTINCT casos_en_proceso.supervisor) as cantidad FROM casos_en_proceso WHERE casos_en_proceso.descripcion = '".$reg->descripcion."' AND year(casos_en_proceso.notificacion) = $anno) AND casos_en_proceso.borrado = 0";
    $supervisores = contar($conexion, $sql);

    $sql = "SELECT count(DISTINCT casos_en_proceso.ci_fiscal) as cantidad FROM casos_en_proceso WHERE casos_en_proceso.descripcion = '".$reg->descripcion."' AND year(casos_en_proceso.notificacion) = $anno) AND casos_en_proceso.borrado = 0";
    $fiscales = contar($conexion, $sql);

    $resguardo = 0;

    $sql = "SELECT count(expedientes_fiscalizacion.numero) AS cantidad FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND expedientes_fiscalizacion.fecha_emision BETWEEN '".$inicio."' AND '".$fin."' ORDER BY a_tipo_programa.id_programa ASC";
    $emitidas = contar($con, $sql);

    $sql = "SELECT count(casos_en_proceso.notificacion) as cantidad FROM casos_en_proceso WHERE casos_en_proceso.descripcion = '".$reg->descripcion."' AND casos_en_proceso.notificacion BETWEEN '".$inicio."' AND '".$fin."'";
    $notificadas = contar($conexion, $sql);

    $sql = "SELECT count(expedientes_fiscalizacion.numero) AS cantidad FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND expedientes_fiscalizacion.fecha_anulacion <> '0000-00-00' AND expedientes_fiscalizacion.fecha_anulacion BETWEEN '".$inicio."' AND '".$fin."' ORDER BY a_tipo_programa.id_programa ASC";
    $anuladas = contar($con, $sql);

    $dia = dia_anterior($inicio);
    $sql = "SELECT sg_cuadro_4_2.proc_final as cantidad FROM sg_cuadro_4_2 WHERE sg_cuadro_4_2.periodo_fin = '".$dia."' AND sg_cuadro_4_2.programa =  '".$reg->descripcion."'";
    $proceso_inicio = contar($conexion, $sql);

    $meta = 0;

    $sql = "SELECT count(casos_en_proceso.fecha_borrado) as cantidad FROM casos_en_proceso WHERE casos_en_proceso.descripcion = '".$reg->descripcion."' AND casos_en_proceso.fecha_borrado BETWEEN '".$inicio."' AND '".$fin."' AND casos_en_proceso.borrado = 1";
    $concluidos = contar($conexion, $sql);

	//**** DETERMINAMOS LOS SANCIONADOS ***//
	$sQL_sanc = "SELECT anno, numero, sector FROM casos_en_proceso WHERE descripcion = '".$reg->descripcion."' AND fecha_borrado BETWEEN '".$inicio."' AND '".$fin."' AND borrado = 1";
	$verificar = $conexion->query($sQL_sanc);
	$acumulado = 0;
	while ($reg_sanc = $verificar->fetch_object())
	{
		if (substr($clasificacion, 0, 3) == 'FIS')
		{
			$sqlsanc = "SELECT count(ct_salida_expediente.FechaEmision) as cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE fis_actas.acta <> 1 AND expedientes_fiscalizacion.anno = ".$reg_sanc->anno." AND expedientes_fiscalizacion.numero = ".$reg_sanc->numero." AND expedientes_fiscalizacion.sector = ".$reg_sanc->sector." AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno";
		} else {
			$sqlsanc = "SELECT count(ct_salida_expediente.FechaEmision) as cantidad FROM expedientes_fiscalizacion INNER JOIN vista_ct_multas ON vista_ct_multas.sector = expedientes_fiscalizacion.sector AND vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE vista_ct_multas.Multas > 0 AND expedientes_fiscalizacion.anno = ".$reg_sanc->anno." AND expedientes_fiscalizacion.numero = ".$reg_sanc->numero." AND expedientes_fiscalizacion.sector = ".$reg_sanc->sector." AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno"; 
		}
		$resultado = $con->query($sqlsanc);
		$tablasanc =  $resultado->fetch_object();
		$acumulado += $tablasanc->cantidad;
	}
	$conc_sancionado = $acumulado;

	if ($concluidos > $conc_sancionado) 
	{
		$conc_conforme = $concluidos - $conc_sancionado;
	} else {
		$conc_conforme = 0;
	}
	//echo "Concluidos: ".$concluidos." Sancionados: ".$conc_sancionado." Conformes: ".$conc_conforme.'<br>';
    $proc_final = $proceso_inicio + $notificadas - $concluidos;

	//AGREGAMOS EL REGISTRO
	/*if ($fiscales > 0 AND $reg->clasificacion <> 'OP')
	{*/
		$insert = "INSERT INTO sg_cuadro_4_2 (programa, coordinadores, supervisores, fiscales, resguardo, emitidas, notificadas, anuladas, proceso_inicio, meta, conc_sancionado, conc_conforme, proc_final, periodo_inicio, periodo_fin) VALUES ('".$programa."', ".$coordinadores.", ".$supervisores.", ".$fiscales.", ".$resguardo.", ".$emitidas.", ".$notificadas.", ".$anuladas.", ".$proceso_inicio.", ".$meta.", ".$conc_sancionado.", ".$conc_conforme.", ".$proc_final.", '".$inicio."', '".$fin."')";
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