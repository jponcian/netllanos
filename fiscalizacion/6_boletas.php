<html>

<head>
	<title>Crear Boleta</title>
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
	$acceso = 8;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	$_SESSION['VARIABLE'] = 'NO';

	if ($_POST['CMDGUARDAR'] == "Guardar") {
		if ($_POST['ORIF'] <> "") {
			// CONSULTA DEL SIGUIENTE
			$consulta_x = "SELECT Max( numero )+1 AS Maximo FROM fis_boletas WHERE anno=" . date('Y') . " AND sector=0" . $_SESSION['SEDE'] . " GROUP BY anno";
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_array($tabla_x);
			if ($registro_x['Maximo'] > 0) {
				$Maximo = $registro_x['Maximo'];
			} else {
				$Maximo = 1;
			}
			// FIN
			// GUARDADO DE LA BOLETA
			$consulta = "INSERT INTO `fis_boletas` ( `sector` , `origen` , `anno` , `numero` , `rif` , `funcionario` , `fecha_com` , `fecha`, cedula, representante, cargo, hora ) VALUES ( '" . $_POST['OSEDE'] . "','4','" . date('Y') . "','" . $Maximo . "','" . strtoupper($_POST['ORIF']) . "','" . $_POST['OFUNCIONARIO'] . "','" . voltea_fecha($_POST['OFECHA']) . "','" . date('Y-m-d') . "', '" . $_POST['OCEDULA'] . "', '" . $_POST['OREPRESENTANTE'] . "', '" . $_POST['OCARGO'] . "', '" . $_POST['OHORA'] . "' );";
			$tabla_x = mysql_query($consulta);
			// FIN
			echo "<script type=\"text/javascript\">alert('Boleta Creada bajo el N�mero => " . $Maximo . "');</script>";
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
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

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
					echo '<table width="60%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>BOLETA REGISTRADA EXITOSAMENTE!!!</strong> </div></td> </tr>  </table>';
				}
				?>
			</p>
			<table width="55%" border="1" align="center">
				<tr>
					<td height="32" colspan="9" align="center" class="TituloTabla"><span><u>Datos de la Boleta</u></span></td>
				</tr>
				<td bgcolor="#CCCCCC"><strong>Sector: </strong></td>
				<td><label>
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
								</select>
							</span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Numero Tentativo:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15">
								<?php
								// CONSULTA DEL SIGUIENTE
								$consulta_x = "SELECT Max( numero )+1 AS Maximo FROM fis_boletas WHERE anno=" . date('Y') . " AND sector=0" . $_POST['OSEDE'] . " GROUP BY anno";
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
				<td bgcolor="#CCCCCC"><strong>
						A�o:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo date('Y'); ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>
						Fecha:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo date('d/m/Y'); ?></span></div>
					</label></td>
			</table>

			<table width="55%" border="1" align="center">
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Rif:</strong></td>
					<td width="28%"><label>
							<input type="text" name="ORIF" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
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
								}
								?>
							</span></label></td>
				</tr>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
					<td colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?></span></label></td>
				</tr>
			</table>
			<table width="55%" border="1" align="center">
				<tr>
					<td bgcolor="#CCCCCC"><strong>Funcionario Actuante: </strong></td>
					<td><strong>
							<label> </label>
						</strong>
						<label>
							<input name="OFUNCIONARIO" type="radio" value="<?php
																			// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR
																			$consulta_x = "SELECT cedula FROM vista_jefe_fis WHERE id_sector=" . $_POST['OSEDE'] . ";";
																			$tabla_x = mysql_query($consulta_x);
																			$registro_x = mysql_fetch_object($tabla_x);
																			//---------------------------------
																			echo $registro_x->cedula;
																			//---------------------------------
																			?>" checked="checked">
						</label>
						Jefe Divisi&oacute;n
						<label>
							<input name="OFUNCIONARIO" type="radio" value="<?php
																			//---------------------------------
																			$consulta_x = "SELECT ci_gerente FROM z_region;";
																			$tabla_x = mysql_query($consulta_x);
																			$registro_x = mysql_fetch_object($tabla_x);
																			//---------------------------------
																			echo $registro_x->ci_gerente;
																			//---------------------------------
																			?>">
						</label>
						Gerente
					</td>
					<td bgcolor="#CCCCCC"><strong>Fecha Pautada: </strong></td>
					<td><label>
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly="true" value="<?php echo $_POST['OFECHA']; ?>">
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Hora Pautada: </strong></td>
					<td><label><input type="text" name="OHORA" size="8" value="<?php echo $_POST['OHORA']; ?>"></label></td>
				</tr>
			</table>
			<table width="55%" border="1" align="center">
				<tr>
					<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<input type="text" name="OCEDULA" size="12" maxlength="10" value="<?php echo $_POST['OCEDULA']; ?>">
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Representante</strong></td>
					<td><label>
							<input type="text" name="OREPRESENTANTE" size="35" maxlength="100" value="<?php echo $_POST['OREPRESENTANTE']; ?>">
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Cargo</strong></td>
					<td><label>
							<input type="text" name="OCARGO" size="35" maxlength="100" value="<?php echo $_POST['OCARGO']; ?>"></label></td>
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
		if ($_SESSION['VARIABLE'] == 'GUARDADO') {
			$consulta_x = "SELECT * FROM fis_boletas WHERE sector=" . $_SESSION['SEDE'] . " AND origen=" . $_SESSION['ORIGEN'] . " AND anno=" . $_SESSION['ANNO'] . " AND numero=" . $_SESSION['NUMERO'] . "";
			$tabla_x = mysql_query($consulta_x);

			if ($registro_x = mysql_fetch_array($tabla_x)) {
		?>
				<form name="form2" method="post" action="formatos/boleta.php" target="_blank">
					<table border="1" align="center">
						<tr>
							<td align="center" colspan=2 bgcolor="#FFFFFF"><input type="submit" class="boton" name="CMDBOLETA" value="Ver Boleta Registrada"></td>
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