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
	<title>Aprobar Planillas</title>
	<?php

	if ($_POST['CMDAPROBAR'] == 'Aprobar') {
		$consulta = "SELECT rif FROM liquidacion WHERE status=1 AND (origen_liquidacion=7 or origen_liquidacion=16) GROUP BY rif;";
		$tabla_datos = mysql_query($consulta);
		//---------------
		while ($registro_datos = mysql_fetch_object($tabla_datos)) {
			if ($_POST[mayuscula($registro_datos->rif)] == 'true') {
				// ------ GENERAR EXPEDIENTE
				$consulta = "SELECT id_expediente as numero, num_expediente, anno_expediente FROM expedientes_ajustes_ut WHERE rif='" . $registro_datos->rif . "' AND (status=0 or status=1);";
				$tabla = mysql_query($consulta);
				$registro = mysql_fetch_object($tabla);
				// ------ 
				if ($registro->numero <= 0) {
					//------ GENERAR EL NUMERO DE EXPEDIENTE
					$consulta = "SELECT Max(num_expediente)+1 AS maximo FROM expedientes_ajustes_ut WHERE sector =" . $_SESSION['SEDE_USUARIO'] . " AND anno_expediente=Year(date(now()));";
					$tablax = mysql_query($consulta);
					$registrox = mysql_fetch_object($tablax);
					if ($registrox->maximo > 0) {
						$maximo = $registrox->maximo;
					} else {
						$maximo = 1;
					}
					// -------------
					// ----- AGREGAR LOS DATOS DEL EXPEDIENTE
					$consultax = "INSERT INTO expedientes_ajustes_ut (origen_exp, rif, anno_expediente, num_expediente, sector, usuario_aprobador, usuario, fecha_aprobacion, status) VALUES ('" . $origenUT . "', '" . $registro_datos->rif . "' , year(date(now())), '" . $maximo . "', '" . $_SESSION['SEDE_USUARIO'] . "', '" . $_SESSION['CEDULA_USUARIO'] . "', '" . $_SESSION['CEDULA_USUARIO'] . "',  date(now()), 6);";
					// -------------
					$tablax = mysql_query($consultax);
					// ----- ACTUALIZAR EL REGISTRO EN LIQUIDACION
					$consulta = "UPDATE liquidacion SET num_expediente=" . $maximo . ", anno_expediente=Year(date(now())), sector=" . $_SESSION['SEDE_USUARIO'] . " WHERE rif='" . $registro_datos->rif . "' AND status=1 AND (origen_liquidacion=7 or origen_liquidacion=16);";
					$tablax = mysql_query($consulta);
					// -------------
				} else {
					// ----- ACTUALIZAR EL REGISTRO EN LIQUIDACION
					$consulta = "UPDATE liquidacion SET num_expediente=" . $registro->num_expediente . ", anno_expediente=" . $registro->anno_expediente . ", sector=" . $_SESSION['SEDE_USUARIO'] . " WHERE rif='" . $registro_datos->rif . "' AND status=1 AND (origen_liquidacion=7 or origen_liquidacion=16);";
					$tablax = mysql_query($consulta);
					// -------------
					// PARA ACTUALIZAR EL STATUS A POR TRANSFERIR
					$consulta = "UPDATE expedientes_ajustes_ut SET status=6 WHERE rif='" . $registro_datos->rif . "' AND (status=0 or status=1);";
					$tabla = mysql_query($consulta);
					// -------------
				}

				// ------ ACTUALIZAR LA PLANILLA NUEVA
				$consultax = "UPDATE liquidacion SET status = 9, usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE rif='" . $registro_datos->rif . "' AND status=1 AND (origen_liquidacion=7 or origen_liquidacion=16);";
				$tablax = mysql_query($consultax);
				// -------------
				echo "<script type=\"text/javascript\">alert('Planillas del Contribuyente " . $registro_datos->rif . " Aprobadas Exitosamente');</script>";
			}
		}
	}
	?>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>

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
						<div align="center"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										$consulta_x = 'SELECT sector as id_sector, dependencia as nombre FROM vista_sanciones_aplicadas WHERE (status=0 or  status=1) AND (origen_liquidacion=7 or origen_liquidacion=16) GROUP BY sector ORDER BY sector;';
									} else {
										$consulta_x = 'SELECT sector as id_sector, dependencia as nombre FROM vista_sanciones_aplicadas WHERE (status=0 or status=1) AND origen_liquidacion=' . $origenUT . ' AND sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector ORDER BY sector;'; //echo $consulta_x;
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
							<div align="center" class="Estilo8"><strong>N&deg;</strong>
								</td>
							</div>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Rif</strong>:</div>
						<th width="57%">
							<div align="center" class="Estilo8"><strong>Contribuyente</strong>:
								</td>
							</div>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Sel.</strong>
								</td>
							</div>
					</tr>

					<?php

					$consulta = "SELECT contribuyentes.rif, contribuyentes.contribuyente FROM liquidacion, contribuyentes WHERE liquidacion.sector=0" . $_POST['OSEDE'] . "  AND liquidacion.Rif=contribuyentes.Rif AND (status=0 or status=1) AND (origen_liquidacion=7 or origen_liquidacion=16) GROUP BY rif, contribuyente";
					$tablax = mysql_query($consulta);

					$i = 0;

					while ($registrox = mysql_fetch_object($tablax)) {
						$MOSTRAR_BOTON = 'SI';
						$i++;
					?>
						<tr id="fila<?php echo $i; ?>">
							<td>
								<div align="center"><?php echo $i; ?></div>
							</td>
							<td>
								<div align="center"><a href="planillas_x_contribuyente.php?rif=<?php echo $registrox->rif; ?>&status=1" target="_blank"><?php echo $registrox->rif; ?></a></div>
							</td>
							<td>
								<div align="left"><?php echo $registrox->contribuyente; ?></div>
							</td>
							<td>
								<div align="center"><input name="<?php echo $registrox->rif; ?>" type="checkbox" value="true" onClick="marcar(this,<?php echo $i; ?>)"></div>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<p>
				<?php
				if ($MOSTRAR_BOTON == 'SI') {	?><input type="submit" class="boton" name="CMDAPROBAR" value="Aprobar"><?php	}
																														?>
			</p>
		</div>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>