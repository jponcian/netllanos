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
$consulta = "SELECT Year(timbre_ventas.fecha) AS anno, vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.direccion AS Direccion, timbre_ventas.liquidacion, timbre_ventas.numero, timbre_ventas.fecha FROM vista_contribuyentes_direccion INNER JOIN timbre_expendedores ON timbre_expendedores.rif = vista_contribuyentes_direccion.rif INNER JOIN timbre_ventas ON timbre_ventas.licencia = timbre_expendedores.licencia WHERE (((timbre_ventas.numero)=" . $_GET['num'] . ") AND (Year(timbre_ventas.fecha)='" . $_GET['anno'] . "'));";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);

// DATOS DEL EXPENDEDOR
$rif = $registro->rif;
$contribuyente = $registro->contribuyente;
$direccion = $registro->Direccion;
$liquidacion = $registro->liquidacion;
$anno = $registro->anno;
// -------------

// PARA VALIDAR SI VA A CERRAR LA VENTA
if ($_POST['CMDGUARDAR'] == 'Guardar') {
	if ($_POST['OPLANILLA'] <> '') {
		$guardar = 'si';
		// VALIDAR SI LOS SERIALES ESTAN COMPLETOS
		// ESPECIES VENDIDAS
		$consulta = "SELECT timbre_ventas_detalle.codigo, timbre_ventas_detalle.cantidad
FROM timbre_ventas_detalle INNER JOIN timbre_ventas ON timbre_ventas_detalle.numero_venta = timbre_ventas.numero
WHERE (((Year(fecha))='" . $_GET['anno'] . "') AND ((timbre_ventas.numero)=" . $_GET['num'] . "));";
		$tabla = mysql_query($consulta);
		while ($registro = mysql_fetch_object($tabla)) {
			$vendidas = $registro->cantidad;
			$registradas = 0;
			$diferencia = 0;
			// -----------------
			$consulta = "SELECT timbre_ventas_seriales_temporal.codigo, Sum(timbre_ventas_seriales_temporal.cantidad) AS SumaDecantidad FROM timbre_ventas_seriales_temporal GROUP BY timbre_ventas_seriales_temporal.codigo, timbre_ventas_seriales_temporal.numero_venta, timbre_ventas_seriales_temporal.anno_venta HAVING (((timbre_ventas_seriales_temporal.codigo)='" . $registro->codigo . "') AND ((timbre_ventas_seriales_temporal.numero_venta)=" . $_GET['num'] . ") AND ((timbre_ventas_seriales_temporal.anno_venta)=" . $_GET['anno'] . "));";
			$tablax = mysql_query($consulta);
			if ($registrox = mysql_fetch_object($tablax)) {
				$registradas = $registrox->SumaDecantidad;
				//------------
				$diferencia =  $vendidas - $registradas;
				//------------
				if ($diferencia <> 0) {
					echo "<script type=\"text/javascript\">alert('La cantidad de Seriales no es correcta!');</script>";
					$guardar = 'no';
				}
			} else {
				// NO HA REGISTRADO LOS SERIALES
				echo "<script type=\"text/javascript\">alert('No ha registrado todos los Seriales!');</script>";
				$guardar = 'no';
			}
		}	// CIERRE DEL WHILE PRINCIPAL

		if ($guardar == 'si') {
			//-------- ACTUALIZAR EL STATUS DE LA VENTA
			$consulta = "UPDATE timbre_ventas SET status=1, cierre=Date(now()), planilla='" . $_POST['OPLANILLA'] . "' WHERE numero=" . $_GET['num'] . " AND year(fecha)='" . $_GET['anno'] . "';";
			$tabla3 = mysql_query($consulta);

			// TABLA TEMPORAL DE SERIALES
			$consulta = "SELECT codigo, serial_desde, serial_hasta, cantidad FROM timbre_ventas_seriales_temporal WHERE (((numero_venta)=" . $_GET['num'] . ") AND ((anno_venta)=" . $_GET['anno'] . ")) ORDER BY codigo, serial_desde;";
			$tabla = mysql_query($consulta);
			while ($registro = mysql_fetch_object($tabla)) {
				// BUSQUEDA DEL SERIAL EN EL INVENTARIO
				$consulta = "SELECT codigo, serial_desde, serial_hasta FROM timbre_inv_detallado WHERE (((codigo)='" . $registro->codigo . "') AND ((serial_desde)=" . $registro->serial_desde . "));";
				//echo $consulta;
				$tabla2 = mysql_query($consulta);
				$registro2 = mysql_fetch_object($tabla2);
				//----------
				// VALIDAR EL SERIAL
				// SI EL ULTIMO SERIAL VENDIDO ES MENOR AL DEL INVENTARIO
				if ($registro->serial_hasta < $registro2->serial_hasta) {
					//-------- ACTUALIZO EL SERIAL DEL INVENTARIO
					$consulta = "UPDATE timbre_inv_detallado SET serial_desde=" . ($registro->serial_hasta + 1) . " WHERE (((codigo)='" . $registro->codigo . "') AND ((serial_desde)=" . $registro->serial_desde . "));";
					$tabla3 = mysql_query($consulta);
					//echo $consulta;
				} else
				// SI EL SERIAL ES IGUAL ENTONCES SE ELIMINA
				{
					//-------- ACTUALIZO EL SERIAL DEL INVENTARIO
					$consulta = "DELETE FROM timbre_inv_detallado WHERE (((codigo)='" . $registro->codigo . "') AND ((serial_desde)=" . $registro->serial_desde . "));";
					$tabla3 = mysql_query($consulta);
					//echo $consulta;
				}
				//-------- CARGO EL SERIAL A LA VENTA
				$consulta = "INSERT INTO timbre_ventas_seriales (numero_venta, anno_venta, codigo, cantidad, serial_desde, serial_hasta, sector) (SELECT timbre_ventas_seriales_temporal.numero_venta, timbre_ventas_seriales_temporal.anno_venta, timbre_ventas_seriales_temporal.codigo, timbre_ventas_seriales_temporal.cantidad, timbre_ventas_seriales_temporal.serial_desde, timbre_ventas_seriales_temporal.serial_hasta, " . $_SESSION['SEDE_USUARIO'] . " FROM timbre_ventas_seriales_temporal WHERE numero_venta=" . $_GET['num'] . " AND anno_venta=" . $_GET['anno'] . " AND codigo='" . $registro->codigo . "' AND serial_desde=" . $registro->serial_desde . ");";
				$tabla3 = mysql_query($consulta);
				//echo $consulta;
				//-------- ACTUALIZAR LA CANTIDAD DE LOS SERIALES RESTANTES EN INVENTARIO
				$consulta = "UPDATE timbre_inv SET comp = comp-" . (($registro->serial_hasta - $registro->serial_desde) + 1) . " WHERE codigo='" . $registro->codigo . "';";
				$tabla3 = mysql_query($consulta);
				//echo $consulta;
			}

			//-------- ELIMINO LOS SERIALES TEMPORALES
			$consulta = "DELETE FROM timbre_ventas_seriales_temporal WHERE numero_venta=" . $_GET['num'] . " AND anno_venta=" . $_GET['anno'] . ";";
			$tabla3 = mysql_query($consulta);
			//echo $consulta;

			//------ REDIRECCIONO PARA QUE IMPRIMA LA HOJA DE SERIALES
			header("Location: 7consulta_ventas.php?serial=si");
			exit();
		}
	} else {
		echo "<script type=\"text/javascript\">alert('No ha cargado el numero de la planilla de liquidaci\u00D3n!');</script>";
	}
}
//

