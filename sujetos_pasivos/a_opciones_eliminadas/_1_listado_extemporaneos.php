<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 132;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['CMDMARCAR'] == "Procesar") { // SI PRESIONA EL BOTON
	$i = 1;
	while ($i <= $_SESSION['VARIABLE1']) {
		if ($_POST['C' . $i] > 0) {
			$consulta_xxx = "UPDATE ce_pagos SET Multado=1 WHERE indice=" . $_POST['C' . $i] . ";";
			//echo $consulta_xxx;
			$tabla_xxx = mysql_query($consulta_xxx);
		}
		$i++;
	}
} // FIN SI PRESIONA EL BOTON

?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Incluir Sanci&oacute;n</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../auxiliar.js"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

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

	<form name="form1" method="post" action="#vista">
		<table width="584" border=1 align=center>
			<tr>
				<td width="574" height="35" align="center" bgcolor="#FF0000">
					<p class="Estilo7 Estilo1"><u>LISTADO EXTEMPORANEOS</u></p>
				</td>
			</tr>
		</table>

		<table width="33%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7 Estilo1"><u>Seleccionar Opciones</u></span></td>
			</tr>
			<tr>
				<td height="33" colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Sector:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label><span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
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
				<td height="33" colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label><span class="Estilo1">
							<select name="ORIF">
								<option value="0">--> Todos <--< /option>
										<?php
										if ($_POST['OSEDE'] > 0) {
											$consulta_x = 'select `contribuyentes`.`rif` AS `rif`,left(`contribuyentes`.`contribuyente`,60) AS `nombre`  from (`contribuyentes` join `ce_pagos` on((`contribuyentes`.`rif` = `ce_pagos`.`Rif`))) WHERE (ce_pagos.Fecha_Pago > ce_pagos.Fecha_Ven OR ce_pagos.Fecha_Presentacion > ce_pagos.Fecha_Ven) AND ce_pagos.Sector = 0' . $_POST['OSEDE'] . '  group by `contribuyentes`.`rif` order by trim(`contribuyentes`.`contribuyente`)';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['ORIF'] == $registro_x['rif']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['rif'] . '>' . $registro_x['rif'] . ' - ' . palabras($registro_x['nombre']) . '</option>';
											}
										}
										?>
							</select>
						</span></label></td>
			</tr>
			<tr>
				<td height="33" colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Impuesto:</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="4"><label><span class="Estilo1">
							<select name="OIMPUESTO">
								<option value="0">--> Todos <--< /option>
										<?php
										if ($_POST['OSEDE'] > 0) {
											//$consulta_x = "SELECT Numero, LEFT(Tipo,80) as Tipo1 FROM ce_cal_tip_obligaciones group by `tipo` ORDER BY Numero ASC";	
											$consulta_x = 'SELECT ce_cal_tip_obligaciones.Numero, LEFT(ce_cal_tip_obligaciones.Tipo,80) as Tipo1 FROM (contribuyentes JOIN ce_pagos ON ((contribuyentes.rif = ce_pagos.Rif))) INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto where ((`ce_pagos`.`Fecha_Pago` > `ce_pagos`.`Fecha_Ven`) or (`ce_pagos`.`Fecha_Presentacion` > `ce_pagos`.`Fecha_Ven`)) AND ce_pagos.Sector = 0' . $_POST['OSEDE'] . ' group by `tipo` ORDER BY ce_cal_tip_obligaciones.Numero ASC';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['OIMPUESTO'] == $registro_x['Numero']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['Numero'] . '>' . $registro_x['Numero'] . ' - ' . $registro_x['Tipo1'] . '</option>';
											}
										}
										?>
							</select>
						</span><a href="javascript:NewCssCal('OINICIO','YYYYMMDD')"></a></label></td>
			</tr>
			<tr>
				<td height="36" colspan="4" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha de Vencimiento:</strong></div>
				</td>
			</tr>
			<tr>
				<td width="17%" bgcolor="#CCCCCC"><strong> Desde:</strong></td>
				<td><label></label>
					<div align="center">
						<input onClick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="10" readonly value="<?php echo $_POST['OINICIO']; ?>" />
					</div>
				</td>
				<td width="16%" bgcolor="#CCCCCC"><strong> Hasta:</strong></td>
				<td><label></label>
					<div align="center">
						<input onClick='javascript:scwShow(this,event);' type="text" name="OFIN" size="10" readonly value="<?php echo $_POST['OFIN']; ?>" />
					</div>
				</td>
			</tr>
			<td height="33" colspan="4" bgcolor="#CCCCCC">
				<div align="center"><strong>Orden:</strong></div>
			</td>
			</tr>
			<tr>
				<td colspan="4"><label><span class="Estilo1">
							<select name="OORDEN">
								<option <?php if ($_POST['OORDEN'] == 'rif') {
											echo 'selected="selected"';
										} ?> value="rif">Rif</option>
								<option <?php if ($_POST['OORDEN'] == 'contribuyente') {
											echo 'selected="selected"';
										} ?> value="contribuyente">Contribuyente</option>
								<option <?php if ($_POST['OORDEN'] == 'Fecha_Presentacion') {
											echo 'selected="selected"';
										} ?> value="Fecha_Presentacion">Fecha de Presentacion</option>
								<option <?php if ($_POST['OORDEN'] == 'Fecha_Pago') {
											echo 'selected="selected"';
										} ?> value="Fecha_Pago">Fecha de Pago</option>
								<option <?php if ($_POST['OORDEN'] == 'Fecha_Ven') {
											echo 'selected="selected"';
										} ?> value="Fecha_Ven">Fecha de Vencimiento</option>
								<option <?php if ($_POST['OORDEN'] == 'sancionado DESC') {
											echo 'selected="selected"';
										} ?> value="sancionado DESC">Sancionado</option>
							</select>
						</span><a href="javascript:NewCssCal('OINICIO','YYYYMMDD')"></a></label></td>
		</table>
		<p></p>
		<p align="center">
			<label>
				<input type="submit" class="boton" name="CMDACTUALIZAR" value="Actualizar">
			</label>
		</p>
		<a name="vista"></a>
		<table align="center" class="formateada" width="70%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="12" align="center">
					<p class="Estilo7"><u>Planillas Pagadas </u></p>
				</td>
			</tr>
			<tr>
				<td height="35" bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Sel.</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Rif</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Contribuyente</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Impuesto</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Periodo</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Fecha Vencimiento</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Fecha Presentaci�n</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Fecha Pago</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Monto</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Planilla</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Banco</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Sancionado</div>
				</td>
			</tr>
			<?php
			if (($_POST['CMDACTUALIZAR'] == 'Actualizar' or $_POST['CMDMARCAR'] == 'Procesar') and $_POST['OSEDE'] > 0 and $_POST['OINICIO'] <> '' and $_POST['OFIN'] <> '') {
				if ($_POST['ORIF'] == '0' or $_POST['ORIF'] == '') {
					$rif = '';
				} else {
					$rif = " rif='" . $_POST['ORIF'] . "' AND ";
				}

				if ($_POST['OIMPUESTO'] == '0'  or $_POST['OIMPUESTO'] == '') {
					$impuesto = '';
				} else {
					$impuesto = "id_impuesto=" . $_POST['OIMPUESTO'] . " AND ";
				}

				$i = 1;
				// CONSULTA DE TODOS LOS PAGOS
				$consulta_x = "SELECT * FROM vista_especiales_extemporaneos WHERE sancionado=0 and " . $rif . " " . $impuesto . " Sector=" . $_POST['OSEDE'] . " and Fecha_Ven > '" . voltea_fecha($_POST['OINICIO']) . "' and Fecha_Ven < '" . voltea_fecha($_POST['OFIN']) . "' ORDER BY " . $_POST['OORDEN'] . ";";
				//echo $consulta_x;
				$tabla_x = mysql_query($consulta_x);
				$numero_filas = mysql_num_rows($tabla_x);
				//-----------------------
				if ($numero_filas > 0) {
					while ($registro_x = mysql_fetch_object($tabla_x)) {
						$Quincena = "";
						if ($registro_x->Quincena > 0) {
							$Quincena = ' - ' . $registro_x->Quincena;
						}

						//--------------
						echo '<tr id="fila' . $i . '" >
		  <td ><div align="center"><input type="checkbox" name="C' . $i . '" value="' . $registro_x->indice . '"></div></td>
			<td ><div align="center">' . $registro_x->rif . '</div></td>
		  <td><div align="left"">' . palabras($registro_x->contribuyente) . '</div></td>
		   <td><div align="center">' . $registro_x->Tipo . '</div></td>
		  <td><div align="center">' . $registro_x->Periodo . $Quincena . '</div></td>
		  <td><div align="center">' . voltea_fecha($registro_x->Fecha_Ven) . '</div></td>
		  <td><div align="center">' . voltea_fecha($registro_x->Fecha_Presentacion) . '</div></td>
		  <td><div align="center">' . voltea_fecha($registro_x->Fecha_Pago) . '</div></td>
		  <td><div align="center">' . formato_moneda($registro_x->Monto) . '</div></td>
		<td><div align="center">' . $registro_x->Numero . '</div></td>
		  <td><div align="center">' . palabras($registro_x->Descripcion) . '</div></td>
		   <td><div align="center">' . formato_si($registro_x->sancionado) . '</div></td>			
				</tr>';
						$i++; //formato_si($registro_x->sancionado)
					}
					$_SESSION['VARIABLE1'] = $i;
				} else {
			?>
					<tr>
						<td height="35" colspan="12" bgcolor="">
							<div align="center" class="Estilo9"><strong>No Existe Informaci�n con las Opciones Seleccionadas !!!</strong></div>
						</td>
					</tr>
			<?php
				}
			}
			?>
		</table>
		<p></p>
		<div align="center">
			<p>
				<?php
				if ($numero_filas > 0) {
				?>
					<input type="submit" class="boton" name="CMDMARCAR" value="Procesar">
				<?php
				}
				?>
			</p>
			<p>&nbsp;</p>
			<!--  <table align="center" width="282" border="1">
<tr>
  <td bgcolor="#CCCCCC" colspan="2" ><div align="center"><strong>Leyenda</strong></div></td>
</tr>
 <tr>
  <td width="92" bgcolor="#33CC66" ><div align="center" class="Estilo15">Fondo Verde</div></td>
  <td width="174" bgcolor="#FFFFFF" ><div align="center" class="Estilo9">Pago Extemporaneo </div></td>
</tr>
 <tr>
   <td bgcolor="#0000FF" ><div align="center" class="Estilo15">Fondo Azul</div></td>
   <td bgcolor="#FFFFFF" ><div align="center" class="Estilo9">Presentacion otra Entidad</div></td>
 </tr>
 <tr>
  <td width="92" bgcolor="#FF0000" ><div align="center" class="Estilo15">Fondo Rojo </div></td>
  <td width="174" bgcolor="#FFFFFF" ><div align="center" class="Estilo9">Ambos Casos </div></td>
</tr>
</table>      -->
		</div>
		<p>&nbsp;</p>
	</form>

	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>