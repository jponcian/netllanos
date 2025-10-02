<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 1;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<title>Anular Providencias</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>

</head>

<body style="background: transparent !important;">

	<?php

	if ($_POST['CMDANULAR'] == 'Anular') {
		if ($_POST['OMOTIVO'] <> '') {
			$I = 1;
			$ANULADAS = 'NO';
			while ($I <= $_SESSION['VARIABLE1']) {
				if (!empty($_POST[$I])) {
					$consulta = "UPDATE expedientes_fiscalizacion SET usuario=" . $_SESSION['CEDULA_USUARIO'] . ", status = 9, fecha_anulacion = DATE(NOW()), motivo_anulacion = '" . $_POST['OMOTIVO'] . "', fecha_proceso = CURRENT_TIMESTAMP() WHERE id_expediente=" . $_POST[$I] . ";";
					$tabla = mysql_query($consulta);
					$ANULADAS = 'SI';
				}
				$I++;
			}
		} else {
			echo "<script type=\"text/javascript\">alert('���Debe colocar el motivo de anulaci�n!!!');</script>";
		}
	}
	//------ CONSULTA PARA LLENAR LA LISTA
	if ($_POST['OSEDE'] > 0) {
		// CONSULTA
		$consulta = "SELECT id_expediente, numero, anno, sector, rif, contribuyente, Apellidos1, Nombres1 FROM vista_providencias WHERE sector=" . $_POST['OSEDE'] . " AND (status=0 Or status=1 Or status=2) ORDER BY anno, numero;";
		$tabla_datos = mysql_query($consulta);
		if ($registro_datos = mysql_fetch_object($tabla_datos)) {
			$DATOS = 'SI';
			$MOSTRAR_BOTON = 'SI';
		} else {
			$DATOS = 'NO';
		}
	}
	?>

	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<p align="center"><?php
						include "menu.php";
						?></p>

	<form name="form1" method="post">
		<div align="center">

			<table width="70%" border=1 align=center>
				<tbody>
					<tr>
						<td class="TituloTabla" height="27" colspan="10" align="center"><span><u>Datos de la(s) Providencia(es)</u></span></td>
						<td width="87" height="35" bgcolor="#CCCCCC">
							<div align="center"><strong>Dependencia</strong></div>
						</td>
						<td width="145" bgcolor="#FFFFFF">
							<div align="center">
								<label></label>
								<span class="Estilo1">
									<select name="OSEDE" size="1" onChange="this.form.submit()">
										<option value="-1">Seleccione</option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['OSEDE'] == $registro_x['id_sector']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
											}
										} else {
											$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
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
					</tr>

				</tbody>
			</table>
			<table class="formateada" width="70%" border=1 align=center>
				<tbody>
					<tr>
					<tr>
						<th height=27>
							<div align="center" class="Estilo8"><strong>Sel.</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><span class="Estilo16">Providencia:</span></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>A�o:</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Fiscal:</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Rif:</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Contribuyente:</strong></div>
							</td>
					</tr>
					<?php
					$I = 1;

					while ($DATOS == 'SI') {
					?>
						<tr id="fila<?php echo $I; ?>">
							<td height=27>
								<div align="center" class="Estilo8">
									<input type="checkbox" name="<?php echo $I ?>" value="<?php echo $registro_datos->id_expediente ?>" onClick="marcar(this,<?php echo $I; ?>)">
									<span class="Estilo17"><span class="Estilo17"></span></span>
								</div>
							</td>
							<td height=27>
								<div align="center" class="Estilo8">
									<?php echo  $I ?><span class="Estilo17"><span class="Estilo17"></span></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8">
									<?php echo $registro_datos->numero ?><span class="Estilo17"><span class="Estilo17"></span></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8">
									<?php echo $registro_datos->anno ?><span class="Estilo17"><span class="Estilo17"></span></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8"><?php echo $registro_datos->Nombres1 . ' ' . $registro_datos->Apellidos1 ?><span class="Estilo17"><span class="Estilo17"></span></span></div>
							</td>
							<td height=27>
								<div align="center" class="Estilo8">
									<?php echo $registro_datos->rif ?><span class="Estilo17"><span class="Estilo17"></span></span></div>
							</td>
							<td>
								<div align="center" class="Estilo8">
									<?php echo $registro_datos->contribuyente ?><span class="Estilo17"><span class="Estilo17"></span></span></div>
							</td>
						</tr>
					<?php
						// CONTROL DEL CICLO
						if ($registro_datos = mysql_fetch_object($tabla_datos)) {
							$I++;
						} else {
							$DATOS = 'NO';
							$_SESSION['VARIABLE1'] = $I;
						}
						// FIN DEL CONTROL
					}

					?>
				</tbody>
			</table>

			<p>
				<?php
				if ($ANULADAS == 'SI') {
					echo '<table width="75%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>PROVIDENCIAS ANULADAS!!! </strong></div></td> </tr>  </table><p></p>';
				}
				if ($MOSTRAR_BOTON == 'SI') {
					echo '<table width="32%" border=1 align=center>
    <tbody>';
					/*<tr>
        <td bgcolor="#FF0000" height="27" colspan="10" align="center"><p class="Estilo7"><u>Fecha de Anulaci�n</u></p></td>
      </tr>
      <tr>
	  <td bgcolor="#CCCCCC" width="6%" height=27><div align="center" class="Estilo8"><label><input onclick="javascript:scwShow(this,event);" type="text" name="OFECHA" readonly="true" maxlength="10" size="10" value="';
	 if ($_POST['OFECHA']=="") {echo date('d/m/Y');} else {echo $_POST['OFECHA']; }
echo '"></label>';
	  echo '</div></td>      </tr>	  */
					echo '<tr><td>	  <label>    <strong>Motivo Anulaci&oacute;n =&gt;</strong>     <input type="text" name="OMOTIVO" maxlength="55" value="';
					echo $_POST['OMOTIVO'];
					echo '">
    </label>
	  </td> </tr>
   </tbody>
  </table>';

					echo '<p></p><input type="submit" class="boton" name="CMDANULAR" value="Anular">';
				}
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