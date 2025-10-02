<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 4;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['ANNO_PRO'] = -1;
$_SESSION['NUM_PRO'] = -1;
$_SESSION['SEDE'] = -1;

// ------- PARA BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0) {
	// CONSULTA
	$consulta = "SELECT rif, ci_supervisor, ci_fiscal1 FROM expedientes_fiscalizacion WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND numero=0" . $_POST['ONUMERO'] . ";";
	$tabla_datos = mysql_query($consulta);
	if ($registro_datos = mysql_fetch_object($tabla_datos)) {
		//------------
		$datos = 'SI';
		$rif = $registro_datos->rif;
		//------------
		$_SESSION['SEDE'] = $_POST['OSEDE'];
		$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
		$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
		//------------
		if ($registro_datos->ci_supervisor == $_SESSION['CEDULA_USUARIO'] or $registro_datos->ci_fiscal1 == $_SESSION['CEDULA_USUARIO'] or $_SESSION['ADMINISTRADOR'] > 0) {
			$autorizado = 'SI';
		} else {
			$autorizado = 'NO';
			echo "<script type=\"text/javascript\">alert('���No posee Autorizaci�n sobre esta Providencia!!!');</script>";
		}
		//-----------
	}
}

if ($_POST['CMDCONCLUIR'] == "Reversar") {
	// ACTUALIZACION DEL STATUS DE LA PROVIDENCIA
	$consulta = "UPDATE expedientes_fiscalizacion SET status=3, fecha_conclusion = null, fecha_reverso=DATE(NOW()), usuario_reverso=" . $_SESSION['CEDULA_USUARIO'] . " WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector=" . $_SESSION['SEDE'] . ";";
	$tabla = mysql_query($consulta);
	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Providencia Reversada Exitosamente!!!');</script>";
	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}

?>

<html>

<head>
	<title>Reversar Providencia</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

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
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias WHERE status=4 GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias WHERE status=3 and sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
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
										$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE status=4 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OANNO'] == $registro_x['anno']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
										}
									} else {
										$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
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
				<td width="15%" bgcolor="#CCCCCC">
					<div align="center"><strong>Numero:</strong></div>
				</td>
				<td width="36%"><label>
						<div align="center"><span class="Estilo1">
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM expedientes_fiscalizacion WHERE status=4 AND anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
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
		if ($_POST['ONUMERO'] > 0 and $autorizado == 'SI') {
		?>
			<table width="60%" border="1" align="center">
				<tr>
					<td align="center" class="TituloTabla" colspan="9"><span><u>Datos de la Providencia</u></span></td>
				</tr>
				<tr>
					<td width="10%" bgcolor="#CCCCCC">
						<div align="center"><strong>A�o:</strong></div>
					</td>
					<td width="10%"><label>
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
							<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_emision); ?></span>
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
			<p></p>
			<p>
			<p>
			<div align="center"><label> <input type="submit" class="boton" name="CMDCONCLUIR" value="Reversar"> </label></div>
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