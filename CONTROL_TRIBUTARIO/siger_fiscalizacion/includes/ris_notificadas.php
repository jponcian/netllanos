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

BorrarRegistros($conexion, 'ris_notificadas', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS
//$sqltipo = "CALL RIS_Notificadas ( $inicio, $fin )";
$sqltipo = "SELECT
expedientes_fiscalizacion.anno,
expedientes_fiscalizacion.numero,
expedientes_fiscalizacion.sector,
expedientes_fiscalizacion.rif,
CONCAT(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,LPAD(expedientes_fiscalizacion.numero,4,'0')) AS numprov,
a_tipo_programa.clasificacion,
a_tipo_providencia.Siglas1 AS tipo_impuesto,
CONCAT(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,LPAD(expedientes_fiscalizacion.numero,4,'0'),'/',resoluciones.anno,'/',lpad(resoluciones.numero,4,'0')) AS numresolucion,
resoluciones.fecha AS emision_resolucion,
Sum(liquidacion.monto_bs/liquidacion.concurrencia*liquidacion.especial) AS multa,
Max(a_sancion.dias) AS clausura,
liquidacion.fecha_not,
resoluciones.fecha_liq_not
FROM expedientes_fiscalizacion INNER JOIN resoluciones ON resoluciones.id_sector = expedientes_fiscalizacion.sector AND resoluciones.anno_expediente = expedientes_fiscalizacion.anno AND resoluciones.num_expediente = expedientes_fiscalizacion.numero INNER JOIN z_siglas ON z_siglas.id_sector = resoluciones.id_sector INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN liquidacion ON liquidacion.sector = expedientes_fiscalizacion.sector AND liquidacion.anno_expediente = expedientes_fiscalizacion.anno AND liquidacion.num_expediente = expedientes_fiscalizacion.numero INNER JOIN a_sancion ON a_sancion.id_sancion = liquidacion.id_sancion
WHERE resoluciones.fecha_liq_not <> '0000-00-00' AND resoluciones.fecha_liq_not BETWEEN '".$inicio."' AND '".$fin."'
GROUP BY expedientes_fiscalizacion.sector, expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero"; 
//echo $sqltipo.'<br>';
$tablaActas = $con->query($sqltipo);
while ($reg = $tablaActas->fetch_object())
{

    $rif = $reg->rif;
    $num_providencia = $reg->numprov;

	if ($reg->clasificacion === 'FIN' or $reg->clasificacion === 'FPN') 
    { 
		$tipo_programa = 0; 
	} else {
		$tipo_programa = 1; 		
	}

    if (substr($reg->tipo_impuesto, -1) == "/") 
	{
		$tipo_impuesto = substr($reg->tipo_impuesto, 0,-1);
	}

    $conformidad = 0;
    $num_resolucion = $reg->numresolucion;
    $tipo_contribuyente = 1;
    $emision = $reg->emision_resolucion;
    $notificacion = $reg->fecha_liq_not;

    if ($reg->multa > 0)
    {
        $multa = 1;
    } else {
        $multa = 0;        
    }

    $comiso = 0;
    $clausura = $reg->clausura;
    $suspension = 0;
    $pp_multa = $reg->multa;
    $pp_valor_bienes = 0;

	$agregar = "INSERT INTO ris_notificadas (rif, num_providencia, tipo_programa, tipo_impuesto, conformidad, num_resolucion, tipo_contribuyente, emision, notificacion, multa, comiso, clausura, suspension, pp_multa, pp_valor_bienes, periodo_inicio, periodo_fin) 
    VALUES ('".$rif."', '".$num_providencia."', ".$tipo_programa.", '".$tipo_impuesto."', ".$conformidad.", '".$num_resolucion."', ".$tipo_contribuyente.", '".$emision."', '".$notificacion."', ".$multa.", ".$comiso.", ".$clausura.", ".$suspension.", ".$pp_multa.", ".$pp_valor_bienes.", '".$inicio."', '".$fin."')";
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

function fecha_not_resolucion($con, $anno, $numero, $sector)
{
	$sql = "SELECT liquidacion.fecha_not FROM liquidacion WHERE liquidacion.sector = ".$sector." AND liquidacion.anno_expediente = ".$anno." AND liquidacion.num_expediente = ".$numero." GROUP BY liquidacion.sector, liquidacion.anno_expediente, liquidacion.num_expediente";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	$fecha_notificacion = $reg->fecha_not;
	return $fecha_notificacion;
}

?>