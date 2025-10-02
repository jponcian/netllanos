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

		<table width="35%" border="1" align="center">
			<tr>
				<td height="33" colspan="5" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo15">Reporte Auxiliar Contable Planillas Pagadas </span></p>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="35%" border="1" align="center">
			<tr>
				<td height="33" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo15">Dependencia</span></p>
				</td>
				<td colspan="4" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo15">Fecha
						</span></p>
				</td>
			</tr>
			<tr>
				<td width="70%"><span class="Estilo1">
						<select name="OSEDE" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							if ($_SESSION['ADMINISTRADOR'] > 0) {
								$origen = '';
								$consulta_x = 'SELECT id_sector, nombre FROM z_sectores';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['id_sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
							} else {
								// -------------------------------------
								$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'];
								$tabla_x = mysql_query($consulta_x);
								if ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['id_sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
							}
							?>
						</select>
					</span></td>
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
			<tr>
				<td height="33" colspan="5" align="center" bgcolor="#FF0000">
					<p class="Estilo1"><span class="Estilo15">Tributo</span></p>
				</td>
			</tr>
			<tr>
				<td colspan="5"><span class="Estilo1">
						<select name="OTRIBUTO" size="1">
							<option value="0">Seleccione</option>
							<?php
							$origen = '';
							$consulta_x = 'SELECT id_tributo, nombre FROM a_tributos WHERE id_tributo = 1 or id_tributo= 3';
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OTRIBUTO'] == $registro_x['id_tributo']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['id_tributo'] . '>' . $registro_x['nombre'] . '</option>';
							}
							?>
						</select>
					</span></td>
			</tr>
			<tr>
				<td height="40" colspan="5" align="center" bgcolor="#504B4B"><input type="submit" class="boton" name="CMDBOTON" value="Cargar"></td>
			</tr>
		</table>
		<p>&nbsp;</p>
	</form>

	<p>
		<?php

		if ($_POST['OSEDE'] > 0 and $_POST['OINICIO'] <> "" and $_POST['OFIN'] <> "" and $_POST['OTRIBUTO'] > 0) {
			$_SESSION['OSEDE'] = $_POST['OSEDE'];
			$_SESSION['VARIABLE'] = $_POST['OOPCION'];
			$_SESSION['FECHA1'] = $_POST['OINICIO'];
			$_SESSION['FECHA2'] = $_POST['OFIN'];
			$_SESSION['OTRIBUTO'] = $_POST['OTRIBUTO'];

			$sql = "SELECT ce_pagos.Rif AS Rif, contribuyentes.contribuyente AS contribuyente, ce_cal_tip_obligaciones.Tipo, liquidacion.liquidacion, liquidacion.sector, liquidacion.origen_liquidacion, liquidacion.anno_expediente, liquidacion.num_expediente, liquidacion.fecha_impresion, ce_pagos.Numero, liquidacion.fecha_not, liquidacion.monto_bs, liquidacion.concurrencia, liquidacion.especial, ce_pagos.Fecha_Pago, a_banco.banco, a_agencia.id_agencia_especial, ce_pagos.Monto AS monto_recaudado, ce_cal_tip_obligaciones.Numero AS forma, liquidacion.id_tributo2, liquidacion.id_tributo FROM ((((ce_pagos JOIN liquidacion ON (((liquidacion.rif = ce_pagos.Rif) AND (ce_pagos.Numero = liquidacion.planilla_notificacion)))) JOIN a_agencia ON ((a_agencia.id_agencia = ce_pagos.Agencia))) JOIN a_banco ON ((a_banco.id_banco = a_agencia.id_banco))) JOIN contribuyentes ON ((ce_pagos.Rif = contribuyentes.rif))) INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto WHERE ce_pagos.Fecha_Pago BETWEEN '" . voltea_fecha($_POST['OINICIO']) . "' AND '" . voltea_fecha($_POST['OFIN']) . "' AND liquidacion.id_tributo2 = " . $_POST['OTRIBUTO'] . " AND ce_pagos.Tipo_Impuesto BETWEEN 8 AND 9";
			//echo $sql;
			$tabla = mysql_query($sql);
			$cantidad = mysql_num_rows($tabla);
			if ($cantidad > 0) {
		?>
	<table width=300 align=center border=0>
		<td colSpan=1>
			<div align="center">
				<form name="form3" method="post" action="reportes/auxilar_contable_liq_not.php" target="_blank">
					<input type="submit" class="boton" name="CMDREPORTE" value="Ver Reporte">
				</form>
			</div>
		</td>
		</tr>
	</table>
<?php
			} else { ?>
	<p align="center"><strong>!!!...No existen registros para estos par&aacute;metros...!!!</strong></p>
<?php }
		}
?>
<p>&nbsp;
</p>
<?php include "../pie.php"; ?>
</p>
<p>&nbsp;</p>
</body>

</html>