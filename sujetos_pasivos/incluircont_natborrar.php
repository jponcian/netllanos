<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

if ($_SESSION['VARIABLE1'] == 'MODIFICAR') {
	$consulta = "SELECT * FROM contribuyentes WHERE Rif = '" . $_SESSION['RIF'] . "'";
	$tabla = mysql_query($consulta);
	if ($registro_contribuyente = mysql_fetch_object($tabla)) {
		$_POST[OCONTRIBUYENTE] = $registro_contribuyente->contribuyente;
		$_POST[OESTADO] = $registro_contribuyente->id_estado;
		$_POST[OCIUDAD] = $registro_contribuyente->id_ciudad;
		$_POST[OURBANIZACION] = $registro_contribuyente->id_zona;
		//$_POST[OTIPOCALLE]=$registro_contribuyente->id_tipo_calle;
		//$_POST[OCALLE]=$registro_contribuyente->id_calle;
		$_POST[OACTIVIDAD] = $registro_contribuyente->actividad;
		$_POST[ODOMICILIO] = $registro_contribuyente->DescripcionDomicilio;
		$_POST['opt_esp'] = $registro_contribuyente->Especial;
		if ($registro_contribuyente->Especial == 1) {
			$especial = 1;
			$_POST['OFECHAE'] = voltea_fecha($registro_contribuyente->fechaespecial);
		} else {
			$especial = 0;
		}
		if ($registro_contribuyente->fechaerevocado <> Null) {
			$_POST['opt_esp'] = 0;
			$revocado = 1;
			$_POST['OFECHAR'] = voltea_fecha($registro_contribuyente->fechaespecial);
		} else {
			$revocado = 0;
		}
		$_SESSION['VARIABLE1'] = "";
	}
}

if ($_POST['CMDGUARDAR'] == "Guardar") {
	if ($_POST[OESTADO] > 0 and $_POST[OCIUDAD] > 0 and $_POST[OURBANIZACION] > 0 and $_POST[ODOMICILIO] <> "" and $_POST[OCONTRIBUYENTE] <> "" and $_POST[OACTIVIDAD] > 0) {
		// PARA ELIMINAR EL CONTRIBUYENTE
		$consulta = "DELETE FROM contribuyentes WHERE rif='" . $_SESSION['RIF'] . "'";
		$tabla = mysql_query($consulta);

		if ($_POST['opt_esp'] == 1) {
			$campo_especial = 1;
			$campo_fecha_especial = voltea_fecha($_POST['OFECHAE']);
			$campos_a_guardar = ',Especial,fechaespecial';
			$valores_a_guardar = "," . $campo_especial . ",'" . $campo_fecha_especial . "'";
		} elseif ($_POST['opt_esp'] == 0 and $_POST['OFECHAR'] <> "") {
			$campo_especial = 0;
			$campo_fecha_revocado = voltea_fecha($_POST['OFECHAR']);
			$campos_a_guardar = ',Especial,fechaerevocado';
			$valores_a_guardar = "," . $campo_especial . ",'" . $campo_fecha_revocado . "'";
		} else {
			$campo_especial = 0;
			$campo_fecha_especial = Null;
			if ($revocado == 1) {
				$campo_fecha_revocado = voltea_fecha($_POST['OFECHAR']);
				$campos_a_guardar = ',Especial,fechaerevocado';
				$valores_a_guardar = "," . $campo_especial . ",'" . $campo_fecha_revocado . "'";
			} else {
				$campo_fecha_revocado = Null;
				$campo_fecha_revocado = Null;
				$campos_a_guardar = ',Especial';
				$valores_a_guardar = "," . $campo_especial . "";
			}
		}

		$consulta = "INSERT INTO contribuyentes (rif,contribuyente,id_estado,id_ciudad,id_zona,DescripcionDomicilio,actividad,CedulaTranscriptor,FechaActualizacion" . $campos_a_guardar . ") VALUES ('" . strtoupper($_SESSION['RIF']) . "','" . strtoupper($_POST[OCONTRIBUYENTE]) . "'," . $_POST[OESTADO] . "," . $_POST[OCIUDAD] . "," . $_POST[OURBANIZACION] . ",'" . strtoupper($_POST[ODOMICILIO]) . "'," . $_POST[OACTIVIDAD] . ",'" . $_SESSION['CEDULA_USUARIO'] . "','" . date("Y/m/d") . "'" . $valores_a_guardar . ")";
		//echo $consulta;

		$tabla = mysql_query($consulta);

		// MENSAJE DE GUARDADO
		header("Location: incluircontribuyente.php?errorusuario=si");
		exit();
	} else {
		// MENSAJE DE CAMPOS VACIOS
		echo '<table width="75%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>EXISTEN CAMPOS VACIOS!!!</strong> </div></td> </tr>  </table>';
	}
}
?>


<html>

