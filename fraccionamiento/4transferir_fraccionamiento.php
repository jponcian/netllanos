<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 95;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
?>

<html>

<head>
	<title>Transferir Planillas</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<?php

	if ($_POST['CMDTRANSFERIR'] == 'Transferir') {
		$consulta = "SELECT liquidacion.rif, vista_contribuyentes_direccion.contribuyente as NombreRazon FROM liquidacion, vista_contribuyentes_direccion WHERE liquidacion.rif=vista_contribuyentes_direccion.rif AND liquidacion.status=0 AND liquidacion.origen_liquidacion=" . $origenF . " GROUP BY liquidacion.rif, vista_contribuyentes_direccion.contribuyente;";
		$tabla = mysql_query($consulta);

		while ($registro = mysql_fetch_object($tabla)) {
			if ($_POST[$registro->rif] == $registro->rif) {
				// BUSCAR EL NUMERO DEL INDICE DEL FRACCIONAMIENTO
				$consultax = "SELECT indice, anno, numero FROM expedientes_fraccionamiento WHERE Rif='" . $registro->rif . "' AND status=1;";
				$tablax = mysql_query($consultax);
				$registrox = mysql_fetch_object($tablax);
				$indice = $registrox->indice;
				$anno = $registrox->anno;
				$numero = $registrox->numero;
				// -------------------
				// ------ ACTUALIZAR LAS PLANILLAS NUEVAS
				$consultax = "UPDATE liquidacion SET anno_expediente=" . $anno . ", num_expediente=" . $numero . " , status = 10, fecha_transferencia_a_liq = date(now()), usuario_transferencia_a_liq = " . $_SESSION['CEDULA_USUARIO'] . ", fecha_proceso = date(now()) WHERE rif='" . $registro->rif . "' AND status=0 AND origen_liquidacion=" . $origenF . ";";
				if ($tablax = mysql_query($consultax)) {
					// ------ ACTUALIZAR EL EXPEDIENTE
					$consultax = "UPDATE expedientes_fraccionamiento SET status = 7, usuario_trans=" . $_SESSION['CEDULA_USUARIO'] . ", fecha_trans=date(now()), fecha_proceso=date(now()) WHERE Rif='" . $registro->rif . "' AND status=1;";
					if ($tablax = mysql_query($consultax)) {
						$consultax = "UPDATE liquidacion SET fraccionada=" . $indice . " WHERE liquidacion.rif='" . $registro->rif . "' AND status=50 AND fraccionada=9999;";
						$tablax = mysql_query($consultax);
						$consultax = "UPDATE liquidacion SET fraccionada=" . $indice . " WHERE liquidacion.rif='" . $registro->rif . "' AND status=10 AND fraccionada=0 AND origen_liquidacion = " . $origenF . ";";
						if ($tablax = mysql_query($consultax)) {
							echo "<script type=\"text/javascript\">alert('�Fraccionamiento bajo el Rif " . $registro->rif . " Transferido Exitosamente!');</script>";
						}
					}
				}
			}
		}
	}

	?>
	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
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

	<form name="form1" method="post">
		<div align="center">


			<table class="formateada" width="49%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="10" align="center">
							<p class="Estilo7"><u>Datos del Contribuyente</u></p>
						</td>
					</tr>
					<tr>
						<th width="6%" height=27>
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
							</td>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Rif</strong>:</div>
							</td>
						<th width="57%">
							<div align="center" class="Estilo8"><strong>Contribuyente</strong>:</div>
							</td>
						<th width="7%" height=27>
							<div align="center" class="Estilo8"><strong>Sel.</strong></div>
							</td>
					</tr>

					<?php

					if ($_SESSION['ADMINISTRADOR'] > 0) {
						$consulta = "SELECT liquidacion.rif, vista_contribuyentes_direccion.contribuyente as NombreRazon FROM liquidacion, vista_contribuyentes_direccion WHERE liquidacion.rif=vista_contribuyentes_direccion.rif AND liquidacion.status=0 AND (liquidacion.origen_liquidacion=8 or liquidacion.origen_liquidacion=17) GROUP BY liquidacion.rif, vista_contribuyentes_direccion.contribuyente;";
					} else {
						$consulta = "SELECT liquidacion.rif, vista_contribuyentes_direccion.contribuyente as NombreRazon FROM liquidacion, vista_contribuyentes_direccion WHERE liquidacion.rif=vista_contribuyentes_direccion.rif AND liquidacion.status=0 AND liquidacion.origen_liquidacion=" . $origenF . " GROUP BY liquidacion.rif, vista_contribuyentes_direccion.contribuyente;";
					}

					$tablax = mysql_query($consulta);

					$i = 0;

					while ($registrox = mysql_fetch_object($tablax)) {
						$MOSTRAR_BOTON = 'SI';
						$i++;
					?>
						<tr id="fila<?php echo $i; ?>">
							<td bgcolor="#FFFFFF">
								<div align="center"><?php echo $i ?></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center"><a href="planillas_x_contribuyente.php?rif=<?php echo $registrox->rif ?>&status=1" target="_blank"><?php echo $registrox->rif ?></a></div>
							</td>
							<td bgcolor="#FFFFFF">
								<div align="center"><?php echo $registrox->NombreRazon ?></div>
							</td>
							<td width="94" bgcolor="#FFFFFF">
								<div align="center"><input name="<?php echo $registrox->rif ?>" type="checkbox" value="<?php echo $registrox->rif ?>" onClick="marcar(this,<?php echo $i; ?>)">
							</td>
						</tr>
					<?php
					}

					?>
				</tbody>
			</table>
			<p>
				<?php
				if ($MOSTRAR_BOTON == 'SI') {	?><input type="submit" class="boton" name="CMDTRANSFERIR" value="Transferir"><?php	}
																							?>
			</p>
		</div>
	</form>
	<?php
	$_SESSION['INICIO'] = date('Y-m-d');
	$_SESSION['FIN'] = date('Y-m-d');

	$consulta = "SELECT rif FROM liquidacion WHERE fecha_transferencia_a_liq=date(now()) AND status=10 AND origen_liquidacion=" . $origenF . "";
	$tablax = mysql_query($consulta);

	if ($registrox = mysql_fetch_object($tablax)) { ?>
		<form name="form55" method="post" action="Reportes/transferidas_a_liquidacion.php" target="_blank">
			<div align="center">
				<p>
					<input type="submit" class="boton" name="Submit3" value="Ver Transferidas del Dia">
				</p>
			</div>
		</form>
	<?php }
	?>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>