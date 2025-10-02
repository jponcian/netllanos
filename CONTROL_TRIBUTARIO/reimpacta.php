<?php
	include "conexion.php";
?>

<div id="formimpacta">
	<form class="contact_form" action="#" id="reimpacta" > 
		<div> 
			<ul> 
				<li> 
					<h2>Reimprimir Acta Destrucción o Inutilización</h2> 
					<span class="required_notification">* Datos requeridos</span> 
				</li> 
				<li> 
					<label for="acta">Numero de Acta:</label> 
					<input type="text" id="numactaimp" />
				</li> 
				<li> 
					<label for="tiposol">Año del Acta:</label> 
					<select name="añoactaimp" id="añoactaimp">
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
					<button class="submit" type="button" id="cargar_acta">Cargar Acta</button> 
					<button class="submit" type="button" id="reimp_acta">Imprimir Acta</button>
					<p align="center" id="txtResultadoimp" style="color: red;"></p>
					<input type="hidden" name="idactaimp" id="idactaimp">
					<input type="hidden" name="cargoimp" id="cargoimp">
					<input type="hidden" id="reimp_sector" value="<?php echo $_SESSION['SEDE_USUARIO']; ?>" />
				</li> 
				</div>
			</ul> 
		</div> 
	</form>
</div>