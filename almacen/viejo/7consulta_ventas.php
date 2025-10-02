<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 27;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";

//----------
include "0_respaldo_inv_seriales.php";

//----------
if ($_POST['OANNO'] == '') {
	$_POST['OANNO'] = date('Y');
}

//----------
if ($_GET['venta'] == 'si') {
	echo "<script type=\"text/javascript\">alert('Venta registrada exitosamente!');</script>";
}

// ---------
if ($_GET['serial'] == 'si') {
	echo "<script type=\"text/javascript\">alert('Seriales registrados exitosamente!');</script>";
}
?>
<html>

<head>
	<title>Ventas Realizadas</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>
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


			<table width="70%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="32" colspan="5" align="center">
							<p class="Estilo7"><u>Ventas Registradas</u></p>
						</td>
						<td bgcolor="#FF0000" colspan="2">
							<div align="center" class="Estilo7">
								A&ntilde;o =&gt;
								<select name="OANNO" onChange="this.form.submit()">
									<option value="-1"> Seleccione </option>
									<?php
									$consulta_x = "SELECT date_format(timbre_ventas.fecha,'%Y') as anno FROM timbre_ventas GROUP BY anno ORDER BY anno DESC;";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';
										if ($_POST['OANNO'] == $registro_x->anno) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->anno;
										echo '">';
										echo $registro_x->anno;
										echo '</option>';
									}
									?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td bgcolor="#97BFFD" height=30>
							<div align="center" class="Estilo8 Estilo17"><strong>Venta</strong></div>
						</td>
						<td bgcolor="#97BFFD">
							<div align="center" class="Estilo8 Estilo17"><strong>Fecha</strong></div>
						</td>
						<td bgcolor="#97BFFD">
							<div align="center" class="Estilo8 Estilo17"><strong>Licencia</strong></div>
						</td>
						<td bgcolor="#97BFFD">
							<div align="center" class="Estilo8 Estilo17"><strong>Expendedor</strong></div>
						</td>
						<td bgcolor="#97BFFD">
							<div align="center" class="Estilo8 Estilo17"><strong>Liquidacion</strong></div>
						</td>
						<td bgcolor="#97BFFD">
							<div align="center" class="Estilo8 Estilo17"><strong>Monto a Pagar</strong></div>
						</td>
						<td bgcolor="#97BFFD">
							<div align="center" class="Estilo8 Estilo17"><strong>Opcion</strong></div>
						</td>
					</tr>
					<?php
					// CONSULTA DE LAS VENTAS
					$consulta = "SELECT timbre_ventas.numero, date_format(timbre_ventas.fecha,'%d/%m/%Y') as fecha1, timbre_expendedores.licencia, contribuyentes.contribuyente, Year(fecha) AS anno, timbre_ventas.liquidacion, timbre_ventas.total, status FROM (timbre_expendedores INNER JOIN timbre_ventas ON timbre_expendedores.licencia = timbre_ventas.licencia) INNER JOIN contribuyentes ON timbre_expendedores.rif = contribuyentes.Rif WHERE date_format(timbre_ventas.fecha,'%Y')=0" . $_POST['OANNO'] . " ORDER BY timbre_ventas.numero DESC;";
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
							<td bgcolor="#<?php echo $color; ?>">
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->fecha1; ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>">
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->licencia; ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>">
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->contribuyente; ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>">
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo $registro->anno . '02010001243' . sprintf("%005s", $registro->liquidacion); ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>">
								<div align="center" class="Estilo8">
									<span class="Estilo18"><?php echo number_format(doubleval($registro->total), 2, ',', '.'); ?></span>
								</div>
							</td>
							<td bgcolor="#<?php echo $color; ?>">
								<div align="center" class="Estilo8"><?php
																	$a = '<a href="formatos/planilla_venta.php?num=' . $registro->numero . '&anno=' . $registro->anno . '" target="_blank">Planilla</a>';
																	$b = '<a href="formatos/planilla_liquidacion.php?num=' . $registro->numero . '&anno=' . $registro->anno . '" target="_blank">Liquidacion</a>';
																	$c = '<a href="formatos/planilla_seriales.php?num=' . $registro->numero . '&anno=' . $registro->anno . '" target="_blank">Seriales</a>';
																	//--------------
																	if ($registro->status == 9) {
																		echo 'Anulada';
																	}
																	if ($registro->status == 0) {
																		echo $a . ' / ' . $b;
																	}
																	if ($registro->status == 1) {
																		echo $a . ' / ' . $b . ' / ' . $c;
																	}
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