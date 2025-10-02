<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 145;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<title>Gesti&oacute;n Usuarios</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=Edge" />
</head>

<body style="background: transparent !important;">
	<?php
	if ($_POST['CMDGUARDAR'] == 'Guardar Cambios') {
		if ($_POST['OSEDE'] > 0) {
			$filtro = 'sector = ' . $_POST['OSEDE'];
		} else {
			$filtro = '1=1';
		}
		if ($_POST['ODIVISION'] > 0) {
			$filtro = $filtro . ' AND division = ' . $_POST['ODIVISION'];
		}
		//--------------------
		$consulta_x = "SELECT * FROM z_empleados WHERE " . $filtro . ";"; // echo $consulta_x;
		$tabla_x = mysql_query($consulta_x);
		while ($registro_x = mysql_fetch_object($tabla_x))
		//-------------
		{
			if ($_POST['ON' . $registro_x->cedula] <> '') {
				//------ PARA MODIFICAR EL FUNCIONARIO
				$consulta = "UPDATE z_empleados SET area = '" . $_POST['area' . $registro_x->cedula] . "', Nombres = '" . $_POST['ON' . $registro_x->cedula] . "', Apellidos = '" . $_POST['OA' . $registro_x->cedula] . "', sector = '" . $_POST['OS' . $registro_x->cedula] . "', division = '" . $_POST['OD' . $registro_x->cedula] . "', Cargo = '" . $_POST['OC' . $registro_x->cedula] . "', correo = '" . $_POST['OE' . $registro_x->cedula] . "', clave = '" . $_POST['OP' . $registro_x->cedula] . "' WHERE cedula='" . $registro_x->cedula . "';";
				//echo $consulta;
				$tabla_datos = mysql_query($consulta);
			}
		}
	}
	?> <p>
		<?php include "../titulo.php"; ?>

	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post" action="#vista">
		<div class="mt-4">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<div class="card border-danger mb-3">
						<div class="card-header bg-danger text-white text-center">
							<u>Opciones para Filtrar</u>
						</div>
						<div class="card-body">
							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label"><strong>Dependencia:</strong></label>
								<div class="col-sm-8">
									<select class="form-select" name="OSEDE" size="1" onChange="this.form.submit()">
										<option value="-1">Todas</option>
										<?php
										if ($_SESSION['SEDE_USUARIO'] == 1) {
											$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5;';
										} else {
											$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
										}
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
								<label class="col-sm-4 col-form-label"><strong>Unidad Administrativa:</strong></label>
								<div class="col-sm-8">
									<select class="form-select" name="ODIVISION" onChange="this.form.submit()" size="1">
										<option value="0">Todas</option>
										<?php
										$consulta_x = "SELECT division, descripcion FROM z_jefes_detalle WHERE division<>17 AND id_sector = 0" . $_POST['OSEDE'] . " order by descripcion;";
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_object($tabla_x)) {
											echo '<option';
											if ($_POST['ODIVISION'] == $registro_x->division) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x->division;
											echo '">';
											echo $registro_x->descripcion;
											echo '</option>';
										}
										?>
									</select>
								</div>
							</div>
							<div class="text-center">
								<input type="submit" class="btn btn-danger" name="CMDGUARDAR" value="Guardar Cambios">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<p><a name="vista"></a>
		</p>
		<table border="1" class="datatabla formateada">
			<thead>
				<!-- <tr>
						<td height="35" align="center" class="TituloTabla" colspan="8"><u>Listado de Funcionarios</u></td>
					</tr> -->
				<tr align="center" class="TituloCampo">
					<th>C.I.</th>
					<th>Nombres</th>
					<th>Apellidos</th>
					<th>Sector</th>
					<th>Divisi&oacute;n</th>
					<th>Area (Para uso de Almacen y Bienes Nacionales)</th>
					<th>Cargo</th>
					<th>Correo</th>
					<th>Contrase&ntilde;a</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ($_POST['OSEDE'] > 0) {
					$filtro = 'sector = ' . $_POST['OSEDE'];
				} else {
					$filtro = '1=1';
				}
				if ($_POST['ODIVISION'] >= 0) {
					$filtro = $filtro . ' AND division = ' . $_POST['ODIVISION'];
					//--------------------
					$consulta_x = "SELECT * FROM z_empleados WHERE " . $filtro . " order by Nombres;";
					$tabla_x = mysql_query($consulta_x);
					while ($registro_x = mysql_fetch_object($tabla_x))
					//-------------
					{
				?>
						<tr id="fila<?php echo $registro_x->cedula; ?>">
							<td align="center"><label><input type="text" name="ON<?php echo $registro_x->cedula; ?>" size="10" value="<?php echo $registro_x->cedula; ?>"></label></td>
							<td align="center"><label><input type="text" name="ON<?php echo $registro_x->cedula; ?>" size="20" value="<?php echo $registro_x->Nombres; ?>"></label></td>
							<td align="center"><label><input type="text" name="OA<?php echo $registro_x->cedula; ?>" size="20" value="<?php echo $registro_x->Apellidos; ?>"></label></td>
							<td align="center"><label>
									<select name="OS<?php echo $registro_x->cedula; ?>" size="1">
										<option value="-1">--> Todas <--< /option>
												<?php
												$consulta_xx = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5;';
												$tabla_xx = mysql_query($consulta_xx);
												while ($registro_xx = mysql_fetch_array($tabla_xx)) {
													echo '<option ';
													if ($registro_x->sector == $registro_xx['id_sector']) {
														echo 'selected="selected" ';
													}
													echo ' value=' . $registro_xx['id_sector'] . '>' . $registro_xx['nombre'] . '</option>';
												}
												?>
									</select>
								</label></td>
							<td align="center"><label>
									<select name="OD<?php echo $registro_x->cedula; ?>" onChange="" size="1">
										<option value="0">Seleccione</option>
										<?php
										//--------------------
										$consulta_xx = "SELECT division, descripcion FROM z_jefes_detalle WHERE division<>17 AND id_sector = 0" . $_POST['OSEDE'] . " order by descripcion;";
										$tabla_xx = mysql_query($consulta_xx);
										while ($registro_xx = mysql_fetch_object($tabla_xx))
										//-------------
										{
											echo '<option';
											if ($registro_xx->division == $registro_x->division) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_xx->division;
											echo '">';
											echo $registro_xx->descripcion;
											echo '</option>';
										}
										?>
									</select>
								</label></td>
							<td align="center"><label>
									<select name="area<?php echo $registro_x->cedula; ?>" onChange="" size="1">
										<option value="0">Seleccione</option>
										<?php
										//--------------------
										$consulta_xx = "SELECT * FROM bn_areas WHERE division= 0" . $registro_x->division . " order by descripcion;"; //echo $consulta_xx ;
										$tabla_xx = mysql_query($consulta_xx);
										while ($registro_xx = mysql_fetch_object($tabla_xx))
										//-------------
										{
											echo '<option';
											if ($registro_xx->id_area == $registro_x->area) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_xx->id_area;
											echo '">';
											echo $registro_xx->descripcion;
											echo '</option>';
										}
										?>
									</select>
								</label></td>
							<td align="center"><label><input type="text" name="OC<?php echo $registro_x->cedula; ?>" size="15" value="<?php echo $registro_x->Cargo; ?>"></label></td>
							<td align="center"><label><input type="text" name="OE<?php echo $registro_x->cedula; ?>" size="20" value="<?php echo $registro_x->correo; ?>"></label></td>
							<td align="center"><label><input type=password name="OP<?php echo $registro_x->cedula; ?>" size="20" value="<?php echo $registro_x->clave; ?>"></label></td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>
		<p>&nbsp;</p>
		</div>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>