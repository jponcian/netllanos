<html>

<head>
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	function BuscarAjustes($id_detalle_acta)
	{
		$sql_ajustes = "SELECT (sumario_ajustes.monto_impto_error_material + sumario_ajustes.monto_impto_error_calculo) as  ajustes FROM sumario_ajustes WHERE sumario_ajustes.id_detalle_acta = " . $id_detalle_acta;
		$res_ajuste = mysql_query($sql_ajustes);
		$valor_ajuste = mysql_fetch_object($res_ajuste);
		$ajuste = $valor_ajuste->ajustes;
		return $ajuste;
	}

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}

	$sql_id = "SELECT id_expediente as id, num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha FROM expedientes_sumario WHERE origen_liquidacion = " . $_GET['origen'] . " AND sector = " . $_GET['sector'] . " AND anno_expediente_fisc = " . $_GET['anno'] . " AND num_expediente_fisc = " . $_GET['num'];
	$result_id = mysql_query($sql_id);
	$valor_id = mysql_fetch_object($result_id);
	$_POST['OID'] = $valor_id->id;

	if ($_POST['CMDCONCLUIR'] == 'Concluir') {
		//VERIFICAMOS SI EL O LOS RESULTADOS SE ENCUENTRAN AGREGADO
		$sql_exp = "SELECT sumario_resultado.id_resultado FROM sumario_resultado INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta INNER JOIN expedientes_sumario ON expedientes_sumario.id_expediente = sumario_resultado.id_expediente WHERE expedientes_sumario.id_expediente =" . $_POST['OID'];
		$resultado = mysql_query($sql_exp);
		$registros = mysql_num_rows($resultado);
		if ($registros > 0) {

			$sql = "SELECT sum(impuesto_por_pagar) as impuesto_por_pagar, sum(monto_confirmado) as monto_confirmado, sum(monto_revocado) as monto_revocado FROM sumario_resultado WHERE id_expediente = " . $_POST['OID'];
			$tabla = mysql_query($sql);
			$montos = mysql_fetch_object($tabla);
			$monto_por_pagar = $montos->impuesto_por_pagar;
			$monto_confirmado = $montos->monto_confirmado;
			$monto_revocado = $montos->monto_revocado;
			if ($_POST['OMONTOPAGADO'] == '') {
				$monto_pagado = 0;
			} else {
				$monto_pagado = $_POST['OMONTOPAGADO'];
			}

			//GUARDAMOS LA CONCLUSION
			$sql_concluir = "UPDATE expedientes_sumario SET status=2, fecha_culminacion=date(now()), monto_confirmado=" . $monto_confirmado . ", monto_revocado=" . $monto_revocado . ", pagos_sumario=" . $monto_pagado . " WHERE id_expediente=" . $_POST['OID'];
			$tabla_c = mysql_query($sql_concluir);
			echo "<script type=\"text/javascript\">alert('Expediente concluido satisfactoriamnete...!');</script>";
			echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
			exit();
		} else {
			echo "<script type=\"text/javascript\">alert('No se han agregado el(los) resultado(s) al Expediente...!');</script>";
		}
	}

	if ($_POST['CMDAGREGAR'] == 'Agregar') {
		if ($_POST['OPERIODO'] > 0) {
			//VALIDAR QUE SE HAYA INCLUIDO LOS MOTIVOS DE REVOCATORIA
			if ($_POST['OMONTO'] < $_POST['OMONTOACONFIRMAR'] and $_POST['OOBSERVACION'] == "") {
				echo "<script type=\"text/javascript\">alert('Se requiere que indique el(los) motivo(s) de revocatoria por favor verifique...!');</script>";
			} else {
				//VERIFICAMOS SI YA HA SIDO AGREGADO EL RESULTADO
				$sql_result = "SELECT monto_confirmado FROM sumario_resultado WHERE id_detalle_acta = " . $_POST['OPERIODO'];
				$result = mysql_query($sql_result);
				$cantidad = mysql_num_rows($result);
				if ($cantidad < 1) {
					/*echo 'Monto confirmado: '.$_POST["OCONFIRMADO"].'<br/>';
				echo 'Monto ingresado: '.$_POST["OMONTO"].'<br/>';
				echo 'Monto acta: '.$_POST["OMONTOACTA"].'<br/>';
				echo 'ID acta: '.$_POST["OPERIODO"].'<br/>';*/
					$insertar = 0;

					if ($_POST["OCONFIRMADO"] == 'Confirmado' and $_POST["OMONTO"] <= $_POST["OMONTOACONFIRMAR"]) {
						$insertar = 1;
						$monto_confirmado = $_POST["OMONTO"];
						$monto_revocado = $_POST["OMONTOACONFIRMAR"] - $_POST["OMONTO"];
					}
					if ($_POST["OREVOCADO"] == 'Revocado' and $_POST["OMONTO"] == $_POST["OMONTOAREVOCAR"]) {
						$insertar = 1;
						if ($_POST['OMONTOPAGADOSUMARIO'] > 0) {
							$monto_confirmado = $_POST['OMONTOPAGADOSUMARIO'];
						} else {
							$monto_confirmado = 0;
						}
						$monto_revocado = $_POST["OMONTO"];
					}

					if ($insertar == 1) {
						//AGREGAMOS EL REGISTRO
						$sql_add = "INSERT INTO sumario_resultado (id_expediente, id_detalle_acta, impuesto_por_pagar, monto_confirmado, monto_revocado, usuario) VALUES (" . $_POST['OID'] . ", " . $_POST['OPERIODO'] . ", " . formato_moneda2($_POST['OMONTOACONFIRMAR']) . ", " . formato_moneda2($monto_confirmado) . ", " . formato_moneda2($monto_revocado) . ", " . $_SESSION['CEDULA_USUARIO'] . ")";
						//echo $sql_add.'<br';
						$result = mysql_query($sql_add);

						if ($monto_revocado > 0) {
							$sql_motivo = "INSERT INTO sumario_motivo_revocatoria (id_expediente, id_detalle, causas, usuario) VALUES (" . $_POST['OID'] . ", " . $_POST['OPERIODO'] . ", '" . $_POST['OOBSERVACION'] . "', " . $_SESSION['CEDULA_USUARIO'] . ")";
							$result_motivo = mysql_query($sql_motivo);
						}
					} else {
						echo "<script type=\"text/javascript\">alert('El monto confirmado no puede ser mayor al impuesto omitido...!');</script>";
					}
				} else {
					echo "<script type=\"text/javascript\">alert('Ya ha sido agregado este resultado por favor verifique...!');</script>";
				}
			}
			//------------------------fiN-------------------------------
		} else {
			echo "<script type=\"text/javascript\">alert('Por favor se requiere que indique el periodo...!');</script>";
		}
	}

	$sqleliminar = "SELECT id_resultado as id, id_expediente, id_detalle_acta FROM sumario_resultado";
	$tabala_e = mysql_query($sqleliminar);
	while ($reg = mysql_fetch_object($tabala_e)) {
		if ($_POST[$reg->id] == 'Eliminar') {
			$sqldelete = mysql_query("DELETE FROM sumario_resultado WHERE id_resultado=" . $reg->id);
			if ($_POST['OIDCAUSAS'] <> '') {
				$sqlborrar = mysql_query("DELETE FROM sumario_motivo_revocatoria WHERE id_expediente=" . $reg->id_expediente . " AND id_detalle =" . $reg->id_detalle_acta);
				$_POST['OCAUSAS'] = '';
				$_POST['OIDCAUSAS'] = '';
			}
		}
	}
	?>

	<title>Concluir Expediente Ponente</title>
	<style type="text/css">
		<!--
		.Estilomenun {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight: bold;
		}

		body {
			background-image: url();
		}

		.Estilo5 {
			font-size: 12px
		}

		.Estilo7 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}

		.Estilo13 {
			font-size: 16px
		}

		.Estilo14 {
			font-size: 16px;
			font-weight: bold;
		}

		.Estilo15 {
			font-size: 14px;
		}

		.Estilo16 {
			color: #FF0000
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>

	</p>

	<form name="form1" method="post" action="#vista">
		<p>&nbsp;</p>

		<?php

		$concluir = 'Si';
		include "0_detalles_expediete.php";

		?>

		<?php
		global $monto_revocado;

		//VERIFICAMOS SI TIENE ESCRITO DE DESCARGO
		$sql_escrito = "SELECT id_expediente as id, num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha, impuesto_omitido FROM expedientes_sumario WHERE origen_liquidacion = " . $_GET['origen'] . " AND sector = " . $_GET['sector'] . " AND anno_expediente_fisc = " . $_GET['anno'] . " AND num_expediente_fisc = " . $_GET['num'] . " AND num_escrito_descargo<>''";
		$result = mysql_query($sql_escrito);
		$existe = mysql_num_rows($result);
		if ($existe > 0) {
			$valor = mysql_fetch_object($result);
		?>
			<table width="60%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="4" align="center">
						<p class="Estilo7"><u>Escrito de Descargo</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo5"><strong><span class="Estilo13">N&uacute;mero de Recepción Correspondencia:</span></a></strong></div>
					</td>
					<td align="center"><?php echo $valor->num_escrito_descargo ?></td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Fecha de Recepción:</strong></div>
					</td>
					<td align="center"><?php echo $valor->fecha;
										$_POST['OID'] = $valor->id ?><input name="OID" type="hidden" value="<?php echo $_POST['OID'] ?>"></td>
				</tr>
			</table>
			<p></p>
		<?php
		} ?>


		<?php
		global $monto_revocado;

		$sql_gestiones = "SELECT sumario_gestion_ponente.id_gestion, sumario_gestion_ponente.id_observacion, a_codigos_observacion.observacion, sumario_gestion_ponente.otras, date_format(sumario_gestion_ponente.fecha_observacion, '%d-%m-%Y') as fecha_observacion, sumario_gestion_ponente.usuario FROM a_codigos_observacion INNER JOIN sumario_gestion_ponente ON sumario_gestion_ponente.id_observacion = a_codigos_observacion.id_observacion WHERE sumario_gestion_ponente.id_expediente=" . $_POST['OID'] . " ORDER BY sumario_gestion_ponente.id_gestion DESC;";
		$result = mysql_query($sql_gestiones);
		$gestiones = mysql_num_rows($result);

		if ($gestiones > 0) {
		?>
			<table width="60%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="4" align="center">
						<p class="Estilo7"><u>Gestiones Efectuadas al Expediente</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" align="center"><strong><span class="Estilo13">#</span></strong></td>
					<td bgcolor="#CCCCCC"><strong>Descripci&oacute;n</strong></div>
					</td>
					<td bgcolor="#CCCCCC" align="center"><strong>Fecha de la Gestión:</strong></td>
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
			$sqlpagos = "SELECT sumario_pagos.id_pago, sumario_pagos.id_expediente, sumario_pagos.id_detalle_acta, sumario_pagos.planilla, sumario_pagos.monto_pagado, sumario_pagos.fecha_pago, sumario_pagos.usuario, sumario_pagos.fecha_proceso, fis_actas_detalle.id_detalle, a_tributos.nombre, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta FROM sumario_pagos INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_pagos.id_detalle_acta INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE sumario_pagos.id_expediente = " . $_POST['OID'];
			$tabla = mysql_query($sqlpagos);
			$cantidad = mysql_num_rows($tabla);
			if ($cantidad > 0) {
			?>
				<p></p>
				<table width="60%" border=1 align=center>
					<tr>
						<td bgcolor="#FF0000" height="40" colspan="5" align="center">
							<p class="Estilo7"><u>Pagos Agregados al Expediente</u></p>
						</td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC" align="center"><strong>Tributo</strong></div>
						</td>
						<td bgcolor="#CCCCCC" align="center"><strong>Periodo</strong></div>
						</td>
						<td bgcolor="#CCCCCC" align="center"><strong>Planilla N°</strong></div>
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
					$monto_revocado = $monto_revocado - $monto_pagado; //echo "Monto Revocado: ".$monto_revocado.'<br/>';
					$_POST['OMONTOPAGADO'] = $monto_pagado; //echo "Monto Pagado: ".$_POST['OMONTOPAGADO'].'<br/>';
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

			<table width="60%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="5" align="center">
						<p class="Estilo7"><u>Agregar Resultados al Expediente</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#666666" width="40%" height="27" colspan="2" align="center">
						<p class="Estilo7">Resultado
					</td>
					<td bgcolor="#666666" width="20%" height="27" align="center">
						<p class="Estilo7">Periodo
					</td>
					<td bgcolor="#666666" width="30%" height="27" align="center">
						<p class="Estilo7">Monto
					</td>
					<td bgcolor="#666666" width="10%" height="27" align="center">
						<p class="Estilo7">Acci&oacute;n
					</td>
				</tr>
				<tr>
					<td align="center"><input name="OCONFIRMADO" id="OCONFIRMADO" type="radio" value="Confirmado" <?php if (isset($_POST["OCONFIRMADO"])) {
																														echo "checked";
																													} ?> onClick="cambiar_estado(this.value)">Confirmado</td>
					<td align="center"><input name="OREVOCADO" id="OREVOCADO" type="radio" value="Revocado" onClick="cambiar_estado(this.value)" <?php if (isset($_POST["OREVOCADO"])) echo "checked"; ?>>Revocado</td>
					<td align="center">
						<select name="OPERIODO" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							//OBTENEMOS LOS MONTOS PAGADOS EN SUMARIO
							$consulta_l = "SELECT sum(sumario_pagos.monto_pagado) as monto FROM sumario_pagos WHERE sumario_pagos.id_expediente = " . $_POST['OID']; //echo $consulta_l;
							$tabla_l = mysql_query($consulta_l);
							$valor = mysql_fetch_object($tabla_l);
							$monto = $valor->monto;

							$monto_a_pagar = 0;
							$monto_ajustes = 0;
							$consulta_x = "SELECT fis_actas_detalle.id_detalle, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta, (fis_actas_detalle.impuesto_omitido - fis_actas_detalle.monto_pagado) as monto_a_pagar FROM expedientes_sumario INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_sumario.sector AND fis_actas.anno_prov = expedientes_sumario.anno_expediente_fisc AND fis_actas.num_prov = expedientes_sumario.num_expediente_fisc INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE expedientes_sumario.id_expediente = " . $_POST['OID'] . " AND (fis_actas_detalle.impuesto_omitido - fis_actas_detalle.monto_pagado) >= 0  AND Not fis_actas_detalle.id_detalle In (Select sumario_resultado.id_detalle_acta From sumario_resultado)"; //echo $consulta_x;
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OPERIODO'] == $registro_x['id_detalle']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['id_detalle'] . '>' . voltea_fecha($registro_x['periodo_desde']) . ' al ' . voltea_fecha($registro_x['periodo_hasta']) . '</option>';
								$monto_a_pagar += $registro_x['monto_a_pagar'];
							}

							?>
						</select>
					</td>
					<?php
					if ($_POST['OPERIODO'] > 0) {
						$monto_ajustes = BuscarAjustes($_POST['OPERIODO']);

						$sqlmonto = "SELECT (fis_actas_detalle.impuesto_omitido - fis_actas_detalle.monto_pagado) AS monto, fis_actas_detalle.impuesto_omitido, expedientes_fiscalizacion.programa FROM fis_actas_detalle LEFT JOIN sumario_pagos ON sumario_pagos.id_detalle_acta = fis_actas_detalle.id_detalle INNER JOIN fis_actas ON fis_actas.id_acta = fis_actas_detalle.id_acta INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = fis_actas.id_sector AND expedientes_fiscalizacion.anno = fis_actas.anno_prov AND expedientes_fiscalizacion.numero = fis_actas.num_prov WHERE fis_actas_detalle.id_detalle = " . $_POST['OPERIODO'] . " GROUP BY fis_actas_detalle.id_detalle"; //echo $sqlmonto;
						$tabla_g = mysql_query($sqlmonto);
						$valor_g = mysql_fetch_object($tabla_g);

						if ($valor_g->programa == 5 or $valor_g->programa == 22 or $valor_g->programa == 29) {
							$monto_por_revocar = $valor_g->impuesto_omitido - $monto_ajustes;
						} else {
							$monto_por_revocar = $valor_g->monto - $monto_ajustes;
						}

						$_POST['OMONTOACONFIRMAR'] = str_replace(",", ".", $monto_por_revocar);

						//VERIFICAMOS SI EXISTEN PAGOS EN SUMARIO
						$sql_pago_sumario = "SELECT sum(monto_pagado) as pagado_sumario FROM sumario_pagos WHERE id_detalle_acta=" . $_POST['OPERIODO'];
						$tabla_ps = mysql_query($sql_pago_sumario);
						$valor_ps = mysql_fetch_object($tabla_ps);
						$pagado_sumario = $valor_ps->pagado_sumario;
						if ($pagado_sumario > 0) {
							$_POST['OMONTOPAGADOSUMARIO'] = $pagado_sumario;
						}

						$_POST['OMONTOAREVOCAR'] = $monto_por_revocar - $pagado_sumario;
						if ($_POST["OCONFIRMADO"] == 'Confirmado') {
							$_POST['OMONTO'] = str_replace(",", ".", $_POST['OMONTOACONFIRMAR']);
						} else {
							$_POST['OMONTO'] = str_replace(",", ".", $_POST['OMONTOAREVOCAR']);
						}
						//if ($_POST['OMONTO'] == "") { $_POST['OMONTO'] = $valor_g->monto; }
						//$_POST['OMONTOACTA'] = $valor_g->monto;
					}
					?>
					<td align="center">&nbsp;Monto => <input name="OMONTO" id="OMONTO" onChange="comprobar_monto()" type="text" value="<?php echo $_POST['OMONTO'] ?>" <?php if (isset($_POST["OREVOCADO"])) echo "readonly"; ?>></td>
					<td align="center"><input type="submit" class="boton" name="CMDAGREGAR" value="Agregar"></td>
					<input name="OMONTOACTA" id="OMONTOACTA" type="hidden" value="<?php echo $_POST['OMONTOACTA'] ?>">
					<input name="OMONTOPAGADO" id="OMONTOPAGADO" type="hidden" value="<?php echo $_POST['OMONTOPAGADO'] ?>">
					<input name="OMONTOCONFIRMADO" id="OMONTOCONFIRMADO" type="hidden" value="<?php echo $_POST['OMONTOCONFIRMADO'] ?>">
					<input name="OMONTOREVOCADO" id="OMONTOREVOCADO" type="hidden" value="<?php echo $_POST['OMONTOREVOCADO'] ?>">
					<input name="OIDDETALLEACTA" id="OIDDETALLEACTA" type="hidden" value="<?php echo $_POST['OPERIODO'] ?>">
					<input name="OMONTOACONFIRMAR" id="OMONTOACONFIRMAR" type="hidden" value="<?php echo $_POST['OMONTOACONFIRMAR'] ?>">
					<input name="OMONTOAREVOCAR" id="OMONTOAREVOCAR" type="hidden" value="<?php echo $_POST['OMONTOAREVOCAR'] ?>">
					<input name="OMONTOPAGADOSUMARIO" id="OMONTOPAGADOSUMARIO" type="hidden" value="<?php echo $_POST['OMONTOPAGADOSUMARIO'] ?>">

				</tr>
				<tr id="used">
					<td bgcolor="#666666" align="center" valign="top" class="Estilo7">Motivos Revocatoria:</td>
					<td colspan="4"><textarea name="OOBSERVACION" id="OOBSERVACION" cols="98%" rows="5"><?php echo $_POST['OOBSERVACION'] ?></textarea></td>
				</tr>
			</table>


			<?php
			//MOSTRAMOS LOS RESULTADOS DEL EXPEDIENTES
			$sql_exp = "SELECT sumario_resultado.id_resultado, sumario_resultado.id_expediente, sumario_resultado.id_detalle_acta, sumario_resultado.impuesto_por_pagar, sumario_resultado.monto_confirmado, sumario_resultado.monto_revocado, sumario_resultado.usuario, sumario_resultado.fecha_proceso, CONCAT_WS(' al ',date_format(fis_actas_detalle.periodo_desde, '%d-%m-%Y'), date_format(fis_actas_detalle.periodo_hasta, '%d-%m-%Y')) as periodo FROM sumario_resultado INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_detalle = sumario_resultado.id_detalle_acta WHERE sumario_resultado.id_expediente =" . $_POST['OID'];
			$resultado = mysql_query($sql_exp);
			$registros = mysql_num_rows($resultado);
			if ($registros > 0) { ?>
				<p></p>
				<table width="60%" border=1 align=center>
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
						<td bgcolor="#CCCCCC" align="center"><strong>Acci&oacute;n</strong></td>
					</tr>
					<?php
					$i = 1;
					while ($valor = mysql_fetch_object($resultado)) { ?>
						<tr>
							<td align="center"><?php echo $i ?></td>
							<td align="center"><?php echo $valor->periodo ?></td>
							<td align="center"><?php echo formato_moneda($valor->impuesto_por_pagar) ?></td>
							<td align="center"><?php echo formato_moneda($valor->monto_confirmado) ?></td>
							<?php
							$sql_motivo = "SELECT id_motivo, causas FROM sumario_motivo_revocatoria WHERE id_expediente=" . $_POST['OID'] . " AND id_detalle=" . $valor->id_detalle_acta;
							$result = mysql_query($sql_motivo);
							$reg_motivo = mysql_fetch_object($result);
							$_POST['OCAUSAS'] = 'CAUSAS DE REVOCATORIA: ' . $reg_motivo->causas;
							$_POST['OIDCAUSAS'] = $reg_motivo->id_motivo;
							?>
							<td align="center"><?php echo formato_moneda($valor->monto_revocado) . ' ';
												$_POST['OMONTOREVOCADO'] = $valor->monto_revocado;
												if ($valor->monto_revocado > 0) { ?><a href="javascript:func_motivos('<?php echo $_POST['OCAUSAS'] ?>')">causas</a><?php } ?></td>
							<td align="center"><input type="submit" class="boton" name="<?php echo $valor->id_resultado ?>" value="Eliminar"></td>
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
			<table border=0 align=center>
				<tr>
					<td>
						<p>
							<input type="submit" class="boton" name="CMDCONCLUIR" value="Concluir">
						</p>
						<p>&nbsp;</p>
						</div>
					</td>
				</tr>
			</table>
			<textarea name="OCAUSAS" id="OCAUSAS" style="display:none;"><?php echo $_POST['OCAUSAS'] ?></textarea>
			<input name="OIDCAUSAS" id="OIDCAUSAS" type="hidden" value="<?php echo $_POST['OIDCAUSAS'] ?>">

			<a name="vista"></a>
			<p>&nbsp;</p>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
	<script type="text/javascript">
		function cambiar_estado(value) {
			if (value == 'Confirmado') {
				//alert('cONFIRMAR');
				document.getElementById('OCONFIRMADO').checked = true;
				document.getElementById('OREVOCADO').checked = false;
				document.getElementById('OMONTO').readOnly = false;
				document.getElementById('OMONTOCONFIRMADO').value = document.getElementById('OMONTOACONFIRMAR').value;
				document.getElementById('OMONTOREVOCADO').value = 0;
				document.getElementById('OOBSERVACION').disabled = true;
			} else {
				//alert('REVOCAR');
				document.getElementById('OCONFIRMADO').checked = false;
				document.getElementById('OREVOCADO').checked = true;
				document.getElementById('OMONTO').readOnly = true;
				document.getElementById('OMONTO').value = document.getElementById('OMONTOAREVOCAR').value;
				document.getElementById('OMONTOACTA').value = document.getElementById('OMONTOAREVOCAR').value;
				document.getElementById('OMONTOREVOCADO').value = document.getElementById('OMONTOAREVOCAR').value;
				document.getElementById('OMONTOCONFIRMADO').value = document.getElementById('OMONTOACONFIRMAR').value - document.getElementById('OMONTOAREVOCAR').value;
				document.getElementById('OOBSERVACION').disabled = false;
			}
		}

		function comprobar_monto() {
			var revocado = document.getElementById('OMONTOAREVOCAR').value;
			//alert(revocado);
			if (revocado > 0) {
				document.getElementById('OMONTOREVOCADO').value = revocado;
				document.getElementById('OMONTOCONFIRMADO').value = document.getElementById('OMONTOACONFIRMAR').value - revocado;
				document.getElementById('OOBSERVACION').disabled = false;
			} else {
				document.getElementById('OMONTOREVOCADO').value = revocado;
				document.getElementById('OMONTOCONFIRMADO').value = document.getElementById('OMONTOACONFIRMAR').value - revocado;
				document.getElementById('OOBSERVACION').disabled = true;
			}
		}

		function func_motivos(id_motivo) {
			alert(id_motivo);
		}
	</script>
</body>

</html>