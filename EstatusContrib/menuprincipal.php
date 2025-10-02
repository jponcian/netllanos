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

<title>Consulta</title>
<link rel="stylesheet" type="text/css" href="css/estilos1.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<body style="background: transparent !important;">

	<p>
		<?php include "../funciones/head.php"; ?>
	</p>
	<p align="center"></p>

	<!-- Formulario Bootstrap para búsqueda de RIF -->
	<form method="post" name="Logar" action="">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="input-group mb-3">
					<span class="input-group-text bg-success text-white"><b>Ingrese n&uacute;mero de Rif:</b></span>
					<input name="numRif" type="text" class="form-control" size="16" maxlength="16" value="<?php echo $_POST['numRif']; ?>" />
					<button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
				</div>
			</div>
		</div>
	</form>

	<p>&nbsp;</p>

	<p>&nbsp;</p>
	<table class="table table-bordered table-hover table-sm bg-white" style="width:80%;margin:auto;">
		<thead class="table-danger">
			<tr>
				<th colspan="11" class="text-center align-middle">
					<p class="Estilo7 mb-0"><u>DIV. Fiscalizaci&oacute;n</u></p>
				</th>
			</tr>
			<tr>
				<th class="text-center">Rif</th>
				<th class="text-center">Est</th>
				<th class="text-center">A&ntilde;o Prov.</th>
				<th class="text-center">N&deg; Prov.</th>
				<th class="text-center">Programa Aplicado</th>
				<th class="text-center">Tributo</th>
				<th class="text-center">Sector</th>
				<th class="text-center">Fecha de Emisi&oacute;n</th>
				<th class="text-center">Fecha de Notificaci&oacute;n</th>
				<th class="text-center">Funcionario</th>
				<th class="text-center">Sancionado</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$numrif = $_POST['numRif'];
			$registrar = false;
			$bdd = $_SESSION['BDD'];
			//Verificamos si exixten registros en la base de datos principal		
			$Ccsql = "SELECT rif, sector, ci_fiscal1, anno, numero, descripcion, tributo, fecha_emision, fecha_notificacion FROM vista_est_cta_providencias WHERE rif='" . $numrif . "' ORDER BY fecha_notificacion DESC"; //ECHO $Ccsql;
			$result = mysql_query($Ccsql);
			$registros = mysql_fetch_object($result);
			if ($registros <> Null) {
				$registrar = true;
			} else {
				$_SESSION['BDD'] = 'losllanos_viejo';
				mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
				$Ccsql = "SELECT rif, sector, anno, numero, descripcion, tributo, fecha_emision, fecha_notificacion FROM vista_est_cta_providencias WHERE rif='" . $numrif . "' ORDER BY fecha_notificacion DESC"; //ECHO $Ccsql;
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
					$Ccsql = "SELECT rif, nombrest, sector,Nombres, anno, numero, descripcion, tributo, fecha_emision, fecha_notificacion, Grupo, nombre FROM vista_est_cta_providencias WHERE rif='" . $numrif . "' ORDER BY fecha_notificacion DESC";
					//echo $Ccsql;
					$result = mysql_query($Ccsql);
					while ($valor = mysql_fetch_array($result)) {
			?>
						<tr id="fila<?php echo $i; ?>">
							<td>
								<div class="Estilo8 Estilo19"><?php echo $valor['rif'] ?></div>
							</td>
							<td>
								<div class="Estilo8 Estilo19"><?php echo $valor['nombrest'] ?></div>
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
							<td>
								<div class="Estilo8 Estilo19"><?php echo $valor['Nombres']; ?></div>
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
					echo '<table class="table table-bordered table-sm table-danger" style="width:80%;margin:auto;"><tr><td class="text-center">No existen Registros</td></tr></table>';
				}
			}
?>
<p></p>
<table class="table table-bordered table-hover table-sm bg-white" style="width:80%;margin:auto;">
	<thead class="table-danger">
		<tr>
			<th colspan="11" class="text-center align-middle">
				<p class="Estilo7 mb-0"><u>DIV. Sujetos Pasivos Especiales</u></p>
			</th>
		</tr>
		<tr>
			<th class="text-center">Rif</th>
			<th class="text-center">Estatus</th>
			<th class="text-center">Concepto</th>
			<th class="text-center">Descripcion</th>
			<th class="text-center">A&ntilde;o Exp.</th>
			<th class="text-center">N&deg; Exp.</th>
			<th class="text-center">Sector</th>
			<th class="text-center">Fecha de Registro</th>
			<th class="text-center">Fecha de Aprobaci&oacute;n</th>
			<th class="text-center">Funcionario</th>
			<th class="text-center">Sancionado</th>
		</tr>
	</thead>
	<tbody>
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
			$Ccsql = "SELECT rif,nombrest, sector, anno, numero, nombrefuncionario, fecha_registro as fecha_emision, fecha_registro as fecha_notificacion, nombre, concepto, descripcion FROM vista_exp_especiales WHERE rif='" . $numrif . "' AND status<>9 ORDER BY fecha_notificacion DESC"; //echo $Ccsql;
			$result = mysql_query($Ccsql);
			while ($valor = mysql_fetch_array($result)) {
		?>
				<tr id="fila<?php echo $i; ?>">
					<td>
						<div class="Estilo8 Estilo19"><?php echo $valor['rif'] ?></div>
					</td>
					<td>
						<div class="Estilo8 Estilo19"><?php echo $valor['nombrest'] ?></div>
					</td>
					<td>
						<div class="Estilo8 Estilo19"><?php echo $valor['concepto'] ?></div>
					</td>
					<td>
						<div class="Estilo8 Estilo19"><?php echo $valor['descripcion'] ?></div>
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
					<td>
						<div class="Estilo8 Estilo19"><?php echo $valor['nombrefuncionario']; ?></div>
					</td>
					<td align="center">
						<div class="Estilo8 Estilo19">
							<?php
							//************
							$Bssql = "SELECT rif FROM vista_exp_especiales_liq WHERE sector=" . $valor['sector'] . " and anno=" . $valor['anno'] . " and numero=" . $valor['numero'] . "";
							if ($rs_access = mysql_query($Bssql)) {
								if ($fila_reg = mysql_fetch_object($rs_access)) {
									echo '<a href="26sanciones_aplicadas.php?bdd=' . $_SESSION['BDD'] . '&rif=' . $numrif . '&num=' . $valor['numero'] . '&anno=' . $valor['anno'] . '&sector=' . $valor['sector'] . '" target="_blank">........SI........ (click aqui)</a>';
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
				echo '<table class="table table-bordered table-sm table-danger" style="width:80%;margin:auto;"><tr><td class="text-center">No existen Registros</td></tr></table>';
			}
		}
?>
</form>

<p>&nbsp;</p>
<p>&nbsp;</p>

<p>&nbsp;</p>

</body>

</html>