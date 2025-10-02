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
		if ($_POST['OGESTION'] > 0) {
			if (($_POST['OGESTION'] == 25 or $_POST['OGESTION'] == 23) and $_POST['OOBSERVACION'] == "") {
				echo "<script type=\"text/javascript\">alert('Se requiere que indique la explicaci�n de la gesti�n...!');</script>";
			} else {
				//VERIFICAMOS SI EXISTE UN GASTION SIMILAR PARA LA MISMA FECHA
				$consulta_e = "SELECT id_expediente FROM sumario_gestion_ponente WHERE id_expediente=" . $_POST['OID'] . " AND id_observacion=" . $_POST['OGESTION'] . " AND fecha_observacion=date(now())";
				$tabla_e = mysql_query($consulta_e);
				$existe = mysql_num_rows($tabla_e);
				if ($existe < 1) {
					//----------------------
					$consultax = "INSERT INTO sumario_gestion_ponente (id_expediente, id_observacion, otras, fecha_observacion, usuario) VALUES (" . $_POST['OID'] . "," . $_POST['OGESTION'] . ",'" . strtoupper(trim($_POST['OOBSERVACION'])) . "',date(now())," . $_SESSION['CEDULA_USUARIO'] . ")";
					$tablax = mysql_query($consultax);
				} else {
					echo "<script type=\"text/javascript\">alert('Ya existe esta gesti�n para dicho expediente...!');</script>";
				}
			}
		} else {
			echo "<script type=\"text/javascript\">alert('No ha Seleccionado la Gesti�n!');</script>";
		}
	}

	//PARA ELIMINAR LA GESTION
	$sql_gestion = "SELECT sumario_gestion_ponente.id_gestion, sumario_gestion_ponente.id_observacion, a_codigos_observacion.observacion, sumario_gestion_ponente.otras, date_format(sumario_gestion_ponente.fecha_observacion, '%d-%m-%Y') as fecha_observacion, sumario_gestion_ponente.usuario FROM a_codigos_observacion INNER JOIN sumario_gestion_ponente ON sumario_gestion_ponente.id_observacion = a_codigos_observacion.id_observacion WHERE sumario_gestion_ponente.id_expediente=" . $_POST['OID'];
	$tabla_g = mysql_query($sql_gestion);
	while ($gestion =  mysql_fetch_object($tabla_g)) {
		if ($_POST[$gestion->id_gestion] == 'Eliminar') {
			// ------ ELIMINAR LA PLANILLA NUEVA
			$consultax = "DELETE FROM sumario_gestion_ponente WHERE id_gestion=" . $gestion->id_gestion . ";";
			$tablax = mysql_query($consultax);
			// ------
		}
	}


	?>

	<title>Expediente x Notificar</title>
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
			<p></p>
		<?php
		} ?>

		<table border=0 align=center>
			<tr>
				<td colspan="6">
					<div align="center">
						<p>&nbsp;</p>
						<p><strong>Seleccione aqu&iacute; la GESTION realizada </strong></p>
						<p>
							<select name="OGESTION" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT id_observacion, observacion FROM a_codigos_observacion WHERE origen_observacion = 'S'";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OGESTION'] == $registro_x->id_observacion) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->id_observacion;
									echo '">';
									echo $registro_x->observacion;
									echo '</option>';
								}
								?>
							</select>
						</p>
						<?php
						if ($_POST['OGESTION'] == 25 or $_POST['OGESTION'] == 23) {
							echo '<table width="60%" border=0 align=center>';
							echo '<tr>';
							echo '<td align="center" valign="top">Explique:</td>';
							echo '<td><textarea name="OOBSERVACION" cols="40" rows="5"></textarea></td>';
							echo '</tr>';
							echo '</table>';
						}
						?>
						<p>
							<input type="submit" class="boton" name="CMDAGREGAR" value="Agregar">
						</p>
						<p>&nbsp;</p>
					</div>
				</td>
			</tr>
		</table>
		<?php
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
					<td bgcolor="#CCCCCC" align="center"><strong>Fecha de la Gesti�n:</strong></td>
					<td bgcolor="#CCCCCC" align="center"><strong>Acci�n</strong></td>
				</tr>
			<?php
			$i = 1;
			while ($reg = mysql_fetch_object($result)) {
				echo '<tr><td align="center">' . $i . '</td>';
				if ($reg->id_observacion <> 24) {
					$descripcion = strtoupper($reg->otras);
				} else {
					$descripcion = strtoupper($reg->observacion);
				}
				echo '<td >' . $descripcion . '</td>';
				echo '<td align="center">' . $reg->fecha_observacion . '</td>';
				echo '<td align="center"><input name="' . $reg->id_gestion . '" type="submit" value="Eliminar" ></td>';
				echo '</tr>';
				$i += 1;
			}
		}

			?>
			</table>


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