<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$_SESSION['VARIABLE'] = $_POST['OMES'];
$_SESSION['VARIABLE2'] = $_POST['OANNO'];
$_SESSION['OSEDE'] = $_POST['OSEDE'];

?>

<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Control Bancario</title>
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

	.Estilo1 {
		color: #FFFFFF;
		font-size: 18px;
		font-weight: bold;
	}
	-->
</style>

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

	<form name="form1" method="post" action="">

		</p>
		<table width="25%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="4"><span class="Estilo7 Estilo1"><u>Control Bancario </u></span></td>
			</tr>
			<tr>
				<td colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Opciones:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="18%" bgcolor="#CCCCCC">
					<div align="center"><strong> Mes:</strong></div>
				</td>
				<td width="30%"><label><a href="javascript:NewCssCal('OINICIO','YYYYMMDD')">
							<select name="OMES" size="1">
								<?php
								$i = 1;
								while ($i <= 12) {
									echo '<option ';
									if ($_POST[OMES] == $i) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $i . '>' . $i . '</option>';
									$i++;
								}
								?>
							</select>
						</a></label></td>
				<td width="18%" bgcolor="#CCCCCC">
					<div align="center"><strong> A�o:</strong></div>
				</td>
				<td width="30%"><label><a href="javascript:NewCssCal('OINICIO','YYYYMMDD')">
							<select name="OANNO" size="1">
								<?php
								$i = date('Y');
								while ($i >= 2009) {
									echo '<option ';
									if ($_POST['OANNO'] == $i) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $i . '>' . $i . '</option>';
									$i--;
								}
								?>
							</select>
						</a></label></td>
			</tr>
			<tr>
				<td colspan="2" height="30" bgcolor="#CCCCCC">
					<div align="center"><strong>Sector:</strong></div>
				</td>
				<td colspan="2"><label><span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								}
								?>
							</select>
						</span></label></td>
			</tr>
		</table>
		<p align="center">
			<input type="submit" class="boton" name="CMDCARGAR" value="Buscar">
		</p>
	</form>
	<?php
	if ($_POST['CMDCARGAR'] == 'Buscar') {
		// FIN DE LAS CONDICIONES
		$consulta_x = "SELECT ce_pagos.Fecha_Pago, sum(ce_pagos.Monto) AS SumaDeMonto, a_banco.banco, a_banco.Descripcion, ce_pagos.Sector FROM ce_pagos INNER JOIN a_agencia ON a_agencia.id_agencia = ce_pagos.Agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco WHERE ce_pagos.Sector = " . $_SESSION['OSEDE'] . " AND Month(ce_pagos.Fecha_Pago)=" . $_SESSION['VARIABLE'] . " AND Year(ce_pagos.Fecha_Pago)=" . $_SESSION[VARIABLE2] . " GROUP BY ce_pagos.Fecha_Pago, a_banco.banco, a_banco.Descripcion, ce_pagos.Sector ORDER BY a_banco.banco ASC, ce_pagos.Fecha_Pago ASC";
		$tabla_x = mysql_query($consulta_x);
		if ($registro_x = mysql_fetch_object($tabla_x)) {
			echo '<form name="form3" method="post" action="Reportes/banco_diario.php" target="_blank">
  <p align="center"><input type="submit" class="boton" name="CMDDIARIO" value="Reporte Diario"></p>
</form>';
		} else {
			echo '<form name="form5" method="post">
  <p align="center"><strong>&iexcl; No Existe Informacion para esas Fechas ! </strong></p>
</form>';
		}
	}
	?>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>