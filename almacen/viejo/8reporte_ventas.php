<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 29;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//----------
include "0_respaldo_inv_seriales.php";

?>
<html>

<head>
	<title>Ventas</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>

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

		.Estilo7 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}

		.Estilo17 {
			color: #FFFFFF
		}

		.Estilo18 {
			color: #000000
		}
		-->
	</style>

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
	<form name="form1" method="post">

		<p>&nbsp;</p>
		<table width="24%" border="1" align="center">
			<tr>
				<td align="center" height="30" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1"><u>VENTAS</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION1" type="radio" onChange="this.form.submit()" value="1" <?php if ($_POST['OOPCION1'] == '1') {
																												echo 'checked="checked" ';
																											} ?>>
								Relacion Mensual
							</label>
						</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION1" type="radio" onChange="this.form.submit()" value="2" <?php if ($_POST['OOPCION1'] == '2') {
																												echo 'checked="checked" ';
																											} ?>>
							</label>
							Resumen Especies
						</strong></div>
				</td>
			</tr>
			<?php if ($_POST['OOPCION1'] == '1' or $_POST['OOPCION1'] == '2') {
			?>
				<tr>
					<td bgcolor="#FFFFFF">
						<div align="center"><strong>
								Periodo</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><strong>
								<label></label>
							</strong>
							<select name="OPERIODO" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								//----------
								$meses = array(Enero, Febrero, Marzo, Abril, Mayo, Junio, Julio, Agosto, Septiembre, Octubre, Noviembre, Diciembre);
								//----------
								$consulta_x = "SELECT concat(Month(fecha),'/',Year(fecha)) AS fecha1 FROM timbre_ventas GROUP BY concat(Month(fecha),'/',Year(fecha)) ORDER BY concat(Month(fecha),'/',Year(fecha));";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									list($mes, $anno) = explode('/', $registro_x->fecha1);
									//--------------
									echo '<option';
									if ($_POST['OPERIODO'] == $registro_x->fecha1) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->fecha1;
									echo '">';
									echo strtoupper($meses[(abs($mes)) - 1]) . ' ' . $anno;
									echo '</option>';
								}
								?>
							</select>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
	</form>

	<form name="form2" method="post" action="<?php
												if ($_POST['OOPCION1'] == '1') {
													echo 'reportes/ven_relacion_mensual.php';
													$_SESSION['var1'] = $_POST['OPERIODO'];
												}
												if ($_POST['OOPCION1'] == '2') {
													echo 'reportes/ven_resumen_mensual.php';
													$_SESSION['var1'] = $_POST['OPERIODO'];
												}
												?>" target="_blank">
		<?php
		if ($_POST['OPERIODO'] <> '0') {
		?>
			<p align="center"><input type="submit" class="boton" name="CMD1" value="Ver Reporte"></p>
		<?php
		}
		?>
	</form>

	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>