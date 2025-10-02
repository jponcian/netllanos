<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";


if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 28;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//----------
include "0_respaldo_inv_seriales.php";
?>
<html>

<head>
	<title>Inventario</title>
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
				<td align="center" height="30" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1"><u>INVENTARIO</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION1" type="radio" onClick="this.form.submit()" value="ACTUAL" <?php if ($_POST['OOPCION1'] == 'ACTUAL') {
																													echo 'checked="checked" ';
																												} ?>>
							</label>
							Actual
						</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION1" type="radio" onClick="this.form.submit()" value="MENSUAL" <?php if ($_POST['OOPCION1'] == 'MENSUAL') {
																														echo 'checked="checked" ';
																													} ?>>
							</label>
							Mensual</strong></div>
				</td>
			</tr>
			<?php if ($_POST['OOPCION1'] == 'MENSUAL') {
			?>
				<tr>
					<td bgcolor="#FFFFFF">
						<div align="center"><strong>
								Periodo
							</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><strong>
								<label></label>
							</strong>
							<select name="OPERIODO" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								//----------
								$mes = array(Enero, Febrero, Marzo, Abril, Mayo, Junio, Julio, Agosto, Septiembre, Octubre, Noviembre, Diciembre);
								//----------
								$consulta_x = "SELECT fecha FROM timbre_inv_detallado_mensual GROUP BY fecha ORDER BY fechaproceso DESC;";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									echo '<option';
									if ($_POST['OPERIODO'] == $registro_x->fecha) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->fecha;
									echo '">';
									echo strtoupper($mes[(abs(substr($registro_x->fecha, 0, 2))) - 1]) . ' 20' . substr($registro_x->fecha, 3, 2);
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
												//--- INVENTARIO ACTUAL
												if ($_POST['OOPCION1'] and $_POST['OOPCION1'] == 'ACTUAL') {
													echo 'reportes/inv_detallado_seriales.php';
												} else {
													//--- INVENTARIO MENSUAL
													if ($_POST['OPERIODO'] and $_POST['OPERIODO'] <> '-1') {
														echo 'reportes/inv_detallado_seriales_mensual.php';
														$_SESSION['var1'] = 'INICIAL';
														$_SESSION['var2'] = $_POST['OPERIODO'];
													}
												}
												?>" target="_blank">
		<p align="center"><input type="submit" class="boton" name="CMD1" value="Ver Reporte"></p>
	</form>

	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>