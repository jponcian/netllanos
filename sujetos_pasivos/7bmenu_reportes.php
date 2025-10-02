<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 151;
//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";
?>
<html>

<head>
	<title>Men&uacute; de Reportes</title>
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

		.Estilo1 {
			font-size: 18px;
			font-weight: bold;
			color: #FF0000;
		}

		.Estilo22 {
			color: #FFFFFF
		}

		.Estilo55 {
			font-size: 22px
		}

		.Estilo66 {
			color: #FFFFFF;
			font-size: 22px;
		}

		.Estilo151 {
			color: #FFFFFF;
			font-size: 17px;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
		<p>
		<table border="1" align="center">
			<tr>
				<td height="45" colspan="5" align="center" bgcolor="#FF0000"><span class="Estilo1 Estilo22 Estilo55"><span class="Estilo151">Providencias</span></span></td>
			</tr>
			<tr>
				<td height="37" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" onClick="this.form.submit()" value="0" <?php if ($_POST['OOPCION'] == '0') {
																												echo 'checked="checked" ';
																											} ?>>
							</label>
							Registro</strong></div>
				</td>
				<td height="37" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" onClick="this.form.submit()" value="1" <?php if ($_POST['OOPCION'] == '1') {
																												echo 'checked="checked" ';
																											} ?>>
							</label>
							Emitidas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" type="radio" onClick="this.form.submit()" value="2" <?php if ($_POST['OOPCION'] == '2') {
																											echo 'checked="checked" ';
																										} ?>>
							Incorporados</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" type="radio" onClick="this.form.submit()" value="3" <?php if ($_POST['OOPCION'] == '3') {
																											echo 'checked="checked" ';
																										} ?>>
							Notificados</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" type="radio" onClick="this.form.submit()" value="4" <?php if ($_POST['OOPCION'] == '4') {
																											echo 'checked="checked" ';
																										} ?>>
							i-Seniat</strong></div>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="30%" border="1" align="center">
			<tr>
				<td height="33" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo151">Dependencia</span></p>
				</td>
				<td colspan="4" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo151">Fecha
							<?php
							if ($_POST['OOPCION'] == "1") {
								echo 'de Emision';
							}
							if ($_POST['OOPCION'] == "2") {
								echo 'de Incorporaci&oacute;n';
							}
							if ($_POST['OOPCION'] == "3") {
								echo 'de Notificaci&oacute;n';
							}
							if ($_POST['OOPCION'] == "4") {
								echo 'de ISeniat';
							}
							?></span></p>
				</td>
			</tr>
			<tr>
				<td width="70%"><span class="Estilo1">
						<select name="OSEDE" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							if ($_SESSION['SEDE_USUARIO'] == 1) {
								$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5;';
							} else {
								$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5 AND id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
							}
							//-------------
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OSEDE'] == $registro_x['id_sector']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
							}
							?>
						</select>
					</span></td>
				<td width="17%" bgcolor="#CCCCCC"><strong>Desde:</strong></td>
				<td><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="10" readonly value="<?php echo $_POST['OINICIO']; ?>" />
						</div>
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>
						Hasta:</strong></td>
				<td><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFIN" size="10" readonly value="<?php echo $_POST['OFIN']; ?>" />
						</div>
					</label></td>


			</tr>
		</table>
		<p align="center">
			<input type="submit" class="boton" name="CMDBOTON" value="Cargar">
		</p>
	</form>

	<p>
		<?php

		if ($_POST['OOPCION'] <> "" and $_POST['OSEDE'] > 0 and $_POST['OINICIO'] <> "" and $_POST['OFIN'] <> "") {
			$_SESSION['OSEDE'] = $_POST['OSEDE'];
			$_SESSION['VARIABLE'] = $_POST['OOPCION'];
			$_SESSION['FECHA1'] = voltea_fecha($_POST['OINICIO']);
			$_SESSION['FECHA2'] = voltea_fecha($_POST['OFIN']);
		?>
	<table width=300 align=center border=0>
		<td colSpan=1>
			<div align="center">
				<form name="form3" method="post" action="reportes/providencias.php" target="_blank">
					<input type="submit" class="boton" name="CMDREPORTE" value="Ver Reporte (Exportar a Excel)">
				</form>
			</div>
		</td>
		</tr>
	</table>
<?php
		}
?>
<p>&nbsp;
</p>
<?php include "../pie.php"; ?>
</p>
<p>&nbsp;</p>
</body>

</html>