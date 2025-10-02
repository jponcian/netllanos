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

$_SESSION['VARIABLE'] = 'NO REGISTRADA';

if ($_POST['CMDGUARDAR'] == "Guardar") {
	if ($_POST['ORIF'] <> "" and $_POST['OCOORDINADOR'] > 1 and $_POST['OFISCAL'] > 1 and $_POST['OSEDE'] > 0 and $_POST['OTRIBUTO'] > 0 and $_POST['OTEXTO1'] <> '') {
		// CONSULTA DEL EXPEDIENTE SIGUIENTE
		$consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM expedientes_especiales WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . date('Y') . ';';
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_array($tabla_x);
		//-------------
		if ($registro_x['Maximo'] > 0) {
			$Maximo = $registro_x['Maximo'];
		} else {
			$Maximo = 1;
		}
		// FIN
		// GERENTE DE LA REGION
		$consulta_x = "SELECT ci_gerente FROM z_region;";
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		$Gerente = $registro_x->ci_gerente;
		//
		// JEFE DIVISION, SECTOR O UNIDAD
		$consulta_x = "SELECT cedula FROM z_jefes_detalle WHERE id_sector=" . $_POST['OSEDE'] . " AND (id_division=0 or id_division=7);";
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		$Jefe_Division = $registro_x->cedula;
		//

		// SECTOR DE LA PROVIDENCIA
		$_SESSION['SEDE_USUARIO'] = $_POST['OSEDE'];
		//	

		//VERIFICAMOS SI EXISTE UN EXPEDIENTE AL CONTRIBUYENTE EN ESE ESTATUS
		$guardar = 0;
		$consulta_existe = "SELECT Rif, Numero, Anno, Status FROM expedientes_especiales WHERE Rif='" . strtoupper($_POST['ORIF']) . "' and Anno=" . date("Y") . " and Status=0";
		$tabla_existe = mysql_query($consulta_existe);
		$cantidad = mysql_num_rows($tabla_existe);
		if ($cantidad < 1) {
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
				$consulta = "INSERT INTO expedientes_especiales (Numero, Anno, Rif, FechaRegistro, Coordinador, Funcionario, Usuario, Sector, Status, tributo, texto1) VALUES (" . $Maximo . ", " . date("Y") . ", '" . strtoupper($_POST['ORIF']) . "', date(now()), " . $_POST['OCOORDINADOR'] . ", " . $_POST['OFISCAL'] . ", " . $_SESSION['CEDULA_USUARIO'] . ", " . $_POST['OSEDE'] . ",0 , '" . $_POST['OTRIBUTO'] . "', '" . $_POST['OTEXTO1'] . "');";
				$tabla = mysql_query($consulta);
				// FIN
				$_SESSION['VARIABLE'] = 'REGISTRADA';
				echo "<script type=\"text/javascript\">alert('Expediente Creado bajo el N�mero => " . $Maximo . "');</script>";
			} else {
				echo "<script type=\"text/javascript\">alert('!!!..Se requiere que indique la fecha de Especial...!!!');</script>";
			}
		} else {
			$valor = mysql_fetch_object($tabla_existe);
			echo "<script type=\"text/javascript\">alert('Existe Expediente Abierto para el contribuyente con el N�mero => " . $valor->Numero . " A�o => " . $valor->Anno . "');</script>";
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}
?>
<html>

<head>
	<title>Crear Expediente SPE</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>
	<form name="form1" method="post" action="">
		<div align="center">
			<table width="50%" border="1" align="center">
				<tr>
					<td height="33" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente - Sujeto Pasivo Especial</u></span></td>
				</tr>
				<td height="35" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								}
								?>
							</select>
						</span>
					</div>
				</td>
				<td bgcolor="#CCCCCC" align="right"><strong>Numero:</strong></td>
				<td width="40"><label>
						<div align="center"><span class="Estilo15">
								<?php
								if ($_POST['OSEDE'] > 0) {
									// CONSULTA DEL EXPEDIENTE SIGUIENTE
									$consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM expedientes_especiales WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . date('Y') . ';';
									$tabla_x = mysql_query($consulta_x);
									$registro_x = mysql_fetch_array($tabla_x);
									//-------------
									if ($registro_x['Maximo'] > 0) {
										$Maximo = $registro_x['Maximo'];
									} else {
										$Maximo = 1;
									}
									// FIN
									echo $Maximo;
								}
								?></span></div>
					</label></td>
				<td width="60" bgcolor="#CCCCCC" align="right"><strong>
						A&ntilde;o:</strong></td>
				<td width="50"><label>
						<div align="center"><span class="Estilo15"><?php echo date('Y'); ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC" align="right"><strong>
						Fecha:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo date('d/m/Y'); ?></span></div>
					</label></td>
			</table>

			<table width="50%" border="1" align="center">
				<tr>
					<td height="36" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
				</tr>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td width="20%"><label>
							<input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
							<input type="submit" class="boton" name="Submit" value="Buscar"></label></td>
					<td width="11%" bgcolor="#CCCCCC"><strong>
							Contribuyente:</strong></td>
					<td width="36%"><label><span class="Estilo15"><?php
																	if ($_POST['ORIF'] <> "") {
																		// BUSQUEDA DEL CONTRIBUYENTE
																		$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
																		$tabla_x = mysql_query($consulta_x);
																		$registro_x = mysql_fetch_object($tabla_x);
																		// FIN
																		echo $registro_x->contribuyente;
																		$_POST['OESPECIAL'] = $registro_x->Especial;
																	}
																	?> <input type="hidden" name="OESPECIAL" id="OESPECIAL" value="<?php echo $_POST['OESPECIAL']; ?>">
							</span></label></td>
				</tr>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
					<td colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?>
							</span></label></td>
				</tr>
				<?php
				if ($_POST['OESPECIAL'] == 0 and $_POST['ORIF'] <> "") { ?>
					<tr>
						<td colspan="4" height="40" align="center" bgcolor="#FFFF00"><span class="Estilo15 luz" id="blink"><?php echo "!!!...El contribuyente seleccionado no est� calificado como especial, por favor debe actualizar la informaci�n, Ingrese Fecha Especial...!!! => "; ?>
							</span><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAE" size="8" readonly value="<?php echo $_POST['OFECHAE']; ?>"></td>
					</tr>
				<?php } ?>
			</table>

			<table width="50%" border="1" align="center">
				<tr>
					<td height="31" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos de los Funcionarios</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Coordinador:</strong></td>
					<td><label>
							<select name="OCOORDINADOR" size="1">
								<option value="-1">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Coordinador' and sector=" . $_POST['OSEDE'] . " AND modulo='ESPECIALES';";
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
							<select name="OFISCAL" size="1">
								<option value="-1">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Funcionario' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='ESPECIALES';";
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
			<table width="50%" border="1" align="center">
				<tr>
					<td height="31" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Autorizaci&oacute;n</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Tributo:</strong></td>
					<td><label>
							<select name="OTRIBUTO">
								<option value="-1">Seleccione</option>
								<?php
								$consulta2 = "SELECT id_tributo, nombre FROM a_tributos WHERE id_tributo IN (1,3,8,63) ORDER BY id_tributo;";
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
							<input type="text" name="OTEXTO1" size="55" maxlength="255" value="<?php if ($_POST['OTEXTO1'] == '') {
																									echo $texto1;
																								} else {
																									echo $_POST['OTEXTO1'];
																								} ?>">
						</label></td>

				</tr>
			</table>
			</p>

			<label>

				<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
			</label>
		</div>
	</form>

	<div align="center">

		<?php
		if ($_SESSION['VARIABLE'] == 'REGISTRADA') {
			// CONSULTA DEL ULTIMO EXPEDIENTE REGISTRADO
			$consulta_x = "SELECT anno, numero FROM expedientes_especiales WHERE sector=" . $_POST['OSEDE'] . " ORDER BY anno DESC , numero DESC;";
			$tabla_x = mysql_query($consulta_x);
			if ($registro_x = mysql_fetch_object($tabla_x)) {
				$_SESSION['ANNO'] = $registro_x->anno;
				$_SESSION['NUMERO'] = $registro_x->numero;
				$_SESSION['SEDE'] = $_POST['OSEDE'];
				//$_SESSION['VARIABLE1'] = $registro_x->tipo;
				//------------------
				echo '<form name="form2" method="post" action="formatos/portada.php?sede=0' . $_POST['OSEDE'] . '&num=' . $_SESSION['NUMERO'] . '&anno=' . $_SESSION['ANNO'] . '" target="_blank">';
				echo '<input type="submit" class="boton" name="CMDPORTADA" value="Ver Hoja de Portada"></form>';
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