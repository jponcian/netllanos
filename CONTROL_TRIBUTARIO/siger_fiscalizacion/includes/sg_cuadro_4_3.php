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

BorrarRegistros($conexion, 'sg_cuadro_4_3', $inicio, $fin);

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
    
    $sql = "SELECT Sum(vista_actas_siger.reparo) AS cantidad FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."'";
    $reparo = contar($con, $sql);

    $sql = "SELECT sum(vista_actas_siger.impuesto_omitido) AS cantidad FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."'";
    $impuesto = contar($con, $sql);

    $rebaja = 0;

    $sql = "SELECT sum(vista_actas_siger.interes) AS cantidad FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."'";
    $intereses = contar($con, $sql);
    
    $sql = "AS cantidad FROM expedientes_fiscalizacion INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."'";
    $multa_reparo = contar($con, $sql);

    $sql = "SELECT Sum(vista_ct_multas.Multas) AS cantidad FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN vista_ct_multas ON vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero AND vista_ct_multas.sector = expedientes_fiscalizacion.sector INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE a_tipo_programa.descripcion = '".$reg->descripcion."' AND year(expedientes_fiscalizacion.fecha_notificacion) = $anno AND ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."'";
    $multa_vdf = contar($con, $sql);
    if (substr($clasificacion, 0, 3) == 'FIS')
    {
        if ($multa_vdf > $multa_reparo)
        {
            $multa_vdf = $multa_vdf - $multa_reparo;
        } else {
            $multa_vdf = 0;
        }
    }

	//AGREGAMOS EL REGISTRO
	/*if ($fiscales > 0 AND $reg->clasificacion <> 'OP')
	{*/
		$insert = "INSERT INTO sg_cuadro_4_3 (programa, reparo, impuesto, rebaja, intereses, multa_reparo, multa_vdf, periodo_inicio, periodo_fin) VALUES ('". $programa."', ". round($reparo, 2).", ". round($impuesto, 2).", ". $rebaja.", ". round($intereses, 2).", ". round($multa_reparo, 2).", ". round($multa_vdf, 2).", '". $inicio."', '". $fin."')";
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