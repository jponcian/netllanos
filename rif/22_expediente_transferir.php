<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 72;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>
<html>

<head>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<title>Transferir Expediente SPE</title>
	<style type="text/css">
		<!--
		.Estilomenun {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight: bold;
		}

		body {
			background-image: url();
		}

		.Estilo7 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}

		.Estilo16 {
			color: #000000;
			font-weight: bold;
		}

		.Estilo15 {
			font-size: 14px;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<?php
	if ($_POST['CMDTRANSFERIR'] == 'Transferir') {
		$consulta = "SELECT * FROM vista_exp_rif WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=6 AND coordinador<>0;";
		$tabla_datos = mysql_query($consulta);
		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			if ($_POST['CHECK' . $registro_datos->id_expediente] == $registro_datos->id_expediente) {
				// -------------- ACTUALIZACION DEL EXPEDIENTE
				$consulta = "UPDATE expedientes_rif SET status=7, fecha_transferencia = date(now()) WHERE id_expediente=" . $registro_datos->id_expediente . ";";
				$tabla = mysql_query($consulta);
				// --------------

				// -------------- ACTUALIZACION DE LA LIQUIDACION
				$consulta = "UPDATE liquidacion SET status = 10, fecha_transferencia_a_liq = date(now()), usuario_transferencia_a_liq=" . $_SESSION['CEDULA_USUARIO'] . ", usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE sector=" . $registro_datos->sector . " AND anno_expediente=" . $registro_datos->anno . " AND num_expediente=" . $registro_datos->numero . " AND origen_liquidacion=" . $_SESSION['ORIGEN'] . ";";
				$tabla = mysql_query($consulta);
				// --------------

				echo "<script type=\"text/javascript\">alert('Expediente Transferido Exitosamente!!!');</script>";
				//-------------
			}
		}
	}
	?>
	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>
	<form name="form1" method="post">
		<div align="center">
			<p>&nbsp;</p>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" colspan="4" bgcolor="#FF0000"><span class="Estilo7"><u>Opciones para Filtrar</u></span></td>
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
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_rif WHERE status=6 GROUP BY sector;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['id_sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
										}
									} else {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_rif WHERE status=6 AND sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
										$consulta_x = 'SELECT anno FROM vista_exp_rif WHERE status=6 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
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
			<table width="50%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="10" align="center">
							<p class="Estilo7"><u>Datos de la(s) Providencia(es) por Transferir </u></p>
						</td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC" height=27>
							<div align="center" class="Estilo8"><strong>Sel.</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>A�o:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><span class="Estilo16">Expediente:</span></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Emision:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Contribuyente:</strong></div>
						</td>
					</tr>
					<?php
					$i = 0;
					//------
					$consulta = "SELECT id_expediente, anno, numero, nombrefuncionario, nombrecoordinador, FechaRegistro, contribuyente FROM vista_exp_rif WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=6 AND coordinador<>0 ORDER BY id_expediente DESC;";
					$tabla_datos = mysql_query($consulta);
					while ($registro_datos = mysql_fetch_object($tabla_datos)) {
						$i++;
						//--------
					?>
						<tr>
							<td bgcolor="#FFFFFF" height=27>
								<div align="center" class="Estilo8"><span class="Estilo15"><input type="checkbox" name="CHECK<?php echo $registro_datos->id_expediente; ?>" value="<?php echo $registro_datos->id_expediente; ?>"></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $i; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->anno; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->numero; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo voltea_fecha($registro_datos->FechaRegistro); ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->contribuyente; ?></span></div>
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
			<p></p><input type="submit" class="boton" name="CMDTRANSFERIR" value="Transferir">
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