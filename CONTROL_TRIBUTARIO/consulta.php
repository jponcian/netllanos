				<div id="consulta">
					<form class="contact_form" action="#" id="formconsultaAct" > 
						<div> 
							<ul> 
								<li> 
									<h2>Consulta Actas de Destrucción de Documentos</h2> 
									<span class="required_notification">* Datos requeridos</span> 
								</li> 
								<li>
									<label for="chkprovidencia">Dependencia:</label>
									<?php include "../conexion.php";
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
									<label for="chkacta">Acta:</label> 
									<input name="chkacta" type="checkbox" id="chkacta" checked="checked"/> 
								</li>
								<li> 
									<label for="añoacta">Año Acta:</label> 
									<input type="text" id="añoacta" value="" />
								</li> 
								<li> 
									<label for="numacta">Nro. Acta:</label> 
									<input type="text" id="numacta" value="" />
								</li> 
								<li> 
									<label for="chkfecha">Fecha:</label> 
									<input type="checkbox" name="chkfecha" id="chkfecha"/> 
								</li> 
								<li> 
									<label for="fechainicio">Fecha Inicial:</label> 
									<input type="text" id="fecha_inicio" readonly /> 
								</li> 
								<li> 
									<label for="fechafinal">Fecha Final:</label> 
									<input type="text" id="fecha_final" readonly /> 
								</li>
								<li> 
									<label for="chkrif">Consulta por Rif:</label> 
									<input type="checkbox" name="chkrif_A" id="chkrif_A"/> 
								</li> 
								<li> 
									<label for="numrif">Nro. de Rif:</label> 
									<input type="text" id="numrif" value="" />
								</li> 
								<div id="botonera" align="center">
								<li> 
									<button class="submit" type="button" id="btnconsulta">Cargar Consulta</button> 
									<p align="center" id="txtResult_Consulta" style="color: red;"></p> 
								</li> 
								</div>
								<div id="detalleconsulta_f"></div>
							</ul> 
						</div> 
					</form>
				</div>