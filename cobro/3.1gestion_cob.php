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
	$acceso = 137;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	if ($_POST['CMDGUARDAR'] == 'Guardar') {
		$consulta_1 = "SELECT id_liquidacion, serie, liquidacion FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . " AND origen_liquidacion=" . $_GET['origen'] . " ORDER BY liquidacion;";
		$tabla_1 = mysql_query($consulta_1);
		//-----------------------------	
		while ($registro_1 = mysql_fetch_object($tabla_1)) {
			if ($_POST['OR' . $registro_1->id_liquidacion] <> '') {
				// PARA GUARDAR LA INFORMACI�N
				if ($_POST['OF' . $registro_1->id_liquidacion] <> '') {
					//----------
					list($codigo, $acepto) = explode('-', $_POST['OR' . $registro_1->id_liquidacion]);
					//----------
					if ($acepto == 'SI') {
						if ($_POST['OBANCO' . $registro_1->id_liquidacion] > 0) {
							//----- ACTUALIZAR LA PLANILLA EN LIQUIDACION
							$consulta = "UPDATE liquidacion SET agencia_pag=" . $_POST['OBANCO' . $registro_1->id_liquidacion] . ", fecha_pag='" . voltea_fecha($_POST['OF' . $registro_1->id_liquidacion]) . "', status = 100, usuario = " . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion=" . $registro_1->id_liquidacion . ";";
							$tabla = mysql_query($consulta);
						} else {
							// --------MENSAJE
							echo "<script type=\"text/javascript\">alert('Debe Seleccionar la Agencia de Pago!!!');</script>";
						}
					} else {
						//----- POR SI VA PARA FRACCIONAMIENTO
						if ($codigo == 16) {
							if ($registro_1->serie <> 38) {
								//----- ACTUALIZAR LA PLANILLA EN LIQUIDACION
								$consulta = "UPDATE liquidacion SET status = 50, usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion=" . $registro_1->id_liquidacion . ";";
								$tabla = mysql_query($consulta);
							} else {
								// --------MENSAJE
								echo "<script type=\"text/javascript\">alert('La Planilla " . $registro_1->liquidacion . " de Inter�s no se puede Fraccionar!!!');</script>";
							}
						} else {
							//----- ACTUALIZAR LA PLANILLA EN LIQUIDACION
							$consulta = "UPDATE liquidacion SET usuario = " . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion=" . $registro_1->id_liquidacion . ";";
							$tabla = mysql_query($consulta);
						}
					}
					//----- ACTUALIZAR LA PLANILLA EN GESTION
					$consulta = "INSERT INTO  liquidacion_gestion_cobro 
				(id_liq, sector , origen_liquidacion , anno_expediente , num_expediente , id_observacion , fecha_observacion , usuario ) 
				VALUES (" . $registro_1->id_liquidacion . ", " . $_GET['sector'] . ",  " . $_GET['origen'] . ", " . $_GET['anno'] . ",  " . $_GET['num'] . ", " . $codigo . ", '" . voltea_fecha($_POST['OF' . $registro_1->id_liquidacion]) . "', " . $_SESSION['CEDULA_USUARIO'] . ");";
					$tabla = mysql_query($consulta);
				}
			}
		}
		// --------MENSAJE
		echo "<script type=\"text/javascript\">alert('Expediente(s) Actualizado(s) Exitosamente!!!');</script>";
		// PARA CERRAR LA PAGINA
		echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
		exit();
	}
	?>

	<title>Expediente Pendiente</title>
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
		</table>
		<table width="50%" border=1 align=center>
			<tr>
				<td width="17%" colspan="2" bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">Liquidacion</span></a></strong></div>
				</td>
				<td bgcolor="#CCCCCC" width="19%">
					<div align="center" class="Estilo13"><strong>Monto</strong></div>
				</td>
				<td width="18%" bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Resultado</strong></div>
				</td>
				<td width="18%" bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Agencia</strong></div>
				</td>
				<td width="18%" bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Fecha</strong></div>
				</td>
			</tr>
			<?php
			$consulta = "SELECT id_sancion, monto_bs, concurrencia, especial, liquidacion, id_liquidacion, serie FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . " AND origen_liquidacion=" . $_GET['origen'] . " ORDER BY liquidacion;";
			$tabla = mysql_query($consulta);

			while ($registro = mysql_fetch_object($tabla)) {
			?>
				<tr>
					<td colspan="2">
						<div align="center" class="Estilo15"><?php echo $registro->liquidacion; ?></div>
					</td>
					<td>
						<div align="center" class="Estilo15"><?php echo formato_moneda($registro->monto_bs / $registro->concurrencia * $registro->especial); ?></div>
					</td>
					<td>
						<div align="left" class="Estilo15"><label>
								<select name="OR<?php echo $registro->id_liquidacion; ?>" size="1">
									<option value="">Seleccione</option>
									<?php
									//--------------------
									$consulta_x = "SELECT id_observacion, observacion, acepto FROM a_codigos_observacion WHERE origen_observacion='C';";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x))
									//-------------
									{
										if ($registro_x->id_observacion == 16 and ($registro->serie == 38 or $registro->id_sancion == 2500 or $registro->id_sancion == 2501)) {
										} else {
											echo '<option';
											if ($_POST['OR' . $registro->id_liquidacion] == $registro_x->id_observacion . '-' . $registro_x->acepto) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x->id_observacion . '-' . $registro_x->acepto;
											echo '">';
											echo palabras($registro_x->observacion);
											echo '</option>';
										}
									}
									?>
								</select>
							</label>
						</div>
					</td>
					<td>
						<div align="left" class="Estilo15"><label>
								<select name="OBANCO<?php echo $registro->id_liquidacion; ?>" size="1">
									<option value="0">Seleccione</option>
									<?php
									$consulta_x = "SELECT * FROM vista_ce_consulta_banco WHERE sector=" . $_GET['sector'] . " ORDER BY id_agencia;";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';
										if ($_POST['OBANCO'] == ($registro_x->id_banco . '-' . $registro_x->id_agencia)) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->id_agencia;
										echo '">';
										echo sprintf("%003s", $registro_x->id_agencia_ordinario) . " - " . sprintf("%003s", $registro_x->id_agencia_especial) . " - " . palabras($registro_x->Descripcion);
										echo '</option>';
									}
									?>
								</select>
							</label>
						</div>
					</td>
					<td>
						<div align="center" class="Estilo15"><label><span class="Estilo7">
									<input onclick='javascript:scwShow(this,event);' type="text" name="OF<?php echo $registro->id_liquidacion; ?>" size="8" readonly value="<?php echo $_POST['OF' . $registro->id_liquidacion]; ?>" />
								</span></label>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
		<div align="center">
			<p></p>
			<p><input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></p>
			<p></p>
		</div>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>