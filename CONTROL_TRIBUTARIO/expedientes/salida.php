<?php
	include "../conexion.php";
//include "../auxiliar.php";

?>

				<div id="formsalida">
					<form class="contact_form" action="#" id="salidaExpediente" > 
						<div> 
							<ul> 
								<li> 
									<h2>Salida de Expedientes Division de Fiscalización</h2> 
									<span class="required_notification">* Datos requeridos</span> 
								</li> 
								<li> 
									<label for="memo">N° de Memorando:</label> 
									<?php	
										$año = date("Y");
										$hoy = date("d/m/Y");
										$sector = $_SESSION['SEDE_USUARIO'];
										$registros = "SELECT max(NroMemo) AS numero, Anno_memo AS anno FROM ct_salida_expediente WHERE sector=".$sector." AND Anno_memo=year(date(now())) GROUP BY sector, Anno_memo";
										$resultregistros = $conexionsql->query($registros);
										$cantidad = $resultregistros->num_rows;
										if ($cantidad > 0)
										{
											$valor = $resultregistros->fetch_object();
											$numero_memo = $valor->numero + 1;
										} else {
											$numero_memo = 1;
										}
									?> 
									<input type="text" id="memo" readonly value="<?php echo $numero_memo ?>" />
								</li> 
								<li> 
									<label for="fechamemo">Fecha Memorando:</label> 
									<input type="text" id="fechamemo" readonly value="<?php echo $hoy ?>" /> 
									<!--<span class="form_hint">Formato correcto: "2015/01/01"</span> -->
								</li> 
								<li> 
									<label for="tipoexp">Tipo de Expediente:</label> 
									<select name="tipoexp" id="tipoexp">
									  <?php
											if ($_POST["tipoexp"]=="")
											{
												echo '<option selected="selected" value=""></option>';
											}
											else
											{
												echo '<option value=""></option>';	
											}
											if ($_POST["tipoexp"]=="VDF")
											{
											  echo '<option selected="selected" value="VDF">VDF</option>';
											}
											else
											{
											  echo '<option value="VDF">VDF</option>';
											}
											if ($_POST["tipoexp"]=="Sucesiones")
											{
											  echo '<option selected="selected" value="Sucesiones">Sucesiones</option>';
											}
											else
											{
											  echo '<option value="Sucesiones">Sucesiones</option>';
											}
											if ($_POST["tipoexp"]=="Investigaciones")
											{
											  echo '<option selected="selected" value="Investigaciones">Investigaciones</option>';
											}
											else
											{
											  echo '<option value="Investigaciones">Investigaciones</option>';
											}
                                        ?>
									</select>
								</li>
								<li> 
									<label for="fiscapuntual">Fisc. Puntuales:</label> 
									<input type="checkbox" name="fiscapuntual" id="fiscapuntual"/> 
								</li>
								<li> 
									<label for="fpretencion">FP - Retenciones:</label> 
									<input type="checkbox" name="fpretencion" id="fpretencion" disabled="disabled"/> 
								</li> 
								<li> 
									<label for="fpretencion">FI - Integral:</label> 
									<input type="checkbox" name="fiintegral" id="fiintegral"/> 
								</li> 
								<li> 
									<label for="resultado">Resultado:</label> 
									<select name="resultado" id="resultado">
									</select>
								</li>
								<li> 
									<label for="especial">Cont. Especiales:</label> 
									<input type="checkbox" name="especial" id="especial"/> 
								</li> 
								<li> 
									<label for="destino">Destino:</label> 
									<input type="text" name="destino" id="destino" readonly />
									<input type="hidden" name="destino2" id="destino2" /> 
									<input type="hidden" name="destino3" id="destino3" /> 
								</li>								
								<li> 
									<label for="notificacion">Enviar a Notificación:</label>
									<input type="checkbox" name="chknoti" id="chknoti" /> 
								</li>
								<li> 
									<!--<label for="plazo25">Fin plazo 25 dias:</label>--> 
									<input type="checkbox" name="chkplazo" id="chkplazo" disabled="disabled" /> 
									<label for="plazo25">Remitir a Cobro</label>
									<select name="plazo25" id="plazo25" disabled="disabled" style="display: none;">
										<option value=""></option>
										<option value="Totalmente">Totalmente Pagado</option>
										<option value="Parcialmente">Parcialmente Pagado</option>
										<option selected="selected" value="No">No Pagado</option>
									</select>
								</li>
								<li> 
									<label for="clausura">Clausurado:</label> 
									<select name="clausura" id="clausura">
										<option value="No">No</option>
										<option value="Si">Sí</option>
									</select>
								</li>
								<div id="documentosregistrodoc" style="margin-top: 5px;">
									<table width="90%" border="0" align="center" bgcolor="#999999">
									  <tr bgcolor="#333333">
									    <td align="center" style="color: #FFF">Año Providencia</td>
									    <td align="center" style="color: #FFF">Numero Providencia</td>
									    <td align="center" style="color: #FFF">Folio</td>
									    <td align="center" style="color: #FFF"></td>
									  </tr>
									  <tr>
									    <td align="center">
									    	<input name="añoprovidencia" type="text" id="añoprovidencia" maxlength="4" value="" style="text-align: center;">
									    </td>
									    <td align="center">
									    	<input name="numprovidencia" type="text" id="numprovidencia" maxlength="4" value="" style="text-align: center;">
									    </td>
									    <td align="center">
									    	<input name="folio" type="text" id="folio" maxlength="4" value="0" style="text-align: center;">
									    </td>
									    <td align="center">
										    <button class="botonagregar" type="button" name="btnAGREGAREXP" id="btnAGREGAREXP" >Agregar</button>
										</td>
									  </tr>
									</table>
									<div id="cargando" style="display:none; width:100%;text-align: center;"><p align="center"><img src="images/loader.gif"></p></div>
									<div id="registroexp" style="margin-top: 5px;">
									</div>
								</div> 
								<div id="botonera" align="center">
								<li> 
									<button class="submit" type="button" id="genera_memo">Generar Memorando</button> 
									<button class="submit" type="button" id="nuevo_memo" disabled>Incluir Nuevo Memorando</button> 
									<button class="submit" type="button" id="imp_memo" disabled>Imprimir Memorando</button>
									<p align="center" id="txtResultadoExp" style="color: red;"></p> 
								</li> 
                                <input type="hidden" name="estatus" id="estatus" value="" />
                                <input type="hidden" name="imprimir" id="imprimir" value="0" />
								</div>
							</ul> 
						</div> 
					</form>
				</div>