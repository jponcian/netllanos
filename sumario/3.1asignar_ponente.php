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
		if ($_POST['OFUNCIONARIO'] > 0) {
			//----------------------
			$consultax = "UPDATE expedientes_sumario SET status=1, cedula_ponente = '" . $_POST['OFUNCIONARIO'] . "', fecha_asignacion_ponente = date(now()), usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE rif='" . $_GET['rif'] . "' and anno_expediente_fisc=" . $_GET['anno'] . " and num_expediente_fisc=" . $_GET['num'] . " and sector=" . $_GET['sector'] . " and status=0 and origen_liquidacion=" . $_GET['origen'];
			$tablax = mysql_query($consultax);
			echo "<script type=\"text/javascript\">alert('Se ha Actualizado el Expediente, pronto se cerrar� la Pagina!');</script>";
			// PARA CERRAR LA PAGINA
			echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
			exit();
		} else {
			echo "<script type=\"text/javascript\">alert('No ha Seleccionado el Ponente!');</script>";
		}
	}

	?>

	<title>Expediente x Notificar</title>
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

		<?php
		//VERIFICAMOS SI TIENE ESCRITO DE DESCARGO
		$sql_escrito = "SELECT num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha FROM expedientes_sumario WHERE origen_liquidacion = " . $_GET['origen'] . " AND sector = " . $_GET['sector'] . " AND anno_expediente_fisc = " . $_GET['anno'] . " AND num_expediente_fisc = " . $_GET['num'] . " AND num_escrito_descargo<>''";
		$result = mysql_query($sql_escrito);
		$existe = mysql_num_rows($result);
		if ($existe > 0) {
			$valor = mysql_fetch_object($result);
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
					<td align="center"><?php echo $valor->num_escrito_descargo ?></td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Fecha de Recepci�n:</strong></div>
					</td>
					<td align="center"><?php echo $valor->fecha ?></td>
				</tr>
			</table>
			<p></p>
		<?php
		} ?>

		<table border=0 align=center>
			<tr>
				<td colspan="6">
					<div align="center">
						<p>&nbsp;</p>
						<p><strong>Seleccione aqu&iacute; el PONENTE a Asignar</strong></p>
						<p>
							<select name="OFUNCIONARIO" size="1">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT z_empleados.cedula, z_empleados.Nombres, z_empleados.Apellidos FROM z_empleados INNER JOIN z_empleados_roles ON z_empleados_roles.cedula = z_empleados.cedula WHERE z_empleados_roles.rol = 18 AND z_empleados.sector = " . $_SESSION['SEDE_USUARIO'] . ";";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OFUNCIONARIO'] == $registro_x->cedula) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->cedula;
									echo '">';
									echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
									echo '</option>';
								}
								?>
							</select>
						</p>
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