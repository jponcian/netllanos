<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 90;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	
?>

<title>Consulta </title>
<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form method="post" name="Logar" action="">
		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr>
				<td align="center" valign="middle" class="TituloTabla"><span><u>GRTI <?php echo buscar_region(); ?> - Consulta de Visitas Efectuadas por Contribuyente</u></span></td>
			</tr>
		</table>
		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr><br /></tr>
			<tr>
				<td align="right"><b>Ingrese n&uacute;mero de Rif:</b></td>
				<td width="548"><input name="numRif" type="text" size="12" maxlength="10" value="<?php echo $_POST['numRif']; ?>" /><input onmouseover=this.style.cursor="hand" type="submit" name="buscar" value="Buscar" />
				</td>
			</tr>
		</table>
		<p>&nbsp;</p>
		<table class="formateada" width="60%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="39" colspan="9" align="center">
						<p class="Estilo7"><u>Fiscalizaci&oacute;n</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC" height="27">
						<div align="center" class="Estilo8"><strong>Rif</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>A&ntilde;o Prov </strong>.</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>N&deg; Prov.</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Programa Aplicado</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Tributo</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Sector</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Fecha de Emision</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Fecha de Notificacion</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo8"><strong>Sancionado</strong></div>
					</td>
				</tr>
				<?php
				$numrif = $_POST['numRif'];
				$registrar = false;
				$bdd = $_SESSION['BDD'];
				//Verificamos si exixten registros en la base de datos principal		
				$Ccsql = "SELECT rif, sector, anno, numero, descripcion, tributo, fecha_emision, fecha_notificacion FROM vista_est_cta_providencias WHERE rif='" . $numrif . "' ORDER BY fecha_notificacion DESC";
				$result = mysql_query($Ccsql);
				$registros = mysql_fetch_object($result);
				if ($registros <> Null) {
					$registrar = true;
				} else {
					$_SESSION['BDD'] = 'losllanos_viejo';
					mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
					$Ccsql = "SELECT rif, sector, anno, numero, descripcion, tributo, fecha_emision, fecha_notificacion FROM vista_est_cta_providencias WHERE rif='" . $numrif . "' ORDER BY fecha_notificacion DESC";
					//echo $Ccsql;
					$result = mysql_query($Ccsql);
					$registros = mysql_fetch_object($result);
					if ($registros <> Null) {
						$registrar = true;
					} else {
						$registrar = false;
					}
					$_SESSION['BDD'] = $bdd;
				}
				$i = 0;
				//------
				if ($registrar == true) {
					$bdd = $_SESSION['BDD'];
					$xxx = 1;
					while ($xxx <= 2) {
						if ($xxx == 2) {
							$_SESSION['BDD'] = 'losllanos_viejo';
						}
						//------
						mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
						//------------
						$Ccsql = "SELECT rif, sector, anno, numero, descripcion, tributo, fecha_emision, fecha_notificacion, Grupo, nombre FROM vista_est_cta_providencias WHERE rif='" . $numrif . "' ORDER BY fecha_notificacion DESC";
						//echo $Ccsql;
						$result = mysql_query($Ccsql);
						while ($valor = mysql_fetch_array($result)) {
				?>
							<tr id="fila<?php echo $i; ?>">
								<td>
									<div class="Estilo8 Estilo19"><?php echo $valor['rif'] ?></div>
								</td>
								<td align="center">
									<div class="Estilo8 Estilo19"><?php echo $valor['anno'] ?></div>
								</td>
								<td align="center">
									<div class="Estilo8 Estilo19"><?php echo $valor['numero'] ?></div>
								</td>
								<td>
									<div class="Estilo8 Estilo19"><?php echo $valor['descripcion']; ?></div>
								</td>
								<td>
									<div class="Estilo8 Estilo19"><?php echo $valor['tributo'] ?></div>
								</td>
								<td>
									<div class="Estilo8 Estilo19"><?php echo $valor['nombre']; ?></div>
								</td>
								<td align="center">
									<div class="Estilo8 Estilo19"><?php echo date("d-m-Y", strtotime($valor['fecha_emision'])); ?></div>
								</td>
								<td align="center">
									<div class="Estilo8 Estilo19"><?php if ($valor['fecha_notificacion'] == Null or $valor['fecha_notificacion'] == '0000-00-00') {
																		echo "";
																	} else {
																		echo date("d-m-Y", strtotime($valor['fecha_notificacion']));
																	} ?></div>
								</td>
								<td align="center">
									<div class="Estilo8 Estilo19">
										<?php
										//************
										if ($valor['Grupo'] == "A") {
											$Bssql = "SELECT num_prov FROM vista_est_cta_actas WHERE anno_prov=" . $valor['anno'] . " and num_prov=" . $valor['numero'] . "";
											if ($rs_access = mysql_query($Bssql)) {
												if ($fila_reg = mysql_fetch_object($rs_access)) {
													if ($fila_reg->conformidad == 0) {
														echo '<a href="26sanciones_aplicadas.php?rif=' . $numrif . '&num=' . $valor['numero'] . '&anno=' . $valor['anno'] . '&sector=' . $valor['sector'] . '" target="_blank">SI</a>';
													} else {
														echo "NO";
													}
												} else {
													echo "NO";
												}
											}
										} else {
											$Bssql = "SELECT sector FROM vista_ct_multas WHERE sector=" . $valor['sector'] . " and anno_expediente=" . $valor['anno'] . " and num_expediente=" . $valor['numero'] . "";
											if ($rs_access = mysql_query($Bssql)) {
												if ($fila_reg = mysql_fetch_object($rs_access)) {
													echo '<a href="26sanciones_aplicadas.php?bdd=' . $_SESSION['BDD'] . '&rif=' . $numrif . '&num=' . $valor['numero'] . '&anno=' . $valor['anno'] . '&sector=' . $valor['sector'] . '" target="_blank">SI</a>';
												} else {
													echo "NO";
												}
											}
										}
										//************
										?>
									</div>
								</td>
							</tr>
					<?php
						}
						$xxx++;
					}
					$_SESSION['BDD'] = $bdd;
					?>
			</tbody>
		</table>
	<?php
				} else {
					if ($_POST['numRif'] != "") {
						echo '<br />';
						echo '<table width="878" align="center">';
						echo '<tr><td align="center">No existen Registros';
						echo '</td></tr>';
						echo '</table>';
					}
				}
	?>
	<p></p>
	<table class="formateada" width="60%" border=1 align=center>
		<tbody>
			<tr>
				<td bgcolor="#FF0000" height="39" colspan="9" align="center">
					<p class="Estilo7"><u>DIV. Sujetos Pasivos Especiales</u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC" height="27">
					<div align="center" class="Estilo8"><strong>Rif</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo8"><strong>A&ntilde;o Exp </strong>.</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo8"><strong>N&deg; Exp.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo8"><strong>Sector</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo8"><strong>Fecha de Registro </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo8"><strong>Fecha de Aprobaci&oacute;n </strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo8"><strong>Sancionado</strong></div>
				</td>
			</tr>
			<?php
			mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
			$numrif = $_POST['numRif'];
			$registrar = false;
			//Verificamos si exixten registros en la base de datos principal		
			$Ccsql = "SELECT rif FROM vista_exp_especiales WHERE rif='" . $numrif . "' AND status<>9";
			$result = mysql_query($Ccsql);
			$registros = mysql_fetch_object($result);
			if ($registros <> Null) {
				$registrar = true;
			}
			$i = 0;
			//------
			if ($registrar == true) {
				//------------
				$Ccsql = "SELECT rif, sector, anno, numero, fecha_registro as fecha_emision, fecha_registro as fecha_notificacion, nombre FROM vista_exp_especiales WHERE rif='" . $numrif . "' AND status<>9 ORDER BY fecha_notificacion DESC";
				$result = mysql_query($Ccsql);
				while ($valor = mysql_fetch_array($result)) {
			?>
					<tr id="fila<?php echo $i; ?>">
						<td>
							<div class="Estilo8 Estilo19"><?php echo $valor['rif'] ?></div>
						</td>
						<td align="center">
							<div class="Estilo8 Estilo19"><?php echo $valor['anno'] ?></div>
						</td>
						<td align="center">
							<div class="Estilo8 Estilo19"><?php echo $valor['numero'] ?></div>
						</td>
						<td>
							<div class="Estilo8 Estilo19"><?php echo $valor['nombre']; ?></div>
						</td>
						<td align="center">
							<div class="Estilo8 Estilo19"><?php echo date("d-m-Y", strtotime($valor['fecha_emision'])); ?></div>
						</td>
						<td align="center">
							<div class="Estilo8 Estilo19"><?php if ($valor['fecha_notificacion'] == Null or $valor['fecha_notificacion'] == '0000-00-00') {
																echo "";
															} else {
																echo date("d-m-Y", strtotime($valor['fecha_notificacion']));
															} ?></div>
						</td>
						<td align="center">
							<div class="Estilo8 Estilo19">
								<?php
								//************
								$Bssql = "SELECT rif FROM vista_exp_especiales_liq WHERE sector=" . $valor['sector'] . " and anno=" . $valor['anno'] . " and numero=" . $valor['numero'] . "";
								if ($rs_access = mysql_query($Bssql)) {
									if ($fila_reg = mysql_fetch_object($rs_access)) {
										echo '<a href="26sanciones_aplicadas.php?bdd=' . $_SESSION['BDD'] . '&rif=' . $numrif . '&num=' . $valor['numero'] . '&anno=' . $valor['anno'] . '&sector=' . $valor['sector'] . '" target="_blank">SI</a>';
									} else {
										echo "NO";
									}
								}
								//************
								?>
							</div>
						</td>
					</tr>
				<?php
				}
				?>
		</tbody>
	</table>
<?php
			} else {
				if ($_POST['numRif'] != "") {
					echo '<br />';
					echo '<table width="878" align="center">';
					echo '<tr><td align="center">No existen Registros';
					echo '</td></tr>';
					echo '</table>';
				}
			}
?>
	</form>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p> <?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>