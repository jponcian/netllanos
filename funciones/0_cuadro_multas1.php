<style type="text/css">
	<!--
	.Estilo2 {
		color: #FFFFFF;
		font-weight: bold;
	}
	-->
</style>
<p></p>
<table width="40%" border="1" align="center">
	<tr>
		<td align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Datos de la(s) Sancion(es) </u></span></td>
	</tr>
	<tr>
		<td width="20%" bgcolor="#CCCCCC">
			<div align="center"><strong>Fecha:</strong></div>
		</td>
		<td><label>
				<div align="left"><span class="Estilo15"><?php echo date("d/m/Y"); ?></span></div>
			</label></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC">
			<div align="center"><strong>Tributo:</strong></div>
		</td>
		<td><label>
				<div align="left">
					<select class="select2" name="OTRIBUTO" style="width: 650px" onChange="this.form.submit()">
						<option value="-1">Seleccione</option>
						<?php
						$consulta2 = "SELECT id_tributo, nombre FROM a_tributos WHERE (id_tributo > 0 and id_tributo < 20) or id_tributo = 64  ORDER BY id_tributo;";
						$tabla2 = mysql_query($consulta2);
						while ($registro2 = mysql_fetch_object($tabla2)) {
							echo '<option';
							if ($_POST[OTRIBUTO] == $registro2->id_tributo) {
								echo ' selected="selected" ';
							}
							echo ' value="';
							echo $registro2->id_tributo;
							echo '">';
							echo $registro2->id_tributo . ' - ' . $registro2->nombre;
							echo '</option>';
							// FIN
						}
						?>
					</select>
				</div>
			</label></td>
	</tr>
</table>
<table width="60%" border="1" align="center">
	<tr>
		<td colspan="2" align="left" bgcolor="#FF0000">
			<div align="center" class="Estilo2">Seleccione la Sanci&oacute;n</div>
		</td>
	</tr>
	<tr>
		<td width="20%"><span class="Estilo16">
				<select name="OSANCION" class="select2" style="width: 800px" onchange="this.form.submit()">
					<option value="-1"> -&gt;-&gt; SELECCIONE LA SANCI&Oacute;N &lt;-&lt;- </option>
					<?php
					if ($_POST['OTRIBUTO'] > 0) {
						$consulta3 = 'SELECT a_leyes.Ley, id_sancion, sancion as Sancion, sancion AS SancionCompleta, ind_planilla, art_regla, aplicacion, art_ley_rgto, art_cot FROM a_sancion, a_leyes WHERE a_sancion.ley = a_leyes.codigo AND tributo=' . $_POST['OTRIBUTO'] . ' ORDER BY id_sancion;';
						if ($rif == 'si') {
							$consulta3 = 'SELECT a_leyes.Ley, id_sancion, sancion as Sancion, sancion AS SancionCompleta, ind_planilla, art_regla, aplicacion, art_ley_rgto, art_cot FROM a_sancion, a_leyes WHERE a_sancion.ley = a_leyes.codigo AND id_sancion>=10849 AND id_sancion<=11000  AND tributo=' . $_POST['OTRIBUTO'] . ' ORDER BY id_sancion;';
						}
						$tabla3 = mysql_query($consulta3);
						while ($registro3 = mysql_fetch_object($tabla3)) {
							echo '<option';
							if ($_POST['OSANCION'] == $registro3->id_sancion) {
								// --- SANCION SELECCIONADA
								echo ' selected="selected" ';
								$texto =  $registro3->id_sancion . " - " . $registro3->SancionCompleta;
								//----------------
								$sancion =  $registro3->id_sancion;
								$ley =  $registro3->art_ley_rgto;
								$ley1 =  $registro3->Ley;
								$prov =  $registro3->art_regla;
								$aplicacion =  $registro3->aplicacion;
								$art_cot =  $registro3->art_cot;
								$planilla =  $registro3->ind_planilla;
								//----------------
							}
							echo ' value="';
							echo $registro3->id_sancion;
							echo '">';
							//-----------------
							if (trim(substr($registro3->Sancion, 0, 21)) == 'LA (EL) CONTRIBUYENTE') {
								echo $registro3->id_sancion . " - " . trim(substr($registro3->Sancion, 21, 120));
							} else {
								echo $registro3->id_sancion . " - " . trim(substr($registro3->Sancion, 0, 120));
							}
							//-----------------
							echo '</option>';
						}
					}
					?>
				</select>
			</span></td>
	</tr>
