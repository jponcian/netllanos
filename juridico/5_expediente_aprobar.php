<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 138;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['ANNO_PRO'] = 0;
$_SESSION['NUM_PRO'] = 0;
$_SESSION['SEDE'] = 0;

if ($_POST['OSEDE'] > 0 and $_POST['OANNO'] > 0 and $_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
}

if ($_POST['CMDAPROBAR'] == "Aprobar") {
	// ACTUALIZACION DEL STATUS DEL EXPEDIENTE
	$consulta = "UPDATE expedientes_juridico SET fecha_aprobacion=date(now()), status=6, usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_expediente=" . $_POST['OID'] . ";";
	$tabla = mysql_query($consulta);
	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Expediente Aprobado Exitosamente!!!');</script>";
	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}
?>
<html>

<head>
	<title>Aprobar Recurso</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<div align="center">
		<p align="center">&nbsp;
			<?php
			include "menu.php";
			?>
	</div>
	<form name="form1" method="post" action="#vista">
		<table width="47%" border="1" align="center">
			<tr>
				<td height="35" align="center" bgcolor="#FF0000" colspan="6"><span class="Estilo7"><u>Selecci�n del Expediente a Aprobar</u></span></td>
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
									$consulta_x = "SELECT expedientes_juridico.sector as sector, z_sectores.nombre as dependencia FROM expedientes_juridico, z_sectores WHERE z_sectores.id_sector = expedientes_juridico.sector and expedientes_juridico.status=4 GROUP BY expedientes_juridico.sector;";
								} else {
									// -------------------------------------
									$consulta_x = "SELECT expedientes_juridico.sector as sector, z_sectores.nombre as dependencia FROM expedientes_juridico, z_sectores WHERE z_sectores.id_sector = expedientes_juridico.sector AND expedientes_juridico.sector = " . $_SESSION['SEDE_USUARIO'] . " AND expedientes_juridico.status=4 GROUP BY expedientes_juridico.sector;";
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
									$consulta_x = "SELECT anno FROM expedientes_juridico WHERE sector = " . $_POST['OSEDE'] . " AND status=4 GROUP BY anno;";
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
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Numero:</strong></div>
				</td>
				<td><label>
						<div align="center"><span class="Estilo1">
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="0">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM expedientes_juridico WHERE status=4 and anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' GROUP BY numero ORDER BY numero DESC;';
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
								</select></span></div>
					</label></td>
			</tr>
		</table>
		<p></p>
		<?php
		if ($_POST['ONUMERO'] > 0) {
		?>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="36" colspan="8" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>A�o:</strong></div>
					</td>
					<td><label>
							<div align="center"><span class="Estilo15">
									<?php
									$consulta = "SELECT * FROM vista_expedientes_juridico WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE'] . ";";
									$tabla = mysql_query($consulta);
									$registro = mysql_fetch_object($tabla);
									//----------
									echo $registro->anno;
									?>
								</span>
						</label>
						<span class="Estilo15">
							<input type="hidden" name="OID" value="<?php echo $registro->id_expediente; ?>">
						</span></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>N&uacute;mero:</strong></div>
					</td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span>
						</label>
						</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Fecha:</strong></div>
					</td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_recepcion); ?></span>
						</label>
						</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Sector:</strong></div>
					</td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo strtoupper($registro->nombre); ?></span>
						</label>
						</div>
					</td>
				</tr>
			</table>
			<table width="50%" border="1" align="center">
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
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->cedula_coordinador); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Coordinador:</strong></td>
					<td><label><span class="Estilo15"><?php
														list($funcionario) = funcion_funcionario(0 + $registro->cedula_coordinador);
														echo $funcionario; ?></span></label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->cedula_ponente); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
					<td><label><span class="Estilo15"><?php
														list($funcionario) = funcion_funcionario(0 + $registro->cedula_ponente);
														echo $funcionario; ?></span></label></td>
				</tr>
			</table>
			<p></p>
			<p>
			<table class="formateada" align="center" width="" border="1">
				<tr>
					<td bgcolor="#FF0000" height="36" colspan="9" align="center">
						<p class="Estilo7"><u>Planillas Recurridas</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>N�</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Liquidacion</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Per&iacute;odo</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Monto</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Cant. U.T.</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Valor U.T.</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Fecha Liquidacion</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Desici&oacute;n</strong></div>
					</td>
				</tr>
				<?php
				$consulta = "SELECT gestion, liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, periodoinicio, periodofinal FROM vista_jur_detalle_planillas WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE'] . " AND status_exp=4 ORDER BY liquidacion;";
				//echo $consulta;
				$tabla = mysql_query($consulta);

				$i = 0;

				while ($registro = mysql_fetch_object($tabla)) {
					$i++;
				?>
					<tr id="fila<?php echo $i; ?>">
						<td>
							<div align="center">
								<?php echo $i;		?>
							</div>
						</td>
						<td>
							<div align="center">
								<?php echo $registro->liquidacion;		?>
							</div>
						</td>
						<td>
							<div align="center">
								<?php echo voltea_fecha($registro->periodoinicio) . ' al ' . voltea_fecha($registro->periodofinal);		?>
							</div>
						</td>
						<td>
							<div align="center">
								<?php echo formato_moneda($registro->monto);		?>
							</div>
						</td>
						<td>
							<div align="center">
								<?php echo formato_moneda($registro->cant_ut);		?>
							</div>
						</td>
						<td>
							<div align="center">
								<?php		//echo unidad_infraccion($registro->fecha_impresion); 
								echo redondea($registro->monto / $registro->cant_ut);		?>
							</div>
						</td>
						<td>
							<div align="center">
								<?php echo voltea_fecha($registro->fecha_impresion);		?>
							</div>
						</td>
						<td>
							<div align="center">
								<?php echo ($registro->gestion);		?>
							</div>
						</td>
					</tr>
				<?php
				}
				?>
			</table>
			<p></p>
			<p> </p>
			<p>
			<div align="center"><label> <input type="submit" class="boton" name="CMDAPROBAR" value="Aprobar">
				</label>
			</div>
			</p>
	</form>
	<a name="vista"></a> <?php
						}
							?>
<?php include "../pie.php"; ?>
<p>&nbsp;</p>
</body>

</html>