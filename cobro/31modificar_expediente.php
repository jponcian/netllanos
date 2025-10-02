<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 16;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['VARIABLE'] = 'NO MODIFICADA';

if ($_POST['CMDGUARDAR'] == "Guardar") {
	$guardar = 0;
	if ($_POST['ORIF'] <> "" and $_POST['OCOORDINADOR'] > 0 and $_POST['OFUNCIONARIO'] > 0 and $_POST['OTRIBUTO'] > 0 and $_POST['OTEXTO1'] <> '') {
		if ($_POST['OESPECIAL'] == 0 and $_POST['OFECHAE'] <> "") {
			//ACTUALIZAMOS AL CONTRIBUYENTE COMO ESPECIAL
			$sql_especial = mysql_query("UPDATE contribuyentes SET Especial=1, fechaespecial='" . $_POST['OFECHAE'] . "' WHERE rif='" . strtoupper($_POST['ORIF']) . "'");
			$guardar = 1;
		} else {
			$guardar = 0;
		}
		if ($_POST['OESPECIAL'] == 1) {
			$guardar = 1;
		}
		if ($guardar == 1) {
			// GUARDADO DE LOS DATOS
			$consulta = "UPDATE expedientes_especiales_siscontri SET FechaRegistro='" . $_POST['OFECHA'] . "', rif='" . $_POST['ORIF'] . "', Coordinador=" . $_POST['OCOORDINADOR'] . ", Funcionario=" . $_POST['OFUNCIONARIO'] . ", Usuario=" . $_SESSION['CEDULA_USUARIO'] . ", tributo=" . $_POST['OTRIBUTO'] . ", texto1='" . $_POST['OTEXTO1'] . "', Status=0 WHERE Numero=" . $_POST['ONUMERO'] . " and Anno=" . $_POST['OANNO'] . " AND Sector=" . $_POST['OSEDE'] . ";";
			$tabla = mysql_query($consulta);
			// FIN
			$_POST['OSEDE'] = '-1';
			$_POST['OANNO'] = '-1';
			$_POST['ONUMERO'] = '-1';
			$_POST['ORIF'] = '';
			$_POST['OCOORDINADOR'] = '';
			$_POST['OFUNCIONARIO'] = '';
			$_POST['OFECHA'] = '';
			$_POST['OTRIBUTO'] = '-1';
			$_POST['OTEXTO1'] = '';
			//--------------
			$_SESSION['VARIABLE'] = 'MODIFICADA';
			echo "<script type=\"text/javascript\">alert('Expediente Modificado Exitosamente!!!');</script>";
		} else {
			echo "<script type=\"text/javascript\">alert('!!!..Se requiere que indique la fecha de Especial...!!!');</script>";
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

//------ BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0 and $_POST['ONUMERO_ANTERIOR'] <> $_POST['ONUMERO']) {
	$consulta_x = 'SELECT numero, anno, rif, coordinador, funcionario, FechaRegistro, Especial, tributo, texto1 FROM vista_exp_especiales_siscontri WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_array($tabla_x);
	//---------
	$_POST['ORIF'] = $registro_x['rif'];
	$_POST['OCOORDINADOR'] = $registro_x['coordinador'];
	$_POST['OFUNCIONARIO'] = $registro_x['funcionario'];
	// $_POST['OFECHA'] = date("d-m-Y", strtotime($registro_x['FechaRegistro']));
	$_POST['OFECHA'] = $registro_x['FechaRegistro'];
	$_POST['OESPECIAL'] = $registro_x['Especial'];
	$_POST['OPORTADANUM'] = $registro_x['numero'];
	$_POST['OPORTADANNO'] = $registro_x['anno'];
	$_POST['OTRIBUTO'] = $registro_x['tributo'];
	$_POST['OTEXTO1'] = $registro_x['texto1'];
}
?>
<html>
<title>Modificar Expediente SPE</title>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</script>
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>
	<form name="form1" method="post" action="">
		<div align="center">
			<table width="55%" border="1" align="center">
				<tr>
					<td height="35" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del
								Expediente - Sujeto Pasivo Especial</u></span></td>
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
									$consulta_x = 'SELECT Sector, nombre FROM vista_exp_especiales_siscontri GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT Sector, nombre FROM vista_exp_especiales_siscontri WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
										$consulta_x = 'SELECT anno FROM vista_exp_especiales_siscontri WHERE sector =0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
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
										$consulta_x = 'SELECT numero FROM vista_exp_especiales_siscontri WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' ORDER BY numero DESC;';
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
								<input type="date" name="OFECHA" id="OFECHA"
									value="<?php echo isset($_POST['OFECHA']) ? $_POST['OFECHA'] : $_POST['OFECHA']; ?>"
									required></span></div>
					</label></td>
			</table>

			<p></p>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="33" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del
								Contribuyente o Sujeto Pasivo</u></span></td>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td width="28%"><label>
							<input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" size="12"
								maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
							<input type="submit" class="boton" name="Submit" value="Buscar"></label></td>
					<td width="21%" bgcolor="#CCCCCC"><strong>
							Contribuyente:</strong></td>
					<td width="36%"><label><span class="Estilo15">
								<?php
								if ($_POST['ORIF'] <> "") {
									// BUSQUEDA DEL CONTRIBUYENTE
									$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
									$tabla_x = mysql_query($consulta_x);
									$registro_x = mysql_fetch_object($tabla_x);
									// FIN
									echo $registro_x->contribuyente;
									$_POST['OESPECIAL'] = $registro_x->Especial;
								}
								?></span></label></td>
				</tr>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
					<td colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion; ?>
							</span></label></td>
				</tr>
				<?php
				if ($registro_x->Especial == 0 and $_POST['ORIF'] <> "") { ?>
					<tr>
						<td colspan="4" height="40" align="center" bgcolor="#FFFF00"><span class="Estilo15 luz"
								id="blink"><?php echo "!!!...El contribuyente seleccionado no est� calificado como especial, por favor debe actualizar la informaci�n, Ingrese Fecha Especial...!!! => "; ?>
							</span><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAE" size="8"
								readonly value="<?php echo $_POST['OFECHAE']; ?>"></td>
					</tr>
				<?php } ?>
			</table>

			<table width="50%" border="1" align="center">
				<tr>
					<td height="32" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos de los
								Funcionarios</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Coordinador:</strong></td>
					<td><label>
							<select name="OCOORDINADOR" size="1">
								<option value="-1">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Coordinador' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='ESPECIALES';";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OCOORDINADOR'] == $registro_x->cedula) {
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
					<td bgcolor="#CCCCCC"><strong>Funcionario:</strong></td>
					<td><label>
							<select name="OFUNCIONARIO" size="1">
								<option value="-1">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Funcionario' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='ESPECIALES';";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OFUNCIONARIO'] == $registro_x->cedula) {
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
			<table width="50%" border="1" align="center">
				<tr>
					<td height="31" colspan="9" align="center" bgcolor="#FF0000"><span
							class="Estilo7"><u>Autorizaci&oacute;n</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Tributo:</strong></td>
					<td><label>
							<select name="OTRIBUTO">
								<option value="-1">Seleccione</option>
								<?php
								$consulta2 = "SELECT id_tributo, nombre FROM a_tributos WHERE id_tributo IN (1,3,8,63,64) ORDER BY id_tributo;";
								$tabla2 = mysql_query($consulta2);
								while ($registro2 = mysql_fetch_object($tabla2)) {
									echo '<option';
									if ($_POST['OTRIBUTO'] == $registro2->id_tributo) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro2->id_tributo;
									echo '">';
									echo $registro2->nombre;
									echo '</option>';
								}
								?>
							</select>
						</label></td>

				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Per&iacute;odo:</strong></td>
					<td><label>
							<input type="text" name="OTEXTO1" size="55" maxlength="255"
								value="<?php echo $_POST['OTEXTO1']; ?>">
						</label></td>

				</tr>
			</table>

			</p>

			<label>

				<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">

			</label>
		</div>
		<input type="hidden" name="OESPECIAL" value="<?php echo $_POST['OESPECIAL']; ?>">
		<input type="hidden" name="OPORTADANUM" value="<?php echo $_POST['OPORTADANUM']; ?>">
		<input type="hidden" name="OPORTADANNO" value="<?php echo $_POST['OPORTADANNO']; ?>">
	</form>
	<div align="center">
		<?php
		if ($_POST['ONUMERO'] > 0) {
			//------------------
			echo '<form name="form2" method="post" action="formatos/portadaSISE.php?num=' . $_POST['ONUMERO'] . '&anno=' . $_POST['OANNO'] . '&sede=0' . $_POST['OSEDE'] . '" target="_blank">';
			echo '<input type="submit" class="boton" name="CMDPORTADA" value="Ver Hoja de Portada"></form>	';
			//------------------
			$_SESSION['SEDE'] = $_POST['OSEDE'];
			$_SESSION['ORIGEN'] = 2;
			$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
			$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
			//---------------
			echo '<form name="form2" method="post" action="formatos/autorizacionSISE.php" target="_blank">';
			echo '<input type="submit" class="boton" name="CMDAUTORIZACION" value="Ver Autorizaci&oacute;n"></form>	';
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