<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 5;

//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<?php
// --- PARA GUARDAR
if ($_POST['CMDAGREGAR'] == "Agregar") {
	if ($_POST['OMONREP'] > 0 and $_POST['OIMPPAG'] > -1 and $_POST['OIMPOMI'] > -1 and $_POST['OCOT'] > 0 and $_POST['OTRIBUTO'] > 0 and $_POST['OFECHA'] <> "" and $_POST['ODESDE'] <> "" and $_POST['OHASTA'] <> "") {
		$EXISTE_ACTA = 'NO';
		//----- PARA VER SI EXISTE EL ACTA
		$consulta_x = "SELECT id_acta, numero AS Maximo FROM fis_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		if ($registro_x->Maximo > 0) {
			$Maximo = $registro_x->Maximo;
			$id_acta = $registro_x->id_acta;
			$EXISTE_ACTA = 'SI';
		} else {
			//----NO EXISTE EL ACTA Y BUSCO EL NUMERO SIGUIENTE
			$consulta_x = "SELECT MAX(numero) AS Maximo FROM fis_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno=YEAR(DATE(NOW()))";
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_object($tabla_x);
			if ($registro_x->Maximo > 0) {
				$Maximo = $registro_x->Maximo + 1;
			} else {
				$Maximo = 1;
			}
		}
		//-------------------

		// GUARDADO	DEL ACTA
		if ($EXISTE_ACTA == 'NO') {
			$consulta = "INSERT INTO fis_actas ( id_sector, anno_prov, num_prov, numero, fecha, anno, COT, status, acta, usuario ) VALUES( " . $_SESSION['SEDE'] . ", " . $_SESSION['ANNO_PRO'] . ", " . $_SESSION['NUM_PRO'] . ", " . $Maximo . ", DATE(NOW()), YEAR(DATE(NOW())), '" . $_POST['OCOT'] . "', 0, 0, " . $_SESSION['CEDULA_USUARIO'] . ");";
			$tabla = mysql_query($consulta);
			//----- PARA VER EL ID DEL ACTA
			$consulta_x = "SELECT id_acta FROM fis_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_object($tabla_x);
			$id_acta = $registro_x->id_acta;
			//--FIN
		}

		//---VALIDAR QUE NO EXISTA EL DETALLE
		$consulta = "SELECT id_detalle FROM fis_actas_detalle WHERE id_acta = 0" . $id_acta . " AND periodo_desde='" . voltea_fecha($_POST['ODESDE']) . "' AND periodo_hasta='" . voltea_fecha($_POST['OHASTA']) . "' AND tributo='" . $_POST['OTRIBUTO'] . "';";
		$tabla_x = mysql_query($consulta);
		if ($registro_x = mysql_fetch_object($tabla_x)) {
			// MENSAJE DE DETALLE REPETIDO
			echo "<script type=\"text/javascript\">alert('���Per�odo ya Registrado!!!');</script>";
		} else {
			// GUARDADO	DEL DETALLE	DEL ACTA
			$consulta = "INSERT INTO fis_actas_detalle ( COT, tributo, id_acta, reparo, impuesto_pagado, impuesto_omitido, fecha_vencimiento, periodo_desde, periodo_hasta, usuario) VALUES( '" . $_POST['OCOT'] . "', " . $_POST['OTRIBUTO'] . ", " . $id_acta . ", '" . $_POST[OMONREP] . "', '0" . $_POST[OIMPPAG] . "', '0" . $_POST[OIMPOMI] . "', '" . voltea_fecha($_POST['OFECHA']) . "', '" . voltea_fecha($_POST['ODESDE']) . "', '" . voltea_fecha($_POST['OHASTA']) . "', " . $_SESSION['CEDULA_USUARIO'] . ");";
			$tabla = mysql_query($consulta);
			// FIN GUARDADO	DEL DETALLE	
			// MENSAJE DE GUARDADO
			echo "<script type=\"text/javascript\">alert('���Acta de Reparo Guardada Exitosamente!!!');</script>";
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Por favor Rellene todos los Campos Obligatorios!!!');</script>";
	}
}

