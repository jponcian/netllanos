<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 49;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//--------------------
$status = 29;
$status2 = 29;
//--------------------

//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";

if ($_POST['CMDTRANSFERIR'] == "Transferir") {
	if ($_POST['OSEDE'] > 0) {
		// CONSULTA DEL MEMO MAXIMO
		$consulta = 'SELECT Max(memo2)+1 AS Maximo FROM liquidacion WHERE sector=0' . $_POST['OSEDE'] . '';
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		if ($registro->Maximo > 0) {
			$memo = $registro->Maximo;
		} else {
			$memo = 1;
		}
		//--------------------------------

		$consulta_x = 'SELECT origen_liquidacion, anno_expediente, num_expediente, sector FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' and sector=0' . $_POST['OSEDE'] . ' GROUP BY sector, origen_liquidacion, anno_expediente, num_expediente';
		$tabla = mysql_query($consulta_x);
		//----------------
		while ($registro = mysql_fetch_object($tabla)) {
			//----------
			if ($_POST['O' . $registro->sector . '-' . $registro->origen_liquidacion . '-' . $registro->anno_expediente . '-' . $registro->num_expediente] == 'true') {
				//-----------
				$consultax = "UPDATE liquidacion SET memo2='" . $memo . "', status = 30, fecha_tranferencia_a_cob = date(now()), usuario_tranf_a_cob = " . $_SESSION['CEDULA_USUARIO'] . ", usuario = " . $_SESSION['CEDULA_USUARIO'] . " WHERE status=29 AND  anno_expediente=" . $registro->anno_expediente . " AND num_expediente=" . $registro->num_expediente . " AND sector=" . $registro->sector . " AND origen_liquidacion=" . $registro->origen_liquidacion . ";";
				$tablax = mysql_query($consultax);
			}
		}
	}

	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Expediente(s) Exportados(s) Exitosamente!!!');</script>";
	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}

?>
<html>

<head>
	<title>Expedientes x Transferir a Cobro</title>
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
	<form name="form1" method="post">
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="45" colspan="5" align="center">
						<p class="Estilo7"><u>Expedientes por transferir a Cobro</u></p>
					</td>
				</tr>

			</tbody>
		</table>

		<p></p>
		<table width="55%" border=1 align=center>
			<tr>
				<td colspan="6" height="27" align="center" bgcolor="#FFFFCC">
					<strong>(Si la Dependencia no est� en la lista es porque no hay informaci&oacute;n para Transferir)</strong>
				</td>
			</tr>
			<tr>
				<td width="22%" height="27" align="center" bgcolor="#999999">
					<p class="Estilo7">Dependencia =&gt;</p>
				</td>
				<td width="18%" align="center">
					<p><span class="Estilo1">
							<?php
							if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
								$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' GROUP BY sector';
							} else { //'.$origen .'
								// -------------------------------------
								$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND sector=' . $_SESSION['SEDE_USUARIO'] . '  GROUP BY sector;';
							}
							// -------------------------------------
							//echo $consulta_x;
							?>
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								// -------------------------------------
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
								}
								?>
							</select>
						</span></p>
				</td>
				<td width="14%" height="27" align="center" bgcolor="#999999">
					<p class="Estilo7 ">Origen =&gt;</p>
				</td>
				<td width="18%" height="27" align="center" bgcolor="#999999">
					<p><span class="Estilo1">
							<select name="OORIGEN" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									// -------------------------------------
									$consulta_x = 'SELECT origen_liquidacion, area FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND sector=0' . $_POST['OSEDE'] . ' AND origen_liquidacion IN ' . $origenes . ' GROUP BY origen_liquidacion';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OORIGEN'] == $registro_x['origen_liquidacion']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['origen_liquidacion'] . '>' . $registro_x['area'] . '</option>';
									}
								}
								?>
							</select>
						</span></p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7">Transferencia =&gt;</p>
				</td>
				<td width="5%" height="27" align="center">
					<p class="Estilo8">
						<label>
							<?php
							if ($_POST['OSEDE'] > 0) {
								// CONSULTA DEL MEMO MAXIMO
								$consulta = 'SELECT Max(memo2)+1 AS Maximo FROM liquidacion WHERE sector=0' . $_POST['OSEDE'] . '';
								$tabla = mysql_query($consulta);
								$registro = mysql_fetch_object($tabla);
								if ($registro->Maximo > 0) {
									$memo = $registro->Maximo;
								} else {
									$memo = 1;
								}
							}
							echo $memo; ?></label>
					</p>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#CCCCCC" height=27>
						<div align="center"><strong>Seleccione</strong></div>
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
						<div align="center"><strong>A&ntilde;o</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>N&uacute;mero</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Origen</strong></div>
					</td>
				</tr>
				<?php
				if ($_POST['OSEDE'] > 0) {
					$consulta_x = 'SELECT area, contribuyente, origen_liquidacion, rif, anno_expediente, num_expediente, sector FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' and sector=0' . $_POST['OSEDE'] . ' AND origen_liquidacion = ' . $_POST['OORIGEN'] . ' GROUP BY sector, origen_liquidacion, anno_expediente, num_expediente';
					$tabla = mysql_query($consulta_x);

					$i = 1;
					while ($registro = mysql_fetch_object($tabla)) {
				?><tr>
							<td>
								<div align="center"><?php
													echo '<input type="checkbox" name="O' . $registro->sector . '-' . $registro->origen_liquidacion . '-' . $registro->anno_expediente . '-' . $registro->num_expediente . '" value="true">';
													?></div>
							</td>
							<td>
								<div align="center"> <?php
														echo $i;
														?></div>
							</td>
							<td>
								<div align="left"><?php
													echo '<a href="0_expediente.php?rif=' . $registro->rif;
													echo '&num=';
													echo $registro->num_expediente;
													echo '&anno=';
													echo $registro->anno_expediente;
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
													echo '</a>';
													?></div>
							</td>
							<td>
								<div align="left"><?php
													echo $registro->contribuyente;
													?></div>
							</td>
							<td>
								<div align="center" class="Estilo5"><?php
																	echo $registro->anno_expediente;
																	?></div>
							</td>
							<td>
								<div align="center" class="Estilo5"><?php
																	echo $registro->num_expediente;
																	?></div>
							</td>
							<td>
								<div align="center" class="Estilo5"><?php
																	echo $registro->area;
																	?></div>
							</td>
						</tr><?php
								$i++;
							}
						}
								?>
			</tbody>
		</table>
		<p></p>
		<?php if ($i > 1) {  ?>
			<p align="center"><input type="submit" class="boton" name="CMDTRANSFERIR" value="Transferir"></p><?php
																											}	 ?>
	</form>

	<?php include "../pie.php"; ?>
</body>

</html>