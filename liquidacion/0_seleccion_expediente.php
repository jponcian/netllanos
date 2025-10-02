<table width="47%" border="1" align="center">
	<tr>
		<td height="35" align="center" bgcolor="#dc3545" colspan="8"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC">
			<div align="center"><strong>Dependencia:</strong></div>
		</td>
		<td bgcolor="#FFFFFF">
			<div align="center"><span class="">
					<select id="OSEDE" name="OSEDE" onChange="cargar_combo1(this.value);">
						<option value="-1">Seleccione</option>
						<?php


						if ($_SESSION['ADMINISTRADOR'] == 1 or $_SESSION['SEDE_USUARIO'] == 1) {
							$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' GROUP BY sector';
						} else {
							// --- VALIDACION DE LA SEDE DEL USUARIO
							if ($_SESSION['SEDE_USUARIO'] <> 0) {
								$sede = 'and sector=' . $_SESSION['SEDE_USUARIO'];
							} else {
								$sede = '';
							}
							// -------------------------------------
							$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' ' . $sede . ' ' . $origen . ' GROUP BY sector;';
						}
						//---------------------
						$tabla_x = mysql_query($consulta_x);
						while ($registro_x = mysql_fetch_array($tabla_x)) {
							echo '<option ';
							if ($_POST['OSEDE'] == $registro_x['sector']) {
								echo 'selected="selected" ';
							}
							echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
						}
						?>
					</select>
				</span></div>
		</td>
		<td bgcolor="#CCCCCC">
			<div align="center"><strong>Origen:</strong></div>
		</td>
		<td bgcolor="#FFFFFF">
			<div align="center"><span class="">
					<select id="OORIGEN" name="OORIGEN" onChange="cargar_combo2(this.value);">
						<option value="-1">Seleccione</option>
						<?php
						if ($_POST['OSEDE'] > 0) {
							$consulta_x = 'SELECT origen_liquidacion, area FROM vista_sanciones_aplicadas WHERE origen_liquidacion>0 AND status>=' . $status . ' AND status<=' . $status2 . ' AND sector=0' . $_POST['OSEDE'] . ' AND origen_liquidacion IN ' . $origenes . ' GROUP BY area';
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OORIGEN'] == $registro_x['origen_liquidacion']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['origen_liquidacion'] . '>' . $consulta_x . $registro_x['area'] . '</option>';
							}
						}
						?>
					</select>
				</span></div>
		</td>
		<td bgcolor="#CCCCCC">
			<div align="center"><strong>A&ntilde;o:</strong></div>
		</td>
		<td bgcolor="#FFFFFF">
			<div align="center"><span class="">
					<select id="OANNO" name="OANNO" onChange="cargar_combo3(this.value);">
						<option value="-1">Seleccione</option>
						<?php
						if ($_POST['OORIGEN'] > 0) {
							$consulta_x = 'SELECT anno_expediente FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND origen_liquidacion=' . $_POST['OORIGEN'] . ' AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno_expediente ORDER BY anno_expediente DESC';
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OANNO'] == $registro_x['anno_expediente']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['anno_expediente'] . '>' . $registro_x['anno_expediente'] . '</option>';
							}
						}
						?>
					</select>
				</span></div>
		</td>
		<td width="15%" bgcolor="#CCCCCC">
			<div align="center"><strong>Numero:</strong></div>
		</td>
		<td width="36%">
			<div align="center">
				<select id="ONUMERO" name="ONUMERO" size="1" onchange="cargar(this.value);">
					<option value="-1">Seleccione</option>
					<?php
					if ($_POST['OANNO'] > 0) {
						$consulta_x = 'SELECT num_expediente FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND anno_expediente=' . $_POST['OANNO'] . ' AND origen_liquidacion=' . $_POST['OORIGEN'] . ' AND sector=0' . $_POST['OSEDE'] . ' GROUP BY num_expediente ORDER BY num_expediente DESC';
						$tabla_x = mysql_query($consulta_x);
						while ($registro_x = mysql_fetch_array($tabla_x)) {
							echo '<option ';
							if ($_POST['ONUMERO'] == $registro_x['num_expediente']) {
								echo 'selected="selected" ';
							}
							echo ' value=' . $registro_x['num_expediente'] . '>' . $registro_x['num_expediente'] . '</option>';
						}
					}
					?>
				</select>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="8" align="center">
			<p>&nbsp;</p>
		</td>
	</tr>
</table>