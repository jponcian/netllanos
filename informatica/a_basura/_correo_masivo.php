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
	<title>Gesti&oacute;n Accesos</title>
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

		.Estilo19 {
			font-size: 18px
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<?php
	if ($_POST['CMDENVIAR'] == 'Enviar') {
		$Correo1 = "netlosllanos@seniat.gob.ve";
		$Correo2 = "jponcian@seniat.gob.ve";
		$Correo3 = "";
		$asunto = 'Caso N� 01';
		$cuerpo = "Prueba de Correo";
		//------------
		mail($Correo1 . ', ' . $Correo2 . ', ' . $Correo3, $asunto, $cuerpo);
	}
	?> <p>
		<?php include "../titulo.php"; ?>

	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>

	<form name="form1" method="post" action="" enctype="multipart/form-data">
		<table width="41%" border="1" align="center">
			<tr>
				<td width="100%" height="27" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Prueba de Env&iacute;o de Correo</u></span></td>
			</tr>
		</table>

		<p>
		<div align="center"> <input type="submit" class="boton" name="CMDENVIAR" value="Enviar"></div>
		</p>
	</form>
	<p>
		<?php include "../pie.php"; ?>


</body>