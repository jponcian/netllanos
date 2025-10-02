<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";

//-------------
if ($_POST['ONUMERO'] > 0) {
	$_SESSION['INICIO'] = $_POST['OINICIO'];
	$_SESSION['FIN'] = $_POST['OFIN'];
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

		<table border="1" align="center">
			<tr>
				<td height="37" colspan="20" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo1 Estilo2">Opciones</span></p>
				</td>
			</tr>
			<tr>
				<td height="27" align="center" bgcolor="#CCCCCC">
					<p><strong>Dependencia =&gt;</strong></p>
				</td>
				<td height="27" align="center" bgcolor="#CCCCCC">
					<p class="Estilo7"><span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE liquidacion<>"" GROUP BY sector';
								} else {
									// ---------
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE liquidacion<>"" and sector=' . $_SESSION['SEDE_USUARIO'] . ' ' . $origen . '  GROUP BY sector;';
								}
								//---------------
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
				<td height="27" align="center" bgcolor="#CCCCCC">
					<p><strong>A&ntilde;o =&gt;</strong></p>
				</td>
				<td height="27" align="center" bgcolor="#CCCCCC">
					<p class="Estilo7"><span class="Estilo1">
							<select name="OANNO" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									// -------------------------------------
									$consulta_x = 'SELECT left(liquidacion,4) as anno FROM vista_sanciones_aplicadas WHERE liquidacion<>"" AND sector=0' . $_POST['OSEDE'] . ' ' . $origen . ' GROUP BY anno ORDER BY anno DESC';
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
						</span></p>
				</td>
				<td height="27" align="center" bgcolor="#CCCCCC">
					<p><strong>Serie =&gt;</strong></p>
				</td>
				<td height="27" align="center" bgcolor="#CCCCCC">
					<p class="Estilo7"><span class="Estilo1">
							<select name="OSERIE" size="1" onChange="this.form.submit()">
								<option value="0">Todas</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									// -------------------------------------
									$consulta_x = 'SELECT serie FROM vista_sanciones_aplicadas WHERE liquidacion<>"" AND left(liquidacion,4)=' . $_POST['OANNO'] . ' AND serie>0 AND sector=0' . $_POST['OSEDE'] . ' ' . $origen . ' GROUP BY serie ORDER BY serie DESC';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSERIE'] == $registro_x['serie']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['serie'] . '>' . $registro_x['serie'] . '</option>';
									}
								}
								?>
							</select>
						</span></p>
				</td>
		</table>
	</form>
	<p>
		<?php if ($_POST['OSEDE'] > 0 and $_POST['OANNO'] > 0) {
		?>
	<form name="form3" method="post" action="reportes/secuenciales_liquidados.php?sede=<?php echo $_POST['OSEDE'] ?>&anno=<?php echo $_POST['OANNO'] ?>&serie=<?php echo $_POST['OSERIE'] ?>" target="_blank">
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