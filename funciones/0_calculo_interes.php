<?php

// PARA ELIMINAR INTERESES ------------------------------------
if ($_POST['CMDELIMINAR'] and isset($_POST['IdsExpedientes'])) {
	foreach ($_POST['IdsExpedientes'] as $IdExpediente) {
		// CONSULTA PARA ELIMINAR
		$consulta = "DELETE FROM liquidacion WHERE id_liquidacion=" . $IdExpediente;
		$tabla = mysql_query($consulta);
		//--------------
		echo "<script type=\"text/javascript\">alert('���Inter�s Eliminado Exitosamente!!!');</script>";
	}
}
// FIN ------------------------------------

// ----------- CALCULO DEL interes Y/O GUARDADO
if ($_POST['CMDCALCULAR'] == 'Calcular' or $_POST['CMDAGREGAR'] == 'Agregar') {
	if ($_POST['OFECHAINICIO'] <> "" & $_POST['OFECHAFIN'] <> "" & $_POST['OFECHAVEN'] <> "" & $_POST['OFECHAPAGO'] <> "" & $_POST['OMONTO'] > 0 & $_POST['OTRIBUTO'] > 0) {
		// CALCULO DE LOS INTERESES
		$interes = funcion_interes($_POST['OMONTO'], $_POST['OFECHAPAGO'], $_POST['OFECHAVEN']);
		// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------

		// AGREGAR LA SANCION A LIQUIDACION -----------------------------------------------------
		$sancion = 2009;
		// --------------------
		if ($_POST['CMDAGREGAR'] == 'Agregar') {
			$sql_existe = "SELECT * FROM vista_sanciones_aplicadas WHERE periodoinicio='" . voltea_fecha($_POST['OFECHAINICIO']) . "' AND periodofinal='" . voltea_fecha($_POST['OFECHAFIN']) . "' AND rif='" . $_SESSION['RIF'] . "' AND id_sancion=" . $sancion . " AND id_tributo=" . $_POST['OTRIBUTO'];
			$tabla_existe = mysql_query($sql_existe);
			$existe = mysql_num_rows($tabla_existe);
			if ($existe < 1) {
				$monto = $_POST['OMONTO'];
				$tributo = $_POST['OTRIBUTO'];
				$_POST['OTRIBUTO'] = 99;
				$ut_aplicadas = 0;
				// INSERTAR EL REGISTRO EN LIQUIDACION
				$consulta = "INSERT INTO liquidacion (sector, origen_liquidacion, anno_expediente, num_expediente, rif, periodoinicio, periodofinal, id_sancion, fecha_vencimiento, fecha_pago, monto_ut, monto_bs, monto_pagado, id_tributo, id_tributo2, status, usuario) VALUES (" . $_SESSION['SEDE'] . ", " . $_SESSION['ORIGEN'] . ", " . $_SESSION['ANNO_PRO'] . ", " . $_SESSION['NUM_PRO'] . ", '" . $_SESSION['RIF'] . "', '" . voltea_fecha($_POST['OFECHAINICIO']) . "', '" . voltea_fecha($_POST['OFECHAFIN']) . "', " . $sancion . ", '" . voltea_fecha($_POST['OFECHAVEN']) . "', '" . voltea_fecha($_POST['OFECHAPAGO']) . "', '" . $ut_aplicadas . "', '" . $interes . "', '" . $_POST['OMONTO'] . "', " . $_POST['OTRIBUTO'] . ", " . $tributo . ", 0, " . $_SESSION['CEDULA_USUARIO'] . ")";
				if ($tabla = mysql_query($consulta)) {
					$_SESSION['VARIABLE'] = 'SI';
					echo "<script type=\"text/javascript\">alert('���Interes Cargado Exitosamente!!!');</script>";
				}
				// --------------------
			} else {
				$registro_e = mysql_fetch_object($tabla_existe);
				//---------------------------------
				echo "<script type=\"text/javascript\">alert('���Sancion Duplicada!!!');</script>";
				echo "<script type=\"text/javascript\">alert('���Dependencia=> " . $registro_e->dependencia . '  /  Area=> ' . $registro_e->area . '\n A�o=> ' . $registro_e->anno_expediente . ' / Expediente o Providencia=> ' . $registro_e->num_expediente . "!!!');</script>";
				//---------------------------------
			}
		}
		// FIN
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

// ----------- FIN DEL GUARDADO
?>
<table width="45%" border="1" align="center">
	<tr>
		<td align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Datos del Inter&eacute;s a Calcular </u></span></td>
	</tr>
	<tr>
		<td width="16%" bgcolor="#CCCCCC">
			<div align="center"><strong>Tributo:</strong></div>
		</td>
		<td width="17%" colspan="3"><select name="OTRIBUTO" size="1">
				<option value="-1">Seleccione</option>
				<?php
				$consulta2 = "SELECT id_tributo, nombre FROM a_tributos WHERE id_tributo in (1,3,8,9,11,13) ORDER BY id_tributo;";
				$tabla2 = mysql_query($consulta2);
				while ($registro2 = mysql_fetch_object($tabla2)) {
					echo '<option';
					if ($_POST['OTRIBUTO'] == $registro2->id_tributo) {
						echo ' selected="selected" ';
					}
					echo ' value="';
					echo $registro2->id_tributo;
					echo '">';
					echo $registro2->nombre;
					echo '</option>';
				}
				?>
			</select></td>
	</tr>
	<tr>
		<td width="16%" bgcolor="#CCCCCC">
			<div align="center"><strong>Per&iacute;odo Inicio: </strong></div>
		</td>
		<td width="17%"><label>
				<div align="center">
					<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAINICIO" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																	echo $_POST['OFECHAINICIO'];
																																} ?>">
			</label>
			</div>
		</td>
		<td width="16%" bgcolor="#CCCCCC">
			<div align="center"><strong>Per&iacute;odo Fin:</strong></div>
		</td>
		<td width="18%"><label>
				<div align="center"><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAFIN" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																				echo $_POST['OFECHAFIN'];
																																			} ?>">
			</label>
			</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC">
			<div align="center"><strong>Fecha Vencimiento:</strong></div>
		</td>
		<td>
			<div align="center"><label>
					<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAVEN" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																echo $_POST['OFECHAVEN'];
																															} ?>"></label></div>
		</td>

		<td bgcolor="#CCCCCC">
			<div align="center"><strong>Fecha Pago:</strong></div>
		</td>
		<td><label>
				<div align="center"><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAPAGO" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																					echo $_POST['OFECHAPAGO'];
																																				} ?>">
			</label></div>
		</td>
	</tr>
	<tr>
		<td width="16%" bgcolor="#CCCCCC">
			<div align="center"><strong>Monto:</strong></div>
		</td>
		<td width="17%"><label>
				<div align="center"><input type="text" onkeypress="return SoloMoneda(event,this)" style="text-align:right" name="OMONTO" maxlength="12" size="10" value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																												echo $_POST['OMONTO'];
																																											} ?>">
				</div>
		</td>

		<td width="17%" bgcolor="#CCCCCC">
			<div align="center"><strong>Interes: </strong></div>
		</td>
		<td width="16%">
			<div align="center"><?php if ($_SESSION['VARIABLE'] == 'NO') {
									echo number_format(doubleval($interes), 2, ',', '.');
								} ?></div>
		</td>
	</tr>