</table>
<table width="60%" border="1" align="center">
	<?php if ($_SESSION['ADMINISTRADOR'] > 0) { ?>
		<tr>
			<td width="20%" align="left" bgcolor="#CCCCCC">
				<div align="left"><strong>Aplicacion</strong></div>
			</td>
			<td><span class="Estilo15"><?php if ($_POST['OSANCION'] > 0) {
											echo $aplicacion;
										} ?>
				</span></td>
		</tr>
	<?php } ?>
	<tr>
		<td width="20%" align="left" bgcolor="#CCCCCC">
			<div align="left"><strong>Sanci&oacute;n:</strong></div>
		</td>
		<td><span class="Estilo15"><?php if ($_POST['OSANCION'] > 0) {
										echo $texto;
									} ?>
			</span></td>
	</tr>
	<tr>
		<td align="left" bgcolor="#CCCCCC">
			<div align="left"><strong>Articulo Ley:</strong></div>
		</td>
		<td><span class="Estilo15"><?php if ($_POST['OSANCION'] > 0) {
										echo $ley;
										echo '; ';
										echo $prov;
									} ?>
			</span></td>
	</tr>
	<tr>
		<td align="left" bgcolor="#CCCCCC">
			<div align="left"><strong> Ley:</strong></div>
		</td>
		<td><span class="Estilo15"><?php if ($_POST['OSANCION'] > 0) {
										echo $ley1;
									} ?>
			</span></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC">
			<div align="left"><strong>Periodo Inicial:</strong></div>
		</td>
		<td><label>
				<div align="left">
					<input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" size="8" readonly="readonly" value="<?php echo $_POST['OINICIO']; ?>" />
				</div>
			</label></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC">
			<div align="left"><strong>Periodo Final:</strong></div>
		</td>
		<td><input onclick='javascript:scwShow(this,event);' type="text" name="OFIN" size="8" readonly="readonly" value="<?php echo $_POST['OFIN']; ?>" /></td>
	</tr>
</table>

<?php
//--------------------------
if ($aplicacion == 10 or $aplicacion == 12  or $aplicacion == 53 or $aplicacion == 153) {
?>
	<table width="60%" border="1" align="center">
		<tr>
			<td width="20%" bgcolor="#CCCCCC">
				<div align="left"><strong>Planilla:</strong></div>
			</td>
			<td><label>
					<div align="left"><input type="text" name="OPLANILLA" maxlength="15" size="12" value="<?php echo $_POST['OPLANILLA']; ?>" />
					</div>
				</label></td>
		</tr>
		<tr>
			<td width="20%" bgcolor="#CCCCCC">
				<div align="left"><strong>Monto BsS.:</strong></div>
			</td>
			<td>
				<div align="left"><label>
						<div align="left"><input type="text" name="OMONTO" maxlength="15" size="8" value="<?php echo $_POST['OMONTO']; ?>" />
						</div>
					</label></div>
				<div align="left"></div>
			</td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="left"><strong>Fecha Presentacion:</strong></div>
			</td>
			<td><label>
					<div align="left">
						<input onclick='javascript:scwShow(this,event);' type="text" name="OPRESENTACION" size="8" readonly value="<?php echo $_POST['OPRESENTACION']; ?>" />
					</div>
				</label></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="left"><strong>Fecha Vencimiento:</strong></div>
			</td>
			<td>
				<div align="left">
					<input onclick='javascript:scwShow(this,event);' type="text" name="OVENCIMIENTO" size="8" readonly value="<?php echo $_POST['OVENCIMIENTO']; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="left"><strong>Porcentaje a Aplicar:</strong></div>
			</td>
			<td>
				<div align="left"><label>
						<div align="left">
							<input type="text" name="OPORCENTAJE" maxlength="4" size="3" value="<?php echo $_POST['OPORCENTAJE']; ?>" />
						</div>
					</label></div>
			</td>
		</tr>
	</table>
<?php
}

