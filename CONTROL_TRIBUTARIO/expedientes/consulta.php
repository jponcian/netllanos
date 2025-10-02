				<div id="consulta">
					<form class="contact_form" action="#" id="formconsultaExp" > 
						<div> 
							<ul> 
								<li> 
									<h2>Consulta Salida de Expedientes División de Fiscalización</h2> 
									<span class="required_notification">* Datos requeridos</span> 
								</li> 
								<li>
									<label for="chkprovidencia">Dependencia:</label>
									<?php 
									include "../conexion.php";
									//include "../auxiliar.php";
 									?>
									<select name="sede" id="sede" size="1">
									                  <option value="0">Gerencia (Todos)</option>
									                  <?php
									if ($_SESSION['ADMINISTRADOR'] > 0) 
										{ 
										$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;'; 
										$tabla_x = mysql_query ($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x))
											{
											echo '<option '; if ($_POST['OSEDE']==$registro_x['id_sector']) {echo 'selected="selected" ';}
											echo ' value='.$registro_x['id_sector'].'>'.$registro_x['nombre'].'</option>';
											}
										}
									else
										{
										$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector='.$_SESSION['SEDE_USUARIO'].';'; 
										$tabla_x = mysql_query ($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x))
											{
											echo '<option '; if ($_POST['OSEDE']==$registro_x['id_sector']) {echo 'selected="selected" ';}
											echo ' value='.$registro_x['id_sector'].'>'.$registro_x['nombre'].'</option>';
											}
										}
									?>
											</select>
								</li>
								<li>
									<label for="chkprovidencia">Providencia:</label> 
									<input name="chkprovidencia" type="checkbox" id="chkprovidencia" checked="checked"/> 
								</li>
								<li> 
									<label for="añoprov">Año Providencia:</label> 
									<input type="text" id="añoprov" value="" />
								</li> 
								<li> 
									<label for="numprov">Nro. Providencia:</label> 
									<input type="text" id="numprov" value="" />
								</li> 
								<li> 
									<label for="chkmemorando">Memorando:</label> 
									<input type="checkbox" name="chkmemorando" id="chkmemorando"/> 
								</li> 
								<li> 
									<label for="añomemo">Año del Memorando:</label> 
									<input type="text" id="añomemo" value="" />
								</li> 
								<li> 
									<label for="nummemo">Nro. Memorando:</label> 
									<input type="text" id="nummemo" value="" />
								</li> 
								<li> 
									<label for="chkdestino">Destino:</label> 
									<input type="checkbox" name="chkdestino" id="chkdestino"/> 
								</li> 
								<li> 
									<label for="division">División Destino:</label> 
									<select name="division" id="division">
										<option value="TRAMITACION">TRAMITACION</option>
										<option value="RECAUDACION">RECAUDACION</option>
										<option value="SUMARIO">SUMARIO</option>
										<option value="ESPECIALES">ESPECIALES</option>
										
									</select>
								</li>
								<li> 
									<label for="fechainicio">Fecha Inicial:</label> 
									<input type="text" id="fechainicio" readonly /> 
								</li> 
								<li> 
									<label for="fechafinal">Fecha Final:</label> 
									<input type="text" id="fechafinal" readonly /> 
								</li>
								<li> 
									<label for="chkrif">Consulta por Rif:</label> 
									<input type="checkbox" name="chkrif" id="chkrif"/> 
								</li> 
								<li> 
									<label for="numrif">Nro. de Rif:</label> 
									<input type="text" name="num_Rif" id="num_Rif" value="" />
								</li> 
								<div id="botonera" align="center">
								<li> 
									<button class="submit" type="button" id="btn_consulta">Cargar Consulta</button> 
									<p align="center" id="txtResultadoConsulta" style="color: red;"></p> 
								</li> 
								</div>
								<div id="detalleconsulta"></div>
							</ul> 
						</div> 
					</form>
				</div>