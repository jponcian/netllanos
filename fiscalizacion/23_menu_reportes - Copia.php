<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

?>

<html>

<head>
	<title>Men&uacute; Reportes</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php";

		$_SESSION['VARIABLE'] = $_POST['OOPCION'];
		$_SESSION['RIF'] = $_POST['ORIF'];
		$_SESSION['INICIO'] = voltea_fecha($_POST['OINICIO']);
		$_SESSION['FIN'] = voltea_fecha($_POST['OFIN']);
		$_SESSION['FISCAL'] = $_POST['OFISCAL'];
		$_SESSION['SUPERVISOR'] = $_POST['OSUPERVISOR'];
		$_SESSION['ANNO'] = $_POST['OANNO'];
		$_SESSION['SEDE'] = $_POST['OSEDE'];
		?>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>

	<form name="form1" method="post" action="">
		<p>
		<table width="64%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Providencias</u></span></td>
			</tr>
			<tr>
				<td width="33%" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" value="EMITIDAS" checked <?php if ($_POST['OOPCION'] == 'EMITIDAS') {
																								echo 'checked="checked" ';
																							} ?>>
							</label>
							Emitidas </strong></div>
				</td>
				<td width="38%" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" value="POR NOTIFICAR" <?php if ($_POST['OOPCION'] == 'POR NOTIFICAR') {
																								echo 'checked="checked" ';
																							} ?>>
							</label>
							No Notificadas</strong></div>
				</td>
				<td width="29%" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" value="NOTIFICADAS" <?php if ($_POST['OOPCION'] == 'NOTIFICADAS') {
																							echo 'checked="checked" ';
																						} ?>>
							</label>
							Notificadas </strong></div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" value="CONCLUIDAS PRODUCTIVAS" disabled="disabled" <?php if ($_POST['OOPCION'] == 'CONCLUIDAS PRODUCTIVAS') {
																															echo 'checked="checked" ';
																														} ?>>
							</label>
							Concluidas Productivas </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" value="CONCLUIDAS CONFORMES" disabled="disabled" <?php if ($_POST['OOPCION'] == 'CONCLUIDAS CONFORMES') {
																														echo 'checked="checked" ';
																													} ?>>
							</label>
							Concluidas Conformes </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" type="radio" value="ANULADAS" <?php if ($_POST['OOPCION'] == 'ANULADAS') {
																						echo 'checked="checked" ';
																					} ?>>
							</label>
							Anuladas</strong></div>
				</td>
			</tr>
		</table>
		</p>
		<table width="38%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Seleccionar Opciones</u></span></td>
			</tr>
			<tr>
				<td colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="24%" bgcolor="#CCCCCC"><strong> Desde:</strong></td>
				<td width="32%"><label>
						<input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="8" readonly value="<?php echo $_POST['OINICIO']; ?>" />
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong> Hasta:</strong></td>
				<td width="28%"><label>
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFIN" size="8" readonly value="<?php echo $_POST['OFIN']; ?>" />
					</label></td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>A�o de Emisi&oacute;n: </strong></div>
				</td>
				<td colspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Sector</strong></div>
				</td>
			</tr>

			<td colspan="2"><label>
					<select name="OANNO" size="1">
						<option value="0"> ->-> TODOS <-<- </option>
								<?php
								$consulta_x = 'SELECT anno FROM vista_providencias GROUP BY anno ORDER BY anno DESC;';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									echo '<option';
									if ($_POST[OANNO] == $registro_x->anno) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->anno;
									echo '">';
									echo $registro_x->anno;
									echo '</option>';
								}
								?>
					</select>
				</label></td>
			<td colspan="2"><span class="Estilo1">
					<select name="OSEDE" size="1" onChange="this.form.submit()">
						<option value="0">Seleccione< /option>
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
				</span></td>
			</tr>

			<tr>
				<td colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Funcionarios Actuantes:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="24%" bgcolor="#CCCCCC"><strong> Fiscal:</strong></td>
				<td colspan="3"><label>
						<select name="OFISCAL" size="1">
							<option value="0">Seleccione</option>
							<?php
							//--------------------
							$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Funcionario' and sector=" . $_POST['OSEDE'] . " AND modulo='FISCALIZACION';";
							echo $consulta_x;
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
			<tr>
				<td width="24%" bgcolor="#CCCCCC"><strong> Supervisor:</strong></td>
				<td colspan="3"><label>
						<select name="OSUPERVISOR" size="1">
							<option value="0">Seleccione</option>
							<?php
							//--------------------
							$consulta_x = "SELECT cedula, Nombres, Apellidos FROM z_empleados WHERE id_origen=4 AND (Rol='S' or Rol='C') AND cedula>1000000 and sector=" . $_POST['OSEDE'] . ";";
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
				<td colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label>
						<select name="ORIF" size="1">
							<option value="0"> ->-> TODOS <-<- </option>
									<?php
									$consulta_x = 'SELECT Contribuyente.Rif, left(Contribuyente.NombreRazon,40) as NombreRazon FROM Providencia INNER JOIN Contribuyente ON Providencia.Rif = Contribuyente.Rif GROUP BY Contribuyente.Rif, Contribuyente.NombreRazon, Providencia.A�o_Providencia HAVING (((Providencia.A�o_Providencia)>2008)) ORDER BY Contribuyente.Rif, Contribuyente.NombreRazon;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';
										if ($_POST['ORIF'] == $registro_x->Rif) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->Rif;
										echo '">';
										echo $registro_x->Rif . ' - ' . $registro_x->NombreRazon;
										echo '</option>';
									}
									?>
						</select>
						<a href="javascript:NewCssCal('OINICIO','YYYYMMDD')"></a></label></td>
			</tr>
		</table>
		<p align="center">
			<label></label>
			<label>
				<input type="submit" class="boton" name="CMDCARGAR" value="Cargar">
			</label>
		</p>
	</form>
	<?php
	if ($_POST['CMDCARGAR'] == 'Cargar') {
		echo '<form name="form4" method="post" action="Reportes/providencias_siger_emi_not_anu_excel.php" target="_blank">
  <p align="center"><input type="submit" class="boton" name="CMDCONCLUIDOSEXCEL" value="Ver Reporte en Excel"></p>
</form>';
	} ?>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>