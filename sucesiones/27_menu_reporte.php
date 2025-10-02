<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$_SESSION['INICIO'] = voltea_fecha($_POST['OINICIO']);
$_SESSION['FIN'] = voltea_fecha($_POST['OFIN']);
$_SESSION['SEDE'] = $_POST['OSEDE'];
$_SESSION['VARIABLE'] = $_POST['OOPCION1'];

?>
<html>

<head>
	<title>Men&uacute; Reportes</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
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
			color: #FFFFFF;
			font-size: 18px;
			font-weight: bold;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>

	<form name="form1" method="post" action="">

		<table width="38%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="2"><span class="Estilo1">Certificados</span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION1" type="radio" onClick="this.form.submit()" value="1" <?php if ($_POST['OOPCION1'] == '1') {
																												echo 'checked="checked" ';
																											} ?>>
							</label>
							Solvencia
						</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION1" type="radio" onClick="this.form.submit()" value="2" <?php if ($_POST['OOPCION1'] == '2') {
																												echo 'checked="checked" ';
																											} ?>>
							</label>
							Liberaci&oacute;n
						</strong></div>
				</td>
			</tr>
		</table>
		<table width="38%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1">Seleccionar Fechas de Emisi&oacute;n</span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong> Sector:</strong></td>
				<td colspan="3"><span class="Estilo1">
						<select name="OSEDE" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							if ($_SESSION['ADMINISTRADOR'] > 0) {
								$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
							} else {
								$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
							}

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
			</tr>

			<tr>
				<td width="24%" bgcolor="#CCCCCC"><strong> Desde:</strong></td>
				<td width="32%" align="center"><label>
						<input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="8" readonly value="<?php echo $_POST['OINICIO']; ?>" />
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong> Hasta:</strong></td>
				<td width="28%" align="center"><label>
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFIN" size="8" readonly value="<?php echo $_POST['OFIN']; ?>" />
					</label></td>
			</tr>
		</table>
		<p align="center">
			<label></label>
			<label>
				<input type="submit" class="boton" name="CMDCARGAR" value="Cargar">
			</label>
		</p>
	</form>
	<?php
	if ($_POST['CMDCARGAR'] == 'Cargar') {
		if ($_SESSION['VARIABLE'] == 1) {
			$Sentencia = "SELECT * FROM vista_re_sucesiones_solvencias ";
		}
		if ($_SESSION['VARIABLE'] == 2) {
			$Sentencia = "SELECT * FROM vista_re_sucesiones_liberacion ";
		}

		$Filtro = "WHERE fecha_emision BETWEEN '" . ($_SESSION['INICIO']) . "' AND '" . ($_SESSION['FIN']) . "' AND sector= " . $_SESSION['SEDE'] . "; ";
		$consulta_x = $Sentencia . $Filtro . $origen . $Orden . "";
		//echo $consulta_x ;
		$tabla = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla);

		if ($numero_filas > 0) {
			echo '<form name="form4" method="post" action="Reportes/certificados.php" target="_blank">
			  <p align="center"><input type="submit" class="boton" name="CMDCONCLUIDOS" value="Ver Reporte"></p>
			</form>';
		} else {
			echo '<form name="form5" method="post">
	  <p align="center"><strong>&iexcl; No Existe Informacion para esas Fechas ! </strong></p>
	</form>';
		}
	} ?>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>