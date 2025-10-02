<?php

session_start();

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

?>

<html>

<head>
	<title>Incluir Tipo de Calle</title>

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
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

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

	<?php

	if ($_POST['CMDGUARDAR'] == "Guardar") {
		if ($_POST['OCALLE'] <> "") {

			//VERIFICAR SI EL NOMBRE DE LA CALLE YA ESTA REGISTRADO
			$sqlnombre = mysql_query("SELECT descripcion FROM dir_calles WHERE descripcion='" . $_POST['OCALLE'] . "'");
			if (mysql_num_rows($sqlnombre) < 1) {
				//PARA GUARDAR LA CALLE
				$consulta = mysql_query("INSERT INTO dir_calles (descripcion) VALUES ('" . $_POST['OCALLE'] . "')");

				// MENSAJE DE GUARDADO
				header("Location: menuprincipal.php?errorusuario=si");
				exit();
			} else {
				echo "<script type=\"text/javascript\">alert('La calle ya se encuentra registrada!');</script>";
			}
		} else {
			// MENSAJE DE CAMPOS VACIOS
			echo '<table width="75%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>EXISTEN CAMPOS VACIOS!!!</strong> </div></td> </tr>  </table>';
		}
	}

	?>
	<form name="form1" method="post" action="">
		</table>
		<table width="45%" border="1" align="center">

			<tr>
				<td colspan="1" width="30%" bgcolor="#CCCCCC"><strong>Descripcion de la Calle:</strong></td>
				<td width="70%" colspan="3"><label>
						<input type="text" name="OCALLE" size="70" value="<?php echo $_POST[OCALLE]; ?>"></label></td>
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