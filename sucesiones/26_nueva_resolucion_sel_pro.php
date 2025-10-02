<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 89;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['OSEDE'] > 0 and $_POST['OANNO'] > 0 and $_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
}

if ($_POST['CMDAPROBAR'] == "Emitir Nueva Resolucion") {
	//------- PARA BUSCAR EL EXPEDIENTE
	include "0_buscar_acta_y_prov.php";

	//------- PARA BUSCAR LA RESOLUCION
	$consulta_y = "SELECT id_resolucion FROM resoluciones WHERE id_sector=" . $_SESSION['SEDE'] . " AND id_origen=" . $_SESSION['ORIGEN'] . " AND anno_expediente=" . $_SESSION['ANNO_PRO'] . " AND num_expediente=" . $_SESSION['NUM_PRO'] . ";";
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	$id_resolucion = $registro_y->id_resolucion;

	// COPIA DE LOS DATOS DEL EXPEDIENTE
	$consulta = "INSERT INTO `expedientes_sucesiones_historial` (cedula, sucesion, fecha_fall, id_resolucion, `numero` ,`anno` ,`rif` ,`fecha_registro` ,`coordinador` ,`funcionario` ,`usuario` ,`sector` ,`status` ,`fecha_aprobacion` ,`fecha_transferencia`)
VALUES ( '" . $registro_acta->cedula . "', '" . $registro_acta->sucesion . "', '" . $registro_acta->fecha_fall . "','" . $id_resolucion . "', '" . $_SESSION['NUM_PRO'] . "',  '" . $_SESSION['ANNO_PRO'] . "',  '" . $registro_acta->rif . "',  '" . $registro_acta->fecha_registro . "',  '" . $registro_acta->coordinador . "',  '" . $registro_acta->funcionario . "', '" . $registro_acta->usuario . "',  '" . $_SESSION['SEDE'] . "',  '" . $registro_acta->status . "', '" . $registro_acta->fecha_aprobacion . "',  '" . $registro_acta->fecha_transferencia . "');";
	$tabla = mysql_query($consulta);

	// ACTUALIZACION DEL STATUS DEL EXPEDIENTE
	$consulta = "UPDATE liquidacion SET id_resolucion=" . $id_resolucion . ", usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE sector=" . $_SESSION['SEDE'] . " AND anno_expediente=" . $_SESSION['ANNO_PRO'] . " AND num_expediente=" . $_SESSION['NUM_PRO'] . " AND origen_liquidacion=3;";
	$tabla = mysql_query($consulta);

	// ACTUALIZACION DE LAS LIQUIDFACIONES
	$consulta = "UPDATE expedientes_sucesiones SET status=0, fecha_aprobacion= null, fecha_transferencia = null, usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE indice=" . $registro_acta->indice . ";";
	$tabla = mysql_query($consulta);

	// ACTUALIZACION DE LA RESOLUCION
	$consulta = "UPDATE resoluciones SET historial=1 WHERE id_resolucion=" . $id_resolucion . ";";
	$tabla = mysql_query($consulta);

	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Expediente Actualizado Exitosamente!!!');</script>";

	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}
?>
<html>

<head>
	<title>Generar Nueva Resoluci&oacute;n</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>
</head>
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

	.Estilo15 {
		font-size: 14px;
	}
	-->
</style>

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
				<td height="115" align="center" bgcolor="#FF0000" colspan="6">
					<p class="Estilo7"><u>Selecci�n del Expediente a generar la Resoluci&oacute;n</u></p>
					<p class="Estilo7">(La resoluci&oacute;n anterior debe estar transferida)</p>
				</td>
			</tr>
			<tr>
				<td height="34" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia:</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center"><span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_re_exp_sucesiones where status=7 GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_re_exp_sucesiones WHERE status=7 and sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
									$consulta_x = 'SELECT anno FROM vista_re_exp_sucesiones WHERE status=7 and sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
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
					<div align="center"><strong>N&uacute;mero:</strong></div>
				</td>
				<td><label>
						<div align="center"><span class="Estilo1">
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM vista_re_exp_sucesiones WHERE status=7 and anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
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
			<tr>
				<td colspan="6" align="center">
					<p>
						<?php include "../msg_validacion.php"; ?></p>
				</td>
			</tr>
		</table>
		<p></p>
		<?php
		if ($_POST['ONUMERO'] > 0) {
		?>
			<table width="60%" border="1" align="center">
				<tr>
					<td height="56" colspan="8" align="center" bgcolor="#FF0000">
						<p class="Estilo7"><u>Datos del Expediente</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>A�o:</strong></div>
					</td>
					<td><label>
							<div align="center"><span class="Estilo15">
									<?php
									$consulta = "SELECT * FROM vista_re_exp_sucesiones WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE'] . ";";
									$tabla = mysql_query($consulta);
									$registro = mysql_fetch_object($tabla);
									//----------
									echo $registro->anno;
									?>
								</span>
						</label>
						</div>
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
							<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_registro); ?></span>
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
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->coordinador); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->nombrecoordinador; ?></span></label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->funcionario); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->nombrefuncionario; ?></span></label></td>
				</tr>
			</table>
			<p></p>
			<p>
				<?php //include "0_actas.php";
				?>
			<p></p>
			<?php $mostrarboton = 'NO';
			$serie = "1=1";
			include "../funciones/0_sanciones_aplicadas.php"; ?>
			</p>
			<p>

			</p>
			<p>
			<div align="center"><label> <input type="submit" class="boton" name="CMDAPROBAR" value="Emitir Nueva Resolucion">
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