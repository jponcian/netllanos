<div id="formmodmemorando">
	<form class="contact_form" action="#" id="modmemorando" > 
		<div> 
			<ul> 
				<li> 
					<h2>Modificar Memorando</h2> 
					<span class="required_notification">* Datos requeridos</span> 
				</li> 
				<li> 
					<label for="acta">Nro de Memorando:</label> 
					<input type="text" id="nummemomod" />
				</li> 
				<li> 
					<label for="tiposol">Año del Memorando:</label> 
					<select name="añomemomod" id="añomemomod">
						<?php
						$actual = date("Y");
						$año = $actual - 5;
						
						for ($año ; $año <= $actual; $año++)
						{
							if ($año == $actual){
								$select = 'selected';
							} else {
								$select = '';
							}
						?>
							<option value="<?php echo $año ?>" <?php echo $select ?>><?php echo $año ?></option>
						<?php
						}
						?>
					</select>
				</li>
				<div id="botonera" align="center">
				<li> 
					<button class="submit" type="button" id="precargar_memo">Cargar Memorando</button> 
					<button class="submit" type="button" id="mod_memo" disabled>Modificar Memorando</button>
					<p align="center" id="txtResultadoimpModMemo" style="color: red;"></p>
					<input type="hidden" name="idmemomod" id="idmemomod">
					<input type="hidden" name="modmpnum" id="modmpnum">
					<input type="hidden" name="modpaño" id="modpaño">
					<input type="hidden" name="valorid" id="valorid">
				</li> 
				</div>
			</ul> 
		</div> 
	</form>
</div>
<div id="cargarmodificacion"></div>