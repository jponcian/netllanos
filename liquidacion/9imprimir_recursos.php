<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

// ----
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 43;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$status = 11;
$status2 = 90;

//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";

//-------------
if ($_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
}
?>
<html>

<head>
	<title>Imprimir Planillas de Juridico</title>
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

		.Estilo16 {
			color: #000000;
			font-weight: bold;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
	<form name="form1" method="post" action="#vista">
		<div align="center">
			<table width="38%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
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

									if ($_SESSION['ADMINISTRADOR'] == 1) {
										$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas_jur WHERE status>=' . $status . ' AND status<=' . $status2 . ' GROUP BY sector';
									} else {
										// --- VALIDACION DE LA SEDE DEL USUARIO
										if ($_SESSION['SEDE_USUARIO'] <> 0) {
											$sede = 'and sector=' . $_SESSION['SEDE_USUARIO'];
										} else {
											$sede = '';
										}
										// -------------------------------------
										$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas_jur WHERE status>=' . $status . ' AND status<=' . $status2 . ' ' . $sede . ' GROUP BY sector;';
									}
									//---------------------
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
									}
									?>
								</select><?php // echo $consulta_x;
											?>
							</span></div>
					</td>

					<td bgcolor="#CCCCCC">
						<div align="center"><strong>A&ntilde;o:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1">
								<?php
								if ($_POST['OSEDE'] > 0) {
									$consulta_x = 'SELECT anno FROM vista_sanciones_aplicadas_jur WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC';
									//echo $consulta_x;
								}
								?>
								<select name="OANNO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
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
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM vista_sanciones_aplicadas_jur WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' GROUP BY numero ORDER BY numero DESC';
										//echo $consulta_x;
									}
									?>
									<select name="ONUMERO" size="1" onChange="this.form.submit()">
										<option value="-1">Seleccione</option>
										<?php
										if ($_POST['OANNO'] > 0) {
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
					<td colspan="8" align="center">
						<p>&nbsp;</p>
					</td>
				</tr>
			</table>
			<p>
				<?php
				if ($_POST['ONUMERO'] > 0) {
					$consulta = 'SELECT vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.direccion FROM vista_sanciones_aplicadas_jur INNER JOIN vista_contribuyentes_direccion ON vista_sanciones_aplicadas_jur.rif = vista_contribuyentes_direccion.rif  WHERE numero=' . $_POST['ONUMERO'] . ' AND anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' GROUP BY vista_sanciones_aplicadas_jur.sector, vista_sanciones_aplicadas_jur.anno, vista_sanciones_aplicadas_jur.numero;';
					$tabla = mysql_query($consulta);
					if ($registro = mysql_fetch_object($tabla)) {
						$rif = $registro->rif;
						$contribuyente = $registro->contribuyente;
						$direccion = $registro->direccion;
					} else {
						$rif = '';
						$contribuyente = '';
						$direccion = '';
					}

				?>
			</p>
			<table width="42%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="27" colspan="2" align="center">
						<p class="Estilo7"><u>Planillas de Liquidacion Generadas al Contribuyente </u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#999999" height=27>
						<div align="center"><strong>Rif</strong></div>
					</td>
					<td bgcolor="#999999">
						<div align="center"><strong>Contribuyente</strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFFF" height=27>
						<div align="center"> <?php echo $rif; ?></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><?php echo $contribuyente; ?></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#999999" colspan="2">
						<div align="center"><strong>Direcci�n</strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFFF" colspan="2">
						<div><?php echo $direccion; ?></div>
					</td>
				</tr>
			</table>
			<p></p>
			<table width="60%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="40" colspan="12" align="center">
							<p class="Estilo7"><u>Sanciones actuales aplicadas al Contribuyente</u></p>
						</td>
					</tr>
					<tr height="35">
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Num</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Concepto</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Tributo</strong></div>
						</td>
						<td width="20%" bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Periodo</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Monto Original BsS.</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>U.T. Original</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Monto BsS.</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>U.T.</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Concurrencia</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Recargo Especial</strong></div>
						</td>
					</tr>
					<?php
					//----------------------- MONTAJE DE LOS DATOS
					$consulta = "SELECT * FROM vista_liquidaciones_jur WHERE status>=$status AND status<=$status2 AND anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector=" . $_SESSION['SEDE'] . ";";
					//echo $consulta;
					$tabla = mysql_query($consulta);

					$i = 0;

					while ($registro = mysql_fetch_object($tabla)) {
						$i++;
					?>
						<tr>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><?php echo $i; ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="left" class="Estilo8"><?php echo $registro->concepto; ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div class="Estilo8"><?php echo $registro->siglas; ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><?php echo voltea_fecha($registro->periodoinicio) . ' al ' . voltea_fecha($registro->periodofinal); ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_bs)); ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_ut)); ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_bs / $registro->concurrencia) * $registro->especial); ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_ut / $registro->concurrencia) * $registro->especial); ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><?php if ($registro->concurrencia > 1) {
																		echo 'Si';
																	} else {
																		echo 'No';
																	} ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center" class="Estilo8"><?php if ($registro->especial > 1) {
																		echo 'Si';
																	} else {
																		echo 'No';
																	} ?></div>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			</p>
			</p>
		</div>
	<?php
				}
	?>
	</form>
	<?php
	if ($_POST['ONUMERO'] > 0) {
	?> <a name="vista"></a>
		<table border="0" align="center">
			<tr>
				<td align="center" bgcolor="#FFFFFF">
					<form name="form3" method="post" action="modelos/planilla_jur.php" target="_blank"><input type="submit" class="boton" name="CMDPLANILLA" value="Planilla"></form>
				</td>
			</tr>
		</table>
	<?php
	}
	?>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>