<html>

<head>
	<title>Crear Requerimiento</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}
	$acceso = 7;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	$_SESSION['VARIABLE'] = 'NO';

	if ($_POST['CMDGUARDAR'] == "Guardar") {
		if ($_POST['ORIF'] <> "" and $_POST['OSUPERVISOR'] <> "" and $_POST['OFISCAL'] <> "" and $_POST['OTEXTO1'] <> "") {
			// CONSULTA DEL SIGUIENTE
			$consulta_x = "SELECT Max( numero )+1 AS Maximo FROM fis_requerimientos WHERE anno=" . date('Y') . " AND sector=0" . $_POST['OSEDE'] . " GROUP BY anno";
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_array($tabla_x);
			if ($registro_x['Maximo'] > 0) {
				$Maximo = $registro_x['Maximo'];
			} else {
				$Maximo = 1;
			}
			// FIN
			// GUARDADO DEL REQUERIMIENTO
			$consulta = "INSERT INTO `fis_requerimientos` ( `sector` , `origen` , `anno` , `numero` , `rif` , `supervisor` , `fiscal` , `fecha` )
VALUES ( '" . $_POST['OSEDE'] . "','4','" . date('Y') . "','" . $Maximo . "','" . strtoupper($_POST['ORIF']) . "','" . $_POST['OSUPERVISOR'] . "','" . $_POST['OFISCAL'] . "','" . date('Y-m-d') . "' );";
			$tabla_x = mysql_query($consulta);
			// FIN
			// ID DEL REQUERIMIENTO
			$consulta = "SELECT id_req FROM `fis_requerimientos` WHERE `sector`=" . $_POST['OSEDE'] . " AND `origen`=4 AND `anno`=" . date('Y') . " AND `numero`=" . $Maximo . ";";
			$tabla_x = mysql_query($consulta);
			$registro_x = mysql_fetch_array($tabla_x);
			$id = $registro_x['id_req'];
			// ----------
			// GUARDADO DEL DETALLE
			$i = 1;
			$x = $_SESSION['VARIABLE1'];
			do {
				if ($_POST[(OTEXTO . $i)] <> '') {
					$consulta = "INSERT INTO `fis_requerimientos_det` ( `id_req` , `texto` ) VALUES ( '" . $id . "','" . strtoupper($_POST[(OTEXTO . $i)]) . "');";
					$tabla_x = mysql_query($consulta);
				}
				$i++;
			} while ($i <= $x);
			// FIN
			echo "<script type=\"text/javascript\">alert('Requerimiento Creado bajo el N�mero => " . $Maximo . "');</script>";
			// -----------
			$_SESSION['ANNO'] = date('Y');
			$_SESSION['NUMERO'] = $Maximo;
			$_SESSION['SEDE'] = $_POST['OSEDE'];
			$_SESSION['ORIGEN'] = 4;
			// -----------
			$_SESSION['VARIABLE1'] = 'NO';
			$_SESSION['VARIABLE'] = 'GUARDADO';
		} else {
			$_SESSION['VARIABLE1'] = 'SI';
		}
	}

	?>
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
			<p>
				<?php
				if ($_SESSION['VARIABLE1'] == 'SI') {
					echo '<table width="60%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>EXISTEN CAMPOS VACIOS!!!</strong> </div></td> </tr>  </table>';
				}
				if ($_SESSION['VARIABLE'] == 'GUARDADO') {
					echo '<table width="60%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>REQUERIMIENTO REGISTRADO EXITOSAMENTE!!!</strong> </div></td> </tr>  </table>';
				}
				?>
			</p>
			<table width="55%" border="1" align="center">
				<tr>
					<td align="center" class="TituloTabla" colspan="9"><span><u>Datos del Requerimiento </u></span></td>
				</tr>
				<td width="16%" bgcolor="#CCCCCC"><strong>Sector: </strong></td>
				<td width="16%"><label>
						<div align="center"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_SESSION['SEDE_USUARIO'] == 1) {
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
								</select></span></div>
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>Numero Tentativo: </strong></td>
				<td width="16%"><label>
						<div align="center"><span class="Estilo15">
								<?php
								// CONSULTA DEL SIGUIENTE
								$consulta_x = "SELECT Max(numero )+1 AS Maximo FROM fis_requerimientos WHERE anno=" . date('Y') . " AND sector=0" . $_POST['OSEDE'] . " GROUP BY anno";
								$tabla_x = mysql_query($consulta_x);
								$registro_x = mysql_fetch_array($tabla_x);
								// ------
								if ($registro_x['Maximo'] > 0) {
									$Maximo = $registro_x['Maximo'];
								} else {
									$Maximo = 1;
								}
								// FIN
								echo $Maximo; ?>
							</span></div>
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>
						A�o:</strong></td>
				<td width="16%"><label>
						<div align="center"><span class="Estilo15"><?php echo date('Y'); ?></span></div>
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>
						Fecha:</strong></td>
				<td width="16%"><label>
						<div align="center"><span class="Estilo15"><?php echo date('d/m/Y'); ?></span></div>
					</label></td>
			</table>
			<table width="55%" border="1" align="center">
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
			<table width="55%" border="1" align="center">
				<tr>
					<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<input type="text" name="OSUPERVISOR" size="12" maxlength="10" value="<?php echo $_POST['OSUPERVISOR']; ?>">
							<input type="submit" class="boton" name="Submit3" value="Buscar">
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
					<td><label><span class="Estilo15"><?php
														if (abs($_POST['OSUPERVISOR']) > 0) {
															// BUSQUEDA DEL EMPLEADO
															$consulta_x = "SELECT * FROM z_empleados WHERE cedula='" . $_POST['OSUPERVISOR'] . "';";
															$tabla_x = mysql_query($consulta_x);
															$registro_x = mysql_fetch_object($tabla_x);
															// FIN
															echo $registro_x->Nombres . ' ' . $registro_x->Apellidos;
														}
														?></span></label></td>
				</tr>
				<tr>
					<td width="11%" bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td width="29%"><label>
							<input type="text" name="OFISCAL" size="12" maxlength="10" value="<?php echo $_POST['OFISCAL']; ?>">
							<input type="submit" class="boton" name="Submit4" value="Buscar">
						</label></td>
					<td width="17%" bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
					<td width="43%"><label><span class="Estilo15"><?php
																	if (abs($_POST['OFISCAL']) > 0) {
																		// BUSQUEDA DEL EMPLEADO
																		$consulta_x = "SELECT * FROM z_empleados WHERE cedula='" . $_POST['OFISCAL'] . "';";
																		$tabla_x = mysql_query($consulta_x);
																		$registro_x = mysql_fetch_object($tabla_x);
																		// FIN
																		echo $registro_x->Nombres . ' ' . $registro_x->Apellidos;
																	}
																	?></span></label></td>
				</tr>
			</table>
			<table width="55%" border="1" align="center">
				<tr>
					<td bgcolor="#CCCCCC"><strong>Texto 1:</strong></td>
					<td>
						<input type="text" name="OTEXTO1" size="100" maxlength="500" value="<?php echo $_POST['OTEXTO1'] ?>"><input name="OOPCION1" type="checkbox" onClick="this.form.submit()" value="1" <?php if ($_POST['OOPCION1'] == '1') {
																																																				echo 'checked="checked"';
																																																			} ?>>
					</td>
				</tr>

				<?php
				$i = 1;
				$x = $_SESSION['VARIABLE1'];
				do {
					if ($_POST[(OOPCION . $i)] == $i) {
						$i++;
				?>
						<tr>
							<td bgcolor="#CCCCCC"><strong>Texto <?php echo $i; ?>:</strong></td>
							<td>
								<input type="text" name="OTEXTO<?php echo $i; ?>" size="100" maxlength="500" value="<?php if ($_POST[(OTEXTO . $i)] <> '') {
																														echo $_POST[(OTEXTO . $i)];
																													} ?>"><input name="OOPCION<?php echo $i; ?>" type="checkbox" onClick="this.form.submit()" value="<?php echo $i; ?>" <?php if ($_POST[(OOPCION . $i)] == $i) {
																																																											echo 'checked="checked"';
																																																										} ?>>
							</td>
						</tr>
				<?php
						$_SESSION['VARIABLE1'] =  $i;
					} else {
						$i++;
					}
				} while ($i <= $x)
				?>
			</table>


			</p>

			<label>


				<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
			</label>
		</div>
	</form>

	<div align="center">

		<?php
		if ($_SESSION['VARIABLE'] == 'GUARDADO') {
			$consulta_x = "SELECT * FROM fis_requerimientos WHERE sector=" . $_SESSION['SEDE_USUARIO'] . " AND origen=" . $_SESSION['ORIGEN'] . " AND anno=" . $_SESSION['ANNO'] . " AND numero=" . $_SESSION['NUMERO'] . "";
			$tabla_x = mysql_query($consulta_x);

			if ($registro_x = mysql_fetch_array($tabla_x)) {
		?>
				<form name="form2" method="post" action="formatos/requerimiento.php" target="_blank">
					<table border="1" align="center">
						<tr>
							<td bgcolor="#FFFFFF">
								<p align="center">
									<label>
										<input type="checkbox" name="LINEA" value="1">Correr Firma
									</label>
							</td>
							<td bgcolor="#FFFFFF">
								<p align="center">
									<label>
										<input type="checkbox" name="GERENTE" value="1">
										Firma Gerente
									</label>
							</td>
						</tr>
						<tr>
							<td align="center" colspan=2 bgcolor="#FFFFFF"><input type="submit" class="boton" name="CMDREQUERIMIENTO" value="Ver Requerimiento Registrado"></td>
						</tr>
					</table>
				</form>
		<?php
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