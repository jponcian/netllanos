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

	?>

	<title>Expediente Por transferir</title>
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

		.Estilo5 {
			font-size: 12px
		}

		.Estilo7 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}

		.Estilo13 {
			font-size: 16px
		}

		.Estilo14 {
			font-size: 16px;
			font-weight: bold;
		}

		.Estilo15 {
			font-size: 14px;
		}

		.Estilo16 {
			color: #FF0000
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<form name="form1" method="post" action="">
		<table width="75%" border=1 align=center>
			<tr>
				<?php
				$consulta = "SELECT contribuyente, rif, direccion FROM vista_contribuyentes_direccion WHERE (((rif)='" . $_GET['rif'] . "'));";
				$tabla = mysql_query($consulta);
				$registro = mysql_fetch_object($tabla)
				?>
				<td bgcolor="#FF0000" height="40" colspan="8" align="center">
					<p class="Estilo7"><u>Contribuyente</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC" height=27>
					<div align="center" class="Estilo14">Rif</div>
				</td>
				<td bgcolor="#CCCCCC" colspan="3">
					<div align="center" class="Estilo14">Contribuyente</div>
				</td>
			</tr>
			<tr>
			<tr>
				<td width="19%" height=27>
					<div align="center" class="Estilo15"><?php echo formato_rif($registro->rif); ?></div>
				</td>
				<td colspan="3">
					<div align="left" class="Estilo15"><?php echo $registro->contribuyente; ?></div>
				</td>
			</tr>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC" height=27 align="center" colspan="6">
					<div align="center" class="Estilo14">Domicilio Fiscal</div>
				</td>
			</tr>
			</tr>
			<tr>
				<td colspan="6">
					<div align="left" class="Estilo15"><?php echo $registro->direccion; ?></div>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="75%" border=1 align=center>
			<tr>
				<td bgcolor="#FF0000" height="40" colspan="8" align="center">
					<p class="Estilo7"><u>Resumen del Acta</u></p>
				</td>
			</tr>
			<tr>
				<td width="17%" colspan="2" bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">Numero</span></a></strong></div>
				</td>
				<td bgcolor="#CCCCCC" width="19%">
					<div align="center" class="Estilo13"><strong>Ejercicio � Periodo</strong></div>
				</td>
				<td bgcolor="#CCCCCC" width="19%">
					<div align="center" class="Estilo13"><strong>Reparo</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Impuesto Omitido</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Monto Pagado</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Notificacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Fecha Transferencia</strong></div>
				</td>
			</tr>
			<?php
			$consulta = "SELECT reparo, impuesto_omitido, monto_pagado, numacta, date_format(periodo_desde, '%d-%m-%Y') as periodo_desde, date_format(periodo_hasta, '%d-%m-%Y') as periodo_hasta, date_format(fecha_notificacion, '%d-%m-%Y') as fecha_notificacion, date_format(fecha_transferencia, '%d-%m-%Y') as fecha_transferencia_sumario FROM vista_sumario_exp_transferido WHERE status=" . $_GET['status'] . " AND rif='" . $_GET['rif'] . "' AND anno=" . $_GET['anno'] . " AND numero=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . " ORDER BY anno, numero;";
			$tabla = mysql_query($consulta);

			while ($registro = mysql_fetch_object($tabla)) {
				echo '<tr><td  colspan="2" ><div align="center" class="Estilo15">';
				echo $registro->numacta;
				echo '</div></td><td height=27><div align="center" class="Estilo15">';
				echo date("d/m/Y", strtotime($registro->periodo_desde)) . ' al ' . date("d/m/Y", strtotime($registro->periodo_hasta));
				echo '</div></td><td ><div align="center" class="Estilo15">';
				echo '<label>' . formato_moneda($registro->reparo) . '</label>';
				echo '</div></td><td ><div align="center" class="Estilo15">';
				echo '<label>' . formato_moneda($registro->impuesto_omitido) . '</label>';
				echo '</div></td><td ><div align="center" class="Estilo15">';
				echo '<label>' . formato_moneda($registro->monto_pagado) . '</label>';
				echo '</div></td><td ><div align="center" class="Estilo15">';
				echo '<label>' . $registro->fecha_notificacion . '</label>';
				echo '</div></td><td ><div align="center" class="Estilo15">';
				echo '<label>' . $registro->fecha_transferencia_sumario . '</label>';
				echo '</div></td> </tr>';
			}
			?>
		</table>
		<p></p>
		<?php
		$_SESSION['ANNO_PRO'] = $_GET['anno'];
		$_SESSION['NUM_PRO'] = $_GET['num'];
		$_SESSION['SEDE'] = $_GET['sector'];
		$mostrarboton = 'NO';
		$serie = "1=1";
		include "../funciones/0_sanciones_aplicadas.php";
		?>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>