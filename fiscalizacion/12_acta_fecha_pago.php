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
<html>

<head>
	<title>Incluir Fecha de Pago - Acta de Reparo</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<!--<meta http-equiv="refresh" content="10">-->
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
		<?php
		// --- PARA GUARDAR
		if ($_POST['CMDGUARDAR'] == "Guardar") {
			$consulta_x = "SELECT id_detalle, fecha_vencimiento, COT, periodo_desde FROM vista_detalle_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
			$tabla_x = mysql_query($consulta_x);
			//---------------
			while ($registro_x = mysql_fetch_object($tabla_x)) {
				if ($_POST['OMONTO' . $registro_x->id_detalle] > -1 and $_POST['OFECHA' . $registro_x->id_detalle] <> '') {
					//--------------- CALCULO DEL INTERES
					$interes = funcion_interes($_POST['OMONTO' . $registro_x->id_detalle], $_POST['OFECHA' . $registro_x->id_detalle], voltea_fecha($registro_x->fecha_vencimiento));
					//--------------- CALCULO DE LA MULTA
					$cot = $registro_x->COT; //echo $cot;
					$monto = $_POST['OMONTO' . $registro_x->id_detalle]; //echo $monto;
					$fecha_pago = ($_POST['OFECHA' . $registro_x->id_detalle]);
					$fecha_vencimiento = ($registro_x->fecha_vencimiento); //voltea_fecha
					$periodo_desde = ($registro_x->periodo_desde);
					//-------------
					include "../funciones/0_calculo_multa_acta.php";
					//---------------
					if ($multa_primitiva > 0) {
						if (fecha_a_numero(voltea_fecha($fecha_vencimiento)) >= fecha_a_numero('2020/03/01')) {
							$UT_primitiva = formato_moneda2($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']);
						} else {
							$UT_primitiva = formato_moneda2($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']);
						}
					} else {
						$UT_primitiva = 0;
					}
					//----------------
					if ($multa > 0) {
						if (fecha_a_numero(voltea_fecha($fecha_vencimiento)) >= fecha_a_numero('2020/03/01')) {
							$UT_actual = formato_moneda2($multa / $_SESSION['MONTO_BS_ACTUAL']);
						} else {
							$UT_actual = formato_moneda2($multa / $_SESSION['VALOR_UT_ACTUAL']);
						}
					} else {
						$UT_actual = 0;
					}

					//---------------
					$consulta_Xx = "UPDATE fis_actas_detalle SET planilla= '" . $_POST['OPLANILLA' . $registro_x->id_detalle] . "', fecha_pago = '" . voltea_fecha($_POST['OFECHA' . $registro_x->id_detalle]) . "', monto_pagado = '" . $_POST['OMONTO' . $registro_x->id_detalle] . "', multa_primitiva = '" . $multa_primitiva . "', UT_primitiva = '" . $UT_primitiva . "', multa_actual = '" . $multa . "', UT_actual = '" . $UT_actual . "', interes = '" . $interes . "', usuario= " . $_SESSION['CEDULA_USUARIO'] . " WHERE id_detalle=" . $registro_x->id_detalle . ";";
					$tabla_Xx = mysql_query($consulta_Xx);  //echo $consulta_Xx;
					//echo $consulta_Xx .' VALOR PRIMITIVO '.$_SESSION['MONTO_BS_PRIMITIVO'] .' VALOR ACTUAL '.$_SESSION['MONTO_BS_ACTUAL']; 

					//------ ACTUALIZAR STATUS A PAGO TOTAL
					$consulta_Xx = "UPDATE fis_actas_detalle SET status=1 WHERE (fis_actas_detalle.`status` = 0 OR fis_actas_detalle.`status` = 1) AND fis_actas_detalle.monto_pagado >= fis_actas_detalle.impuesto_omitido AND fis_actas_detalle.monto_pagado >= 0 and (planilla)<>'';";
					$tabla_Xx = mysql_query($consulta_Xx);

					//------ ACTUALIZAR STATUS A PAGO PARCIAL
					$consulta_Xx = "UPDATE fis_actas_detalle SET status=2 WHERE (fis_actas_detalle.`status` = 0 OR fis_actas_detalle.`status` = 1) AND fis_actas_detalle.monto_pagado < fis_actas_detalle.impuesto_omitido AND fis_actas_detalle.monto_pagado > 0 and (planilla)<>'';";
					$tabla_Xx = mysql_query($consulta_Xx);

					// MENSAJE DE GUARDADO
					//echo "<script type=\"text/javascript\">alert('¡¡¡Pago Actualizado Exitosamente!!!');</script>";
				}
			}
			echo "<script type=\"text/javascript\">alert('¡¡¡Pago(s) Actualizado(s) Exitosamente!!!');</script>";
		}
		?>
	</p>
	<p>
		<?php
		include "menu.php";
		?>

	<form name="form1" method="post" action="">
		</p>
		<p>&nbsp;</p>
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
								//----------
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
		<table width="50%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="6"><span><u>Datos del Acta</u></span></td>
			</tr>
			<tr>
				<td align="center" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
				<td align="center" bgcolor="#CCCCCC"><strong>Fecha Registro:</strong></td>
			</tr>
			<tr>
				<td align="center"><label><span class="Estilo15">
							<?php
							list($resolucion, $fecha_reg, $cot) = funcion_acta_reparo($_SESSION['SEDE'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
							echo $resolucion;
							?>
						</span></label></td>
				<td align="center"><label><span class="Estilo15"><?php if ($fecha_reg == '') {
																		echo date("d/m/Y");
																	} else {
																		echo $fecha_reg;
																	} ?></span></label></td>

			</tr>
		</table>

		<p>
			<label></label>
		<form name="form5" method="post" action="#vista">
			<table width="70%" border=1 align=center>
				<tbody>
					<tr>
						<td class="TituloTabla" height="27" colspan="12" align="center"><span><u>Periodo(s) actual(es) registrado(s) al Acta</u></span></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>#</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Periodo</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Monto Reparo</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Impuesto Pagado</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Impuesto Omitido</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Fecha Venc</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Dias Trans</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Planilla Pago</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Monto Pagado</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Fecha Pago</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Multa Bs</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Inter&eacute;s Bs</strong></div>
						</td>
					</tr>
					<?php
					$i = 0;
					$consulta_x = "SELECT id_detalle, periodo_desde, periodo_hasta, fecha_vencimiento, reparo, impuesto_omitido, impuesto_pagado, multa_actual, interes, monto_pagado, fecha_pago, planilla FROM vista_detalle_actas WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
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
								<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_x->periodo_desde) . ' al ' . voltea_fecha($registro_x->periodo_hasta); ?></span></div>
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
								<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_x->fecha_vencimiento); ?></span></div>
							</td>
							<td>
								<div align="center"><span class="Estilo15"><?php echo ((fecha_a_numero($registro_x->fecha_pago)) - (fecha_a_numero($registro_x->fecha_vencimiento))) / 86400 ?></span></div>
							</td>
							<td>
								<div align="center"><span class="Estilo15">
										<?php
										if ($registro_x->monto_pagado > 0) //($registro_x->fecha_notificacion>'01-01-2000') 
										{
											$_POST['OPLANILLA' . $registro_x->id_detalle] = $registro_x->planilla;
										} else {
											$_POST['OPLANILLA' . $registro_x->id_detalle] = $_POST['OPLANILLA' . $registro_x->id_detalle];
										}
										?><input style="text-align:right" type="text" name="OPLANILLA<?php echo $registro_x->id_detalle; ?>" size="10" value="<?php echo $_POST['OPLANILLA' . $registro_x->id_detalle]; ?>" />
									</span></div>
							</td>
							<td>
								<div align="center"><span class="Estilo15">
										<?php
										if ($registro_x->monto_pagado > 0) //($registro_x->fecha_notificacion>'01-01-2000') 
										{
											$_POST['OMONTO' . $registro_x->id_detalle] = $registro_x->monto_pagado;
										} else {
											$_POST['OMONTO' . $registro_x->id_detalle] = $_POST['OMONTO' . $registro_x->id_detalle];
										}
										?><input style="text-align:right" type="text" name="OMONTO<?php echo $registro_x->id_detalle; ?>" size="10" value="<?php echo $_POST['OMONTO' . $registro_x->id_detalle]; ?>" />
									</span></div>
							</td>

							<td>
								<div align="center"><span class="Estilo15">
										<?php
										if ($registro_x->monto_pagado > 0) {
											$_POST['OFECHA' . $registro_x->id_detalle] = voltea_fecha($registro_x->fecha_pago);
										} else {
											$_POST['OFECHA' . $registro_x->id_detalle] = $_POST['OFECHA' . $registro_x->id_detalle];
										}
										?><input style="text-align:center" onclick='javascript:scwShow(this,event);' type="text" name="OFECHA<?php echo $registro_x->id_detalle; ?>" size="8" readonly value="<?php echo $_POST['OFECHA' . $registro_x->id_detalle]; ?>" />
									</span></div>
							</td>
							<td>
								<div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->multa_actual); ?></span></div>
							</td>
							<td>
								<div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->interes); ?></span></div>
							</td>
						</tr>
					<?php
						//echo ((fecha_a_numero($registro_x->fecha_pago))-(fecha_a_numero($registro_x->fecha_vencimiento)))/86400;
					}
					?>
				</tbody>
			</table>
			<p align="center">
				<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
			</p>
		</form>
		<p align="center">Haga Click en Planilla y Monto de Pago para Agregar o Cambiar y presione Guardar.</p>
		<p>&nbsp;</p>
		</td>

		<p>
			<?php include "../pie.php"; ?>
		</p>
		<p>&nbsp;</p>
</body>

</html>