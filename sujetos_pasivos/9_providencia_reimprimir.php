<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 14;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['VARIABLE'] = 'NO';

$_SESSION['ANNO'] = $_POST['OANNO'];
$_SESSION['NUMERO'] = $_POST['ODESDE'];
$_SESSION['FIN'] = $_POST['OHASTA'];
$_SESSION['SEDE'] = $_POST['OSEDE'];
$_SESSION['VARIABLE'] = 'SI';

?>
<html>

<head>
	<title>Reimprimir Providencia</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
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
	<form name="form1" method="post" action="">
		<div align="center">

			<table width="47%" border="1" align="center">
				<tr>
					<td height="35" align="center" class="TituloTabla" colspan="4"><span><u>Datos de la Providencia</u></span></td>
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
									if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
										$consulta_x = 'SELECT sector, nombre FROM vista_ce_providencias GROUP BY sector;';
									} else {
										$consulta_x = 'SELECT sector, nombre FROM vista_ce_providencias WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
									}
									//------------------
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
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
										$consulta_x = 'SELECT anno FROM vista_ce_providencias WHERE sector =0' . $_POST['OSEDE'] . ' and motivo_anulacion is null GROUP BY anno ORDER BY anno DESC;';
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
			</table>
			<table width="47%" border="1" align="center">
				<tr>
					<td width="15%" bgcolor="#CCCCCC">
						<div align="center"><strong>Desde:</strong></div>
					</td>
					<td width="28%"><label>
							<div align="center"><span class="Estilo1">
									<select name="ODESDE" size="1" onChange="this.form.submit()">
										<option value="-1">Seleccione</option>
										<?php
										if ($_POST['OANNO'] > 0) {
											$consulta_x = 'SELECT numero FROM vista_ce_providencias WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' and motivo_anulacion is null ORDER BY numero DESC;';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['ODESDE'] == $registro_x['numero']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
											}
										}
										?>
									</select>
								</span></div>
						</label></td>
					<td width="21%" bgcolor="#CCCCCC">
						<div align="center"><strong>
								Hasta:</strong></div>
					</td>
					<td width="36%"><label>
							<div align="center"><span class="Estilo1">
									<select name="OHASTA" size="1" onChange="this.form.submit()">
										<option value="-1">Seleccione</option>
										<?php
										if ($_POST['ODESDE'] > 0) {
											//--------
											if (($_POST['ODESDE'] > $_POST['OHASTA']) or ($_POST['OHASTA'] == -1)) {
												$_POST['OHASTA'] = $_POST['ODESDE'];
											}
											//--------
											$consulta_x = 'SELECT numero FROM vista_ce_providencias WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['OHASTA'] == $registro_x['numero']) {
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
			</table>
			<p></p>
		</div>
	</form>
	<div align="center">

		<?php
		if ($_POST['OHASTA'] > 0) {
			echo '<form name="form2" method="post" action="formatos/providencia.php?sector=0' . $_POST['OSEDE'] . '&anno=' . $_POST['OANNO'] . '&num1=' . $_POST['ODESDE'] . '&num2=' . $_POST['OHASTA'] . '" target="_blank">';
			echo '<input type="submit" class="boton" name="CMDPROVIDENCIA" value="Ver Providencia"></form>	';
		}
		?>
	</div>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>