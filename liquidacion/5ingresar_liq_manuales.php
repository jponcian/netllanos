<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 111;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//-------- POR SI YA EXISTE EL EXPEDIENTE
if ($_GET['existe'] == 'si' and !$_POST['ORIF']) {
	// ---------------- EXPEDIENTE
	$consulta = "SELECT * FROM expedientes_manuales WHERE sector=" . $_SESSION['SEDE'] . " AND origen=" . $_SESSION['ORIGEN'] . " AND anno=" . $_SESSION['ANNO'] . " AND numero=" . $_SESSION['NUMERO'] . ";";
	$tabla_datos = mysql_query($consulta);
	$registro_datos = mysql_fetch_object($tabla_datos);
	//----------
	$_POST['ORIF'] = $registro_datos->rif;
	$_POST['ORESOLUCION'] = $registro_datos->resolucion;
	$_POST['OFECHA'] = voltea_fecha($registro_datos->fecha_resolucion);
	//----------
}

list($contribuyente, $direccion) = funcion_contribuyente($_POST['ORIF']);

if ($_POST['CMDBUSCAR'] == "Buscar" or $_POST['CMDGUARDAR'] == "Agregar") {
	list($contribuyente, $direccion) = funcion_contribuyente($_POST['ORIF']);
	// VALIDACION DEL RIF
	if (!$contribuyente <> '') {
		echo "<script type=\"text/javascript\">alert('���No Existe Contribuyente Registrado con ese N� de Rif!!!');</script>";
	}
}

if ($_POST['CMDGUARDAR'] == "Agregar") {
	//--------------------
	if ($contribuyente <> '') // VALIDACION DEL RIF
	{
		if ($_POST['CMDGUARDAR'] == "Agregar") {
			if ($_POST['ORESOLUCION'] <> "" and $_POST['OFECHA'] <> "") {
				if ($_POST['OTRIBUTO'] > 0 and $_POST['OSERIE'] > 0) {
					if ($_POST['OINICIO'] <> "" and $_POST['OFIN'] <> "") {
						if ($_POST['OMONTO'] > 0) {
							// ------------VALIDACION
							$consulta_x = "SELECT rif FROM liquidacion, a_sancion WHERE liquidacion.id_sancion = a_sancion.id_sancion and sector='" . $_SESSION['SEDE'] . "' AND origen_liquidacion='" . $_SESSION['ORIGEN'] . "' AND anno_expediente='" . $_SESSION['ANNO'] . "' AND num_expediente='" . $_SESSION['NUMERO'] . "' AND periodoinicio='" . voltea_fecha($_POST['OINICIO']) . "' AND periodofinal='" . voltea_fecha($_POST['OFIN']) . "' AND id_tributo=" . $_POST['OTRIBUTO'] . " AND serie=" . $_POST['OSERIE'] . ";";
							$tabla_x = mysql_query($consulta_x);
							if ($registro_x = mysql_fetch_array($tabla_x)) {
								echo "<script type=\"text/javascript\">alert('���Ya est� registrada esa Sanci�n!!!');</script>";
							} else {
								// GUARDADO DE LOS DATOS
								// ELIMINAR EL EXPEDIENTE ANTERIOR
								$consulta = "DELETE FROM expedientes_manuales WHERE sector=" . $_SESSION['SEDE'] . " AND origen=" . $_SESSION['ORIGEN'] . " AND anno=" . $_SESSION['ANNO'] . " AND numero=" . $_SESSION['NUMERO'] . ";";
								$tabla = mysql_query($consulta);

								//---- TABLA EXPEDIENTE
								$consulta = "INSERT INTO expedientes_manuales ( numero, anno, origen, sector, resolucion, fecha_resolucion, rif, fecha_registro, usuario ) SELECT '" . $_SESSION['NUMERO'] . "','" . $_SESSION['ANNO'] . "','" . $_SESSION['ORIGEN'] . "','" . $_SESSION['SEDE'] . "','" . $_POST['ORESOLUCION'] . "','" . voltea_fecha($_POST['OFECHA']) . "','" . strtoupper($_POST['ORIF']) . "',Date(now()),'" . $_SESSION['CEDULA_USUARIO'] . "';";
								$tabla = mysql_query($consulta);

								// PARA BUSCAR LA SANCION MANUAL DEPENDE DE LA SERIE
								$consulta_y = "SELECT id_sancion FROM a_sancion WHERE id_sancion>3600 AND id_sancion<3650 AND serie =" . $_POST['OSERIE'] . ";";
								$tabla_y = mysql_query($consulta_y);
								$registro_y = mysql_fetch_object($tabla_y);
								$id_sancion = $registro_y->id_sancion;
								//---------	

								//--- POR SI ES UN INTERES AGREGO LOS DEMAS DATOS
								if ($_POST['OSERIE'] == 38) {
									$txt1 = ', monto_pagado, fecha_vencimiento, fecha_pago';
									$txt2 = ", " . $_POST['OPAGADO'] . ", '" . voltea_fecha($_POST['OVENCIMIENTO']) . "', '" . voltea_fecha($_POST['OPAGO']) . "'";
								}
								//----- TABLA LIQUIDACION
								$consultaxx = "INSERT INTO liquidacion (secuencial, status, usuario, sector, origen_liquidacion, anno_expediente, num_expediente, rif, periodoinicio, periodofinal, id_sancion, id_tributo, monto_ut, monto_bs" . $txt1 . " ) VALUES (
								'999999',
								'10', 
								'" . $_SESSION['CEDULA_USUARIO'] . "', 
								'" . $_SESSION['SEDE'] . "',
								'" . $_SESSION['ORIGEN'] . "',
								'" . $_SESSION['ANNO'] . "',
								'" . $_SESSION['NUMERO'] . "',
								'" . strtoupper($_POST['ORIF']) . "', 
								'" . voltea_fecha($_POST['OINICIO']) . "',  
								'" . voltea_fecha($_POST['OFIN']) . "', 
								'" . $id_sancion . "', 
								'" . $_POST['OTRIBUTO'] . "',
								'0', 
								'" . $_POST['OMONTO'] . "'" . $txt2 . ");";
								//-----------------
								$tablaxx =  mysql_query($consultaxx);
								//------------------------ 
							}
							// ------------FIN
						} else {
							echo "<script type=\"text/javascript\">alert('�Por favor Verifique el Monto!');</script>";
						}
					} else {
						echo "<script type=\"text/javascript\">alert('�Por favor Verifique el Per�odo!');</script>";
					}
				} else {
					echo "<script type=\"text/javascript\">alert('�Por favor Verifique el Tributo y la Serie!');</script>";
				}
			} else {
				echo "<script type=\"text/javascript\">alert('�Por favor Verifique la Resoluci�n y Fecha!');</script>";
			}
		}
	}
}