<head>
	<title>Incluir Contribuyente Natural</title>

	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>
	<style type="text/css">
		<!--
		.Estilomenun {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight: bold;
		}

		body {
			background-image: url();
		}

		.Estilo7 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}

		.Estilo15 {
			font-size: 14px;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body style="background: transparent !important;">


	<p>

		<?php include "../titulo.php"; ?>
	</p>
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post" action="">
		<table width="700" border="1" align="center">

			<tr>

				<td height="27" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>USO SUJETOS PASIVOS ESPECIALES</u></span></td>
			</tr>

			<tr>
				<td width="30%" bgcolor="#CCCCCC" td align="center" valign="middle"><strong>Tipo:</strong></td>
				<td width="30%" bgcolor="#CCCCCC" td align="center" valign="middle"><strong>Fecha Especial:</strong></td>
				<td width="30%" bgcolor="#CCCCCC" td align="center" valign="middle"><strong>Fecha Revocado:</strong></td>
			</tr>
			<tr>
				<td align="center">
					<input name="opt_esp" type="radio" value="0" <?php if ($_POST['opt_esp'] == 0) {
																		echo checked;
																	} ?> onClick="<?php if ($especial == 1) { ?> OFECHAR.disabled = false; OFECHAE.disabled = true <?php } ?>"><label> Ordinario</label>
					<input name="opt_esp" type="radio" value="1" <?php if ($_POST['opt_esp'] == 1) {
																		echo checked;
																	} ?> onClick="OFECHAE.disabled = false; OFECHAR.disabled = true"><label> Especial</label>
				</td>
				<td align="center">
					<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAE" size="10" readonly value="<?php if ($_POST['OFECHAE'] <> "") {
																																echo $_POST['OFECHAE'];
																															} else {
																																if ($_POST['opt_esp'] == 1) {
																																	echo $registro_contribuyente->fechaespecial;
																																}
																															} ?>" <?php if ($_POST['opt_esp'] <> 1) {
																																		echo disabled;
																																	} ?>>
				</td>
				<td align="center">
					<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAR" size="10" readonly value="<?php if ($_POST['opt_esp'] == 0) {
																																echo $registro_contribuyente->fechaerevocado;
																															} ?>" <?php if ($revocado == 0 and $especial == 1) {
																																		echo disabled;
																																	} else {
																																		echo disabled;
																																	} ?>>
				</td>
			</tr>

			<tr>

				<td height="27" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Contribuyente Natural </u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
				<td width="20%">
					<div align="center">
						<?php echo $_SESSION['RIF']; ?>
					</div><!--</td>
			   <td width="18%" bgcolor="#CCCCCC"><strong>Firma Personal :</strong></td>
            <td width="5%"><label>
<div align="center">
  <input type="checkbox" name="OFIRMA" value="FIRMA" onClick="this.form.submit()" <?php if ($_POST[OFIRMA] == "FIRMA") {
																						echo 'checked="checked"';
																					}	?>>
</div>
</label>-->
				</td>
		</table>

		<table width="700" border="1" align="center">
			<tr>

				<td width="20%" bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
				<td width="80%"><label>
						<input type="text" name="OCONTRIBUYENTE" size="50" value="<?php if ($_SESSION['VARIABLE1'] == 'MODIFICAR' and $ACTUALIZAR == "SI") {
																						echo $registro_contribuyente->contribuyente;
																					} else {
																						echo $_POST[OCONTRIBUYENTE];
																					} ?>">
					</label></td>
			</tr>
			<?php if ($_POST[OFIRMA] == "FIRMA") {
				echo '<tr>
			  <td colspan="1" width="20%" bgcolor="#CCCCCC"><strong>Nombre Firma Comercial:</strong></td>
			  <td  colspan="1" width="57%"><label>
                <input type="text" name="ONOMBRE" size="50" value="';
				echo $_POST[ONOMBRE];
				echo '">
              </label></td>
		    </tr>';
			}
			?>
		</table>
		<table width="700" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Direccion</u></span></td>
			</tr>
			<tr>
				<td width="20%" bgcolor="#CCCCCC"><strong>Estado:</strong></td>
				<td width="30%"><label>
						<select name="OESTADO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$consulta = "SELECT id_estado,descripcion FROM dir_estados ORDER BY descripcion ASC";
							$tabla = mysql_query($consulta);
							while ($registro = mysql_fetch_object($tabla)) {
								echo '<option';
								if ($_POST[OESTADO] == $registro->id_estado) {
									echo ' selected="selected" ';
								}
								//
								if ($ESTADO <> -1) {
									$VAR_ESTADO = 'SI';
								} else {
									$VAR_ESTADO = 'NO';
								}

								echo ' value="';
								echo $registro->id_estado;
								echo '">';
								echo $registro->descripcion;
								echo '</option>';
							}
							?>
						</select>
					</label></td>
				<td width="15%" bgcolor="#CCCCCC"><strong>Ciudad:</strong></td>
				<td width="32%"><label>
						<select name="OCIUDAD" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php

							if ($_POST[OESTADO] <> -1) {

								$consulta = "SELECT id_ciudad, descripcion FROM dir_ciudades WHERE id_estado='" . $_POST[OESTADO] . "' ORDER BY descripcion;";
								$tabla = mysql_query($consulta);
								while ($registro = mysql_fetch_object($tabla)) {
									echo '<option';
									if ($_POST[OCIUDAD] == $registro->id_ciudad) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro->id_ciudad;
									echo '">';
									echo $registro->descripcion;
									echo '</option>';
								}
							}
							?>
						</select>
					</label></td>
			</tr>
			<tr>
				<td colspan="1" width="23%" bgcolor="#CCCCCC"><strong>Urb./Barrio/Zonas:</strong></td>
				<td colspan="3"><label>
						<select name="OURBANIZACION" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							if ($_POST[OCIUDAD] <> -1) {
								$consulta = "SELECT id_zona, descripcion FROM dir_zonas ORDER BY descripcion";
								$tabla = mysql_query($consulta);
								while ($registro = mysql_fetch_object($tabla)) {
									echo '<option';
									if ($_POST[OURBANIZACION] == $registro->id_zona) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro->id_zona;
									echo '">';
									echo $registro->descripcion;
									echo '</option>';
								}
							}
							?>
						</select>
					</label>

				</td>

			</tr>
			<!--
