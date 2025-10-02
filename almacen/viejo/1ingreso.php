<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 24;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//----------
include "0_respaldo_inv_seriales.php";

// PARA VALIDAR SI AGREG� ITEM
if (@$_POST['CMDADDITEM'] == 'Agregar Item') {
	if ($_POST['OCODIGO'] <> '-1' and $_POST['OCANTIDAD'] > 0) {
		//REGISTRAMOS LA ORDEN
		if (trim($_POST['guardar_orden']) == "") {
			$sql_orden = ("INSERT INTO timbre_ingresos (numero, fecha, sector) VALUES ('" . $_POST['OORDEN'] . "','" . voltea_fecha($_POST['OFECHA']) . "', " . $_SESSION['SEDE_USUARIO'] . ")");
			//echo $sql_orden;
			$result_items = mysql_query($sql_orden);
			$_POST['guardar_orden'] = 1;
		}

		if ($_POST['guardar_orden'] == 1) {
			$sql_items = "INSERT INTO timbre_ingresos_detalle (numero_ingreso, codigo, cantidad, sector) VALUES ('" . $_POST['OORDEN'] . "','" . $_POST['OCODIGO'] . "'," . $_POST['OCANTIDAD'] . ", " . $_SESSION['SEDE_USUARIO'] . ");";
			$result_items = mysql_query($sql_items);
			$_POST['OCODIGO'] = -1;
			$_POST['OCANTIDAD'] = "";
		} else {
			echo "<script type=\"text/javascript\">alert('Problema al registrar la informaci\u00D3n!');</script>";
		}
	}
}

if (@$_POST['CMDADDSERIAL'] == 'Agregar Serial') {
	if ($_POST['OORDEN'] <> "" and $_POST['OCODIGO1'] <> '-1' and $_POST['OHASTA'] > 0 and $_POST['ODESDE'] < $_POST['OHASTA']) {
		$cantidadseriales = $_POST['OHASTA'] - $_POST['ODESDE'] + 1;
		$sql_serial = "INSERT INTO timbre_ingresos_seriales (numero_ingreso, codigo, serial_desde, serial_hasta, cantidad, sector) VALUES ('" . $_POST['OORDEN'] . "', '" . $_POST['OCODIGO1'] . "', " . $_POST['ODESDE'] . ", " . $_POST['OHASTA'] . ", " . $cantidadseriales . ", " . $_SESSION['SEDE_USUARIO'] . ")";
		$result_serial = mysql_query($sql_serial);
		$_POST['OCODIGO1'] = -1;
		$_POST['OHASTA'] = "";
		$_POST['ODESDE'] = "";
	}
}
//

//PARA GUARDAR Y CERRAR EL INGRESO
if ($_POST['CMDGUARDAR'] == 'Guardar') {
	$agregado = 0;
	//CERRAMOS LA ORDEN
	$sql_cerrar = "UPDATE timbre_ingresos SET status=1, cierre=date(now()) WHERE numero='" . $_POST['OORDEN'] . "' AND sector=" . $_SESSION['SEDE_USUARIO'];
	if (mysql_query($sql_cerrar)) {
		$sql_select = "SELECT codigo, serial_desde, serial_hasta, cantidad FROM timbre_ingresos_seriales WHERE numero_ingreso='" . $_POST['OORDEN'] . "' AND sector=" . $_SESSION['SEDE_USUARIO'];
		$tabla_x = mysql_query($sql_select);
		while ($valor = mysql_fetch_object($tabla_x)) {
			$sql_guardar = "INSERT INTO timbre_inv_detallado (codigo, serial_desde, serial_hasta, cantidad, sector) VALUES ('" . $valor->codigo . "', " . $valor->serial_desde . ", " . $valor->serial_hasta . ", " . $valor->cantidad . ", " . $_SESSION['SEDE_USUARIO'] . ")";
			$result = mysql_query($sql_guardar);
			$agregado++;
		}
		if ($agregado > 0) {
			echo "<script type=\"text/javascript\">alert('!!!...Orden registrada satisfactoriamente...!!!');</script>";
			$_POST['OORDEN'] = "";
			$_POST['OFECHA'] = "";
			$_POST['OMONTO'] = 0;
		}
	}
}

