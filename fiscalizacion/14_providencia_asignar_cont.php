<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 3;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['SEDE'] = 0;
$_SESSION['ANNO_PRO'] = 0;
$_SESSION['NUM_PRO'] = 0;
//--------
$rif = '';
// ------- PARA BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0) {
	// CONSULTA
	$consulta = "SELECT rif, ci_supervisor, ci_fiscal1 FROM expedientes_fiscalizacion WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND numero=0" . $_POST['ONUMERO'] . ";";
	$tabla_datos = mysql_query($consulta);
	if ($registro_datos = mysql_fetch_object($tabla_datos)) {
		//------------
		$datos = 'SI';
		$rif = $registro_datos->rif;
		//------------
		$_SESSION['SEDE'] = $_POST['OSEDE'];
		$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
		$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
		//------------
		if ($registro_datos->ci_supervisor == $_SESSION['CEDULA_USUARIO'] or $registro_datos->ci_fiscal1 == $_SESSION['CEDULA_USUARIO'] or $_SESSION['ADMINISTRADOR'] > 0) {
			$autorizado = 'SI';
		} else {
			$autorizado = 'NO';
			echo "<script type=\"text/javascript\">alert('���No posee Autorizaci�n sobre esta Providencia!!!');</script>";
		}
		//-----------
		if (strtoupper($rif) <> "J000000000") {
			echo "<script type=\"text/javascript\">alert('���Ya se ha Asignado el Contribuyente!!!');</script>";
		}
	}
}

// ------- PARA GUARDAR EL RIF
if ($_POST['CMDASIGNAR'] == "Asignar") {
	$_POST['ORIF'] = strtoupper($_POST['ORIF']);
	// VALIDACION SI EXISTE EL RIF EN LA BASE DE DATOS
	$consulta = "SELECT * FROM contribuyentes WHERE rif='" . $_POST['ORIF'] . "';";
	$tabla = mysql_query($consulta);
	if ($registro = mysql_fetch_object($tabla)) {
		$consulta = "UPDATE expedientes_fiscalizacion SET rif = '" . $_POST['ORIF'] . "', usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE'] . ";";
		$tabla = mysql_query($consulta);
		//-------
		$rif = $_POST['ORIF'];
		// ACTUALIZACION TERMINADA
		echo "<script type=\"text/javascript\">alert('���Providencia Actualizada!!!');</script>";
	} else {
		// MENSAJE DE QUE NO EXISTE EL RIF EN LA BASE DE DATOS
		echo "<script type=\"text/javascript\">alert('���No Existe el Rif en la Base de Datos!!!');</script>";
	}
}
?>
<html>

<head>
	<title>Asignar Contribuyente a la Providencia</title>
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
			<table width="47%" border="1" align="center">
				<tr>
					<td height="35" align="center" class="TituloTabla" colspan="6"><span><u>Datos de la Providencia a Asignar Contribuyente</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Dependencia:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										$consulta_x = 'SELECT id_sector, z_sectores.nombre FROM vista_providencias, z_sectores WHERE id_sector=sector GROUP BY sector;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['id_sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
										}
									} else {
										$consulta_x = 'SELECT id_sector, z_sectores.nombre FROM vista_providencias, z_sectores WHERE id_sector=sector AND sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
										$tabla_x = mysql_query($consulta_x);
										if ($registro_x = mysql_fetch_array($tabla_x)) {
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
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>A&ntilde;o:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1">
								<select name="OANNO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
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
								</select>
							</span></div>
					</td>
					<td width="15%" bgcolor="#CCCCCC">
						<div align="center"><strong>Numero:</strong></div>
					</td>
					<td width="36%"><label>
							<div align="center"><span class="Estilo1">
									<select name="ONUMERO" size="1">
										<option value="-1">Seleccione</option>
										<?php
										if ($_POST['OANNO'] > 0) {
											$consulta_x = 'SELECT numero FROM expedientes_fiscalizacion WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ';';
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
									</select>
								</span>
								<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
							</div>
						</label></td>
				</tr>
				<tr>
					<td colspan="6" align="center">
						<p>
							<?php include "../msg_validacion.php"; ?></p>
					</td>
				</tr>
			</table>
			<p>
				<?php
				if (strtoupper($rif) == "J000000000" and $autorizado == 'SI') {
				?>
			</p>
			<table width="60%" border="1" align="center">
				<tr>
					<td align="center" class="TituloTabla" colspan="9"><span><u>Datos de la Providencia</u></span></td>
				</tr>
				<tr>
					<td width="10%" bgcolor="#CCCCCC"><strong>A�o:</strong></td>
					<td width="10%"><label>
							<div align="center"><span class="Estilo15">
									<?php
									$consulta = "SELECT * FROM vista_providencias WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE'] . ";";
									$tabla = mysql_query($consulta);
									$registro = mysql_fetch_object($tabla);

									echo $registro->anno;
									$tipo = $registro->tipo;
									?>
								</span></div>
						</label></td>
					<td width="10%" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
					<td width="10%"><label>
							<div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span></div>
						</label></td>
					<td width="10%" bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
					<td width="10%"><label>
							<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_emision); ?></span></div>
						</label></td>
					<td width="10%" bgcolor="#CCCCCC"><strong>Sector:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo strtoupper($registro->nombre); ?></span></div>
						</label></td>
				</tr>
			</table>

			<table width="60%" border="1" align="center">
				<tr>
					<td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_rif($registro->rif); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->contribuyente; ?></span></label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_supervisor); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->Nombres . " " . $registro->Apellidos; ?></span></label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_fiscal1); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->Nombres1 . " " . $registro->Apellidos1; ?></span></label></td>
				</tr>
			</table>
		<?php
				}
		?>
		</p>
		<p>
			<?php
			if ($_POST['ONUMERO'] > 0) {
				if (strtoupper($rif) == "J000000000" and $autorizado == 'SI') {
			?>
		<table width="29%" border="1" align="center">
			<tr>
				<td width="100%" bgcolor="#FFFFFF">
					<p align="center"><strong>Rif a Asignar:</strong><input type="text" name="ORIF" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
						<input type="submit" class="boton" name="CMDASIGNAR" value="Asignar">
					</p>
				</td>
			</tr>
		</table>
<?php
				}
			}
?>
</p>
<p>&nbsp;</p>
		</div>
	</form>


	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>