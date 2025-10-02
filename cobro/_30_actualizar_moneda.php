<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//-----------
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
?>
<html>

<head>
	<title>Incluir Moneda</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<!-- <script type='text/JavaScript' src='../funciones/scw_normal.js'></script> -->
	</script>
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
		<?php
		//-----------
		if ($_POST['Guardar'] == 'Guardar') {
			//--------- BUSCAMOS LOS DATOS DE LA PROVIDENCIA
			$consulta_00 = "DELETE FROM a_moneda_cambio WHERE (FechaAplicacion='" . voltea_fecha($_POST[ofecha]) . "');";
			$tabla_00 = mysql_query($consulta_00);
			if (($_POST[omonto]) > 0) {
				$consulta_00 = "INSERT INTO a_moneda_cambio (FechaAplicacion, moneda, descripcion, valor, usuario) VALUES ('" . voltea_fecha($_POST[ofecha]) . "', 'EUR', 'Euro', '" . ($_POST[omonto]) . "', '" . ($_SESSION['CEDULA_USUARIO']) . "');";
				$tabla_00 = mysql_query($consulta_00);
			}
		?>
			<script language="JavaScript" type="text/javascript">
				alert('Valor Actualizado Exitosamente!');
			</script>
		<?php
		}
		?>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	<form name="form1" method="post" action="#vista">
		<table width="60" border="1" align="center">
			<tr>
				<td height="52" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7">Moneda de Mayor Valor Publicada por el Banco Central</span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC"><strong>Monto:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><input name="omonto" type="text" size="10"></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><input name="ofecha" type="text" size="8" value="<?php echo date('d/m/Y'); ?>"></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong><input name="Guardar" type="submit" class="button" value="Guardar"></strong></td>
			</tr>
		</table>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>