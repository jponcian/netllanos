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
$id = $_GET['id'];
$status = $_GET['status'];

if ($rif <> "") {
	list($Contribuyente, $Direccion) = funcion_contribuyente($rif);
	//-------------------------	
	if ($Contribuyente == '') {
		echo "<script type=\"text/javascript\">alert('No Existe Contribuyente Registrado con ese Rif');</script>";
	}
}

?>

<html>

<head>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Planillas</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>&nbsp;</p>
	<form name="form1" method="post" action="">
		<table width="65%" border=1 align=center>
			<tbody>
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
						<div align="center">
							<?php echo $rif;		?>
						</div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="left">
							<?php echo $Contribuyente;		?></div>
					</td>
				</tr>
				<tr>
					<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Domicilio</strong></div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $Direccion;		?></div>
					</td>
				</tr>
			</tbody>
		</table>
		<table align="center" width="65%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="9" align="center">
					<p class="Estilo7"><u>Planillas Ajustadas</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Planilla Original</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T. </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T. Original</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Original </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T. Aplicada</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Actual </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Diferencia</strong></div>
				</td>
			</tr>
			<?php
			$consultax = "SELECT monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, id_liq_primitiva FROM liquidacion WHERE rif='$rif' and  liquidacion.status=" . $status . " AND (liquidacion.origen_liquidacion=7 or liquidacion.origen_liquidacion=16);";
			$tablax = mysql_query($consultax);

			$i = 0;

			while ($registrox = mysql_fetch_object($tablax)) {
				// DATOS DE LA PLANILLA ORIGINAL
				$consultaxx = "SELECT liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion FROM liquidacion WHERE id_liquidacion=" . $registrox->id_liq_primitiva . ";";
				$tablaxx = mysql_query($consultaxx);
				$registroxx = mysql_fetch_object($tablaxx);
				// -----------------------------
				$i++;
				echo '<tr>
		  <td bgcolor="#FFFFFF"><div align="center">' . $i . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . $registroxx->liquidacion . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registroxx->cant_ut) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . redondea($registroxx->monto / $registroxx->cant_ut) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="right">' . formato_moneda($registroxx->monto) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . redondea(($registroxx->monto + $registrox->monto) / $registroxx->cant_ut) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="right">' . formato_moneda($registroxx->monto + $registrox->monto) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="right">' . formato_moneda($registrox->monto) . '</div></td>
		  ';
				echo '</tr>';
			}

			?>
		</table>
		<p>&nbsp;</p>
		<p><br>
		</p>
	</form>

</body>

</html>