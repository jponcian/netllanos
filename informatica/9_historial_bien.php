<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 155;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	

?>
<html>

<head>
	<title>Consultar Bien Nacional</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=Edge" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
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
	<form name="form1" method="post" action="#vista">
		<table width="40%" border="1" align="center">
			<tr>
				<td height="40" align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Bien Nacional</u></span></td>
			</tr>
		</table>

		<table width="40%" border="1" align="center">
			<tr>
				<td bgcolor="#CCCCCC"><strong>N&deg; Bien:</strong></td>
				<td bgcolor="#CCCCCC"><strong>Descripcion:</strong></td>

			</tr>
			<tr>
				<td><label><span class="Estilo7">
							<input type="text" name="OBIEN" size="10" maxlength="6" value="<?php echo $_POST['OBIEN']; ?>">
						</span></label></td>
				<td><label><span class="Estilo7">
							<input type="text" name="ODESCRIPCION" size="70" value="<?php echo $_POST['ODESCRIPCION']; ?>"></span></label></td>
			</tr>
		</table>
		<p align="center">
			<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar" />
		</p>
		<table width="60%" class="formateada" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="7" align="center">
						<p class="Estilo7"><u>Resultados de la Busqueda </u></p>
					</td>
				</tr>
				<tr>
					<th>
						<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Categoria</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Numero Bien</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Area</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Divisi&oacute;n</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Dependencia</strong></div>
					</th>
				</tr>
				<?php

				if ($_POST['OBIEN'] > 0) {
					$consulta = "SELECT * FROM vista_bienes_nacionales WHERE borrado=0 AND numero_bien = " . $_POST['OBIEN'] . " ORDER BY numero_bien, descripcion_bien";
				} else {
					if ($_POST['ODESCRIPCION'] <> '') {
						$consulta = "SELECT * FROM vista_bienes_nacionales WHERE borrado=0 AND descripcion_bien LIKE '%" . $_POST['ODESCRIPCION'] . "%' ORDER BY numero_bien, descripcion_bien";
					}
				}

				//echo $consulta;
				//----------------------- MONTAJE DE LOS DATOS
				$i = 0;
				$tabla = mysql_query($consulta);
				while ($registro = mysql_fetch_object($tabla)) {
					$i++;
				?>
					<tr id="fila<?php echo $i . $registro->id_bien; ?>">
						<td>
							<div align="center" class="Estilo15"><?php echo ($i); ?></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><?php echo mayuscula($registro->codigo); ?></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><?php echo ($registro->numero_bien); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo15"><?php echo strtoupper($registro->descripcion_bien); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo15"><?php echo palabras($registro->area); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo15"><?php echo palabras($registro->division); ?></div>
						</td>
						<td>
							<div align="left" class="Estilo15"><?php echo palabras($registro->sector); ?></div>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<p>&nbsp;</p>
	</form>
	<p>
		<?php include "../pie.php"; ?>

	</p>
	<p>&nbsp;</p>
</body>