//--------------------------	
if ($aplicacion == 2 or $aplicacion == 9 or $aplicacion == 13 or $aplicacion == 14 or $aplicacion == 15) {
?>
	<table width="60%" border="1" align="center">
		<tr>
			<?php if ($planilla > 0) { ?><td width="20%" bgcolor="#CCCCCC">
					<div align="left"><strong>Planilla:</strong></div>
				</td>
				<td><label>
						<div align="left"><input type="text" name="OPLANILLA" maxlength="15" size="12" value="<?php echo $_POST['OPLANILLA']; ?>" />
						</div>
					</label></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="left"><strong>Fecha Presentacion:</strong></div>
			</td>
			<td><label>
					<div align="left">
						<input onclick='javascript:scwShow(this,event);' type="text" name="OPRESENTACION" size="8" readonly value="<?php echo $_POST['OPRESENTACION']; ?>" />
					</div>
				</label></td>
		</tr>
		<tr><?php } ?>
		<td width="20%" bgcolor="#CCCCCC">
			<div align="left"><strong>Fecha Vencimiento:</strong></div>
		</td>
		<td>
			<div align="left">
				<input onclick='javascript:scwShow(this,event);' type="text" name="OVENCIMIENTO" size="8" readonly value="<?php echo $_POST['OVENCIMIENTO']; ?>" />
			</div>
		</td>
		</tr>
	</table>
<?php
}

//--------------------------	
if ($aplicacion == 3 or $aplicacion == 11 or $aplicacion == 18 or $aplicacion == 51 or $aplicacion == 151 or $aplicacion == 54 or $aplicacion == 154 or $aplicacion == 155) {
?>
	<table width="60%" border="1" align="center">
		<tr>
			<td width="20%" bgcolor="#CCCCCC">
				<div align="left"><strong>Planilla:</strong></div>
			</td>
			<td><label>
					<div align="left"><input type="text" name="OPLANILLA" maxlength="15" size="12" value="<?php echo $_POST['OPLANILLA']; ?>" />
					</div>
				</label></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="left"><strong>Monto BsS.:</strong> (Decimales con Punto (.)) </div>
			</td>
			<td><label>
					<div align="left">
						<input type="text" name="OMONTO" maxlength="15" size="8" value="<?php echo $_POST['OMONTO']; ?>" />
					</div>
				</label></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="left"><strong>Fecha Pago:</strong></div>
			</td>
			<td><label>
					<div align="left"><input onclick='javascript:scwShow(this,event);' type="text" name="OPAGO" size="8" readonly value="<?php echo $_POST['OPAGO']; ?>" />
					</div>
				</label></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="left"><strong>Fecha Vencimiento:</strong></div>
			</td>
			<td>
				<div align="left">
					<input onclick='javascript:scwShow(this,event);' type="text" name="OVENCIMIENTO" size="8" readonly value="<?php echo $_POST['OVENCIMIENTO']; ?>" />
				</div>
			</td>
		</tr>
	</table>
<?php
}
?>


