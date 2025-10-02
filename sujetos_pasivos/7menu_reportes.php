<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 151;
//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";
?>

<html>

<head>
	<title>Men&uacute; de Reportes</title>
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

		.Estilo1 {
			font-size: 18px;
			font-weight: bold;
			color: #FF0000;
		}

		.Estilo2 {
			color: #FFFFFF
		}

		.Estilo5 {
			font-size: 22px
		}

		.Estilo6 {
			color: #FFFFFF;
			font-size: 22px;
		}

		.Estilo15 {
			color: #FFFFFF;
			font-size: 17px;
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

	<form name="form1" method="post">

		<table width="30%" border="1" align="center">
			<tr>
				<td height="33" colspan="2" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo15">Dependencia</span></p>
				</td>
				<td colspan="4" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo15">Fecha
							<?php
							if ($_POST['OOPCION'] == "1") {
								echo 'de Recepci�n en Tramitaciones';
							}
							if ($_POST['OOPCION'] == "2") {
								echo 'de Asignaci�n';
							}
							if ($_POST['OOPCION'] == "3") {
								echo 'de Notificaci�n';
							}
							if ($_POST['OOPCION'] == "4") {
								echo 'de Recepci�n en Tramitaciones';
							}
							if ($_POST['OOPCION'] == "5") {
								echo 'de Transferencia a Cobro';
							} ?></span></p>
				</td>
			</tr>
			<tr>
				<td width="70%"><span class="Estilo1">
						<select name="OSEDE" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							if ($_SESSION['SEDE_USUARIO'] == 1) {
								$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5;';
							} else {
								$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
							}
							//-------------
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OSEDE'] == $registro_x['id_sector']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
							}
							?>
						</select>
					</span></td>
				<td width="30%"><input type="submit" class="boton" name="CMDBOTON" value="Cargar"></td>
				<td width="17%" bgcolor="#CCCCCC"><strong>
						Desde:</strong></td>
				<td><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="10" readonly value="<?php echo $_POST['OINICIO']; ?>" />
						</div>
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>
						Hasta:</strong></td>
				<td><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFIN" size="10" readonly value="<?php echo $_POST['OFIN']; ?>" />
						</div>
					</label></td>


			</tr>
		</table>
		<p>&nbsp;</p>
	</form>

	<p>
		<?php

		if ($_POST['OSEDE'] <> "" and $_POST['OINICIO'] <> "" and $_POST['OFIN'] <> "") {
			$_SESSION['OSEDE'] = $_POST['OSEDE'];
			$_SESSION['VARIABLE'] = $_POST['OOPCION'];
			$_SESSION['FECHA1'] = $_POST['OINICIO'];
			$_SESSION['FECHA2'] = $_POST['OFIN'];
		?>
	<table width=300 align=center border=0>
		<td colSpan=1>
			<div align="center">
				<form name="form3" method="post" action="reportes/auxilar_contable_liq_not.php" target="_blank">
					<input type="submit" class="boton" name="CMDREPORTE" value="Ver Reporte (Exportar a Excel)">
				</form>
			</div>
		</td>
		</tr>
	</table>
<?php
		}
?>
<p>&nbsp;
</p>
<?php include "../pie.php"; ?>
</p>
<p>&nbsp;</p>
</body>

</html>