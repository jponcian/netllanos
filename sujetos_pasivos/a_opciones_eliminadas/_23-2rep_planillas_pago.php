<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$_SESSION['VARIABLE'] = $_POST['OOPCION'];
$_SESSION['RIF'] = $_POST['ORIF'];
$_SESSION['INICIO'] = $_POST['OINICIO'];
$_SESSION['FIN'] = $_POST['OFIN'];
$_SESSION['IMPUESTO'] = $_POST['OIMPUESTO'];
$_SESSION['OSEDE'] = $_POST['OSEDE'];
$_SESSION['TERMINAL'] = $_POST['OTERMINAL'];

?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Planillas de Pago</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

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
		<table width="43%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1"><u>Planillas de Pago</u></span></td>
			</tr>
			<tr>
				<td colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha de Pago:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="18%" bgcolor="#CCCCCC">
					<div align="center"><strong> Desde:</strong></div>
				</td>
				<td width="30%"><label>
						<input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="8" readonly="true" value="<?php if (isset($_POST['OINICIO'])) {
																																		echo $_POST['OINICIO'];
																																	} ?>"></label></td>
				<td width="16%" bgcolor="#CCCCCC">
					<div align="center"><strong> Hasta:</strong></div>
				</td>
				<td width="36%"><label>
						<input type="text" name="OFIN" size="8" readonly="true" onclick='javascript:scwShow(this,event);' value="<?php if (isset($_POST['OFIN'])) {
																																		echo $_POST['OFIN'];
																																	} ?>"></label></td>
			</tr>
			<tr>
				<td height="30" colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Sector:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label><span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
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
			<tr>
				<td height="30" colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Terminal de Rif:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label>
						<select name="OTERMINAL" size="1" onChange="this.form.submit()">
							<option value="100"> ->-> TODOS <-<- </option>
									<?php
									$consulta_x = "SELECT RIGHT(ce_pagos.Rif,1) AS Terminal FROM ce_pagos WHERE ce_pagos.Sector = 0" . $_POST['OSEDE'] . " GROUP BY RIGHT(ce_pagos.Rif,1)";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';
										if ($_POST[OTERMINAL] == $registro_x->Terminal) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->Terminal;
										echo '">';
										echo $registro_x->Terminal;
										echo '</option>';
									}
									?>
						</select>
					</label></td>
			</tr>
			<tr>
				<td height="30" colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label>
						<select name="ORIF" size="1" onChange="this.form.submit()">
							<option value="100"> ->-> TODOS <-<- </option>
									<?php
									if ($_POST['OTERMINAL'] == '100') {
										$terminal = '';
									} else {
										$terminal = "AND RIGHT(ce_pagos.Rif,1) = " . $_POST['OTERMINAL'];
									}

									$consulta_x = "SELECT ce_pagos.Rif, LEFT(vista_contribuyentes_direccion.contribuyente,40) AS NombreRazon FROM ce_pagos INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = ce_pagos.Rif WHERE ce_pagos.Sector = 0" . $_POST['OSEDE'] . " " . $terminal . " GROUP BY vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';
										if ($_POST['ORIF'] == $registro_x->Rif) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->Rif;
										echo '">';
										echo $registro_x->Rif . ' - ' . $registro_x->NombreRazon;
										echo '</option>';
									}
									?>
						</select>
					</label></td>
			</tr>
			<tr>
				<td colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Impuesto:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label>
						<select name="OIMPUESTO" size="1">
							<option value="0"> ->-> TODOS <-<- </option>
									<?php

									if ($_POST['ORIF'] == 0) {
										$Rif = "<>''";
									} else {
										$Rif = "='" . $_POST['ORIF'] . "'";
									}

									$consulta_x = "SELECT ce_cal_tip_obligaciones.Numero, ce_cal_tip_obligaciones.Tipo FROM ce_cal_tip_obligaciones INNER JOIN ce_pagos ON ce_pagos.Tipo_Impuesto = ce_cal_tip_obligaciones.Numero WHERE ce_pagos.Rif" . $Rif . " GROUP BY ce_cal_tip_obligaciones.Numero, ce_cal_tip_obligaciones.Tipo ORDER BY ce_cal_tip_obligaciones.Numero ASC";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';
										if ($_POST['OIMPUESTO'] == $registro_x->Numero) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->Numero;
										echo '">';
										echo $registro_x->Numero . ' - ' . $registro_x->Tipo;
										echo '</option>';
									}
									?>
						</select>
						<a href="javascript:NewCssCal('OINICIO','YYYYMMDD')"></a></label></td>
			</tr>
		</table>
		<p align="center">
			<label></label>
			<label>
				<input type="submit" class="boton" name="CMDCARGAR" value="Cargar">
			</label>
		</p>

	</form>
	<?php
	if ($_POST['CMDCARGAR'] == 'Cargar' and $_SESSION['INICIO'] <> "" and $_SESSION['FIN'] <> "") {
		// CONDICIONES
		if ($_SESSION['OSEDE'] == 0) {
			$Sede = '';
		} else {
			$Sede = " AND Sector=" . $_SESSION['OSEDE'] . "";
		}
		if ($_SESSION['RIF'] == 100) {
			$Contribuyente = '';
		} else {
			$Contribuyente = " AND ce_pagos.Rif='" . $_SESSION['RIF'] . "'";
		}
		if ($_SESSION['TERMINAL'] == 100) {
			$terminal = '';
		} else {
			$terminal = " AND RIGHT(ce_pagos.Rif,1)='" . $_SESSION['TERMINAL'] . "'";
		}
		if ($_SESSION['IMPUESTO'] == 0) {
			$Impuesto = '';
		} else {
			$Impuesto = " AND ce_pagos.Tipo_Impuesto=" . $_SESSION['IMPUESTO'];
		}
		// FIN DE LAS CONDICIONES
		$consulta_x = "SELECT ce_pagos.Rif, vista_contribuyentes_direccion.contribuyente AS NombreRazon, ce_cal_tip_obligaciones.Tipo, date_format(ce_pagos.Fecha_Presentacion,'%d/%m/%Y') AS presentacion, date_format(ce_pagos.Fecha_Pago,'%d/%m/%Y') AS pago, ce_pagos.Monto FROM ce_pagos INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = ce_pagos.Rif INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto WHERE ce_pagos.Fecha_Pago >= '" . $_SESSION['INICIO'] . "' AND ce_pagos.Fecha_Pago <= '" . $_SESSION['FIN'] . "'" . $Contribuyente . $Impuesto . $Sede . $terminal . " ORDER BY ce_pagos.Fecha_Presentacion ASC, ce_pagos.Fecha_Pago ASC, ce_pagos.Rif ASC";
		$tabla_x = mysql_query($consulta_x);
		if ($registro_x = mysql_fetch_object($tabla_x)) {
	?>
			<table width="200" align="center">
				<tr>
					<td>
						<form name="form2" method="post" action="reportes/reporte.php" target="_blank">
							<div align="center">
								<input type="submit" class="boton" name="CMDREPORTE" value="Ver en PDF">
							</div>
						</form>
					</td>
					<td>
						<form name="form3" method="post" action="reportes/reporte_html.php" target="_blank">
							<div align="center">
								<input type="submit" class="boton" name="CMDREPORTE2" value="Ver en HTML">
							</div>
						</form>
					</td>
				</tr>
			</table>
		<?php
		} else {
		?>
			<p align="center"><strong>&iexcl; No Existe Informacion para esas Fechas ! </strong></p>
	<?php
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