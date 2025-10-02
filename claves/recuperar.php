<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
?>
<html>

<head>
	<title>Recuperar Contrase&ntilde;a</title>
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
		if (isset($_POST['OCI'])) {
			$_SESSION['CEDULA_USUARIO'] = (get_magic_quotes_gpc()) ? $_POST['OCI'] : addslashes($_POST['OCI']);
		}
		//----------- VALIDAR LA CEDULA
		$consulta_x = "SELECT cedula, correo, clave FROM z_empleados WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . ";";
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_array($tabla_x);
		//***************
		if ($registro_x['cedula'] <> $_SESSION['CEDULA_USUARIO']) {
			header("Location: recuperar.php?errorusuario=sist");
			exit();
		} else {
			$Correo1 = "netlosllanos@seniat.gob.ve";
			$Correo2 = $registro_x['correo'];
			$Correo3 = "";
			$asunto = 'Recuperaci�n de Contrase�a';
			$cuerpo = "La Contrase�a para ingresar al Sistema NetlosLlanos es: " . $registro_x['clave'];
			//------------
			mail($Correo1 . ', ' . $Correo2 . ', ' . $Correo3, $asunto, $cuerpo);
			echo "<script type=\"text/javascript\">alert('La Contrase�a fue enviada a su correo: " . $registro_x['correo'] . "');</script>";
			//header ("Location: recuperar.php?errorusuario=rexi");
			//exit();
		}
	}
	?> <p>
		<?php include "../titulo.php"; ?>

	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<table width=669 align=center border=0>
		<tbody>
			<tr>
				<td colSpan=5>
					<div align="right">
						<form name="form2" method="post" action="../">
							<input type="submit" class="boton" name="Submit2" value="HOME">
						</form>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<form name="form1" method="post" action="" enctype="multipart/form-data">
		<table width="32%" border="1" align="center">
			<tr>
				<td width="100%" height="27" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Indique su C&eacute;dula de Identidad </u></span></td>
			</tr>
			<tr>
				<td align="center"><label><input type="text" style="text-align:center" name="OCI" size="15" value="<?php echo $_POST['OCI']; ?>"></label></td>
			</tr>
			<tr>
				<td colspan="2" class="Estilomenun">
					<div align="center">
						<?php //include "../msg_validacion.php";
						?>
					</div>
				</td>
			</tr>
		</table>

		<p>
		<div align="center"> <input type="submit" class="boton" name="CMDENVIAR" value="Enviar"></div>
		</p>
	</form>
	<p>
		<?php include "../pie.php"; ?>


</body>