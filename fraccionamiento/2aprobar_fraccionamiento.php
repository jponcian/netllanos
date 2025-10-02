<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 93;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
?>
<html>

<head>
	<title>Aprobar Planillas</title>
	<?php

	if ($_POST['CMDAPROBAR'] == 'Aprobar') {
		$consulta = "SELECT expedientes_fraccionamiento.rif, vista_contribuyentes_direccion.contribuyente FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_fraccionamiento.rif AND expedientes_fraccionamiento.status=0;";
		$tabla = mysql_query($consulta);

		while ($registro = mysql_fetch_object($tabla)) {
			if ($_POST[$registro->rif] == $registro->rif) {
				// ----------- BUSCAR LAS INFORMACION DEL FRACCIONAMIENTO
				$consulta = "SELECT * FROM expedientes_fraccionamiento WHERE rif='" . $registro->rif . "' AND status=0;";
				$tablax = mysql_query($consulta);
				$registrox = mysql_fetch_object($tablax);
				// ------------
				$rif = $registro->rif;
				$total = $registrox->monto;
				$cuotas = $registrox->cuotas;
				$tasa = $registrox->tasa;
				$sector = $registrox->sector;

				// --- PRIMERA CUOTA
				$z = 1;
				// ------ CALCULO DE LA MENSUALIDAD
				// tasa de interes mensual
				$tasa_2 =  ($tasa / 100) / 12;
				// -------------
				$mensualidad = 1 / pow((1 + $tasa_2), $cuotas);
				$mensualidad = 1 - $mensualidad;
				$mensualidad = ($total * $tasa_2) / $mensualidad;
				// -------------
				$desde = date('Y/m/d');
				$hasta = date("Y/m/d", strtotime(date('Y/m/d') . " +30 day"));
				// ------------		

				while ($z <= $cuotas) //**********
				{
					// -------------
					$interes = ($total * $tasa_2);
					$amortizacion = $mensualidad - $interes;
					// ------------
					$total = $total - $amortizacion;

					// -----------  AGREGAR LA PLANILLA DE MULTA A LIQUIDACION
					$consulta_x = "INSERT INTO liquidacion 
			(rif, periodoinicio, periodofinal, id_sancion, id_tributo, porcion, monto_ut, monto_bs, status, origen_liquidacion, sector, anno_expediente, num_expediente, usuario) VALUES ('" . $rif . "', '" . $desde . "', '" . $hasta . "', '2501', '51', '" . $z . "', '0', '" . $amortizacion . "', '0', '" . $origenF . "', '" . $sector . "', '9999', '9999', '" . $_SESSION['CEDULA_USUARIO'] . "')";
					//echo $consulta_x;
					$tabla_x = mysql_query($consulta_x);

					// -----------  AGREGAR LA PLANILLA DE INTERES A LIQUIDACION
					$consulta_x = "INSERT INTO liquidacion (rif, periodoinicio, periodofinal, id_sancion, id_tributo, porcion, monto_ut, monto_bs, status, origen_liquidacion, sector, anno_expediente, num_expediente, usuario) VALUES ('" . $rif . "', '" . $desde . "', '" . $hasta . "', '2500', '52', '" . $z . "', '0', '" . $interes . "', '0', '" . $origenF . "', '" . $sector . "', '9999', '9999', '" . $_SESSION['CEDULA_USUARIO'] . "')";
					//echo $consulta_x;
					$tabla_x = mysql_query($consulta_x);

					// -------------
					$desde = date("Y/m/d", strtotime($desde . " +31 day"));
					$hasta = date("Y/m/d", strtotime($desde . " +30 day"));
					// ------------

					$z++;
				}


				// ------ ACTUALIZAR LA PLANILLA NUEVA
				$consultax = "UPDATE expedientes_fraccionamiento SET status = 1, usuario_aprob=" . $_SESSION['CEDULA_USUARIO'] . ", fecha_aprob=date(now()) WHERE Rif='" . $registro->rif . "' AND status=0;";
				if ($tablax = mysql_query($consultax)) {
					echo "<script type=\"text/javascript\">alert('�Fraccionamiento bajo el Rif " . $registro->rif . " Aprobado Exitosamente!');</script>";
				}
			}
		}
	}

	?>

	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
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
						$consulta = "SELECT expedientes_fraccionamiento.rif, vista_contribuyentes_direccion.contribuyente FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_fraccionamiento.rif AND expedientes_fraccionamiento.status=0;";
					} else {
						$consulta = "SELECT expedientes_fraccionamiento.rif, vista_contribuyentes_direccion.contribuyente FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_fraccionamiento.rif AND expedientes_fraccionamiento.status=0 AND expedientes_fraccionamiento.origen_exp=" . $origenF . ";";
					}

					$tablax = mysql_query($consulta);

					$i = 0;

					while ($registrox = mysql_fetch_object($tablax)) {
						$MOSTRAR_BOTON = 'SI';
						$i++;
					?>
						<tr id="fila<?php echo $i; ?>">
							<td>
								<div align="center"><?php echo $i ?></div>
							</td>
							<td>
								<div align="center"><a href="planillas_x_contribuyente.php?rif=<?php echo $registrox->rif ?>&status=0" target="_blank"><?php echo $registrox->rif ?></a></div>
							</td>
							<td>
								<div align="center"><?php echo $registrox->contribuyente ?></div>
							</td>
							<td width="94">
								<div align="center"><input name="<?php echo $registrox->rif ?>" type="checkbox" value="<?php echo $registrox->rif ?>" onClick="marcar(this,<?php echo $i; ?>)"></div>
							</td>
						</tr>
					<?php
					}

					?>

				</tbody>
			</table>
			<p>
				<?php
				if ($MOSTRAR_BOTON == 'SI') {	?><input type="submit" class="boton" name="CMDAPROBAR" value="Aprobar"><?php	}
																					?>
			</p>
		</div>
	</form>
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