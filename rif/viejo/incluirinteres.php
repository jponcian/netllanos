<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$_SESSION['VARIABLE'] = 'NO';

?>

<html>

<head>
	<title>Incluir Intereses</title>
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

	.Estilo20 {
		font-size: 20px
	}

	.Estilo16 {
		color: #FF0000
	}
	-->
</style>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>

	<?php

	// PARA ELIMINAR INTERESES ------------------------------------
	if ($_POST['CMDELIMINAR'] and isset($_POST['IdsExpedientes'])) {
		foreach ($_POST['IdsExpedientes'] as $IdExpediente) {
			// CONSULTA PARA ELIMINAR
			$consulta = "DELETE FROM liquidacion WHERE id_liquidacion=" . $IdExpediente;
			$tabla = mysql_query($consulta);
		}
	}
	// FIN ------------------------------------

	// PARA REVISAR SI EXISTE
	$consulta = "SELECT anno, numero, rif, sector, contribuyente, funcionario, nombrefuncionario, coordinador, status, nombrecoordinador, FechaRegistro as FechaRegistro1, FechaRegistro as FechaRegistro2 FROM vista_sel_exp_especiales WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . ";";

	$tabla = mysql_query($consulta);

	if ($registro = mysql_fetch_object($tabla)) {
		// CONTRIBUYENTE POR INCLUIR
		if ($registro->Rif == "J000000000") {
			header("Location: incluirsancion_sel_pro_int.php?errorusuario=cpi");
			exit();
		}
		// SI EST� APROBADA
		if ($registro->status == 1) {
			header("Location: incluirsancion_sel_pro_int.php?errorusuario=expa");
			exit();
		}
		// SI EST� ANULADA
		if ($registro->status == 9) {
			header("Location: incluirsancion_sel_pro_int.php?errorusuario=ea");
			exit();
		}
		// VERIFICAR SI NO ES EL ADMINISTRADOR
		/*if ($_SESSION['ADMINISTRADOR'] <> 1)
		{
		// FISCAL NO AUTORIZADO	
		if ($registro->Funcionario<>$_SESSION['CEDULA_USUARIO'] and $registro->Coordinador<>$_SESSION['CEDULA_USUARIO'])
		{header ("Location: incluirsancion_sel_pro_int.php?errorusuario=una"); 
		exit();}
		}*/

		// CORRECTO
		$_SESSION['RIF'] = $registro->rif;
		$_SESSION['OSEDE'] = $registro->sector;
		$FechaRegistro = $registro->FechaRegistro1;
		$FechaRegistro2 = $registro->FechaRegistro2;
	} else {
		header("Location: incluirsancion_sel_pro_int.php?errorusuario=neexp");
		exit();
	}

	// ----------- CALCULO DEL INTERES Y/O GUARDADO
	if ($_POST['CMDCALCULAR'] == 'Calcular' or $_POST['CMDAGREGAR'] == 'Agregar') {
		if ($_POST['OFECHAINICIO'] <> "" & $_POST['OFECHAFIN'] <> "" & $_POST['OFECHAVEN'] <> "" & $_POST['OFECHAPAGO'] <> "" & $_POST['OMONTO'] <> "") {
			// CALCULO DE LOS INTERESES Y LA MULTA DEL 10%
			list($dia, $mes, $anno) = explode('/', $_POST['OFECHAPAGO']);
			$FECHA_PAGO = mktime(0, 0, 0, $mes, $dia, $anno);

			list($dia, $mes, $anno) = explode('/', $_POST['OFECHAVEN']);
			$FECHA_VENCIMIENTO = mktime(0, 0, 0, $mes, $dia, $anno);

			$Dias = $FECHA_PAGO - $FECHA_VENCIMIENTO;
			$Dias_Total = $Dias_Total + $Dias;

			$txt = $Dias / 86400;

			// CALCULO DEL INTERES-------------------------------------------------------------------
			$FECHA_VENCIMIENTO = $FECHA_VENCIMIENTO + 86400;
			$INTERES = 0;
			// CONSULTA DE LAS TAZAS
			$consulta_y = "SELECT * FROM a_tasa_interes ORDER BY anno";
			$tabla_y = mysql_query($consulta_y);
			$registro_y = mysql_fetch_object($tabla_y);
			// FIN CONSULTA DE LAS TAZAS
			while ($FECHA_VENCIMIENTO <= $FECHA_PAGO) {
				while ($registro_y->anno < date('Y', $FECHA_VENCIMIENTO)) // BUSQUEDA DEL A�O
				{
					$registro_y = mysql_fetch_object($tabla_y);
					$tazas = array('0', $registro_y->enero, $registro_y->febrero, $registro_y->marzo, $registro_y->abril, $registro_y->mayo, $registro_y->junio, $registro_y->julio, $registro_y->agosto, $registro_y->septiembre, $registro_y->octubre, $registro_y->noviembre, $registro_y->diciembre);
				} 													// FIN DE LA BUSQUEDA DEL A�O
				$tasa = $tazas[number_format(doubleval(date('m', $FECHA_VENCIMIENTO)), 0, '', '')];
				$INTERES = $INTERES + (($_POST['OMONTO'] * ($tasa * 1.20)) / 36000);
				$_POST['OINTERES'] = round($INTERES, 2);
				$FECHA_VENCIMIENTO = $FECHA_VENCIMIENTO + 86400;
			}
			// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------
			// AGREGAR LA SANCION A LIQUIDACION -----------------------------------------------------
			$SANCION = 2009;
			// BUSQUEDA DE LA SANCION A APLICAR
			$consulta = 'SELECT * FROM a_sancion WHERE (((id_sancion)=' . $SANCION . '));';
			$tabla_x = mysql_query($consulta);
			$registro_x = mysql_fetch_object($tabla_x);
			$Ley = $registro_x->Ley;
			$Numeral = $registro_x->Numeral;
			$Literal = $registro_x->Literal;
			$Art_COT = $registro_x->Art_COT;
			$Art_Ley_Rgto = $registro_x->Art_Ley_Rgto;
			$Art_Regla = $registro_x->Art_Regla;
			$Serie = $registro_x->Serie;
			$Concepto = $registro_x->Concepto;
			// ----------- OFICINA DE EMISION
			/*$consulta_x = "SELECT Cod_Oficina_liqui FROM Dependencias WHERE (((Cod_Oficina_liqui)<>'')) ORDER BY Cod_Oficina_liqui DESC;";
				$tabla_x = mysql_query($consulta_x);
				$registro_x = mysql_fetch_object($tabla_x);*/
			$Oficina = 02;
			// --------------------			
			// --------------------
			if ($_POST['CMDAGREGAR'] == 'Agregar') {
				$sql_existe = "SELECT * FROM vista_sanciones_aplicadas WHERE periodoinicio='" . voltea_fecha($_POST['OFECHAINICIO']) . "' AND periodofinal='" . voltea_fecha($_POST['OFECHAFIN']) . "' AND rif='" . $_SESSION['RIF'] . "' AND id_sancion=2009 AND id_tributo=" . $_POST['OTRIBUTO'];
				$tabla_existe = mysql_query($sql_existe);
				$existe = mysql_num_rows($tabla_existe);
				if ($existe < 1) {
					$MONTO = $_POST['OMONTO'];
					$UT_APLICADAS = 0;
					// INSERTAR EL REGISTRO DEL ACTA DE REPARO EN LIQUIDACION
					$consulta = "INSERT INTO liquidacion (sector, origen_liquidacion, anno_expediente, num_expediente, rif, periodoinicio, periodofinal, id_sancion, fecha_vencimiento, fecha_pago, monto_ut, monto_bs, monto_pagado, id_tributo, status) VALUES (" . $_SESSION['OSEDE'] . ", 2, " . $_SESSION['ANNO_PRO'] . ", " . $_SESSION['NUM_PRO'] . ", '" . $_SESSION['RIF'] . "', '" . voltea_fecha($_POST['OFECHAINICIO']) . "', '" . voltea_fecha($_POST['OFECHAFIN']) . "', 2009, '" . voltea_fecha($_POST['OFECHAVEN']) . "', '" . voltea_fecha($_POST['OFECHAPAGO']) . "', " . $UT_APLICADAS . ", " . $_POST['OINTERES'] . ", " . $_POST['OMONTO'] . ", " . $_POST['OTRIBUTO'] . ", 0)";
					if ($tabla = mysql_query($consulta)) {
						$_SESSION['VARIABLE'] = 'SI';
						echo "<script type=\"text/javascript\">alert('���Interes Cargado Exitosamente!!!');</script>";
					}
					// --------------------
				} else {
					$registro_e = mysql_fetch_object($tabla_existe);
					//---------------------------------
					echo "<script type=\"text/javascript\">alert('���Sancion Duplicada!!!');</script>";
					echo "<script type=\"text/javascript\">alert('���Dependencia=> " . $registro_e->dependencia . '  /  Area=> ' . $registro_e->area . '\n A�o=> ' . $registro_e->anno_expediente . ' / Expediente o Providencia=> ' . $registro_e->num_expediente . "!!!');</script>";
					//---------------------------------
				}
			}
			// FIN LA SANCION A LIQUIDACION ---------------------------------------------------------		
		} else {
			echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
		}
	}

	// ----------- FIN DEL GUARDADO
	?>
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post" action="">

		<table width="45%" border="1" align="center">
			<tr>
				<td height="30" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente </u></span></td>
			</tr>
			<tr>
				<td width="10%" height="25" bgcolor="#CCCCCC">
					<div align="center"><strong>A�o:</strong></div>
				</td>
				<td width="7%">
					<div align="center"><span class="Estilo15"><?php echo $registro->anno; ?></span></div>
				</td>
				<td width="12%" bgcolor="#CCCCCC">
					<div align="center"><strong>N&uacute;mero:</strong></div>
				</td>
				<td width="7%">
					<div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span></div>
				</td>
				<td width="9%" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha:</strong></div>
				</td>
				<td width="12%">
					<div align="center"><span class="Estilo15"><?php echo date("d-m-Y", strtotime($registro->FechaRegistro2)); ?></span></div>
				</td>
				<td width="11%" bgcolor="#CCCCCC">
					<div align="center"><strong>Sector:</strong></div>
				</td>
				<td width="35%">
					<div align="center"><span class="Estilo20"> <?php
																$SEDES = array('', 'Calabozo', 'San Juan', 'San Fernando', 'Altagracia', 'Valle de la Pascua');
																echo $SEDES[$registro->sector];		?></span></div>
				</td>
			</tr>
		</table>

		<table width="45%" border="1" align="center">
			<tr>
				<td width="6%" bgcolor="#CCCCCC">
					<div align="center"><strong>Rif: </strong></div>
				</td>
				<td width="16%"><label>
						<div align="center"><span class="Estilo15"><?php echo formato_rif($registro->rif); ?></span></div>
					</label></td>
				<td width="19%" bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente:</strong></div>
				</td>
				<td width="59%"><label><span class="Estilo15"><?php echo $registro->contribuyente; ?></span></label></td>
			</tr>
		</table>
		<table width="45%" border="1" align="center">
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Cedula:</strong></div>
				</td>
				<td width="13%"><label>
						<div align="center"><span class="Estilo15"><?php echo $registro->coordinador; ?></span></div>
					</label></td>
				<td width="9%" bgcolor="#CCCCCC">
					<div align="center"><strong>Coodinador:</strong></div>
				</td>
				<td width="68%"><label><span class="Estilo15"><?php echo $registro->nombrecoordinador; ?></span></label></td>
			</tr>
		</table>
		<table width="45%" border="1" align="center">
			<tr>
				<td width="10%" bgcolor="#CCCCCC">
					<div align="center"><strong>Cedula:</strong></div>
				</td>
				<td width="13%"><label>
						<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->funcionario); ?></span></div>
					</label></td>
				<td width="9%" bgcolor="#CCCCCC">
					<div align="center"><strong>Funcionario:</strong></div>
				</td>
				<td width="68%"><label><span class="Estilo15"><?php echo $registro->nombrefuncionario; ?></span></label></td>
			</tr>
		</table>


		<p></p>
		<table width="45%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Datos del Inter&eacute;s a Calcular </u></span></td>
			</tr>
			<tr>
				<td width="16%" bgcolor="#CCCCCC">
					<div align="center"><strong>Tributo:</strong></div>
				</td>
				<td width="17%" colspan="3"><select name="OTRIBUTO" size="1">
						<option value="-1">Seleccione</option>
						<?php
						$consulta2 = "SELECT id_tributo, nombre FROM a_tributos WHERE id_tributo in (1,3,8,11,13) ORDER BY id_tributo;";
						$tabla2 = mysql_query($consulta2);
						while ($registro2 = mysql_fetch_object($tabla2)) {
							echo '<option';
							if ($_POST['OTRIBUTO'] == $registro2->id_tributo) {
								echo ' selected="selected" ';
							}
							echo ' value="';
							echo $registro2->id_tributo;
							echo '">';
							echo $registro2->nombre;
							echo '</option>';
						}
						?>
					</select></td>

			</tr>
			<tr>
				<td width="16%" bgcolor="#CCCCCC">
					<div align="center"><strong>Per&iacute;odo Inicio: </strong></div>
				</td>
				<td width="17%"><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAINICIO" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																			echo $_POST['OFECHAINICIO'];
																																		} ?>">
					</label>
					</div>
				</td>
				<td width="16%" bgcolor="#CCCCCC">
					<div align="center"><strong>Per&iacute;odo Fin:</strong></div>
				</td>
				<td width="18%"><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAFIN" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																		echo $_POST['OFECHAFIN'];
																																	} ?>">
					</label>
					</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Vencimiento: </strong></div>
				</td>
				<td>
					<div align="center"><label>
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAVEN" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																		echo $_POST['OFECHAVEN'];
																																	} ?>"></label></div>
				</td>

				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Pago :</strong></div>
				</td>
				<td><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAPAGO" size="10" readonly value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																																			echo $_POST['OFECHAPAGO'];
																																		} ?>">
					</label></div>
				</td>
			</tr>
			<tr>
				<td width="16%" bgcolor="#CCCCCC">
					<div align="center"><strong>Monto:</strong></div>
				</td>
				<td width="17%"><label>
						<div align="center">
							<input type="text" name="OMONTO" maxlength="10" size="10" value="<?php if ($_SESSION['VARIABLE'] == 'NO') {
																									echo $_POST['OMONTO'];
																								} ?>">
						</div>
				</td>

				<td width="17%" bgcolor="#CCCCCC">
					<div align="center"><strong>Interes: </strong></div>
				</td>
				<td width="16%">
					<div align="center"><?php if ($_SESSION['VARIABLE'] == 'NO') {
											echo number_format(doubleval($INTERES), 2, ',', '.');
										} ?></div>
				</td>
			</tr>
		</table>
		<!--<p align="center"><span class="Estilo16">&lt; Para separar los decimales utilice el PUNTO "." ></span></p>-->
		<p align="center">
			<input type="submit" class="boton" name="CMDCALCULAR" value="Calcular">
			<input type="submit" class="boton" name="CMDAGREGAR" value="Agregar">
		</p>

		<table width="45%" border=1 align=center>
			<tbody>

				<tr>
					<td bgcolor="#FF0000" height="27" colspan="11" align="center">
						<p class="Estilo7"><u>Intereses actuales aplicados al Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" width="2%">
						<div align="center" class="Estilo8">#</div>
					</td>
					<td bgcolor="#CCCCCC" width="10%">
						<div align="center" class="Estilo8">Tributo</div>
					</td>
					<td bgcolor="#CCCCCC" width="12%">
						<div align="center" class="Estilo8">Fecha Inicio</div>
					</td>
					<td bgcolor="#CCCCCC" width="12%">
						<div align="center" class="Estilo8">Fecha Fin</div>
					</td>
					<td bgcolor="#CCCCCC" width="12%">
						<div align="center" class="Estilo8">Fecha Ven</div>
					</td>
					<td bgcolor="#CCCCCC" width="20%">
						<div align="center" class="Estilo8">Monto</div>
					</td>
					<td bgcolor="#CCCCCC" width="12%">
						<div align="center" class="Estilo8">Fecha Pago</div>
					</td>
					<td bgcolor="#CCCCCC" width="20%">
						<div align="center" class="Estilo8">Interes</div>
					</td>
				</tr>

				<?php

				$consulta_m = "SELECT liquidacion.id_liquidacion, a_tributos.siglas, a_tributos.nombre, liquidacion.periodoinicio, liquidacion.periodofinal, liquidacion.fecha_vencimiento, liquidacion.monto_bs, liquidacion.fecha_pago, liquidacion.monto_pagado, a_sancion.serie FROM liquidacion INNER JOIN a_tributos ON a_tributos.id_tributo = liquidacion.id_tributo INNER JOIN a_sancion ON a_sancion.id_sancion = liquidacion.id_sancion WHERE liquidacion.origen_liquidacion = 2 and anno_expediente=" . $_SESSION['ANNO_PRO'] . " and num_expediente=" . $_SESSION['NUM_PRO'] . " and status=0 and serie=38 ORDER BY liquidacion.id_sancion ASC, liquidacion.periodoinicio ASC, liquidacion.periodofinal ASC";

				$tabla = mysql_query($consulta_m);

				$z = 0;

				while ($registro = mysql_fetch_object($tabla)) {
					$z++;
					echo '<tr> <td bgcolor="#FFFFFF" width="8%" height=27><div align="center" class="Estilo8"> <input type="checkbox" name="IdsExpedientes[]"';
					echo '" value="';
					echo  $registro->id_liquidacion;
					echo '" /></div></td><td bgcolor="#FFFFFF" ><div align="center" class="Estilo8">';
					echo $registro->siglas;
					echo '</div></td><td ><div align="center">';
					echo date("d-m-Y", strtotime($registro->periodoinicio));
					echo '</div></td><td bgcolor="#FFFFFF" ><div align="center" class="Estilo8 Estilo1">';
					echo date("d-m-Y", strtotime($registro->periodofinal));
					echo '</div></td><td ><div align="center">';
					echo date("d-m-Y", strtotime($registro->fecha_vencimiento));
					echo '</div></td><td><div align="center">';
					echo number_format(doubleval($registro->monto_pagado), 2, ',', '.');
					echo '</div></td><td><div align="center">';
					echo date("d-m-Y", strtotime($registro->fecha_pago));
					echo '</div></td><td><div align="center" class="Estilo5">';
					echo number_format(doubleval($registro->monto_bs), 2, ',', '.');
					echo '</div></td></tr>';
				}
				$_SESSION['VARIABLE1'] = $z;
				?>
			</tbody>
		</table>

		<p></p>
		<div align="center">
			<input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />

		</div>
		<input type="hidden" name="OINTERES" value="<?php echo $_POST['OINTERES']; ?>"
			</form>

		<p>&nbsp; </p>
		<p>
			<?php include "../pie.php"; ?>
		</p>
		<p>&nbsp;</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>