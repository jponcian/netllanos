<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 144;
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
		$consulta = "SELECT Codigo FROM a_origen_liquidacion ORDER BY Codigo;";
		$tabla_datos = mysql_query($consulta);
		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			//--- PARA AHORRAR TIEMPO
			if ($_POST['OTODAS'] == 'TODAS') {
				$_POST['O' . $registro_datos->Codigo] = 'SI';
			}
			if ($_POST['OTODAS'] == 'NINGUNA') {
				$_POST['O' . $registro_datos->Codigo] = 'NO';
			}
			//--------------
			if ($_POST['O' . $registro_datos->Codigo] == 'SI') {
				// ELIMINAR EL ORIGEN
				$consulta_x = "DELETE FROM z_empleados_origenes WHERE cedula = " . $_POST['OFUNCIONARIO'] . " AND origen = " . $registro_datos->Codigo . ";";
				$tabla_x = mysql_query($consulta_x);
				// AGREGAR EL ORIGEN
				$consulta_x = "INSERT INTO z_empleados_origenes (cedula, origen) VALUES (" . $_POST['OFUNCIONARIO'] . ", " . $registro_datos->Codigo . ");";
				$tabla_x = mysql_query($consulta_x);
			}
			//--------------
			if ($_POST['O' . $registro_datos->Codigo] == 'NO') {
				// ELIMINAR EL ORIGEN
				$consulta_x = "DELETE FROM z_empleados_origenes WHERE cedula = " . $_POST['OFUNCIONARIO'] . " AND origen = " . $registro_datos->Codigo . ";";
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
			<table width="40%" border="1" align="center">
				<tr>
					<td height="35" align="center" colspan="6" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Funcionario </u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>Dependencia:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="left"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione< /option>
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

				</tr>
				<tr>
					<td bgcolor="#CCCCCC" colspan="1">
						<div align="left"><strong>Funcionario:</strong></div>
					</td>
					<td bgcolor="#FFFFFF" colspan="3">
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
						<div align="left"><strong>Unidad Administrativa:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="left">

							<input type="hidden" name="ODIVISION" value="<?php echo $_POST['ODIVISION']; ?>">

							<?php
							//--------------------
							$consulta_x = "SELECT division, descripcion FROM z_jefes_detalle WHERE division = 0" . $_POST['ODIVISION'] . " order by descripcion;";
							$tabla_x = mysql_query($consulta_x);
							$registro_x = mysql_fetch_object($tabla_x);
							//-------------
							echo $registro_x->descripcion;
							?>
						</div>
					</td>
				</tr>
			</table>
			<p></p>
			<table width="35%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="2" align="center">
							<p class="Estilo7"><u>Opciones del Origen Seleccionado</u></p>
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
					if ($_POST['OFUNCIONARIO'] > 0) {
						$i = 0;
						//------
						$consulta = "SELECT Codigo, Descripcion FROM a_origen_liquidacion WHERE Codigo>0 ORDER BY Codigo;";
						$tabla_datos = mysql_query($consulta);
						while ($registro_datos = mysql_fetch_object($tabla_datos)) {
							$i++;
							//--------
					?>
							<tr>
								<td bgcolor="#FFFFFF" height=27>
									<div align="center" class="Estilo8 Estilo19"><?php echo $registro_datos->Codigo; ?></div>
								</td>
								<td bgcolor="#FFFFFF">
									<div class="Estilo8 Estilo19"><?php echo $registro_datos->Descripcion; ?></div>
								</td>
								<td bgcolor="#FFFFFF">
									<div align="center" class="Estilo8">
										<select style="font-size:16px" name="O<?php echo $registro_datos->Codigo; ?>" onChange="this.form.submit()">
											<?php
											//----------- PARA VALIDAR SI TIENE ACCESO A LA OPCION
											$consulta_x = "SELECT origen FROM z_empleados_origenes WHERE cedula= 0" . $_POST['OFUNCIONARIO'] . " AND origen = " . $registro_datos->Codigo . ";";
											$tabla_x = mysql_query($consulta_x);
											echo $consulta_x;
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
		</div>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>