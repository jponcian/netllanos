<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 117;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<title>Crear Recurso</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
		<?php
		// --------- BUSQUEDAS ----------
		if ($_POST['ORIF'] <> "") {
			list($Contribuyente, $Direccion) = funcion_contribuyente($_POST['ORIF']);
		}

		// ---------
		if ($Contribuyente <> "") {
			//-------- PARA CREAR EL RECURSO
			if ($_POST['CMDGUARDAR'] == 'Guardar') {
				if ($_POST['OSEDE'] > 0 and $_POST['OESCRITO'] <> '' and $_POST['OFECHA'] <> '') {
					// BUSQUEDA DE PLANILLAS PARA AGREGAR
					$consulta = "SELECT id_liquidacion, liquidacion, status FROM liquidacion WHERE (status >= 31 and status <= 32) AND rif='" . $_POST['ORIF'] . "' ORDER BY liquidacion;";
					$tabla =  mysql_query($consulta);
					while ($registro = mysql_fetch_object($tabla)) {
						if ($_POST[$registro->id_liquidacion] == $registro->id_liquidacion) {
							// PARA BUSCAR EL EXPEDIENTE Y SI NO EXISTE CREARLO
							$consulta = "SELECT id_expediente FROM expedientes_juridico WHERE rif ='" . $_POST['ORIF'] . "' AND status=0 AND sector ='" . $_POST['OSEDE'] . "'";
							$tabla_x = mysql_query($consulta);
							$registro_x = mysql_fetch_object($tabla_x);
							$id_expediente = $registro_x->id_expediente;
							if ($id_expediente  <= 0) {
								$sql_maximo = "SELECT max(numero) as maximo FROM expedientes_juridico WHERE anno = year(date(now())) AND sector =" . $_POST['OSEDE'];
								$resultado = mysql_query($sql_maximo);
								$registro_x = mysql_fetch_object($resultado);
								if ($registro_x->maximo > 0) {
									$maximo = $registro_x->maximo + 1;
								} else {
									$maximo = 1;
								}
								// JEFE DIVISION, SECTOR O UNIDAD
								if ($_POST['OSEDE'] > 1) {
									$division = $_POST['OSEDE'];
								} else {
									$division = 11;
								}
								//--------------------
								$consulta_x = "SELECT cedula FROM z_jefes_detalle WHERE division=" . $division;
								$tabla_x = mysql_query($consulta_x);
								$registro_x = mysql_fetch_object($tabla_x);
								$Jefe_Division = $registro_x->cedula;
								//------------------------
								$insert = "INSERT INTO expedientes_juridico ( sector, anno, numero, rif, fecha_recepcion, num_escrito_descargo, fecha_escrito_descargo, status, usuario, cedula_coordinador, cedula_jefe) VALUES (" . $_POST['OSEDE'] . ",year(date(now()))," . $maximo . ",'" . $_POST['ORIF'] . "', date(now()),'" . $_POST['OESCRITO'] . "','" . voltea_fecha($_POST['OFECHAESCRITO']) . "',0," . $_SESSION['CEDULA_USUARIO'] . "," . $_SESSION['CEDULA_USUARIO'] . "," . $Jefe_Division . ")";
								$tabla_i = mysql_query($insert);
								$id_expediente = mysql_insert_id();
								//echo $insert;
							}

							// ------ INSERTAR LA PLANILLA NUEVA
							$consultax = "INSERT INTO jur_detalle_expediente (usuario, id_expediente, id_liquidacion, status_anterior) SELECT '" . $_SESSION['CEDULA_USUARIO'] . "', '" . $id_expediente . "'," . $registro->id_liquidacion . "," . $registro->status . ";";
							$tablax =  mysql_query($consultax);

							// ------ ACTUALIZAR EL REGISTRO SELECCIONADO
							$consultax = "UPDATE liquidacion SET status = 60, fecha_recurrida=date(now()), usuario_recurrida=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion='" . $registro->id_liquidacion . "';";
							$tablax =  mysql_query($consultax);
							//------------------------
							echo "<script type=\"text/javascript\">alert('La Planilla " . $registro->liquidacion . " fue Agregada al Recurso Exitosamente');</script>";
						}
					}
				} else {
					echo "<script type=\"text/javascript\">alert('Debe seleccionar el Sector, cargar el N�mero de Recepci�n y la Fecha');</script>";
				}
			}

			//-------- BUSQUEDA DE PLANILLAS PARA ELIMINAR
			$consulta = "SELECT id_liquidacion, status_anterior FROM vista_jur_detalle_planillas WHERE status_exp=0 AND rif='" . $_POST['ORIF'] . "' ORDER BY id_liquidacion;";
			$tabla =  mysql_query($consulta);
			while ($registro = mysql_fetch_object($tabla)) {
				if ($_POST['BOTON' . $registro->id_liquidacion] == 'Quitar') {
					// ------ ELIMINAR LA PLANILLA NUEVA
					$consultax = "DELETE FROM jur_detalle_expediente WHERE id_liquidacion='" . $registro->id_liquidacion . "';";
					$tablax = mysql_query($consultax);
					// ------
					// ------ ACTUALIZAR LA PLANILLA PRIMITIVA
					$consultax = "UPDATE liquidacion SET status = " . $registro->status_anterior . " WHERE id_liquidacion='" . $registro->id_liquidacion . "';";
					$tablax = mysql_query($consultax);
					// ------		
					echo "<script type=\"text/javascript\">alert('La Planilla " . $registro->liquidacion . " fue Eliminada Exitosamente');</script>";

					//-- PARA VER SI EXISTEN MAS PLANILLAS
					$consultax = "SELECT id_liquidacion FROM vista_jur_detalle_planillas WHERE status_exp=0 AND rif='" . $_POST['ORIF'] . "' ORDER BY liquidacion;;";
					$tablax = mysql_query($consultax);
					if (!$registro_x = mysql_fetch_object($tablax)) {
						// ------ ELIMINAR EL EXPEDIENTE
						$consultax = "DELETE FROM expedientes_juridico WHERE status=0 AND rif='" . $_POST['ORIF'] . "';";
						$tablax = mysql_query($consultax);
						echo "<script type=\"text/javascript\">alert('Expediente Eliminado Exitosamente');</script>";
						// ------
					}
				}
			}
		}

		?>

	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>

	<form name="form1" method="post" action="">
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="24" colspan="6" align="center">
						<p class="Estilo7"><u>Datos del Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Rif</strong></div>
					</td>
					<td colspan="4" bgcolor="#CCCCCC">
						<div align="center"><strong>Contribuyente</strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFFF" colspan="1" height=27>
						<div align="center">
							<input style="text-align:center" type="text" name="ORIF" size="12" maxlength="10" value="<?php echo mayuscula($_POST['ORIF']); ?>">
							<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
						</div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $Contribuyente;		?></div>
					</td>
				</tr>
				<tr>
					<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Domicilio</strong></div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $Direccion;		?></div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table width="55%" border=1 align=center>
			<tr>
				<td bgcolor="#FF0000" height="40" colspan="6" align="center">
					<p class="Estilo7"><u>Escrito de Descargo</u></p>
				</td>
			</tr>
			<tr>
				<td width="11%" height="35" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia:</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<span class="Estilo1">
							<select name="OSEDE" size="1">
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
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">Numero de Recepci�n:</span></a></strong></div>
				</td>
				<td><input name="OESCRITO" type="text" size="10" maxlength="6" value="<?php echo $_POST['OESCRITO'] ?>"></td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Fecha de Recepci�n:</strong></div>
				</td>
				<td><input onclick='javascript:scwShow(this,event);' name="OFECHA" type="text" size="10" maxlength="10" value="<?php echo $_POST['OFECHA'] ?>" readonly></td>
			</tr>
		</table>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="8" align="center">
					<p class="Estilo7"><u>Planillas Disponibles</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Per�odo</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Agregar</strong></div>
				</td>
			</tr>
			<?php
			$consulta = "SELECT liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, periodoinicio, periodofinal FROM liquidacion WHERE id_liquidacion NOT IN (select id_liquidacion from jur_detalle_expediente) AND (liquidacion.status >= 31 and liquidacion.status <= 32) AND rif='" . $_POST['ORIF'] . "' ORDER BY liquidacion;";
			$tabla = mysql_query($consulta);

			$i = 0;

			while ($registro = mysql_fetch_object($tabla)) {
				$i++;
			?>
				<tr>
					<td>
						<div align="center">
							<?php echo $i;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo $registro->liquidacion;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo voltea_fecha($registro->periodoinicio) . ' al ' . voltea_fecha($registro->periodofinal);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->monto);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->cant_ut);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php		//echo unidad_infraccion($registro->fecha_impresion); 
							echo redondea($registro->monto / $registro->cant_ut);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo voltea_fecha($registro->fecha_impresion);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<input type="checkbox" name="<?php echo ($registro->id_liquidacion);		?>" value="<?php echo ($registro->id_liquidacion);		?>">
						</div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
		<p></p>
		<p align="center">
			<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
			<br>
		</p>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="9" align="center">
					<p class="Estilo7"><u>Planillas Recurridas </u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Per&iacute;odo</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Opciones</strong></div>
				</td>
			</tr>
			<?php
			$consulta = "SELECT liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, periodoinicio, periodofinal FROM vista_jur_detalle_planillas WHERE status_exp=0 AND rif='" . $_POST['ORIF'] . "' ORDER BY liquidacion;";
			$tabla = mysql_query($consulta);

			$i = 0;

			while ($registro = mysql_fetch_object($tabla)) {
				$i++;
			?>
				<tr>
					<td>
						<div align="center">
							<?php echo $i;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo $registro->liquidacion;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo voltea_fecha($registro->periodoinicio) . ' al ' . voltea_fecha($registro->periodofinal);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->monto);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->cant_ut);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php		//echo unidad_infraccion($registro->fecha_impresion); 
							echo redondea($registro->monto / $registro->cant_ut);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo voltea_fecha($registro->fecha_impresion);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<input type="submit" class="boton" name="BOTON<?php echo $registro->id_liquidacion;		?>" value="Quitar">
						</div>
					</td>
				</tr>
			<?php
			}
			?>

		</table>
		<p>&nbsp;</p>
		<p><br>
		</p>
	</form>

	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>