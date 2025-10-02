<?php

session_start();

include "../conexion.php";
include "../auxiliar.php";


if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$listado = 0;
// ----- FILTRO DE LA SELECCION
if ($_POST['OESTADO'] == -1) {
	$estado = "<>''";
} else {
	$estado = "='" . $_POST['OESTADO'] . "'";
}
if ($_POST['OCIUDAD'] == -1) {
	$ciudad = "<>''";
} else {
	$ciudad = "='" . $_POST['OCIUDAD'] . "'";
}

// ----- CONSULTA 
$_SESSION['VARIABLE1'] = "SELECT Rif, NombreRazon, Ciiu1, Telefonos, Ciudades.Estado, Contribuyente.Ciudad, Ciudades.Nombre, Especial FROM Ciudades INNER JOIN Contribuyente ON Ciudades.Codigo = Contribuyente.Ciudad
WHERE (((Ciudades.Estado) $estado) AND ((Contribuyente.Ciudad) $ciudad)) ORDER BY Estado, Ciudad, NombreRazon;";
$tabla = mysql_query($_SESSION['VARIABLE1']);
if ($registro = mysql_fetch_object($tabla)) {
	$listado++;
}

?>

<html>

<head>

	<title>Men&uacute; Reportes</title>
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
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>
</head>

<body style="background: transparent !important;">

	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<table width=669 align=center border=0>
		<tr>
			<td colSpan=5>
				<div align="right">
					<form name="form2" method="post" action="menuprincipal.php">
						<input type="submit" class="boton" name="Submit2" value="Volver">
					</form>
				</div>
			</td>
		</tr>
	</table>
	<p align="center">
		<?php include "../msg_validacion.php"; ?></p>
	<form name="form9" method="post" action="menu_reportes.php">
		<table width="42%" border="1" align="center">
			<tr>
				<td colspan="5" align="center" bgcolor="#FF0000">
					<p class="Estilo7"><u>Contribuyentes</u></p>
			</tr>
			<tr>
				<td width="16%" bgcolor="#CCCCCC"><strong>Estado:</strong></td>
				<td width="60%"><select name="OESTADO" size="1" onChange="submit()">
						<option value="-1">->-> Todos <-<-< /option>
								<?php
								include "../conexion.php";
								include "../auxiliar.php";

								session_start();
								$consulta = "SELECT Codigo, Nombre FROM Estados;";
								$tabla = mysql_query($consulta);
								while ($registro = mysql_fetch_object($tabla)) {
									echo '<option';
									if ($_POST[OESTADO] == $registro->Codigo) {
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
					</select></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>Ciudad:</strong></td>
				<td width="60%"><select name="OCIUDAD" size="1" onChange="submit()">
						<option value="-1">->-> Todas <-<-< /option>
								<?php
								include "../conexion.php";
								include "../auxiliar.php";

								session_start();
								$consulta = "SELECT Codigo, Nombre FROM Ciudades WHERE Estado = '" . $_POST[OESTADO] . "' ORDER BY Nombre;";
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
					</select></td>
			</tr>
		</table>
	</form>
	<p>
		<?php

		if ($listado > 0) {
		?>
	<table width=300 align=center border=0>
		<td>
			<div align="center">
				<form name="form3" method="post" action="reportes/reporte_html.php" target="_blank">
					<input type="submit" class="boton" name="Submit" value="Ver Listado">
				</form>
			</div>
		</td>
		</tr>
	</table>
<?php
		}

?>
</p>

<p>&nbsp;</p>
<p>
	<?php include "../pie.php"; ?>
</p>


<p>&nbsp;</p>
</body>

</html>