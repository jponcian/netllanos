				<?php
					Global $valor;
				?>
				<div id="siger">
					<button class="css_button" id="generar_siger">
										<?php 
						echo $valor+1 ." - "; 
					?> Generar Siger</button>
					<button class="css_button" id="mod_acta">
										<?php 
						echo $valor+2 ." - "; 
					?> Casos en Proceso (Año Actual)</button>
					<button class="css_button" id="rimp_acta">
										<?php 
						echo $valor+3 ." - "; 
					?> Casos en Proceso (Años Anteriores)</button>
					<button class="css_button" id="cs_acta">
					<?php 
						echo $valor+4 ." - "; 
					?> Consultas </button>					
				</div>