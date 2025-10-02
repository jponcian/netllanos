<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 60;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>
<html>

<head>
	<title>Transferir Providencias</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
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

		.Estilo1 {
			color: #FFFFFF;
			font-size: 18px;
			font-weight: bold;
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
		$consulta = "SELECT id_expediente, anno, numero, anno_expediente_fisc, num_expediente_fisc, rif, contribuyente, fecha_culminacion, fecha_aprobacion, monto_confirmado, monto_revocado, sector FROM vista_sumario_expedientes WHERE sector =0" . $_POST['OSEDE'] . " AND anno_expediente_fisc =0" . $_POST['OANNO'];
		$tabla_datos = mysql_query($consulta);
		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			if ($_POST['CHECK' . $registro_datos->id_expediente] == $registro_datos->id_expediente) {
				// -------------- ACTUALIZACION DE LA LIQUIDACION
				$consulta = "UPDATE liquidacion SET status = 10, fecha_transferencia_a_liq = date(now()), usuario_transferencia_a_liq=" . $_SESSION['CEDULA_USUARIO'] . ", usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE secuencial=999999 AND status<=10 AND sector=" . $registro_datos->sector . " AND anno_expediente=" . $registro_datos->anno_expediente_fisc . " AND num_expediente=" . $registro_datos->num_expediente_fisc . " AND origen_liquidacion=5;";
				$tabla = mysql_query($consulta);
				// echo $consulta;
				//--------------- ACTUALIZAMOS EL ESTATUS DEL EXPEDIENTE DE SUMARIO
				$consulta = "UPDATE expedientes_sumario SET status = 4, fecha_transferida_liquidacion = date(now()), usuario_tranfiere_liquidacion=" . $_SESSION['CEDULA_USUARIO'] . ", usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE sector=" . $registro_datos->sector . " AND id_expediente=" . $registro_datos->id_expediente;
				$tabla = mysql_query($consulta);

				// MENSAJE
				echo "<script type=\"text/javascript\">alert('Expediente(s) Transferido(s) Exitosamente...!!!');</script>";
				//-- CAMBIO DE LA DIRECCION
				echo '<meta http-equiv="refresh" content="0";/>';
			}
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
					<td height="35" align="center" colspan="4" bgcolor="#FF0000"><span class="Estilo7"><u>Opciones para Filtrar</u></span></td>
				</tr>
				<tr height="27">
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Dependencia:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="0">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_sumario_expedientes WHERE status=3 GROUP BY sector;';
									} else {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_sumario_expedientes WHERE status=3 AND sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
									}
									//-----------
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
						<div align="center"><strong>A&ntilde;o:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1">
								<select name="OANNO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										if ($_SESSION['SEDE_USUARIO'] == 1) {
											$consulta_x = 'SELECT anno_expediente_fisc as anno FROM vista_sumario_expedientes WHERE status=3 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno_expediente_fisc;';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['OANNO'] == $registro_x['anno']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
											}
										} else {
											$consulta_x = 'SELECT anno_expediente_fisc as anno FROM vista_sumario_expedientes WHERE status=3 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno_expediente_fisc;';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['OANNO'] == $registro_x['anno']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
											}
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
							<p class="Estilo7"><u>Datos del(los) Expediente(s) por Transferir </u></p>
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
							<div align="center" class="Estilo8"><strong>N� Recepcion</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>A�o</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><span class="Estilo16">Providencia</span></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Nro. Resoluci�n</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Fecha Res.</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Contribuyente</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Monto Confirmado</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Monto Revocado</strong></div>
						</td>
					</tr>
					<?php
					$i = 0;
					//------
					$consulta = "SELECT id_expediente, anno, numero, anno_expediente_fisc, num_expediente_fisc, rif, contribuyente, fecha_culminacion, fecha_aprobacion, monto_confirmado, monto_revocado FROM vista_sumario_expedientes WHERE status=3 AND sector =0" . $_POST['OSEDE'] . " AND anno_expediente_fisc =0" . $_POST['OANNO'];
					$tabla_datos = mysql_query($consulta);
					while ($registro_datos = mysql_fetch_object($tabla_datos)) {
						$i++;
						//--------
						//PARA BUSCAR LA RESOLUCION
						$resolucion = funcion_resolucion($_POST['OSEDE'], 5, $registro_datos->anno_expediente_fisc, $registro_datos->num_expediente_fisc);
					?>
						<tr>
							<td bgcolor="#FFFFFF" height=27>
								<div align="center" class="Estilo8"><span class="Estilo15"><input type="checkbox" name="CHECK<?php echo $registro_datos->id_expediente; ?>" value="<?php echo $registro_datos->id_expediente; ?>"></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $i; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->anno . '-' . $registro_datos->numero; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->anno_expediente_fisc; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->num_expediente_fisc; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $resolucion[0]; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo date("d/m/Y", strtotime(voltea_fecha($resolucion[1]))); ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->contribuyente; ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo formato_moneda($registro_datos->monto_confirmado); ?></span></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><span class="Estilo15"><?php echo formato_moneda($registro_datos->monto_revocado); ?></span></div>
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