<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 44;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//--------------------
$status = 19;
$status2 = 19;
//--------------------

//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";

if ($_POST['CMDTRANSFERIR'] == "Transferir") {
	if ($_POST['OMEMO'] <> "") {
		// PARA VALIDAR SI ELIMIN� ITEM
		if ($_POST['OORIGEN'] > 0) {
			// CONSULTA DEL MEMO MAXIMO
			$consulta = 'SELECT Max(memo)+1 AS Maximo FROM liquidacion WHERE memo<999999 AND sector=0' . $_POST['OSEDE'] . '';
			$tabla = mysql_query($consulta);
			$registro = mysql_fetch_object($tabla);
			if ($registro->Maximo > 0) {
				$memo = $registro->Maximo;
			} else {
				$memo = 1;
			}
			//--------------------------------
			$i = 0;
			//--------------------------------
			$consulta_x = 'SELECT origen_liquidacion, anno_expediente, num_expediente, sector FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' and sector=0' . $_POST['OSEDE'] . ' and origen_liquidacion=' . $_POST['OORIGEN'] . ' GROUP BY anno_expediente, num_expediente';
			$tablax = mysql_query($consulta_x);
			while ($registrox = mysql_fetch_object($tablax)) {
				$i++;
				if ($_POST[$registrox->origen_liquidacion . '/' . $registrox->num_expediente . '/' . $registrox->anno_expediente . '/' . $registrox->sector] == 'true') {
					$consulta = "UPDATE liquidacion SET memo='" . $memo . "', status = 20, fecha_transferencia_not = date(now()), usuario_transferencia_a_not = " . $_SESSION['CEDULA_USUARIO'] . ", usuario = " . $_SESSION['CEDULA_USUARIO'] . "  WHERE status=" . $status . " AND origen_liquidacion=" . $registrox->origen_liquidacion . " AND anno_expediente=" . $registrox->anno_expediente . " AND num_expediente=" . $registrox->num_expediente . " AND sector=" . $registrox->sector . ";";
					$tabla = mysql_query($consulta);
				}
			}
			if ($i > 0) {
				// MENSAJE
				echo "<script type=\"text/javascript\">alert('Expediente(s) Transferido(s) Exitosamente!!!');</script>";
				//-- CAMBIO DE LA DIRECCION
				echo '<meta http-equiv="refresh" content="0";/>';
			}
		}
	} else {
		echo '<script type="text/javascript">alert("No ha Ingresado el N�mero de Memo");</script>';
	}
}
?>
<html>

<head>
	<title>Expedientes x Transferir a Notificacion</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
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
						<p class="Estilo7"><u>Expedientes por Transferir</u></p>
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
					<p class=""><span class="">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' GROUP BY sector';
								} else {
									// -------------------------------------
									$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' ' . $origen . '  ' . $sede . ' GROUP BY sector;';
								}
								//-----------------
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
					<p class="Estilo7 Estilo8">Origen =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class=""><span class="">
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
					<p class="Estilo7 Estilo8">Transferencia =&gt;</p>
				</td>
				<td height="27" align="center" bgcolor="#999999">
					<p class="">
						<label>
							<?php
							if ($_POST['OSEDE'] > 0) {
								// CONSULTA DEL MEMO MAXIMO
								$consulta = 'SELECT Max(memo)+1 AS Maximo FROM liquidacion WHERE sector=0' . $_POST['OSEDE'] . '';
								$tabla = mysql_query($consulta);
								$registro = mysql_fetch_object($tabla);
								if ($registro->Maximo > 0) {
									$memo = $registro->Maximo;
								} else {
									$memo = 1;
								}
							}
							?>
							<input type="text" name="OMEMO" size="4" maxlength="5" readonly="" value="<?php echo $memo; ?>">
						</label>
					</p>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="65%" border=1 align=center>
			<tr>
				<td bgcolor="#CCCCCC" height=27>
					<div align="center"><strong>x</strong></div>
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
			</tr>
			<?php
			if ($_POST['OORIGEN'] > 0) {
				$consulta_x = 'SELECT contribuyente, origen_liquidacion, rif, anno_expediente, num_expediente, sector FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' and sector=0' . $_POST['OSEDE'] . ' and origen_liquidacion=' . $_POST['OORIGEN'] . ' GROUP BY anno_expediente, num_expediente';
				$tabla = mysql_query($consulta_x);

				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) {
					echo '<tr><td ><div align="center" class="Estilo5"><div align="center">';
					echo '<input name="' . $registro->origen_liquidacion . '/' . $registro->num_expediente . '/' . $registro->anno_expediente . '/' . $registro->sector;
					echo '" type="checkbox" value="true">';
					echo '</div></td><td ><div align="center" class="Estilo5"><div align="center"><a href="0_expediente.php?rif=' . $registro->rif;
					echo '&num=';
					echo $registro->num_expediente;
					echo '&anno=';
					echo $registro->anno_expediente;
					echo '&sector=';
					echo $registro->sector;
					echo '&origen=';
					echo $registro->origen_liquidacion;
					echo '" target=_BLANK>';
					echo $registro->rif;
					echo '</a></div></td><td ><div align="left">';
					echo $registro->contribuyente;
					echo '</div></div></td><td><div align="center" class="Estilo5">';
					echo $registro->anno_expediente;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $registro->num_expediente;
					echo '</div></td></tr>';
					$i++;
				}
			}
			?>
		</table>
		<?php if ($i > 1) {
			echo '<p align="center"><input type="submit" class="boton" name="CMDTRANSFERIR" value="Transferir"></p>';
		}	 ?>

	</form>

	<?php

	if ($_SESSION['MEMO'] > 0) {
		echo '<form name="form3" method="post" action="Reportes/transferidas_a_notificacion.php" target="_blank"><table width="20%" border="1" align="center">   <tr>   <td width="100%" bgcolor="#FFFFFF">    <p align="center">
         <input type="submit" class="boton" name="CMDVER" value="Transferidas A Notificaci�n"></td>    </tr>		  </table></form>';
	}
	?>

	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>