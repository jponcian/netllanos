<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 139;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Transferir Recurso</title>
</head>

<body style="background: transparent !important;">
	<?php
	if ($_POST['CMDTRANSFERIR'] == 'Transferir') {
		$consulta = "SELECT * FROM vista_expedientes_juridico WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=6 ORDER BY id_expediente DESC;";
		$tabla_datos = mysql_query($consulta);
		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			if ($_POST['CHECK' . $registro_datos->id_expediente] == $registro_datos->id_expediente) {
				// -------------- ACTUALIZACION DEL EXPEDIENTE
				$consulta = "UPDATE expedientes_juridico SET status=7, fecha_transferida_liquidacion = date(now()), usuario_tran_liquidacion = " . $_SESSION['CEDULA_USUARIO'] . " WHERE id_expediente=" . $registro_datos->id_expediente . ";";
				$tabla = mysql_query($consulta);
				//echo $consulta;
				// -- PARA EL DETALLE DE LAS PLANILLAS
				$consulta = "SELECT * FROM vista_jur_detalle_planillas WHERE id_expediente=" . $registro_datos->id_expediente . ";";
				//echo $consulta;
				$tabla_x = mysql_query($consulta);
				while ($registro_x = mysql_fetch_object($tabla_x)) {
					// -------------- ACTUALIZACION DE LA LIQUIDACION
					if ($registro_x->gestion == 'Con Lugar') {
						$status = '91';
					} else {
						$status = $registro_x->status_anterior;
					}
					//---------------
					$consulta = "UPDATE liquidacion SET status=" . $status . ", usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion=" . $registro_x->id_liquidacion . ";";
					//echo $consulta;
					$tabla = mysql_query($consulta);
					// --------------
				}
				//-------------
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
									if ($_SESSION['ADMINISTRADOR'] > 0) //or $_SESSION['SEDE_USUARIO']==1
									{
										$consulta_x = "SELECT expedientes_juridico.sector as sector, z_sectores.nombre as dependencia FROM expedientes_juridico, z_sectores WHERE z_sectores.id_sector = expedientes_juridico.sector and expedientes_juridico.status=6 GROUP BY expedientes_juridico.sector;";
									} else {
										// -------------------------------------
										$consulta_x = "SELECT expedientes_juridico.sector as sector, z_sectores.nombre as dependencia FROM expedientes_juridico, z_sectores WHERE z_sectores.id_sector = expedientes_juridico.sector AND expedientes_juridico.sector = " . $_SESSION['SEDE_USUARIO'] . " AND expedientes_juridico.status=6 GROUP BY expedientes_juridico.sector;";
									}
									// -------------------------------------
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
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
									<option value="0">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										$consulta_x = "SELECT anno FROM expedientes_juridico WHERE sector = " . $_POST['OSEDE'] . " AND status=6 GROUP BY anno;";
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
						<td bgcolor="#FF0000" height="27" colspan="10" align="center">
							<p class="Estilo7"><u>Datos de lo(s) Expediente(es) por Transferir </u></p>
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
							<div align="center" class="Estilo8"><strong>Expediente:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Rif:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Contribuyente:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Resultado:</strong></div>
						</td>
					</tr>
					<?php
					$i = 0;
					//------
					$consulta = "SELECT * FROM vista_expedientes_juridico WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND status=6 ORDER BY id_expediente DESC;";
					//echo $consulta ;
					$tabla_datos = mysql_query($consulta);
					while ($registro_datos = mysql_fetch_object($tabla_datos)) {
						$i++;
						//--------
					?>
						<tr>
							<td height=27>
								<div align="center" class="Estilo8"><span class="Estilo15"><input type="checkbox" name="CHECK<?php echo $registro_datos->id_expediente; ?>" value="<?php echo $registro_datos->id_expediente; ?>"></span></div>
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
								<div align="center" class="Estilo8"><span class="Estilo15"><a href="6.1resultado_ponente.php?rif=<?php echo ($registro_datos->rif); ?>" target=_BLANK><?php echo ($registro_datos->rif); ?></a></span></div>
							</td>
							<td>
								<div align="left" class="Estilo8"><span class="Estilo15"><?php echo $registro_datos->contribuyente; ?></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8"><span class="Estilo15"><?php
																							//-------
																							$ii = 0;
																							$resultado = '';
																							$final = '';
																							$consulta = "SELECT gestion FROM vista_jur_detalle_planillas WHERE status_exp=6 AND rif='" . $registro_datos->rif . "' GROUP BY gestion;";
																							$tabla = mysql_query($consulta);
																							while ($registro = mysql_fetch_object($tabla)) {
																								if ($ii == 0) {
																									$resultado = $registro->gestion;
																								}
																								if ($resultado <> $registro->gestion) {
																									$final = 'Parcialmente con Lugar';
																								}
																								if ($final <> 'Parcialmente con Lugar') {
																									$final = $registro->gestion;
																								}
																								$ii++;
																							}
																							echo $final;
																							//-------
																							?></span></div>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>

			<p>
				<?php
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