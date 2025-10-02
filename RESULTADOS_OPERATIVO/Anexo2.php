<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Resultados Operativos Anexo 2</title>
	<script language="javascript" script type="text/javascript" src="datetimepicker_css.js">
	</script>
	<style type="text/css">
		<!--
		body,
		td,
		th {
			font-family: Tahoma, Geneva, sans-serif;
			font-weight: bold;
			color: #000;
			font-size: 12px;
		}

		body {
			background-image: url();
		}
		-->
	</style>
</head>

<body style="background: transparent !important;">
	<form method="post" name="Logar" action="">

		<?php

		$buscar = "0";
		$añoprov = $_POST['añoProvidencia'];
		$numprov = $_POST['numProvidencia'];
		if ($añoprov == "") {
			$buscar = "1";
		}
		if ($numprov == "") {
			$buscar = "1";
		}

		if ($_POST['añoProvidencia'] != "") {
			if ($buscar == "0") {
				$año = $_POST['añoProvidencia'];
				$numero = $_POST['numProvidencia'];
				//echo $año.",".$numero.",".$añoprov.",".$numprov;

				if ($conn_access = odbc_connect("LLANOS", "Administrador", "losllanos")) {
					$Bssql = "SELECT Anno_Providencia,NroAutorizacion FROM Anexo2 WHERE Anno_Providencia=" . $año . " and NroAutorizacion=" . $numero . "";
					if ($rs_access = odbc_exec($conn_access, $Bssql)) {
						if ($fila = odbc_fetch_object($rs_access)) {
							//
							echo "<script type=\"text/javascript\">
								alert('Providencia YA Registrada, por favor verifique');
								</script>";
							$numprov = "";
						} else {
							$Pssql = Consultar_providencia($año, $numero);
							if ($brs_access = odbc_exec($conn_access, $Pssql)) {
								if ($fila_reg = odbc_fetch_object($brs_access)) {
									$db_año = $fila_reg->Anno;
									$db_numero = $fila_reg->NroAutorizacion;
									$db_emision = date("Y-m-d", strtotime($fila_reg->FechaEmision));
									$db_notificacion = date("d-m-Y", strtotime($fila_reg->FechaNotificacion));
									$db_nombre = $fila_reg->NombreRazon;
									$db_rif = $fila_reg->Rif;
									$db_domicilio = $fila_reg->Domicilio;
									$db_cedfiscal = $fila_reg->CedulaFiscal;
									$db_fiscal = $fila_reg->Fiscal;
									$db_cedsuper = $fila_reg->CedulaSupervisor;
									$db_super = $fila_reg->Supervisor;
									$db_programa = $fila_reg->Programa;
									$db_tributo = $fila_reg->Tributo;
									$sql_coord = "Select * FROM Coordinador";
									if ($coord_access = odbc_exec($conn_access, $sql_coord)) {
										if ($fila_coord = odbc_fetch_object($coord_access)) {
											$CI_Coord = $fila_coord->Cedula;
											$NOM_Coord = $fila_coord->Apellidos . " " . $fila_coord->Nombres;
											$TLF_Coord = $fila_coord->Telefono;
										}
									}
									//echo $CI_Coord."--".$NOM_Coord."--".$TLF_Coord;
								} else {
									//
									echo "<script type=\"text/javascript\">
									alert('Providencia NO Notificada, por favor verifique');
									</script>";
									$numprov = "";
								}
							}
						}
					}
				}
			}
		}
		?>
		<?php
		function Consultar_providencia($año, $numero)
		{
			$CSQL = "SELECT * FROM CS_Notificadas WHERE NroAutorizacion=" . $numero . " and Anno=" . $año . "";
			return $CSQL;
		}
		?>

		<?php
		$guardar = "0";
		$añoprov = $_POST['añoProvidencia'];
		$numprov = $_POST['numProvidencia'];
		$emision = $_POST['Emision'];
		$notificacion = $_POST['Notificacion'];
		$nombre = $_POST['Nombre'];
		$rif = $_POST['Rif'];
		$domicilio = strtoupper($_POST['Domicilio']);
		$cedfiscal = $_POST['CedFiscal'];
		$fiscal = $_POST['Fiscal'];
		$cedsuper = $_POST['CedSuper'];
		$super = $_POST['Super'];
		$programa = $_POST['Programa'];
		$tributo = $_POST['Tributo'];
		$cedcoord = $_POST['CedCoord'];
		$coord = $_POST['Coord'];
		$tlfcoord = $_POST['TlfCoord'];
		$can_sucursal = $_POST['CantSucursal'];
		$cadenatienda = strtoupper($_POST['CadenaTienda']);
		$actividad = strtoupper($_POST['Actividad']);
		$tipoMF = $_POST['TipoMF'];
		$modeloMF = strtoupper($_POST['ModeloMF']);
		$cumpleMF = strtoupper($_POST['CumpleMF']);
		//$sancionesMF= strtoupper($_POST['SancionesMF']);
		$multas = $_POST['MultasDF'];
		if ($_POST['MultasDF'] == "" or $_POST['MultasDF'] == "NO") {
			$clausuras = "";
			$diasclausuras = "";
			$monto = 0;
			$notificacionclausura = "";
		} else {
			$sancionesMF = strtoupper($_POST['SancionesMF']);
			$clausuras = $_POST['Clausura'];
			$monto = str_replace(".", ",", $_POST['MontoSanciones']);
			//echo $monto;
		}
		if ($clausuras == "SI") {
			$notificacionclausura = $_POST['NotificacionCierre'];
			$diasclausuras = $_POST['DiasClausura'];
		}
		$sancionesDF = strtoupper($_POST['Sanciones']);
		$observaciones = strtoupper($_POST['Observaciones']);

		if ($añoprov == "") {
			$guardar = "1";
		}
		if ($numprov == "") {
			$guardar = "1";
		}
		if ($can_sucursal == "") {
			$guardar = "1";
		}
		if ($actividad == "") {
			$guardar = "1";
		}
		if ($multas == "") {
			$guardar = "1";
		}
		//if ($clausuras==""){$guardar="1";}
		//echo "AÑO: ".$añoprov."-NUMERO: ".$numprov."-EMISION: ".$emision."-NOTIFICACION: ".$notificacion."-NOMBRE: ".$nombre."-RIF: ".$rif."-DOMICILIO: ".$domicilio."-SUC: ".$can_sucursal."-CADENA: ".$cadenatienda."-ACT: ".$actividad."-CIF: ".$cedfiscal."-FISC: ".$fiscal."-CIS: ".$cedsuper."-SUP: ".$super."-CICOORD: ".$cedcoord."-COORD: ".$coord."-TLF: ".$tlfcoord."-TIPOMF: ".$tipoMF."-MOD MF: ".$modeloMF."-CumpleMF: ".$cumpleMF."-SANCiones MF: ".$sancionesMF."-INC DF: ".$multas."-CIERRE: ".$clausuras."-DIAS: ".$diasclausuras."-RES CIE: ".$notificacionclausura."-MONTO: ".$monto."-INC DF: ".$sancionesDF."-OBS: ".$observaciones."-PROG: ".$programa."-TRIB: ".$tributo;


		if ($_POST['agregar'] == 'Agregar') {
			if ($guardar == "1") {
				echo "<script type=\"text/javascript\">
					alert('Existen campos vacios, por favor verifique');
					</script>";
			} else {
				//
				if ($notificacionclausura == "") {
					$consulta = "INSERT INTO Anexo2 (Anno_Providencia,NroAutorizacion,FechaEmision,FechaNotificacion,Nombre,Rif,Domicilio,Cant_sucursales,Cadena_Tienda,Actividad_Economica,CedulaFiscal,Fiscal_Actuante,CedulaSupervisor,Supervisor,CedulaCoord,Coordinador,Tlf_Contacto_Coord,Tipo_Maq_fiscal,Mod_Maq_fiscal,MF_Cumple_Req,MF_Incumplimientos,DF_Multas,DF_Clausura,Dias_clausura,MontoMultas,DF_Incumplimientos,Observaciones,Programa,Tributo,FechaProceso) VALUES  ('" . $añoprov . "','" . $numprov . "','" . date("Y-m-d", strtotime($emision)) . "','" . date("Y-m-d", strtotime($notificacion)) . "','" . $nombre . "','" . $rif . "','" . $domicilio . "','" . $can_sucursal . "','" . $cadenatienda . "','" . $actividad . "','" . $cedfiscal . "','" . $fiscal . "','" . $cedsuper . "','" . $super . "','" . $cedcoord . "','" . $coord . "','" . $tlfcoord . "','" . $tipoMF . "','" . $modeloMF . "','" . $cumpleMF . "','" . $sancionesMF . "','" . $multas . "','" . $clausuras . "','" . $diasclausuras . "','" . $monto . "','" . $sancionesDF . "','" . $observaciones . "','" . $programa . "','" . $tributo . "','" . date("Y-m-d") . "')";
				} else {
					$consulta = "INSERT INTO Anexo2 (Anno_Providencia,NroAutorizacion,FechaEmision,FechaNotificacion,Nombre,Rif,Domicilio,Cant_sucursales,Cadena_Tienda,Actividad_Economica,CedulaFiscal,Fiscal_Actuante,CedulaSupervisor,Supervisor,CedulaCoord,Coordinador,Tlf_Contacto_Coord,Tipo_Maq_fiscal,Mod_Maq_fiscal,MF_Cumple_Req,MF_Incumplimientos,DF_Multas,DF_Clausura,Dias_clausura,Res_Clausura_Notificacion,MontoMultas,DF_Incumplimientos,Observaciones,Programa,Tributo,FechaProceso) VALUES  ('" . $añoprov . "','" . $numprov . "','" . date("Y-m-d", strtotime($emision)) . "','" . date("Y-m-d", strtotime($notificacion)) . "','" . $nombre . "','" . $rif . "','" . $domicilio . "','" . $can_sucursal . "','" . $cadenatienda . "','" . $actividad . "','" . $cedfiscal . "','" . $fiscal . "','" . $cedsuper . "','" . $super . "','" . $cedcoord . "','" . $coord . "','" . $tlfcoord . "','" . $tipoMF . "','" . $modeloMF . "','" . $cumpleMF . "','" . $sancionesMF . "','" . $multas . "','" . $clausuras . "','" . $diasclausuras . "','" . date("Y-m-d", strtotime($notificacionclausura)) . "','" . $monto . "','" . $sancionesDF . "','" . $observaciones . "','" . $programa . "','" . $tributo . "','" . date("Y-m-d") . "')";
				}
				//echo $consulta;
				$conn_access = odbc_connect("LLANOS", "Administrador", "losllanos");
				//$tabla = odbc_exec ($conn_access, $consulta);
				if ($tabla = odbc_exec($conn_access, $consulta)) {
					if (odbc_result) {
						$numprov = "";
						$db_notificacion = "";
						$db_rif = "";
						echo "<script type=\"text/javascript\">
						alert('¡Registo guardado con exito!');
						</script>";
						$guardar = "0";
					}
				} else {
					echo "<script type=\"text/javascript\">
						alert('¡Problemas al guardar el registro!');
						</script>";
				}
			}
		}
		?>

		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr>
				<td align="center" valign="middle" bgcolor="#1321CC">
					<font color="#FFFFFF">
						<p>GRTI Región Los Llanos - Area de Control Tributario - Resultados Operativos Anexo 2<br />
						</p>
					</font>
				</td>
			</tr>
		</table>
		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr><br /></tr>
			<tr>
				<td width="310">Ingrese año de providencia: </td>
				<td width="548"=""><input name="añoProvidencia" type="text" size="6" maxlength="4" value="<?php echo $añoprov; ?>" /></td>
			</tr>
			<tr>
				<td>Ingrese número de providencia: </td>
				<td width="548"><input name="numProvidencia" type="text" size="6" maxlength="4" value="<?php echo $numprov; ?>" /><input onmouseover=this.style.cursor="hand" type="submit" name="buscar" value="..." /></td>
			</tr>
			<!--    <tr>
		<td>Fecha de Emisión: </td>
		<td width="548"><input name="Emision" readonly="readonly" type="text" size="10" maxlength="10" />
        <a href="javascript:NewCal('Emision','YYYYMMDD',false,24)"><img src="../imagenes/cal.gif" width="16" height="16" border="0" alt="Click para selecionar..."></a></td>
	</tr> -->
			<tr>
				<td>Fecha de Notificación: </td>
				<td width="548"><input name="Notificacion" readonly type="text" size="10" maxlength="10" value="<?php echo $db_notificacion; ?>" /></td>
			</tr>
			<tr>
				<td>Número de R.I.F.: </td>
				<td width="548"><input name="numRIF" readonly type="text" size="12" maxlength="10" value="<?php echo $db_rif; ?>" /></td>
			</tr>
			<tr>
				<td>Cantidad de Sucursales: </td>
				<td width="548">
					<p><select name="CantSucursal" language="javascript">
							<option selected><?php echo $_POST['CantSucursal']; ?></option>
							<option>0</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
							<option>6</option>
							<option>7</option>
							<option>8</option>
							<option>9</option>
							<option>10</option>
						</select></p>
				</td>
			</tr>
			<tr>
				<td>Cadena de Tienda/Franquicia: </td>
				<td width="548"><input name="CadenaTienda" type="text" size="80" maxlength="250" value="<?php echo $_POST['CadenaTienda']; ?>" /></td>
			</tr>
			<tr>
				<td>Actividad Económica: </td>
				<td width="548"><input name="Actividad" type="text" size="80" maxlength="250" value="<?php echo $_POST['Actividad']; ?>" /></td>
			</tr>
			<tr>
				<td>Tipo de Maquina Fiscal: </td>
				<td width="548">
					<p><select name="TipoMF" language="javascript" onChange="this.form.submit()">
							<option selected><?php echo $_POST['TipoMF']; ?></option>
							<option>NO APLICA</option>
							<option>REGISTRADORA</option>
							<option>IMPRESORA</option>
							<option>PUNTO DE VENTA</option>
							<option>NO TIENE</option>
						</select></p>
				</td>
			</tr>
			<tr>
				<td>Modelo Maquina Fiscal: </td>
				<td width="548"><input name="ModeloMF" type="text" size="60" maxlength="50" <?php if ($_POST[TipoMF] == "NO APLICA") {
																								echo 'disabled="disabled"';
																							} ?> value="<?php echo $_POST['ModeloMF']; ?>" /></td>
			</tr>
			<tr>
				<td>Maquina Fiscal Cumple Requisitos: </td>
				<td width="548">
					<p><select name="CumpleMF" language="javascript" onChange="this.form.submit()" <?php if ($_POST[TipoMF] == "NO APLICA") {
																										echo 'disabled="disabled"';
																									} ?>>
							<option selected><?php echo $_POST['CumpleMF']; ?></option>
							<option>SI</option>
							<option>NO</option>
						</select></p>
				</td>
			</tr>
			<tr>
				<td>Incumplimientos Maquina Fiscal: </td>
				<td width="548"><input name="SancionesMF" type="text" size="80" maxlength="250" <?php if ($_POST[TipoMF] == "NO APLICA" or $_POST[CumpleMF] == "NO") {
																									echo 'disabled="disabled"';
																								} ?> value="<?php echo $_POST['SancionesMF']; ?>" /> </td>
			</tr>
			<tr>
				<td>Multas por Deberes Formales: </td>
				<td width="548">
					<p><select name="MultasDF" language="javascript">
							<option selected><?php echo $_POST['MultasDF']; ?></option>
							<option>SI</option>
							<option>NO</option>
						</select></p>
				</td>
			</tr>
			<tr>
				<td>Clausura: </td>
				<td width="548">
					<p><select name="Clausura" language="javascript" onChange="this.form.submit()">
							<option selected><?php echo $_POST['Clausura']; ?></option>
							<option>SI</option>
							<option>NO</option>
						</select></p>
				</td>
			</tr>
			<tr>
				<td>Dias de Clausura: </td>
				<td width="548">
					<p><select name="DiasClausura" language="javascript" <?php /*if ($_POST[Clausura]=="NO") {*/ echo 'disabled="disabled"'; //} 
																			?>>
							<option selected><?php echo $_POST['DiasClausura']; ?></option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
						</select></p>
				</td>
			</tr>
			<tr>
				<td>Fecha de Notificación Resolucion Clausura: </td>
				<td width="548"><input name="NotificacionCierre" readonly type="text" size="10" maxlength="10" value="<?php echo $_POST['NotificacionCierre']; ?>" <?php /*if ($_POST[Clausura]=="NO") {*/ echo 'disabled="disabled"'; //} 
																																									?> />
					<a href="javascript:NewCssCal('NotificacionCierre','YYYYMMDD')"><img src="../imagenes/cal.gif" width="16" height="16" border="0" alt="Click para selecionar..."></a>
				</td>
			</tr>
			<tr>
				<td>Monto Multas: </td>
				<td width="548"><input name="MontoSanciones" type="text" size="10" maxlength="10" value="<?php echo $_POST['MontoSanciones']; ?>" /> (Utilice "." no "," para separar solamente decimales Ejemplo: 1234.50)</td>
			</tr>
			<tr>
				<td>Incumplimientos Deberes Formales: </td>
				<td width="548"><input name="Sanciones" type="text" size="80" maxlength="250" value="<?php echo $_POST['Sanciones']; ?>" /></td>
			</tr>
			<tr>
				<td>Observaciones: </td>
				<td width="548"><input name="Observaciones" type="text" size="80" maxlength="250" value="<?php echo $_POST['Observaciones']; ?>" /></td>
			</tr>
		</table>
		<br />
		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr>
				<td align="center" bgcolor="#1321CC">
					<input onmouseover=this.style.cursor="hand" type="submit" name="agregar" value="Agregar">
				</td>
			</tr>
		</table>

		<input name="Emision" type="hidden" size="6" maxlength="4" value="<?php echo $db_emision; ?>" />
		<input name="Notificacion" type="hidden" size="6" maxlength="4" value="<?php echo $db_notificacion; ?>" />
		<input name="Nombre" type="hidden" size="6" maxlength="4" value="<?php echo $db_nombre; ?>" />
		<input name="Rif" type="hidden" size="6" maxlength="4" value="<?php echo $db_rif; ?>" />
		<input name="Domicilio" type="hidden" size="6" maxlength="4" value="<?php echo $db_domicilio; ?>" />
		<input name="CedFiscal" type="hidden" size="6" maxlength="4" value="<?php echo $db_cedfiscal; ?>" />
		<input name="Fiscal" type="hidden" size="6" maxlength="4" value="<?php echo $db_fiscal; ?>" />
		<input name="CedSuper" type="hidden" size="6" maxlength="4" value="<?php echo $db_cedsuper; ?>" />
		<input name="Super" type="hidden" size="6" maxlength="4" value="<?php echo $db_super; ?>" />
		<input name="Programa" type="hidden" size="6" maxlength="4" value="<?php echo $db_programa; ?>" />
		<input name="Tributo" type="hidden" size="6" maxlength="4" value="<?php echo $db_tributo; ?>" />
		<input name="CedCoord" type="hidden" size="6" maxlength="4" value="<?php echo $CI_Coord; ?>" />
		<input name="Coord" type="hidden" size="6" maxlength="4" value="<?php echo $NOM_Coord; ?>" />
		<input name="TlfCoord" type="hidden" size="6" maxlength="4" value="<?php echo $TLF_Coord; ?>" />

	</form>
</body>

</html>