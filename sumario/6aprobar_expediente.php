<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Aprobar Expedientes Sumario</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	//include "multa_sumario.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}
	$acceso = 59;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	//--------------------
	$status = 8;
	$status2 = 0;
	$_SESSION['SEDE'] = $_POST['OSEDE'];
	//--------------------

	?>

</head>

<body style="background: transparent !important;">

	<?php
	function monto_ajustado($id_expediente, $periodo_I, $periodo_F)
	{
		$sqlajustes = "SELECT sumario_ajustes.motivo_error_material, sumario_ajustes.motivo_error_calculo, sumario_ajustes.monto_bi_error_material, sumario_ajustes.monto_impto_error_material, sumario_ajustes.monto_bi_error_calculo, sumario_ajustes.monto_impto_error_calculo FROM sumario_ajustes INNER JOIN sumario_tipo_ajuste ON sumario_tipo_ajuste.id_ajuste = sumario_ajustes.id_tipo_ajuste WHERE sumario_ajustes.id_expediente = " . $id_expediente . " and sumario_ajustes.periodoinicio = '" . $periodo_I . "' and sumario_ajustes.periodohasta = '" . $periodo_F . "'";
		$tabla = mysql_query($sqlajustes);
		$cantidad = mysql_num_rows($tabla);
		//echo $cantidad;
		if ($cantidad > 0) {
			$monto_ajuste = 0;
			while ($monto_aj = mysql_fetch_object($tabla)) {
				if ($monto_aj->monto_bi_error_material > 0) {
					$monto_ajuste = $monto_ajuste + $monto_aj->monto_impto_error_material;
				} else {
					$monto_ajuste = $monto_ajuste + $monto_aj->monto_impto_error_calculo;
				}
			}
		}
		return $monto_ajuste;
	}
	?>

	<p>
		<?php include "../titulo.php";
		if ($_POST['CMDRECHAZAR'] == "Rechazar") {
			//ACTUALIZAMOS EL ESTATUS DEL EXPEDIENTE
			$sql_concluir = "UPDATE expedientes_sumario SET status=1 WHERE anno_expediente_fisc = 0" . $_POST['OANNO'] . " AND num_expediente_fisc = 0" . $_POST['ONUMERO'];
			$tabla_c = mysql_query($sql_concluir);

			// MENSAJE
			echo "<script type=\"text/javascript\">alert('Expediente(s) Rechazado(s) Exitosamente...!!!');</script>";
			//-- CAMBIO DE LA DIRECCION
			echo '<meta http-equiv="refresh" content="0";/>';
		}

		if ($_POST['CMDAPROBAR'] == "Aprobar") {
			$_SESSION['ORIGEN'] = 5;

			//---------------- PARA LA MULTA

			$sql_confirmado = "SELECT expedientes_sumario.rif, fis_actas_detalle.COT, sumario_resultado.monto_confirmado, fis_actas_detalle.fecha_vencimiento, expedientes_sumario.anno_expediente_fisc, expedientes_sumario.num_expediente_fisc, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta, expedientes_sumario.sector, expedientes_sumario.rif FROM sumario_resultado INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta INNER JOIN expedientes_sumario ON expedientes_sumario.id_expediente = sumario_resultado.id_expediente INNER JOIN fis_actas ON fis_actas.id_acta = fis_actas_detalle.id_acta WHERE sumario_resultado.id_expediente = " . $_POST['OID']; //echo $sql_confirmado;
			$tabla_c = mysql_query($sql_confirmado);
			while ($multa = mysql_fetch_object($tabla_c)) {
				$rif = $multa->rif;
				$montoajustado = monto_ajustado($_POST['OID'], $multa->periodo_desde, $multa->periodo_hasta);
				$multa_info = multa_sumario(($multa->monto_confirmado + $montoajustado), $multa->periodo_hasta, $multa->COT);
				$ut = $multa_info[0] / $_SESSION['VALOR_UT_PRIMITIVA'];
				$multa_actual = $multa_info[1];
				$sancion = $multa_info[2];
				$sector = $multa->sector;
				$anno = $multa->anno_expediente_fisc;
				$numero = $multa->num_expediente_fisc;
				$multa_acumulada += $multa_actual;

				if ($multa_acumulada > 0) {
					$consulta4 = "INSERT INTO liquidacion ( sector , origen_liquidacion , anno_expediente , num_expediente , rif , periodoinicio , periodofinal , id_sancion , id_tributo , porcion , monto_ut , monto_bs , fecha_vencimiento, usuario ) VALUES ( " . $multa->sector . ",  " . $_SESSION['ORIGEN'] . ",  " . $multa->anno_expediente_fisc . ", " . $multa->num_expediente_fisc . ", '" . $multa->rif . "',  '" . $multa->periodo_desde . "',  '" . $multa->periodo_hasta . "', " . $sancion . ", 8,  '1',  '" . $ut . "',  '" . formato_moneda2($multa_actual) . "', '" . $multa->fecha_vencimiento . "', " . $_SESSION['CEDULA_USUARIO'] . ");"; //echo $consulta4.'<br>';
					$tabla4 = mysql_query($consulta4);
				}
			}
			//-----------------

			//---------------- PARA EL INTERES POR LA PARTE CONFIRMADA
			$sql_confirmada = "SELECT expedientes_sumario.id_expediente, sumario_resultado.monto_confirmado, fis_actas_detalle.fecha_vencimiento, expedientes_sumario.sector, expedientes_sumario.anno_expediente_fisc, expedientes_sumario.num_expediente_fisc, expedientes_sumario.rif, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta, fis_actas_detalle.monto_pagado, fis_actas_detalle.fecha_pago FROM expedientes_sumario INNER JOIN sumario_resultado ON sumario_resultado.id_expediente = expedientes_sumario.id_expediente INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta WHERE expedientes_sumario.id_expediente = " . $_POST['OID']; //echo $sql_confirmada.'<br>';
			$tabla_confirmada = mysql_query($sql_confirmada);
			while ($confirmada = mysql_fetch_object($tabla_confirmada)) {
				$montoajustado = monto_ajustado($_POST['OID'], $confirmada->periodo_desde, $confirmada->periodo_hasta);
				//echo "Aquiii .... ".$confirmada->monto_pagado;
				if ($confirmada->monto_pagado > 0) {
					$interes = funcion_interes(($confirmada->monto_confirmado + $montoajustado), voltea_fecha($confirmada->fecha_pago), voltea_fecha($confirmada->fecha_vencimiento));
				} else {
					$interes = funcion_interes(($confirmada->monto_confirmado + $montoajustado), date('d-m-Y'), voltea_fecha($confirmada->fecha_vencimiento));
				}
				$interes_acumulado += $interes;

				if ($interes > 0) {
					$consulta4 = "INSERT INTO liquidacion ( sector , origen_liquidacion , anno_expediente , num_expediente , rif , periodoinicio , periodofinal , id_sancion , id_tributo , porcion , monto_ut , monto_bs , fecha_vencimiento, fecha_pago, usuario ) VALUES ( " . $confirmada->sector . ",  " . $_SESSION['ORIGEN'] . ",  " . $confirmada->anno_expediente_fisc . ", " . $confirmada->num_expediente_fisc . ", '" . $confirmada->rif . "',  '" . $confirmada->periodo_desde . "',  '" . $confirmada->periodo_hasta . "', 2009, 99,  '1',  0,  '" . $interes . "', '" . $confirmada->fecha_vencimiento . "',date(now()), " . $_SESSION['CEDULA_USUARIO'] . ");"; //echo 'Int Confirmada: '.$consulta4.'<br>';
					$tabla4 = mysql_query($consulta4);
				}
			}

			//PARA TRANSFERIR LAS SANCIONES DE VDF GENERADAS EN FISCALIZACION
			$sql_vdf = "UPDATE liquidacion SET origen_liquidacion = 5 WHERE origen_liquidacion = 4 AND sector = " . $sector . " AND anno_expediente = " . $anno . " AND num_expediente = " . $numero;
			$tabla_vdf = mysql_query($sql_vdf);

			//--------------------------------
			include "0_guardar_sancion_impuesto.php";

			//---------------- PARA EL INTERES PARA LOS PAGOS EN SUMARIO
			//VERIFICAMOS SI EXISTEN PAGOS REGISTRADOS EN SUMARIO
			$sqlpagos = "SELECT sumario_pagos.id_expediente, sumario_pagos.id_pago, sumario_pagos.planilla, sumario_pagos.monto_pagado, expedientes_sumario.sector, expedientes_sumario.anno_expediente_fisc, expedientes_sumario.num_expediente_fisc, expedientes_sumario.rif, sumario_pagos.fecha_pago, sumario_pagos.usuario, sumario_pagos.fecha_proceso, fis_actas_detalle.fecha_vencimiento, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta FROM sumario_pagos INNER JOIN expedientes_sumario ON expedientes_sumario.id_expediente = sumario_pagos.id_expediente INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_pagos.id_detalle_acta WHERE sumario_pagos.id_expediente = " . $_POST['OID'];
			$tabla_p = mysql_query($sqlpagos);
			$existe = mysql_num_rows($tabla_p);
			$i = 1;
			if ($existe > 0) {
				while ($pago = mysql_fetch_object($tabla_p)) {
					// CALCULO DE LOS INTERESES
					$interes = funcion_interes($pago->monto_pagado, voltea_fecha($pago->fecha_pago), voltea_fecha($pago->fecha_vencimiento));
					$interes_acumulado += $interes;
					if ($interes_acumulado > 0) {
						// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------
						$consulta4 = "INSERT INTO liquidacion ( sector , origen_liquidacion , anno_expediente , num_expediente , rif , periodoinicio , periodofinal , id_sancion , id_tributo , porcion , monto_ut , monto_bs , fecha_vencimiento, fecha_pago, monto_pagado, planilla, usuario ) VALUES (" . $pago->sector . ",  " . $_SESSION['ORIGEN'] . ", " . $pago->anno_expediente_fisc . ", " . $pago->num_expediente_fisc . ", '" . $pago->rif . "', '" . $pago->periodo_desde . "', '" . $pago->periodo_hasta . "', 2009, 99,  '1',  0,  '" . $interes . "', '" . $pago->fecha_vencimiento . "', '" . $pago->fecha_pago . "', 0" . $pago->monto_pagado . ", '" . $pago->planilla . "', " . $_SESSION['CEDULA_USUARIO'] . ");"; //echo $consulta4.'<br>';
						$tabla4 = mysql_query($consulta4);
					}
				}
			}

			//ACTUALIZAMOS EL ESTATUS DEL EXPEDIENTE
			$sql_concluir = "UPDATE expedientes_sumario SET status=3, fecha_aprobacion=date(now()), multa=" . formato_moneda2($multa_acumulada) . ", intereses=" . formato_moneda2($interes_acumulado) . " WHERE id_expediente=" . $_POST['OID'];
			$tabla_c = mysql_query($sql_concluir);

			//AGREGAMOS EL NUMERO DE RESOLUCION DE SUMARIO
			generar_resolucion($sector, $_SESSION['ORIGEN'], $anno, $numero);
			$resolucion = funcion_resolucion($sector, $_SESSION['ORIGEN'], $anno,  $numero);

			// MENSAJE
			echo "<script type=\"text/javascript\">alert('Expediente(s) Aprobado(s) Exitosamente bajo la Resolucion N\u00B0: $resolucion[0] de fecha " . voltea_fecha($resolucion[1]) . "');</script>";
			//-- CAMBIO DE LA DIRECCION
			echo '<meta http-equiv="refresh" content="0";/>';
		}
		?>

	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post" action="#vista">
		<table width="65%" border=1 align=center>
			<tr>
				<td bgcolor="#FF0000" height="50" colspan="5" align="center" class="Estilo7"><u>Aprobar Expedientes Sumario Administrativo</u></td>
			</tr>
		</table>

		<p></p>
		<table width="65%" border=1 align=center>
			<tr>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7 Estilo8">Dependencia =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class=""><span class="">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
									$origen = '';
									$consulta_x = 'SELECT sector, nombre FROM vista_sumario_exp_transferido_suma GROUP BY sector';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									// -------------------------------------
									$consulta_x = 'SELECT sector, nombre FROM vista_sumario_exp_transferido_suma WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' and status=8 and status2=0 GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									if ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								}
								?>
							</select>
						</span></p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7 Estilo8">A&ntilde;o =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class=""><span class="">
							<select name="OANNO" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									$consulta_x = 'SELECT anno_expediente_fisc as anno FROM expedientes_sumario WHERE sector=0' . $_POST['OSEDE'] . ' and status=2 GROUP BY anno_expediente_fisc';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OANNO'] == $registro_x['anno']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
									}
								}
								?>
							</select>
						</span></p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7 Estilo8">N&uacute;mero =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class=""><span class="">
							<select name="ONUMERO" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									echo "Aquiiiiii";
									$consulta_x = 'SELECT num_expediente_fisc as numero FROM expedientes_sumario WHERE sector=0' . $_POST['OSEDE'] . ' and status=2 GROUP BY anno_expediente_fisc, num_expediente_fisc';
									echo $consulta_x;
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['ONUMERO'] == $registro_x['numero']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
									}
								}
								?>
							</select>
						</span></p>
				</td>
			</tr>
		</table>
		<p></p>

		<!--MOSTRAMOS EL RESULTADO DEL EXPEDIENTE-->
		<?php
		$sql_id = "SELECT id_expediente as id, rif, num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha, anno_expediente_fisc, num_expediente_fisc FROM expedientes_sumario WHERE sector = 0" . $_POST['OSEDE'] . " AND anno_expediente_fisc = 0" . $_POST['OANNO'] . " AND num_expediente_fisc = 0" . $_POST['ONUMERO']; //echo $sql_id ;
		$result_id = mysql_query($sql_id);
		$cantidad = mysql_num_rows($result_id);
		$valor_id = mysql_fetch_object($result_id);
		$rif = $valor_id->rif;
		$id = $valor_id->id;
		$anno_prov = $valor_id->anno_expediente_fisc;
		$num_prov = $valor_id->num_expediente_fisc;
		$_POST['OID'] = $id;
		?> <input name="OID" type="hidden" value="<?php echo $_POST['OID']; ?>"> <?php

																				if ($cantidad > 0) {

																					$consulta = "SELECT contribuyente, rif, direccion FROM vista_contribuyentes_direccion WHERE (((rif)='" . $rif . "'));";
																					$tabla = mysql_query($consulta);
																					$registro = mysql_fetch_object($tabla)
																				?>
			<p>&nbsp;</p>
			<table width="75%" border=1 align=center>
				<td bgcolor="#FF0000" height="27" colspan="6" align="center" class="Estilo7"><u>Contribuyente</u></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" height=27>
						<div align="center" class="Estilo14"><strong>Rif</strong></div>
					</td>
					<td bgcolor="#CCCCCC" colspan="3">
						<div align="center" class="Estilo14"><strong>Contribuyente</strong></div>
					</td>
				</tr>
				<tr>
				<tr>
					<td width="19%" height=27>
						<div align="center" class="Estilo15"><?php echo formato_rif($registro->rif); ?></div>
					</td>
					<td colspan="3">
						<div align="left" class="Estilo15"><?php echo $registro->contribuyente; ?></div>
					</td>
				</tr>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" height=27 align="center" colspan="6">
						<div align="center" class="Estilo14"><strong>Domicilio Fiscal</strong></div>
					</td>
				</tr>
				</tr>
				<tr>
					<td colspan="6">
						<div align="left" class="Estilo15"><?php echo $registro->direccion; ?></div>
					</td>
				</tr>
			</table>

			</table>
			<p></p>
			<table width="75%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="27" colspan="8" align="center" class="Estilo7"><u>Resumen del Acta</u></td>
				</tr>
				<tr>
					<td colspan="2" bgcolor="#CCCCCC">
						<div align="center" class="Estilo5"><strong><span class="Estilo13">N&uacute;mero</span></a></strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Ejercicio &oacute; Periodo</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Reparo</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Impto Omitido</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Notificaci&oacute;n</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Fecha Recepci&oacute;n</strong></div>
					</td>
				</tr>
				<?php
																					global $monto_revocado;

																					$consulta = "SELECT reparo, impuesto_omitido, numacta, periodo_desde, periodo_hasta, date_format(fecha_notificacion, '%d-%m-%Y') as fecha_notificacion, date_format(fecha_recepcion_sumario, '%d-%m-%Y') as fecha_recepcion_sumario FROM vista_sumario_exp_transferido WHERE rif='" . $rif . "' AND anno=" . $anno_prov . " AND numero=" . $num_prov . " AND sector=" . $_POST['OSEDE'] . " ORDER BY anno, numero;";
																					$tabla = mysql_query($consulta);

																					while ($registro = mysql_fetch_object($tabla)) {
																						echo '<tr><td  colspan="2" ><div align="center" class="Estilo15">';
																						echo $registro->numacta;
																						echo '</div></td><td height=27><div align="center" class="Estilo15">';
																						echo $registro->periodo_desde . ' - ' . $registro->periodo_hasta;
																						echo '</div></td><td ><div align="center" class="Estilo15">';
																						echo '<label>' . formato_moneda($registro->reparo) . '</label>';
																						echo '</div></td><td ><div align="center" class="Estilo15">';
																						echo '<label>' . formato_moneda($registro->impuesto_omitido) . '</label>';
																						echo '</div></td><td ><div align="center" class="Estilo15">';
																						echo '<label>' . $registro->fecha_notificacion . '</label>';
																						echo '</div></td><td ><div align="center" class="Estilo15">';
																						echo '<label>' . $registro->fecha_recepcion_sumario . '</label>';
																						echo '</div></td> </tr>';
																						$monto_omitido += $registro->impuesto_omitido;
																						$COT =  $registro->COT;
																					}
				?>
				</tbody>
			</table>
			<p></p>
			<?php
																					global $monto_revocado;

																					$_SESSION['ANNO_PRO'] = $anno_prov;
																					$_SESSION['NUM_PRO'] = $num_prov;
																					$_SESSION['SEDE_USUARIO'] = $_POST['OSEDE'];
																					$_SESSION['ORIGEN'] = 4;
																					$mostrarboton = 'NO';
																					$serie = "1=1";
																					include "../funciones/0_sanciones_aplicadas.php";

																					//VERIFICAMOS SI TIENE ESCRITO DE DESCARGO
																					$sql_escrito = "SELECT id_expediente as id, num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha, impuesto_omitido FROM expedientes_sumario WHERE sector = " . $_POST['OSEDE'] . " AND anno_expediente_fisc = " . $anno_prov . " AND num_expediente_fisc = " . $num_prov . " AND num_escrito_descargo<>''";
																					$result = mysql_query($sql_escrito);
																					$existe = mysql_num_rows($result);
																					if ($existe > 0) {
																						$valor = mysql_fetch_object($result);
			?>
				<table width="75%" border=1 align=center>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="4" align="center" class="Estilo7"><u>Escrito de Descargo</u></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo5"><strong><span class="Estilo13">N&uacute;mero de Recepci�n Correspondencia:</span></a></strong></div>
						</td>
						<td align="center"><?php echo $valor->num_escrito_descargo ?></td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Fecha de Recepci�n:</strong></div>
						</td>
						<td align="center"><?php echo $valor->fecha ?></td>
					</tr>
				</table>
				<p></p>
			<?php
																					} ?>


			<?php
																					global $monto_revocado;

																					$sql_gestiones = "SELECT sumario_gestion_ponente.id_gestion, sumario_gestion_ponente.id_observacion, a_codigos_observacion.observacion, sumario_gestion_ponente.otras, date_format(sumario_gestion_ponente.fecha_observacion, '%d-%m-%Y') as fecha_observacion, sumario_gestion_ponente.usuario FROM a_codigos_observacion INNER JOIN sumario_gestion_ponente ON sumario_gestion_ponente.id_observacion = a_codigos_observacion.id_observacion WHERE sumario_gestion_ponente.id_expediente=" . $id . " ORDER BY sumario_gestion_ponente.id_gestion DESC;";
																					$result = mysql_query($sql_gestiones);
																					$gestiones = mysql_num_rows($result);

																					if ($gestiones > 0) {
			?>
				<table width="75%" border=1 align=center>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="4" align="center" class="Estilo7"><u>Gestiones Efectuadas al Expediente</u></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC" align="center"><strong><span class="Estilo13">#</span></strong></td>
						<td bgcolor="#CCCCCC"><strong>Descripci&oacute;n</strong></td>
						<td bgcolor="#CCCCCC" align="center"><strong>Fecha de la Gesti�n:</strong></td>
					</tr>
				<?php
																						$i = 1;
																						while ($reg = mysql_fetch_object($result)) {
																							echo '<tr><td align="center">' . $i . '</td>';
																							if ($reg->id_observacion == 25) {
																								$descripcion = strtoupper($reg->otras);
																							} else {
																								$descripcion = strtoupper($reg->observacion);
																							}
																							echo '<td >' . $descripcion . '</td>';
																							echo '<td align="center">' . $reg->fecha_observacion . '</td>';
																							echo '</tr>';
																							$i += 1;
																						}
																					}

				?>
				</table>
				<p></p>
				<?php
																					//PARA MOSTRAR LOS PAGOS CARGADOS AL EXPEDIENTE
																					$sqlpagos = "SELECT sumario_pagos.id_pago, sumario_pagos.id_expediente, sumario_pagos.id_detalle_acta, sumario_pagos.planilla, sumario_pagos.monto_pagado, sumario_pagos.fecha_pago, sumario_pagos.usuario, sumario_pagos.fecha_proceso, fis_actas_detalle.id_detalle, a_tributos.nombre, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta FROM sumario_pagos INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_pagos.id_detalle_acta INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE sumario_pagos.id_expediente = " . $id;
																					$tabla = mysql_query($sqlpagos);
																					$cantidad = mysql_num_rows($tabla);
																					if ($cantidad > 0) {
				?>
					<p></p>
					<table width="75%" border=1 align=center>
						<tr>
							<td bgcolor="#FF0000" height="27" colspan="5" align="center" class="Estilo7"><u>Pagos Agregados al Expediente</u></td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC" align="center"><strong>Tributo</strong></div>
							</td>
							<td bgcolor="#CCCCCC" align="center"><strong>Periodo</strong></div>
							</td>
							<td bgcolor="#CCCCCC" align="center"><strong>Planilla N�</strong></div>
							</td>
							<td bgcolor="#CCCCCC" align="center"><strong>Fecha del Pago</strong></td>
							<td bgcolor="#CCCCCC" align="center"><strong>Monto Pagado</strong></td>
						</tr>
						<?php
																						$monto_pagado = 0;
																						while ($reg = mysql_fetch_object($tabla)) { ?>
							<tr>
								<td align="center"><?php echo $reg->nombre ?></td>
								<td align="center"><?php echo voltea_fecha($reg->periodo_desde) . ' al ' . voltea_fecha($reg->periodo_hasta) ?></td>
								<td align="center"><?php echo $reg->planilla ?></td>
								<td align="center"><?php echo voltea_fecha($reg->fecha_pago) ?></td>
								<td align="center"><?php echo formato_moneda($reg->monto_pagado) ?></td>
							</tr>
						<?php
																							$monto_pagado += $reg->monto_pagado;
																						}
																						$monto_revocado = $monto_revocado - $monto_pagado;
																						$_POST['OMONTOPAGADO'] = $monto_pagado;
						?>
					</table>
				<?php
																					}
				?>

				<p>&nbsp;</p>
				<?php
																					//************************************************************************************************************//
																					//                                   PARA MOSTRAR LOS AJUSTES EN SUMARIO                                      //
																					//************************************************************************************************************//

																					$sqlpagos = "SELECT sumario_ajustes.id_ajuste,sumario_tipo_ajuste.descripcion as tipoajuste, sumario_ajustes.periodoinicio, sumario_ajustes.periodohasta, sumario_ajustes.motivo_error_material, sumario_ajustes.motivo_error_calculo, sumario_ajustes.monto_bi_error_material, sumario_ajustes.monto_impto_error_material, sumario_ajustes.monto_bi_error_calculo, sumario_ajustes.monto_impto_error_calculo FROM sumario_ajustes INNER JOIN sumario_tipo_ajuste ON sumario_tipo_ajuste.id_ajuste = sumario_ajustes.id_tipo_ajuste WHERE sumario_ajustes.id_expediente = " . $_POST['OID'];
																					$tabla = mysql_query($sqlpagos);
																					$cantidad = mysql_num_rows($tabla);
																					if ($cantidad > 0) {
				?>
					<p></p>
					<table width="65%" border=1 align=center>
						<tr>
							<td bgcolor="#FF0000" height="40" colspan="6" align="center">
								<p class="Estilo7"><u>Ajustes Agregados al Expediente</u></p>
							</td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC" align="center"><strong>Tipo Ajuste</strong></div>
							</td>
							<td bgcolor="#CCCCCC" align="center"><strong>Periodo</strong></div>
							</td>
							<td bgcolor="#CCCCCC" align="center" width="30%"><strong>Motivo Ajuste</strong></div>
							</td>
							<td bgcolor="#CCCCCC" align="center"><strong>Ajuste BI Bs.</strong></td>
							<td bgcolor="#CCCCCC" align="center"><strong>Ajuste IMPUESTO Bs.</strong></td>
						</tr>
						<?php
																						while ($reg = mysql_fetch_object($tabla)) { ?>
							<tr>
								<td align="center"><?php echo $reg->tipoajuste ?></td>
								<td align="center"><?php echo voltea_fecha($reg->periodoinicio) . ' al ' . voltea_fecha($reg->periodohasta) ?></td>
								<?php
																							if ($reg->monto_bi_error_material > 0) {
																								$motivo_ajuste = $reg->motivo_error_material;
																								$montoajuste_bi = $reg->monto_bi_error_material;
																								$montoajuste_impto = $reg->monto_impto_error_material;
																							} else {
																								$motivo_ajuste = $reg->motivo_error_calculo;
																								$montoajuste_bi = $reg->monto_bi_error_calculo;
																								$montoajuste_impto = $reg->monto_impto_error_material;
																							}
								?>
								<td align="center"><?php echo $motivo_ajuste ?></td>
								<td align="center"><?php echo formato_moneda($montoajuste_bi) ?></td>
								<td align="center"><?php echo formato_moneda($montoajuste_impto) ?></td>
							</tr>
						<?php
																						}
						?>
					</table>
				<?php
																					}
				?>
				<p>&nbsp;</p>

				<p></p>
				<table width="75%" border=1 align=center>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="8" align="center" class="Estilo7"><u>Resultado del Expediente</u></td>
					</tr>
					<tr>
						<td align="center" bgcolor="#CCCCCC"><strong>Ponente</strong></td>
						<td align="center" bgcolor="#CCCCCC"><strong>Fecha Asignaci&oacute;n</strong></td>
						<td align="center" bgcolor="#CCCCCC"><strong>Fecha Culminaci&oacute;n</strong></td>
						<td align="center" bgcolor="#CCCCCC"><strong>Impuesto Omitido</strong></td>
						<td align="center" bgcolor="#CCCCCC"><strong>Monto Pagado Fisc</strong></td>
						<td align="center" bgcolor="#CCCCCC"><strong>Monto Pagado Sumario</strong></td>
						<td align="center" bgcolor="#CCCCCC"><strong>Monto Confirmado</strong></td>
						<td align="center" bgcolor="#CCCCCC"><strong>Monto Revocado</strong></td>
					</tr>
					<?php
																					$sql_result = "SELECT CONCAT_WS(' ', z_empleados.Nombres, z_empleados.Apellidos) AS ponente, date_format(expedientes_sumario.fecha_asignacion_ponente, '%d-%m-%Y') AS fecha_asignacion, sumario_resultado.monto_confirmado, sumario_resultado.monto_revocado, date_format(expedientes_sumario.fecha_culminacion, '%d-%m-%Y') AS fecha_conclusion, sumario_resultado.impuesto_por_pagar AS impuesto_omitido, fis_actas_detalle.monto_pagado, sumario_pagos.monto_pagado as pagos_sumario, sumario_resultado.id_detalle_acta FROM expedientes_sumario INNER JOIN z_empleados ON z_empleados.cedula = expedientes_sumario.cedula_ponente INNER JOIN sumario_resultado ON sumario_resultado.id_expediente = expedientes_sumario.id_expediente INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta LEFT JOIN sumario_pagos ON sumario_pagos.id_detalle_acta = sumario_resultado.id_detalle_acta WHERE expedientes_sumario.id_expediente=" . $id; //echo $sql_result;
																					$result = mysql_query($sql_result);
																					while ($registro = mysql_fetch_object($result)) {
																						echo '<tr>';
																						echo '<td align="center">' . $registro->ponente . '</td>';
																						echo '<td align="center">' . $registro->fecha_asignacion . '</td>';
																						echo '<td align="center">' . $registro->fecha_conclusion . '</td>';
																						echo '<td align="center">' . formato_moneda($registro->impuesto_omitido) . '</td>';
																						echo '<td align="center">' . formato_moneda($registro->monto_pagado) . '</td>';
																						echo '<td align="center">' . formato_moneda($registro->pagos_sumario) . '</td>';
																						echo '<td align="center">' . formato_moneda($registro->monto_confirmado) . '</td>';
																						$sql_motivo = "SELECT id_motivo, causas FROM sumario_motivo_revocatoria WHERE id_expediente=" . $id . " AND id_detalle=" . $registro->id_detalle_acta; //echo $sql_motivo; 
																						$result_m = mysql_query($sql_motivo);
																						$reg_motivo = mysql_fetch_object($result_m);
																						$_POST['OCAUSAS'] = 'CAUSAS DE REVOCATORIA111: ' . $reg_motivo->causas; //echo $_POST['OCAUSAS'];
					?>
						<td align="center"><?php echo formato_moneda($registro->monto_revocado) . ' ';
																						if ($registro->monto_revocado > 0) { ?><a href="javascript:func_motivos('<?php echo $_POST['OCAUSAS'] ?>')">causas</a><?php } ?></td>
					<?php
																						echo '</tr>';
																					}
					?>
				</table>

				<?php
																					//MOSTRAMOS LOS RESULTADOS DEL EXPEDIENTES
																					$sql_exp = "SELECT sumario_resultado.id_resultado, sumario_resultado.id_expediente, sumario_resultado.id_detalle_acta, sumario_resultado.impuesto_por_pagar, sumario_resultado.monto_confirmado, sumario_resultado.monto_revocado, sumario_resultado.usuario, sumario_resultado.fecha_proceso, CONCAT_WS(' al ',date_format(fis_actas_detalle.periodo_desde, '%d-%m-%Y'), date_format(fis_actas_detalle.periodo_hasta, '%d-%m-%Y')) as periodo FROM sumario_resultado INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta WHERE sumario_resultado.id_expediente =" . $id;
																					$resultado = mysql_query($sql_exp);
																					$registros = mysql_num_rows($resultado);
																					if ($registros > 0) { ?>
					<p></p>
					<table width="75%" border=1 align=center>
						<tr>
							<td bgcolor="#FF0000" height="40" colspan="6" align="center">
								<p class="Estilo7"><u>Resultados Agregados al Expediente</u></p>
							</td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC" align="center"><strong>Numero</strong></td>
							<td bgcolor="#CCCCCC" align="center"><strong>Periodo</strong></td>
							<td bgcolor="#CCCCCC" align="center"><strong>Impuesto por Pagar</strong></td>
							<td bgcolor="#CCCCCC" align="center"><strong>Monto Confirmado</strong></td>
							<td bgcolor="#CCCCCC" align="center"><strong>Monto Revocado</strong></td>
						</tr>
						<?php
																						$i = 1;
																						while ($valor = mysql_fetch_object($resultado)) { ?>
							<tr>
								<td align="center"><?php echo $i ?></td>
								<td align="center"><?php echo $valor->periodo ?></td>
								<td align="center"><?php echo formato_moneda($valor->impuesto_por_pagar) ?></td>
								<td align="center"><?php echo formato_moneda($valor->monto_confirmado) ?></td>
								<td align="center"><?php echo formato_moneda($valor->monto_revocado) ?></td>
							</tr>
						<?php
																							$i++;
																						}
						?>
					</table>
				<?php
																					}
				?>

				<p>&nbsp;</p>
				<table width="65%" border=1 align=center>
					<tr>
						<td bgcolor="#FF0000" height="40" colspan="8" align="center" class="Estilo7"><u>Multas e Intereses Generados Sumario Administrativo</u></td>
					</tr>
				</table>

				<?php
																					//OBTENEMOS EL MONTO CONFIRMADO
																					$sql_confirmado = "SELECT fis_actas_detalle.COT, sumario_resultado.monto_confirmado, fis_actas_detalle.fecha_vencimiento, expedientes_sumario.anno_expediente_fisc, expedientes_sumario.num_expediente_fisc, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta FROM sumario_resultado INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta INNER JOIN expedientes_sumario ON expedientes_sumario.id_expediente = sumario_resultado.id_expediente INNER JOIN fis_actas ON fis_actas.id_acta = fis_actas_detalle.id_acta WHERE sumario_resultado.id_expediente = " . $id; //echo $sql_confirmado;
																					$tabla_c = mysql_query($sql_confirmado);
				?>
				<p></p>
				<table width="65%" border=1 align=center>
					<tr>
						<td bgcolor="#666666" height="27" colspan="8" align="center" class="Estilo7"><u>Multas</u></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo5"><strong><span class="Estilo13">Numero</span></a></strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Concepto</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Periodo</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>UT Primitiva</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Valor UT Primitiva</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Multa Primitiva (BsS)</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Valor UT Actual</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Multa Actual (BsS)</strong></div>
						</td>
					</tr>
					<?php
																					$i = 1;
																					while ($monto = mysql_fetch_object($tabla_c)) {
																						$montoajustado = monto_ajustado($id, $monto->periodo_desde, $monto->periodo_hasta);
																						$periodo = voltea_fecha($monto->periodo_desde) . ' al ' . voltea_fecha($monto->periodo_hasta);
																						$monto_confirmado = $monto->monto_confirmado;
																						$fecha_vencimiento = $monto->periodo_hasta;
																						$fecha_vencimientoI = $monto->fecha_vencimiento;

																						//*******CAMBIO DEL PORCENTAJE CORRECTO DE LA MULTA********//
																						$fecha1 = $monto->periodo_hasta;
																						$fecha2 = "2015-02-25";
																						//echo "...COT... : ".$monto->COT;;

																						/*if (strcmp($fecha1,$fecha2) < 0)
	{
		$cot = $monto->COT;
	}
	else
	{
		$cot= "112";
	}*/
																						$cot = $monto->COT; //echo $cot;
																						$monto = $monto->monto_confirmado + $montoajustado;
																						$multa_info = multa_sumario($monto, $fecha_vencimiento, $cot);
																						echo '<tr>';
																						echo '<td align="center">' . $i . '</td>';
																						echo '<td align="center">MULTA</td>';
																						echo '<td align="center">' . $periodo . '</td>';
																						echo '<td align="right">' . formato_moneda($multa_info[0] / $_SESSION['VALOR_UT_PRIMITIVA']) . '</td>';
																						echo '<td align="right">' . formato_moneda($_SESSION['VALOR_UT_PRIMITIVA']) . '</td>';
																						echo '<td align="right">' . formato_moneda($multa_info[0]) . '</td>';
																						echo '<td align="right">' . formato_moneda($_SESSION['VALOR_UT_ACTUAL']) . '</td>';
																						echo '<td align="right">' . formato_moneda($multa_info[1]) . '</td>';
																						echo '</tr>';
																					}
					?>
				</table>
				<p></p>
				<table width="65%" border=1 align=center>
					<tr>
						<td bgcolor="#666666" height="27" colspan="9" align="center" class="Estilo7"><u>Intereses</u></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo5"><strong><span class="Estilo13">Numero</span></a></strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Concepto</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Ejercicio � Periodo</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Planilla Pago</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Fecha Pago</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Fecha Vencimiento</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Monto Confirmado/Pagado</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Monto</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo13"><strong>Cuadro Demostrativo</strong></div>
						</td>
					</tr>
					<?php


																					$monto_pagado = 0;

																					//VERIFICAMOS SI EXISTEN PAGOS REGISTRADOS EN SUMARIO
																					$sqlpagos = "SELECT sumario_pagos.id_expediente, sumario_pagos.id_pago, sumario_pagos.planilla, sumario_pagos.monto_pagado, sumario_pagos.fecha_pago, sumario_pagos.usuario, sumario_pagos.fecha_proceso, fis_actas_detalle.fecha_vencimiento, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta FROM sumario_pagos INNER JOIN expedientes_sumario ON expedientes_sumario.id_expediente = sumario_pagos.id_expediente INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_pagos.id_detalle_acta WHERE sumario_pagos.id_expediente=" . $id; //echo $sqlpagos;
																					$tabla_p = mysql_query($sqlpagos);
																					$existe = mysql_num_rows($tabla_p);
																					$i = 1;
																					if ($existe > 0) {
																						while ($pago = mysql_fetch_object($tabla_p)) {
																							// CALCULO DE LOS INTERESES
																							$interes = funcion_interes($pago->monto_pagado, voltea_fecha($pago->fecha_pago), voltea_fecha($pago->fecha_vencimiento));
																							// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------

																							// AGREGAR LA SANCION A LIQUIDACION -----------------------------------------------------
																							$sancion = 2009;
																							echo '<tr>';
																							echo '<td align="center">' . $i . '</td>';
																							echo '<td align="center">INTERESES</td>';
																							echo '<td align="center">' . voltea_fecha($pago->periodo_desde) . ' al ' . voltea_fecha($pago->periodo_hasta) . '</td>';
																							echo '<td align="center">' . $pago->planilla . '</td>';
																							echo '<td align="center">' . voltea_fecha($pago->fecha_pago) . '</td>';
																							echo '<td align="center">' . voltea_fecha($pago->fecha_vencimiento) . '</td>';
																							echo '<td align="right">' . formato_moneda($pago->monto_pagado) . '</td>';
																							echo '<td align="right">' . formato_moneda($interes) . '</td>';
																							$monto = $pago->monto_pagado;
																							$fecha_pago = $pago->fecha_vencimiento;
																							$fecha_venc = $pago->fecha_pago;
					?>
							<td align="center">
								<p align="center"><a href="0_interes2.php?monto=<?php echo $monto ?>&pago=<?php echo date("Y/m/d", strtotime($fecha_pago)) ?>&venc=<?php echo date("Y/m/d", strtotime($fecha_venc)) ?>" target=_BLANK>Ver Cuadro</a></p>
							</td>
						<?php
																							echo '</tr>';
																							$fecha_vencimiento = $pago->fecha_vencimiento;
																							$monto_pagado += $pago->monto_pagado;
																							$periodo = voltea_fecha($pago->periodo_desde) . ' al ' . voltea_fecha($pago->periodo_hasta);
																							$i++;
																						}
																					}

																					//OBTENEMOS EL MONTO CONFIRMADO
																					$sql_confirmado = "SELECT sumario_resultado.monto_confirmado, fis_actas_detalle.fecha_vencimiento, sumario_resultado.id_detalle_acta FROM sumario_resultado INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta WHERE id_expediente =" . $id;
																					$tabla_confirmada = mysql_query($sql_confirmado);

																					while ($valor_confirmada = mysql_fetch_object($tabla_confirmada)) {
																						//Obtenemos el periodo y los monto de ajustes de existir
																						$sqlperiodo = "SELECT fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta, sumario_resultado.id_detalle_acta, fis_actas_detalle.monto_pagado, fis_actas_detalle.fecha_pago FROM sumario_resultado INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta WHERE sumario_resultado.id_expediente = " . $id . " AND sumario_resultado.id_detalle_acta = " . $valor_confirmada->id_detalle_acta; //echo $sqlperiodo;
																						$tabla_periodo = mysql_query($sqlperiodo);
																						$valor_periodo = mysql_fetch_object($tabla_periodo);
																						$monto_pagoF = $valor_periodo->monto_pagado;
																						$fecha_pagoF = $valor_periodo->fecha_pago;
																						$montoajustado = monto_ajustado($id, $valor_periodo->periodo_desde, $valor_periodo->periodo_hasta);

																						$monto_confirmado = $valor_confirmada->monto_confirmado;
																						$monto = $monto_confirmado + $montoajustado; //- $monto_pagado;
																						$monto = str_replace(',', '.', $monto);
																						$fecha_vcto = $valor_confirmada->fecha_vencimiento;


																						//CALCULO DE LOS INTERESES POR LA PARTE CONFIRMADA EN CASO DE EXISTIR
																						if ($monto > 0) {
																							// CALCULO DE LOS INTERESES
																							if ($monto_pagoF > 0) {
																								$interes = funcion_interes($monto, voltea_fecha($fecha_pagoF), voltea_fecha($fecha_vcto));
																								$fecha_del_pago = voltea_fecha($fecha_pagoF);
																							} else {
																								$fecha_del_pago = date("d-m-Y");
																								$interes = funcion_interes($monto, $fecha_del_pago, voltea_fecha($fecha_vcto));
																							}
																							// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------
																							echo '<tr>';
																							//$i= $i+1;
																							echo '<td align="center">' . $i . '</td>';
																							echo '<td align="center">INTERESES</td>';
																							echo '<td align="center">' . $periodo . '</td>';
																							echo '<td align="center">Res. Sumario</td>';
																							echo '<td align="center">' . $fecha_del_pago . '</td>';
																							echo '<td align="center">' . voltea_fecha($fecha_vcto) . '</td>';
																							echo '<td align="right">' . formato_moneda($monto) . '</td>';
																							echo '<td align="right">' . formato_moneda($interes) . '</td>';
																							$monto = $monto;
																							$fecha_pago = date("Y-m-d");
																							$fecha_venc = $fecha_vcto;
						?>
							<td align="center">
								<p align="center"><a href="0_interes2.php?monto=<?php echo $monto ?>&pago=<?php echo date("Y/m/d", strtotime($fecha_del_pago)) ?>&venc=<?php echo date("Y/m/d", strtotime($fecha_venc)) ?>" target=_BLANK>Ver Cuadro</a></p>
							</td>
					<?php
																							echo '</tr>';
																						}
																					}

					?>
				</table>
				<p>&nbsp;</p>
				<!--<p align="center"><a href="0_interes2.php?monto=<?php echo $monto ?>&pago=<?php echo date("Y/m/d") ?>&venc=<?php echo date("Y/m/d", strtotime($fecha_vencimientoI)) ?>" target=_BLANK>Ver Cuadro demostrativo Intereses Moratorios</a></p>
	<p>&nbsp;</p>-->
				<table border=0 align=center>
					<tr>
						<td>
							<input type="submit" class="boton" name="CMDAPROBAR" value="Aprobar">
						</td>
						<td>
							<input type="submit" class="boton" name="CMDRECHAZAR" value="Rechazar">
						</td>
					</tr>
				</table>
				<textarea name="OCAUSAS" id="OCAUSAS" style="display:none;"><?php echo $_POST['OCAUSAS'] ?></textarea>
			<?php
																				}
			?>
			<a name="vista"></a>
			<p>&nbsp;</p>
	</form>
	<?php
	include "../pie.php"; ?>
	<p>&nbsp;</p>
	<script type="text/javascript">
		function func_motivos(id_motivo) {
			alert(id_motivo);
		}
	</script>
</body>

</html>