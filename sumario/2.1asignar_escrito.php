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


	if ($_POST['CMDGUARDAR'] == 'Guardar') {
		if ($_POST['ONUMESCRITO'] <> "" and $_POST['OFECHASCRITO'] <> "") {
			//----------------------
			$consultax = "UPDATE expedientes_sumario SET num_escrito_descargo = '" . sprintf("%006s", $_POST['ONUMESCRITO']) . "', fecha_escrito_descargo = '" . voltea_fecha($_POST['OFECHASCRITO']) . "', usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE rif='" . $_GET['rif'] . "' and anno_expediente_fisc=" . $_GET['anno'] . " and num_expediente_fisc=" . $_GET['num'] . " and sector=" . $_GET['sector'] . " and status=0 and origen_liquidacion=" . $_GET['origen'];
			$tablax = mysql_query($consultax);
			echo "<script type=\"text/javascript\">alert('Se ha Actualizado el Expediente, pronto se cerrar� la Pagina!');</script>";
			// PARA CERRAR LA PAGINA
			echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
			exit();
		} else {
			echo "<script type=\"text/javascript\">alert('No ha ingresado numero o fecha del escrito de descargo!');</script>";
		}
	}

	?>

	<title>Asignar Escrito Descargo</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
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
		<p>&nbsp;</p>

		<?php

		$concluir = 'No';
		include "0_detalles_expediete.php";

		?>

		<table width="60%" border=1 align=center>
			<tr>
				<td bgcolor="#FF0000" height="40" colspan="4" align="center">
					<p class="Estilo7"><u>Escrito de Descargo</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">Numero de Recepci�n Correspondencia:</span></a></strong></div>
				</td>
				<td><input name="ONUMESCRITO" type="text" size="10" maxlength="6" value="<?php echo $_POST['ONUMESCRITO'] ?>"></td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Fecha de Recepci�n:</strong></div>
				</td>
				<td><input onclick='javascript:scwShow(this,event);' name="OFECHASCRITO" type="text" size="10" maxlength="10" value="<?php echo $_POST['OFECHASCRITO'] ?>" readonly></td>
			</tr>
		</table>

		<table border=0 align=center>
			<tr>
				<td>
					<p>&nbsp;</p>
					<p>
						<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
					</p>
					<p>&nbsp;</p>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>