<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 68;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

/*$_SESSION['ANNO_PRO'] = -1;
$_SESSION['NUM_PRO'] = -1;
$_SESSION['SEDE_USUARIO'] = -1;*/

if ($_POST['OSEDE'] > 0 and $_POST['OANNO'] > 0 and $_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE_USUARIO'] = $_POST['OSEDE'];
}

if ($_POST['CMDCONCLUIR'] == "Concluir") {
	//----- INFORMACION DE LA PROVIDENCIA
	include "0_buscar_acta_y_prov.php";
	//-----------
	if ($_POST['OFECHA'] <> "") {
		if (strtotime($_POST['OFECHA']) < strtotime($fecha_registro)) {
			echo "<script type=\"text/javascript\">alert('���La Fecha de Conlusi�n no puede ser menor a la Fecha de Registro del Expediente!!!');</script>";
		} else {
			// BUSCAR SI TIENE ACTA Y FUE NOTIFICADA
			include "0_buscar_acta_y_prov.php";
			//-----------	
			if ($status_prov == 0) {
				// ACTUALIZACION DEL STATUS DEL EXPEDIENTE
				$consulta = "UPDATE expedientes_rif SET Status=4, fecha_conclusion = '" . $_POST['OFECHA'] . "', Usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND Sector=" . $_SESSION['SEDE_USUARIO'] . ";";
				$tabla = mysql_query($consulta);
				// MENSAJE
				echo "<script type=\"text/javascript\">alert('Expediente Concluido Exitosamente!!!');</script>";
				//-- CAMBIO DE LA DIRECCION
				echo '<meta http-equiv="refresh" content="0";/>';
			}
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Debe incluir la fecha de Conclusi�n!!!');</script>";
	}
}
?>
<html>

<head>
	<title>Concluir Expediente</title>
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
				<td height="35" align="center" bgcolor="#FF0000" colspan="6"><span class="Estilo7"><u>Selecci�n del Expediente a Concluir </u></span></td>
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
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_rif WHERE Status=0 GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_rif WHERE status=0 and sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
									$consulta_x = 'SELECT anno FROM vista_exp_rif WHERE sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
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
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM vista_exp_rif WHERE status=0 AND anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
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
							</span></div>
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
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
				</tr>
				<tr>
					<td width="10%" bgcolor="#CCCCCC">
						<div align="center"><strong>A�o:</strong></div>
					</td>
					<td width="10%"><label>
							<div align="center"><span class="Estilo15">
									<?php
									$consulta = "SELECT * FROM vista_exp_rif WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE_USUARIO'] . ";";
									$tabla = mysql_query($consulta);
									$registro = mysql_fetch_object($tabla);
									//----------
									echo $registro->anno;
									$tipo = $registro->tipo;
									?>
								</span>
						</label>
						</div>
					</td>
					<td width="10%" bgcolor="#CCCCCC">
						<div align="center"><strong>N&uacute;mero:</strong></div>
					</td>
					<td width="10%"><label>
							<div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span>
						</label>
						</div>
					</td>
					<td width="10%" bgcolor="#CCCCCC">
						<div align="center"><strong>Fecha:</strong></div>
					</td>
					<td width="10%"><label>
							<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->FechaRegistro); ?></span>
						</label>
						</div>
					</td>
					<td width="10%" bgcolor="#CCCCCC">
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
					<td bgcolor="#CCCCCC"><strong>rif: </strong></td>
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
			<table width="30%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Resultado</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
					<td><label><?php echo date("Y/m/d"); ?>
							<input onclick='javascript:scwShow(this,event);' type="hidden" name="OFECHA" size="8" readonly value="<?php echo date("Y/m/d"); ?>" />
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Resultado:</strong></td>
					<td><label><select name="ORESULTADO" size="1" onChange="this.form.submit()">
								<option value="-1"> ->->SELECCIONE<-<- </option>
								<option <?php if ($_POST['ORESULTADO'] == '1') {
											echo 'selected="selected" ';
										} ?>value='1'>CONFORME</option>
								<option <?php if ($_POST['ORESULTADO'] == '2') {
											echo 'selected="selected" ';
										} ?>value='2'>PRODUCTIVO</option>
							</select></label></td>
				</tr>
			</table>
			<p>
				<?php
				if ($_POST['ORESULTADO'] == 1)
				// --- SI ES CONFORME
				{
					//----- MONTO SANCION LIQUIDACIONES
					$consulta = "SELECT Sum(vista_sanciones_aplicadas.monto_bs) as monto FROM vista_sanciones_aplicadas WHERE origen_liquidacion = " . $_SESSION['ORIGEN'] . " AND anno_expediente = " . $_SESSION['ANNO_PRO'] . " AND num_expediente = " . $_SESSION['NUM_PRO'] . " AND sector = " . $_SESSION['SEDE_USUARIO'] . ";";
					$tabla_r = mysql_query($consulta);
					$registro_r = mysql_fetch_object($tabla_r);

					/*
	//----- MONTO ACTAS DE REPARO
	$consulta = "SELECT Sum(fis_actas_detalle.reparo) as monto FROM fis_actas_detalle INNER JOIN fis_actas ON fis_actas_detalle.id_acta = fis_actas.id_acta WHERE fis_actas.anno_prov = ".$_SESSION['ANNO_PRO']." AND fis_actas.num_prov = ".$_SESSION['NUM_PRO']." AND fis_actas.id_sector = ".$_SESSION['SEDE_USUARIO'].";";
	$tabla_r2 = mysql_query($consulta);
	$registro_r2 = mysql_fetch_object($tabla_r2);
	*/

					//-------------------------------
					if ($registro_r->monto > 0 or $registro_r2->monto > 0) {
				?>
			<table width="50%" border="1" align="center">
				<tr>
					<td width="11%" bgcolor="#CCCCCC">
						<div align="center" class="Estilo2"><span class="Estilo15 luz" id="blink"><strong>EL EXPEDIENTE POSEE MULTA NO PUEDE ESTAR CONFORME!!!</strong></span></div>
					</td>
				</tr>
			</table>
		<?php
					} else {
						$_SESSION['VARIABLE'] = 0;
		?><p>
			<div align="center"><label> <input type="submit" class="boton" name="CMDCONCLUIR" value="Concluir"> </label></div>
			</p> <?php
					}
				}

				//-----------------------
				if ($_POST['ORESULTADO'] == 2)
				// --- SI ES CONFORME
				{
					//----- MONTO SANCION LIQUIDACIONES
					$consulta = "SELECT Sum(vista_sanciones_aplicadas.monto_bs) as monto FROM vista_sanciones_aplicadas WHERE origen_liquidacion = " . $_SESSION['ORIGEN'] . " AND anno_expediente = " . $_SESSION['ANNO_PRO'] . " AND num_expediente = " . $_SESSION['NUM_PRO'] . " AND sector = " . $_SESSION['SEDE_USUARIO'] . ";";
					$tabla_r = mysql_query($consulta);
					$registro_r = mysql_fetch_object($tabla_r);

					/*
	//----- MONTO ACTAS DE REPARO
	$consulta = "SELECT Sum(fis_actas_detalle.reparo) as monto FROM fis_actas_detalle INNER JOIN fis_actas ON fis_actas_detalle.id_acta = fis_actas.id_acta WHERE fis_actas.anno_prov = ".$_SESSION['ANNO_PRO']." AND fis_actas.num_prov = ".$_SESSION['NUM_PRO']." AND fis_actas.id_sector = ".$_SESSION['SEDE_USUARIO'].";";
	$tabla_r2 = mysql_query($consulta);
	$registro_r2 = mysql_fetch_object($tabla_r2);
	*/

					//-------------------------------
					if ($registro_r->monto < 1 and $registro_r2->monto < 1) {
					?>
			<table width="50%" border="1" align="center">
				<tr>
					<td width="11%" bgcolor="#CCCCCC">
						<div align="center" class="Estilo2"><span class="Estilo15 luz" id="blink"><strong>EL EXPEDIENTE NO POSEE SANCIONES NO PUEDE SER PRODUCTIVO!!!</strong></span></div>
					</td>
				</tr>
			</table>
	<?php
					} else {
						$_SESSION['VARIABLE'] = 0;
						echo '<p>  <div align="center"><label>  <input type="submit" class="boton" name="CMDCONCLUIR" value="Concluir">  </label></div></p>';
					}
				}
	?>
	</p>
	<p>
	<?php
		}
	?>
	</p>
	</form>
	<a name="vista"></a>
	<?php include "../pie.php"; ?>
	<p>&nbsp;</p>
</body>

</html>