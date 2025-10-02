<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 77;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['AREA'] = $_POST['OAREA'];
$_SESSION['DIVISION'] = $_POST['ODIVISION'];
$_SESSION['SEDE'] = $_POST['OSEDE'];
$_SESSION['ORDEN'] = $_POST['OORDEN'];
//---------------
if ($_POST['OAREA'] == 0) {
	$_SESSION['AREAS'] = 'TODAS';
} else {
	$_SESSION['AREAS'] = 'DETALLE';
}
if ($_POST['ODIVISION'] == 0) {
	$_SESSION['DIVISIONES'] = 'TODAS';
} else {
	$_SESSION['DIVISIONES'] = 'DETALLE';
}
if ($_POST['OSEDE'] == 0) {
	$_SESSION['SEDES'] = 'TODAS';
} else {
	$_SESSION['SEDES'] = 'DETALLE';
}

?>
<html>

<head>
	<title>Men&uacute; de Reportes</title>
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

		.Estilo77 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<!--<meta http-equiv="refresh" content="30">-->
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
		<table width="60%" border="1" align="center">
			<tr>
				<td height="40" align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo77"><u>Inventario Bienes Nacional</u></span></td>
			</tr>
			<tr>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Dependencia:</strong></td>
				<td width="14%"><label><span class="Estilo7">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="0">--> Todas <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT z_jefes_detalle.id_sector, z_sectores.nombre FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division INNER JOIN z_sectores ON z_sectores.id_sector = z_jefes_detalle.id_sector WHERE z_sectores.id_sector<=5 GROUP BY z_jefes_detalle.id_sector;';
										} else {
											$consulta_x = 'SELECT z_jefes_detalle.id_sector, z_sectores.nombre FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division INNER JOIN z_sectores ON z_sectores.id_sector = z_jefes_detalle.id_sector WHERE z_sectores.id_sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY z_jefes_detalle.id_sector;';
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
							</select></span></label></td>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Divisi&oacute;n:</strong></td>
				<td colspan="1"><label><span class="Estilo7">
							<select name="ODIVISION" size="1" onChange="this.form.submit()">
								<option value="0">--> Todas <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE id_sector=0' . $_POST['OSEDE'] . ' GROUP BY z_jefes_detalle.division';
										} else {
											$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE id_sector=0' . $_POST['OSEDE'] . ' GROUP BY z_jefes_detalle.division';
										}
										//-----------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['ODIVISION'] == $registro_x['division']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['division'] . '>' . palabras($registro_x['descripcion']) . '</option>';
										}
										?>
							</select>
						</span></label></td>
				<td height="35" colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Area:</strong></td>
				<td colspan="1"><label><span class="Estilo7">
							<select name="OAREA" size="1" onChange="this.form.submit()">
								<option value="0">--> Todas <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT bn_areas.id_area, bn_areas.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE bn_areas.division=0' . $_POST['ODIVISION'] . ' GROUP BY bn_areas.id_area ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
										} else {
											$consulta_x = 'SELECT bn_areas.id_area, bn_areas.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE bn_areas.division=0' . $_POST['ODIVISION'] . ' GROUP BY bn_areas.id_area ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
										}

										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OAREA'] == $registro_x['id_area']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_area'] . '>' . palabras($registro_x['descripcion']) . '</option>';
										}
										?>
							</select></span></label></td>
				<td height="35" colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Orden:</strong></td>
				<td colspan="1"><label><span class="Estilo7">
							<select name="OORDEN" size="1" onChange="this.form.submit()">
								<option <?php if ($_POST['OORDEN'] == 'categoria, Nombres') {
											echo 'selected="selected" ';
										} ?> value="categoria, Nombres">Categoria</option>
								<option <?php if ($_POST['OORDEN'] == 'Nombres, categoria') {
											echo 'selected="selected" ';
										} ?> value="Nombres, categoria">Funcionario</option>
								<option <?php if ($_POST['OORDEN'] == 'inf_nombre_equipo') {
											echo 'selected="selected" ';
										} ?> value="inf_nombre_equipo">Equipo</option>
							</select></span></label></td>
			</tr>
		</table>
	</form>
	<p>
		<?php if (1 == 1) //($_POST['OSEDE']>0 and $_POST['ODIVISION']>0 and $_POST['OAREA']>0) 
		{
		?>
	<form name="form3" method="post" action="reportes/x_inventario.php" target="_blank">
		<p align="center">
			<input type="submit" class="boton" name="CMDBOTON" value="Ver Inventario">
		</p>
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