// PARA VALIDAR SI AGREG� ITEM
if ($_POST['CMDAGREGAR'] == 'Agregar') {
	if ($_POST['OCODIGO'] <> '-1' and $_POST['OHASTA'] >= $_POST['ODESDE'] and $_POST['ODESDE'] >= 0) {
		$consulta = "INSERT INTO timbre_ventas_seriales_temporal ( numero_venta, anno_venta, codigo, cantidad, serial_desde, serial_hasta) VALUES ('" . $_GET['num'] . "', '" . $_GET['anno'] . "', '" . $_POST['OCODIGO'] . "','" . ($_POST['OHASTA'] - $_POST['ODESDE'] + 1) . "','" . $_POST['ODESDE'] . "','" . $_POST['OHASTA'] . "');";
		$tablax = mysql_query($consulta);
		//-----------------
		$_POST['OCODIGO'] = '';
		//-------------
	} else {
		echo "<script type=\"text/javascript\">alert('Seleccione el codigo y cargue los seriales correctamente!');</script>";
	}
}
//

// PARA VALIDAR SI ELIMIN� ITEM
$consulta = "SELECT guia FROM timbre_ventas_seriales_temporal;";
if ($tablax = mysql_query($consulta)) {
	// ------------
	while ($registrox = mysql_fetch_object($tablax)) {
		if ($_POST['CMDE' . $registrox->guia] == 'Eliminar') {
			$consulta = "DELETE FROM timbre_ventas_seriales_temporal WHERE guia =" . $registrox->guia . ";";
			$tablaxx = mysql_query($consulta);
			echo "<script type=\"text/javascript\">alert('Item Eliminado!');</script>";
		}
	}
	//
}
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

		.Estilo21 {
			color: #FFFFFF
		}

		.Estilo18 {
			color: #000000
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
	<p align="center"></p>
	<form name="form1" method="post" action="#vista">

		<table width="65%" border="1" align="center">
			<tr>
				<td height="30" align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Expendedor</u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Licencia:</strong></div>
				</td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo 'GRTI-RLL-DR-ATF-' . $_SESSION['LICENCIA']; ?></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Liquidacion:</strong></div>
				</td>
				<td width="8%">
					<div align="center">
						<div align="center"><span class="Estilo18">
								<?php echo $anno . '02010001243' . sprintf("%005s", $liquidacion); ?>
							</span></div>
					</div>
				</td>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha:</strong></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><?php echo date("d/m/Y"); ?></span></div>
				</td>

				<td width="10%" bgcolor="#00FF00">
					<div align="center"><strong>Planilla:</strong></div>
				</td>
				<td bgcolor="#00FF00">
					<div align="center">
						<input style="text-align:center" type="text" size="20" maxlength="15" name="OPLANILLA" value="<?php echo $_POST['OPLANILLA']; ?>">
					</div>
				</td>
			</tr>
		</table>
		<table width="65%" border="1" align="center">
			<tr>
				<td width="13%" bgcolor="#CCCCCC">
					<div align="center"><strong>Rif:</strong></div>
				</td>
				<td width="13%"><label><span class="Estilo15"><?php echo $rif; ?></span></label></td>
				<td width="13%" bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente:</strong></div>
				</td>
				<td><label><span class="Estilo15"><?php echo $contribuyente; ?></span></label></td>
			</tr>
		</table>
		<table width="65%" border="1" align="center">
			<tr>
				<td width="13%" bgcolor="#CCCCCC">
					<div align="center"><strong>Direccion:</strong></div>
				</td>
				<td><label><span class="Estilo15"><?php echo $direccion; ?></span></label></td>
			</tr>
		</table>

		<table width="65%" border="1" align="center">
			<tr>
				<td height="30" align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Especies Fiscales Vendidas </u></span></td>
			</tr>
			<tr>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Descripcion:</strong></div>
				</td>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Valor Facial Bs:</strong></div>
				</td>
				<td colspan="3" bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad:</strong></div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Vendida:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Registrada:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Diferencia:</strong></div>
				</td>
			</tr>
			<?php
			$filtro = '';

			// --- ITEMS YA REGISTRADOS
			$consulta = "SELECT timbre_inv.codigo, timbre_inv.descripcion, timbre_inv.precio, timbre_ventas_detalle.cantidad FROM (timbre_ventas_detalle INNER JOIN timbre_inv ON timbre_ventas_detalle.codigo = timbre_inv.codigo) INNER JOIN timbre_ventas ON timbre_ventas_detalle.numero_venta = timbre_ventas.numero WHERE (((Year(fecha))='" . $_GET['anno'] . "') AND ((timbre_ventas.numero)=" . $_GET['num'] . "));";
			$tabla = mysql_query($consulta);

			$monto = 0;

			while ($registro = mysql_fetch_object($tabla)) {
				$registrada = 0;
				$diferencia = 0;

				$consulta = "SELECT timbre_ventas_seriales_temporal.codigo, Sum(timbre_ventas_seriales_temporal.cantidad) AS SumaDecantidad FROM timbre_ventas_seriales_temporal GROUP BY timbre_ventas_seriales_temporal.codigo, timbre_ventas_seriales_temporal.numero_venta, timbre_ventas_seriales_temporal.anno_venta HAVING (((timbre_ventas_seriales_temporal.codigo)='" . $registro->codigo . "') AND ((timbre_ventas_seriales_temporal.numero_venta)=" . $_GET['num'] . ") AND ((timbre_ventas_seriales_temporal.anno_venta)=" . $_GET['anno'] . "));";
				$tablax = mysql_query($consulta);

				if ($registrox = mysql_fetch_object($tablax)) {
					$registrada = $registrox->SumaDecantidad;
				}

				$diferencia = $registro->cantidad - $registrada;

				// COLOR A LA LINEA
				if ($diferencia == 0) {
					$color = '#00FF00';
					$clase = '';
				}
				if ($diferencia > 0) {
					$color = '#0000FF';
					$clase = 'class="Estilo21"';
					$filtro = $filtro . "'" . $registro->codigo . "',";
				}
				if ($diferencia < 0) {
					$color = '#FFFF00';
					//$clase = 'class="Estilo21"';
					$filtro = $filtro . "'" . $registro->codigo . "',";
				}
			?>
				<tr>
					<td bgcolor="<?php echo $color; ?>">
						<div align="center" <?php echo $clase; ?>><span class="Estilo15"><?php echo $registro->codigo; ?></span></div>
					</td>
					<td bgcolor="<?php echo $color; ?>">
						<div align="center" <?php echo $clase; ?>><span class="Estilo15"><?php echo $registro->descripcion; ?></span></div>
					</td>
					<td bgcolor="<?php echo $color; ?>">
						<div align="center" <?php echo $clase; ?>><span class="Estilo15"><?php echo number_format(doubleval($registro->precio), 2, ',', '.'); ?></span></div>
					</td>
					<td bgcolor="<?php echo $color; ?>">
						<div align="center" <?php echo $clase; ?>><span class="Estilo15"><?php echo number_format(doubleval($registro->cantidad), 0, ',', '.'); ?></span></div>
					</td>
					<td bgcolor="<?php echo $color; ?>">
						<div align="center" <?php echo $clase; ?>><span class="Estilo15"><?php echo number_format(doubleval($registrada), 0, ',', '.'); ?></span></div>
					</td>
					<td bgcolor="<?php echo $color; ?>">
						<div align="center" <?php echo $clase; ?>><span class="Estilo15"><?php echo number_format(doubleval($diferencia), 0, ',', '.'); ?></span></div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>

		<p>&nbsp;</p>
		<table width="65%" border="1" align="center">
			<tr>
				<td height="30" align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Seriales a Entregar</u></span></td>
			</tr>
			<tr>
				<td width="10%" rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>
				<td bgcolor="#CCCCCC" colspan="3">
					<div align="center"><strong>Seriales</strong></div>
				</td>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad Estimada:</strong></div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Desde:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Hasta:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Disponible:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="30%"><label>
					</label>
					<div align="center">
						<?php
						$consulta3 = "SELECT timbre_inv_detallado.codigo, timbre_inv.descripcion, timbre_ventas_detalle.cantidad FROM ((timbre_ventas_detalle INNER JOIN timbre_inv ON timbre_ventas_detalle.codigo = timbre_inv.codigo) INNER JOIN timbre_ventas ON timbre_ventas_detalle.numero_venta = timbre_ventas.numero) INNER JOIN timbre_inv_detallado ON timbre_inv.codigo = timbre_inv_detallado.codigo WHERE timbre_inv_detallado.codigo IN (" . $filtro . "'0') AND (((Year(fecha))=" . $_GET['anno'] . ") AND ((timbre_ventas.numero)=" . $_GET['num'] . ")) GROUP BY timbre_inv_detallado.codigo, timbre_inv.descripcion, timbre_ventas_detalle.cantidad, timbre_inv.indice ORDER BY timbre_inv.indice;";
						$tabla3 = mysql_query($consulta3);
						?>
						<select name="OCODIGO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							while ($registro3 = mysql_fetch_object($tabla3)) {
								echo '<option';
								if ($_POST['OCODIGO'] == $registro3->codigo) {
									echo ' selected="selected" ';
									$vendidas = $registro3->cantidad;
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
					<div align="center">
						<select name="ODESDE" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$hasta = 0;

							$consulta3 = "SELECT timbre_inv_detallado.codigo, timbre_inv_detallado.serial_desde, timbre_inv_detallado.serial_hasta FROM ((timbre_ventas_detalle INNER JOIN timbre_inv ON timbre_ventas_detalle.codigo = timbre_inv.codigo) INNER JOIN timbre_ventas ON timbre_ventas_detalle.numero_venta = timbre_ventas.numero) INNER JOIN timbre_inv_detallado ON timbre_inv.codigo = timbre_inv_detallado.codigo WHERE timbre_inv_detallado.serial_desde NOT IN (SELECT serial_desde FROM timbre_ventas_seriales_temporal) AND timbre_inv_detallado.codigo='" . $_POST['OCODIGO'] . "' AND (((Year(fecha))=" . $_GET['anno'] . ") AND ((timbre_ventas.numero)=" . $_GET['num'] . "));";
							$tabla3 = mysql_query($consulta3);
							while ($registro3 = mysql_fetch_object($tabla3)) {
								echo '<option';
								if ($_POST['ODESDE'] == $registro3->serial_desde) {
									echo ' selected="selected" ';
									$desde = $registro3->serial_desde;
									$hasta = $registro3->serial_hasta;
									//----------- CONSULTA DE SERIALES REGISTRADOS 
									$consulta4 = "SELECT Sum(cantidad) AS SumaDecantidad FROM timbre_ventas_seriales_temporal GROUP BY numero_venta, anno_venta, codigo HAVING (((numero_venta)=" . $_GET['num'] . ") AND ((anno_venta)=" . $_GET['anno'] . ") AND ((codigo)='" . $registro3->codigo . "'));";
									$tabla4 = mysql_query($consulta4);
									$listos = 0;
									if ($registro4 = mysql_fetch_object($tabla4)) {
										$listos = $registro4->SumaDecantidad;
									}
									//-----------------
									if (($registro3->serial_desde + ($vendidas - $listos)) <= $registro3->serial_hasta) {
										$_POST['OHASTA'] = $registro3->serial_desde + ($vendidas - $listos) - 1;
									}
								}
								echo ' value="';
								echo $registro3->serial_desde;
								echo '">';
								echo number_format(doubleval($registro3->serial_desde), 0, ',', '.');
								echo '</option>';
							}
							?>
						</select>
					</div>
				</td>
				<td>
					<div align="center"><input style="text-align:center" type="text" size="10" name="OHASTA" value="<?php

																													if ($_POST['OHASTA'] < 0 or $_POST['OHASTA'] < $desde or $_POST['OHASTA'] > $hasta) {
																														echo $hasta;
																													} else {
																														echo $_POST['OHASTA'];
																													}

																													?>"></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><?php echo '' . number_format(doubleval($desde), 0, ',', '.') . ' al ' . number_format(doubleval($hasta), 0, ',', '.'); ?></span></div>
				</td>
				<td>
					<div align="center"><span class="Estilo15"><?php echo $_POST['OHASTA'] - $_POST['ODESDE'] + 1; ?></span></div>
				</td>
			</tr>
		</table>
		<p align="center"><input type="submit" class="boton" name="CMDAGREGAR" value="Agregar"></p>
		<table width="65%" border="1" align="center">
			<tr>
				<td height="30" align="center" bgcolor="#FF0000" colspan="10"><span class="Estilo7"><u>Seriales Registrados </u></span></td>
			</tr>
			<tr>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Codigo:</strong></div>
				</td>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Descripcion:</strong></div>
				</td>
				<td bgcolor="#CCCCCC" colspan="2">
					<div align="center"><strong>Seriales</strong></div>
				</td>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Cantidad Estimada:</strong></div>
				</td>
				<td rowspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Opcion:</strong></div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Desde:</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Hasta:</strong></div>
				</td>
			</tr>
			<?php
			// --- ITEMS YA REGISTRADOS
			$consulta = "SELECT guia, timbre_inv.codigo, timbre_inv.descripcion, timbre_ventas_seriales_temporal.serial_desde, timbre_ventas_seriales_temporal.serial_hasta, timbre_ventas_seriales_temporal.cantidad FROM timbre_inv INNER JOIN timbre_ventas_seriales_temporal ON timbre_inv.codigo = timbre_ventas_seriales_temporal.codigo WHERE (((timbre_ventas_seriales_temporal.numero_venta)=" . $_GET['num'] . ") AND ((timbre_ventas_seriales_temporal.anno_venta)=" . $_GET['anno'] . "));";
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
						<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($registrox->serial_desde), 0, ',', '.'); ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($registrox->serial_hasta), 0, ',', '.'); ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><?php echo number_format(doubleval($registrox->cantidad), 0, ',', '.'); ?></span></div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><input type="submit" class="boton" name="CMDE<?php echo $registrox->guia; ?>" value="Eliminar" /></span></div>
					</td>
				</tr>
			<?php
			}
			?>

		</table>

		<p>&nbsp;</p>
		<table align="center" width="65%">
			<tr>
				<td width="69%" align="center"><input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></td>
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