<?php /*
	//--------------------------	alexander 7-08-2024
 if ($aplicacion==151 or $aplicacion==151)
	{
	?>
	<table width="60%" border="1" align="center"><tr>
	<td width="20%" bgcolor="#CCCCCC"><div align="left"><strong>Planilla:</strong></div></td>
	<td ><label><div align="left"><input type="text" name="OPLANILLA" maxlength="15" size="12" value="<?php echo $_POST['OPLANILLA']; ?>" />
	  </div>
	</label></td>
	</tr>
	<tr>
	<td bgcolor="#CCCCCC"><div align="left"><strong>Monto BsS.:</strong> (Decimales con Punto (.)) </div></td>
	<td ><label><div align="left">
	  <input type="text" name="OMONTO" maxlength="15" size="8" value="<?php echo $_POST['OMONTO']; ?>" />
	  </div>
	</label></td>
	</tr>
	<tr>
	<td bgcolor="#CCCCCC"><div align="left"><strong>Fecha Pago:</strong></div></td>
	<td ><label><div align="left"><input onclick='javascript:scwShow(this,event);' type="text" name="OPAGO" size="8" readonly value="<?php echo $_POST['OPAGO']; ?>" />
	  </div>
	</label></td>
	</tr>
	<tr>
	<td bgcolor="#CCCCCC"><div align="left"><strong>Fecha Vencimiento:</strong></div></td>
	  <td >
	    <div align="left">
	      <input onclick='javascript:scwShow(this,event);' type="text" name="OVENCIMIENTO" size="8" readonly value="<?php echo $_POST['OVENCIMIENTO']; ?>" />
        </div></td></tr></table>
	<?php 
	}	
	//-----------------fin alex */
?>


<?php
if ($aplicacion == 14 or $aplicacion == 114) {
	if (($sancion < 836 or $sancion >= 1550) and $sancion < 10000) {
?>
		<table width="60%" border="1" align="center">
			<tr>
				<td width="20%" bgcolor="#CCCCCC">
					<div align="left"><strong>Facturas o Comprabantes:</strong></div>
				</td>
				<td><label>
						<div align="left"><input type="text" name="OFACTURAS" maxlength="3" size="5" value="<?php echo $_POST['OFACTURAS']; ?>" />
						</div>
					</label></td>
			</tr>
		</table>
<?php
	}
}
?>
<?php

