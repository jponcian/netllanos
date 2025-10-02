<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 33;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>
<?php

if ($_POST['CMDGUARDAR'] == "Guardar") {
	if ($_POST['OZONA'] <> "") {

		//VERIFICAR SI EL NOMBRE DE LA CIUDAD YA ESTA REGISTRADO
		$sqlnombre = mysql_query("SELECT descripcion FROM dir_zonas WHERE descripcion='" . $_POST['OZONA'] . "'");
		if (mysql_num_rows($sqlnombre) < 1) {
			//PARA GUARDAR LA CALLE
			$consulta = mysql_query("INSERT INTO dir_zonas (descripcion) VALUES ('" . $_POST['OZONA'] . "')");

			// MENSAJE DE GUARDADO
			header("Location: menuprincipal.php?errorusuario=si");
			exit();
		} else {
			echo "<script type=\"text/javascript\">alert('La zona ya se encuentra registrada!');</script>";
		}
	} else {
		// MENSAJE DE CAMPOS VACIOS
		echo '<table width="75%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>EXISTEN CAMPOS VACIOS!!!</strong> </div></td> </tr>  </table>';
	}
}

?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Incluir Zonas</title>

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
		-->
	</style>

</head>

<body style="background: transparent !important;">
	<p>

		<?php include "../titulo.php"; ?>
	</p>
	<table width=669 align=center border=0>
		<tbody>
			<tr>
				<td colSpan=5>
					<form name="form2" method="post" action="menuprincipal.php">
						<div align="center"> <?php include "menu.php"; ?>
						</div>
					</form>
				</td>
			</tr>
		</tbody>
	</table>


	<form name="form1" method="post" action="">
		</table>
		<table width="45%" border="1" align="center">

			<tr>
				<td colspan="1" width="30%" bgcolor="#CCCCCC"><strong>Descripcion de la Zona:</strong></td>
				<td width="70%" colspan="3"><label>
						<input type="text" name="OZONA" size="70" value="<?php echo $_POST[OZONA]; ?>"></label></td>
			</tr>
		</table>
		<p>
		<div align="center"> <input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></div>
		</p>
	</form>
	<p>
		<?php
		include "../pie.php";
		include "../desconexion.php";
		?>

	</p>
	<p>&nbsp;</p>
</body>