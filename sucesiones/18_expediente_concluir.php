<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 128;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

// ------- PARA BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0) {
	// CONSULTA
	$consulta = "SELECT fecha_conclusion FROM expedientes_sucesiones WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND numero=0" . $_POST['ONUMERO'] . ";";
	$tabla_datos = mysql_query($consulta);
	if ($registro_datos = mysql_fetch_object($tabla_datos)) {
		if ($registro_datos->fecha_conclusion <> '' and $registro_datos->fecha_conclusion <> 'null') {
			$expediente = 'culminado';
			//--------------
			echo "<script type=\"text/javascript\">alert('El Expediente ya fue Concluido!!!');</script>";
		} else {
			$expediente = '';
		}
	}
}

if ($_POST['CMDCONCLUIR'] == "Concluir") {
	// ACTUALIZACION DEL STATUS
	$consulta = "UPDATE expedientes_sucesiones SET certificado=" . $_POST['ORESULTADO'] . ", status=7, fecha_conclusion = date(now()), usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE anno=" . $_POST['OANNO'] . " AND numero=" . $_POST['ONUMERO'] . " AND sector=" . $_POST['OSEDE'] . ";";
	$tabla = mysql_query($consulta);
	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Expediente Concluido Exitosamente!!!');</script>";
	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}
