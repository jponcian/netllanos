<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 92;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------

$validacion = 1;

// --------- PARA REVISAR SI TIENE CONTRATOS VIGENTES
if ($_POST['ORIF'] <> "") {
	$consulta = "SELECT * FROM expedientes_fraccionamiento WHERE rif='" . $_POST['ORIF'] . "' AND status=1;";
	$tablax = mysql_query($consulta);
	if ($registrox = mysql_fetch_object($tablax)) {
		$validacion = 2;
		$_POST['ORIF_R'] = $registrox->representante;
		$_POST['OCUOTAS'] = $registrox->cuotas;
		echo "<script type=\"text/javascript\">alert('�Este Contribuyente posee un Fraccionamiento sin culminar!');</script>";
	}
}

// --------- BUSQUEDAS ----------
if ($_POST['ORIF'] <> "") {
	$consultax = "SELECT contribuyente, direccion FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax)) {
		$Contribuyente = $registrox->contribuyente;
		$Direccion = $registrox->direccion;
		if ($_POST['ORIF_R'] == '' and $_POST['OCUOTAS'] == 2) {
			// ----- PARA REVISAR SI HAY CONTRATO VIGENTE POR PRIMERA VEZ QUE ENTRA A LA PAGINA
			$consulta = "SELECT * FROM expedientes_fraccionamiento WHERE rif='" . $_POST['ORIF'] . "' AND (status=0 OR status=1);";
			$tablax = mysql_query($consulta);
			if ($registrox = mysql_fetch_object($tablax)) {
				$_POST['ORIF_R'] = $registrox->representante;
				$_POST['OCUOTAS'] = $registrox->cuotas;
			}
		}
	} else {
		$validacion = 0;
		echo "<script type=\"text/javascript\">alert('No Existe Contribuyente Registrado con ese Rif');</script>";
	}
}

if ($_POST['ORIF_R'] <> "") {
	$consultax = "SELECT contribuyente FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF_R'] . "';";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax)) {
		$representante = $registrox->contribuyente;
	} else {
		$validacion = 0;
		echo "<script type=\"text/javascript\">alert('No Existe Contribuyente Registrado con ese Rif');</script>";
	}
}

