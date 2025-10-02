<html>

<head>
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}
	$acceso = 134;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	if ($_POST['CMDGUARDAR'] == 'Guardar Gesti�n') {
		$consulta = "SELECT id_liquidacion FROM vista_jur_detalle_planillas WHERE status_exp=1 AND rif='" . $_GET['rif'] . "' ORDER BY liquidacion;";
		//echo $consulta;
		$tabla = mysql_query($consulta);
		while ($registro = mysql_fetch_object($tabla)) {
			//----------------------
			$consultax = "UPDATE jur_detalle_expediente SET gestion='" . $_POST['BOTON' . $registro->id_liquidacion] . "', fecha_gestion = date(now()), usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion=" . $registro->id_liquidacion . "";
			//echo $consultax;
			$tablax = mysql_query($consultax);
			//----------------------
		}
		//----- PARA CTUALIZAR EL EXPEDIENTE
		$consultax = "UPDATE expedientes_juridico SET status=2, usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE rif='" . $_GET['rif'] . "' and anno=" . $_GET['anno'] . " and numero=" . $_GET['num'] . " and sector=" . $_GET['sector'] . " and status=1";
		$tablax = mysql_query($consultax);
		//-------------
		echo "<script type=\"text/javascript\">alert('Se ha Actualizado el Expediente, pronto se cerrar� la Pagina!');</script>";
		// PARA CERRAR LA PAGINA
		echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
		exit();
	}
	?>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Gesti&oacute;n Ponente</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<form name="form1" method="post" action="">
		<p>&nbsp;</p>

		<table class="formateada" align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="40" colspan="9" align="center">
					<p class="Estilo7"><u>Planillas Recurridas</u></p>
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
			$consulta = "SELECT liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, periodoinicio, periodofinal FROM vista_jur_detalle_planillas WHERE status_exp=1 AND rif='" . $_GET['rif'] . "' ORDER BY liquidacion;";
			//echo $consulta;
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
							<select name="BOTON<?php echo $registro->id_liquidacion;		?>">
								<option value='Sin Lugar'>Sin Lugar</option>
								<option value='Con Lugar'>Con Lugar</option>
							</select>
						</div>
					</td>
				</tr>
			<?php
			}
			?>

		</table>
		<p align="center">
			<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar Gesti�n">
		</p>
		<p>&nbsp;</p>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>