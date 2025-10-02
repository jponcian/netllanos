<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Listado Expedientes x Importar a Notificaci�n</title>
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}
	$acceso = 56;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	//--------------------
	$status = 8;
	$status2 = 0;
	//--------------------

	if ($_POST['CMDTRANSFERIR'] == "Recibir") {
		if ($_POST['OFECHA'] <> '') {
			// PARA GUARDAR LA INFORMACI�N
			if ($_POST['OANNO'] > 0) {
				//VERIFICAMOS LOS SELECCIONADOS
				$consulta = "SELECT id_expediente, anno, numero, sector, programa, status, origen_liquidacion, rif, sum(reparo) as reparo, sum(impuesto_omitido) as impuesto_omitido, sum(monto_pagado) as monto_pagado, fecha_notificacion FROM vista_sumario_exp_transferido_suma WHERE sector = " . $_POST['OSEDE'] . " AND anno=" . $_POST['OANNO'] . " AND status=8 AND (fecha_recepcion_sumario IS NULL OR fecha_recepcion_sumario = '0000-00-00') group by rif";
				$tabla = mysql_query($consulta);
				while ($valor = mysql_fetch_object($tabla)) {
					if ($_POST['CHECK' . $valor->id_expediente] == 'true') {
						//BUSCAMOS SI EL EXPEDIENTE TIENE MULTAS POR DF
						$sqldf = "SELECT (monto_bs / concurrencia * especial) AS multasdf FROM liquidacion WHERE anno_expediente = " . $valor->anno . " AND num_expediente = " . $valor->numero . " AND origen_liquidacion = 4 AND sector = " . $valor->sector;
						$resultdf = mysql_query($sqldf);
						$cantidad = mysql_num_rows($resultdf);
						if ($cantidad > 0) {
							$df = mysql_fetch_object($resultdf);
							$multasdf = $df->multasdf;
						} else {
							$multasdf = 0;
						}

						//ACTUALIZAMOS LA RECEPCION DEL EXPEDIENTE EN FISCALIZACION
						$sql_expediente = "UPDATE expedientes_fiscalizacion SET fecha_recepcion_sumario = date(now()), usuario_receptor_sumario = " . $_SESSION['CEDULA_USUARIO'] . " WHERE anno = " . $valor->anno . " AND numero = " . $valor->numero . " AND sector = " . $valor->sector . " AND status = " . $valor->status;
						$tabla_exp = mysql_query($sql_expediente);

						//AGREGAMOS EL EXPEDIENTE A LA TABLA DE EXPEDIENTES_SUMARIO
						//buscamo el ultimo numero de recepcion generado
						$sql_maximo = "SELECT max(numero) as maximo FROM expedientes_sumario WHERE anno = year(date(now()))";
						$resultado = mysql_query($sql_maximo);
						$registro = mysql_fetch_object($resultado);
						if ($registro->maximo > 0) {
							$maximo = $registro->maximo + 1;
						} else {
							$maximo = 1;
						}
						// JEFE DIVISION, SECTOR O UNIDAD (AHORITA SOLO DIVISION DE SUMARIO)
						$consulta_x = "SELECT cedula FROM z_jefes_detalle WHERE division=14;";
						$tabla_x = mysql_query($consulta_x);
						$registro_x = mysql_fetch_object($tabla_x);
						$Jefe_Division = $registro_x->cedula;
						//------------------------
						$insert = "INSERT INTO expedientes_sumario ( sector, anno, numero, origen_liquidacion, rif, anno_expediente_fisc, num_expediente_fisc, fecha_notificacion_acta, fecha_recepcion, programa, monto_reparo, impuesto_omitido, monto_pagado, multa_df, status, usuario, cedula_jefe) VALUES (" . $valor->sector . ",year(date(now()))," . $maximo . "," . $valor->origen_liquidacion . ",'" . $valor->rif . "'," . $valor->anno . "," . $valor->numero . ",'" . $valor->fecha_notificacion . "',date(now()),'" . $valor->programa . "'," . $valor->reparo . "," . $valor->impuesto_omitido . "," . $valor->monto_pagado . "," . $multasdf . ",0," . $_SESSION['CEDULA_USUARIO'] . "," . $Jefe_Division . ")";
						$tabla_i = mysql_query($insert); //echo $insert;
						// MENSAJE
						echo "<script type=\"text/javascript\">alert('Expediente(s) Importados(s) Exitosamente!!!');</script>";
						//-- CAMBIO DE LA DIRECCION
						//echo '<meta http-equiv="refresh" content="0";/>';
					}
				}
			} else {
				echo '<script type="text/javascript">alert("No ha seleccionado el A�o");</script>';
			}
		} else {
			echo '<script type="text/javascript">alert("No ha Ingresado la Fecha de Recepci�n");</script>';
		}
	}
	?>

</head>
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

	.Estilo8 {
		color: #000000
	}
	-->
