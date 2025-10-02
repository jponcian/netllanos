<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";


if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
//----------
include "0_respaldo_inv_seriales.php";

// CONSULTA CON LOS DATOS
$consulta = "SELECT vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.direccion, timbre_expendedores.tipo FROM vista_contribuyentes_direccion INNER JOIN timbre_expendedores ON timbre_expendedores.rif = vista_contribuyentes_direccion.rif  WHERE timbre_expendedores.licencia=" . $_SESSION['LICENCIA'] . ";";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);

// DATOS DEL EXPENDEDOR
$tipo = $registro->tipo;
$rif = $registro->rif;
$contribuyente = $registro->contribuyente;
$direccion = $registro->direccion;

// VENTA SIGUIENTE
$consulta = "SELECT max(numero) as num FROM timbre_ventas;";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);

$num = $registro->num + 1;
$_SESSION['VARIABLE'] = $num;
// -------------

// LIQUIDACION SIGUIENTE
$consulta = "SELECT max(liquidacion) as liq FROM timbre_ventas WHERE Year(Fecha)=Year(Date(now()));";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);

$liq = $registro->liq + 1;
// -------------

// CALCULO DEL PORCENTAJE
include "0_funcion_porcentaje.php";

// PARA VALIDAR SI VA A CERRAR LA VENTA
if ($_POST['CMDGUARDAR'] == 'Guardar') {
	$consulta = "SELECT Sum((timbre_inv.precio*timbre_ventas_detalle_temporal.cantidad)) AS monto FROM timbre_ventas_detalle_temporal INNER JOIN timbre_inv ON timbre_ventas_detalle_temporal.codigo = timbre_inv.codigo;";
	$tabla = mysql_query($consulta);
	if ($registro = mysql_fetch_object($tabla)) {
		// VENTA
		$consulta = "INSERT INTO timbre_ventas ( numero, fecha, licencia, monto, comision, total, liquidacion, status) VALUES (" . $num . ", Date(now()), " . $_SESSION['LICENCIA'] . ", " . $registro->monto . ", " . $registro->monto / 100 * $porcentaje . ", " . formato_moneda2($registro->monto - ($registro->monto / 100 * $porcentaje)) . ", " . $liq . ",0);";
		$tablax = mysql_query($consulta);
		echo $consulta;
		// ---------
		// DETALLE DE LA VENTA
		$consulta = "INSERT INTO timbre_ventas_detalle ( numero_venta, codigo, cantidad) SELECT " . $num . ", codigo, cantidad FROM timbre_ventas_detalle_temporal;";
		$tablax = mysql_query($consulta);
		// ---------
		// ACTUALIZAR LA CANTIDAD COMPROMETIDA
		$consulta = "SELECT * FROM timbre_ventas_detalle_temporal;";
		$tablax = mysql_query($consulta);
		while ($registro = mysql_fetch_object($tablax)) {
			$consulta = "UPDATE timbre_inv SET comp = comp+" . $registro->cantidad . " WHERE codigo='" . $registro->codigo . "';";
			$tablaxx = mysql_query($consulta);
		}
		// ---------
		// ELIMINAR DETALLES DE LA VENTA
		$consulta = "DELETE FROM timbre_ventas_detalle_temporal;";
		$tablax = mysql_query($consulta);
		// ---------
		$_POST['OCANTIDAD'] = '';
		// ---------
		header("Location: 7consulta_ventas.php?venta=si");
		exit();
	} else {
		echo "<script type=\"text/javascript\">alert('No ha registrado Item(s)!');</script>";
	}
}
//

