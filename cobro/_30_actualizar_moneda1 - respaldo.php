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
	<title>Actualizar Moneda</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>
</head>
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
		font-size: 12px
	}
	-->
</style>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
		<?php
		//-----------
		if ($_POST['Guardar'] == 'Guardar') {
			$consultabuscar = "select FechaAplicacion from a_moneda_cambio order by FechaAplicacion DESC limit 1";
			$tabla_00 = mysql_query($consultabuscar);
			$registro_x = mysql_fetch_array($tabla_00);
			$fecha = $registro_x['FechaAplicacion'];

			if (fecha_a_numero(($_POST[ofecha])) <= fecha_a_numero($fecha)) {
		?>
				<script language="JavaScript" type="text/javascript">
					alert('La Fecha que desea cargar es anterior a la fecha actual!');
				</script>
			<?php
			} elseif (fecha_a_numero(($_POST[ofecha])) > fecha_a_numero(date('Y-m-d'))) {
			?>
				<script language="JavaScript" type="text/javascript">
					alert('La Fecha que desea cargar no puede ser mayor a la actual!');
				</script>
			<?php
			} else {
				//--------- BUSCAMOS LOS DATOS DE LA PROVIDENCIA
				$consulta_00 = "DELETE FROM a_moneda_cambio WHERE (FechaAplicacion='" . voltea_fecha($_POST[ofecha]) . "');";
				$tabla_00 = mysql_query($consulta_00);
				if (($_POST[omonto]) > 0) {
					$consulta_00 = "INSERT INTO a_moneda_cambio (FechaAplicacion, moneda, descripcion, valor) VALUES ('" . ($_POST[ofecha]) . "', 'EUR', 'Euro', '" . ($_POST[omonto]) . "');";
					$tabla_00 = mysql_query($consulta_00);
				}
			?>
				<script language="JavaScript" type="text/javascript">
					alert('Valor Actualizado Exitosamente!');
				</script>
		<?php
			}
		}
		?>
	</p>
	<div align="center">
		<p align="center">

	</div>
	<form name="form1" method="post" action="#vista">
		<table width="60" border="1" align="center">
			<tr>
				<td height="52" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7">Moneda de Mayor Valor Publicada por el Banco Central de Venezuela</span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC"><strong>Historial Euro</strong></td>
				<td width="20%"><label>
						<div align="center"><a target="_blank" rel="noopener noreferrer" href="http://www.bcv.org.ve/politica-cambiaria/intervencion-cambiaria" class="button">::BCV::</a></span></div>
				<td width="10%" bgcolor="#CCCCCC"><strong>Monto (Bs):</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><input name="omonto" type="text" size="10"></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>Fecha Reg:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><input name="ofecha" type="date" value="2022-06-13" size="8" "></span></div>
  </label></td>
  <td width=" 10%" bgcolor="#CCCCCC"><strong><input name="Guardar" type="submit" class="button" value="Registrar"></strong></td>

			</tr>
		</table>
	</form>

	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
	<a href="../index.php" class="boton">INICIO</a>
</body>

</html>