</style>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post">
		<table width="65%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="45" colspan="5" align="center">
						<p class="Estilo7"><u>Expedientes por Importar a Sumario Administrativo</u></p>
					</td>
				</tr>

			</tbody>
		</table>

		<p></p>
		<table width="65%" border=1 align=center>
			<tr>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7 Estilo8">Dependencia =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7"><span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
									$origen = '';
									$consulta_x = "SELECT expedientes_fiscalizacion.sector, z_sectores.nombre FROM expedientes_fiscalizacion INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.status = 8 AND expedientes_fiscalizacion.fecha_recepcion_sumario IS NULL OR expedientes_fiscalizacion.fecha_recepcion_sumario = '0000-00-00' GROUP BY expedientes_fiscalizacion.sector"; //echo $consulta_x;
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									// -------------------------------------
									$consulta_x = "SELECT expedientes_fiscalizacion.sector, z_sectores.nombre FROM expedientes_fiscalizacion INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.status = 8 AND expedientes_fiscalizacion.sector = " . $_SESSION['SEDE_USUARIO'] . " AND expedientes_fiscalizacion.fecha_recepcion_sumario IS NULL OR expedientes_fiscalizacion.fecha_recepcion_sumario = '0000-00-00' GROUP BY expedientes_fiscalizacion.sector";
									$tabla_x = mysql_query($consulta_x);
									if ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								}
								?>
							</select>
						</span></p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7 Estilo8">A&ntilde;o =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7"><span class="Estilo1">
							<select name="OANNO" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									$consulta_x = "SELECT anno FROM expedientes_fiscalizacion WHERE sector=" . $_POST['OSEDE'] . " and status=8 AND (fecha_recepcion_sumario IS NULL OR fecha_recepcion_sumario = '0000-00-00') GROUP BY anno";
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
						</span></p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7 Estilo8">Fecha de Recepci&oacute;n =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7">
						<label></label>
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>" />
					</p>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="65%" border=1 align=center>
			<tr>
				<td bgcolor="#CCCCCC" height=27>
					<div align="center" class="Estilo8"><strong>Sel.</strong></div>
				</td>
				<td bgcolor="#CCCCCC" height=27>
					<div align="center"><strong>Num</strong></div>
				</td>
				<td bgcolor="#CCCCCC" height=27>
					<div align="center"><strong>Rif</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>A�o</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N&uacute;mero</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Vcto. 15 dias</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Dias Restantes</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Origen</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Sector</strong></div>
				</td>
			</tr>
			<?php
			if ($_POST['OANNO'] > 0) {
				$consulta_x = "SELECT id_expediente, rif, contribuyente, anno, numero, area, nombre, origen_liquidacion, sector, numacta, fecha_notificacion FROM vista_sumario_exp_transferido_suma WHERE sector = " . $_POST['OSEDE'] . " AND anno=" . $_POST['OANNO'] . " AND status=8 AND (fecha_recepcion_sumario IS NULL OR fecha_recepcion_sumario = '0000-00-00') GROUP BY sector, origen_liquidacion, anno, numero"; //echo $consulta_x;
				$tabla = mysql_query($consulta_x);

				$i = 1;
				$alerta = 0;
				while ($registro = mysql_fetch_object($tabla)) {
					$fechaactual = date('Y-m-d');
					$fechaactual = strtotime($fechaactual);
					$fechavcto = strtotime(dias_feriados($registro->fecha_notificacion, 15));
					$resto = ($fechavcto - $fechaactual) / 86400;
					if ($resto < 1) {
						$resto = 0;
						$color = '#FFFF00';
						$alerta = 1;
					} else {
						$color = '#FFFFFF';
					}
					echo '<tr bgcolor="' . $color . '"><td height=27><div align="center" class="Estilo8"><span class="Estilo15"><input type="checkbox" name="CHECK' . $registro->id_expediente . '" value="true"></span></div></td>';
					echo '<td ><div align="center" class="Estilo5"><div align="center">' . $i . '</div></div></td>';
					echo '<td ><div align="center" class="Estilo5"><div align="center"><a href="0_expediente.php?rif=' . $registro->rif;
					echo '&num=';
					echo $registro->numero;
					echo '&anno=';
					echo $registro->anno;
					echo '&sector=';
					echo $registro->sector;
					echo '&origen=';
					echo $registro->origen_liquidacion;
					echo '&status=';
					echo $status;
					echo '&status2=';
					echo $status2;
					echo '" target=_BLANK>';
					echo $registro->rif;
					echo '</a></div></td><td ><div align="left">';
					echo $registro->contribuyente;
					echo '</div></div></td><td><div align="center" class="Estilo5">';
					echo $registro->anno;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $registro->numero;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo voltea_fecha(dias_feriados($registro->fecha_notificacion, 15));
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $resto;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $registro->area;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $registro->nombre;
					echo '</div></td></tr>';
					$i++;
				}
			}
			?>
		</table>
		<?php
		if ($alerta == 1) { ?>
			<table width="65%" border=1 align=center>
				<tr>
					<td align="center">
						<div id="blink" class="luzon">Los Expedientes en celdas resaltadas se encuentran con lapso de los 15 d�as VENCIDO</div>
					</td>
				</tr>
			</table>
		<?php
		} ?>
		<?php if ($i > 1) {  ?>
			<p align="center"><input type="submit" class="boton" name="CMDTRANSFERIR" value="Recibir"></p><?php
																										}	 ?>
	</form>
	<?php
	include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>