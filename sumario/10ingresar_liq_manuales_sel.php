<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 119;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['OSEDE'] > 0 and $_POST['OORIGEN'] > 0 and $_POST['OANNO'] > 0 and $_POST['ONUMERO'] > 0) {
	if ($_POST['CMDBUSCAR'] == "Buscar") {
		$_SESSION['NUMERO'] = $_POST['ONUMERO'];
		$_SESSION['ANNO'] = $_POST['OANNO'];
		$_SESSION['ORIGEN'] = $_POST['OORIGEN'];
		$_SESSION['SEDE'] = $_POST['OSEDE'];
		//-----------------
		$consulta = "SELECT * FROM expedientes_manuales WHERE sector=" . $_SESSION['SEDE'] . " AND origen=" . $_SESSION['ORIGEN'] . " AND anno=" . $_SESSION['ANNO'] . " AND numero=" . $_SESSION['NUMERO'] . ";";
		$tabla_datos = mysql_query($consulta);
		if ($registro_datos = mysql_fetch_array($tabla_datos)) {
			header("Location: 10ingresar_liq_manuales.php?existe=si");
			exit();
		} else {
			header("Location: 10ingresar_liq_manuales.php?existe=no");
			exit();
		}
		///---------------------	
	}
}
?>

<html>

<head>
	<title>Ingresar Expediente Manual</title>
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
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Dependencia:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1"><select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php

									if ($_SESSION['ADMINISTRADOR'] == 1) {
										$consulta_x = 'SELECT id_sector, nombre FROM z_sectores';
									} else {
										// --- VALIDACION DE LA SEDE DEL USUARIO
										if ($_SESSION['SEDE_USUARIO'] <> 1) {
											$sede = ' WHERE sector=' . $_SESSION['SEDE_USUARIO'];
										} else {
											$sede = '';
										}
										// -------------------------------------
										$consulta_x = 'SELECT id_sector, nombre FROM z_sectores ' . $sede . '';
									}
									//---------------------
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
						<div align="center"><strong>Origen:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1"><select name="OORIGEN" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										$consulta_x = 'SELECT codigo, descripcion FROM a_origen_liquidacion WHERE codigo = 54';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OORIGEN'] == $registro_x['codigo']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['codigo'] . '>' . $registro_x['descripcion'] . '</option>';
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
						<div align="center"><span class="Estilo1"><select name="OANNO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OORIGEN'] > 0) {
										$i = date('Y');
										while ($i > (date('Y') - 5)) {
											echo '<option ';
											if ($_POST['OANNO'] == $i) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $i . '>' . $i . '</option>';
											$i--;
										}
									}
									?>
								</select>
							</span></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Numero:</strong></div>
					</td>
					<td><label>
							<div align="center">
								<input type="text" name="ONUMERO" size="4" value="<?php echo $_POST['ONUMERO']; ?>">
							</div>
						</label></td>
					<td><label>
							<div align="center">
								<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
							</div>
						</label></td>
				</tr>
				<tr>
					<td colspan="9" align="center">
						<p>
							<?php include "../msg_validacion.php"; ?>
						</p>
					</td>
				</tr>
			</table>
			<p>&nbsp;</p>
		</div>
	</form>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>