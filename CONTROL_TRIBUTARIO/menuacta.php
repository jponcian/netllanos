				<?php
					Global $valor;
					$valor = 1;
				?>
				<div id="facturas">
					<button class="css_button" id="inc_acta">
										<?php 
						echo $valor++ ." - "; 
					?> Incluir Acta</button>
					<!--
					<button class="css_button" id="mod_acta">
										<?php 
						//echo $valor++ ." - "; 
					?> Modificar Acta</button>
					-->
					<button class="css_button" id="rimp_acta">
										<?php 
						echo $valor++ ." - "; 
					?> Reimprimir Acta</button>
					<button class="css_button" id="cs_acta">
					<?php 
						echo $valor++ ." - "; 
					?> Consultas </button>					
				</div>
 