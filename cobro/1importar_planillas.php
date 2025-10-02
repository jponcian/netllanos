<html>

<head>
	<title>Listado Expedientes x Importar a Notificaci�n</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}
	$acceso = 50;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	//---------- ORIGEN DEL FUNCIONARIO 
	include "../funciones/origen_funcionario.php";

	//--------------------
	$status = 30;
	$status2 = 30;
	//--------------------

	if ($_POST['CMDTRANSFERIR'] <> "") {
		if ($_POST['OFECHA'] <> '') {
			// PARA GUARDAR LA INFORMACI�N
			if ($_POST['OMEMO'] > 0) {
				//-----------
				$consulta = "UPDATE liquidacion SET status = 31, fecha_importacion_a_cob = date(now()), usuario_importador_a_cob = " . $_SESSION['CEDULA_USUARIO'] . ", usuario = " . $_SESSION['CEDULA_USUARIO'] . " WHERE status=" . $status . " AND memo2='" . $_POST['OMEMO'] . "' AND rif='" . $_POST['CMDTRANSFERIR'] . "' AND sector=" . $_POST['OSEDE'] . ";";
				$tabla = mysql_query($consulta);
				// MENSAJE
				echo "<script type=\"text/javascript\">alert('Expediente(s) Importados(s) Exitosamente!!!');</script>";
				//-- CAMBIO DE LA DIRECCION
				echo '<meta http-equiv="refresh" content="0";/>';
			} else {
				echo '<script type="text/javascript">alert("No seleccionado el Memo!");</script>';
			}
		} else {
			echo '<script type="text/javascript">alert("No ha Ingresado la Fecha de Recepci�n!");</script>';
		}
	}
	?>

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
		<table width="65%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="45" colspan="5" align="center">
						<p class="Estilo7"><u>Expedientes por Importar a Cobro </u></p>
					</td>
				</tr>

			</tbody>
		</table>

		<p></p>
		<table width="65%" border=1 align=center>
			<tr>
				<td colspan="6" height="27" align="center" bgcolor="#FFFFCC">
					<strong>(Si la Dependencia no est� en la lista es porque no hay informaci�n para Importar)</strong>
				</td>
			</tr>
			<tr>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7">Dependencia =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p><span>
							<?php
							if ($_SESSION['ADMINISTRADOR'] > 0) {
								$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' GROUP BY sector';
							} else { //'.$origen.'
								// --- VALIDACION DE LA SEDE DEL USUARIO
								if ($_SESSION['SEDE_USUARIO'] <> 0) {
									$sede = ' and sector=' . $_SESSION['SEDE_USUARIO'];
								} else {
									$sede = '';
								}
								// -------------------------------------
								$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . '' . $sede . ' GROUP BY sector;';
							}
							//---------------
							?>
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								//---------------
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
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo7">Transferencia =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo5"><span class="Estilo1">
							<select name="OMEMO" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) { //'.$origen.'
									$consulta_x = 'SELECT memo2 FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND sector=0' . $_POST['OSEDE'] . '  GROUP BY memo2 ORDER BY memo2 DESC';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OMEMO'] == $registro_x['memo2']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['memo2'] . '>' . $registro_x['memo2'] . '</option>';
									}
								}
								?>


							</select><?php //echo $consulta_x ; 
										?>
						</span></p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="Estilo5">Fecha de Recepci&oacute;n =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p>
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
					<div align="center"><strong>Origen</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Recibir</strong></div>
				</td>
			</tr>
			<?php
			if ($_POST['OMEMO'] > 0) {
				$consulta_x = 'SELECT area, contribuyente, origen_liquidacion, rif, anno_expediente, num_expediente, sector FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' and sector=0' . $_POST['OSEDE'] . ' and memo2="' . $_POST['OMEMO'] . '" GROUP BY sector, origen_liquidacion, anno_expediente, num_expediente';
				$tabla = mysql_query($consulta_x);

				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) {
					echo '<tr><td ><div align="center" class="Estilo5"><div align="center">';
					echo $i;
					echo '</div></td><td ><div align="center" class="Estilo5"><div align="center"><a href="0_expediente.php?rif=' . $registro->rif;
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
					echo '</a></div></td><td ><div align="left">';
					echo $registro->contribuyente;
					echo '</div></div></td><td><div align="center" class="Estilo5">';
					echo $registro->anno_expediente;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $registro->num_expediente;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $registro->area;
					echo '</div></td><td><p align="center"><input type="submit" class="boton" name="CMDTRANSFERIR" value="' . $registro->rif . '"></p></td></tr>';
					$i++;
				}
			}

			?>



		</table>
	</form>
	<?php
	include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>