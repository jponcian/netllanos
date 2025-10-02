<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 14;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['VARIABLE'] = 'NO REGISTRADA';

if ($_POST['CMDGUARDAR'] == "Guardar") {
	if ($_POST['OSEDE'] > 0 and $_POST['ORIF'] <> "" and $_POST['OPLANILLA'] <> "" and $_POST['OFECHA'] <> "" and $_POST['OMONTO'] <> "" and $_POST['OFECHAI'] <> "") {
		// CONSULTA DE LA PROVIDENCIA SIGUIENTE
		$consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM ce_providencia WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . date('Y') . ';';
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		//-------------
		if ($registro_x['Maximo'] > 0) {
			$Maximo = $registro_x['Maximo'];
		} else {
			$Maximo = 1;
		}
		// FIN

		if ($_POST['OSEDE'] == 1) {
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
		$_SESSION['SEDE_USUARIO'] = $_POST['OSEDE'];
		//

		// VERIFICAMOS SI EL CONTRIBUYENTE NO TIENE PROVIDENCIA EMITIDA EN EL A�O
		$existe = 0;
		$consulta_existe = "SELECT numero, anno, sector FROM ce_providencia WHERE anno=" . date("Y") . " and rif='" . strtoupper($_POST['ORIF']) . "' and motivo_anulacion is null";
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
			$consulta = "INSERT INTO ce_providencia (numero, anno, rif, fecha_registro, id_tributo, planilla, fecha_planilla, monto_planilla, cedula_autorizado, inicio_sujeto_especial, usuario, sector) VALUES (" . $Maximo . ", " . date("Y") . ", '" . strtoupper($_POST['ORIF']) . "', date(now()), " . $_POST['OTRIBUTO'] . ", '" . $_POST['OPLANILLA'] . "', '" . $_POST['OFECHA'] . "', " . $_POST['OMONTO'] . ", " . $Gerente . ", '" . $_POST['OFECHAI'] . "', " . $_SESSION['CEDULA_USUARIO'] . ", " . $_POST['OSEDE'] . ");";
			if ($tabla = mysql_query($consulta)) {
				$_SESSION['VARIABLE'] = 'REGISTRADA';
				//ACTUALIZACION DEL CONTRIBUYENTE COMO ESPECIAL
				$update_especial = "UPDATE contribuyentes SET Especial=1, fechaespecial='" . $_POST['OFECHAI'] . "' WHERE rif='" . $_POST['ORIF'] . "'";
				echo "<script type=\"text/javascript\">alert('Providencia Creada bajo el N�mero => " . $Maximo . "');</script>";
			}
		} else {
			echo "<script type=\"text/javascript\">alert('El contribuyente ya posee providencia emitida en el a�o actual bajo el numero => " . $existe_numero . " por => " . $dependencia . "');</script>";
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}
?>
<html>

<head>
	<title>Crear Providencia Calificacion SPE</title>
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
	-->
	.right
	{
	text-align:right
	}
</style>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	<form name="form1" method="post" action="">
		<div align="center">
			<table width="50%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Providencia Administrativa - Calificaci&oacute;n Sujeto Pasivo Especial</u></span></td>
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
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
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
						</span>
					</div>
				</td>
				<td bgcolor="#CCCCCC" align="right"><strong>Numero:</strong></td>
				<td width="40"><label>
						<div align="center"><span class="Estilo15">
								<?php
								if ($_POST['OSEDE'] > 0) {
									// CONSULTA DE LA PROVIDENCIA SIGUIENTE
									$consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM ce_providencia WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . date('Y') . ';';
									$tabla_x = mysql_query($consulta_x);
									$registro_x = mysql_fetch_object($tabla_x);
									//-------------
									if ($registro_x['Maximo'] > 0) {
										$Maximo = $registro_x['Maximo'];
									} else {
										$Maximo = 1;
									}
									// FIN
									echo $Maximo;
								}
								?></span></div>
					</label></td>
				<td width="60" bgcolor="#CCCCCC" align="right"><strong>
						A�o:</strong></td>
				<td width="50"><label>
						<div align="center"><span class="Estilo15"><?php echo date('Y'); ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC" align="right"><strong>
						Fecha:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo date('d/m/Y'); ?></span></div>
					</label></td>
			</table>

			<table width="50%" border="1" align="center">
				<tr>
					<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
				</tr>
				<tr>
					<td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td width="20%"><label>
							<input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
							<input type="submit" class="boton" name="Submit" value="Buscar"></label></td>
					<td width="11%" bgcolor="#CCCCCC"><strong>
							Contribuyente:</strong></td>
					<td width="36%"><label><span class="Estilo15"><?php
																	if ($_POST['ORIF'] <> "") {
																		// BUSQUEDA DEL CONTRIBUYENTE
																		$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
																		$tabla_x = mysql_query($consulta_x);
																		$registro_x = mysql_fetch_object($tabla_x);
																		// FIN
																		echo $registro_x->contribuyente;
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
					<td width="76%"> <select name="OTRIBUTO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$consulta_x = "SELECT id_tributo, nombre FROM a_tributos ORDER BY id_tributo;";
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
					<td width="76%"><input name="OPLANILLA" type="text" value="<?php echo $_POST['OPLANILLA']; ?>" size="25" maxlength="15"></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
					<td><input onclick='javascript:scwShow(this,event);' name="OFECHA" type="text" value="<?php echo $_POST['OFECHA']; ?>" size="15" maxlength="10" readonly></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Monto Bs.:</strong></td>
					<td><input class="right" name="OMONTO" type="text" value="<?php echo $_POST['OMONTO']; ?>" size="15" maxlength="12" onKeyPress="return SoloMoneda(event,this)"></td>
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
	</form>

	<div align="center">
		<?php
		if ($_SESSION['VARIABLE'] == 'REGISTRADA') {
			// CONSULTA DEL ULTIMO EXPEDIENTE REGISTRADO
			$consulta_x = "SELECT anno, numero FROM ce_providencia WHERE sector=" . $_POST['OSEDE'] . " ORDER BY anno DESC , numero DESC;";
			$tabla_x = mysql_query($consulta_x);
			if ($registro_x = mysql_fetch_object($tabla_x)) {
				$_SESSION['ANNO'] = $registro_x->anno;
				$_SESSION['NUMERO'] = $registro_x->numero;
				$_SESSION['SEDE'] = $_POST['OSEDE'];
				//$_SESSION['VARIABLE1'] = $registro_x->tipo;
				//------------------
				echo '<form name="form2" method="post" action="formatos/providencia.php?num=' . $_SESSION['NUMERO'] . '&anno=' . $_SESSION['ANNO'] . '&sector=' . $_SESSION['SEDE_USUARIO'] . '" target="_blank">';
				echo '<input type="submit" class="boton" name="CMDPORTADA" value="Ver Providencia"></form>';
			}
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