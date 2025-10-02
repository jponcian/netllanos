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

	$acceso = 47;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	if ($_POST['CMDGUARDAR'] == 'Guardar') {
		if ($_POST['OFUNCIONARIO'] > 0 and $_POST['OFECHA'] <> '') {
			//----------------------
			$i = 0;
			$j = 0;
			//----------
			$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . " AND origen_liquidacion=" . $_GET['origen'] . " ORDER BY liquidacion;";
			$tabla = mysql_query($consulta);
			//----------
			while ($registro = mysql_fetch_object($tabla)) {
				$i++;
				if (trim($_POST[$registro->id_liquidacion]) <> '') {
					$j++;
				}
			}
			//-----------
			if ($i == $j) {
				$tabla = mysql_query($consulta);
				//----------
				while ($registro = mysql_fetch_object($tabla)) {
					if (trim($_POST[$registro->id_liquidacion]) <> '') {
						//-----------
						$consultax = "UPDATE liquidacion SET status = 22, planilla_notificacion = " . trim($_POST[$registro->id_liquidacion]) . ", usuario = " . $_SESSION['CEDULA_USUARIO'] . ", notificador = " . $_POST['OFUNCIONARIO'] . ", fecha_asignacion_notificador = '" . voltea_fecha($_POST['OFECHA']) . "' WHERE status=" . $_GET['status'] . " AND id_liquidacion='" . $registro->id_liquidacion . "';";
						$tablax = mysql_query($consultax);
						//-----------
					}
				}
				echo "<script type=\"text/javascript\">alert('Se ha Actualizado el Expediente, pronto se cerrar� la Pagina!');</script>";
				// PARA CERRAR LA PAGINA
				echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
				exit();
			} else {
				echo "<script type=\"text/javascript\">alert('Debe Rellenar todas las Planillas!');</script>";
			}
		} else {
			echo "<script type=\"text/javascript\">alert('No ha Seleccionado el Notificador!');</script>";
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
			$consulta = "SELECT monto_bs, concurrencia, especial, liquidacion, id_liquidacion FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND origen_liquidacion=" . $_GET['origen'] . " AND sector=" . $_GET['sector'] . " ORDER BY liquidacion;";
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
						<div align="center" class="Estilo15"><label>
								<input type="text" maxlength="15" size="15" name="<?php echo $registro->id_liquidacion; ?>">
							</label>
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
						<p><strong>Seleccione aqu&iacute; el Notificador a Asignar</strong></p>
						<p>
							<select name="OFUNCIONARIO" size="1">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Notificador' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='" . strtoupper($_SESSION['NOMBRE_MODULO']) . "';";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OFUNCIONARIO'] == $registro_x->cedula) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->cedula;
									echo '">';
									echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
									echo '</option>';
								}
								?>
							</select>
							<span class="Estilo7">
								<?php if ($_POST['OFECHA'] == '') {
									$_POST['OFECHA'] = date('d/m/Y');
								}; ?>
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