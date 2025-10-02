<?php
session_start();
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
//----------------------
if ($_POST['OPCION'] == 'fecha') {
	$_POST['OMEMO'] = 0;
}
if ($_POST['OPCION'] == 'memo') {
	$_POST['OFECHA'] = '';
	$_POST['OFECHA2'] = '';
}
?>
<html>

<head>
	<title>Men&uacute; de Reportes</title>
	<script type='text/JavaScript' src='../../funciones/scw_normal.js'></script>
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

		.Estilo1 {
			font-size: 18px;
			font-weight: bold;
			color: #FF0000;
		}

		.Estilo2 {
			color: #FFFFFF
		}

		.Estilo3 {
			font-size: 24px
		}

		.Estilo5 {
			color: #FFFFFF;
			font-size: 22px;
		}

		.Estilo10 {
			font-size: 20px
		}

		.Estilo7 {
			font-size: 22px
		}

		.Estilo8 {
			color: #000000
		}

		.Estilo11 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script language="javascript" type="text/javascript" src="../datetimepicker_css.js">
	</script>
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>

	<form name="form1" method="post">

		<table border="1" align="center">
			<tr>
				<td height="39" colspan="20" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo1 Estilo2 Estilo3"><span class="Estilo5">Importadas a Cobro </span></span></p>
				</td>
			</tr>
			<tr>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo10 Estilo8"><strong>Dependencia =&gt;</strong></p>
				</td>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo7"><span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$origen = '';
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE memo>0 GROUP BY sector';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
									}
								} else {
									// --- VALIDACION DEL ORIGEN DEL USUARIO
									if ($_SESSION['ORIGEN_USUARIO'] > 0) {
										$origen = 'and origen_liquidacion=' . $_SESSION['ORIGEN_USUARIO'];
									} else {
										$origen = '';
									}
									// -------------------------------------
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE memo2>0 AND sector=' . $_SESSION['SEDE_USUARIO'] . '  ' . $origen . ' GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									if ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
									}
								}
								?>
							</select>
						</span></p>
				</td>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo8 Estilo10"><strong>
							<input onClick="this.form.submit()" name="OPCION" type="radio" value="fecha" checked <?php if ($_POST['OPCION'] == 'fecha') {
																														echo 'checked="checked"';
																													} ?>>
							Fecha =&gt;</strong></p>
				</td>
				<?php if ($_POST['OPCION'] == 'fecha') {
				?>
					<td height="37" align="center" bgcolor="#999999">
						<p class="Estilo7"><span class="Estilo11">
								Desde
								<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>" />
								Hasta
								<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA2" size="8" readonly value="<?php echo $_POST['OFECHA2']; ?>" />
								<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
							</span></p>
					</td>
				<?php
				}
				?>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo8 Estilo10"><strong>
							<input onClick="this.form.submit()" name="OPCION" type="radio" value="memo" <?php if ($_POST['OPCION'] == 'memo') {
																											echo 'checked="checked"';
																										} ?>>
							Transferencia =&gt;</strong></p>
				</td>
				<?php if ($_POST['OPCION'] == 'memo') {
				?>
					<td height="37" align="center" bgcolor="#999999">
						<p class="Estilo7"><span class="Estilo1">
								<select name="OMEMO" size="1" onChange="this.form.submit()">
									<option value="0">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										// --- VALIDACION DEL ORIGEN DEL USUARIO
										if ($_SESSION['ORIGEN_USUARIO'] > 0) {
											$origen = 'and origen_liquidacion=' . $_SESSION['ORIGEN_USUARIO'];
										} else {
											$origen = '';
										}
										// -------------------------------------
										$consulta_x = 'SELECT memo2 as memo FROM vista_sanciones_aplicadas WHERE memo2>0 AND sector=0' . $_POST['OSEDE'] . ' ' . $origen . ' GROUP BY memo';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OMEMO'] == $registro_x['memo']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['memo'] . '>' . $registro_x['memo'] . '</option>';
										}
									}
									?>
								</select>
							</span></p>
					</td>
				<?php
				}
				?>
		</table>
	</form>
	<p>
		<?php if ($_POST['OMEMO'] > 0 or $_POST['OFECHA'] <> '') {
		?>
	<form name="form3" method="post" action="../reportes/importadas_a_cobro.php?sede=<?php echo $_POST['OSEDE'] ?>&memo=<?php echo $_POST['OMEMO'] ?>&origen=0<?php echo $origen ?>&fecha1=<?php echo $_POST['OFECHA'] ?>&fecha2=<?php echo $_POST['OFECHA2'] ?>" target="_blank">
		<p align="center"><input type="submit" class="boton" name="CMDBOTON" value="Ver Reporte"></p>
	</form>
<?php
		}
?>
<p>&nbsp;</p>
<?php include "../pie.php"; ?>
</p>

<p>&nbsp;</p>
</body>

</html>