// --------- FRACCIONAR PLANILLAS
if ($_POST['ORIF'] <> "" and $_POST['ORIF_R'] <> "" and $validacion == 1) {
	// BUSQUEDA DE PLANILLAS PARA FRACCIONAR
	$consulta = "SELECT id_liquidacion, liquidacion FROM liquidacion, a_sancion WHERE (a_sancion.id_sancion = liquidacion.id_sancion OR a_sancion.id_sancion_ajuste = liquidacion.id_sancion) AND liquidacion.rif = '" . $_POST['ORIF'] . "' AND (liquidacion.status = 31 or liquidacion.status = 32) AND liquidacion.id_sancion <> 2500 AND liquidacion.id_sancion <> 2501 AND fraccionada=0 AND a_sancion.serie <> 38 ORDER BY liquidacion.liquidacion ASC;";
	$tabla = mysql_query($consulta);
	while ($registro = mysql_fetch_object($tabla)) {
		if ($_POST[$registro->id_liquidacion] == 'Agregar') {
			// ------ ACTUALIZAR EL REGISTRO SELECCIONADO
			$consultax = "UPDATE liquidacion SET fraccionada = 9999, status = 50, fecha_fraccionamiento = date(now()), usuario_fraccionamiento = " . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion=" . $registro->id_liquidacion;
			$tablax = mysql_query($consultax);
			echo "<script type=\"text/javascript\">alert('�La Planilla " . $registro->liquidacion . " fue agregada al Fraccionamiento Exitosamente!');</script>";
		}
	}

	// BUSQUEDA DE PLANILLAS PARA ELIMINAR
	$consulta = "SELECT id_liquidacion, liquidacion FROM liquidacion, a_sancion WHERE (a_sancion.id_sancion = liquidacion.id_sancion OR a_sancion.id_sancion_ajuste = liquidacion.id_sancion) AND liquidacion.rif = '" . $_POST['ORIF'] . "' AND liquidacion.status = 50 AND liquidacion.id_sancion <> 2500 AND liquidacion.id_sancion <> 2501 AND fraccionada=9999 AND a_sancion.serie <> 38 ORDER BY liquidacion.liquidacion ASC;";
	$tabla = mysql_query($consulta);
	while ($registro = mysql_fetch_object($tabla)) {
		if ($_POST[$registro->id_liquidacion] == 'Eliminar') {
			// ------ ACTUALIZAR LA PLANILLA A FRACCIONAR
			$consultax = "UPDATE liquidacion SET fraccionada = 0 WHERE id_liquidacion=" . $registro->id_liquidacion;
			$tablax = mysql_query($consultax);
			// ------			
		}
	}

	// ----- ELIMINAR LOS DATOS DEL EXPEDIENTE
	$consultax = "DELETE FROM expedientes_fraccionamiento WHERE rif='" . $_POST['ORIF'] . "' AND status=0;";
	$tablax = mysql_query($consultax);
	// -----------------

	// TAZA DE INTERES ACTUAL
	$consulta = "SELECT * FROM a_tasa_interes WHERE anno='" . date(Y) . "' ORDER BY anno;";
	$tablax = mysql_query($consulta);
	$registrox = mysql_fetch_object($tablax);
	// -----------------
	switch (date('m')) {
		case "01":
			$tasa = $registrox->enero;
			break;
		case "02":
			$tasa = $registrox->febrero;
			break;
		case "03":
			$tasa = $registrox->marzo;
			break;
		case "04":
			$tasa = $registrox->abril;
			break;
		case "05":
			$tasa = $registrox->mayo;
			break;
		case "06":
			$tasa = $registrox->junio;
			break;
		case "07":
			$tasa = $registrox->julio;
			break;
		case "08":
			$tasa = $registrox->agosto;
			break;
		case "09":
			$tasa = $registrox->septiembre;
			break;
		case "10":
			$tasa = $registrox->octubre;
			break;
		case "11":
			$tasa = $registrox->noviembre;
			break;
		case "12":
			$tasa = $registrox->diciembre;
			break;
	}

	// ------ MONTO TOTAL A FRACCIONAR
	$consulta = "SELECT (liquidacion.monto_ut / liquidacion.concurrencia * liquidacion.especial) AS utdividido FROM liquidacion WHERE liquidacion.rif = '" . $_POST['ORIF'] . "' AND liquidacion.status = 50 AND liquidacion.fraccionada = 9999 AND liquidacion.id_sancion<>2500 and liquidacion.id_sancion<>2501;";
	$tabla = mysql_query($consulta);

	$total = 0;

	while ($registro = mysql_fetch_object($tabla)) {
		$total = $total + ($_SESSION['VALOR_UT_ACTUAL'] * ($registro->utdividido));
	}

	// ------------------------
	if ($total > 0) {
		// ----- AGREGAR LOS DATOS DEL EXPEDIENTE
		$consultax = "INSERT INTO expedientes_fraccionamiento (origen_exp, rif, anno, numero, sector, representante, cargo, monto, cuotas, tasa, usuario_frac, fecha_frac, usuario_aprob, fecha_aprob, status, fecha_proceso) VALUES ('" . $origenF . "', '" . $_POST['ORIF'] . "' , '0', '0', '" . $_SESSION['SEDE_USUARIO'] . "', '" . $_POST['ORIF_R'] . "', 'Representante Legal', " . $total . ", " . $_POST['OCUOTAS'] . ", " . $tasa . ", " . $_SESSION['CEDULA_USUARIO'] . ", date(now()), 0, date(now()), 0, date(now()));";
		$tablax = mysql_query($consultax);
		// -----------------
	}
} else {
	if ($validacion == 2) {
		echo "<script type=\"text/javascript\">alert('�No se puede modificar el Fraccionamiento!');</script>";
	} else {
		echo "<script type=\"text/javascript\">alert('�Ingrese el Contribuyente y el Representante!');</script>";
	}
}
?>

