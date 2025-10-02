<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 146;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

// --------- BUSQUEDAS ----------
if ($_POST['ORIF'] <> "" and ($_POST['CMDBUSCAR'] == 'Buscar' or $_POST['VALIDADO'] == 'SI')) {
	$consultax = "SELECT contribuyente, direccion FROM vista_contribuyentes_direccion WHERE Rif='" . $_POST['ORIF'] . "';";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax)) {
		$Contribuyente = $registrox->contribuyente;
		$Direccion = $registrox->direccion;
	} else {
		echo "<script type=\"text/javascript\">alert('No Existe Contribuyente Registrado con ese Rif');</script>";
	}
}

// --------- IMPORTAR PLANILLAS
if ($_POST['ORIF'] <> "" and $_POST['VALIDADO'] == 'SI') {
	$bdd = $_SESSION['BDD'];
	$_SESSION['BDD'] = 'losllanos_viejo';
	mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
	// BUSQUEDA DE PLANILLAS PARA IMPORTAR
	$consulta = "SELECT * FROM ec_liquidacion WHERE rif='" . $_POST['ORIF'] . "';";
	$tabla = mysql_query($consulta);
	while ($registro = mysql_fetch_object($tabla)) {
		if ($_POST[$registro->Numero_Liquidacion] == 'Importar') {
			$_SESSION['BDD'] = $bdd;
			mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
			// VALIDAR SI YA SE AGREGO LA PLANILLA NUEVA
			$consulta_busqueda = "SELECT rif FROM liquidacion WHERE liquidacion='" . $registro->Numero_Liquidacion . "';";
			//------------------------
			$tabla_busqueda =  mysql_query($consulta_busqueda);
			if ($registro_busqueda = mysql_fetch_object($tabla_busqueda)) {
			} else {
				//------- POR SI NO TIENE LA CANTIDAD DE UT
				if ($registro->UTCifras <= 0 and $registro->Sancion <> 2009) {
					$valorut = unidad_infraccion(extrae_fecha($registro->FechaLiquidacion));
					$cantut = ($registro->MontoCifras - $registro->Montodividida) / $valorut;
				} else {
					$cantut = $registro->UTCifras - $registro->UTdividida;
				}

				//------- POR SI LLEVA CONCURRENCIA
				if ($registro->montodividida > 0) {
					$concurrencia = 2;
				} else {
					$concurrencia = 1;
				}

				//------- STATUS GESTION COBRANZA
				$status = 99;

				// ------ PARA BUSCAR EL TRIBUTO
				$consulta_tributo = "SELECT tributo FROM a_sancion WHERE id_sancion='" . $registro->Sancion . "';";
				$tabla_tributo =  mysql_query($consulta_tributo);
				$registro_tributo = mysql_fetch_object($tabla_tributo);

				// ------ INSERTAR LA PLANILLA NUEVA
				$consultaxx = "INSERT INTO liquidacion (Sector, origen_liquidacion, anno_expediente, num_expediente, rif, periodoinicio, periodofinal, id_sancion, id_tributo, monto_pagado, fecha_vencimiento, fecha_pago, monto_ut, monto_bs, concurrencia, status, usuario_transferencia_a_liq, fecha_transferencia_a_liq, aprobador_liquidacion, fecha_aprobacion_liq, fecha_impresion, secuencial, liquidacion, usuario_transferencia_a_not, fecha_transferencia_not, memo, usuario ) VALUES (
				'" . $registro->sector . "',
				'" . $registro->Origen_Liquidacion . "',
				'" . $registro->AnnoProvidencia . "',
				'" . $registro->Autorizacion . "',
				'" . $registro->Rif . "', 
				'" . ($registro->FechaInicioDeclaracion) . "', 
				'" . ($registro->FechaFinDeclaracion) . "', 
				'" . $registro->Sancion . "', 
				'" . $registro_tributo->tributo . "', 
				'" . formato_moneda2($registro->Monto_pagado) . "', 
				'" . ($registro->FechaExigibilidad) . "', 
				'" . ($registro->FechaPago) . "', 
				'" . formato_moneda2($registro->UTCifras - $registro->UTdividida) . "', 
				'" . formato_moneda2($registro->MontoCifras - $registro->Montodividida) . "', 
				'" . $concurrencia . "', 
				'" . $status . "', 
				'" . $registro->Cedula_Aprobador . "', 
				'" . ($registro->FechaLiquidacion) . "', 
				'" . $registro->Cedula_Aprobador . "', 
				'" . ($registro->FechaLiquidacion) . "', 
				'" . ($registro->FechaLiquidacion) . "', 
				'" . ($registro->Secuencial) . "', 
				'" . ($registro->Numero_Liquidacion) . "', 
				'" . ($registro->CedulaLiquidador) . "', 
				'" . extrae_fecha($registro->fechatransmision) . "', 
				'999999', 
				'" . $_SESSION['CEDULA_USUARIO'] . "');";
				//-----------------
				$tablaxx =  mysql_query($consultaxx);
				// MENSAJE
				echo "<script type=\"text/javascript\">alert('Planillas Importada Exitosamente!!!');</script>";
			}
		}
	}
	//------------------
	$_SESSION['BDD'] = $bdd;
	mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
}
?>
<html>

