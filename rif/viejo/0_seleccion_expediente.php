<form name="form1" method="post">
	<p>&nbsp;</p>
	<table width="47%" border="1" align="center">
		<tr>
			<td height="35" align="center" bgcolor="#FF0000" colspan="6"><span class="Estilo7"><u>Selecci�n del Expediente a Sancionar</u></span></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC">
				<div align="center"><strong>Dependencia:</strong></div>
			</td>
			<td bgcolor="#FFFFFF">
				<div align="center"><span class="Estilo1">
						<select name="OSEDE" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							if ($_SESSION['ADMINISTRADOR'] > 0) {
								$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_especiales GROUP BY sector;';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['id_sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
							} else {
								$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_especiales WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['id_sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
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
				<div align="center"><span class="Estilo1">
						<select name="OANNO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							if ($_POST['OSEDE'] > 0) {
								$consulta_x = 'SELECT anno FROM vista_exp_especiales WHERE sector=0' . $_POST['OSEDE'] . ' GROUP BY anno;';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									echo '<option ';
									if ($_POST['OANNO'] == $registro_x['anno']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
								}
							}
							?>
						</select>
					</span></div>
			</td>
			<td width="15%" bgcolor="#CCCCCC">
				<div align="center"><strong>Numero:</strong></div>
			</td>
			<td width="36%"><label>
					<div align="center"><span class="Estilo1">
							<select name="ONUMERO" size="1">
								<option value="-1">Seleccione</option>
								<?php
								if ($_POST['OANNO'] > 0) {
									$consulta_x = 'SELECT numero FROM vista_exp_especiales WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ';';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option ';
										if ($_POST['ONUMERO'] == $registro_x['numero']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
									}
								}
								?>
							</select>
						</span>
						<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
					</div>
				</label></td>
		</tr>
		<tr>
			<td colspan="6" align="center">
				<p>
					<?php include "../msg_validacion.php"; ?></p>
			</td>
		</tr>
	</table>

	<p>&nbsp;</p>
</form>