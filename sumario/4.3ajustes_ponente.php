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
		if ($_POST['OPERIODO'] > 0 and $_POST['OOBSERVACION'] <> "" and $_POST['OMONTO']) {
			//OBTENEMOS EL PERIODO DE INICIO Y FIN
			$sql_periodo = "SELECT periodo_desde, periodo_hasta FROM fis_actas_detalle WHERE id_detalle=" . $_POST['OPERIODO'];
			$tabla_periodo = mysql_query($sql_periodo);
			$valor_p = mysql_fetch_object($tabla_periodo);

			//VERIFICAMOS SI EXISTE DICHO AJUSTE PARA DICHO PERIODO
			$fechainicio = substr("abcdef", -3, 1);
			$fechafin = substr("abcdef", -3, 1);
			$consulta_e = "SELECT id_ajuste FROM sumario_ajustes WHERE id_expediente = " . $_POST['OID'] . " AND periodoinicio = '" . $valor_p->periodo_desde . "' AND periodohasta = '" . $valor_p->periodo_hasta . "' AND id_tipo_ajuste = " . $_POST['OTIPOAJUSTE'];
			$tabla_e = mysql_query($consulta_e);
			$existe = mysql_num_rows($tabla_e);
			if ($existe < 1) {
				if ($_POST['OTIPOAJUSTE'] == 1) {
					$errormaterial_bi = $_POST['OMONTOBI'];
					$errormaterial_impto = $_POST['OMONTO'];
					$motivo_errormaterial = $_POST['OOBSERVACION'];
					$errorcalculo_bi = 0;
					$errorcalculo_impto = 0;
					$motivo_errorcalculo = "";
				} else {
					$errormaterial_bi = $_POST['OMONTOBI'];
					$errormaterial_impto = $_POST['OMONTO'];
					$motivo_errormaterial = "";
					$errorcalculo_bi = 0;
					$errorcalculo_impto = 0;
					$motivo_errorcalculo = $_POST['OOBSERVACION'];;
				}
				//----------------------
				$consultax = "INSERT INTO sumario_ajustes (id_expediente, id_tipo_ajuste, id_detalle_acta, periodoinicio, periodohasta, monto_bi_error_material, monto_impto_error_material, monto_bi_error_calculo, monto_impto_error_calculo, motivo_error_material, motivo_error_calculo, usuario) VALUES (" . $_POST['OID'] . "," . $_POST['OTIPOAJUSTE'] . "," . $_POST['OPERIODO'] . ",'" . $valor_p->periodo_desde . "','" . $valor_p->periodo_hasta . "'," . $errormaterial_bi . "," . $errormaterial_impto . "," . $errorcalculo_bi . "," . $errorcalculo_impto . ",'" . $motivo_errormaterial . "','" . $motivo_errorcalculo . "'," . $_SESSION['CEDULA_USUARIO'] . ")";
				$tablax = mysql_query($consultax);
			} else {
				echo "<script type=\"text/javascript\">alert('Ya existe este ajuste en el expediente...!');</script>";
			}
		} else {
			echo "<script type=\"text/javascript\">alert('No ha Seleccionado el ajuste!');</script>";
		}
	}

	//PARA ELIMINAR EL AJUSTE
	$sql_gestion = "SELECT id_ajuste FROM sumario_ajustes WHERE id_expediente = " . $_POST['OID'];
	$tabla_g = mysql_query($sql_gestion);
	while ($ajustes =  mysql_fetch_object($tabla_g)) {
		if ($_POST[$ajustes->id_ajuste] == 'Eliminar') {
			// ------ ELIMINAR LA PLANILLA NUEVA
			$consultax = "DELETE FROM sumario_ajustes WHERE id_ajuste=" . $ajustes->id_ajuste . ";";
			$tablax = mysql_query($consultax);
			// ------
		}
	}


	?>

	<title>Ajustes Sumario</title>
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
			<table width="60%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="4" align="center">
						<p class="Estilo7"><u>Escrito de Descargo</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo5"><strong><span class="Estilo13">N&uacute;mero de Recepci�n Correspondencia:</span></a></strong></div>
					</td>
					<td align="center"><?php echo $valor->num_escrito_descargo ?></td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Fecha de Recepci�n:</strong></div>
					</td>
					<td align="center"><?php echo $valor->fecha;
										$_POST['OID'] = $valor->id ?><input name="OID" type="hidden" value="<?php echo $_POST['OID'] ?>"></td>
				</tr>
			</table>
			<p>&nbsp;</p>
		<?php
		} ?>

		<table width="60%" border=1 align=center>
			<tr>
				<td bgcolor="#FF0000" height="40" colspan="6" align="center"><strong class="Estilo7"><u>Ajuste al Acta de Reparo por Error Material o de C�lculo</u></strong></td>
			</tr>
			<tr>
				<th align="center" bgcolor="#CCCCCC">Tipo Ajuste</th>
				<th align="center" bgcolor="#CCCCCC">Periodo</th>
				<th align="center" bgcolor="#CCCCCC">Motivo del Ajsute</th>
				<th align="center" bgcolor="#CCCCCC">Ajuste BI Bs.</th>
				<th align="center" bgcolor="#CCCCCC">Ajuste IMPUESTO Bs.</th>
				<th align="center" bgcolor="#CCCCCC">Acci�n</th>
			</tr>
			<tr>
				<td><select name="OTIPOAJUSTE" onChange="this.form.submit()">
						<?php
						$sql = "SELECT descripcion, id_ajuste FROM sumario_tipo_ajuste ORDER BY descripcion ASC";
						$result = mysql_query($sql);
						while ($datos = mysql_fetch_object($result)) {
						?>
							<option value="<?php echo $datos->id_ajuste ?>" <?php if ($_POST['OTIPOAJUSTE'] == $datos->id_ajuste) {
																				echo 'selected="selected" ';
																			} ?>><?php echo $datos->descripcion ?></option>
						<?php
						} ?>
					</select></td>
				<td><select name="OPERIODO" size="1" onChange="this.form.submit()">
						<option value="0">Seleccione</option>
						<?php
						//OBTENEMOS LOS MONTOS PAGADOS EN SUMARIO
						$consulta_l = "SELECT sum(sumario_pagos.monto_pagado) as monto FROM sumario_pagos WHERE sumario_pagos.id_expediente = " . $_POST['OID'];
						echo $consulta_l;
						$tabla_l = mysql_query($consulta_l);
						$valor = mysql_fetch_object($tabla_l);
						$monto = $valor->monto;

						$monto_a_pagar = 0;
						$consulta_x = "SELECT fis_actas_detalle.id_detalle, fis_actas_detalle.periodo_desde, fis_actas_detalle.periodo_hasta, (fis_actas_detalle.impuesto_omitido - fis_actas_detalle.impuesto_pagado) as monto_a_pagar FROM expedientes_sumario INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_sumario.sector AND fis_actas.anno_prov = expedientes_sumario.anno_expediente_fisc AND fis_actas.num_prov = expedientes_sumario.num_expediente_fisc INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE expedientes_sumario.id_expediente = " . $_POST['OID'];
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
					</select></td>
				<td><textarea name="OOBSERVACION" id="OOBSERVACION" cols="30%" rows="5"><?php echo $_POST['OOBSERVACION'] ?></textarea></td>
				<td><input name="OMONTOBI" id="OMONTOBI" onChange="comprobar_monto()" type="text" value="<?php echo $_POST['OMONTOBI'] ?>"></td>
				<td><input name="OMONTO" id="OMONTO" onChange="comprobar_monto()" type="text" value="<?php echo $_POST['OMONTO'] ?>"></td>
				<td><input type="submit" class="boton" name="CMDAGREGAR" value="Agregar"></td>
			</tr>
		</table>

		<?php

		//PARA MOSTRAR LOS AJUSTES CARGADOS AL EXPEDIENTE
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
					<td bgcolor="#CCCCCC" align="center"><strong>Acci&oacute;n</strong></td>
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
						<td align="center"><input name="<?php echo $reg->id_ajuste ?>" type="submit" value="Eliminar"></td>
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
</body>

</html>