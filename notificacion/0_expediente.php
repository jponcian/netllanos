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

	<title>Expediente</title>
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
		<table width="50%" border=1 align=center>
			<tbody>
				<?php
				$consulta = "SELECT contribuyente, rif FROM contribuyentes WHERE (((rif)='" . $_GET['rif'] . "'));";
				$tabla = mysql_query($consulta);
				$registro = mysql_fetch_object($tabla)
				?>
				<td bgcolor="#FF0000" height="27" colspan="6" align="center">
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
					<td width="19%" height=27>
						<div align="center" class="Estilo15"><?php echo $registro->rif; ?></div>
					</td>
					<td colspan="3">
						<div align="left" class="Estilo15"><?php echo $registro->contribuyente; ?></div>
					</td>

				<tr>
					<td bgcolor="#FF0000" height="27" colspan="6" align="center">
						<p class="Estilo7"><u>Planillas</u></p>
					</td>
				</tr>
				<tr>
					<td width="17%" colspan="2" bgcolor="#CCCCCC">
						<div align="center" class="Estilo5"><strong><span class="Estilo13">Liquidacion</span></a></strong></div>
					</td>
					<td bgcolor="#CCCCCC" width="19%">
						<div align="center" class="Estilo13"><strong>Serie</strong></div>
					</td>
					<td width="18%" bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Monto</strong></div>
					</td>
				</tr>
				<?php
				$consulta = "SELECT monto_bs, concurrencia, especial, serie, liquidacion FROM vista_sanciones_aplicadas WHERE status>=" . $_GET['status'] . " AND status<=" . $_GET['status2'] . " AND rif='" . $_GET['rif'] . "' AND anno_expediente=" . $_GET['anno'] . " AND num_expediente=" . $_GET['num'] . " AND sector=" . $_GET['sector'] . " ORDER BY liquidacion;";
				$tabla = mysql_query($consulta);

				while ($registro = mysql_fetch_object($tabla)) {
					echo '<tr><td  colspan="2" ><div align="center" class="Estilo15">';
					echo $registro->liquidacion;
					echo '</div></td><td height=27><div align="center" class="Estilo15">';
					echo $registro->serie;
					echo '</div></td><td ><div align="center" class="Estilo15">';
					echo '<label>' . formato_moneda($registro->monto_bs / $registro->concurrencia * $registro->especial) . '</label>';
					echo '</div></td> </tr>';
				}
				?>
			</tbody>
		</table>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>