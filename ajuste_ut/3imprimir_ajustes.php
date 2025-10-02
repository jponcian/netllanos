<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 84;
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
	<title>Imprimir Resoluciones</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<div align="center">
		<p align="center">
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
									<option value="-1">--> Seleccione <--< /option>
											<?php
											if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
												$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_expedientes_ajustes WHERE status>=1 GROUP BY sector;';
											} else {
												$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_expedientes_ajustes WHERE status>=1 AND sector=' . $_SESSION['SEDE_USUARIO'] . ' AND origen_exp=' . $origenUT . ' GROUP BY sector;';
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
											$consulta_x = 'SELECT anno_expediente as anno FROM vista_expedientes_ajustes WHERE status>=1 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
											//echo $consulta_x ;
										}
									} else {
										if ($_POST['OSEDE'] > 0) {
											$consulta_x = 'SELECT anno_expediente as anno FROM vista_expedientes_ajustes WHERE status>=1 AND sector=0' . $_POST['OSEDE'] . ' AND origen_exp=' . $origenUT . ' GROUP BY anno ORDER BY anno DESC';
											// echo $consulta_x ;
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
						<th height=27>
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
							</td>
						<th height=27>
							<div align="center" class="Estilo8"><strong>Rif</strong>:</div>
							</td>
						<th height=27>
							<div align="center" class="Estilo8"><strong>Expediente:</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Contribuyente</strong>:</div>
							</td>
						<th height=27>
							<div align="center" class="Estilo8"><strong>Resoluci&oacute;n</strong></div>
							</td>
					</tr>
					<?php
					if ($_POST['OANNO'] > 1 and $_POST['OSEDE'] > 0) {
						$consulta = "SELECT id_expediente, expedientes_ajustes_ut.rif, vista_contribuyentes_direccion.contribuyente, anno_expediente, num_expediente FROM expedientes_ajustes_ut, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_ajustes_ut.rif AND expedientes_ajustes_ut.status>=1 AND anno_expediente=" . $_POST['OANNO'] . " AND sector=" . $_POST['OSEDE'] . " AND origen_exp=" . $origenUT . " ORDER BY anno_expediente DESC, num_expediente DESC;";
						//----- POR SI ES EL ADMINISTRADOR
						if ($_SESSION['ADMINISTRADOR'] > 0) {
							$consulta = "SELECT id_expediente, expedientes_ajustes_ut.rif, vista_contribuyentes_direccion.contribuyente, anno_expediente, num_expediente FROM expedientes_ajustes_ut, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_ajustes_ut.rif AND expedientes_ajustes_ut.status>=1 AND anno_expediente=" . $_POST['OANNO'] . " AND sector=" . $_POST['OSEDE'] . " ORDER BY anno_expediente DESC, num_expediente DESC;";
						}
						$tablax = mysql_query($consulta);

						$i = 0;

						while ($registrox = mysql_fetch_object($tablax)) {
							$MOSTRAR_BOTON = 'SI';
							$i++;
					?><tr>
								<td>
									<div align="center"><?php echo $i; ?></div>
								</td>
								<td>
									<div align="center"><a href="planillas_x_contribuyente.php?rif=<?php echo $registrox->rif; ?>&id=<?php echo $registrox->id_expediente ?>&status=9" target="_blank">
											<?php echo $registrox->rif; ?></a></div>
								</td>
								<td>
									<div align="center">
										<?php echo $registrox->num_expediente; ?></div>
								</td>
								<td>
									<div align="left">
										<?php echo $registrox->contribuyente; ?></div>
								</td>
								<td>
									<div align="center"><a href="formatos/resolucion.php?id=<?php echo $registrox->id_expediente; ?>" target="_blank">Resolucion</a></div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</form>

	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>