?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Concluir Expediente</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

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
				<td height="35" align="center" class="TituloTabla" colspan="6"><span><u>Datos del Expediente</u></span></td>
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
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_re_exp_sucesiones GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_re_exp_sucesiones WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
									$consulta_x = 'SELECT anno FROM vista_re_exp_sucesiones WHERE sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
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
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM vista_re_exp_sucesiones WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
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
		</table>
		<?php
		if ($_POST['ONUMERO'] > 0 and $expediente == '') {

			$consulta = "SELECT * FROM vista_re_exp_sucesiones WHERE anno=0" . $_POST['OANNO'] . " AND numero=0" . $_POST['ONUMERO'] . " AND sector =0" . $_POST['OSEDE'] . ";";
			$tabla = mysql_query($consulta);
			$registro = mysql_fetch_object($tabla);
			$rif = $registro->rif;
			//----------
		?>
			<table width="45%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de la Declaraci&oacute;n</u></span></td>
				</tr>
				<tr>
					<td width="24%" bgcolor="#CCCCCC">
						<div align="right"><strong>Rif:</strong></div>
					</td>
					<td width="76%"><label>
							<div align="left"><?php echo $registro->rif;	?></div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>
								Razon Social:</strong></div>
					</td>
					<td>
						<div align="left"><?php echo ($registro->contribuyente);	?></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Declaraci&oacute;n N&deg;:</strong></div>
					</td>
					<td><label>
							<div align="left"><?php echo $registro->declaracion;	?></div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>
								Fecha:</strong></div>
					</td>
					<td>
						<div align="left"><?php echo voltea_fecha($registro->fecha_declaracion);	?></div>
					</td>
				</tr>
			</table>
			<table width="45%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Causante </u></span></td>
				</tr>
				<tr>
					<td width="24%" bgcolor="#CCCCCC">
						<div align="right"><strong>Cedula:</strong></div>
					</td>
					<td width="76%"><label>
							<div align="left"><?php echo $registro->cedula;	?></div>
						</label></td>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Cusante:</strong></div>
					</td>
					<td><label>
							<div align="left"><?php echo $registro->contribuyente;	?></div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Fecha Fallecimiento:</strong></div>
					</td>
					<td><label>
							<div align="left"><?php echo voltea_fecha($registro->fecha_fall);	?></div>
						</label></td>
				</tr>
				<tr>
				</tr>
			</table>
			<p></p>
			<table width="30%" border="1" align="center">
				<tr>
					<td align="center" class="TituloTabla" colspan="9"><span><u>Certificado</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo date('d/m/Y'); ?></span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Resultado:</strong></td>
					<td><label><select name="ORESULTADO" size="1" onChange="this.form.submit()">
								<option value="-1"> ->->SELECCIONE<-<- </option>
								<option <?php if ($_POST['ORESULTADO'] == '1') {
											echo 'selected="selected" ';
										} ?>value='1'>SOLVENCIA</option>
								<option <?php if ($_POST['ORESULTADO'] == '2') {
											echo 'selected="selected" ';
										} ?>value='2'>LIBERACION</option>
							</select></label></td>
				</tr>
			</table>
			<p>
				<?php
				if ($_POST['ORESULTADO'] == 1)
				// --- SI ES SOLVENCIA
				{
					////////// INFORMACION DE LA RECEPCION
					$consulta_datos = "SELECT fecha_recepcion, fecha_fall FROM sucesiones_recepcion, expedientes_sucesiones WHERE sucesiones_recepcion.rif = expedientes_sucesiones.rif AND expedientes_sucesiones.rif='" . $rif . "';";
					$tabla_x = mysql_query($consulta_datos);
					$registro_x = mysql_fetch_object($tabla_x);
					$fecha_presentacion = $registro_x->fecha_recepcion;
					$fecha_fall = $registro_x->fecha_fall;
					$fecha_v = dias_feriados($registro_x->fecha_fall, 180);
					$fecha_p = fecha_a_numero($fecha_presentacion);
					$fecha_v = fecha_a_numero($fecha_v);
					// ---------------------
					$consulta = "SELECT id_liquidacion FROM liquidacion WHERE origen_liquidacion = " . $_SESSION['ORIGEN'] . " AND anno_expediente = " . $_POST['OANNO'] . " AND num_expediente = " . $_POST['ONUMERO'] . " AND sector = " . $_POST['OSEDE'] . ";";
					//echo $consulta;
					$tabla_r = mysql_query($consulta);
					$numero_filas = mysql_num_rows($tabla_r);
					//----------------
					if ($numero_filas > 0) {
						//-----
						$consulta = "SELECT id_liquidacion FROM liquidacion WHERE status<>100 and origen_liquidacion = " . $_SESSION['ORIGEN'] . " AND anno_expediente = " . $_POST['OANNO'] . " AND num_expediente = " . $_POST['ONUMERO'] . " AND sector = " . $_POST['OSEDE'] . ";";
						//echo $consulta;
						$tabla_r = mysql_query($consulta);
						$numero_filas = mysql_num_rows($tabla_r);
						//----------------
						if ($numero_filas > 0) {
				?>
			<table width="50%" border="1" align="center">
				<tr>
					<td width="11%" bgcolor="#CCCCCC">
						<div align="center" class="Estilo2"><span class="Estilo15 luz" id="blink"><strong>LA SUCESION POSEE PLANILLAS POR PAGAR!!! </strong></span></div>
					</td>
				</tr>
			</table>
		<?php
						} else {
		?><p>
			<div align="center"><label> <input type="submit" class="boton" name="CMDCONCLUIR" value="Concluir"> </label>
				<input type="hidden" name="OFECHA" value="<?php echo date('Y/m/d'); ?>">
			</div>
			</p> <?php
						}
					} else {
						// POR SI NO POSEE RESOLUCIONES Y EST� FUERA DEL PLAZO
						if ($fecha_p > $fecha_v) {
					?>
			<table width="50%" border="1" align="center">
				<tr>
					<td width="11%" bgcolor="#CCCCCC">
						<div align="center" class="Estilo2"><span class="Estilo15 luz" id="blink"><strong>LA SUCESION NO POSEE RESOLUCIONES Y EST� EXTEMPORANEA!!! </strong></span></div>
					</td>
				</tr>
			</table>
		<?php
						} else {
		?><p>
			<div align="center"><label> <input type="submit" class="boton" name="CMDCONCLUIR" value="Concluir"> </label>
				<input type="hidden" name="OFECHA" value="<?php echo date('Y/m/d'); ?>">
			</div>
			</p> <?php
						}
					}
				}

				if ($_POST['ORESULTADO'] == 2)
				// --- SI ES LIBERACION
				{
					//-----
					$consulta = "SELECT id_liquidacion FROM liquidacion WHERE origen_liquidacion = " . $_SESSION['ORIGEN'] . " AND anno_expediente = " . $_POST['OANNO'] . " AND num_expediente = " . $_POST['ONUMERO'] . " AND sector = " . $_POST['OSEDE'] . ";";
					//echo $consulta;
					$tabla_r = mysql_query($consulta);
					$numero_filas = mysql_num_rows($tabla_r);
					//----------------
					if ($numero_filas > 0) {
					?>
		<table width="50%" border="1" align="center">
			<tr>
				<td width="11%" bgcolor="#CCCCCC">
					<div align="center" class="Estilo2"><span class="Estilo15 luz" id="blink"><strong>LA SUCESION POSEE RESOLUCIONES EN EL SISTEMA!!! </strong></span></div>
				</td>
			</tr>
		</table>
	<?php
					} else {
	?><p>
		<div align="center"><label> <input type="submit" class="boton" name="CMDCONCLUIR" value="Concluir"> </label>
			<input type="hidden" name="OFECHA" value="<?php echo date('Y/m/d'); ?>">
		</div>
		</p> <?php
					}
				}
				?>
</p>
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