if ((substr($art_cot, 0, 3) >= 100 and substr($art_cot, 0, 3) <= 108) and $sancion > 10000) {
?>
	<table width="60%" border="1" align="center">
		<tr>
			<td width="20%" bgcolor="#CCCCCC">
				<div align="left"><strong>Especial</strong></div>
			</td>
			<td>
				<div align="left">
					<input type="checkbox" name="OESPECIAL" value="3" <?php if ($_POST['OESPECIAL'] == "3") {
																			echo 'checked="checked"';
																		}; ?> />
				</div>
			</td>
		</tr>
	</table>
<?php
}
?>
<p></p>
<?php
if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
	include "0_calculo_multas2.php";
}
?>
<table border="1" align="center">
	<tr>
		<td align="center" bgcolor="#FF0000" colspan="3"><span class="Estilo7"><u>Monto de la Sanci&oacute;n Aplicada</u></span></td>
	</tr>
	<?php if ($_SESSION['ADMINISTRADOR'] > 0) {
		echo ($FechaPago  - $FechaVen) / 86400  ?>

		<tr>
			<td align="left" bgcolor="#CCCCCC">
				<div align="left"><strong>Dias:</strong></div>
			</td>
			<td>
				<div align="right">Cant.</div>
			</td>
			<td>
				<div align="right"><?php if ($_POST['OSANCION'] > 0) {
										echo $Dias;
									} ?>
				</div>
			</td>
		</tr>
	<?php } ?>
	<tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong><?php if ($_POST['OSANCION'] < 60000) {
											echo 'U.T. Aplicadas:';
										} else {
											echo 'Veces aplicada la Moneda mas alta:';
										} ?></strong></td>
		<td>
			<label></label>
			<div align="right">Cant. </div>
		</td>
		<td width="20%">
			<label></label>
			<div align="right">
				<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
					echo number_format(doubleval($ut_aplicadas), 2, ',', '.');
				} ?>
			</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong>
				<?php if ($_POST['OSANCION'] < 60000) {
					echo ' U.T. de recargo por ser Contribuyente Especial:';
				} else {
					echo 'Veces aplicada la Moneda mas alta de recargo por ser Contribuyente Especial:';
				} ?>
			</strong></td>
		<td>
			<label></label>
			<div align="right">Cant. </div>
		</td>
		<td width="20%">
			<label></label>
			<div align="right">
				<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
					echo number_format(doubleval($ut_aplicadas * $_POST['OESPECIAL'] - $ut_aplicadas), 2, ',', '.');
				} ?>
			</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong>
				<?php if ($_POST['OSANCION'] < 60000) {
					echo 'Total U.T.:';
				} else {
					echo 'Total veces aplicada la Moneda mas alta:';
				} ?>
			</strong></td>
		<td>
			<strong>
				<label></label>
			</strong>
			<div align="right"><strong>Cant. </strong></div>
		</td>
		<td width="20%">
			<strong>
				<label></label>
			</strong>
			<div align="right"><strong>
					<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
						echo number_format(doubleval($ut_aplicadas * $_POST['OESPECIAL']), 2, ',', '.');
					} ?>
				</strong></div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF"><strong>
				<?php if ($_POST['OSANCION'] < 60000) {
					echo 'Valor U.T. (para el momento de la infracci&oacute;n):';
				} else {
					echo 'Moneda mas alta (para el momento de la infracci&oacute;n):';
				} ?>
			</strong></td>
		<td>
			<label></label>
			<div align="right">BsS. </div>
		</td>
		<td>
			<label></label>
			<div align="right">
				<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
					echo number_format(doubleval($_SESSION['VALOR_UT_PRIMITIVA']), 2, ',', '.');
				} ?>
			</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF"><strong>Multa (para el momento de la infracci&oacute;n):</strong></td>
		<td>
			<label></label>
			<div align="right">BsS. </div>
		</td>
		<td>
			<label></label>
			<div align="right">
				<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
					echo number_format(doubleval($ut_aplicadas * $_SESSION['VALOR_UT_PRIMITIVA']), 2, ',', '.');
				} ?>
			</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong>
				<?php if ($_POST['OSANCION'] < 60000) {
					echo 'Valor U.T. Actual:';
				} else {
					echo 'Valor Moneda mas alta:';
				} ?>
			</strong></td>
		<td>
			<strong>
				<label></label>
			</strong>
			<div align="right"><strong>BsS. </strong></div>
		</td>
		<td>
			<strong>
				<label></label>
			</strong>
			<div align="right"><strong>
					<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
						if ($_POST['OSANCION'] < 60000) {
							echo number_format(doubleval($_SESSION['VALOR_UT_ACTUAL']), 2, ',', '.');
						} else {
							echo number_format(doubleval(moneda_mas_alta()), 2, ',', '.');
						}
					} ?>
				</strong></div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong>Multa Actual:</strong></td>
		<td>
			<label></label>
			<div align="right">BsS. </div>
		</td>
		<td>
			<label></label>
			<div align="right">
				<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
					echo number_format(doubleval($monto), 2, ',', '.');
				} ?>
			</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong>Monto recargo por ser Contribuyente Especial:</strong></td>
		<td>
			<label></label>
			<div align="right">BsS. </div>
		</td>
		<td>
			<label></label>
			<div align="right">
				<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
					echo number_format(doubleval($monto * $_POST['OESPECIAL'] - $monto), 2, ',', '.');
				} ?>
			</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong>Total Multa Actual:</strong></td>
		<td>
			<strong>
				<label></label>
			</strong>
			<div align="right"><strong>BsS.</strong></div>
		</td>
		<td>
			<strong>
				<label></label>
			</strong>
			<div align="right"><strong>
					<?php if ($_POST['CMDCALCULAR'] == 'Calcular' | $_POST['CMDGUARDAR'] == 'Guardar') {
						echo number_format(doubleval($monto * $_POST['OESPECIAL']), 2, ',', '.');
					} ?>
				</strong></div>
		</td>
	</tr>
</table>
<table align="center">
	<tr>
		<td><?php if ($_POST['OSANCION'] > 0) {	?> <p align="center"><input type="submit" class="boton" name="CMDCALCULAR" value="Calcular"></p> <?php } ?>
		</td>
		<td><?php if ($monto > 0) { 	?> <p align="center"><input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></p> <?php	} ?></td>
	</tr>
</table>