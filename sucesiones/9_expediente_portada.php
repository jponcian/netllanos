<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 35;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
$consulta_xx = 'UPDATE sucesiones_recepcion, expedientes_sucesiones SET
expedientes_sucesiones.declaracion = sucesiones_recepcion.declaracion,
expedientes_sucesiones.fecha_declaracion = sucesiones_recepcion.fecha_declaracion
WHERE sucesiones_recepcion.rif = expedientes_sucesiones.rif 
AND expedientes_sucesiones.sector = sucesiones_recepcion.sector 
and expedientes_sucesiones.declaracion is null;';
$tabla_xx = mysql_query($consulta_xx);
?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<title>Imprimir Auto de Apertura</title>
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
			$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
			$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
			$_SESSION['SEDE'] = $_POST['OSEDE'];
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
		<?php
		} ?>

	</form>
	<a name="vista"></a>
	<form name="form2" method="post" action="formatos/auto_apertura.php" target="_blank">
		<?php
		if ($_POST['ONUMERO'] > 0 and $expediente == '') {
		?>
			<p align="center"><input type="submit" class="boton" value="Ver Auto Apertura"></p>
			<p align="center">&nbsp;</p>
		<?php
		} ?>
	</form>
	<?php include "../pie.php"; ?>
	<p>&nbsp;</p>
</body>

</html>