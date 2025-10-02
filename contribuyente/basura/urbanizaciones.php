<?php

session_start();

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

?>

<html>

<head>
	<title>Incluir Urbanizacion o Barrio</title>

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
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>

	<?php

	if ($_POST['CMDGUARDAR'] == "Guardar") {
		if ($_POST[OCIUDAD] > 0 and $_POST[OBARRIO] <> "") {
			include "../conexion.php";
			include "../auxiliar.php";

			session_start();

			// PARA BUSCAR EL SECTOR
			$consulta = "SELECT sector FROM Ciudades WHERE Codigo='" . $_POST[OCIUDAD] . "';";
			$tabla = mysql_query($consulta);
			$registro = mysql_fetch_object($tabla);
			$SECTOR = $registro->sector;
			// PARA BUSCAR EL CORRELATIVO DE URB. O BARRIOS
			$consulta = "SELECT Format((Max(Val(Numero))+1),'##') AS Num FROM [Urbanizaciones o Barrios];";
			$tabla = mysql_query($consulta);
			$registro = mysql_fetch_object($tabla);
			$NUMERO = $registro->Num;
			//PARA GUARDAR LA URBANIZACION O BARRIO
			$consulta = "INSERT INTO [Urbanizaciones o Barrios] ( Numero, Nombre, Ciudad, fecha_proceso, sector ) SELECT '" . $NUMERO . "' AS Expr1, '" . strtoupper($_POST[OBARRIO]) . "' AS Expr2, '" . $_POST[OCIUDAD] . "' AS Expr3, Date() AS Expr4, " . $SECTOR . " AS Expr5;";
			$tabla = mysql_query($consulta);
			// MENSAJE DE GUARDADO
			header("Location: menuprincipal.php?errorusuario=si");
			exit();
		} else {
			// MENSAJE DE CAMPOS VACIOS
			echo '<table width="75%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>EXISTEN CAMPOS VACIOS!!!</strong> </div></td> </tr>  </table>';
		}
	}

	?>
	<form name="form1" method="post" action="">
		</table>
		<table width="63%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Urbanizaci&oacute;n o Barrio </u></span></td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="#CCCCCC"><strong>Ciudad:</strong></td>
				<td colspan="4"><label>
						<select name="OCIUDAD" size="1">
							<option value="-1">Seleccione</option>
							<?php
							include "../conexion.php";
							include "../auxiliar.php";

							session_start();
							$consulta = "SELECT Codigo, Nombre FROM Ciudades ORDER BY Nombre;";
							$tabla = mysql_query($consulta);
							while ($registro = mysql_fetch_object($tabla)) {
								echo '<option';
								if ($_POST[OCIUDAD] == $registro->Codigo) {
									echo ' selected="selected" ';
								}
								//
								echo ' value="';
								echo $registro->Codigo;
								echo '">';
								echo $registro->Nombre;
								echo '</option>';
							}
							?>
						</select>
					</label></td>
			</tr>
			<tr>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Codigo:</strong></td>
				<td width="14%"><label>
						<input type="text" name="OCODIGO" size="10" maxlength="8" readonly="true" value="<?php
																											include "../conexion.php";
																											include "../auxiliar.php";

																											session_start();
																											$consulta = "SELECT Format((Max(Val(Numero))+1),'##') AS Num FROM [Urbanizaciones o Barrios];";
																											$tabla = mysql_query($consulta);
																											$registro = mysql_fetch_object($tabla);
																											echo $registro->Num; ?>"></label></td>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Urb. o Barrio:</strong></td>
				<td colspan="3"><label>
						<input type="text" name="OBARRIO" size="40" value="<?php echo $_POST[OBARRIO]; ?>"></label></td>
			</tr>

		</table>
		<p>
		<div align="center"> <input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></div>
		</p>
	</form>
	<p>
		<?php include "../pie.php";; ?>

	</p>
	<p>&nbsp;</p>
</body>