?>

<html>

<head>
	<title>Ingresar Expediente Manual</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
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

	<form name="form1" method="post" action="#vista">
		<p></p>
		<table width="55%" border="1" align="center">
			<tr>
				<td height="37" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
			</tr>

		</table>

		<table width="55%" border="1" align="center">
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Rif:</strong></div>
				</td>
				<td>
					<div align="center">
						<input type="text" name="ORIF" value="<?php echo $_POST['ORIF']; ?>" maxlength="10">
					</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center">
						<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
					</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente:</strong></div>
				</td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo $contribuyente; ?></span>
						</div>
					</label></td>
			</tr>
			<tr>
				<td width="13%" bgcolor="#CCCCCC">
					<div align="center"><strong>Direcci&oacute;n:</strong></div>
				</td>
				<td colspan="4"><label><span class="Estilo15"><?php echo $direccion; ?>
						</span></label></td>
			</tr>
		</table>
		<table width="55%" border="1" align="center">
			<tr>
				<td width="22%" bgcolor="#CCCCCC">
					<div align="center"><strong>Resolucion:</strong></div>
				</td>
				<td width="49%"><label>
						<input type="text" name="ORESOLUCION" size="60" value="<?php echo $_POST['ORESOLUCION']; ?>">
					</label></td>
				<td width="14%" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha:</strong></div>
				</td>
				<td width="15%">
					<div align="center"><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>"></label>
					</div>
				</td>
			<tr>
		</table>
		<p></p>
		<table width="55%" border="1" align="center">
			<tr>
				<td height="37" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos de la Liquidaci&oacute;n </u></span></td>
			</tr>
			<tr>
				<td width="23%" bgcolor="#CCCCCC">
					<div align="right"><strong>Tipo:</strong></div>
				</td>
				<td width="77%"><label>
						<select name="OTIPO" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<option value="1" <?php if ($_POST['OTIPO'] == 1) {
													echo 'selected="selected"';
												} ?>>IMPUESTO</option>
							<option value="2" <?php if ($_POST['OTIPO'] == 2) {
													echo 'selected="selected"';
												} ?>>MULTA</option>
							<option value="3" <?php if ($_POST['OTIPO'] == 3) {
													echo 'selected="selected"';
												} ?>>INTERES</option>
							<!-- <option value="4" <?php //if ($_POST['OTIPO']==4) {echo 'selected="selected"';} 
													?>>RECARGOS</option>-->
						</select></label>
				</td>
			</tr>
			<tr>
				<td width="23%" bgcolor="#CCCCCC">
					<div align="right"><strong>Tributo:</strong></div>
				</td>
				<td width="77%"><label>
						<select name="OTRIBUTO" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$consulta2 = "SELECT id_tributo, nombre FROM a_tributos WHERE id_tributo IN (1,3,7,8,9,11,12,13,20,21,22,23,99) AND tipo=" . $_POST['OTIPO'] . " ORDER BY id_tributo;";
							$tabla2 = mysql_query($consulta2);
							while ($registro2 = mysql_fetch_object($tabla2)) {
								echo '<option';
								if ($_POST['OTRIBUTO'] == $registro2->id_tributo) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro2->id_tributo;
								echo '">';
								echo sprintf("%002s", $registro2->id_tributo) . ' - ' . $registro2->nombre;
								echo '</option>';
							}
							?>
						</select>
						<?php if ($_POST['OTRIBUTO'] == 99) {
							$_POST['OSERIE'] = 38;
						} ?>
					</label></td>
			</tr>
			<tr>
				<td width="23%" bgcolor="#CCCCCC">
					<div align="right"><strong>Serie:</strong></div>
				</td>
				<td width="77%"><label>
						<select name="OSERIE" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$consulta2 = "SELECT id_serie, descripcion FROM a_series_liquidacion WHERE id_serie>0 ORDER BY id_serie;";
							$tabla2 = mysql_query($consulta2);
							while ($registro2 = mysql_fetch_object($tabla2)) {
								echo '<option';
								if ($_POST['OSERIE'] == $registro2->id_serie) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro2->id_serie;
								echo '">';
								echo sprintf("%002s", $registro2->id_serie) . ' - ' . $registro2->descripcion;
								echo '</option>';
							}
							?>
						</select>
					</label></td>
			</tr>
		</table>
		<table width="55%" border="1" align="center">
			<tr>
				<td width="23%" bgcolor="#CCCCCC">
					<div align="right"><strong>Periodo Inicial:</strong></div>
				</td>
				<td width="77%"><label>
						<div align="left"><input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="8" readonly value="<?php echo $_POST['OINICIO']; ?>" />
						</div>
					</label></td>
			</tr>
			<tr>
				<td width="23%" bgcolor="#CCCCCC">
					<div align="right"><strong>Periodo Final:</strong></div>
				</td>
				<td><input onclick='javascript:scwShow(this,event);' type="text" name="OFIN" size="8" readonly value="<?php echo $_POST['OFIN']; ?>" /></td>
			</tr>
			<?php if ($_POST['OSERIE'] == 38) { ?>
				<tr>
					<td width="23%" bgcolor="#FFFF00">
						<div align="right"><strong>Fecha Vencimiento:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input onclick='javascript:scwShow(this,event);' type="text" name="OVENCIMIENTO" size="8" readonly value="<?php echo $_POST['OVENCIMIENTO']; ?>" />
							</div>
						</label></td>
				</tr>
				<tr>
					<td width="23%" bgcolor="#FFFF00">
						<div align="right"><strong>Fecha Pago:</strong></div>
					</td>
					<td><label><input onclick='javascript:scwShow(this,event);' type="text" name="OPAGO" size="8" readonly value="<?php echo $_POST['OPAGO']; ?>" /></label></td>
				</tr>
				<tr>
					<td width="23%" bgcolor="#FFFF00">
						<div align="right"><strong>Monto Pagado:</strong></div>
					</td>
					<td><label><input type="text" name="OPAGADO" size="12" value="<?php echo $_POST['OPAGADO']; ?>"></label></td>
				</tr>
			<?php } ?>
			<tr>
				<td width="23%" bgcolor="#CCCCCC">
					<div align="right"><strong>Monto:</strong></div>
				</td>
				<td><input type="text" name="OMONTO" size="12" value="<?php echo $_POST['OMONTO']; ?>"></td>
			</tr>
		</table>
		<p align="center" class="Estilo16">&lt;&lt;&lt; Utilizar el signo de punto (.) para separar los decimales &gt;&gt;&gt;</p>
		<table width="19%" border="1" align="center">
			<tr>
				<td width="100%" bgcolor="#FFFFFF">
					<p align="center">
						<input type="submit" class="boton" name="CMDGUARDAR" value="Agregar">
					</p>
				</td>
			</tr>
		</table>
		</p>
		<p><a name="vista"></a></p>
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="38" colspan="10" align="center">
						<p class="Estilo7"><u>Sanciones actuales aplicadas al Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" width="6%" height=27>
						<div align="center" class="Estilo8">Sel.</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Periodo Inicial</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Periodo Final</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Tributo</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Serie</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">UT</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Monto</div>
					</td>
				</tr>
				<?php
				if ($_POST['CMDELIMINAR']) {
					$i = 1;
					while ($i <= $_SESSION['VARIABLE']) {
						if ($_POST[$i]) {
							// CONSULTA PARA ELIMINAR EN LIQUIDACION
							$consulta = "DELETE FROM Liquidacion WHERE id_liquidacion=" . $_POST[$i] . ";";
							$tabla = mysql_query($consulta);
							//-----------------
							echo "<script type=\"text/javascript\">alert('���Sanci�n Eliminada!!!');</script>";
						}
						$i++;
					}
				}

				$consulta_x = "SELECT liquidacion.id_liquidacion, liquidacion.sector, liquidacion.origen_liquidacion, liquidacion.anno_expediente, liquidacion.num_expediente, liquidacion.rif, liquidacion.periodoinicio, liquidacion.periodofinal, a_series_liquidacion.descripcion, a_tributos.nombre, liquidacion.monto_bs FROM a_series_liquidacion , a_tributos , liquidacion, a_sancion WHERE a_series_liquidacion.id_serie = a_sancion.serie AND liquidacion.id_tributo = a_tributos.id_tributo AND liquidacion.id_sancion = a_sancion.id_sancion AND sector=" . $_SESSION['SEDE'] . " AND origen_liquidacion=" . $_SESSION['ORIGEN'] . " AND anno_expediente=" . $_SESSION['ANNO'] . " AND num_expediente=" . $_SESSION['NUMERO'] . ";";
				$tabla_x = mysql_query($consulta_x);
				$i = 0; //echo $consulta_x ;

				while ($registro_x = mysql_fetch_object($tabla_x)) {
					$i++;
					echo '<tr> <td bgcolor="#FFFFFF" width="8%" height=27><div align="center" class="Estilo8">  <input type="checkbox" name="';
					echo $i;
					echo '" value="';
					echo $registro_x->id_liquidacion;
					echo '" /></div></td><td ><div align="center">';
					echo voltea_fecha($registro_x->periodoinicio);
					echo '</div></td><td ><div align="center">';
					echo voltea_fecha($registro_x->periodofinal);
					echo '</div></td><td ><div align="left">';
					echo palabras($registro_x->nombre);
					echo '</div></td><td><div align="left">';
					echo palabras($registro_x->descripcion);
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo formato_moneda($registro_x->monto_bs / $_SESSION['VALOR_UT_ACTUAL']);
					echo '</div></td><td><div align="right" class="Estilo5">';
					echo formato_moneda($registro_x->monto_bs);
					echo '</div></td></tr>';
				}
				$_SESSION['VARIABLE'] = $i;

				?>
			</tbody>
		</table>
		<p align="center"><?php if ($i > 0) {
								echo '<input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />';
							} ?> </p>
	</form>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>