// --- PARA ELIMINAR
$i = 0;
$consulta_x = "SELECT id_detalle FROM vista_detalle_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
$tabla_x = mysql_query($consulta_x);
//---------------
while ($registro_x = mysql_fetch_object($tabla_x)) {
	if ($_POST['CMDE' . $registro_x->id_detalle] == "Eliminar") {
		// CONSULTA PARA ELIMINAR
		$consulta_xx = "DELETE FROM fis_actas_detalle WHERE id_detalle=" . $registro_x->id_detalle . ";";
		$tabla_xx = mysql_query($consulta_xx);
		echo "<script type=\"text/javascript\">alert('���Per�odo Eliminado!!!');</script>";
		//----------------
		// REVISION SI FUERON ELIMINADOS TODOS LOS DETALLES DE LA ACTA DE REPARO
		$consulta_xx = "SELECT id_detalle FROM fis_actas, fis_actas_detalle WHERE fis_actas.id_acta = fis_actas_detalle.id_acta AND fis_actas.id_sector=" . $_SESSION['SEDE'] . " AND fis_actas.anno_prov=" . $_SESSION['ANNO_PRO'] . " AND fis_actas.num_prov=" . $_SESSION['NUM_PRO'] . ";";
		$tabla_xx = mysql_query($consulta_xx);
		if ($registro_acta = mysql_fetch_object($tabla_xx)) {
		} else {
			$consulta = "DELETE FROM fis_actas WHERE status=0 AND id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
			$tabla = mysql_query($consulta);
			echo "<script type=\"text/javascript\">alert('���Acta de Reparo Eliminada!!!');</script>";
		}
		//---------------
	}
}

?>
<html>

