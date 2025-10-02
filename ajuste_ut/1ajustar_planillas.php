<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 83;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
?>
<html>

<head>
	<title>Ajustar Planillas</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />

	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
		<?php
		// --------- BUSQUEDAS ----------
		if ($_POST['ORIF'] <> "") {
			list($Contribuyente, $Direccion) = funcion_contribuyente($_POST['ORIF']);
		}

		// --------- AJUSTAR PLANILLAS
		if ($Contribuyente <> "") {
			// BUSQUEDA DE PLANILLAS PARA AJUSTAR
			$consulta = "SELECT id_sancion, id_liquidacion, liquidacion, status, fecha_impresion, ((monto_bs*concurrencia)/especial) as monto, fecha_vencimiento FROM liquidacion WHERE id_sancion<>2500 AND id_sancion<>2009 AND (liquidacion.status = 99 or liquidacion.status >=20 or liquidacion.status = 100) AND rif='" . $_POST['ORIF'] . "' AND ajustada=0;";  //AND id_sancion<>2501 
			$tabla =  mysql_query($consulta);
			while ($registro = mysql_fetch_object($tabla)) {
				if ($_POST[$registro->id_liquidacion] == 'Ajustar') {
					// ------ POR SI NO TIENE FECHA
					if ($_POST['FECHA' . $registro->id_liquidacion] == '0000/00/00') {
						$_POST['FECHA' . $registro->id_liquidacion] = date('d/m/Y');
					}

					//--------------
					$fecha_pago = fecha_a_numero(voltea_fecha($_POST['FECHA' . $registro->id_liquidacion]));
					$fecha_liq = fecha_a_numero($registro->fecha_impresion);

					// ------ VALIDACION FECHAS
					if ($fecha_liq >= $fecha_pago) {
						echo "<script type=\"text/javascript\">alert('�La Fecha de Pago no puede ser menor o igual a la fecha de Liquidaci\u00D3n!');</script>";
					} else {
						if ($registro->id_sancion > 60000 or (($registro->id_sancion == 10683 or $registro->id_sancion == 15449 or $registro->id_sancion == 11502 or $registro->id_sancion == 15567 or $registro->id_sancion == 11507 or $registro->id_sancion == 11508 or $registro->id_sancion == 11509 or $registro->id_sancion == 11503) and fecha_a_numero($registro->fecha_vencimiento) >= fecha_a_numero('2020/03/01'))) // or $registro->id_sancion<3649
						{
							// ------ CALCULO DEL VALOR DE LA MONEDA AL DIA DEL PAGO
							$valor_ut_pago = moneda_infraccion(voltea_fecha($_POST['FECHA' . $registro->id_liquidacion]));
							//---------------------------------

							// ------ VALIDACION QUE NO EXISTA AJUSTE
							$consultax = "SELECT monto_bs/monto_ut as valor_ut_infraccion FROM liquidacion WHERE id_liquidacion=" . $registro->id_liquidacion . ";";
							$tablax =  mysql_query($consultax);
							echo $consultax;
							$registrox = mysql_fetch_object($tablax);
							if ($registrox->valor_ut_infraccion <> $valor_ut_pago) {
								//-----------------
								$consultax = "INSERT INTO liquidacion (status, usuario, sector, origen_liquidacion, anno_expediente, num_expediente, rif, periodoinicio, periodofinal, id_sancion, id_tributo, planilla, monto_pagado, fecha_presentacion, fecha_vencimiento, fecha_pago, monto_ut, monto_bs, concurrencia, especial, reiteracion, reiteracion_resolucion,  id_liq_primitiva ) SELECT '1', '" . $_SESSION['CEDULA_USUARIO'] . "', '" . $_SESSION['SEDE_USUARIO'] . "','" . $origenUT . "','0','0','" . mayuscula($_POST['ORIF']) . "', periodoinicio, periodofinal, id_sancion+100000, id_tributo, planilla, monto_pagado, fecha_presentacion, fecha_vencimiento, fecha_pago, monto_ut, ((" . $valor_ut_pago . "*monto_ut)-(monto_bs)), concurrencia, especial, reiteracion, reiteracion_resolucion, id_liquidacion FROM liquidacion WHERE id_liquidacion=" . $registro->id_liquidacion . ";";
								//echo $consultax;
								$tablax =  mysql_query($consultax);
								// ------ ACTUALIZAR EL REGISTRO SELECCIONADO
								$consultax = "UPDATE liquidacion SET fecha_pag='" . voltea_fecha($_POST['FECHA' . $registro->id_liquidacion]) . "', status=100, ajustada = 1, fecha_ajuste=date(now()), usuario_ajuste=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion='" . $registro->id_liquidacion . "';";
								$tablax =  mysql_query($consultax);
								// ------ ACTUALIZAR LAS PLANILLAS IMPORTADAS
								//$consultax = "UPDATE a_sancion, liquidacion SET liquidacion.id_tributo=tributo WHERE liquidacion.id_tributo<=0 AND a_sancion.id_sancion_ajuste = liquidacion.id_sancion AND (liquidacion.origen_liquidacion = 7 or liquidacion.origen_liquidacion = 16);";
								//$tablax =  mysql_query($consultax);
								//------------------------
								echo "<script type=\"text/javascript\">alert('La Planilla " . $registro->liquidacion . " fue Ajustada Exitosamente');</script>";
							} else {
								echo "<script type=\"text/javascript\">alert('La Planilla " . $registro->liquidacion . " No necesita Ajuste de U.T.!');</script>";
							}
						} else {
							// ------ CALCULO DEL VALOR DE LA UNIDAD DE PAGO
							$valor_ut_pago = unidad_infraccion(voltea_fecha($_POST['FECHA' . $registro->id_liquidacion]));
							//---------------------------------

							// ------ VALIDACION QUE NO EXISTA AJUSTE
							$consultax = "SELECT monto_bs/monto_ut as valor_ut_infraccion FROM liquidacion WHERE id_liquidacion=" . $registro->id_liquidacion . ";";
							$tablax =  mysql_query($consultax); //echo $consultax;
							$registrox = mysql_fetch_object($tablax);
							if ($registrox->valor_ut_infraccion < $valor_ut_pago) {
								//-----------------
								$consultax = "INSERT INTO liquidacion (status, usuario, sector, origen_liquidacion, anno_expediente, num_expediente, rif, periodoinicio, periodofinal, id_sancion, id_tributo, planilla, monto_pagado, fecha_presentacion, fecha_vencimiento, fecha_pago, monto_ut, monto_bs, concurrencia, especial, reiteracion, reiteracion_resolucion,  id_liq_primitiva ) SELECT '1', '" . $_SESSION['CEDULA_USUARIO'] . "', '" . $_SESSION['SEDE_USUARIO'] . "','" . $origenUT . "','0','0','" . mayuscula($_POST['ORIF']) . "', periodoinicio, periodofinal, id_sancion+100000, id_tributo, planilla, monto_pagado, fecha_presentacion, fecha_vencimiento, fecha_pago, monto_ut, ((" . $valor_ut_pago . "*monto_ut)-(monto_bs)), concurrencia, especial, reiteracion, reiteracion_resolucion, id_liquidacion FROM liquidacion WHERE id_liquidacion=" . $registro->id_liquidacion . ";";
								//echo $consultax;
								$tablax =  mysql_query($consultax);
								// ------ ACTUALIZAR EL REGISTRO SELECCIONADO
								$consultax = "UPDATE liquidacion SET fecha_pag='" . voltea_fecha($_POST['FECHA' . $registro->id_liquidacion]) . "', status=100, ajustada = 1, fecha_ajuste=date(now()), usuario_ajuste=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_liquidacion='" . $registro->id_liquidacion . "';";
								$tablax =  mysql_query($consultax);
								// ------ ACTUALIZAR LAS PLANILLAS IMPORTADAS
								//$consultax = "UPDATE a_sancion, liquidacion SET liquidacion.id_tributo=tributo WHERE liquidacion.id_tributo<=0 AND a_sancion.id_sancion_ajuste = liquidacion.id_sancion AND (liquidacion.origen_liquidacion = 7 or liquidacion.origen_liquidacion = 16);";
								//$tablax =  mysql_query($consultax);
								//------------------------
								echo "<script type=\"text/javascript\">alert('La Planilla " . $registro->liquidacion . " fue Ajustada Exitosamente');</script>";
							} else {
								echo "<script type=\"text/javascript\">alert('La Planilla " . $registro->liquidacion . " No necesita Ajuste de U.T.!');</script>";
							}
						}
					}
				}
			}

			//--------------------------------------------- BUSQUEDA DE PLANILLAS PARA ELIMINAR
			$consulta = "SELECT id_liquidacion, id_liq_primitiva, liquidacion FROM liquidacion WHERE rif='" . $_POST['ORIF'] . "' AND ajustada=0 AND (origen_liquidacion=7 OR origen_liquidacion=16);";
			$tabla =  mysql_query($consulta);
			while ($registro = mysql_fetch_object($tabla)) {
				if ($_POST[$registro->id_liquidacion] == 'Eliminar') {
					// ------ ELIMINAR LA PLANILLA NUEVA
					$consultax = "DELETE FROM liquidacion WHERE id_liquidacion='" . $registro->id_liquidacion . "';";
					$tablax = mysql_query($consultax);
					// ------
					// ------ ACTUALIZAR LA PLANILLA PRIMITIVA
					$consultax = "UPDATE liquidacion SET ajustada = 0 WHERE id_liquidacion='" . $registro->id_liq_primitiva . "';";
					$tablax = mysql_query($consultax);
					// ------		
					echo "<script type=\"text/javascript\">alert('La Planilla " . $registro->liquidacion . " fue Eliminada Exitosamente');</script>";
				}
			}
		}
		?>

	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>

	<form name="form1" method="post" action="">
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="24" colspan="6" align="center">
						<p class="Estilo7"><u>Datos del Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Rif</strong></div>
					</td>
					<td colspan="4" bgcolor="#CCCCCC">
						<div align="center"><strong>Contribuyente</strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFFF" colspan="1" height=27>
						<div align="center">
							<input style="text-align:center" type="text" name="ORIF" size="12" maxlength="10" value="<?php echo mayuscula($_POST['ORIF']); ?>">
							<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
						</div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $Contribuyente;		?></div>
					</td>
				</tr>
				<tr>
					<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Domicilio</strong></div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $Direccion;		?></div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="8" align="center">
					<p class="Estilo7"><u>Planillas Disponibles</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Planilla</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha de Pago</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Opciones</strong></div>
				</td>
			</tr>
			<?php

			$consulta = "SELECT status, liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, fecha_pag FROM liquidacion WHERE id_sancion<>2500 AND id_sancion<>2009 AND (liquidacion.status = 99 or liquidacion.status >=20 or liquidacion.status = 100) AND rif='" . $_POST['ORIF'] . "' AND ajustada=0;";
			$tabla = mysql_query($consulta);

			$i = 0;

			while ($registro = mysql_fetch_object($tabla)) {
				$i++;
			?>
				<tr>
					<td>
						<div align="center">
							<?php echo $i;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo $registro->liquidacion;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->monto);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->cant_ut);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php		//echo unidad_infraccion($registro->fecha_impresion); 
							echo minimo_soberano(formato_moneda($registro->monto / $registro->cant_ut, 1));		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo voltea_fecha($registro->fecha_impresion);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php		//if ($registro->status==99) {		
							?>
							<input type="OFECHA" name="FECHA<?php echo $registro->id_liquidacion;	?>" <?php if ($registro->status <> 99) {
																												echo 'readonly=""';
																											} ?> value="<?php if ($_POST['FECHA' . $registro->id_liquidacion] <> '') {
																																													echo $_POST['FECHA' . $registro->id_liquidacion];
																																												} else {
																																													echo voltea_fecha($registro->fecha_pag);
																																												}	?>" size="10" onclick='javascript:scwShow(this,event);'>
							<?php		//}	else {	echo voltea_fecha($registro->fecha_pag);	}	
							?>
						</div>
					</td>
					<td>
						<div align="center">
							<input type="submit" class="boton" name="<?php echo $registro->id_liquidacion;		?>" value="Ajustar">
						</div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="9" align="center">
					<p class="Estilo7"><u>Planillas Ajustadas</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Planilla Original</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T.Original</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Original</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T. Aplicada</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Actual</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto Diferencia</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Opciones</strong></div>
				</td>
			</tr>

			<?php

			$consulta = "SELECT id_liq_primitiva, liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion FROM liquidacion WHERE status=1 AND rif='" . $_POST['ORIF'] . "' AND (origen_liquidacion=7 or origen_liquidacion=16);";
			$tablax = mysql_query($consulta);

			$i = 0;

			while ($registrox = mysql_fetch_object($tablax)) {
				// DATOS DE LA PLANILLA ORIGINAL
				$consulta = "SELECT liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion FROM liquidacion WHERE id_liquidacion=" . $registrox->id_liq_primitiva . ";";
				$tablaxx = mysql_query($consulta);
				$registroxx = mysql_fetch_object($tablaxx);
				// -----------------------------
				$i++;
				echo '<tr>
		  <td bgcolor="#FFFFFF"><div align="center">' . $i . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . $registroxx->liquidacion . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registroxx->cant_ut) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . minimo_soberano(formato_moneda($registroxx->monto / $registroxx->cant_ut, 1)) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="right">' . formato_moneda($registroxx->monto) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . redondea(($registroxx->monto + $registrox->monto) / $registroxx->cant_ut) . '</div></td>		  
		  <td bgcolor="#FFFFFF"><div align="right">' . formato_moneda($registroxx->monto + $registrox->monto) . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="right">' . formato_moneda($registrox->monto) . '</div></td>
		  ';
				echo ' <td width="94" bgcolor="#FFFFFF"><div align="center"><input name="' . $registrox->id_liquidacion . '" type="submit" value="Eliminar" ';
				echo '</tr>';
			}

			?>
		</table>
		<p>&nbsp;</p>
		<p><br>
		</p>
	</form>

	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>