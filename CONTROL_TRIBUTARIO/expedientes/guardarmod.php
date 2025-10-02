<?php

	include "../conexion.php";
//include "../auxiliar.php";


	$nummemo=1;//$_POST['nummemo'];
	$sector = 1;//$_POST['sector'];
	$fechamemo='2016/01/30';//date("Y-m-d",strtotime($_POST['fechamemo']));
	$Anno_memo=2016;//date("Y",strtotime($_POST['fechamemo']));
	$json = array();
	$proceso  = false;
	$permitido = false;
	$mensaje = "";

	$verificar_mod = $conexionsql->query("SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and (modificado=1 or borrado=1)");
	$cantidad = $verificar_mod->num_rows;

	if ($cantidad > 0)
	{

		//ELIMINAMOS LOS NUEVOS REGISTROS QUE FUERON ELIMINADOS
		$eliminar_modificados = $conexionsql->query("DELETE FROM ct_tmp_mod_salida_expediente WHERE Anno_memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and (modificado=1 AND borrado=1)");

		//AGREGAMOS A SALIDA DE EXPEDIENTES LOS NUEVOS REGISTROS
		$nuevos = $conexionsql->query("INSERT INTO ct_salida_expediente (Anno_memo, NroMemo, sector, FechaEmision, Anno_Providencia, NroAutorizacion, Rif, Nombre, 
								FechaNotificacion, FechaRecepcion, Division, Anno_Resolucion, NroResolucion, FechaResolucion, Monto_Reparo, Impto_Omitido, 
								Multa_Reparo, Intereses, Multa_DF, Monto_Pagado, NumActa, FechaActa, FechaNotificacionActa, Status, Clausurado, FP, Tipo, Contenido, 
								Folio) SELECT Anno_memo, NroMemo, sector, FechaEmision, Anno_Providencia, NroAutorizacion, Rif, Nombre, 
								FechaNotificacion, FechaRecepcion, Division, Anno_Resolucion, NroResolucion, FechaResolucion, Monto_Reparo, Impto_Omitido, 
								Multa_Reparo, Intereses, Multa_DF, Monto_Pagado, NumActa, FechaActa, FechaNotificacionActa, Status, Clausurado, FP, Tipo, Contenido, 
								Folio FROM ct_tmp_mod_salida_expediente WHERE Anno_memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and (modificado=1 AND borrado=0)");
		

		if ($conexionsql->affected_rows > 0) 
		{
			$permitido = true;
		}


		//ELIMINAOS LOS REGISTROS QUE ESTABAN EN SALIDA DE EXPEDIENTE QUE FUERON ELIMINADOS
		$borrar_eliminados = $conexionsql->query("DELETE FROM ct_salida_expediente WHERE id IN (SELECT id FROM ct_tmp_mod_salida_expediente WHERE Anno_memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and (modificado=0 AND borrado=1))");

		if ($conexionsql->affected_rows > 0) 
		{
			$permitido = true;
		}

		//BORRAMOS EL TEMPORAL
		$temporal = $conexionsql->query("DELETE FROM ct_tmp_mod_salida_expediente");

		if ($permitido == true)
		{
			$proceso = true;
			$mensaje = "Modificación guardada satisfactoriamente";
		} else
		{
			$proceso = false;
			$mensaje = "Problema al guardar la modificación";
		}
	} else {
		$proceso = false;
		$mensaje = "No se han efectuados modificaciones al memorando";
	}

	$json = array(
					'proceso' => $proceso,
					'mensaje' => $mensaje
				);


	echo json_encode($json);

?>