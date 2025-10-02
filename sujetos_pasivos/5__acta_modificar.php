<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 999;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['VARIABLE'] = 'NO MODIFICADA';

if ($_POST['CMDGUARDAR'] == "Guardar") {
	if ($_POST['OSEDE'] > 0 and $_POST['ORIF'] <> "" and $_POST['OPLANILLA'] <> "" and $_POST['OFECHA'] <> "" and $_POST['OMONTO'] <> "" and $_POST['OFECHAI'] <> "") {
		if ($_POST['OSEDE'] != 0) {
			// GERENTE DE LA REGION
			$consulta_g = "SELECT ci_gerente FROM z_region";
			$tabla_g = mysql_query($consulta_g);
			$registro_g = mysql_fetch_object($tabla_g);
			$Gerente = $registro_g->ci_gerente;
			//
		} else {
			// JEFE DEL SECTOR
			$consulta_g = "SELECT cedula FROM z_jefes_detalle WHERE id_sector=" . $_POST['OSEDE'];
			$tabla_g = mysql_query($consulta_g);
			$registro_g = mysql_fetch_object($tabla_g);
			$Gerente = $registro_g->cedula;
			//
		}

		// SECTOR DE LA PROVIDENCIA
		//$_SESSION['SEDE_USUARIO'] = $_POST['OSEDE'];
		//

		// VERIFICAMOS SI EL CONTRIBUYENTE NO TIENE PROVIDENCIA EMITIDA EN EL A�O
		$existe = 0;
		$consulta_existe = "SELECT numero, anno, sector FROM ce_providencia WHERE anno=" . date("Y") . " and rif='" . strtoupper($_POST['ORIF']) . "' and id_providencia<>" . $_POST['OID'];
		$tabla_existe = mysql_query($consulta_existe);
		if ($valor = mysql_fetch_object($tabla_existe)) {
			switch ($valor->sector) {
				case 1:
					$dependencia = "la Gerencia de Tributos Internos Regi�n Los Llanos";
					break;
				case 2:
					$dependencia = "el Sector de Tributos Internos San Juan de los Morros";
					break;
				case 3:
					$dependencia = "el Sector de Tributos Internos San Fernando de Apure";
					break;
				case 4:
					$dependencia = "la Unidad de Tributos Internos Altagracia de Orituco";
					break;
				case 5:
					$dependencia = "el Sector de Tributos Internos Valle de la Pascua";
					break;
			}
			$existe_numero = $valor->numero;
			$existe = 1;
		}

		if ($existe < 1) {
			// GUARDADO DE LOS DATOS
			$consulta = "UPDATE ce_providencia SET rif='" . strtoupper($_POST['ORIF']) . "', id_tributo=" . $_POST['OTRIBUTO'] . ", planilla='" . $_POST['OPLANILLA'] . "', fecha_planilla='" . voltea_fecha($_POST['OFECHAP']) . "', monto_planilla=" . $_POST['OMONTO'] . ", planilla_periodo_ini='" . voltea_fecha($_POST['OFECHA_I']) . "', planilla_periodo_fin='" . voltea_fecha($_POST['OFECHA_F']) . "', inicio_sujeto_especial='" . voltea_fecha($_POST['OFECHAI']) . "', usuario=" . $_SESSION['CEDULA_USUARIO'] . ", sector=" . $_POST['OSEDE'] . " WHERE id_providencia=" . $_POST['OID'] . ";";
			//echo "Update: ".$consulta.'<br/>';
			if ($tabla = mysql_query($consulta)) {
				$_SESSION['VARIABLE'] = 'MODIFICADA';
				//ACTUALIZACION DEL CONTRIBUYENTE COMO ESPECIAL
				$update_especial = "UPDATE contribuyentes SET Especial=1, fechaespecial='" . voltea_fecha($_POST['OFECHAI']) . "' WHERE rif='" . $_POST['ORIF'] . "'";
				if ($tabla_esp = mysql_query($update_especial)) {
					echo "<script type=\"text/javascript\">alert('Providencia modificada satisfactoriamente');</script>";
				} else {
					echo "<script type=\"text/javascript\">alert('Providencia modificada satisfactoriamente');</script>";
				}
			}
		} else {
			echo "<script type=\"text/javascript\">alert('El contribuyente ya posee providencia emitida en el a�o actual bajo el numero => " . $existe_numero . " por => " . $dependencia . "');</script>";
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

//------ BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0 and $_POST['ONUMERO_ANTERIOR'] <> $_POST['ONUMERO']) {
	$consulta_x = 'SELECT id_providencia, numero, anno, rif, fecha_registro, id_tributo, planilla, fecha_planilla, monto_planilla, planilla_periodo_ini, planilla_periodo_fin, inicio_sujeto_especial FROM ce_providencia WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_array($tabla_x);
	//---------
	$_POST['ORIF'] = $registro_x['rif'];
	$_POST['OFECHA'] = voltea_fecha($registro_x['fecha_registro']);
	$_POST['OTRIBUTO'] = $registro_x['id_tributo'];
	$_POST['OPLANILLA'] = $registro_x['planilla'];
	$_POST['OMONTO'] = $registro_x['monto_planilla'];
	$_POST['OFECHA_I'] = voltea_fecha($registro_x['planilla_periodo_ini']);
	$_POST['OFECHA_F'] = voltea_fecha($registro_x['planilla_periodo_fin']);
	$_POST['OFECHAI'] = voltea_fecha($registro_x['inicio_sujeto_especial']);
	$_POST['OFECHAP'] = voltea_fecha($registro_x['fecha_planilla']);
	$_POST['OID'] = $registro_x['id_providencia'];
}
?>
<html>
<title>Modificar Providencia SPE</title>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</script>
</head>
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

	.Estilo16 {
		color: #000000;
		font-weight: bold;
	}

	.right {
		text-align: right
	}
	-->
</style>

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
		<div align="center">
			<table width="50%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Modificar Providencia Administrativa - Calificaci&oacute;n Sujeto Pasivo Especial GGG</u></span></td>
				</tr>
				<td height="35" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
									$consulta_x = 'SELECT sector, nombre FROM vista_ce_providencias GROUP BY sector;';
								} else {
									$consulta_x = 'SELECT sector, nombre FROM vista_ce_providencias WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
								}
								//----------------------
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
								}

								?>
							</select>
						</span>
					</div>
				</td>
				<td bgcolor="#CCCCCC"><strong>A&ntilde;o:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo1">
								<select name="OANNO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										$consulta_x = 'SELECT anno FROM vista_ce_providencias WHERE sector =0' . $_POST['OSEDE'] . ' and motivo_anulacion is null GROUP BY anno ORDER BY anno DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OANNO'] == $registro_x['anno']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
										}
									}
									?>
								</select></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>
						Numero:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo1">
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero FROM vista_ce_providencias WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' and motivo_anulacion is null ORDER BY numero DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['ONUMERO'] == $registro_x['numero']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
										}
									}
									?>
								</select></span></div>
						<input type="hidden" name="ONUMERO_ANTERIOR" value="<?php echo $_POST['ONUMERO']; ?>"></td>
				<td bgcolor="#CCCCCC"><strong>
						Fecha:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15">
								<input type="text" name="OFECHA" value="<?php echo $_POST['OFECHA']; ?>" size="8" readonly style="text-align:center"></span></div>
					</label></td>
			</table>

			<table width="50%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td width="28%"><label>
							<input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
							<input type="submit" class="boton" name="Submit" value="Buscar"></label></td>
					<td width="21%" bgcolor="#CCCCCC"><strong>
							Contribuyente:</strong></td>
					<td width="36%"><label><span class="Estilo15"><?php
																	if ($_POST['ORIF'] <> "") {
																		// BUSQUEDA DEL CONTRIBUYENTE
																		$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
																		$tabla_x = mysql_query($consulta_x);
																		$registro_x = mysql_fetch_object($tabla_x);
																		// FIN
																		echo $registro_x->contribuyente;
																		$_POST['OESPECIAL'] = $registro_x->Especial;
																	}
																	?></span></label></td>
				</tr>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
					<td colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?>
							</span></label></td>
				</tr>
			</table>

			<table width="50%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de la Declaraci&oacute;n </u></span></td>
				</tr>
				<tr>
					<td width="24%" bgcolor="#CCCCCC"><strong>Tributo:</strong></td>
					<td width="76%" colspan="4"> <select name="OTRIBUTO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$consulta_x = "SELECT id_tributo, nombre FROM a_tributos WHERE id_tributo IN (1,3,20) ORDER BY id_tributo;";
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_object($tabla_x)) {
								echo '<option';
								//-----------------
								if ($_POST['OTRIBUTO'] <> "-1" and $_POST['OTRIBUTO'] == $registro_x->id_tributo) {
									echo ' selected="selected" ';
								}
								//-----------------	
								echo ' value="';
								echo $registro_x->id_tributo;
								echo '">';
								echo $registro_x->id_tributo . " - " . $registro_x->nombre;
								echo '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="24%" bgcolor="#CCCCCC"><strong>Planilla Nro:</strong></td>
					<td width="76%" colspan="4"><input name="OPLANILLA" type="text" value="<?php echo $_POST['OPLANILLA']; ?>" size="25" maxlength="15"></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Ejercicio/Periodo:</strong></td>
					<td bgcolor="#CCCCCC" align="center"><strong>Inicio:</strong></td>
					<td align="center"><input style="text-align:center" onclick='javascript:scwShow(this,event);' name="OFECHA_I" type="text" value="<?php echo $_POST['OFECHA_I']; ?>" size="15" maxlength="10" readonly>
					</td>
					<td bgcolor="#CCCCCC" align="center"><strong>Fin:</strong></td>
					<td align="center"><input style="text-align:center" onclick='javascript:scwShow(this,event);' name="OFECHA_F" type="text" value="<?php echo $_POST['OFECHA_F']; ?>" size="15" maxlength="10" readonly></td>

				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Fecha Presentacion:</strong></td>
					<td colspan="4"><input style="text-align:center" onclick='javascript:scwShow(this,event);' name="OFECHAP" type="text" value="<?php echo $_POST['OFECHAP']; ?>" size="15" maxlength="10" readonly></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Monto Bs.:</strong></td>
					<td colspan="4"><input class="right" name="OMONTO" type="text" value="<?php echo $_POST['OMONTO']; ?>" size="15" maxlength="20" onKeyPress="return SoloMoneda(event,this)"></td>
				</tr>
			</table>

			<table width="50%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Fecha Inicio Obligaciones como Sujeto Pasivo Especial</u></span></td>
				</tr>
				<tr>
					<td width="24%" bgcolor="#CCCCCC"><strong>Fecha Inicio:</strong></td>
					<td width="76%"><input onclick='javascript:scwShow(this,event);' name="OFECHAI" type="text" value="<?php echo $_POST['OFECHAI']; ?>" size="15" maxlength="10" readonly></td>
				</tr>
			</table>


			</p>

			<label>

				<?php echo '<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">';
				?>
			</label>
		</div>
		<input name="OID" type="hidden" value="<?php echo $_POST['OID']; ?>">
	</form>

	<div align="center">

		<?php
		if ($_SESSION['VARIABLE'] == 'MODIFICADA') {
			//$_SESSION['VARIABLE1'] = $registro_x->tipo;
			//------------------
			echo '<form name="form2" method="post" action="formatos/providencia.php?num=' . $_POST['ONUMERO'] . '&anno=' . $_POST['OANNO'] . '&sector=0' . $_POST['OSEDE'] . '" target="_blank">';
			echo '<input type="submit" class="boton" name="CMDPORTADA" value="Ver Providencia"></form>';
			// FIN

		}
		?>
	</div>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>



</body>

</html>