<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 156;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	

if ($_POST['OSEDE'] > 0) {
	$sede = $_POST['OSEDE'];
}
if ($_POST['ODIVISION'] > 0) {
	$division = $_POST['ODIVISION'];
}
if ($_POST['OAREA'] > 0) {
	$area = $_POST['OAREA'];
}

?>
<html>

<head>
	<title>Equipos en Soporte Tecnico</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=Edge" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>

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
	<form name="form1" method="post" action="#vista">
		<table width="60%" border="1" align="center">
			<tr>
				<td height="40" align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Areas</u></span></td>
			</tr>
			<tr>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Dependencia:</strong></td>
				<td width="14%"><label><span class="Estilo7">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="0">--> Todas <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
										} else {
											$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
											//$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector='.$_SESSION['SEDE_USUARIO'].';'; 
										}
										//---------------
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
											$consulta_x = 'SELECT * FROM z_jefes_detalle WHERE id_sector=0' . $_POST['OSEDE'] . ';';
											$tabla_x = mysql_query($consulta_x);
										} else {
											$consulta_x = 'SELECT * FROM z_jefes_detalle WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
											$tabla_x = mysql_query($consulta_x);
										}

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
											$consulta_x = 'SELECT * FROM bn_areas WHERE division=0' . $_POST['ODIVISION'] . ' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
										} else {
											$consulta_x = 'SELECT * FROM bn_areas WHERE division=0' . $_POST['ODIVISION'] . ' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
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
			</tr>
		</table>

		<p><a name="vista"></a>

		<table width="35%" border="1" align="center">
			<tr>
				<td height="30" align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Orden del Listado</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong><input name="OOPCION1" type="radio" onClick="this.form.submit()" value="status" <?php if ($_POST['OOPCION1'] == 'status') {
																																	echo 'checked="checked" ';
																																} ?>>
						Status </strong></td>
				<td bgcolor="#CCCCCC"><strong><input name="OOPCION1" type="radio" onClick="this.form.submit()" value="area" <?php if ($_POST['OOPCION1'] == 'area') {
																																echo 'checked="checked" ';
																															} ?>>
						Area </strong></td>
				<td bgcolor="#CCCCCC"><strong><input name="OOPCION1" type="radio" onClick="this.form.submit()" value="observacion" <?php if ($_POST['OOPCION1'] == 'observacion') {
																																		echo 'checked="checked" ';
																																	} ?>>
						Observaci&oacute;n</strong></td>

			</tr>
		</table>
		</p>
		<table class="formateada" width="70%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="10" align="center">
						<p class="Estilo7"><u>Equipos en Soporte Tecnico </u></p>
					</td>
				</tr>
				<tr>
					<th width="54">
						<div align="center" class="Estilo8"><strong>Item</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Numero Bien</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Observacion</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Status</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Area</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Dependencia</strong></div>
					</th>
				</tr>
				<?php
				$filtro = '1=1';
				if ($sede > 0) {
					$filtro = $filtro . ' AND id_sector=' . $sede;
				} else {
					$filtro = $filtro . ' AND id_sector';
				}
				if ($division > 0) {
					$filtro = $filtro . ' AND id_division=' . $division;
				} else {
					$filtro = $filtro . ' AND id_division';
				}
				if ($area > 0) {
					$filtro = $filtro . ' AND id_area=' . $area;
				} else {
					$filtro = $filtro . ' AND id_area';
				}

				$orden = '';
				if ($_POST['OOPCION1'] == 'status') {
					$orden = ' ORDER BY descripcion ';
				}
				if ($_POST['OOPCION1'] == 'area') {
					$orden = ' ORDER BY sector, area ';
				}
				if ($_POST['OOPCION1'] == 'observacion') {
					$orden = ' ORDER BY observacion ';
				}

				//----------------------- MONTAJE DE LOS DATOS

				$consulta = "SELECT * FROM vista_inf_bienes_soporte_tecnico WHERE $filtro AND id_status<5 AND borrado=0 $orden";
				$tabla = mysql_query($consulta);

				$i = 0;
				while ($registro = mysql_fetch_object($tabla)) {
					$i++;
				?>
					<tr id="fila<?php echo $registro->id_bien; ?>">
						<td>
							<div align="center" class="Estilo8"><?php echo $i; ?></div>
						</td>
						<td>
							<div align="center" class="Estilo8"><?php echo ($registro->numero_bien); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo palabras($registro->descripcion_bien); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo ($registro->observacion); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo ($registro->descripcion); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo palabras($registro->area); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo palabras($registro->division); ?></div>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		</p>
		</p>
		<p></p>
		<table class="formateada" width="70%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="10" align="center">
						<p class="Estilo7"><u>Equipos Entregados </u></p>
					</td>
				</tr>
				<tr>
					<th width="54">
						<div align="center" class="Estilo8"><strong>Item</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Numero Bien</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Observacion</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Status</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Area</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Dependencia</strong></div>
					</th>
				</tr>
				<?php
				$filtro = '1=1';
				if ($sede > 0) {
					$filtro = $filtro . ' AND id_sector=' . $sede;
				} else {
					$filtro = $filtro . ' AND id_sector';
				}
				if ($division > 0) {
					$filtro = $filtro . ' AND id_division=' . $division;
				} else {
					$filtro = $filtro . ' AND id_division';
				}
				if ($area > 0) {
					$filtro = $filtro . ' AND id_area=' . $area;
				} else {
					$filtro = $filtro . ' AND id_area';
				}

				$orden = '';
				if ($_POST['OOPCION1'] == 'status') {
					$orden = ' ORDER BY descripcion ';
				}
				if ($_POST['OOPCION1'] == 'area') {
					$orden = ' ORDER BY division, area ';
				}
				if ($_POST['OOPCION1'] == 'observacion') {
					$orden = ' ORDER BY observacion ';
				}

				//----------------------- MONTAJE DE LOS DATOS

				$consulta = "SELECT * FROM vista_inf_bienes_soporte_tecnico WHERE $filtro AND borrado=0 AND id_status=5 $orden";
				$tabla = mysql_query($consulta);

				$i = 0;
				while ($registro = mysql_fetch_object($tabla)) {
					$i++;
				?>
					<tr id="fila<?php echo $registro->id_bien; ?>">
						<td>
							<div align="center" class="Estilo8"><?php echo $i; ?></div>
						</td>
						<td>
							<div align="center" class="Estilo8"><?php echo ($registro->numero_bien); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo palabras($registro->descripcion_bien); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo ($registro->observacion); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo ($registro->descripcion); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo palabras($registro->area); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo8"><?php echo palabras($registro->division); ?></div>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<p>&nbsp;</p>
	</form>
	<p>
		<?php include "../pie.php"; ?>

	</p>
	<p>&nbsp;</p>
</body>