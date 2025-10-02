<html>

<head>
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}

	$sql_id = "SELECT id_expediente as id, num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha FROM expedientes_sumario WHERE origen_liquidacion = " . $_GET['origen'] . " AND sector = " . $_GET['sector'] . " AND anno_expediente_fisc = " . $_GET['anno'] . " AND num_expediente_fisc = " . $_GET['num'];
	$result_id = mysql_query($sql_id);
	$valor_id = mysql_fetch_object($result_id);
	$_POST['OID'] = $valor_id->id;

	if ($_POST['CMDAGREGAR'] == 'Agregar') {
		if ($_POST['OPLANILLA'] <> "" and $_POST['OFECHAPAGO'] <> "" and $_POST['OMONTOPAGO'] > 0) {
			if ($_POST['OMONTOPAGO'] <= $_POST['OMONTOAPAGAR']) {
				//VERIFICAMOS SI EXISTE EL PAGO
				$consulta_e = "SELECT planilla FROM sumario_pagos WHERE planilla = '" . $_POST['OPLANILLA'] . "' AND id_expediente = " . $_POST['OID'];
				$tabla_e = mysql_query($consulta_e);
				$existe = mysql_num_rows($tabla_e);
				if ($existe < 1) {
					//----------------------
					$consultax = "INSERT INTO sumario_pagos (id_expediente, id_detalle_acta, planilla, monto_pagado, fecha_pago, usuario) VALUES (" . $_POST['OID'] . "," . $_POST['OPERIODO'] . ",'" . $_POST['OPLANILLA'] . "'," . $_POST['OMONTOPAGO'] . ",'" . voltea_fecha($_POST['OFECHAPAGO']) . "'," . $_SESSION['CEDULA_USUARIO'] . ")";
					$tablax = mysql_query($consultax);
				} else {
					echo "<script type=\"text/javascript\">alert('Ya existe este pago para dicho expediente...!');</script>";
				}
			} else {
				echo "<script type=\"text/javascript\">alert('El monto pagado no puede ser mayor el impuesto omitido por pagar de Bs." . number_format($_POST['OMONTOAPAGAR'], 2, ',', '.') . "');</script>";
			}
		} else {
			echo "<script type=\"text/javascript\">alert('Existen campos requeridos vacios...!');</script>";
		}
	}

	//PARA ELIMINAR LA GESTION
	$sql_gestion = "SELECT id_pago FROM sumario_pagos WHERE id_expediente = " . $_POST['OID'];
	$tabla_g = mysql_query($sql_gestion);
	while ($gestion =  mysql_fetch_object($tabla_g)) {
		if ($_POST[$gestion->id_pago] == 'Eliminar') {
			// ------ ELIMINAR LA PLANILLA NUEVA
			$consultax = "DELETE FROM sumario_pagos WHERE id_pago=" . $gestion->id_pago . ";";
			echo $consultax;
			$tablax = mysql_query($consultax);
			// ------
		}
	}


	?>

	<title>Expediente x Notificar</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
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

		$concluir = 'No';
		include "0_detalles_expediete.php";

		?>


		<?php

		//VERIFICAMOS SI TIENE ESCRITO DE DESCARGO
		$sql_escrito = "SELECT id_expediente as id, num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha FROM expedientes_sumario WHERE origen_liquidacion = " . $_GET['origen'] . " AND sector = " . $_GET['sector'] . " AND anno_expediente_fisc = " . $_GET['anno'] . " AND num_expediente_fisc = " . $_GET['num'] . " AND num_escrito_descargo<>''";
		$result = mysql_query($sql_escrito);
		$existe = mysql_num_rows($result);
		if ($existe > 0) {
			$valor = mysql_fetch_object($result);
		?>
			<table width="75%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="27" colspan="4" align="center">
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
		<?php
		}

		global $monto_revocado;

		$sql_gestiones = "SELECT sumario_gestion_ponente.id_gestion, sumario_gestion_ponente.id_observacion, a_codigos_observacion.observacion, sumario_gestion_ponente.otras, date_format(sumario_gestion_ponente.fecha_observacion, '%d-%m-%Y') as fecha_observacion, sumario_gestion_ponente.usuario FROM a_codigos_observacion INNER JOIN sumario_gestion_ponente ON sumario_gestion_ponente.id_observacion = a_codigos_observacion.id_observacion WHERE sumario_gestion_ponente.id_expediente=" . $_POST['OID'] . " ORDER BY sumario_gestion_ponente.id_gestion DESC;";
		$result = mysql_query($sql_gestiones);
		$gestiones = mysql_num_rows($result);

		if ($gestiones > 0) {
		?>
			<table width="75%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="27" colspan="4" align="center">
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

			<table width="60%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="6" align="center">
						<p class="Estilo7"><u>Agregar Pagos al Expediente</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" align="center"><strong>Tributo</strong></div>
					</td>
					<td bgcolor="#CCCCCC" align="center"><strong>Periodo</strong></div>
					</td>
					<td bgcolor="#CCCCCC" align="center"><strong>Planilla N&deg;</strong></div>
					</td>
					<td bgcolor="#CCCCCC" align="center"><strong>Fecha del Pago</strong></td>
					<td bgcolor="#CCCCCC" align="center"><strong>Monto Pagado</strong></td>
					<td bgcolor="#CCCCCC" align="center"><strong>Acci&oacute;n</strong></td>
				</tr>
				<tr>
					<td align="center">
						<?php
						$sql = "SELECT a_tributos.id_tributo, a_tributos.nombre FROM expedientes_sumario INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_sumario.sector AND fis_actas.anno_prov = expedientes_sumario.anno_expediente_fisc AND fis_actas.num_prov = expedientes_sumario.num_expediente_fisc INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE expedientes_sumario.id_expediente = " . $_POST['OID'] . " GROUP BY fis_actas_detalle.tributo";
						$tabla = mysql_query($sql);
						?>
						<select name="OTRIBUTO" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							while ($valor = mysql_fetch_object($tabla)) {
								echo '<option ';
								if ($_POST['OTRIBUTO'] == $valor->id_tributo) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $valor->id_tributo . '>' . $valor->nombre . '</option>';
							} ?>
						</select>
					</td>

					<td align="center">
						<select name="OPERIODO" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							if ($_POST['OTRIBUTO'] > 0) {
								//OBTENEMOS LOS MONTOS PAGADOS EN SUMARIO
								$consulta_l = "SELECT sum(sumario_pagos.monto_pagado) as monto FROM sumario_pagos WHERE sumario_pagos.id_expediente = " . $_POST['OID'];
								echo $consulta_l;
								$tabla_l = mysql_query($consulta_l);
								$valor = mysql_fetch_object($tabla_l);
								$monto = $valor->monto;

								$monto_a_pagar = 0;
								$consulta_x = "SELECT fis_actas_detalle.id_detalle, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta, (fis_actas_detalle.impuesto_omitido - fis_actas_detalle.monto_pagado) as monto_a_pagar FROM expedientes_sumario INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_sumario.sector AND fis_actas.anno_prov = expedientes_sumario.anno_expediente_fisc AND fis_actas.num_prov = expedientes_sumario.num_expediente_fisc INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE expedientes_sumario.id_expediente = " . $_POST['OID'] . " AND a_tributos.id_tributo = " . $_POST['OTRIBUTO'];
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OPERIODO'] == $registro_x['id_detalle']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['id_detalle'] . '>' . voltea_fecha($registro_x['periodo_desde']) . ' al ' . voltea_fecha($registro_x['periodo_hasta']) . '</option>';
								}
							}
							?>
						</select>
					</td>
					<?php
					if ($_POST['OPERIODO'] > 0) {
						$sqlmonto = "SELECT fis_actas_detalle.id_detalle, (fis_actas_detalle.impuesto_omitido - fis_actas_detalle.monto_pagado) as monto_a_pagar FROM expedientes_sumario INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_sumario.sector AND fis_actas.anno_prov = expedientes_sumario.anno_expediente_fisc AND fis_actas.num_prov = expedientes_sumario.num_expediente_fisc INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE expedientes_sumario.id_expediente = " . $_POST['OID'] . " AND a_tributos.id_tributo = " . $_POST['OTRIBUTO'] . " AND fis_actas_detalle.id_detalle=" . $_POST['OPERIODO'];
						//echo $sqlmonto;
						$tabla_g = mysql_query($sqlmonto);
						$valor_g = mysql_fetch_object($tabla_g);
						//echo '..../////....'.$valor_g->monto_a_pagar;
						//$_POST['OMONTOAPAGAR'] = $valor_g->monto_a_pagar;

						//****BUSCAMOS SI EXISTEN PAGOS EN SUMARIO PARA ESTE DETALLE*******/
						$sqlPagoSumario = "SELECT SUM(monto_pagado) AS pagado_sumario FROM sumario_pagos WHERE id_detalle_acta=" . $_POST['OPERIODO'];
						$tabla_s = mysql_query($sqlPagoSumario);
						$valor_s = mysql_fetch_object($tabla_s);
						$_POST['OMONTOAPAGAR'] = $valor_g->monto_a_pagar - $valor_s->pagado_sumario;
					}
					?>
					<td align="center"><input name="OPLANILLA" type="text" size="15" maxlength="15" value="<?php echo $_POST['OPLANILLA'] ?>"></td>
					<td align="center"><input name="OFECHAPAGO" type="text" size="15" maxlength="10" value="<?php echo $_POST['OFECHAPAGO'] ?>" onclick='javascript:scwShow(this,event);'></td>
					<td align="center"><input name="OMONTOPAGO" type="text" size="15" maxlength="15" value="<?php echo $_POST['OMONTOPAGO'] ?>"><input name="OMONTOAPAGAR" type="hidden" size="15" maxlength="15" value="<?php echo $_POST['OMONTOAPAGAR'] ?>"></td>
					<td align="center"><input name="CMDAGREGAR" type="submit" value="Agregar"></td>
				</tr>
			</table>


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
						<td bgcolor="#FF0000" height="40" colspan="6" align="center">
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
						<td bgcolor="#CCCCCC" align="center"><strong>Acci&oacute;n</strong></td>
					</tr>
					<?php
					while ($reg = mysql_fetch_object($tabla)) { ?>
						<tr>
							<td align="center"><?php echo $reg->nombre ?></td>
							<td align="center"><?php echo voltea_fecha($reg->periodo_desde) . ' al ' . voltea_fecha($reg->periodo_hasta) ?></td>
							<td align="center"><?php echo $reg->planilla ?></td>
							<td align="center"><?php echo voltea_fecha($reg->fecha_pago) ?></td>
							<td align="center"><?php echo formato_moneda($reg->monto_pagado) ?></td>
							<td align="center"><input name="<?php echo $reg->id_pago ?>" type="submit" value="Eliminar"></td>
						</tr>
					<?php
					}
					?>
				</table>
			<?php
			}
			?>
			<a name="vista"></a>
			<p>&nbsp;</p>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>