<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

if ($_POST['CMDBUSCAR'] == "Buscar" and $_POST['OSEDE'] > 0 and $_POST['OANNO'] > 0 and $_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['OSEDE'] = $_POST['OSEDE'];

	header("Location: incluirsancion.php");
	exit();
}

?>

<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Seleccionar Expediente a Sancionar</title>
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

	.Estilo14 {
		font-size: 16px;
		font-weight: bold;
	}

	.Estilo15 {
		font-size: 14px;
	}
	-->
</style>

<body style="background: transparent !important;">

	<p>
		<?php include "../titulo.php"; ?>

	</p>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post">
		<p>&nbsp;</p>
		<table width="35%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="5"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
			</tr>
			<tr>
				<td width="17%" bgcolor="#CCCCCC"><strong>A�o:</strong></td>
				<td width="10%"><label><select name="OANNO" size="1" onChange="this.form.submit()">
							<option value="-1"> ->-> A�O <-<- </option>
									<?php
									$consulta_x = 'SELECT anno FROM vista_exp_especiales WHERE status<9 GROUP BY anno ORDER BY anno DESC;';
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
						</select></label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
				<td width="9%"><label>
						<select name="ONUMERO" size="1">
							<option value="-1"> ->-> NUMERO <-<- </option>
									<?php
									$consulta_x = "SELECT numero FROM vista_exp_especiales WHERE status<9 AND anno=" . $_POST['OANNO'] . " ORDER BY numero DESC;";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';
										if ($_POST[NUMERO] == $registro_x->numero) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->numero;
										echo '">';
										echo $registro_x->numero;
										echo '</option>';
									}
									?>
						</select>
					</label></td>
				<td width="48%"><label>
						<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
					</label></td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<p>
						<?php include "../msg_validacion.php"; ?></p>
				</td>
			</tr>
		</table>

		<p>&nbsp;</p>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>


</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>