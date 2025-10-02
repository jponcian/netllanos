<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 94;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
?>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<title>Imprimir Resoluciones</title>

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


			<table class="formateada" width="50%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="35" colspan="6" align="center">
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
						<th width="7%" colspan="3" height=27>
							<div align="center" class="Estilo8"><strong>Fraccionamiento</strong></div>
							</td>
					</tr>

					<?php
					if ($_SESSION['ADMINISTRADOR'] > 0) {
						$consulta = "SELECT expedientes_fraccionamiento.rif, vista_contribuyentes_direccion.contribuyente FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_fraccionamiento.rif AND expedientes_fraccionamiento.status=1;";
					} else {
						$consulta = "SELECT expedientes_fraccionamiento.rif, vista_contribuyentes_direccion.contribuyente FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = expedientes_fraccionamiento.rif AND expedientes_fraccionamiento.status=1 AND expedientes_fraccionamiento.origen_exp=" . $origenF . ";";
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
								<div align="center"><a href="planillas_x_contribuyente.php?rif=<?php echo $registrox->rif; ?>&status=1" target="_blank"><?php echo $registrox->rif; ?></a></div>
							</td>
							<td>
								<div align="center"><?php echo $registrox->contribuyente ?></div>
							</td>
							<td>
								<div align="center"><a href="formatos/aprobacion_fraccionamiento.php?rif=<?php echo $registrox->rif ?>" target="_blank">Aprobacion</a></div>
							</td>
							<td>
								<div align="center"><a href="formatos/contrato_fraccionamiento.php?rif=<?php echo $registrox->rif ?>" target="_blank">Contrato</a></div>
							</td>
							<td>
								<div align="center"><a href="formatos/anexos_fraccionamiento.php?rif=<?php echo $registrox->rif ?>" target="_blank">Anexos</a></div>
							</td>
						</tr>
					<?php
					}

					?>

				</tbody>
			</table>
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