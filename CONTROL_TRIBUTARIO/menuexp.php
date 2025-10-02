				<?php
					Global $valor;
					$valor = 1;
				?>
				<div id="expedientes" style="display:none">
					<button class="css_button" id="inc_exp">
										<?php 
						echo $valor++ ." - "; 
					?> Memorando de Salida de Expediente</button>
										<?php 
					if ($_SESSION['ADMINISTRADOR'] > 0)
					{?>
					<button class="css_button" id="mod_exp">
										<?php 
						echo $valor++ ." - "; 
					?> Modificar Memorando</button>
					<?php
					}?>
						<button class="css_button" id="rimp_exp">
					<?php 
						echo $valor++ ." - ";
					?> Reimprimir Memorando</button>
					<button class="css_button" id="cs_exp">
					<?php 
						echo $valor++ ." - "; 
					?> Consultas </button>					
				</div>
 