<head>
	<title>Importar Planillas</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	<form name="form1" method="post" action="#vista">
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="42" colspan="4" align="center">
						<p class="Estilo7"><u>Busqueda del Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td height=27 bgcolor="#CCCCCC">
						<div align="center"><strong>Rif</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><span class="Estilo1">
								<input style="text-align:center" type="text" name="ORIF" size="12" maxlength="10" value="<?php echo mayuscula($_POST['ORIF']); ?>">
								<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
							</span></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Proceder a Importar =&gt;</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><span class="Estilo7"><span class="Estilo1">
									<select name="VALIDADO" size="1">
										<?php
										echo '<option ';
										if ($_POST['VALIDADO'] == 'NO') {
											echo 'selected="selected" ';
										}
										echo ' value="NO">NO</option>';
										echo '<option ';
										if ($_POST['VALIDADO'] == 'SI') {
											echo 'selected="selected" ';
										}
										echo ' value="SI">SI</option>';
										?>
									</select>
								</span></span></div>
					</td>
				</tr>
		</table>
		<p></p>
		<table width="55%" border=1 align=center>
			<tr>
				<td bgcolor="#CCCCCC" height=27>
					<div align="center">
						<strong>Contribuyente</strong>
					</div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center">
						<?php echo $Contribuyente;		?></div>
				</td>
			</tr>
			<tr>
				<td width="23%" height=27 bgcolor="#CCCCCC">
					<div align="center"><strong>Domicilio</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center">
						<?php echo $Direccion;		?></div>
				</td>
			</tr>
		</table>
		<br>
		<a name="vista"></a>
		<table class="formateada" align="center" width="65%" border="1">
			<tbody>
				<tr class="TituloTabla">
					<td colspan="11" align="center">Planillas Disponibles (Sistema anterior)</td>
				</tr>
				<tr class="TituloCampo">
					<th>
						<div align="center"><strong>N&deg;</strong><strong></strong></div>
					</th>
					<th>
						<div align="center"><strong>Per&iacute;odo</strong></div>
					</th>
					<th>
						<div align="center"><strong>Liquidacion</strong></div>
					</th>
					<th>
						<div align="center"><strong>Monto</strong></div>
					</th>
					<th>
						<div align="center"><strong>Cant. U.T.</strong></div>
					</th>
					<th>
						<div align="center"><strong>Fecha Liquidacion</strong></div>
					</th>
					<th>
						<div align="center"><strong>A&ntilde;o Exp.</strong></div>
					</th>
					<th>
						<div align="center"><strong>Num Exp. </strong></div>
					</th>
					<th>
						<div align="center"><strong>Sector Exp.</strong></div>
					</th>
					<th>
						<div align="center"><strong>Origen Exp. </strong></div>
					</th>
					<th>
						<div align="center"><strong>Opciones</strong></div>
					</th>
				</tr>
				<?php
				$bdd = $_SESSION['BDD'];
				$_SESSION['BDD'] = 'losllanos_viejo';
				mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
				//------------------
				$consulta = "SELECT * FROM ec_liquidacion WHERE rif='" . $_POST['ORIF'] . "';"; //ORDER BY secuencial
				$tabla = mysql_query($consulta);
				$i = 0;
				while ($registro = mysql_fetch_object($tabla)) {
					$_SESSION['BDD'] = $bdd;
					mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
					// VALIDAR SI YA SE AGREGO LA PLANILLA NUEVA
					$consulta_busqueda = "SELECT rif FROM liquidacion WHERE liquidacion='" . $registro->Numero_Liquidacion . "';";
					//------------------------
					$tabla_busqueda =  mysql_query($consulta_busqueda);
					if ($registro_busqueda = mysql_fetch_object($tabla_busqueda)) {
					} else {
						$i++;
						echo '<tr id="fila' . $i . '">
<td bgcolor="#FFFFFF"><div align="center">' . $i . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . $registro->FechaInicioDeclaracion . ' al ' . $registro->FechaFinDeclaracion . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . $registro->Numero_Liquidacion . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . formato_moneda($registro->MontoCifras - $registro->Montodividida) . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . ($registro->UTCifras - $registro->UTdividida) . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . voltea_fecha($registro->FechaLiquidacion) . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . ($registro->AnnoProvidencia) . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . ($registro->Autorizacion) . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . sector($registro->sector) . '</div></td>
<td bgcolor="#FFFFFF"><div align="center">' . origen($registro->Origen_Liquidacion) . '</div></td>';
						echo ' <td  bgcolor="#FFFFFF"><div align="center"><input name="' . $registro->Numero_Liquidacion . '" type="submit" value="Importar" ';
						echo ' ></div></td>	</tr>';
					}
				}
				//}
				//--------------------
				$_SESSION['BDD'] = $bdd;
				mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
				?>
			</tbody>
		</table>
		<br>
		<table align="center" width="65%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="46" colspan="12" align="center"><u>Planillas en el Sistema</u></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N&deg;</strong><strong></strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Per&iacute;odo</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>A&ntilde;o Exp.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Num Exp. </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Sector Exp.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Origen Exp. </strong></div>
				</td>
				<td colspan="2" bgcolor="#CCCCCC">
					<div align="center"><strong>Status</strong></div>
				</td>
			</tr>
			<?php

			//$consulta = "SELECT liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, fecha_pag FROM liquidacion WHERE id_sancion<>2500 AND id_sancion<>2501 AND id_sancion<>2009 AND id_sancion<100000 AND rif='".$_POST['ORIF']."';";
			$consulta = "SELECT * FROM liquidacion, a_status_liquidacion WHERE cod_status=status AND  rif='" . $_POST['ORIF'] . "';"; //status=20 AND 
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
						<div align="center"> <?php echo $registro->periodoinicio . ' al ' . $registro->periodofinal;		?></div>
					</td>
					<td>
						<div align="center"> <?php echo ($registro->liquidacion);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo formato_moneda($registro->monto_bs / $registro->concurrencia * $registro->especial);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo ($registro->monto_ut / $registro->concurrencia * $registro->especial);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo voltea_fecha($registro->fecha_impresion);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo ($registro->anno_expediente);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo ($registro->num_expediente);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo sector($registro->sector);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo origen($registro->origen_liquidacion);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo ($registro->status_descripcion);		?></div>
					</td>
					<td>
						<div align="center"> <?php echo ($registro->status_siguiente);		?></div>
					</td>
				</tr><?php	}
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