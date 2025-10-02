<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
// --- VALIDACION DEL ORIGEN DEL USUARIO
if ($_SESSION['ORIGEN_USUARIO'] > 0) {
	$origen = 'and origen_liquidacion=' . $_SESSION['ORIGEN_USUARIO'];
} else {
	$origen = '';
}
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
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
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
		<p>&nbsp;</p>
		<table border="1" align="center">
			<tr>
				<td height="39" colspan="20" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo1 Estilo2 Estilo3"><span class="Estilo5">Asignadas en Notificaci&oacute;n</span></span></p>
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
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>21 and memo>0 GROUP BY sector';
								} else {
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE  status>21 and  memo>0 AND sector=' . $_SESSION['SEDE_USUARIO'] . '  ' . $origen . ' GROUP BY sector;';
								}
								//----------------
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
								}
								?>
							</select>
						</span></p>
				</td>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo8 Estilo10"><strong>Desde =&gt;</strong></p>
				</td>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo7">
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA1" size="10" readonly value="<?php echo $_POST['OFECHA1']; ?>" />
					</p>
				</td>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo8 Estilo10"><strong>Hasta =&gt;</strong></p>
				</td>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo7">
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA2" size="10" readonly value="<?php echo $_POST['OFECHA2']; ?>" />
					</p>
				</td>
		</table>
		<p>
		<p align="center">
			<input type="submit" name="Submit" value="Cargar">
		</p>
		</p>
	</form>
	<p>
		<?php if ($_POST['OSEDE'] > 0 and $_POST['OFECHA1'] <> '' and $_POST['OFECHA2'] <> '') {
		?>
	<form name="form3" method="post" action="reportes/asignadas_notificacion.php?sede=<?php echo $_POST['OSEDE'] ?>&fecha1=<?php echo $_POST['OFECHA1'] ?>&fecha2=<?php echo $_POST['OFECHA2'] ?>&origen=0<?php echo $origen ?>" target="_blank">
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