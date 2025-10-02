<?php
//***SCRIPT PARA REPORTE OTROS PROGRAMAS CONTROL FISCAL***//
//                                                        //
//       Elaborado por Gustavo García para el SENIAT      //
//                                                        //
////////////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");

//Variables a utilizar
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];

BorrarRegistros($conexion, 'programa_control_fiscal', $inicio, $fin);

//RECORREMOS LAS ACTAS NOTIFICADAS
//$sqlActas = "CALL Actas_Notificadas( $inicio, $fin )";
$sqlActas = "SELECT ct_destruccion_facturas.rif, ct_destruccion_facturas.tipo_solicitud, CONCAT(z_siglas.Siglas_resol_fis,'/',year(ct_destruccion_facturas.fecha_emision),'/',ct_destruccion_facturas.numero_acta) AS numeroacta, ct_destruccion_facturas.fecha_emision, z_sectores.nombre FROM ct_destruccion_facturas INNER JOIN z_siglas ON z_siglas.id_sector = ct_destruccion_facturas.sector INNER JOIN z_sectores ON z_sectores.id_sector = z_siglas.id_sector WHERE ct_destruccion_facturas.fecha_emision BETWEEN '".$inicio."' AND '".$fin."'";
//echo $sqlActas;
$tablaActas = $con->query($sqlActas);
while ($reg = $tablaActas->fetch_object())
{

	$agregar = "INSERT INTO programa_control_fiscal (rif, tipo_solicitud, numero_documento, emision, notificacion, periodo_inicio, periodo_fin) VALUES ('".$reg->rif."', '".$reg->tipo_solicitud."', '".$reg->numeroacta."', '".$reg->fecha_emision."', '".$reg->fecha_emision."', '".$inicio."', '".$fin."')";
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

?>