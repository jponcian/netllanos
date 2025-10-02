<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 143;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<title>Gesti&oacute;n Accesos</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<?php
	if ($_POST['OFUNCIONARIO'] > 0 and $_POST['OFUNCIONARIO'] == $_POST['OFUNCIONARIO_VIEJO']) {
		//------ PARA AGREGAR LOS ACCESOS
		$consulta = "SELECT * FROM z_accesos_roles;";
		$tabla_datos = mysql_query($consulta);
		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			if ($_POST['O' . $registro_datos->id] == 'SI') {
				$consulta = "DELETE FROM z_empleados_roles WHERE cedula='" . $_POST['OFUNCIONARIO'] . "' AND rol=" . $registro_datos->id . ";";
				$tabla = mysql_query($consulta);
				$consulta = "INSERT INTO z_empleados_roles (cedula, rol) VALUES ('" . $_POST['OFUNCIONARIO'] . "', " . $registro_datos->id . ");";
				$tabla = mysql_query($consulta);
			} else {
				$consulta = "DELETE FROM z_empleados_roles WHERE cedula='" . $_POST['OFUNCIONARIO'] . "' AND rol=" . $registro_datos->id . ";";
				$tabla = mysql_query($consulta);
			}
		}
	}
	?>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post" action="#vista">
		<div align="center">
			<p><a name="vista"></a></p>
			<div class="card" style="max-width: 600px; margin: auto;">
				<div class="card-header text-white bg-danger text-center">
					<h5 class="mb-0">Datos del Funcionario</h5>
				</div>
				<div class="card-body">
					<div class="mb-3 row">
						<label for="osede" class="col-sm-4 col-form-label"><strong>Dependencia:</strong></label>
						<div class="col-sm-8">
							<select name="OSEDE" id="osede" class="form-select" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['SEDE_USUARIO'] == 1) {
									$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5;';
								} else {
									$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
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
							</select>
						</div>
					</div>
					<div class="mb-3 row">
						<label for="ofuncionario" class="col-sm-4 col-form-label"><strong>Funcionario:</strong></label>
						<div class="col-sm-8">
							<?php
							if ($_POST['OFUNCIONARIO'] <> $_POST['OFUNCIONARIO_VIEJO']) {
								$_POST['OORIGEN'] = 0;
								$_POST['ODIVISION'] = 0;
								$_POST['OROL'] = 0;
								$_POST['OROL2'] = 0;
							}
							?>
							<input type="hidden" name="OFUNCIONARIO_VIEJO" value="<?php echo $_POST['OFUNCIONARIO']; ?>">
							<select name="OFUNCIONARIO" id="ofuncionario" class="form-select" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT cedula, Nombres, Apellidos, division FROM z_empleados WHERE sector = 0" . $_POST['OSEDE'] . " and cedula>1000000 ORDER BY Nombres;";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									echo '<option';
									if ($_POST['OFUNCIONARIO'] == $registro_x->cedula) {
										echo ' selected="selected" ';
										$_POST['ODIVISION'] = $registro_x->division;
									}
									echo ' value="';
									echo $registro_x->cedula;
									echo '">';
									echo $registro_x->Nombres . " " . $registro_x->Apellidos;
									echo '</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label"><strong>Unidad Administrativa:</strong></label>
						<div class="col-sm-8">
							<input type="hidden" name="ODIVISION" value="<?php echo $_POST['ODIVISION']; ?>">
							<p class="form-control-plaintext">
								<?php
								//--------------------
								$consulta_x = "SELECT division, descripcion FROM z_jefes_detalle WHERE division = 0" . $_POST['ODIVISION'] . " order by descripcion;";
								$tabla_x = mysql_query($consulta_x);
								$registro_x = mysql_fetch_object($tabla_x);
								//-------------
								echo $registro_x ? htmlspecialchars($registro_x->descripcion) : 'N/A';
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
			<p></p>

			<table width="35%" border=1 align=center>
				<tr>
					<td height="35" align="center" colspan="4" bgcolor="#FF0000"><span class="Estilo7"><u>Roles en el Sistema</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" height="27">
						<div align="center" class="Estilo8"><strong>Id</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Modulo</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Rol</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Opcion</strong></div>
					</td>
				</tr>
				<?php
				if ($_POST['OFUNCIONARIO'] > 0) {
					$consulta = "SELECT * FROM z_accesos_roles ORDER BY modulo, rol;";
					$tabla_datos = mysql_query($consulta);
					while ($registro_datos = mysql_fetch_object($tabla_datos)) {
						$i++;
						//--------
				?>
						<tr>
							<td bgcolor="#FFFFFF" height=27>
								<div class="Estilo8 Estilo19"><?php echo $i; ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div class="Estilo8 Estilo19"><?php echo $registro_datos->modulo; ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div class="Estilo8 Estilo19"><?php echo $registro_datos->rol; ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8">
									<?php
									$consulta_x = "SELECT rol FROM z_empleados_roles WHERE cedula=" . $_POST['OFUNCIONARIO'] . " AND rol = " . $registro_datos->id . ";";
									$tabla_x = mysql_query($consulta_x);
									$tiene_acceso = mysql_fetch_object($tabla_x) ? true : false;
									$checkbox_id = "toggle-switch-" . $registro_datos->id;
									?>
									<div class="toggle-switch">
										<input type="hidden" name="O<?php echo $registro_datos->id; ?>" value="NO">
										<input
											type="checkbox"
											id="<?php echo $checkbox_id; ?>"
											class="toggle-switch-checkbox"
											name="O<?php echo $registro_datos->id; ?>"
											value="SI"
											onchange="this.form.submit()"
											<?php if ($tiene_acceso) echo 'checked'; ?>>
										<label for="<?php echo $checkbox_id; ?>" class="toggle-switch-label">
											<span class="toggle-switch-inner"></span>
											<span class="toggle-switch-switch"></span>
										</label>
									</div>
								</div>
							</td>
						</tr>
				<?php
					}
				}
				?>
			</table>

			<p></p>
		</div>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>