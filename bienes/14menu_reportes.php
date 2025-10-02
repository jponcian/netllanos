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

		.Estilo11 {
			font-size: 17px;
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
		<table width="45%" border="1" align="center">
			<tr>
				<td height="40" align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo77"><u>Seleccionar Divisi&oacute;n </u></span></td>
			</tr>
			<tr>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Dependencia:</strong></td>
				<td width="14%"><label><span class="">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="0">--> Seleccione <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT id_sector_actual, sector_actual FROM vista_bienes_reasignaciones_solicitadas WHERE numero>=0 GROUP BY id_sector_actual';
										} else {
											$consulta_x = 'SELECT id_sector_actual, sector_actual FROM vista_bienes_reasignaciones_solicitadas WHERE numero>=0 GROUP BY id_sector_actual';
										}
										//-------------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['id_sector_actual']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_sector_actual'] . '>' . $registro_x['sector_actual'] . '</option>';
										}

										?>
							</select>
						</span></label></td>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Divisi&oacute;n:</strong></td>
				<td colspan="1"><label><span class="">
							<select name="ODIVISION" size="1" onChange="this.form.submit()">
								<option value="0">--> Seleccione <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT division_actual, id_division_actual  FROM vista_bienes_reasignaciones_solicitadas WHERE numero>=0 AND id_sector_actual = 0' . $_POST['OSEDE'] . ' GROUP BY id_division_actual';
										} else {
											$consulta_x = 'SELECT division_actual, id_division_actual  FROM vista_bienes_reasignaciones_solicitadas WHERE numero>=0 AND id_sector_actual = 0' . $_POST['OSEDE'] . ' GROUP BY id_division_actual';
										}
										echo $consulta_x;
										//------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['ODIVISION'] == $registro_x['id_division_actual']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_division_actual'] . '>' . $registro_x['division_actual'] . '</option>';
										}
										?>
							</select>
						</span></label></td>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>A&ntilde;o:</strong></td>
				<td colspan="1"><label><span class="">
							<select name="OANNO" size="1" onChange="this.form.submit()">
								<option value="0">--> Todos <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT anno  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual<>id_division_destino AND numero>0 AND id_sector_actual = 0' . $_POST['OSEDE'] . ' GROUP BY anno DESC';
										} else {
											$consulta_x = 'SELECT anno FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual<>id_division_destino AND numero>0 AND id_sector_actual = 0' . $_POST['OSEDE'] . ' GROUP BY anno DESC';
										}
										//------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OANNO'] == $registro_x['anno']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
										}
										?>
							</select></span></label></td>

			</tr>
		</table>
		<br>
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="10" align="center">
						<p class="Estilo77"><u>Movimientos Internos </u></p>
					</td>
				</tr>
				<tr>
					<?php if ($eliminar == 'SI' or $reasignar == 'SI') { ?>
						<th width="44" height=41 bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Sel</strong></div>
						</th>
					<?php } ?>
					<th width="18" bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>N�</strong></div>
					</th>
					<th width="90" bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Fecha</strong></div>
					</th>
					<th bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Divisi&oacute;n</strong></div>
					</th>
					<th bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Area Anterior </strong></div>
					</th>
					<th bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Area Actual</strong></div>
					</th>
					<th bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Documentos</strong></div>
					</th>
				</tr>
				<?php
				$sede = $_POST['OSEDE'];
				$division = $_POST['ODIVISION'];
				$anno = $_POST['OANNO'];
				$filtro = 'id_division_actual=id_division_destino';

				if ($sede > 0) {
					$filtro = $filtro . ' AND id_sector_actual=' . $sede;
				} else {
					$filtro = $filtro . ' AND id_sector_actual=0';
				}
				if ($division > 0) {
					$filtro = $filtro . ' AND id_division_actual=' . $division;
				} else {
					$filtro = $filtro . ' AND id_division_actual=0';
				}
				if ($anno > 0) {
					$filtro = $filtro . ' AND anno=' . $anno;
				} else {
					$filtro = $filtro . ' AND anno<>0';
				}

				//-------- 
				$consulta = "SELECT * FROM vista_bienes_reasignaciones_solicitadas WHERE $filtro GROUP BY id_reasignacion ORDER BY fecha DESC";
				//echo $consulta;
				//----------------------- MONTAJE DE LOS DATOS
				$i = 0;

				$tabla = mysql_query($consulta);

				while ($registro = mysql_fetch_object($tabla)) {
					$i++;
				?>
					<tr>
						<?php if ($eliminar == 'SI' or $reasignar == 'SI') { ?>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8 Estilo1"><input type="checkbox" name="<?php echo $registro->id_bien; ?>" value="<?php echo $registro->id_bien; ?>" /></div>
							</td>
						<?php } ?>
						<td bgcolor="#FFFFFF">
							<div align="center" class="Estilo8 Estilo11"><?php echo $i; ?></div>
						</td>
						<td bgcolor="#FFFFFF">
							<div align="center" class="Estilo8 Estilo11"><?php echo voltea_fecha($registro->fecha); ?></div>
						</td>
						<td bgcolor="#FFFFFF">
							<div align="left" class="Estilo8 Estilo11"><?php echo ucwords(strtolower($registro->division_actual)); ?></div>
						</td>
						<td bgcolor="#FFFFFF">
							<div align="left" class="Estilo8 Estilo11"><?php echo ucwords(strtolower($registro->area_actual)); ?></div>
						</td>
						<td bgcolor="#FFFFFF">
							<div align="left" class="Estilo8 Estilo11"><?php echo ucwords(strtolower($registro->area_destino)); ?></div>
						</td>
						<td bgcolor="#FFFFFF">
							<div align="center" class="Estilo8 Estilo11"><a href="reportes/x_movimiento.php?id=<?php echo $registro->id_reasignacion; ?>" target="_blank">Anexo</a></div>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		</p>
		<?php if ($eliminar == 'SI') { ?>
			<p align="center">
				<input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />
			</p> <?php
				} ?>
	</form>
	<p>
		<?php if ($_POST['OAREA'] > 0) {
		?>
	<form name="form3" method="post" action="formatos/x_inventario.php" target="_blank">
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