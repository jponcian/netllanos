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
								//-------------
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>30 and memo2>0 GROUP BY sector';
								} else {
									// -------------------------------------
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>30 and memo2>0 AND sector=' . $_SESSION['SEDE_USUARIO'] . ' ' . $origen . ' GROUP BY sector;';
								}
								//-------------
								$tabla_x = mysql_query($consulta_x);
								if ($registro_x = mysql_fetch_array($tabla_x)) {
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
					<p class="Estilo8 Estilo10"><strong>Transferencia =&gt;</strong></p>
				</td>
				<td height="37" align="center" bgcolor="#999999">
					<p class="Estilo7"><span class="Estilo1">
							<select name="OMEMO" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									// -------------------------------------
									$consulta_x = 'SELECT memo2 as memo FROM vista_sanciones_aplicadas WHERE status>30 AND memo2>0 AND sector=0' . $_POST['OSEDE'] . ' ' . $origen . ' GROUP BY memo2 ORDER BY memo2 DESC';
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
		</table>
	</form>
	<p>
		<?php if ($_POST['OMEMO'] > 0) {
		?>
	<form name="form3" method="post" action="reportes/importadas_a_cobro.php?sede=<?php echo $_POST['OSEDE'] ?>&memo=<?php echo $_POST['OMEMO'] ?>&origen=0<?php echo $origen ?>" target="_blank">
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