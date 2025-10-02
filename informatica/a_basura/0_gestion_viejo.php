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
	<link rel="stylesheet" type="text/css" href="../../accesos/estilos/estilos.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<?php
	if ($_POST['OMODULO'] <> '' and $_POST['OFUNCIONARIO'] > 0 and $_POST['OFUNCIONARIO'] == $_POST['OFUNCIONARIO_VIEJO']) {
		//------ PARA MODIFICAR EL FUNCIONARIO
		$consulta = "UPDATE z_empleados SET division = " . $_POST['ODIVISION'] . ", id_origen3 = " . $_POST['OORIGEN3'] . ", id_origen2 = " . $_POST['OORIGEN2'] . ", id_origen = " . $_POST['OORIGEN'] . ", rol = '" . $_POST['OROL'] . "', rol2 = '" . $_POST['OROL2'] . "', rol3 = '" . $_POST['OROL3'] . "' WHERE cedula='" . $_POST['OFUNCIONARIO'] . "';"; //echo $consulta;
		$tabla_datos = mysql_query($consulta);
		//------ PARA AGREGAR LOS ACCESOS
		$consulta = "SELECT acceso FROM z_accesos_tipo WHERE modulo='" . $_POST['OMODULO'] . "';";
		$tabla_datos = mysql_query($consulta);
		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			//--- PARA AHORRAR TIEMPO
			if ($_POST['OTODAS'] == 'TODAS') {
				$_POST['O' . $registro_datos->acceso] = 'SI';
			}
			if ($_POST['OTODAS'] == 'NINGUNA') {
				$_POST['O' . $registro_datos->acceso] = 'NO';
			}
			//--------------
			if ($_POST['O' . $registro_datos->acceso] == 'SI') {
				// ELIMINAR EL ACCESO
				$consulta_x = "DELETE FROM z_empleados_accesos WHERE cedula = " . $_POST['OFUNCIONARIO'] . " AND acceso = " . $registro_datos->acceso . ";";
				$tabla_x = mysql_query($consulta_x);
				// AGREGAR EL ACCESO
				$consulta_x = "INSERT INTO z_empleados_accesos (cedula, acceso) VALUES (" . $_POST['OFUNCIONARIO'] . ", " . $registro_datos->acceso . ");";
				$tabla_x = mysql_query($consulta_x);
			}
			//--------------
			if ($_POST['O' . $registro_datos->acceso] == 'NO') {
				// ELIMINAR EL ACCESO
				$consulta_x = "DELETE FROM z_empleados_accesos WHERE cedula = " . $_POST['OFUNCIONARIO'] . " AND acceso = " . $registro_datos->acceso . ";";
				$tabla_x = mysql_query($consulta_x);
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
			<table width="55%" border="1" align="center">
				<tr>
					<td height="35" align="center" colspan="6" bgcolor="#FF0000"><span class="Estilo7"><u>Opciones para Filtrar</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Dependencia:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">--> Seleccione <--< /option>
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
							</span></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Modulo:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<select name="OMODULO" onChange="this.form.submit()" size="1">
								<option value="">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT modulo FROM z_accesos_tipo GROUP BY modulo;";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OMODULO'] == $registro_x->modulo) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->modulo;
									echo '">';
									echo $registro_x->modulo;
									echo '</option>';
								}
								?>
							</select>
						</div>
					</td>
				</tr>
			</table>
			<table width="55%" border=1 align=center>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Funcionario:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="left">
							<?php
							if ($_POST['OFUNCIONARIO'] <> $_POST['OFUNCIONARIO_VIEJO']) {
								$_POST['OORIGEN'] = 0;
								$_POST['ODIVISION'] = 0;
								$_POST['OROL'] = 0;
								$_POST['OROL2'] = 0;
							}
							?>
							<input type="hidden" name="OFUNCIONARIO_VIEJO" value="<?php echo $_POST['OFUNCIONARIO']; ?>">
							<select name="OFUNCIONARIO" onChange="this.form.submit()" size="1">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT cedula, Nombres, Apellidos, division FROM z_empleados WHERE sector = 0" . $_POST['OSEDE'] . " and cedula>1000000 ORDER BY Nombres;";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
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
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Division:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="left">
							<select name="ODIVISION" onChange="this.form.submit()" size="1">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT division, descripcion FROM z_jefes_detalle WHERE id_sector = 0" . $_POST['OSEDE'] . " order by descripcion;";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
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
					</td>
				</tr>
			</table>
			<table width="55%" border=1 align=center>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Origen:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<?php
							list($funcionario, $rol, $origen, $rol2, $origen2, $rol3, $origen3) = funcion_funcionario(0 + $_POST['OFUNCIONARIO']);
							$_POST['OROL'] = $rol;
							$_POST['OROL2'] = $rol2;
							$_POST['OROL3'] = $rol3;
							$_POST['OORIGEN'] = $origen;
							$_POST['OORIGEN2'] = $origen2;
							$_POST['OORIGEN3'] = $origen3;
							?>
							<select name="OORIGEN" onChange="this.form.submit()" size="1">
								<option value="0">->-> Todos <-<-< /option>
										<?php
										//--------------------
										$consulta_x = "SELECT Codigo, Abreviatura FROM a_origen_liquidacion WHERE Codigo IN (1,2,3,4,9,12,13,16) ORDER BY Codigo;";
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_object($tabla_x))
										//-------------
										{
											echo '<option';
											if ($_POST['OORIGEN'] == $registro_x->Codigo) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x->Codigo;
											echo '">';
											echo $registro_x->Codigo . ' - ' . $registro_x->Abreviatura;
											echo '</option>';
										}
										?>
							</select>
						</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Origen 2:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<select name="OORIGEN2" onChange="this.form.submit()" size="1">
								<option value="0">->-> Todos <-<-< /option>
										<?php
										//--------------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_object($tabla_x))
										//-------------
										{
											echo '<option';
											if ($_POST['OORIGEN2'] == $registro_x->Codigo) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x->Codigo;
											echo '">';
											echo $registro_x->Codigo . ' - ' . $registro_x->Abreviatura;
											echo '</option>';
										}
										?>
							</select>
						</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Origen 3:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<select name="OORIGEN3" onChange="this.form.submit()" size="1">
								<option value="0">->-> Todos <-<-< /option>
										<?php
										//--------------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_object($tabla_x))
										//-------------
										{
											echo '<option';
											if ($_POST['OORIGEN3'] == $registro_x->Codigo) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x->Codigo;
											echo '">';
											echo $registro_x->Codigo . ' - ' . $registro_x->Abreviatura;
											echo '</option>';
										}
										?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Rol:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<div align="center">
								<select name="OROL" onChange="this.form.submit()" size="1">
									<option value="0">->-> Ninguno <-<-< /option>
											<?php
											//--------------------
											$consulta_x = "SELECT Abreviatura, Descripcion FROM a_roles;";
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_object($tabla_x))
											//-------------
											{
												echo '<option';
												if ($_POST['OROL'] == $registro_x->Abreviatura) {
													echo ' selected="selected" ';
												}
												echo ' value="';
												echo $registro_x->Abreviatura;
												echo '">';
												echo $registro_x->Descripcion;
												echo '</option>';
											}
											?>
								</select>
							</div>
						</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Rol 2:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<div align="center">
								<select name="OROL2" onChange="this.form.submit()" size="1">
									<option value="0">->-> Ninguno <-<-< /option>
											<?php
											//--------------------
											$consulta_x = "SELECT Abreviatura, Descripcion FROM a_roles;";
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_object($tabla_x))
											//-------------
											{
												echo '<option';
												if ($_POST['OROL2'] == $registro_x->Abreviatura) {
													echo ' selected="selected" ';
												}
												echo ' value="';
												echo $registro_x->Abreviatura;
												echo '">';
												echo $registro_x->Descripcion;
												echo '</option>';
											}
											?>
								</select>
							</div>
						</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Rol 3:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<div align="center">
								<select name="OROL3" onChange="this.form.submit()" size="1">
									<option value="0">->-> Ninguno <-<-< /option>
											<?php
											//--------------------
											$consulta_x = "SELECT Abreviatura, Descripcion FROM a_roles;";
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_object($tabla_x))
											//-------------
											{
												echo '<option';
												if ($_POST['OROL3'] == $registro_x->Abreviatura) {
													echo ' selected="selected" ';
												}
												echo ' value="';
												echo $registro_x->Abreviatura;
												echo '">';
												echo $registro_x->Descripcion;
												echo '</option>';
											}
											?>
								</select>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<p>&nbsp;</p>
			<table width="40%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="2" align="center">
							<p class="Estilo7"><u>Opciones del Modulo Seleccionado</u></p>
						</td>
						<td bgcolor="#FF0000" height="27" colspan="1" align="center">
							<p class="Estilo7"><span class="Estilo8">
									<select style="font-size:16px" name="OTODAS" onChange="this.form.submit()">
										<option selected="selected" value="-1">********</option>
										<option value="TODAS">TODAS</option>
										<option value="NINGUNA">NINGUNA</option>
									</select>
								</span></p>
						</td>

					</tr>
					<tr>
						<td bgcolor="#CCCCCC" height="27">
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Opcion</strong></div>
						</td>
					</tr>
					<?php
					if ($_POST['OMODULO'] <> '' and $_POST['OFUNCIONARIO'] > 0) {
						$i = 0;
						//------
						$consulta = "SELECT descripcion, acceso FROM z_accesos_tipo WHERE modulo='" . $_POST['OMODULO'] . "' ORDER BY descripcion;";
						$tabla_datos = mysql_query($consulta);
						while ($registro_datos = mysql_fetch_object($tabla_datos)) {
							$i++;
							//--------
					?>
							<tr>
								<td bgcolor="#FFFFFF" height=27>
									<div align="center" class="Estilo8 Estilo19"><?php echo $i; ?></div>
								</td>
								<td bgcolor="#FFFFFF">
									<div class="Estilo8 Estilo19"><?php echo $registro_datos->descripcion; ?></div>
								</td>
								<td bgcolor="#FFFFFF">
									<div align="center" class="Estilo8">
										<select style="font-size:16px" name="O<?php echo $registro_datos->acceso; ?>" onChange="this.form.submit()">
											<?php
											//----------- PARA VALIDAR SI TIENE ACCESO A LA OPCION
											$consulta_x = "SELECT acceso FROM z_empleados_accesos WHERE cedula= 0" . $_POST['OFUNCIONARIO'] . " AND acceso = " . $registro_datos->acceso . ";";
											$tabla_x = mysql_query($consulta_x);
											if ($registro_x = mysql_fetch_object($tabla_x))
											//-------------
											{
												echo '<option selected="selected" value="SI">SI</option>';
												echo '<option value="NO">NO</option>';
											} else {
												echo '<option value="SI">SI</option>';
												echo '<option selected="selected" value="NO">NO</option>';
											}
											?>
										</select>
									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>
			<p> </p>
			<p></p>
			<table class="formateada" width="40%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="39" colspan="4" align="center">
							<p class="Estilo7"><u>Permisos Asignados en el Sistema</u></p>
						</td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC" height="27">
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Modulo</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
						</td>
					</tr>
					<?php
					if ($_POST['OFUNCIONARIO'] > 0) {
						$i = 0;
						//------
						$consulta = "SELECT modulo, descripcion, z_accesos_tipo.acceso FROM z_accesos_tipo, z_empleados_accesos WHERE cedula=" . $_POST['OFUNCIONARIO'] . " and z_accesos_tipo.acceso=z_empleados_accesos.acceso ORDER BY modulo, descripcion;";
						$tabla_datos = mysql_query($consulta);
						while ($registro_datos = mysql_fetch_object($tabla_datos)) {
							$i++;
							//--------
					?>
							<tr id="fila<?php echo $i; ?>">
								<td height=27>
									<div align="center" class="Estilo8 Estilo19"><?php echo $i; ?></div>
								</td>
								<td>
									<div class="Estilo8 Estilo19"><?php echo $registro_datos->modulo; ?></div>
								</td>
								<td>
									<div class="Estilo8 Estilo19"><?php echo $registro_datos->descripcion; ?></div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>