// PARA VALIDAR SI AGREG� ITEM
if ($_POST['CMDAGREGAR'] == 'Agregar') {
	if ($_POST['OCODIGO'] <> '-1' and $_POST['OCANTIDAD'] > 0) {
		//--------- VALIDAR LA CANTIDAD
		$consulta3 = "SELECT cant-comp as disp, indice FROM VISTA_TIMBRE_SUMA_INV_SERIALES WHERE codigo='" . $_POST['OCODIGO'] . "';";
		$tabla3 = mysql_query($consulta3);
		$registro3 = mysql_fetch_object($tabla3);
		//---------
		if ($registro3->disp >=  $_POST['OCANTIDAD']) {
			$consulta = "INSERT INTO timbre_ventas_detalle_temporal ( indice, codigo, cantidad) VALUES (" . $registro3->indice . ",'" . $_POST['OCODIGO'] . "'," . $_POST['OCANTIDAD'] . ");";
			$tablax = mysql_query($consulta);
			//-----------------
			$_POST['OCANTIDAD'] = '0';
			//-------------
		} else {
			echo "<script type=\"text/javascript\">alert('No hay suficiente existencia!');</script>";
			//-----------------
			$_POST['OCANTIDAD'] = number_format(doubleval($registro3->disp), 0, '', '');
			//-------------
		}
	} else {
		echo "<script type=\"text/javascript\">alert('Seleccione el codigo y escriba una cantidad de venta v�lida!');</script>";
	}
}
//

// PARA VALIDAR SI ELIMIN� ITEM
$consulta = "SELECT indice FROM timbre_ventas_detalle_temporal;";
$tablax = mysql_query($consulta);
// ------------
while ($registrox = mysql_fetch_object($tablax)) {
	if ($_POST['E' . $registrox->indice . ''] == 'Eliminar') {
		$consulta = "DELETE FROM timbre_ventas_detalle_temporal WHERE indice =" . $registrox->indice . ";";
		$tablaxx = mysql_query($consulta);
		echo "<script type=\"text/javascript\">alert('Item Eliminado!');</script>";
	}
}
//
?>
<html>