<tr>
			<td width="23%" bgcolor="#CCCCCC"><strong>Tipo Av./Calle/Carrera:</strong></td>
              <td  colspan="3"><label>
                <select name="OTIPOCALLE" size="1" onChange="this.form.submit()">
                <option value="-1">Seleccione</option>
                <?php
				if ($_POST[OURBANIZACION] <> -1) {

					$consulta = "SELECT id_tipo_calle,descripcion FROM dir_tipos_calle ORDER BY descripcion";

					$tabla = mysql_query($consulta);
					while ($registro = mysql_fetch_object($tabla)) {
						echo '<option';
						if ($_POST[OTIPOCALLE] == $registro->id_tipo_calle) {
							echo ' selected="selected" ';
						}
						echo ' value="';
						echo $registro->id_tipo_calle;
						echo '">';
						echo $registro->descripcion;
						echo '</option>';
					}
				}
				?>
              </select>
              </label></td>
			
</tr>


<tr>
			<td width="23%" bgcolor="#CCCCCC"><strong>Av./Calle/Carrera:</strong></td>
              <td  colspan="3"><label>
                <select name="OCALLE" size="1" onChange="this.form.submit()">
                <option value="-1">Seleccione</option>
                <?php

				if ($_POST[OTIPOCALLE] <> -1) {

					$consulta = "SELECT id_calle,descripcion FROM dir_calles ORDER BY descripcion";

					$tabla = mysql_query($consulta);
					while ($registro = mysql_fetch_object($tabla)) {
						echo '<option';
						if ($_POST[OCALLE] == $registro->id_calle) {
							echo ' selected="selected" ';
						}
						echo ' value="';
						echo $registro->id_calle;
						echo '">';
						echo $registro->descripcion;
						echo '</option>';
					}
				}
				?>
              </select>
              </label></td>
			
</tr>
-->
			<tr>
				<td width="23%" bgcolor="#CCCCCC"><strong>Domicilio:</strong></td>
				<td colspan="3"><label>
						<input type="text" name="ODOMICILIO" size="60" value="<?php if ($_SESSION['VARIABLE1'] == 'MODIFICAR') {
																					echo $registro_contribuyente->DescripcionDomicilio;
																				} else {
																					echo $_POST[ODOMICILIO];
																				} ?>">
					</label></td>
			</tr>
		</table>

		<table width="700" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="6"><span class="Estilo7"><u>Ocupacion</u></span></td>
			</tr>
			<tr>
				<td colspan="6" bgcolor="#CCCCCC">
					<div align="center"><strong>Actividad economica :</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="6"><label>
						<select name="OACTIVIDAD" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php

							if ($_POST[OURBANIZACION] <> -1) {
								$consulta = "SELECT id_actividad,left(descripcion,78) as descripcion1 FROM a_actividades ORDER BY descripcion;";
								$tabla = mysql_query($consulta);
								while ($registro = mysql_fetch_object($tabla)) {
									echo '<option';
									if ($_POST[OACTIVIDAD] == $registro->id_actividad) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro->id_actividad;
									echo '">';
									echo $registro->descripcion1;
									echo '</option>';
								}
							}
							?>
						</select>
					</label></td>
			</tr>

		</table>

		<p>
		<div align="center"> <input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></div>
		</p>
	</form>
	<p>
		<?php include "../pie.php";
		$_SESSION['VARIABLE1'] = 'INCLUYENDO'; ?>

	</p>
	<p>&nbsp;</p>
</body>