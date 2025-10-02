<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

// --------- BUSQUEDA ----------
$rif = $_GET['rif'];
$status = $_GET['status'];

if ($rif <> "") {
	// INFORMACION DEL FRACCIONAMIENTO
	$consulta = "SELECT * FROM expedientes_fraccionamiento WHERE rif='" . $rif . "' AND status=" . $status . ";";
	$tablax = mysql_query($consulta);
	$registrox = mysql_fetch_object($tablax);
	// ------------
	$total = $registrox->monto;
	$cuotas = $registrox->cuotas;
	$tasa = $registrox->tasa;
	$rif_r = $registrox->representante;

	// ---------------
	list($Contribuyente, $Direccion) = funcion_contribuyente($rif);
	// ---------------
	list($representante, $Direccion2) = funcion_contribuyente($rif_r);
	// ---------------	
}
?>

<html>

<head>
	<title>Planillas</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='scw.js'></script>
</head>

<body style="background: transparent !important;">
	<p>&nbsp;</p>


	<form name="form1" method="post" action="">
		<table width="55%" border=1 align=center>
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
					<div align="center"><?php echo $rif;		?></div>
				</td>
				<td bgcolor="#FFFFFF" colspan="4">
					<div align="center"><?php echo $Contribuyente;		?></div>
				</td>
			</tr>
			<tr>
				<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
					<div align="center"><strong>Domicilio</strong></div>
				</td>
				<td bgcolor="#FFFFFF" colspan="4">
					<div align="center"><?php echo $Direccion;		?></div>
				</td>
			</tr>
			<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
				<div align="center"><strong>Rif</strong></div>
			</td>
			<td colspan="4" bgcolor="#CCCCCC">
				<div align="center"><strong>Representante Legal </strong></div>
			</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" colspan="1" height=27>
					<div align="center"><?php echo $rif_r;		?></div>
				</td>
				<td bgcolor="#FFFFFF" colspan="4">
					<div align="center">
						<?php echo $representante;		?></div>
				</td>
		</table>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="8" align="center">
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
			</tr>
			<?php

			$consulta = "SELECT id_liquidacion, liquidacion.liquidacion, liquidacion.monto_bs, liquidacion.monto_ut, (liquidacion.monto_bs / liquidacion.concurrencia * liquidacion.especial) AS montodividido, (liquidacion.monto_ut / liquidacion.concurrencia * liquidacion.especial) AS utdividido, date_format(liquidacion.fecha_impresion, '%d/%m/%Y') AS fecha_liquidacion, liquidacion.id_sancion FROM liquidacion INNER JOIN a_sancion ON a_sancion.id_sancion = liquidacion.id_sancion WHERE liquidacion.rif = '" . $rif . "' AND liquidacion.status = 50 AND liquidacion.fraccionada = 9999 AND liquidacion.id_sancion<>2500 and liquidacion.id_sancion<>2501 AND a_sancion.serie<>38 ORDER BY liquidacion.liquidacion ASC;";
			$tabla = mysql_query($consulta);

			$i = 0;

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
				echo ' </tr>';
			}

			?>
		</table>
		<br>

		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="4" align="center">
					<p class="Estilo7"><u>Demostracion del Fraccionamiento</u></p>
				</td>
				<td bgcolor="#666666">
					<div align="center">
						<p class="Estilo7"><strong>% Interes Anual=> <?php echo number_format(doubleval($tasa), 2, ',', '.'); ?></strong></p>
					</div>
				</td>
				<td bgcolor="#666666">
					<div align="center">
						<p class="Estilo7"><strong>Cuotas => <?php echo number_format(doubleval($cuotas), 0, ',', '.'); ?></strong></p>
					</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
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
			if ($cuotas > 0) {
				$z = 1;
				// ------ CALCULO DE LA MENSUALIDAD
				// tasa de interes mensual
				$tasa_2 =  ($tasa / 100) / 12;
				// -------------
				$mensualidad = 1 / pow((1 + $tasa_2), $cuotas);
				$mensualidad = 1 - $mensualidad;
				$mensualidad = ($total * $tasa_2) / $mensualidad;
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

</body>

</html>