<head>
	<title>Registrar Venta</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />

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

		<table width="65%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Expendedor</u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Venta:</strong></div>
				</td>
				<td width="7%">
					<div align="center"><span class="Estilo15"><?php echo $num; ?></span></div>
				</td>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Licencia:</strong></div>
				</td>
				<td width="7%">
					<div align="center"><span class="Estilo15"><?php echo $_SESSION['LICENCIA']; ?></span></div>
				</td>
				<td width="9%" bgcolor="#CCCCCC">
					<div align="center"><strong>Rif:</strong></div>
				</td>
				<td width="9%">
					<div align="center"><span class="Estilo15"><?php echo $rif; ?></span></div>
				</td>
				<td width="9%" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha:</strong></div>
				</td>
				<td width="9%">
					<div align="center"><span class="Estilo15"><?php echo date("d/m/Y"); ?></span></div>
				</td>
			</tr>
		</table>

		<table width="65%" border="1" align="center">
			<tr>
				<td width="15%" bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente:</strong></div>
				</td>
				<td><label><span class="Estilo15"><?php echo $contribuyente; ?></span></label></td>
			</tr>
			<tr>
				<td width="15%" bgcolor="#CCCCCC">
					<div align="center"><strong>Direccion:</strong></div>
				</td>
				<td><label><span class="Estilo15"><?php echo $direccion; ?></span></label></td>
			</tr>
		</table>

		<table width="65%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Items</u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>

				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Disponibles:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor Facial Bs:</strong></div>
				</td>
				<td width="12%" bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad:</strong></div>
				</td>
				<td width="12%" bgcolor="#CCCCCC">
					<div align="center"><strong>Total Bs:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="30%"><label>
					</label>
					<div align="center">
						<select name="OCODIGO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							//SI ES OFICIAL VENDE FORMAS
							if ($tipo == 'Libre') {
								$comando = " AND VISTA_TIMBRE_SUMA_INV_SERIALES.grupo='TIMBRES'";
							}
							if ($tipo == 'Oficial') {
								$comando = " AND VISTA_TIMBRE_SUMA_INV_SERIALES.grupo='FORMAS'";
							}
							//-----------
							$consulta3 = "SELECT DISTINCTROW VISTA_TIMBRE_SUMA_INV_SERIALES.codigo, VISTA_TIMBRE_SUMA_INV_SERIALES.descripcion, VISTA_TIMBRE_SUMA_INV_SERIALES.cant, VISTA_TIMBRE_SUMA_INV_SERIALES.comp, VISTA_TIMBRE_SUMA_INV_SERIALES.precio, VISTA_TIMBRE_SUMA_INV_SERIALES.grupo, VISTA_TIMBRE_SUMA_INV_SERIALES.indice FROM VISTA_TIMBRE_SUMA_INV_SERIALES LEFT JOIN timbre_ventas_detalle_temporal ON VISTA_TIMBRE_SUMA_INV_SERIALES.codigo = timbre_ventas_detalle_temporal.codigo WHERE (((timbre_ventas_detalle_temporal.codigo) Is Null)) " . $comando . " ORDER BY VISTA_TIMBRE_SUMA_INV_SERIALES.indice;";
							$tabla3 = mysql_query($consulta3);
							while ($registro3 = mysql_fetch_object($tabla3)) {
								echo '<option';
								if ($_POST['OCODIGO'] == $registro3->codigo) {
									echo ' selected="selected" ';
									$precio = $registro3->precio;
									$cant = $registro3->cant;
									$disp = $registro3->cant - $registro3->comp;
								}
								echo ' value="';
								echo $registro3->codigo;
								echo '">';
								echo $registro3->codigo . " - " . $registro3->descripcion;
								echo '</option>';
							}
							?>
						</select>
					</div>
					<div align="center"></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($disp), 0, ',', '.'); ?></span></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($precio), 2, ',', '.'); ?></span></div>
				</td>
				<td width="3%">
					<div align="center">
						<input type="text" name="OCANTIDAD" size="4" value="<?php echo $_POST['OCANTIDAD']; ?>" style="text-align:center">
					</div>
				</td>
				<td width="5%">
					<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($precio * $_POST['OCANTIDAD']), 2, ',', '.'); ?></span></div>
				</td>
			</tr>
		</table>
		<p align="center"><input type="submit" class="boton" name="CMDAGREGAR" value="Agregar"></p>
		<table width="65%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Items Registrados</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Descripcion:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor Facial Bs:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Total Bs:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Opcion</strong></div>
				</td>
			</tr>
			<?php
			// --- ITEMS YA REGISTRADOS
			$consulta = "SELECT timbre_inv.codigo, timbre_inv.descripcion, timbre_inv.precio, timbre_ventas_detalle_temporal.cantidad, timbre_ventas_detalle_temporal.indice FROM timbre_ventas_detalle_temporal INNER JOIN timbre_inv ON timbre_ventas_detalle_temporal.codigo = timbre_inv.codigo;";
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
						<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($registrox->precio), 2, ',', '.'); ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($registrox->cantidad), 0, ',', '.'); ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($registrox->cantidad * $registrox->precio), 2, ',', '.') ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><input type="submit" class="boton" name="E<?php echo $registrox->indice; ?>" value="Eliminar" /></span></div>
					</td>
				</tr>
			<?php
				$monto += $registrox->cantidad * $registrox->precio;
			}

			?>
		</table>

		<p></p>
		<?php include "0_funcion_porcentaje.php"; ?>
		<table align="center" width="65%">
			<tr>
				<td width="69%" align="right"><input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></td>
				<td width="31%">
					<table border="1" align="right">
						<tr>
							<td align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Totales</u></span></td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC">
								<div align="right"><strong>Venta:</strong></div>
							</td>
							<td>
								<div align="center"><strong><span class="Estilo15">BsS. <?php echo number_format(doubleval($monto), 2, ',', '.'); ?></span></strong></div>
							</td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC">
								<div align="right"><strong>Comisi&oacute;n: (<?php echo $porcentaje; ?>%)</strong></div>
							</td>
							<td>
								<div align="center"><strong><span class="Estilo15">BsS. <?php echo number_format(doubleval($monto / 100 * $porcentaje), 2, ',', '.'); ?></span></strong></div>
							</td>
						</tr>
						<tr>
							<td bgcolor="#00FF00">
								<div align="right"><strong>Total a Pagar :</strong></div>
							</td>
							<td bgcolor="#00FF00">
								<div align="center"><strong><span class="Estilo15">BsS. <?php echo number_format(doubleval($monto - ($monto / 100 * $porcentaje)), 2, ',', '.'); ?></span></strong></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
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