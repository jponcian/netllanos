<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Providencias sin Incluir en Anexo 2</title>
</head>

<body style="background: transparent !important;">
	<?php echo "<br/>";
	$fecha = date("d-m-Y");

	echo '<p align="center">Providencias Notificadas en NETLOSLLANOS y sin incluir en el Anexo 2 al ' . $fecha . '<p>';

	?>

	</tr>

	<table width="40%" border="1" cellpadding="1" cellspacing="0" align="center">
		<tr>
			<td align="center">Año Prov.</td>
			<td align="center">Nro Prov</td>
			<td align="center">Fiscal</td>
			<td align="center">Supervisor</td>
			<td align="center">Sector/Sede</td>
		</tr>
		<?php
		//consulta sede;
		$sector = $_GET['sector']; // "LLANOS";

		switch ($sector) {
			case "LLANOS":
				$ubica = "SEDE";
				break;
			case "SFA":
				$ubica = "SFA";
				break;
			case "SJM":
				$ubica = "SJM";
				break;
			case "VLP":
				$ubica = "VLP";
				break;
			case "ALT":
				$ubica = "ALT";
				break;
		}

		$año = date("Y");
		$mes = date("m");
		$fecha1 = "01-" . $mes . "-" . $año;
		$fecha1 = date("m-d-Y", strtotime($fecha1));
		$fecha_sig = "01-" . ($mes + 1) . "-" . $año;
		$fecha_sig = date("d-m-Y", strtotime($fecha_sig));
		$fecha2 = strtotime('-1 day', strtotime($fecha_sig));
		$fecha2 = date('m-d-Y', $fecha2);

		$conn = odbc_connect($sector, "Admin", "losllanos");
		$consulta = "SELECT * FROM CS_Notificadas WHERE (FechaNotificacion BETWEEN #$fecha1# AND #$fecha2#) AND Anno=$año AND Programa='Verificacion' ORDER BY NroAutorizacion ASC";

		if ($conn) {
			$tabla = odbc_exec($conn, $consulta);
			//print odbc_result_all($tabla,"border=1");
			while ($fila = odbc_fetch_object($tabla)) {
				$sqlAnexo = "SELECT count(Anno_Providencia) as num FROM Anexo2 WHERE Anno_Providencia=$fila->Anno AND NroAutorizacion=$fila->NroAutorizacion";
				//echo $sqlAnexo ;
				$rs = odbc_exec($conn, $sqlAnexo);
				$anexo = odbc_fetch_object($rs);
				$cantidad = $anexo->num;
				if ($cantidad == 0) {
					//echo 'Cantidad: '.$anexo->num.'<br/>';
		?>
					<tr>
						<td><?php echo $fila->Anno ?></td>
						<td><?php echo $fila->NroAutorizacion ?></td>
						<td><?php echo $fila->Fiscal ?></td>
						<td><?php echo $fila->Supervisor ?></td>
						<td><?php echo $ubica ?></td>
					</tr><?php
						}
					}
				} else {
					die("No se pudo conectar con la base de datos");
				} ?>
	</table>
</body>

</html>