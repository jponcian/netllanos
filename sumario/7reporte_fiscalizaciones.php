<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 65;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
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
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1"><u>Fiscalizaciones Sumario Administtativo </u></span></td>
			</tr>
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1">Seleccionar Fechas o Periodos </span></td>
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
		$_SESSION['INICIO'] = $_POST['OINICIO'];
		$_SESSION['FIN'] = $_POST['OFIN'];
		$_SESSION['SEDE'] = $_POST['OSEDE'];
		$consulta = "SELECT nombre, providencia, contribuyente, rif, programa, numacta, fecha_recepcion, fecha_asignacion_ponente, ponente, fecha_devuelto_fiscalizacion, fecha_culminacion, monto_reparo, monto_confirmado, monto_revocado, total_tributo, multa, intereses, resolucion_sumario FROM vista_sumario_reporte WHERE fecha BETWEEN '" . voltea_fecha($_SESSION['INICIO']) . "' AND '" . voltea_fecha($_SESSION['FIN']) . "'";
		$tabla = mysql_query($consulta);
		$numero_filas = mysql_num_rows($tabla);

		if ($numero_filas > 0) {
			echo '<form name="form4" method="post" action="reportes/reporte_excel.php" target="_blank">
			  <p align="center"><input type="submit" class="boton" name="CMDCONCLUIDOSEXCEL" value="Ver Reporte en Excel"></p>
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