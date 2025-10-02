<html>

<head>
	<title>Reimpresi&oacute;n</title>
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
	$acceso = 31;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

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
			<table width="55%" border="1" align="center">
				<tr>
					<td height="42" colspan="9" align="center" class="TituloTabla"><span><u>Seleccione las opciones a consultar...</u></span></td>
				</tr>
				<td bgcolor="#CCCCCC"><strong>Tipo:</strong></td>
				<td><label>
						<div align="center">
							<select name="OTIPO" onChange="this.form.submit()">
								<option <?php if ($_POST['OTIPO'] == -1) {
											echo 'selected="selected" ';
										}			  ?>value='-1'>--- Seleccione ---</option>
								<option <?php if ($_POST['OTIPO'] == 1) {
											echo 'selected="selected" ';
										}			  ?>value='1'>REQUERIMIENTOS</option>
								<option <?php if ($_POST['OTIPO'] == 2) {
											echo 'selected="selected" ';
										}			  ?>value='2'>BOLETAS</option>
							</select>
						</div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Sector:</strong></td>
				<td><label>
						<div align="center">
							<select name="OSECTOR" onChange="this.form.submit()">
								<option <?php if ($_POST['OSECTOR'] == -1) {
											echo 'selected="selected" ';
										}			  ?>value='-1'>--- Seleccione ---</option>

								<?php

								if ($_POST['OTIPO'] == 1) {
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										$consulta_x = "SELECT sector, nombre FROM fis_requerimientos, z_sectores WHERE id_sector = sector GROUP BY sector, nombre";
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option';
											if ($_POST['OSECTOR'] == $registro_x['sector']) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x['sector'];
											echo '">';
											echo $registro_x['nombre'];
											echo '</option>';
										}
									} else {
										$consulta_x = "SELECT sector, nombre FROM fis_boletas, z_sectores WHERE id_sector = sector AND sector =0" . $_SESSION['SEDE_USUARIO'] . " GROUP BY sector, nombre ORDER BY sector ";
										echo $consulta_x;
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option';
											if ($_POST['OSECTOR'] == $registro_x['sector']) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x['sector'];
											echo '">';
											echo $registro_x['nombre'];
											echo '</option>';
										}
									}
								}

								if ($_POST['OTIPO'] == 2) {
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										$consulta_x = "SELECT sector, nombre FROM fis_boletas , z_sectores WHERE id_sector = sector GROUP BY sector, nombre";
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option';
											if ($_POST['OSECTOR'] == $registro_x['sector']) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x['sector'];
											echo '">';
											echo $registro_x['nombre'];
											echo '</option>';
										}
									} else {
										$consulta_x = "SELECT sector, nombre FROM fis_boletas , z_sectores WHERE id_sector = sector AND sector =0" . $_SESSION['SEDE_USUARIO'] . " GROUP BY sector, nombre ORDER BY sector ";
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option';
											if ($_POST['OSECTOR'] == $registro_x['sector']) {
												echo ' selected="selected" ';
											}
											echo ' value="';
											echo $registro_x['sector'];
											echo '">';
											echo $registro_x['nombre'];
											echo '</option>';
										}
									}
								}
								?>
							</select>
						</div>
					</label></td>

				<td bgcolor="#CCCCCC"><strong>
						A�o:</strong></td>
				<td><label>
						<div align="center">
							<select name="OANNO" onChange="this.form.submit()">
								<option <?php if ($_POST['OANNO'] == -1) {
											echo 'selected="selected" ';
										}			  ?>value='-1'>--- Seleccione ---</option>
								<?php
								if ($_POST['OTIPO'] == '1' and $_POST['OSECTOR'] > 0) {
									$consulta_x = "SELECT anno FROM fis_requerimientos WHERE sector = 0" . $_POST['OSECTOR'] . " GROUP BY anno ORDER BY anno DESC";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option';
										if ($_POST['OANNO'] == $registro_x['anno']) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x['anno'];
										echo '">';
										echo $registro_x['anno'];
										echo '</option>';
									}
								}

								if ($_POST['OTIPO'] == 2 and $_POST['OSECTOR'] <> '-1') {
									$consulta_x = "SELECT anno FROM fis_boletas WHERE sector = 0" . $_POST['OSECTOR'] . " GROUP BY anno ORDER BY anno DESC";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option';
										if ($_POST['OANNO'] == $registro_x['anno']) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x['anno'];
										echo '">';
										echo $registro_x['anno'];
										echo '</option>';
									}
								}

								?>
							</select>
						</div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>
						Numero:</strong></td>
				<td w><label>
						<div align="center">
							<select name="ONUMERO" onChange="this.form.submit()">
								<option <?php if ($_POST['ONUMERO'] == -1) {
											echo 'selected="selected" ';
										}			  ?>value='-1'>--- Seleccione ---</option>
								<?php
								if ($_POST['OTIPO'] == 1 and $_POST['OSECTOR'] <> '-1' and $_POST['OANNO'] <> '-1') {
									$consulta_x = "SELECT numero FROM fis_requerimientos WHERE sector = 0" . $_POST['OSECTOR'] . " AND anno = 0" . $_POST['OANNO'] . " ORDER BY numero DESC";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option';
										if ($_POST['ONUMERO'] == $registro_x['numero']) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x['numero'];
										echo '">';
										echo $registro_x['numero'];
										echo '</option>';
									}
								}

								if ($_POST['OTIPO'] == 2 and $_POST['OSECTOR'] <> '-1' and $_POST['OANNO'] <> '-1') {
									$consulta_x = "SELECT numero FROM fis_boletas WHERE sector = 0" . $_POST['OSECTOR'] . " AND anno = 0" . $_POST['OANNO'] . " GROUP BY numero ORDER BY numero DESC";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option';
										if ($_POST['ONUMERO'] == $registro_x['numero']) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x['numero'];
										echo '">';
										echo $registro_x['numero'];
										echo '</option>';
									}
								}
								?>
							</select>
						</div>
					</label></td>
				<td bgcolor="#CCCCCC"><label>
						<div align="center">
							<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
						</div>
					</label></td>
			</table>


			</table>

			</p>

		</div>
	</form>

	<div align="center">

		<?php
		if ($_POST['CMDBUSCAR'] == 'Buscar') {
			if ($_POST['OTIPO'] == 1 and $_POST['OSECTOR'] <> '-1' and $_POST['OANNO'] <> '-1' and $_POST['ONUMERO'] <> '-1') {
				$consulta_x = "SELECT numero FROM fis_requerimientos WHERE sector = 0" . $_POST['OSECTOR'] . " AND anno = 0" . $_POST['OANNO'] . " AND numero= 0" . $_POST['ONUMERO'] . "";
				$tabla_x = mysql_query($consulta_x);
				if ($registro_x = mysql_fetch_array($tabla_x)) {
					$_SESSION['ANNO'] = $_POST['OANNO'];
					$_SESSION['NUMERO'] = $_POST['ONUMERO'];
					$_SESSION['SEDE'] = $_POST['OSECTOR'];
					$_SESSION['ORIGEN'] = 4;
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
								<td align="center" colspan=2 bgcolor="#FFFFFF"><input type="submit" class="boton" name="CMDREQUERIMIENTO" value="Ver Requerimiento"></td>
							</tr>
						</table>
					</form>
				<?php
				}
			}

			if ($_POST['OTIPO'] == 2 and $_POST['OSECTOR'] <> '-1' and $_POST['OANNO'] <> '-1' and $_POST['ONUMERO'] <> '-1') {
				$consulta_x = "SELECT numero FROM fis_boletas WHERE sector = 0" . $_POST['OSECTOR'] . " AND anno = 0" . $_POST['OANNO'] . " AND numero= 0" . $_POST['ONUMERO'] . "";
				$tabla_x = mysql_query($consulta_x);
				if ($registro_x = mysql_fetch_array($tabla_x)) {
					$_SESSION['ANNO'] = $_POST['OANNO'];
					$_SESSION['NUMERO'] = $_POST['ONUMERO'];
					$_SESSION['SEDE'] = $_POST['OSECTOR'];
					$_SESSION['ORIGEN'] = 4;
				?>
					<form name="form2" method="post" action="formatos/boleta.php" target="_blank">
						<table border="1" align="center">
							<tr>
								<td align="center" colspan=2 bgcolor="#FFFFFF"><input type="submit" class="boton" name="CMDBOLETA" value="Ver Boleta"></td>
							</tr>
						</table>
					</form>
		<?php
				}
			}
		}
		?>
	</div>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>