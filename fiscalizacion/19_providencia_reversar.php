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
	$consulta = "SELECT rif, ci_supervisor, ci_fiscal1, status FROM expedientes_fiscalizacion WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND numero=0" . $_POST['ONUMERO'] . ";";
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
		if ($registro_datos->$_SESSION['ADMINISTRADOR'] = 1)
		//if ($registro_datos->ci_supervisor==$_SESSION['CEDULA_USUARIO'] or $registro_datos->ci_fiscal1==$_SESSION['CEDULA_USUARIO'] or $_SESSION['ADMINISTRADOR']>0)
		{
			$autorizado = 'SI';
		} else {
			$autorizado = 'NO';
			echo "<script type=\"text/javascript\">alert('���No posee Autorizaci�n sobre esta Providencia!!!');</script>";
		}
		//-----------
	}
}

if ($_POST['CMDCONCLUIR'] == "Reversar") {
	$consultabuscar = "select status from expedientes_fiscalizacion WHERE sector=0" . $_POST['OSEDE'] . " AND anno=0" . $_POST['OANNO'] . " AND numero=0" . $_POST['ONUMERO'] . ";";
	$tabla_00 = mysql_query($consultabuscar);
	$registro_x = mysql_fetch_array($tabla_00);
	$statusbd = $registro_x['status'];

	if (($_POST['txtstatus']) > $statusbd) {
		echo "<script type=\"text/javascript\">alert('El Estatus no puede ser Superior o igual al actual');</script>";
	} else {
		$status1 = $_POST['txtstatus'];
		// ACTUALIZACION DEL STATUS DE LA PROVIDENCIA
		$consulta = "UPDATE expedientes_fiscalizacion SET status='$status1', fecha_conclusion = null, fecha_reverso=DATE(NOW()), usuario_reverso=" . $_SESSION['CEDULA_USUARIO'] . " WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector=" . $_SESSION['SEDE'] . ";";
		$tabla = mysql_query($consulta);
		// MENSAJE
		echo "<script type=\"text/javascript\">alert('Providencia Reversada Exitosamente!!!');</script>";
		//-- CAMBIO DE LA DIRECCION
		echo '<meta http-equiv="refresh" content="0";/>';
	}
}

?>

<html>


<style>
	.redondeado {
		border-radius: 25px;
		border: 4px solid 339c;
	}

	.redondeadotxt {
		border-radius: 50px;
		width: 50;
		height: 30;
		style-align: center;
	}

	.redondeadotxt1 {
		border-radius: 50px;
		width: 50;
		height: 5;
		style-align: center;
	}
</style>

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

		//include "menu.php";
		?>
	</p>
	<form name="form1" method="post" action="#vista">
		<table cellspacing="2" cellpadding="2" width="60%" border="0" align="center">
			<tr>
				<td height="35" align="center" class="TituloTabla" colspan="6"><span><u>Datos de la Providencia para el Reverso</u></span></td>
			</tr>
			<tr>
				<td class="redondeado" width="30%" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia:</strong></div>
				</td>
				<td class="redondeadotxt1" bgcolor="#FFFFFF">
					<div align="center">
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
						</span>
					</div>
				</td>
				<td class="redondeado" width="35%" bgcolor="#CCCCCC">
					<div align="center"><strong>A&ntilde;o:</strong></div>
				</td>
				<td class="redondeadotxt1" bgcolor="#FFFFFF">
					<div align="center">
						<select name="OANNO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							if ($_POST['OSEDE'] > 0) {
								if ($_SESSION['SEDE_USUARIO'] == 1) {
									$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE status <=4 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
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
						</span>
					</div>
				</td>
				<td width="35%" class="redondeado" bgcolor="#CCCCCC">
					<div align="center"><strong>Numero:</strong></div>
				</td>
				<td class="redondeadotxt1" width="36%"><label>
						<div align="center">
							<select name="ONUMERO" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_POST['OANNO'] > 0) {
									$consulta_x = 'SELECT numero, status FROM expedientes_fiscalizacion WHERE status <=4 AND anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										$status = $registro_x['status'];
										echo '<option ';
										if ($_POST['ONUMERO'] == $registro_x['numero']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
									}
								}
								?>
							</select></span>
						</div>
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
			<table cellspacing="2" cellpadding="2" width="60%" border="3" align="center">
				<tr>
					<td align="center" class="TituloTabla" colspan="10"><span><u>Datos de la Providencia</u></span></td>
				</tr>
				<tr>
					<td align="center" class="TituloTabla1" colspan="10"><span><u>TABLA AFECTADA (EXPEDIENTES_FISCALIZACION) Leyenda (0:Emitida - 1: Impresa - 2:Asignada - 3: Notificada - 4: Concluida - 5: Aprob. Superv - 6: Aprob. Coord - 7:Liquidacion</u></span></td>
				</tr>
				<tr>
					<td width="10%" class="redondeado" bgcolor="#CCCCCC">
						<div align="center"><strong>A#o:</strong></div>
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
					<td width="5%" class="redondeado" bgcolor="#CCCCCC">
						<div align="center"><strong>N&uacute;mero:</strong></div>
					</td>
					<td width="5%"><label>
							<div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span>
						</label>
						</div>
					</td>
					<td width="5%" class="redondeado" bgcolor="#CCCCCC">
						<div align="center"><strong>Estatus:</strong></div>
					</td>
					<td width="5%"><label>
							<div align="center"><span class="Estilo15"> <input type="text" class="redondeadotxt" style="background:violet" size="5" color="red" border="4" name="txtstatus" value="<?php echo $registro->status; ?>" required></span>
						</label>
						</div>
					</td>
					<td width="10%" class="redondeado" bgcolor="#CCCCCC">
						<div align="center"><strong>Fecha:</strong></div>
					</td>
					<td width="10%"><label>
							<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_emision); ?></span>
						</label>
						</div>
					</td>
					<td width="10%" class="redondeado" bgcolor="#CCCCCC">
						<div align="center"><strong>Sector:</strong></div>
					</td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo strtoupper($registro->nombre); ?></span>
						</label>
						</div>
					</td>

				</tr>

			</table>
			<table cellspacing="2" cellpadding="2" width="60%" border="4" align="center">
				<tr>
					<td class="redondeado" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_rif($registro->rif); ?></span></div>
						</label></td>
					<td class="redondeado" bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->contribuyente; ?></span></label></td>
				</tr>
				<tr>
					<td class="redondeado" bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_supervisor); ?></span></div>
						</label></td>
					<td class="redondeado" bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->Nombres . " " . $registro->Apellidos; ?></span></label></td>
				</tr>
				<tr>
					<td class="redondeado" bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_fiscal1); ?></span></div>
						</label></td>
					<td class="redondeado" bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
					<td><label><span class="Estilo15"><?php echo $registro->Nombres1 . " " . $registro->Apellidos1; ?></span></label></td>
				</tr>

			</table>
			<p></p>
			<center>
				<marquee bgcolor="#ff7070" height=20 width=50% align=bottom scrolldelay=150> Debes ubicarte en el texto ESTATUS y degradar el digito </marquee>
			</center>
			<p>
			<p>
			<div align="center"><label> <input type="submit" class="boton" name="CMDCONCLUIR" value="Reversar"> </label></div>
			</p>
			<p>
			<div align="center"><label><a href="../index.php" class="boton">...VOLVER...</a> </label></div>
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