<head>
	<title>Incluir Acta de Reparo</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<a name="vista"></a>
	<form name="form1" method="post" action="#vista">
		<table width="60%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Datos de la Providencia</u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC"><strong>A�o:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15">
								<?php
								$consulta = "SELECT * FROM vista_providencias WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector =" . $_SESSION['SEDE'] . ";";
								$tabla = mysql_query($consulta);
								$registro = mysql_fetch_object($tabla);

								echo $registro->anno;
								$tipo = $registro->tipo;
								?>
							</span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_emision); ?></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>Sector:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo strtoupper($registro->nombre); ?></span></div>
					</label></td>
			</tr>
		</table>

		<table width="60%" border="1" align="center">
			<tr>
				<td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo formato_rif($registro->rif); ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro->contribuyente; ?></span></label></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_supervisor); ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro->Nombres . " " . $registro->Apellidos; ?></span></label></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_fiscal1); ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro->Nombres1 . " " . $registro->Apellidos1; ?></span></label></td>
			</tr>
		</table>
		<p></p>
		<table width="60%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="8"><span><u>Datos del Acta</u></span></td>
			</tr>
			<tr>
				<td align="center" bgcolor="#CCCCCC"><strong>N&uacute;mero Tentativo:</strong></td>
				<td align="center" bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
				<td align="center" bgcolor="#CCCCCC"><strong>Procedimiento:</strong></td>
				<td align="center" bgcolor="#CCCCCC"><strong>Tributo:</strong></td>
			</tr>
			<tr>
				<td align="center"><label><span class="Estilo15">
							<?php
							// REVISION DE LAS ACTAS CON LA PROVIDENCIA Y EL TRIBUTO SELECCIONADO
							$Maximo = 0;
							$fecha_reg = '';
							//				if ($_POST['OTRIBUTO']>0)
							//					{
							//----- PARA VER SI EXISTE EL ACTA
							$consulta_x = "SELECT numero AS Maximo, COT, fecha FROM fis_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
							$tabla_x = mysql_query($consulta_x);
							$registro_x = mysql_fetch_object($tabla_x);
							if ($registro_x->Maximo > 0) {
								$Maximo = $registro_x->Maximo;
								//$_POST['OTRIBUTO'] = $registro_x->tributo; 	
								//$_POST['OCOT'] = $registro_x->COT; 
								$fecha_reg = voltea_fecha($registro_x->fecha);
							} else {
								//----NO EXISTE EL ACTA Y BUSCO EL NUMERO SIGUIENTE
								$consulta_x = "SELECT MAX(numero) AS Maximo FROM fis_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno=YEAR(DATE(NOW()))";
								$tabla_x = mysql_query($consulta_x);
								$registro_x = mysql_fetch_object($tabla_x);
								if ($registro_x->Maximo > 0) {
									$Maximo = $registro_x->Maximo + 1;
								} else {
									$Maximo = 1;
								}
							}
							//					}		
							// FIN
							////////// SIGLAS DE LA RESOLUCION
							$consulta_x = "SELECT Siglas_resol_fis FROM z_siglas WHERE id_sector=" . $_SESSION['SEDE'] . ";";
							$tabla_x = mysql_query($consulta_x);
							$registro_x = mysql_fetch_object($tabla_x);
							$SIGLAS = $registro_x->Siglas_resol_fis;

							////////// SIGLAS DE LA PROVIDENCIA
							$consulta_x = "SELECT * FROM a_tipo_providencia WHERE tipo=" . $tipo . ";";
							$tabla_x = mysql_query($consulta_x);
							$registro_x = mysql_fetch_object($tabla_x);
							$SIGLAS1 = $registro_x->Siglas1;
							$SIGLAS2 = $registro_x->Siglas2;
							// ---------------------

							////////// DATOS DE LA RESOLUCION
							$RES_PRO = $SIGLAS . "/" . $_SESSION['ANNO_PRO'] . "/" . $SIGLAS2 . "/" . $SIGLAS1 . sprintf("%005s", $_SESSION['NUM_PRO']);
							$RES_PRO = $SIGLAS . "/" . $_SESSION['ANNO_PRO'] . "/" . $SIGLAS1 . sprintf("%005s", $_SESSION['NUM_PRO']);
							$RES_ACTA = "/" . date('Y') . '/' . $Maximo;
							echo $RES_PRO . $RES_ACTA;
							////////// FIN

							?>
						</span></label></td>
				<td align="center"><label><span class="Estilo15"><?php if ($fecha_reg == '') {
																		echo date("d/m/Y");
																	} else {
																		echo $fecha_reg;
																	} ?></span></label></td>
				<td align="center"><label>
						<select name="OCOT" size="1" onChange="this.form.submit()">
							<option value="0">Seleccione</option>
							<?php
							$consulta2 = "SELECT nombre FROM a_procedimiento_actas;";
							$tabla2 = mysql_query($consulta2);
							while ($registro2 = mysql_fetch_object($tabla2)) {
								echo '<option';
								if ($_POST['OCOT'] == $registro2->nombre) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro2->nombre;
								echo '">';
								echo $registro2->nombre;
								echo '</option>';
								// FIN
							}
							?>
						</select>
					</label></td>
				<td align="center"><label>
						<select name="OTRIBUTO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$consulta2 = "SELECT id_tributo, Siglas FROM a_tributos WHERE id_tributo IN (1,3,5,9,11,13) ORDER BY id_tributo;";
							$tabla2 = mysql_query($consulta2);
							while ($registro2 = mysql_fetch_object($tabla2)) {
								echo '<option';
								if ($_POST[OTRIBUTO] == $registro2->id_tributo) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro2->id_tributo;
								echo '">';
								echo $registro2->Siglas;
								echo '</option>';
								// FIN
							}

							?>
						</select></label></td>
			</tr>
		</table>
		<p>
		</p>
		<table width="60%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="8"><span><u>Detalle del Acta</u></span></td>
			</tr>
			<tr>
				<td colspan="1" align="center" bgcolor="#CCCCCC"><strong>Per&iacute;odo Desde:</strong></td>
				<td colspan="1" align="center"><input onclick='javascript:scwShow(this,event);' type="text" name="ODESDE" size="8" readonly value="<?php echo $_POST['ODESDE']; ?>" /></td>
				<td colspan="1" align="center" bgcolor="#CCCCCC"><strong>Per&iacute;odo Hasta:</strong></td>
				<td colspan="1" align="center"><input onclick='javascript:scwShow(this,event);' type="text" name="OHASTA" size="8" readonly value="<?php echo $_POST['OHASTA']; ?>" /></td>
				<td align="center" bgcolor="#CCCCCC"><strong>Fecha Vencimiento:</strong></td>
				<td align="center"><label><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>" /></label></td>
			</tr>
		</table>
		<table width="60%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#CCCCCC"><strong>Monto Reparo:</strong></td>
				<td align="center"><label>
						<input type="text" name="OMONREP" value="<?php echo $_POST['OMONREP']; ?>" size="13">
					</label></td>
				<td align="center" bgcolor="#CCCCCC"><strong>Impuesto Pagado (Autoliquidacion):</strong></td>
				<td align="center"><label>
						<input type="text" name="OIMPPAG" value="<?php echo $_POST['OIMPPAG']; ?>" size="13">
					</label></td>
				<td align="center" bgcolor="#CCCCCC"><strong>Impuesto Omitido:</strong></td>
				<td align="center"><label>
						<input type="text" name="OIMPOMI" value="<?php echo $_POST['OIMPOMI']; ?>" size="13">
					</label></td>
			</tr>
		</table>
		<?php $consulta_x = "SELECT id_acta FROM fis_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND (((anno_prov)=" . $_SESSION['ANNO_PRO'] . ") AND ((num_prov)=" . $_SESSION['NUM_PRO'] . ") AND ((status)>0));";
		$tabla_x = mysql_query($consulta_x);
		if ($registro_x = mysql_fetch_object($tabla_x)) {
			echo '<p align="center"><strong>(La Providencia ya posee Actas Notificadas)</strong></p>';
		}
		?>
		<p align="center">
			<input type="submit" class="boton" name="CMDAGREGAR" value="Agregar">
		</p>
		<p align="center">&nbsp;</p>
	</form>

	<table width="80%" border=1 align=center>
		<tbody>
			<tr>
				<td>
					<p>&nbsp;</p>
					<form name="form5" method="post" action="8_incluir_acta.php">
						<table width="70%" border=1 align=center>
							<tbody>
								<tr>
									<td class="TituloTabla" height="27" colspan="11" align="center"><span><u>Periodo(s) actual(es) registrado(s) al Acta</u></span> </td>
								</tr>
								<tr>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">#</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Procedimiento</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Tributo</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Periodo</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Fecha Venc</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Reparo</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Impuesto Pagado</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Impuesto Omitido</div>
									</td>
									<td bgcolor="#CCCCCC">
										<div align="center" class="Estilo8">Opciones</div>
									</td>
								</tr>
								<?php $i = 0;

								$consulta_x = "SELECT COT, siglas, id_detalle, periodo_desde, periodo_hasta, fecha_vencimiento, reparo, impuesto_omitido, impuesto_pagado FROM vista_detalle_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
								$tabla_x = mysql_query($consulta_x);
								//---------------
								while ($registro_x = mysql_fetch_object($tabla_x)) {
									$i++;
								?>
									<tr>
										<td>
											<div align="center"><span class="Estilo15"><?php echo $i; ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><?php echo $registro_x->COT; ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><?php echo $registro_x->siglas; ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><?php echo date('d-m-Y', strtotime($registro_x->periodo_desde)) . ' al ' . voltea_fecha($registro_x->periodo_hasta); ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_x->fecha_vencimiento); ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->reparo); ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->impuesto_pagado); ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->impuesto_omitido); ?></span></div>
										</td>
										<td>
											<div align="center"><span class="Estilo15"><input type="submit" class="boton" name="CMDE<?php echo $registro_x->id_detalle; ?>" value="Eliminar" /></span></div>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</form>
					<p>
						<?php $serie = "";
						$mostrarboton = 'NO';
						$serie = "1=1";
						include "../funciones/0_sanciones_aplicadas.php"; ?></p>
					<p>&nbsp;</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>