</table>
<p align="center">
	<input type="submit" class="boton" name="CMDCALCULAR" value="Calcular">
	<input type="submit" class="boton" name="CMDAGREGAR" value="Agregar">
</p>
<table class="formateada" width="50%" border=1 align=center>
	<tbody>
		<tr>
			<td bgcolor="#FF0000" height="27" colspan="11" align="center">
				<p class="Estilo7"><u>Intereses actuales aplicados al Contribuyente</u></p>
			</td>
		</tr>
		<tr>
			<th width="2%">
				<div align="center" class="Estilo8">#</div>
			</th>
			<th>
				<div align="center" class="Estilo8">Tributo</div>
			</th>
			<th>
				<div align="center" class="Estilo8">Per&iacute;odo</div>
			</th>
			<th>
				<div align="center" class="Estilo8">Monto</div>
			</th>
			<th>
				<div align="center" class="Estilo8">Fecha Ven</div>
			</th>
			<th>
				<div align="center" class="Estilo8">Fecha Pago</div>
			</th>
			<th>
				<div align="center" class="Estilo8">Interes</div>
			</th>
		</tr>
		<?php
		$consulta_m = "SELECT id_liquidacion, siglas, nombre, periodoinicio, periodofinal, fecha_vencimiento, monto_bs, fecha_pago, monto_pagado, serie FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND serie=38 AND sector=" . $_SESSION['SEDE_USUARIO'] . " AND origen_liquidacion = " . $_SESSION['ORIGEN'] . " and anno_expediente=" . $_SESSION['ANNO_PRO'] . " and num_expediente=" . $_SESSION['NUM_PRO'] . " and serie=38 ORDER BY id_sancion ASC, periodoinicio ASC, periodofinal ASC";

		$tabla = mysql_query($consulta_m);

		$z = 0;

		while ($registro = mysql_fetch_object($tabla)) {
			$z++;

		?>
			<tr id="fila<?php echo $registro->id_liquidacion; ?>">
				<td width="2%">
					<div align="center" class="Estilo8"><input type="checkbox" name="IdsExpedientes[]" value="<?php echo $registro->id_liquidacion; ?>" onClick="marcar(this,<?php echo $registro->id_liquidacion; ?>)" /></div>
				</td>
				<td>
					<div align="center" class="Estilo8"><?php echo $registro->siglas; ?></div>
				</td>
				<td>
					<div align="center" class="Estilo8"><?php echo voltea_fecha($registro->periodoinicio) . ' al ' . voltea_fecha($registro->periodofinal); ?></div>
				</td>
				<td>
					<div align="center" class="Estilo8"><?php echo number_format(doubleval($registro->monto_pagado), 2, ',', '.'); ?></div>
				</td>
				<td>
					<div align="center" class="Estilo8"><?php echo date("d-m-Y", strtotime($registro->fecha_vencimiento)); ?></div>
				</td>
				<td>
					<div align="center" class="Estilo8"><?php echo date("d-m-Y", strtotime($registro->fecha_pago)); ?></div>
				</td>
				<td>
					<div align="center" class="Estilo8"><?php echo number_format(doubleval($registro->monto_bs), 2, ',', '.'); ?></div>
				</td>
			</tr>
		<?php
		}
		$_SESSION['VARIABLE1'] = $z;
		?>
	</tbody>
</table>

<p></p>
<div align="center">
	<input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />

</div>