// PARA VALIDAR SI ELIMIN� ITEM
$consulta = "SELECT * FROM timbre_ingresos_detalle WHERE sector=" . $_SESSION['SEDE_USUARIO'];
$tablax = mysql_query($consulta);
// ------------
while ($registrox = mysql_fetch_object($tablax)) {
	if ($_POST['E#' . $registrox->id_ingreso_det] == 'Eliminar') {
		$consulta = "DELETE FROM timbre_ingresos_detalle WHERE numero_ingreso ='" . $registrox->numero_ingreso . "' and codigo='" . $registrox->codigo . "' and sector=" . $_SESSION['SEDE_USUARIO'];
		$tablaxx = mysql_query($consulta);
		$consulta2 = "DELETE FROM timbre_ingresos_seriales WHERE numero_ingreso ='" . $registrox->numero_ingreso . "' and codigo='" . $registrox->codigo . "' and sector=" . $_SESSION['SEDE_USUARIO'];
		$tablaxx = mysql_query($consulta2);
		echo "<script type=\"text/javascript\">alert('Item Eliminado!');</script>";
	}
}

$consulta = "SELECT * FROM timbre_ingresos_seriales WHERE sector=" . $_SESSION['SEDE_USUARIO'];
$tablaxx = mysql_query($consulta);
// ------------
while ($registroxx = mysql_fetch_object($tablaxx)) {
	if ($_POST['E2#' . $registroxx->id_ingreso_serial] == 'Eliminar') {
		$consultax = "DELETE FROM timbre_ingresos_seriales WHERE numero_ingreso ='" . $registroxx->numero_ingreso . "' and codigo='" . $registroxx->codigo . "' and serial_desde=" . $registroxx->serial_desde . " and serial_hasta=" . $registroxx->serial_hasta . " and sector=" . $_SESSION['SEDE_USUARIO'];
		$tablaxxx = mysql_query($consultax);
		echo "<script type=\"text/javascript\">alert('Item Eliminado!');</script>";
	}
}
//

?>
<html>

