<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 10;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
}

?>
<html>

<head>
	<title>Imprimir Resoluci&oacute;n</title>
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
		<table width="47%" border="1" align="center">
			<tr>
				<td height="35" align="center" class="TituloTabla" colspan="6"><span><u>Datos de la Providencia</u></span></td>
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
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias where status>=6 GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias WHERE status>=6 and sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
									if ($_SESSION['SEDE_USUARIO'] == 1) {
										$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE status>=6 and sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OANNO'] == $registro_x['anno']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
										}
									} else {
										$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE status>=6 and sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
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
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Numero:</strong></div>
				</td>
				<td><label>
						<div align="center"><span class="Estilo1">
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM expedientes_fiscalizacion WHERE status>=6 and anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
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
					<td height="36" colspan="8" align="center" class="TituloTabla"><span><u>Datos de la Providencia</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>A&ntilde;o:</strong></div>
					</td>
					<td><label>
							<div align="center"><span class="Estilo15">
									<?php
									$consulta = "SELECT * FROM vista_providencias WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE'] . ";";
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
							<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_emision); ?></span>
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
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_supervisor); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->Nombres . " " . $registro->Apellidos; ?></span></label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_fiscal1); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->Nombres1 . " " . $registro->Apellidos1; ?></span></label></td>
				</tr>
			</table>
			<p><a name="vista"></a></p>
			<p>
				<?php include "0_actas.php"; ?>
			<p></p>
			<?php $mostrarboton = 'NO';
			$serie = "1=1";
			include "../funciones/0_sanciones_aplicadas.php"; ?>
			</p>
			<p>

			</p>
			<p> </p>
	</form>

	<?php
			$validar = 0;
			//-------
			$consulta = "SELECT id_liquidacion FROM liquidacion WHERE sector = 0" . $_SESSION['SEDE'] . " AND origen_liquidacion = 4 AND anno_expediente = 0" . $_SESSION['ANNO_PRO'] . " AND num_expediente = 0" . $_SESSION['NUM_PRO'] . ";";
			$tabla = mysql_query($consulta);
			if ($registro = mysql_fetch_object($tabla)) {
				$validar = 1;
			}
			//-------
			$consulta = "SELECT sum(monto_pagado) as monto FROM vista_detalle_actas WHERE id_sector = 0" . $_SESSION['SEDE'] . " AND anno_prov = 0" . $_SESSION['ANNO_PRO'] . " AND num_prov = 0" . $_SESSION['NUM_PRO'] . " GROUP BY id_sector, anno_prov, num_prov;";
			//echo $consulta;
			$tabla = mysql_query($consulta);
			if ($registro = mysql_fetch_object($tabla)) {
				if ($registro->monto > 0) {
					$validar = 1;
				}
			}
			//----------
			if ($validar > 0) {
	?>
		<form name="form2" method="post" action="formatos/resolucion.php" target="_blank">
			<div align="center"><label><input type="submit" class="boton" name="CMDRESOLUCION" value="Ver Resolucion">
				</label></div>
		</form>
<?php
			}
		}
?>

<p>&nbsp;</p>
<?php include "../pie.php"; ?>
<p>&nbsp;</p>
</body>

</html>