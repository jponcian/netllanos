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
	$acceso = 51;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	if ($_POST['CMDGUARDAR'] == 'Guardar') {
		if ($_POST['OFUNCIONARIO'] > 0) {
			//----------
			$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . " AND origen_liquidacion=" . $_GET['origen'] . " ORDER BY liquidacion;";
			$tabla = mysql_query($consulta);
			//----------
			while ($registro = mysql_fetch_object($tabla)) {
				//-----------
				$consultax = "UPDATE liquidacion SET fecha_asignacion_cobrador=date(now()), status = 32, usuario_asignacion_cobrador = " . $_SESSION['CEDULA_USUARIO'] . ", usuario = " . $_SESSION['CEDULA_USUARIO'] . ", cobrador = " . $_POST['OFUNCIONARIO'] . " WHERE status=" . $_GET['status'] . " AND id_liquidacion='" . $registro->id_liquidacion . "';";
				$tablax = mysql_query($consultax);
				//-----------
			}
			echo "<script type=\"text/javascript\">alert('Se ha Actualizado el Expediente, pronto se cerrar� la Pagina!');</script>";
			// PARA CERRAR LA PAGINA
			echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
			exit();
		} else {
			echo "<script type=\"text/javascript\">alert('No ha Seleccionado el Notificador!');</script>";
		}
	}

	?>

	<title>Expediente x Cobrar</title>
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
					<div align="center" class="Estilo13"><strong>Notificada</strong></div>
				</td>
			</tr>
			<?php
			$consulta = "SELECT fecha_not, monto_bs, concurrencia, especial, liquidacion, id_liquidacion FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND origen_liquidacion=" . $_GET['origen'] . " AND sector=" . $_GET['sector'] . " ORDER BY liquidacion;";
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
						<div align="center" class="Estilo15"><label><?php echo voltea_fecha($registro->fecha_not); ?></label>
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
						<p><strong>Seleccione aqu&iacute; el Cobrador a Asignar</strong></p>
						<p>
							<select name="OFUNCIONARIO" size="1">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = "SELECT cedula, Nombres, Apellidos FROM z_empleados WHERE (Rol='COB' or Rol2='COB' or Rol3='COB') AND cedula>1000000;";
								} else {
									$consulta_x = "SELECT cedula, Nombres, Apellidos FROM z_empleados WHERE (Rol='COB' or Rol2='COB' or Rol3='COB') AND cedula>1000000 and sector=" . $_SESSION['SEDE_USUARIO'] . ";";
								}

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