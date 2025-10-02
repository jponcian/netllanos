<?php 

	include "../conexion.php";
//include "../auxiliar.php";

	
	//VARIABLES
	$Anno_memo = date("Y");;
	$num_memo = $_POST['nummemo'];
	$sector = $_POST['sector'];
	$año = date("Y");
	$json = array();
	$generado = false;
	$mensaje = "";
	$retencion = $_POST['ret'];
	
	//Verificamos si existe registros en el temporar
	$sqlTemporal = "SELECT * FROM ct_temp_salida_Expediente WHERE Anno_memo=".$Anno_memo." AND NroMemo=".$num_memo." AND sector=".$sector;
	$result = $conexionsql->query($sqlTemporal);
	$cantidad = $result->num_rows;

	if ($cantidad > 0 )
	{
		
		$insert = "INSERT INTO ct_salida_expediente (Anno_memo, NroMemo, sector, FechaEmision, Anno_Providencia, NroAutorizacion, Rif, Nombre, 
		FechaNotificacion, FechaRecepcion, Division, Anno_Resolucion, NroResolucion, FechaResolucion, Monto_Reparo, Impto_Omitido, 
		Multa_Reparo, Intereses, Multa_DF, Monto_Pagado, NumActa, FechaActa, FechaNotificacionActa, Status, Clausurado, FP, Tipo, Contenido, 
		Folio,ESPECIAL,Notificacion) SELECT Anno_memo, NroMemo, sector, FechaEmision, Anno_Providencia, NroAutorizacion, Rif, Nombre, FechaNotificacion, FechaRecepcion, 
		Division, Anno_Resolucion, NroResolucion, FechaResolucion, Monto_Reparo, Impto_Omitido, Multa_Reparo, Intereses, Multa_DF, Monto_Pagado, 
		NumActa, FechaActa, FechaNotificacionActa, Status, Clausurado, FP, Tipo, Contenido, Folio, ESPECIAL, Notificacion FROM ct_temp_salida_Expediente 
		WHERE Anno_memo=".$Anno_memo." AND NroMemo=".$num_memo." AND sector=".$sector;
		//echo $insert.'<br/>';
		$guardar = $conexionsql->query($insert);
		//$proceso = "proceso ".$guardar->affected_rows;
		//echo "Guardando ".$i.'<br/>';

		//ACTUALIZAMOS EL ESTATUS DEL EXPEDIENTE EN CASO DE SUMARIO ADMINISTRATIVO
		if ($retencion == 1)
		{
			$temp = "SELECT Anno_Providencia, NroAutorizacion, sector FROM ct_temp_salida_Expediente";
			$rs_temp = $conexionsql->query($temp);
			while ($valor = $rs_temp->fetch_array()){
				$sql_sumario = "UPDATE expedientes_fiscalizacion SET status=8, fecha_transferencia = date(now()) WHERE anno=".$valor['Anno_Providencia']." AND numero=".$valor['NroAutorizacion']." AND sector=".$valor['sector']; //echo $sql_sumario;
				$actualizar = $conexionsql->query($sql_sumario);
	
				$consulta="UPDATE liquidacion SET status = 10, fecha_transferencia_a_liq = date(now()), usuario_transferencia_a_liq=".$_SESSION['CEDULA_USUARIO'].", usuario=".$_SESSION['CEDULA_USUARIO']." WHERE sector=".$valor['sector']." AND anno_expediente=".$valor['Anno_Providencia']." AND num_expediente=".$valor['NroAutorizacion']." AND origen_liquidacion=4"; //echo "Aquiii... ".$consulta;
				$tabla = $conexionsql->query($consulta);
			}

		}

		//BORRAMOS EL TEMPORAL
		$borrar = "DELETE FROM ct_temp_salida_Expediente WHERE Anno_memo=".$Anno_memo." AND NroMemo=".$num_memo." AND sector=".$sector;
		$procesar = $conexionsql->query($borrar);

		$generado = true;
		$mensaje = "!!!... Memorando Generado con éxito ...!!!";
			
	} else {
		$generado = false;
		$mensaje = "!!!... No existen registros para generar el memorando, por favor verifique ...!!!";
	}
	
	$json = array(
		'generado' => $generado,
	 	'mensaje' => $mensaje
	);

	echo json_encode($json);
?>