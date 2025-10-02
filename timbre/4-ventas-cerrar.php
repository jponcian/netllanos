<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";


if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 26;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//----------
include "0_respaldo_inv_seriales.php";
?>
<html>

<head>
	<meta http-equiv="refresh" content="10">
	<title>Registrar Seriales</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
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

		.Estilo17 {
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
	<form name="form1" method="post">
		<div align="center">
			<table width="65%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="32" colspan="10" align="center">
							<p class="Estilo7"><u>Ventas Registradas por Entregar</u></p>
						</td>
					</tr>
					<tr>
						<td bgcolor="#0033CC" height=30>
							<div align="center" class="Estilo8 Estilo17"><strong>Venta</strong></div>
						</td>
						<td bgcolor="#0033CC">
							<div align="center" class="Estilo8 Estilo17"><strong>Fecha</strong></div>
						</td>
						<td bgcolor="#0033CC">
							<div align="center" class="Estilo8 Estilo17"><strong>Licencia</strong></div>
						</td>
						<td bgcolor="#0033CC">
							<div align="center" class="Estilo8 Estilo17"><strong>Expendedor</strong></div>
						</td>
						<td bgcolor="#0033CC">
							<div align="center" class="Estilo8 Estilo17"><strong>Liquidacion</strong></div>
						</td>
						<td bgcolor="#0033CC">
							<div align="center" class="Estilo8 Estilo17"><strong>Monto a Pagar</strong></div>
						</td>
						<td bgcolor="#0033CC">
							<div align="center" class="Estilo8 Estilo17"><strong>Opcion</strong></div>
						</td>
					</tr>
					<?php
					// CONSULTA DE LAS VENTAS
					$consulta = "SELECT timbre_ventas.numero, date_format(timbre_ventas.fecha,'%d/%m/%Y') as fecha1, timbre_expendedores.licencia, contribuyentes.contribuyente, Year(fecha) AS anno, timbre_ventas.liquidacion, timbre_ventas.total, status FROM (timbre_expendedores INNER JOIN timbre_ventas ON timbre_expendedores.licencia = timbre_ventas.licencia) INNER JOIN contribuyentes ON timbre_expendedores.rif = contribuyentes.Rif WHERE status=0 ORDER BY timbre_ventas.numero DESC;";
					$tabla = mysql_query($consulta);

					$I = 1;

					while ($registro = mysql_fetch_object($tabla)) {
						if ($I % 2 == 0) {
							$color = 'CCCCCC';
						} else {
							$color = 'FFFFFF';
						}
					?>
						<tr>
							<td bgcolor="#<?php echo $color; ?>" height=27>
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $I; ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>" height=27>
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->fecha1; ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>" height=27>
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->licencia; ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>" height=27>
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->contribuyente; ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>" height=27>
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->anno . '02010001243' . sprintf("%005s", $registro->liquidacion); ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>" height=27>
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo number_format(doubleval($registro->total), 2, ',', '.'); ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>" height=27>
								<div align="center" class="Estilo8"><?php
																	echo '<a href="4.1-ventas-cerrar.php?num=' . $registro->numero . '&anno=' . $registro->anno . '">Cerrar</a>';
																	?>
									</span></div>
							</td>
						</tr>
					<?php
						$I++;
					}

					$_SESSION['VARIABLE1'] = $I;

					?>
				</tbody>
			</table>

		</div>
	</form>

	<p>&nbsp;</p>
	<p>&nbsp;</p>
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