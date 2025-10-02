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
	$acceso = 48;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	if ($_POST['CMDGUARDAR'] == 'Guardar') {
		if ($_POST['OFECHA'] <> '') {
			// PARA GUARDAR LA INFORMACI�N
			if ($_POST['OOBSERVACION'] <> '') {
				//----- VALIDAR LA FECHA CON LA FECHA DE ASIGNACION
				$consultaX = "SELECT id_liquidacion FROM liquidacion WHERE fecha_asignacion_notificador>'" . voltea_fecha($_POST['OFECHA']) . "' AND status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND origen_liquidacion=" . $_GET['origen'] . " AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . ";";
				$tablaX = mysql_query($consultaX);
				$registroX = mysql_fetch_object($tablaX);
				//------------------
				if ($registroX->id_liquidacion > 100000000) {
					echo "<script type=\"text/javascript\">alert('La Fecha no puede ser menor a la fecha de asignaci�n!!!');</script>";
				} else {
					//----------
					list($codigo, $acepto) = explode('-', $_POST['OOBSERVACION']);
					//----------
					if ($acepto == 'SI') {
						//-------- POR SI LA FECHA DE ASIGNACION ES MENOR LE COLOCAMOS LA DE NOTIFICACION
						if ($registroX->id_liquidacion > 0) {
							//----- ACTUALIZAR LA PLANILLA EN LIQUIDACION
							$consulta = "UPDATE liquidacion SET fecha_asignacion_notificador='" . voltea_fecha($_POST['OFECHA']) . "' WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND origen_liquidacion=" . $_GET['origen'] . " AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . ";";
							$tabla = mysql_query($consulta);
						}
						//----- ACTUALIZAR LA PLANILLA EN LIQUIDACION
						$consulta = "UPDATE liquidacion SET status = 29, fecha_not='" . voltea_fecha($_POST['OFECHA']) . "', fecha_ven='" . dias_feriados(voltea_fecha($_POST['OFECHA']), 25) . "', usuario = " . $_SESSION['CEDULA_USUARIO'] . " WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND origen_liquidacion=" . $_GET['origen'] . " AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . ";";
						$tabla = mysql_query($consulta);
					} else {
						//----- ACTUALIZAR LA PLANILLA EN LIQUIDACION
						$consulta = "UPDATE liquidacion SET usuario = " . $_SESSION['CEDULA_USUARIO'] . " WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . ";";
						$tabla = mysql_query($consulta);
					}
					//----- ACTUALIZAR LA PLANILLA EN GESTION
					$consulta = "INSERT INTO  liquidacion_gestion_notificacion 
				(sector , origen_liquidacion , anno_expediente , num_expediente , id_observacion , fecha_observacion , usuario ) 
				VALUES ( " . $_GET['sector'] . ",  " . $_GET['origen'] . ", " . $_GET['anno'] . ",  " . $_GET['num'] . ",  " . $codigo . ", '" . voltea_fecha($_POST['OFECHA']) . "', " . $_SESSION['CEDULA_USUARIO'] . ");";
					$tabla = mysql_query($consulta);
					// --------MENSAJE
					echo "<script type=\"text/javascript\">alert('Expediente(s) Actualizado(s) Exitosamente!!!');</script>";
					// PARA CERRAR LA PAGINA
					echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
					exit();
				}
			} else {
				echo '<script type="text/javascript">alert("No seleccionado la Observaci�n");</script>';
			}
		} else {
			echo '<script type="text/javascript">alert("No ha Ingresado la Fecha");</script>';
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
	<form name="form1" method="post" action="">
		<p>&nbsp;</p>
		<table width="50%" border=1 align=center>
			<?php
			$consulta = "SELECT contribuyente, rif FROM contribuyentes WHERE (((rif)='" . $_GET['rif'] . "'));";
			$tabla = mysql_query($consulta);
			$registro = mysql_fetch_object($tabla)
			?>
			<td bgcolor="#FF0000" height="27" colspan="6" align="center">
				<p class="Estilo7"><u>Contribuyente</u></p>
			</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC" height=27>
					<div align="center" class="Estilo14">Rif</div>
				</td>
				<td bgcolor="#CCCCCC" colspan="3">
					<div align="center" class="Estilo14">Contribuyente</div>
				</td>
			</tr>
			<tr>
				<td width="19%" height=27>
					<div align="center" class="Estilo15"><?php echo $registro->rif; ?></div>
				</td>
				<td colspan="3">
					<div align="left" class="Estilo15"><?php echo $registro->contribuyente; ?></div>
				</td>
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="6" align="center">
					<p class="Estilo7"><u>Planillas</u></p>
				</td>
			</tr>
			<tr>
				<td width="17%" colspan="2" bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">Liquidacion</span></a></strong></div>
				</td>
				<td bgcolor="#CCCCCC" width="19%">
					<div align="center" class="Estilo13"><strong>Monto</strong></div>
				</td>
				<td width="18%" bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Serial</strong></div>
				</td>
			</tr>
			<?php
			$consulta = "SELECT planilla_notificacion, monto_bs, concurrencia, especial, liquidacion, id_liquidacion FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . " AND origen_liquidacion=" . $_GET['origen'] . " ORDER BY liquidacion;";
			$tabla = mysql_query($consulta);

			while ($registro = mysql_fetch_object($tabla)) {
			?><tr>
					<td colspan="2">
						<div align="center" class="Estilo15">
							<?php echo $registro->liquidacion; ?>
						</div>
					</td>
					<td height=27>
						<div align="center" class="Estilo15">
							<?php echo formato_moneda($registro->monto_bs / $registro->concurrencia * $registro->especial); ?>
						</div>
					</td>
					<td>
						<div align="center" class="Estilo15"><label><?php echo $registro->planilla_notificacion; ?></label>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
		<table border=0 align=center>
			<tr>
				<td colspan="6">
					<div align="center">
						<p>&nbsp;</p>
						<p><strong>Resultados</strong></p>
						<p>
							<select name="OOBSERVACION" size="1">
								<option value="">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT id_observacion, observacion, acepto FROM a_codigos_observacion WHERE origen_observacion='N';";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OOBSERVACION'] == $registro_x->id_observacion . '-' . $registro_x->acepto) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->id_observacion . '-' . $registro_x->acepto;
									echo '">';
									echo $registro_x->observacion;
									echo '</option>';
								}
								?>
							</select>
							<span class="Estilo7">
								<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>" />
							</span>
						</p>
						<p>
							<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
						</p>
						<p>&nbsp;</p>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>