<head>
	<title>Registrar Ingreso</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>

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

		.Estilo15 {
			font-size: 14px;
		}
		-->
	</style>
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


	<form name="form1" method="post" action="#vista">
		<input type="hidden" name="guardar_orden" value="<?php echo $_POST['guardar_orden']; ?>">

		<table width="50%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Orden de Entrada </u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>N&deg; Orden Ingreso </strong></div>
				</td>
				<td width="7%">
					<div align="center"><span class="Estilo15"><input type="text" style="text-align:center" name="OORDEN" value="<?php echo $_POST['OORDEN']; ?>"></span></div>
				</td>
				<td width="9%" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha:</strong></div>
				</td>
				<td width="9%">
					<div align="center"><span class="Estilo15"><input type="text" style="text-align:center" name="OFECHA" onclick='javascript:scwShow(this,event);' value="<?php echo $_POST['OFECHA']; ?>" readonly></span></div>
				</td>
			</tr>
		</table>

		<table width="50%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Cantidades Recibidas </u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>

				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="30%"><label>
					</label>
					<div align="center">
						<select name="OCODIGO" size="1">
							<option value="-1">Seleccione</option>
							<?php
							//--------------------
							$consulta_x = 'SELECT codigo, descripcion FROM timbre_inv';
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_object($tabla_x))
							//-------------
							{
								echo '<option';
								if ($_POST['OCODIGO'] == $registro_x->codigo) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro_x->codigo;
								echo '">';
								echo $registro_x->codigo . " - " . $registro_x->descripcion;
								echo '</option>';
							}
							?>
						</select>
					</div>
					<div align="center"></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><input type="text" style="text-align:center" name="OCANTIDAD" value="<?php echo $_POST['OCANTIDAD']; ?>"></span></div>
				</td>
			</tr>
		</table>
		<p align="center"><input type="submit" class="boton" name="CMDADDITEM" value="Agregar Item"></p>
		<table width="50%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Cantidades Registradas</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Descripcion:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Precio:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Total:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Opcion</strong></div>
				</td>
			</tr>
			<?php
			// --- ITEMS YA REGISTRADOS
			$consulta = "SELECT id_ingreso_det, timbre_inv.codigo, timbre_inv.descripcion, timbre_inv.precio, timbre_ingresos_detalle.cantidad, timbre_ingresos_detalle.numero_ingreso FROM timbre_ingresos_detalle, timbre_inv WHERE timbre_inv.sector = timbre_ingresos_detalle.sector AND timbre_inv.codigo = timbre_ingresos_detalle.codigo AND numero_ingreso='" . $_POST['OORDEN'] . "' and timbre_ingresos_detalle.sector=" . $_SESSION['SEDE_USUARIO'];
			$tabla = mysql_query($consulta); //echo $consulta;

			$monto = 0;

			while ($registrox = mysql_fetch_object($tabla)) {
			?>
				<tr>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->codigo; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->descripcion; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->cantidad; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->precio; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->cantidad * $registrox->precio; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><input type="submit" class="boton" name="E#<?php echo $registrox->id_ingreso_det; ?>" value="Eliminar" /></span></div>
					</td>
				</tr>
			<?php
				$monto = $monto + ($registrox->cantidad * $registrox->precio);
				$_POST['OMONTO'] = $monto;
			}

			?>
		</table>
		<input type="hidden" name="OMONTO" value="<?php echo $_POST['OMONTO']; ?>">
		<p></p>
		<table width="50%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Seriales Recibidos</u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>

				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Desde:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Hasta:</strong></div>
				</td>
				<td width="12%" bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="30%"><label>
					</label>
					<div align="center">
						<select name="OCODIGO1" size="1">
							<option value="-1">Seleccione</option>
							<?php
							//--------------------
							$consulta_x = "SELECT DISTINCTROW timbre_inv.codigo, timbre_inv.descripcion FROM timbre_inv INNER JOIN timbre_ingresos_detalle ON timbre_ingresos_detalle.codigo = timbre_inv.codigo WHERE numero_ingreso='" . $_POST['OORDEN'] . "'";
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_object($tabla_x))
							//-------------
							{
								echo '<option';
								if ($_POST['OCODIGO1'] == $registro_x->codigo) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro_x->codigo;
								echo '">';
								echo $registro_x->codigo . " - " . $registro_x->descripcion;
								echo '</option>';
							}
							?>
						</select>
					</div>
					<div align="center"></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><input type="text" style="text-align:center" name="ODESDE" value="<?php echo $_POST['ODESDE']; ?>"></span></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><input type="text" style="text-align:center" name="OHASTA" value="<?php echo $_POST['OHASTA']; ?>"></span></div>
				</td>
				<td width="3%">
					<div align="center">
						<?php
						if ($_POST['OHASTA'] <> "" and $_POST['ODESDE'] <> "") {
							echo ($_POST['OHASTA'] - $_POST['ODESDE'] + 1);
						} else {
							echo 0;
						}
						?>
					</div>
				</td>

			</tr>
		</table>

		<p align="center"><input type="submit" class="boton" name="CMDADDSERIAL" value="Agregar Serial"></p>
		<table width="50%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Seriales Registrados</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Descripcion:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Serial Desde :</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Serial Hasta:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Opcion</strong></div>
				</td>
			</tr>
			<?php
			// --- ITEMS YA REGISTRADOS
			$consulta = "SELECT id_ingreso_serial, timbre_ingresos_seriales.codigo, timbre_ingresos_seriales.cantidad, timbre_ingresos_seriales.numero_ingreso, timbre_ingresos_seriales.serial_desde, timbre_ingresos_seriales.serial_hasta, timbre_inv.descripcion FROM timbre_ingresos_seriales INNER JOIN timbre_inv ON timbre_inv.codigo = timbre_ingresos_seriales.codigo WHERE timbre_ingresos_seriales.numero_ingreso='" . $_POST['OORDEN'] . "'";

			$tabla = mysql_query($consulta);

			$monto = 0;

			while ($registrox = mysql_fetch_object($tabla)) {
			?>
				<tr>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->codigo; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->descripcion; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->serial_desde; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo $registrox->serial_hasta; ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($registrox->cantidad), 2, ',', '.') ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><input type="submit" class="boton" name="E2#<?php echo $registrox->id_ingreso_serial; ?>" value="Eliminar" /></span></div>
					</td>
				</tr>
			<?php
				$monto += $registrox->cantidad * $registrox->precio;
			}

			?>
		</table>

		<p></p>
		<?php include "0_funcion_porcentaje.php"; ?>
		<table align="center" width="50%">
			<tr>
				<td width="69%" align="right">
				</td>
				<td width="31%">
					<table border="1" align="right">
						<tr>
							<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Totales</u></span></td>
						</tr>
						<tr>
							<td bgcolor="#00FF00">
								<div align="right"><strong>Total Orden:</strong></div>
							</td>
							<td bgcolor="#00FF00">
								<div align="center"><strong><span class="Estilo15">BsS. <?php echo number_format(doubleval($_POST['OMONTO']), 2, ',', '.'); ?></span></strong></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<td colspan="2">
				<div align="center">
					<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar" onClick="reset()">
				</div>
			</td>
		</table>
		</p>

		<a name="vista"></a>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>