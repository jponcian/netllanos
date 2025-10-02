<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 86;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
?>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Transferir Planillas</title>

	<?php

	if ($_POST['CMDTRANSFERIR'] == 'Transferir') {
		$consulta = "SELECT * FROM expedientes_ajustes_ut WHERE status=6;";
		$tabla_datos = mysql_query($consulta);

		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			if ($_POST['CHECK' . $registro_datos->id_expediente] == 'true') {
				// -------------- ACTUALIZACION DEL EXPEDIENTE
				$consulta = "UPDATE expedientes_ajustes_ut SET status=7, fecha_transferencia = date(now()) WHERE id_expediente=" . $registro_datos->id_expediente . ";";
				$tabla = mysql_query($consulta);
				// --------------

				// -------------- ACTUALIZACION DE LA LIQUIDACION
				$consulta = "UPDATE liquidacion SET status = 10, fecha_transferencia_a_liq = date(now()), usuario_transferencia_a_liq=" . $_SESSION['CEDULA_USUARIO'] . ", usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE sector=" . $registro_datos->sector . " AND anno_expediente=" . $registro_datos->anno_expediente . " AND num_expediente=" . $registro_datos->num_expediente . " AND (origen_liquidacion=7 or origen_liquidacion=16);";
				$tabla = mysql_query($consulta);
				// --------------

				// -------------- ACTUALIZACION DEL EXPEDIENTE
				//$consulta="update a_sancion, liquidacion set liquidacion.id_tributo=tributo where a_sancion.id_sancion_ajuste = liquidacion.id_sancion and liquidacion.origen_liquidacion = 16;";
				//$tabla = mysql_query($consulta); 
				// --------------

				echo "<script type=\"text/javascript\">alert('Expediente Transferido Exitosamente!!!');</script>";
				//-------------

				//include "0_actualizar_sancion.php";
			}
		}
	}

	?>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

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

	<form name="form1" method="post">
		<div align="center">
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" colspan="4" bgcolor="#FF0000"><span class="Estilo7"><u>Opciones para Filtrar</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Dependencia:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_expedientes_ajustes WHERE status=6 GROUP BY sector;';
									} else {
										$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_expedientes_ajustes WHERE status=6 AND sector=' . $_SESSION['SEDE_USUARIO'] . ' AND origen_exp=' . $origenUT . ' GROUP BY sector;';
									}
									//-------------
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
									?>
								</select>
							</span></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>A&ntilde;o:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="">
								<select name="OANNO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										if ($_POST['OSEDE'] > 0) {
											$consulta_x = 'SELECT anno_expediente as anno FROM vista_expedientes_ajustes WHERE status=6 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
											//		echo $consulta_x ;
										}
									} else {
										if ($_POST['OSEDE'] > 0) {
											$consulta_x = 'SELECT anno_expediente as anno FROM vista_expedientes_ajustes WHERE status=6 AND sector=0' . $_POST['OSEDE'] . ' AND origen_exp=' . $origenUT . ' GROUP BY anno;';
											echo $consulta_x;
										}
									}
									//-------------
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OANNO'] == $registro_x['anno']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
									}

									?>
								</select>
							</span></div>
					</td>
				</tr>
			</table>
			<table class="formateada" width="50%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="10" align="center">
							<p class="Estilo7"><u>Datos del Contribuyente</u></p>
						</td>
					</tr>
					<tr>
						<th width="6%" height=27>
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
							</td>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Anno</strong>:</div>
							</td>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Exp</strong>:</div>
							</td>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Rif</strong>:</div>
							</td>
						<th width="57%">
							<div align="center" class="Estilo8"><strong>Contribuyente</strong>:</div>
							</td>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Sel.</strong></div>
							</td>
					</tr>
					<?php
					if ($_POST['OANNO'] > 0) {
						$consulta = "SELECT id_expediente, expedientes_ajustes_ut.rif, vista_contribuyentes_direccion.contribuyente, anno_expediente, num_expediente FROM expedientes_ajustes_ut, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_ajustes_ut.rif AND expedientes_ajustes_ut.status=6 AND anno_expediente=" . $_POST['OANNO'] . " AND sector=" . $_POST['OSEDE'] . " AND origen_exp=" . $origenUT . " ORDER BY num_expediente DESC;";
						//echo $consulta;
						//------------------
						if ($_SESSION['ADMINISTRADOR'] > 0) {
							$consulta = "SELECT id_expediente, expedientes_ajustes_ut.rif, vista_contribuyentes_direccion.contribuyente, anno_expediente, num_expediente FROM expedientes_ajustes_ut, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_ajustes_ut.rif AND expedientes_ajustes_ut.status=6 AND anno_expediente=" . $_POST['OANNO'] . " AND sector=" . $_POST['OSEDE'] . " ORDER BY num_expediente DESC;";
						}

						$tablax = mysql_query($consulta);

						$i = 0;

						while ($registrox = mysql_fetch_object($tablax)) {
							$MOSTRAR_BOTON = 'SI';
							$i++;
					?><tr id="fila<?php echo $i; ?>">
								<td>
									<div align="center"><?php echo $i; ?></div>
								</td>
								<td>
									<div align="center"><?php echo $registrox->anno_expediente; ?></div>
								</td>
								<td>
									<div align="center"><?php echo $registrox->num_expediente; ?></div>
								</td>
								<td>
									<div align="center"><a href="planillas_x_contribuyente.php?rif=<?php echo $registrox->rif; ?>&status=9" target="_blank"><?php echo $registrox->rif; ?></a></div>
								</td>
								<td>
									<div align="left"><?php echo $registrox->contribuyente; ?></div>
								</td>
								<td>
									<div align="center"><input name="CHECK<?php echo $registrox->id_expediente; ?>" type="checkbox" value="true" onClick="marcar(this,<?php echo $i; ?>)"></div>
								</td>
							</tr>
					<?php
						}
					}
					?>
			</table>
			<p>
				<?php
				if ($MOSTRAR_BOTON == 'SI') {	?><input type="submit" class="boton" name="CMDTRANSFERIR" value="Transferir"><?php	}
																																?>
			</p>
		</div>
	</form>

	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>