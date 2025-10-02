<?php
	include "conexion.php";
?>

				<div id="formexp">
					<form class="contact_form" action="#" id="destfactura" > 
						<div> 
							<ul> 
								<li> 
									<h2>Destrucción o Inutilización de Facturas y Otros Documentos</h2> 
									<span class="required_notification">* Datos requeridos</span> 
								</li> 
								<li> 
									<label for="acta">Numero de Acta:</label> 
									<?php
										$año = date("Y");
										$sector = $_SESSION['SEDE_USUARIO'];
										$registros = "SELECT max(numero_acta) AS numero, year(fecha_emision) AS anno FROM ct_destruccion_facturas WHERE sector=".$sector." AND year(fecha_emision)=".$año." GROUP BY sector, year(fecha_emision)";
										$resultregistros = $conexionsql->query($registros);
										if ($valor = $resultregistros->fetch_object())
										{
											$numero_acta = $valor->numero + 1;
										} else {
											$numero_acta = 1;
										}
									?> 
									<input type="text" id="acta" readonly value="<?php echo $numero_acta ?>" />
									<input type="hidden" id="sector" value="<?php echo $_SESSION['SEDE_USUARIO']; ?>" />
								</li> 
								<li> 
									<label for="rif">Rif Contribuyente:</label> 
									<input type="text" id="rif" placeholder="J000000000" required /> 
									<span class="form_hint">Formato correcto: "V000000000"</span> 
								</li> 
								<li> 
									<label for="nombresujeto">Sujeto Pasivo:</label> 
									<input type="text" id="nombresujeto" readonly="readonly" /> 
								</li> 
								<li> 
									<label for="persona">Persona que Asiste:</label> 
									<select name="persona" id="persona">
										<option value="0">REPRESENTANTE LEGAL</option>
										<option value="1">PERSONA AUTORIZADA</option>
									</select>
								</li> 
								<li> 
									<label for="cedula">Cedula de Identidad:</label> 
									<input type="text" id="cedula" placeholder="12345678" required /> 
									<span class="form_hint">Formato correcto: "12345678"</span> 
								</li> 
								<li> 
									<label for="nombrerp">Nombre y Apellido:</label> 
									<input type="text" id="nombrerp" placeholder="NOMBRE APELLIDO" required /> 
								</li> 
								<li> 
									<label for="numsolicitud">Numero de Solicitud:</label> 
									<input type="text" id="numsolicitud" placeholder="1234567890" required /> 
									<span class="form_hint">Formato correcto: "1234567890"</span> 
								</li> 
								<li> 
									<label for="fechasol">Fecha Solicitud:</label> 
									<input type="text" id="fechasol" placeholder="dd/MM/AAAA" required /> 
									<span class="form_hint">Formato correcto: "2015/01/01"</span> 
								</li> 
								<li> 
									<label for="tiposol">Tipo de Solicitud:</label> 
									<select name="tiposol" id="tiposol">
										<?php
										$consulta = "SELECT * FROM ct_tipo_solicituddest";
										$resultadotipo = $conexionsql->query($consulta);
										
										while ($tipos = $resultadotipo->fetch_object()) 
										{
										?>
											<option value="<?php echo $tipos->descripcion ?>"><?php echo $tipos->descripcion ?></option>
										<?php
										}
										?>
									</select>
								</li>
								<div id="documentosregistrodoc" style="margin-top: 5px;">
									<table width="90%" border="0" align="center" bgcolor="#999999">
									  <tr bgcolor="#333333">
									    <td align="center" style="color: #FFF">Tipo de Documento</td>
									    <td align="center" style="color: #FFF">Numero de Control Inicial</td>
									    <td align="center" style="color: #FFF">Numero de Control Final</td>
									    <td align="center" style="color: #FFF"></td>
									  </tr>
									  <tr>
									    <td align="center">
												<select name="txtTIPODOC" id="tipodoc" size="1">
													<?php
													$consulta = "SELECT * FROM ct_tipo_docdestfacturas";
													$resultadotipo = $conexionsql->query($consulta);

													while ($tipos = $resultadotipo->fetch_object()) 
													{?>
														<option value="<?php echo $tipos->codigo ?>"><?php echo $tipos->descripcion ?></option>
													<?php
													}
													?>
									    		</select>
									    </td>
									    <td align="center">
									    	<input name="txtNUMINICIO" type="text" id="txtNUMINICIO" maxlength="8" value="" style="text-align: center;">
									    </td>
									    <td align="center">
										    <input name="txtNUMFINAL" type="text" id="txtNUMFINAL" maxlength="8" value="" style="text-align: center;">
										</td>
										<td>
										    <button class="botonagregar" type="button" name="btnAGREGARDOC" id="btnAGREGARDOC" >Agregar</button>
										</td>
									  </tr>
									</table>
									<div id="registrodoc" style="margin-top: 5px;">
									</div>
								</div> 
								<div id="botonera" align="center">
								<li> 
									<button class="submit" type="button" id="genera_acta">Generar Acta</button> 
									<button class="submit" type="button" id="nueva_acta">Incluir Nueva Acta</button> 
									<button class="submit" type="button" id="imp_acta">Imprimir Acta</button>
									<p align="center" id="txtResultado" style="color: red;"></p> 
								</li> 
								</div>
							</ul> 
						</div> 
					</form>
				</div>