<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Fraccionar Planillas</title>
	<script type='text/JavaScript' src='scw.js'></script>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<div align="center">
		<p align="center">&nbsp;
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
							<input style="text-align:center" type="text" name="ORIF" size="12" maxlength="10" value="<?php echo $_POST['ORIF'] ?>">
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
				<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
					<div align="center"><strong>Rif</strong></div>
				</td>
				<td colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Representante Legal</strong></div>
				</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFFF" colspan="1" height=27>
						<div align="center">
							<input style="text-align:center" type="text" name="ORIF_R" size="12" maxlength="10" value="<?php echo $_POST['ORIF_R'] ?>">
							<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
						</div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $representante;		?></div>
					</td>
				</tr>
			</tbody>

		</table>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="7" align="center">
					<p class="Estilo7"><u>Planillas Disponibles</u></p>
				</td>
				<td bgcolor="#666666" height="27" align="center">
					<p class="Estilo7">Cuotas=></p>
				</td>
				<td bgcolor="#666666">
					<div align="center"><strong>
							<label>
								<select name="OCUOTAS" onChange="this.form.submit()">
									<?php
									$i = 2;
									while ($i <= 36) {
										// ---------- PARA RECORDAR LA OPCION SELECCIONADA
										if ($_POST['OCUOTAS'] == $i) {
											$a = 'selected="selected"';
										} else {
											$a = '';
										}
										// ----------
										echo '<option value="' . $i . '" ' . $a . '>' . $i . '</option>';
										$i++;
									}
									?>
								</select>
							</label>
						</strong></div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Planilla</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T. Primitivo</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T. Actual</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Actual</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fraccionar</strong></div>
				</td>
			</tr>
			<?php

			$consulta = "SELECT id_liquidacion, liquidacion.liquidacion, liquidacion.monto_bs, liquidacion.monto_ut, (liquidacion.monto_bs / liquidacion.concurrencia * liquidacion.especial) AS montodividido, (liquidacion.monto_ut / liquidacion.concurrencia * liquidacion.especial) AS utdividido, date_format(liquidacion.fecha_impresion, '%d/%m/%Y') AS fecha_liquidacion, liquidacion.id_sancion FROM liquidacion, a_sancion WHERE (a_sancion.id_sancion = liquidacion.id_sancion OR a_sancion.id_sancion_ajuste = liquidacion.id_sancion) AND liquidacion.rif = '" . $_POST['ORIF'] . "' AND (liquidacion.status = 31 or liquidacion.status = 32) AND liquidacion.fraccionada = 0 AND liquidacion.id_sancion<>2500 and liquidacion.id_sancion<>2501 AND a_sancion.serie<>38 ORDER BY liquidacion.liquidacion ASC;";
			$tabla = mysql_query($consulta);

			$i = 0;

			while ($registro = mysql_fetch_object($tabla)) {
				// ------ CALCULO DE LA UNIDAD LIQUIDADA
				$consultax = "SELECT * FROM a_valorut WHERE FechaAplicacion<'" . $registro->fecha_liquidacion . "' ORDER BY FechaAplicacion DESC;";
				$tablax = mysql_query($consultax);
				$registrox = mysql_fetch_object($tablax);
				$ut = $registrox->ValorUT;
				//--------------------
				$i++;
				echo '<tr>
		  <td bgcolor="#FFFFFF"><div align="center">' . $i . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . $registro->liquidacion . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registro->montodividido) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registro->utdividido) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registro->montodividido / $registro->utdividido) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . $registro->fecha_liquidacion . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($_SESSION['VALOR_UT_ACTUAL']) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($_SESSION['VALOR_UT_ACTUAL'] * $registro->utdividido) . '</div></td>';

				echo ' <td width="94" bgcolor="#FFFFFF"><div align="center"><input name="' . $registro->id_liquidacion . '" type="submit" value="Agregar" ';

				echo ' ></div></td>	</tr>';
			}

			?>
		</table>
		<br>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="9" align="center">
					<p class="Estilo7"><u>Planillas Fraccionadas</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Planilla</strong></div>
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
					<div align="center"><strong>Valor U.T. Actual</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Actual</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Eliminar</strong></div>
				</td>
			</tr>
			<?php

			$consulta = "SELECT id_liquidacion, liquidacion.liquidacion, liquidacion.monto_bs, liquidacion.monto_ut, (liquidacion.monto_bs / liquidacion.concurrencia * liquidacion.especial) AS montodividido, (liquidacion.monto_ut / liquidacion.concurrencia * liquidacion.especial) AS utdividido, date_format(liquidacion.fecha_impresion, '%d/%m/%Y') AS fecha_liquidacion, liquidacion.id_sancion FROM liquidacion INNER JOIN a_sancion ON a_sancion.id_sancion = liquidacion.id_sancion WHERE liquidacion.rif = '" . $_POST['ORIF'] . "' AND liquidacion.status = 50 AND liquidacion.fraccionada = 9999 AND liquidacion.id_sancion<>2500 and liquidacion.id_sancion<>2501 AND a_sancion.serie<>38 ORDER BY liquidacion.liquidacion ASC;";
			$tabla = mysql_query($consulta);

			$i = 0;
			$monto_a_fraccionar = 0;

			while ($registro = mysql_fetch_object($tabla)) {
				$i++;
				echo '<tr>
		  <td bgcolor="#FFFFFF"><div align="center">' . $i . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . $registro->liquidacion . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registro->montodividido) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registro->utdividido) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registro->montodividido / $registro->utdividido) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . $registro->fecha_liquidacion . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($_SESSION['VALOR_UT_ACTUAL']) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($_SESSION['VALOR_UT_ACTUAL'] * $registro->utdividido) . '</div></td>';

				echo ' <td width="94" bgcolor="#FFFFFF"><div align="center"><input name="' . $registro->id_liquidacion . '" type="submit" value="Eliminar" ';

				echo ' ></div></td>	</tr>';

				$monto_a_fraccionar = $monto_a_fraccionar + $_SESSION['VALOR_UT_ACTUAL'] * ($registro->utdividido);
			}

			?>
		</table>
		<br>

		<?php

		// TAZA DE INTERES ACTUAL
		$consulta = "SELECT * FROM a_tasa_interes WHERE anno='" . date(Y) . "' ORDER BY anno;";
		$tablax = mysql_query($consulta);
		$registrox = mysql_fetch_object($tablax);

		switch (date('m')) {
			case "01":
				$tasa = $registrox->enero;
				break;
			case "02":
				$tasa = $registrox->febrero;
				break;
			case "03":
				$tasa = $registrox->marzo;
				break;
			case "04":
				$tasa = $registrox->abril;
				break;
			case "05":
				$tasa = $registrox->mayo;
				break;
			case "06":
				$tasa = $registrox->junio;
				break;
			case "07":
				$tasa = $registrox->julio;
				break;
			case "08":
				$tasa = $registrox->agosto;
				break;
			case "09":
				$tasa = $registrox->septiembre;
				break;
			case "10":
				$tasa = $registrox->octubre;
				break;
			case "11":
				$tasa = $registrox->noviembre;
				break;
			case "12":
				$tasa = $registrox->diciembre;
				break;
		}
		?>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="5" align="center">
					<p class="Estilo7"><u>Demostracion del Fraccionamiento</u></p>
				</td>
				<td bgcolor="#666666">
					<div align="center">
						<p class="Estilo7"><strong>Interes => <?php echo formato_moneda($tasa); ?></strong></p>
					</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N&deg;</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Inicial</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Mensualidad</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Amortizacion Capital</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Interes Mensual</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Final</strong></div>
				</td>
			</tr>
			<?php

			if ($_POST['OCUOTAS'] > 0) {
				$cuotas = $_POST['OCUOTAS'];
				$z = 1;
				$total = $monto_a_fraccionar;
				// ------------------------
				// ------ CALCULO DE LA MENSUALIDAD
				// tasa de interes mensual
				$tasa_2 =  ($tasa / 100) / 12;
				// -------------
				$mensualidad = 1 / pow((1 + $tasa_2), $cuotas);
				$mensualidad = 1 - $mensualidad;
				if ($total > 0) {
					$mensualidad = ($total * $tasa_2) / $mensualidad;
				}
				// -------------
				// todo lo demas
				$interes = ($total * $tasa_2);
				$amortizacion = $mensualidad - $interes;
				$monto_final = $total - $amortizacion;
				// ------------
				echo '<tr>
		<td bgcolor="#FFFFFF"><div align="center">' . $z . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($total) . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($mensualidad) . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($amortizacion) . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($interes) . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($monto_final) . '</div></td>
		</tr>';
				// ---------------
				$total = $total - $amortizacion;
				// -------------
				$z++;

				while ($z <= $cuotas) {
					// -------------
					// todo lo demas
					$interes = ($total * $tasa_2);
					$amortizacion = $mensualidad - $interes;
					$monto_final = $total - $amortizacion;
					// ------------
					echo '<tr>
			<td bgcolor="#FFFFFF"><div align="center">' . $z . '</div></td>
			<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($total) . '</div></td>
			<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($mensualidad) . '</div></td>
			<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($amortizacion) . '</div></td>
			<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($interes) . '</div></td>
			<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($monto_final) . '</div></td>
			</tr>';
					// ---------------
					$total = $total - $amortizacion;
					// -------------
					$z++;
				}
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