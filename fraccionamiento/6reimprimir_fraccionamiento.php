<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 96;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['INICIO'] = voltea_fecha($_POST['OINICIO']);
$_SESSION['FIN'] = voltea_fecha($_POST['OFIN']);
$_SESSION['SEDE'] = $_POST['OSEDE'];

?>

<html>

<head>
	<title>Men&uacute; Resoluciones</title>
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
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1"><u>Reimprimir Resoluciones </u></span></td>
			</tr>
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1">Datos del Expediente </span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong> Sector:</strong></td>
				<td colspan="3"><span class="Estilo1">
						<select name="OSEDE" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							if ($_SESSION['ADMINISTRADOR'] > 0) {
								$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['id_sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
							} else {
								$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['id_sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
							}
							?>
						</select>
					</span></td>

				<td width="24%" bgcolor="#CCCCCC"><strong> A&ntilde;o:</strong></td>
				<td width="32%" align="center"><label><span class="Estilo1">
							<select name="OANNO" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									$consulta_x = 'SELECT anno FROM expedientes_fraccionamiento WHERE status>0 and sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OANNO'] == $registro_x['anno']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
									}
								}
								?>
							</select>
						</span></label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong> Numero:</strong></td>
				<td width="28%" align="center"><label><span class="Estilo1">
							<select name="ONUMERO" size="1">
								<option value="-1">Seleccione</option>
								<?php
								if ($_POST['OANNO'] > 0) {
									$consulta_x = 'SELECT numero FROM expedientes_fraccionamiento WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' and numero<>9999;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['ONUMERO'] == $registro_x['numero']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
									}
								}
								?>
							</select></span></label></td>
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
		$consulta = "SELECT liquidacion.rif, liquidacion.fraccionada FROM liquidacion WHERE anno_expediente=" . $_POST['OANNO'] . " and num_expediente=" . $_POST['ONUMERO'] . " and origen_liquidacion=" . $origenF . " and sector=" . $_POST['OSEDE'];
		$tabla = mysql_query($consulta);
		$numero_filas = mysql_num_rows($tabla);
		$registro = mysql_fetch_object($tabla);

		if ($numero_filas > 0) {
	?><table align="center">
				<tr>
					<td>
						<form name="form4" method="post" action="formatos/reimprimir_aprobacion.php?id=<?php echo $registro->fraccionada ?>" target="_blank">
							<input type="submit" class="boton" name="CMDCONCLUIDOSEXCEL" value="Aprobacion">
						</form>
					<td>
						<form name="form5" method="post" action="formatos/reimprimir_contrato.php?id=<?php echo $registro->fraccionada ?>" target="_blank">
							<input type="submit" class="boton" name="CMDCONCLUIDOSEXCEL" value="Contrato">
						</form>
					</td>
					<td>
						<form name="form6" method="post" action="formatos/reimprimir_anexos.php?id=<?php echo $registro->fraccionada ?>" target="_blank">
							<input type="submit" class="boton" name="CMDCONCLUIDOSEXCEL" value="Anexos">
						</form>
					</td>
				</tr>
			</table><?php
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