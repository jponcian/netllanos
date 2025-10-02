<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 2;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<title>Asignar Providencias al Fiscal</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />

</head>

<body style="background: transparent !important;">
	<?php
	if ($_POST['CMDASIGNAR'] == 'Asignar') {
		if ($_POST['OFECHA'] <> '') {
			$consulta = "SELECT id_expediente, anno, numero FROM expedientes_fiscalizacion WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=1 AND (ci_supervisor=" . $_SESSION['CEDULA_USUARIO'] . " or ci_fiscal1=" . $_SESSION['CEDULA_USUARIO'] . ");";
			//------ SI ES ADMINISTRADOR
			if ($_SESSION['ADMINISTRADOR'] > 0) {
				$consulta = "SELECT id_expediente, anno, numero FROM expedientes_fiscalizacion WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=1;";
			}
			//--------------------------
			$tabla_datos = mysql_query($consulta);
			while ($registro_datos = mysql_fetch_object($tabla_datos)) {
				if ($_POST['CHECK' . $registro_datos->id_expediente] == $registro_datos->id_expediente) {
					// --------------
					$consulta = "SELECT id_expediente FROM expedientes_fiscalizacion  WHERE id_expediente=" . $registro_datos->id_expediente . " AND fecha_emision>'" . voltea_fecha($_POST['OFECHA']) . "';";
					$tabla_xxx = mysql_query($consulta);
					if ($registro_xxx = mysql_fetch_object($tabla_xxx)) {
						echo "<script type=\"text/javascript\">alert('Providencia " . $registro_datos->anno . '/' . $registro_datos->numero . ", la Fecha de Asignaci�n no puede ser menor a la Fecha de Emisi�n !!!');</script>";
					} else {
						// --------------
						$consulta = "UPDATE expedientes_fiscalizacion SET status=2, fecha_asignacion = '" . voltea_fecha($_POST['OFECHA']) . "' WHERE id_expediente=" . $registro_datos->id_expediente . " AND fecha_emision<='" . voltea_fecha($_POST['OFECHA']) . "';";
						$tabla = mysql_query($consulta);
						// --------------
						echo "<script type=\"text/javascript\">alert('Providencia Asignada Exitosamente!!!');</script>";
					}
				}
			}
		} else {
			echo "<script type=\"text/javascript\">alert('Seleccione una Fecha...');</script>";
		}
	}
	?>
	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post">
		<div align="center">
			<p>&nbsp;</p>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" colspan="4" class="TituloTabla"><span><u>Opciones para Filtrar</u></span></td>
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
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias WHERE status=1 GROUP BY sector;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['id_sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
										}
									} else {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias WHERE status=1 AND sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
										$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE status=1 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
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
				</tr>
			</table>
			<table class="formateada" width="50%" border=1 align=center>
				<tbody>
					<tr>
						<td class="TituloTabla" height="27" colspan="10" align="center"><span><u>Datos de la(s) Providencia(es) por Asignar al Fiscal</u></span></td>
					</tr>
					<tr>
						<th height=27>
							<div align="center" class="Estilo8"><strong>Sel.</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>A�o:</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><span class="Estilo16">Providencia:</span></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Emision:</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Fiscal:</strong></div>
							</td>
					</tr>
					<?php
					$i = 0;
					//------
					$consulta = "SELECT id_expediente, anno, numero, Apellidos1, Nombres1, fecha_emision FROM vista_providencias WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=1 AND (ci_supervisor=" . $_SESSION['CEDULA_USUARIO'] . " or ci_fiscal1=" . $_SESSION['CEDULA_USUARIO'] . ");";
					//------ SI ES ADMINISTRADOR
					if ($_SESSION['ADMINISTRADOR'] > 0) {
						$consulta = "SELECT id_expediente, anno, numero, Apellidos1, Nombres1, fecha_emision FROM vista_providencias WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=1;";
					}
					//--------------------------
					$tabla_datos = mysql_query($consulta);
					while ($registro_datos = mysql_fetch_object($tabla_datos)) {
						$i++;
						//--------
					?>
						<tr id="fila<?php echo $registro_datos->id_expediente; ?>">
							<td height=27>
								<div align="center" class="Estilo8"><span class="Estilo15"><input type="checkbox" name="CHECK<?php echo $registro_datos->id_expediente; ?>" value="<?php echo $registro_datos->id_expediente; ?>" onClick="marcar(this,<?php echo $registro_datos->id_expediente; ?>)"></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $i; ?></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->anno; ?></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->numero; ?></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo voltea_fecha($registro_datos->fecha_emision); ?></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->Nombres1 . ' ' . $registro_datos->Apellidos1; ?></span></div>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>

			<p>
				<?php
				if ($ASIGNADAS == 'SI') {
					echo '<table width="60%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>PROVIDENCIAS ASIGNADAS!!! </strong></div></td> </tr>  </table><p></p>';
				}
				if ($i > 0) {
				?>
			<table width="20%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="10" align="center">
							<p class="Estilo7"><u>Fecha de Asignaci&oacute;n</u></p>
						</td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC" width="6%" height=27>
							<div align="center" class="Estilo8"><label>
									<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>" /></label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<p></p><input type="submit" class="boton" name="CMDASIGNAR" value="Asignar">
		<?php
				}
		?>
		</p>
		</div>
	</form>

	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>