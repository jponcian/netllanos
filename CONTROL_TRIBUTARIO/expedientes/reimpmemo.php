<div id="formimpmemorando">
	<form class="contact_form" action="#" id="reimpmemorando" > 
		<div> 
			<ul> 
				<li> 
					<h2>Reimprimir Memorando</h2> 
					<span class="required_notification">* Datos requeridos</span> 
				</li> 
				<li> 
					<label for="acta">Nro de Memorando:</label> 
					<input type="text" id="nummemoimp" />
				</li> 
				<li> 
					<label for="tiposol">Año del Memorando:</label> 
					<select name="añomemoimp" id="añomemoimp">
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
					<button class="submit" type="button" id="cargar_memo">Cargar Memorando</button> 
					<button class="submit" type="button" id="reimp_memo" disabled>Imprimir Memorando</button>
					<p align="center" id="txtResultadoimpMemo" style="color: red;"></p>
					<input type="hidden" name="idmemoimp" id="idmemoimp">
					<input type="hidden" name="reimpnum" id="reimpnum">
					<input type="hidden" name="reimpaño" id="reimpaño">
				</li> 
				</div>
			</ul> 
		</div> 
	</form>
</div>