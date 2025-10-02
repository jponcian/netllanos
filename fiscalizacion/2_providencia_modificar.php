<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 1;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['VARIABLE'] = 'NO MODIFICADA';

if ($_POST['CMDGUARDAR'] == "Guardar") {
	if ($_POST['OTEXTO1'] <> "" and $_POST['ORIF'] <> "" and $_POST['OTIPO'] > 0 and $_POST['OSUPERVISOR'] > 1000000 and $_POST['OFISCAL'] > 1000000) {
		// GUARDADO DE LOS DATOS
		$consulta = "UPDATE expedientes_fiscalizacion SET programa=" . $_POST['OPROGRAMA'] . ", rif='" . $_POST['ORIF'] . "', tipo=" . $_POST['OTIPO'] . ", ci_supervisor=" . $_POST['OSUPERVISOR'] . ", ci_fiscal1=" . $_POST['OFISCAL'] . ", texto1='" . $_POST['OTEXTO1'] . "', texto2='" . $_POST['OTEXTO2'] . "', texto3='" . $_POST['OTEXTO3'] . "' WHERE anno=" . $_POST['OANNO'] . " AND numero=" . $_POST['ONUMERO'] . " AND sector=" . $_POST['OSEDE'] . ";";
		$tabla = mysql_query($consulta);
		// FIN
		$_POST['OSEDE'] = '-1';
		$_POST['OANNO'] = '-1';
		$_POST['ONUMERO'] = '-1';
		$_POST['ORIF'] = '';
		$_POST['OTIPO'] = '';
		$_POST['OPROGRAMA'] = '';
		$_POST['OSUPERVISOR'] = '';
		$_POST['OFISCAL'] = '';
		$_POST['OTEXTO1'] = '';
		$_POST['OTEXTO2'] = '';
		$_POST['OTEXTO3'] = '';
		$_POST['OFECHA'] = '';
		//--------------
		$_SESSION['VARIABLE'] = 'MODIFICADA';
		echo "<script type=\"text/javascript\">alert('Providencia Modificada Exitosamente!!!');</script>";
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

//------ BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0 and $_POST['ONUMERO_ANTERIOR'] <> $_POST['ONUMERO']) {
	$consulta_x = 'SELECT programa, rif, tipo, ci_supervisor, ci_fiscal1, texto1, texto2, texto3, fecha_emision, status FROM expedientes_fiscalizacion WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_array($tabla_x);
	//---------
	if ($registro_x['status'] >= 0 and $registro_x['status'] <= 2) {
		//---------
		$_POST['ORIF'] = $registro_x['rif'];
		$_POST['OTIPO'] = $registro_x['tipo'];
		$_POST['OPROGRAMA'] = $registro_x['programa'];
		$_POST['OSUPERVISOR'] = $registro_x['ci_supervisor'];
		$_POST['OFISCAL'] = $registro_x['ci_fiscal1'];
		$_POST['OTEXTO1'] = $registro_x['texto1'];
		$_POST['OTEXTO2'] = $registro_x['texto2'];
		$_POST['OTEXTO3'] = $registro_x['texto3'];
		$_POST['OFECHA'] = voltea_fecha($registro_x['fecha_emision']);
		//---------
	} else {
		$_POST['ONUMERO'] = -1;
		echo "<script type=\"text/javascript\">alert('���La Providencia ya fue Notificada!!!');</script>";
	}
}
?>
<html>

<head>
	<title>Modificar Providencia</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
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
	<form name="form1" method="post" action="">
		<div align="center">
			<table width="70%" border="1" align="center">
				<tr>
					<td height="35" align="center" colspan="8" class="TituloTabla"><span><u>Opciones para Filtrar</u></span></td>
				</tr>
				<td width="87" height="35" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia</strong></div>
				</td>
				<td width="145" bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT sector, nombre FROM vista_providencias GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT sector, nombre FROM vista_providencias WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									if ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								}
								?>
							</select>
						</span>
					</div>
				</td>
				<td width="98" bgcolor="#CCCCCC"><strong>A&ntilde;o:</strong></td>
				<td width="98"><label>
						<div align="center"><span class="Estilo1">
								<select name="OANNO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										$consulta_x = 'SELECT anno FROM vista_providencias WHERE sector =0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OANNO'] == $registro_x['anno']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
										}
									}
									?>
								</select></span></div>
					</label></td>
				<td width="97" bgcolor="#CCCCCC"><strong>
						Numero:</strong></td>
				<td width="97"><label>
						<div align="center"><span class="Estilo1">
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {

										$consulta_x = 'SELECT numero FROM vista_providencias WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' ORDER BY numero DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['ONUMERO'] == $registro_x['numero']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
										}
									}
									?>
								</select></span></div>
						<input type="hidden" name="ONUMERO_ANTERIOR" value="<?php echo $_POST['ONUMERO']; ?>"></td>
				<td width="97" bgcolor="#CCCCCC"><strong>
						Fecha:</strong></td>
				<td width="56"><label>
						<div align="center"><span class="Estilo15">
								<input type="text" name="OFECHA" value="<?php echo voltea_fecha($_POST['OFECHA']); ?>" readonly></span></div>
					</label></td>
			</table>

			<table width="70%" border="1" align="center">
				<tr>
					<td height="35" colspan="9" align="center" class="TituloTabla"><span><u>Datos de la Providencia</u></span></td>
				</tr>
				<tr>
					<td width="19%" bgcolor="#CCCCCC"><strong>Tipo de Providencia :</strong></td>
					<td colspan="3" width="59%"><select name="OTIPO" size="1" onChange="this.form.submit()">
							<option value="-1">->-> SELECCIONE EL TIPO <-<-< /option>
									<?php
									$texto1 = '';
									$texto2 = '';
									$texto3 = '';
									//--------------------
									$consulta_x = 'SELECT * FROM a_tipo_providencia WHERE tipo IN (2099, 2098, 2000, 2005, 2007, 2008, 2306, 1022, 1112, 1116, 1118, 1120, 1122, 1124, 1125, 1318, 1312, 1321, 1322, 1324, 1327, 1333, 1334, 1335, 1336, 1337, 1338, 1339, 1340, 1341, 1342, 1343, 1344, 1345, 2006, 2106, 2107, 2108, 2010, 2012, 2110, 2112, 2113, 2114, 2115, 2116, 2117, 2118, 2119, 2406, 1801, 1802, 1803, 1804, 1805, 1806, 1809, 1810, 1811, 1812, 1813, 1814, 1815, 1816, 1817, 1818, 1901, 1902, 1903, 1904, 1905, 1906, 1909, 1910, 1911, 1912, 1913, 1914, 1915, 1916, 1917, 1918, 1919);';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x))
									//-------------
									{
										echo '<option';
										if ($_POST['OTIPO'] == $registro_x->tipo) {
											echo ' selected="selected" ';
											$texto1 = $registro_x->texto1;
											$texto2 = $registro_x->texto2;
											$texto3 = $registro_x->texto3;
										}
										echo ' value="';
										echo $registro_x->tipo;
										echo '">';
										echo $registro_x->tipo . " - " . $registro_x->descripcion;
										echo '</option>';
									}
									?>
						</select></td>
				</tr>
				<tr>
					<td width="19%" bgcolor="#CCCCCC"><strong>Tipo de Programa:</strong></td>
					<td colspan="3" width="59%"><select name="OPROGRAMA" size="1">
							<option value="-1">->-> SELECCIONE EL PROGRAMA <-<-< /option>
									<?php
									//--------------------
									$consulta_x = 'SELECT * FROM a_tipo_programa;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x))
									//-------------
									{
										echo '<option';
										if ($_POST['OPROGRAMA'] == $registro_x->id_programa) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->id_programa;
										echo '">';
										echo $registro_x->id_programa . " - " . $registro_x->descripcion;
										echo '</option>';
									}
									?>
						</select></td>
				</tr>
			</table>
			<table width="70%" border="1" align="center">
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td width="28%"><label>
							<input type="text" name="ORIF" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
							<input type="submit" class="boton" name="Submit" value="Buscar"></label></td>
					<td width="21%" bgcolor="#CCCCCC"><strong>
							Contribuyente:</strong></td>
					<td width="36%"><label><span class="Estilo15"><?php
																	if ($_POST['ORIF'] <> "") {
																		// BUSQUEDA DEL CONTRIBUYENTE
																		$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
																		$tabla_x = mysql_query($consulta_x);
																		$registro_x = mysql_fetch_object($tabla_x);
																		// FIN
																		echo $registro_x->contribuyente;
																	}
																	?></span></label></td>
				</tr>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
					<td colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?>
							</span></label></td>
				</tr>
			</table>

			<table width="70%" border="1" align="center">
				<tr>
					<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
					<td><label>
							<select name="OSUPERVISOR" size="1">
								<option value="-1">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Coordinador' and sector=" . $_POST['OSEDE'] . " AND modulo='" . strtoupper($_SESSION['NOMBRE_MODULO']) . "';";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OSUPERVISOR'] == $registro_x->cedula) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->cedula;
									echo '">';
									echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
									echo '</option>';
								}
								?>
							</select>
						</label></td>

				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
					<td><label>
							<select name="OFISCAL" size="1">
								<option value="-1">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Funcionario' and sector=" . $_POST['OSEDE'] . " AND modulo='" . strtoupper($_SESSION['NOMBRE_MODULO']) . "';";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OFISCAL'] == $registro_x->cedula) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->cedula;
									echo '">';
									echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
									echo '</option>';
								}
								?>
							</select>
						</label></td>
				</tr>
			</table>
			<table width="70%" border="1" align="center">
				<tr>
					<td width="20%" bgcolor="#CCCCCC"><strong>Texto 1:</strong></td>
					<td width="80%">
						<input type="text" name="OTEXTO1" size="55" maxlength="255" value="<?php if ($_POST['OTEXTO1'] == '') {
																								echo $texto1;
																							} else {
																								echo $_POST['OTEXTO1'];
																							} ?>">
					</td>
				</tr>
				<tr>
					<td width="20%" bgcolor="#CCCCCC"><strong>Texto 2:</strong></td>
					<td width="80%"><input type="text" name="OTEXTO2" size="55" maxlength="255" value="<?php if ($_POST['OTEXTO2'] == '') {
																											echo $texto2;
																										} else {
																											echo $_POST['OTEXTO2'];
																										} ?>"></td>
				</tr>
				<tr>
					<td width="20%" bgcolor="#CCCCCC"><strong>Texto 3:</strong></td>
					<td width="80%"><input type="text" name="OTEXTO3" size="55" maxlength="255" value="<?php if ($_POST['OTEXTO3'] == '') {
																											echo $texto3;
																										} else {
																											echo $_POST['OTEXTO3'];
																										} ?>"></td>
				</tr>
			</table>


			</p>

			<label>

				<?php echo '<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">';
				?>
			</label>
		</div>
	</form>

	<div align="center">

		<?php
		if ($_SESSION['VARIABLE'] == 'REGISTRADA') {
			// CONSULTA DE LA ULTIMA PROVIDENCIA REGISTRADA
			$consulta_x = "SELECT anno, numero, tipo FROM expedientes_fiscalizacion WHERE sector=" . $_POST['OSEDE'] . " ORDER BY anno DESC , numero DESC;";
			$tabla_x = mysql_query($consulta_x);
			if ($registro_x = mysql_fetch_object($tabla_x)) {
				$_SESSION['ANNO'] = $registro_x->anno;
				$_SESSION['NUMERO'] = $registro_x->numero;
				$_SESSION['FIN'] = $registro_x->numero;
				$_SESSION['SEDE'] = $_POST['OSEDE'];
				//$_SESSION['VARIABLE1'] = $registro_x->tipo;
				//------------------
				echo '<form name="form2" method="post" action="Providencias/formato.php" target="_blank">';
				echo '<input type="submit" class="boton" name="CMDPROVIDENCIA" value="Ver Providencia Registrada"></form>	';
			}
			// FIN

		}
		?>
	</div>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>