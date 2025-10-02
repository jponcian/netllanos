<html>

<head>
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}

	$_SESSION['SEDE'] = $_GET['sector'];

	$acceso = 123;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	$rif = $_GET['rif'];
	$num = $_GET['num'];
	$anno = $_GET['anno'];
	$sector = $_GET['sector'];
	$origen = $_GET['origen'];
	$status = $_GET['status'];
	$status2 = $_GET['status2'];

	if ($_POST['CMDGENERAR'] == 'Generar') {
		// PARA CALCULAR LOS DIAS
		$fecha_actual  = date('Y/m/d');
		// SE LE RESTAN 3 DIAS PARA VER SI NO HAN GENERADO ACTAS DENTRO DE ESTE RANGO
		$fecha_actual = dias_feriados_reverso($fecha_actual, 3);
		// FIN	
		// PARA GUARDAR
		$consulta_x = "SELECT * FROM cob_actadecobro WHERE  sector=" . $sector . " AND origen=" . $_SESSION['ORIGEN'] . " AND numero=" . $num . " AND anno=" . $anno . " AND Fecha_Resolucion>'" . $fecha_actual . "';";
		$tabla_x = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla_x);

		if ($numero_filas > 0) {
			echo "<script type=\"text/javascript\">alert('�Ya se han generado Actas hace menos de 3 d�as!');</script>";
			echo "<script type=\"text/javascript\">alert('�Fueron actualizadas las Planillas!');</script>";
			// 1ra parte
			// BUSCAR LAS PLANILLAS SELECCIONADAS
			$liquidaciones = '';
			$i = 0;
			$consulta_xx = 'SELECT liquidacion FROM vista_sanciones_aplicadas WHERE origen_liquidacion=' . $origen . ' AND status>=' . $status . ' AND status<=' . $status2 . ' and sector=' . $sector . ' AND anno_expediente=' . $anno . ' AND num_expediente=' . $num . ' and fecha_ven<date(now())';
			$tabla_xx = mysql_query($consulta_xx);
			while ($registro_xx = mysql_fetch_object($tabla_xx)) {
				if ($_POST['O' . $registro_xx->liquidacion] == '1') {
					if ($i > 0) {
						$liquidaciones = $liquidaciones . ', ' . '"' . $registro_xx->liquidacion . '"';
					} else {
						$liquidaciones = '"' . $registro_xx->liquidacion . '"';
					}
					$i++;
				}
			}

			// 2da parte
			// ACTUALIZAR LA ACTA DE COBRO
			$consulta_xx = "UPDATE cob_actadecobro SET Planillas = '" . trim($liquidaciones) . "' WHERE sector=" . $sector . " AND origen=" . $_SESSION['ORIGEN'] . " AND numero=" . $num . " AND anno=" . $anno . ";";
			$tabla_xx = mysql_query($consulta_xx);
		} else {
			// 1ra parte
			// NUMERO DE LA RESOLUCION CONSULTA DEL MAXIMO
			$consulta_xx = "SELECT Max([NroResolucion])+1 AS Maximo FROM cob_actadecobro WHERE AnnoResolucion=" . date('Y') . " AND sector=" . $sector . " AND origen=" . $_SESSION['ORIGEN'] . ";";
			$tabla_xx = mysql_query($consulta_xx);
			$numero_filas = mysql_num_rows($numero_filas);

			if ($numero_filas > 0) {
				$registro_xx = mysql_fetch_object($tabla_xx);
				$Maximo = $registro_xx->Maximo;
			} else {
				$Maximo = 1;
			}

			// 2da parte
			// BUSCAR LAS PLANILLAS SELECCIONADAS
			$liquidaciones = '';
			$i = 0;
			$consulta_xx = 'SELECT liquidacion FROM vista_sanciones_aplicadas WHERE origen_liquidacion=' . $origen . ' AND status>=' . $status . ' AND status<=' . $status2 . ' and sector=' . $sector . ' AND anno_expediente=' . $anno . ' AND num_expediente=' . $num . ' and fecha_ven<date(now())';
			$tabla_xx = mysql_query($consulta_xx);
			while ($registro_xx = mysql_fetch_object($tabla_xx)) {
				if ($_POST['O' . $registro_xx->liquidacion] == '1') {
					if ($i > 0) {
						$liquidaciones = $liquidaciones . ', ' . '"' . $registro_xx->liquidacion . '"';
					} else {
						$liquidaciones = '"' . $registro_xx->liquidacion . '"';
					}
					$i++;
				}
			}

			// 3da parte
			// INSERTAR LA ACTA DE COBRO
			$consulta_xx = "INSERT INTO cob_actadecobro ( rif, origen, numero, anno, NroResolucion, AnnoResolucion, Fecha_Resolucion, Planillas, Usuario, sector) SELECT '" . $rif . "', " . $_SESSION['ORIGEN'] . "," . $num . ", " . $anno . ", " . $Maximo . ", " . date('Y') . ",  date(now()), '" . trim($liquidaciones) . "', '" . $_SESSION['CEDULA_USUARIO'] . "', " . $sector . ";";
			$tabla_xx = mysql_query($consulta_xx);
		}
	}
	?>
	<title>Generar Acta de Cobro</title>
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

		.Estilo5 {
			font-size: 12px
		}

		.Estilo7 {
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
		}

		.Estilo13 {
			font-size: 16px
		}

		.Estilo14 {
			font-size: 16px;
			font-weight: bold;
		}

		.Estilo15 {
			font-size: 14px;
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='scw.js'></script>
</head>

<body style="background: transparent !important;">
	<p>&nbsp;</p>
	<form name="form1" method="post" action="">
		<table width="50%" height="204" border=1 align=center>
			<?php
			$consulta = "SELECT contribuyente, rif FROM contribuyentes WHERE (((rif)='" . $_GET['rif'] . "'));";
			$tabla = mysql_query($consulta);
			$registro = mysql_fetch_object($tabla)
			?>
			<td bgcolor="#FF0000" height="27" colspan="6" align="center">
				<p class="Estilo7"><u>Contribuyente</u></p>
			</td>
			<tr>
				<td width="9%" height="24">
					<div align="center"></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo14">Rif</div>
				</td>
				<td bgcolor="#CCCCCC" colspan="3">
					<div align="center" class="Estilo14">Contribuyente</div>
				</td>
				<td width="9%">
					<div align="center"></div>
				</td>
			</tr>
			<tr>
				<td height=27>
					<div align="center"></div>
				</td>
				<td>
					<div align="center" class="Estilo15"><?php echo $registro->rif; ?></div>
				</td>
				<td colspan="3">
					<div align="left" class="Estilo15"><?php echo $registro->contribuyente; ?></div>
				</td>
				<td width="9%" colspan="1">
					<div align="center"></div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="6" align="center">
					<p class="Estilo7"><u>Gesti&oacute;n de Cobro </u></p>
				</td>
			</tr>
			<tr>
				<td width="9%" height=27>
					<div align="center"></div>
				</td>
				<td width="19%" bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Asignaci&oacute;n</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo14">Cedula</div>
				</td>
				<td colspan="2" bgcolor="#CCCCCC">
					<div align="center" class="Estilo14">Funcionario</div>
				</td>
				<td width="9%" colspan="1">
					<div align="center"> </div>
				</td>
			</tr>
			<?php
			$consulta_x = 'SELECT fecha_asignacion_cobrador, cobrador FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' and sector=' . $sector . ' and fecha_ven<date(now()) GROUP BY sector, origen_liquidacion, anno_expediente, num_expediente';
			$tabla = mysql_query($consulta_x);
			$registro = mysql_fetch_object($tabla);
			?>

			<tr>
				<td height=27>
					<div align="center"></div>
				</td>
				<td>
					<div align="center" class="Estilo15"><?php echo voltea_fecha($registro->fecha_asignacion_cobrador); ?></div>
				</td>
				<td height=27>
					<div align="center" class="Estilo15"><?php echo formato_cedula($registro->cobrador); ?></div>
				</td>
				<td colspan="2">
					<div align="center" class="Estilo15"><?php list($cobrador) = funcion_funcionario($registro->cobrador);
															echo $cobrador; ?></div>
				</td>
				<td width="9%" height=27 colspan="1">
					<div align="center"></div>
				</td>
			</tr>
		</table>
		<table width="65%" border=1 align=center>
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="6" align="center">
					<p class="Estilo7"><u>Actas de Cobro generadas al Contribuyente </u></p>
				</td>
			</tr>
		</table>

		<table width="65%" border=1 align=center>
			<tr>
				<td height=27>
					<div align="center"></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">Acta</span></a></strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">A&ntilde;o</span></a></strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Fecha</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Liquidaciones</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Opcion</strong></div>
				</td>
				<td height=27 colspan="1">
					<div align="center"></div>
				</td>
			</tr>
			<?php
			$consulta = "SELECT * FROM cob_actadecobro WHERE sector=" . $sector . " AND origen=" . $_SESSION['ORIGEN'] . " AND numero=" . $num . " AND anno=" . $anno . " ORDER BY numero;";
			$tabla = mysql_query($consulta);
			$numero_filas = mysql_num_rows($tabla);

			if ($numero_filas > 0) {
				while ($registro = mysql_fetch_object($tabla)) {
			?>
					<tr>
						<td height=27>
							<div align="center"></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><?php echo $registro->NroResolucion; ?></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><?php echo $registro->AnnoResolucion; ?></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><?php echo voltea_fecha($registro->Fecha_Resolucion); ?></div>
						</td>
						<td width="40%">
							<div align="center" class="Estilo15"><?php echo $registro->Planillas;  ?></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><a href="formatos/acta_cobro.php?anno=<?php echo $registro->anno; ?>&num=<?php echo $registro->numero; ?>&sector=<?php echo $sector; ?>" target=_blank>VER ACTA</a></div>
						</td>
						<td colspan="1">
							<div align="center"></div>
						</td>
					</tr><?php
						}
					}
							?>
		</table>
		<p></p>
		<table width="65%" border=1 align=center>
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="8" align="center">
					<p class="Estilo7"><u>Planillas</u></p>
				</td>
			</tr>
			<tr>
				<td height=27>
					<div align="center"></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo5"><strong><span class="Estilo13">Liquidacion</span></a></strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Fecha Notificacion </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Fecha Vencimiento </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Planilla</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo13"><strong>Generar</strong></div>
				</td>
				<td height=27 colspan="1">
					<div align="center"></div>
				</td>
			</tr>
			<?php
			$consulta_x = 'SELECT liquidacion, fecha_not, fecha_ven, planilla_notificacion, ((monto_bs*especial)/concurrencia) as monto FROM vista_sanciones_aplicadas WHERE origen_liquidacion=' . $origen . ' AND status>=' . $status . ' AND status<=' . $status2 . ' and sector=' . $sector . ' AND anno_expediente=' . $anno . ' AND num_expediente=' . $num . ' and fecha_ven<date(now())';
			$tabla = mysql_query($consulta_x);
			while ($registro = mysql_fetch_object($tabla)) {
			?>
				<tr>
					<td height=27>
						<div align="center"></div>
					</td>
					<td>
						<div align="center" class="Estilo15"><?php echo $registro->liquidacion; ?></div>
					</td>
					<td>
						<div align="center" class="Estilo15"><?php echo voltea_fecha($registro->fecha_not); ?></div>
					</td>
					<td>
						<div align="center" class="Estilo15"><?php echo voltea_fecha($registro->fecha_ven);  ?></div>
					</td>
					<td>
						<div align="center" class="Estilo15"><?php echo $registro->planilla_notificacion;  ?></div>
					</td>
					<td>
						<div align="center" class="Estilo15"><?php echo formato_moneda($registro->monto); ?></div>
					</td>
					<td>
						<div align="center" class="Estilo15">
							<label>
								<input type="checkbox" name="O<?php echo $registro->liquidacion; ?>" value="1" checked="checked">
							</label>
						</div>
					</td>
					<td colspan="1">
						<div align="center"></div>
					</td>
				</tr><?php
					}
						?>
		</table>
		<p>
		<p align="center">
			<input type="submit" class="boton" name="CMDGENERAR" value="Generar">
		</p>
		</p>
	</form>

</body>

</html>