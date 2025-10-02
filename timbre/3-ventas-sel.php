<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";


if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 25;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//----------
include "0_respaldo_inv_seriales.php";

if ($_POST['CMDBUSCAR'] == "Continuar" and $_POST['OLICENCIA'] <> "-1") {
	$_SESSION['LICENCIA'] = $_POST['OLICENCIA'];
	//-----------------
	header("Location: 3.1-ventas_reg.php");
	exit();
}
?>
<html>

<head>
	<title>Seleccionar Expendedor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
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
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	<form name="form1" method="post">
		<p>&nbsp;</p>
		<table border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="2"><span class="Estilo7"><u>Datos del Expendedor</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Licencia:</strong></td>
				<td><label><select name="OLICENCIA" size="1">
							<option value="-1"> Seleccione </option>
							<?php
							$consulta_x = "SELECT licencia, contribuyente FROM contribuyentes, timbre_expendedores WHERE contribuyentes.rif = timbre_expendedores.rif;";
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_object($tabla_x)) {
								echo '<option';
								if ($_POST['OLICENCIA'] == $registro_x->licencia) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro_x->licencia;
								echo '">';
								echo $registro_x->licencia . ' - ' . $registro_x->contribuyente;
								echo '</option>';
							}
							?>
						</select></label></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<p>
						<input type="submit" class="boton" name="CMDBUSCAR" value="Continuar">
					</p>
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
//include "../desconexion.php";
//----------
?>