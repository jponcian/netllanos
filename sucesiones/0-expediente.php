<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

?>
<html>

<head>
	<title>Expediente</title>

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

		.Estilo15 {
			font-size: 14px;
		}

		.Estilo16 {
			color: #000000;
			font-weight: bold;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body style="background: transparent !important;">

	<?php

	$consulta = "SELECT Contribuyentes_Direccion.NombreRazon, Contribuyentes_Direccion.Direccion, Net_RE_exp_suc.Coordinador, Empleados.Nombres as NombresS, Empleados.Apellidos as ApellidosS, Net_RE_exp_suc.Funcionario, Empleados_1.Nombres, Empleados_1.Apellidos, Net_RE_exp_suc.Rif, Net_RE_exp_suc.Status, Net_RE_exp_suc.Usuario FROM Empleados AS Empleados_1 INNER JOIN (Empleados INNER JOIN (Contribuyentes_Direccion INNER JOIN Net_RE_exp_suc ON Contribuyentes_Direccion.Rif = Net_RE_exp_suc.Rif) ON Empleados.Cedula = Net_RE_exp_suc.Coordinador) ON Empleados_1.Cedula = Net_RE_exp_suc.Funcionario WHERE (((Net_RE_exp_suc.[A�o])=" . $_GET['anno'] . ") AND ((Net_RE_exp_suc.[Numero])=" . $_GET['num'] . "));";
	$tabla = odbc_exec($_SESSION['conexion'], $consulta);
	$registro_datos = mysql_fetch_object($tabla);

	?>
	<p>
		<?php include "../titulo.php"; ?>

	</p>


	<form name="form1" method="post" action="">
		<div align="center">
			<p>&nbsp;</p>
			<table width="70%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
				</tr>

			</table>

			<table width="70%" border="1" align="center">
				<tr>
					<td width="6%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td width="16%"><label><span class="Estilo15"><?php echo $registro_datos->Rif; ?></span></label></td>
					<td width="19%" bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
					<td width="59%"><label><span class="Estilo15"><?php echo $registro_datos->NombreRazon; ?></span></label></td>
				</tr>
				<tr>
					<td width="19%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
					<td colspan="3" width="59%"><label><span class="Estilo15"><?php echo $registro_datos->Direccion;	?>
							</span></label></td>
				</tr>
			</table>
			<table width="70%" border="1" align="center">
				<tr>
					<td width="10%" bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td width="13%"><label><span class="Estilo15"><?php echo $registro_datos->Coordinador; ?></span></label></td>
					<td width="9%" bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
					<td width="68%"><label><span class="Estilo15"><?php echo $registro_datos->NombresS . " " . $registro_datos->ApellidosS; ?></span></label></td>
				</tr>
				<tr>
					<td width="10%" bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
					<td width="13%"><label><span class="Estilo15"><?php echo $registro_datos->Funcionario; ?></span></label></td>
					<td width="9%" bgcolor="#CCCCCC"><strong>Funcionario:</strong></td>
					<td width="68%"><label><span class="Estilo15"><?php echo $registro_datos->Nombres . " " . $registro_datos->Apellidos; ?></span></label></td>
				</tr>
			</table>

			<table width="75%" border=1 align=center>

				<tr>
					<td bgcolor="#FF0000" height="27" colspan="13" align="center">
						<p class="Estilo7"><u>Sanciones actuales aplicadas al Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" height=27>
						<div align="center" class="Estilo8">Num</div>
					</td>
					<td bgcolor="#CCCCCC" height=27>
						<div align="center" class="Estilo8">Sancion</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Descripci&oacute;n</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Periodo Inicial</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Periodo Final</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Fecha Pago</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Fecha Vencimiento</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">UT</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8">Monto</div>
					</td>
				</tr>

				<?php

				//----------------------- MONTAJE DE LOS DATOS
				$consulta = "SELECT Format(Liquidacion.FechaPago,'dd/mm/yyyy') AS FechaPago, Format(Liquidacion.FechaExigibilidad,'dd/mm/yyyy') AS FechaVencimiento, Liquidacion.Sancion AS codigo, Format(Liquidacion.FechaInicioDeclaracion,'dd/mm/yyyy') AS FechaInicioDeclaracion, Format(Liquidacion.FechaInicioDeclaracion,'mm/dd/yyyy') AS FechaInicioDeclaracion1, Format(Liquidacion.FechaFinDeclaracion,'dd/mm/yyyy') AS FechaFinDeclaracion, Format(Liquidacion.FechaFinDeclaracion,'mm/dd/yyyy') AS FechaFinDeclaracion1, Format(Liquidacion.UTCifras,'#,0.00') AS UTCifras, Format(Liquidacion.MontoCifras,'#,0.00') AS MontoCifras, Liquidacion.Concurrente, Format(Liquidacion.UTdividida,'#,0.00') AS UTdividida, Format(Liquidacion.Montodividida,'#,0.00') AS Montodividida, Sancion.Sancion FROM Liquidacion INNER JOIN Sancion ON Liquidacion.Sancion = Sancion.Codigo WHERE (((Liquidacion.A�oProvidencia)=" . $_GET['anno'] . ") AND ((Liquidacion.Autorizacion)=" . $_GET['num'] . ") AND ((Liquidacion.Situacion_Liquidacion)=92) AND ((Liquidacion.Origen_Liquidacion)=3)) ORDER BY Liquidacion.Sancion, Format(Liquidacion.FechaInicioDeclaracion,'dd/mm/yyyy'), Format(Liquidacion.FechaFinDeclaracion,'dd/mm/yyyy');";
				$tabla = mysql_query($consulta);

				$i = 0;

				while ($registro = mysql_fetch_object($tabla)) {
					$DATOS = 'SI';
					$i++;
					echo '<tr> <td bgcolor="#FFFFFF" height=27><div align="center" class="Estilo8">';
					echo $i;
					echo '</div></td><td ><div align="center">';
					echo $registro->codigo;
					echo '</div></td><td bgcolor="#FFFFFF" ><div align="left" class="Estilo8 Estilo1">';
					echo $registro->Sancion;
					echo '</div></td><td ><div align="center">';
					echo $registro->FechaInicioDeclaracion;
					echo '</div></td><td ><div align="center">';
					echo $registro->FechaFinDeclaracion;
					echo '</div></td><td ><div align="center">';
					echo $registro->FechaPago;
					echo '</div></td><td><div align="center">';
					echo $registro->FechaVencimiento;
					echo '</div></td><td><div align="center">';
					echo $registro->UTCifras;
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo $registro->MontoCifras;
					echo '</div></td></tr>';
				}

				//----------
				include "../desconexion.php";
				//----------

				?>
			</table>

			</p>

		</div>
	</form>
	<p>
	<p>
		<?php include "../pie.php"; ?>
	<p>&nbsp;</p>
</body>

</html>