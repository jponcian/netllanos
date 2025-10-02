<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 110;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
// --- VALIDACION DEL ORIGEN DEL USUARIO
if ($_SESSION['ORIGEN_USUARIO'] > 0 and $_SESSION['ORIGEN_USUARIO'] <> 13) {
	$origen = ' and origen_liquidacion=' . $_SESSION['ORIGEN_USUARIO'];
} else {
	$origen = ' ';
}
if ($_SESSION['SEDE_USUARIO'] <> 1) {
	$sede = ' and sector=' . $_SESSION['SEDE_USUARIO'];
} else {
	$sede = ' ';
}
// -------------------------------------
?>
<html>

<head>
	<title>Men&uacute; de Reportes</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">&nbsp;
		<?php
		include "menu.php";
		?>
		</div>

	<form name="form1" method="post" action="">

		<table width="38%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1"><u>Transferidas a Liquidaci&oacute;n </u></span></td>
			</tr>
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1">Seleccionar Fechas o Periodos </span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong> Sector:</strong></td>
				<td colspan="3"><span class="Estilo1">
						<select name="OSEDE" size="1" onChange="this.form.submit()">
							<option value="0">--> Seleccione <--< /option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_transferidas_cob_liquidacion GROUP BY sector;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['id_sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
										}
									} else {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_transferidas_cob_liquidacion GROUP BY sector;';
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
		$_SESSION['INICIO'] = voltea_fecha($_POST['OINICIO']);
		$_SESSION['FIN'] = voltea_fecha($_POST['OFIN']);
		$_SESSION['SEDE'] = $_POST['OSEDE'];
		//-----------------
		$consulta = "SELECT rif FROM vista_transferidas_cob_liquidacion WHERE fecha_transferencia_a_liq BETWEEN '" . ($_SESSION['INICIO']) . "' AND '" . ($_SESSION['FIN']) . "' AND sector = " . $_POST['OSEDE'] . "";
		$tabla = mysql_query($consulta);
		$numero_filas = mysql_num_rows($tabla);

		if ($numero_filas > 0) {
			echo '<form name="form4" method="post" action="Reportes/transferidas_a_liquidacion.php" target="_blank">
			  <p align="center"><input type="submit" class="boton" name="CMDCONCLUIDOSEXCEL" value="Ver Reporte"></p>
			</form>';
		} else {
			echo '<form name="form5" method="post">
	  <p align="center"><strong>&iexcl; No Existe Informacion para esas Fechas! </strong></p>
	</form>';
		}
	} ?>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>