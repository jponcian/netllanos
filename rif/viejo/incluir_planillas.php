<?php
ob_end_clean();
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 12;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>
<?php
if ($_POST['CMDAGREGAR'] == "Agregar") {
	if ($_POST['ORIF'] <> "") {
		// BUSQUEDA DEL CONTRIBUYENTE
		$consulta_x = "SELECT * FROM contribuyentes WHERE Rif='" . $_POST['ORIF'] . "';";
		$tabla_x = mysql_query($consulta_x);
		if ($registro_x = mysql_fetch_object($tabla_x)) {
			if ($_POST['OTIPO'] > 0 and $_POST['OSEDE'] > 0 and $_POST['OBANCO'] > 0 and $_POST['OPERIODO'] <> '-1' and $_POST['OQUINCENA'] <> '-1' and $_POST['ONUMERO'] > 0 and $_POST['OFECHA_PAGO'] <> "" and $_POST[OFECHA_PRE] <> "" and $_POST['OMONTO'] > 0) {
				// VALIDACION SI EST� INCLUIDA
				$consulta_x = "SELECT * FROM CE_Pagos WHERE Numero=" . $_POST['ONUMERO'] . " and Tipo_Impuesto=" . $_POST['OTIPO'];
				$tabla_x = mysql_query($consulta_x);
				if ($registro_x = mysql_fetch_object($tabla_x)) {
					$_POST['CMDBUSCAR'] = 'Buscar';
					echo "<script type=\"text/javascript\">alert('�Ya est� Registrada la Planilla!');</script>";
				} else {
					// BUSQUEDA DE LA FECHA DE VENCIMIENTO
					if ($_GET[periodo] <> "") {
						$PERIODO = $_GET[periodo];
					} else {
						$PERIODO = $_POST['OPERIODO'];
					}

					if (($_GET[periodo] <> "" or $_POST['OPERIODO'] <> "-1") and ($_POST['OQUINCENA'] <> "-1" and substr($_POST['ORIF'], 9, 1) <> "")) {
						$consulta_x = "SELECT date_format(Fecha_Ven,'%Y/%m/%d') as Fecha FROM CE_Calendario WHERE Periodo='" . $PERIODO . "' AND Rif LIKE '%" . substr($_POST['ORIF'], 9, 1) . "%' AND Tipo_Impuesto=" . $_POST['OTIPO'] . " AND Quincena=" . $_POST['OQUINCENA'] . ";";
						$tabla_x = mysql_query($consulta_x);
						if ($registro_x = mysql_fetch_object($tabla_x)) {

							if ($registro_x->Fecha <> '') {
								$Fecha_Ven = $registro_x->Fecha;
							} else {
								$Fecha_Ven = '01/01/9999';
							}
						}
					}
					//********************************************************************
					//---------------------------------------
					//------------ DIVIDIR EL BANCO Y AGENCIA
					list($banco, $agencia) = explode('-', $_POST['OBANCO']);
					//------------
					// ----- VERFICAR SI ES UN DIA INHABILITADO
					$consulta_x = "SELECT * FROM CE_Dias_Inhabilitados WHERE dia='" . $Fecha_Ven . "' AND sector=" . $_POST['OSEDE'] . ";";
					$tabla_x = mysql_query($consulta_x);
					if ($registro_x = mysql_fetch_object($tabla_x)) {
						$multado = 1;
					} else {
						$multado = 0;
					}
					//-----------------------------------------
					//------------ CARGA DE LA PLANILLA
					$consulta = "INSERT INTO CE_Pagos ( Rif, Tipo_Impuesto, Numero, Periodo, Quincena, Fecha_Ven, Agencia, Monto, Fecha_Pago, Fecha_Presentacion, Sector, Multado ) SELECT '" . strtoupper($_POST['ORIF']) . "' AS Expr1, '" . $_POST['OTIPO'] . "' AS Expr2, '" . $_POST['ONUMERO'] . "' AS Expr3, '" . $_POST['OPERIODO'] . "' AS Expr4, '" . $_POST['OQUINCENA'] . "' AS Expr5, '" . $Fecha_Ven . "' AS Expr6, '" . $agencia . "' AS Expr7, '" . $_POST['OMONTO'] . "' AS Expr8, '" . $_POST['OFECHA_PAGO'] . "' AS Expr9, '" . $_POST[OFECHA_PRE] . "' AS Expr15, '" . $_POST['OSEDE'] . "' AS Expr10, '" . $multado . "' AS Expr12;";

					if ($tabla = mysql_query($consulta)) {
						echo "<script type=\"text/javascript\">alert('�Se ha insertado la Planilla Exitosamente!');</script>";
					} else {
						$_POST['CMDBUSCAR'] = 'Buscar';
						echo "<script type=\"text/javascript\">alert('�ERROR en la carga de la Planilla!');</script>";
					}
				}
			} else {
				$_POST['CMDBUSCAR'] = 'Buscar';
				echo "<script type=\"text/javascript\">alert('�Por Favor Rellene Todos los Campos!');</script>";
			}
		} else {
			$_POST['CMDBUSCAR'] = 'Buscar';
			echo "<script type=\"text/javascript\">alert('�No Existe Contribuyente Especial con ese Rif!');</script>";
		}

		//********************************************************************
	} else {
		$_POST['CMDBUSCAR'] = 'Buscar';
		echo "<script type=\"text/javascript\">alert('�Por Favor Introduzca un Rif Correcto!');</script>";
	}
}

?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<title>Inclusion de Planillas</title>
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

		.Estilo1 {
			color: #FFFFFF;
			font-weight: bold;
			font-size: 18px;
		}

		.Estilo16 {
			color: #FF0000
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post" action="">
		<table width="719" border=1 align=center>
			<tr>
				<td height="35" align="center" bgcolor="#FF0000">
					<p class="Estilo7 Estilo1"><u>INGRESO DE PLANILLAS</u></p>
				</td>
			</tr>
		</table>
		<table align="center" width="719" border="1">
			<tr>
				<td width="131" height="35" bgcolor="#CCCCCC">
					<div align="center"><strong>Tipo Impuesto </strong></div>
				</td>
				<td width="542" bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<select name="OTIPO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							$consulta_x = "SELECT * FROM CE_Cal_Tip_Obligaciones ORDER BY Numero;";
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_object($tabla_x)) {
								echo '<option';
								//-----------------
								if ($_GET[tipo] <> "" and $_GET[tipo] == $registro_x->Numero) {
									echo ' selected="selected" ';
								} else {
									if ($_GET[tipo] == "" and $_POST['OTIPO'] == $registro_x->Numero) {
										echo ' selected="selected" ';
									}
								}
								//-----------------	
								echo ' value="';
								echo $registro_x->Numero;
								echo '">';
								echo $registro_x->Numero . " - " . $registro_x->Tipo;
								echo '</option>';
							}
							?>
						</select>
					</div>
				</td>
			</tr>
		</table>
		<table align="center" width="719" border="1">
			<tr>
				<td width="97" height="35" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia</strong></div>
				</td>
				<td width="172" bgcolor="#FFFFFF">
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
				<td width="63" bgcolor="#CCCCCC">
					<div align="center"><strong>Banco</strong></div>
				</td>
				<td width="357" bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<select name="OBANCO" size="1">
							<option value="-1">Seleccione</option>
							<?php
							$consulta_x = "SELECT * FROM vista_ce_consulta_banco WHERE sector=" . $_POST['OSEDE'] . " ORDER BY id_agencia;";
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_object($tabla_x)) {
								echo '<option';
								if ($_POST['OBANCO'] == ($registro_x->id_banco . '-' . $registro_x->id_agencia)) {
									echo ' selected="selected" ';
								}
								echo ' value="';
								echo $registro_x->id_banco . '-' . $registro_x->id_agencia;
								echo '">';
								echo sprintf("%003s", $registro_x->id_agencia_ordinario) . " - " . sprintf("%003s", $registro_x->id_agencia_especial) . " - " . $registro_x->Descripcion;
								echo '</option>';
							}
							?>
						</select>
					</div>
				</td>

			</tr>
		</table>

		<table align="center" width="719" border="1">
			<tr>
				<td width="52" height="30" bgcolor="#CCCCCC">
					<div align="center"><strong>A&ntilde;o</strong></div>
				</td>
				<td width="134" bgcolor="#FFFFFF">
					<div align="center">
						<select name="OANNO" size="1" onChange="this.form.submit()">
							<option value="-1">Seleccione</option>
							<?php
							if ($_GET[tipo] <> "") {
								$Tipo = $_GET[tipo];
							} else {
								$Tipo = $_POST['OTIPO'];
							}

							$consulta_x = "SELECT RIGHT(Periodo,4) AS anno FROM CE_Calendario WHERE Tipo_Impuesto=" . $Tipo . " GROUP BY RIGHT(Periodo,4) ORDER BY RIGHT(Periodo,4) DESC;";
							$tabla_x = mysql_query($consulta_x);

							while ($registro_x = mysql_fetch_object($tabla_x)) {
								echo '<option';

								//-----------------
								if ($_GET[periodo] <> "" and $_GET[periodo] == $registro_x->anno) {
									echo ' selected="selected" ';
								} else {
									if ($_GET[periodo] == "" and $_POST[OANNO] == $registro_x->anno) {
										echo ' selected="selected" ';
									}
								}
								//-----------------

								echo ' value="';
								echo $registro_x->anno;
								echo '">';
								echo $registro_x->anno;
								echo '</option>';
							}
							?>
						</select>
					</div>
				</td>
				<td width="52" height="30" bgcolor="#CCCCCC">
					<div align="center"><strong>Periodo</strong></div>
				</td>
				<td width="134" bgcolor="#FFFFFF">
					<div align="center">
						<select name="OPERIODO" size="1" onChange="this.form.submit()">
							<option value="-1">-> Seleccione <-< /option>
									<?php
									if ($_GET[tipo] <> "") {
										$Tipo = $_GET[tipo];
									} else {
										$Tipo = $_POST['OTIPO'];
									}

									$consulta_x = "SELECT CE_Calendario.Periodo FROM CE_Calendario WHERE (((CE_Calendario.Tipo_Impuesto)=" . $Tipo . ")) GROUP BY CE_Calendario.Periodo, RIGHT(Periodo,4) HAVING (((RIGHT(Periodo,4))=" . $_POST['OANNO'] . ")) ORDER BY CE_Calendario.Periodo DESC , RIGHT(Periodo,4) DESC;";
									$tabla_x = mysql_query($consulta_x);

									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';

										//-----------------
										if ($_GET[periodo] <> "" and $_GET[periodo] == $registro_x->Periodo) {
											echo ' selected="selected" ';
										} else {
											if ($_GET[periodo] == "" and $_POST['OPERIODO'] == $registro_x->Periodo) {
												echo ' selected="selected" ';
											}
										}
										//-----------------

										echo ' value="';
										echo $registro_x->Periodo;
										echo '">';
										echo $registro_x->Periodo;
										echo '</option>';
									}
									?>
						</select>
					</div>
				</td>
				<td width="89" bgcolor="#CCCCCC">
					<div align="center"><strong>Quincena</strong></div>
				</td>
				<td width="134" bgcolor="#FFFFFF">
					<div align="center">
						<select name="OQUINCENA" size="1" onChange="this.form.submit()">
							<option value="-1">-> Seleccione <-< /option>
									<?php
									if ($_GET[tipo] <> "") {
										$Tipo = $_GET[tipo];
									} else {
										$Tipo = $_POST['OTIPO'];
									}

									$consulta_x = "SELECT Quincena FROM CE_Calendario WHERE Tipo_Impuesto=" . $Tipo . " GROUP BY Quincena ORDER BY Quincena DESC;";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										echo '<option';

										//-----------------
										if ($_GET[quincena] <> "" and $_GET[quincena] == $registro_x->Quincena) {
											echo ' selected="selected" ';
										} else {
											if ($_GET[quincena] == "" and $_POST['OQUINCENA'] == $registro_x->Quincena) {
												echo ' selected="selected" ';
											}
										}
										//-----------------

										echo ' value="';
										echo $registro_x->Quincena;
										echo '">';

										//----------- QUINCENA
										switch ($registro_x->Quincena) {
											case 0:
												echo "No Aplica";
												break;
											case 1:
												echo "1ra";
												break;
											case 2:
												echo "2da";
												break;
										}
										//-----------

										echo '</option>';
									}
									?>
						</select>
					</div>
				</td>
				<td width="101" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Vencimiento</strong></div>
				</td>
				<td width="166" bgcolor="#FFFFFF">
					<div align="center">
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA_VEN" size="8" readonly value="<?php

																																// BUSQUEDA DE LA FECHA DE VENCIMIENTO
																																if ($_GET[periodo] <> "") {
																																	$PERIODO = $_GET[periodo];
																																} else {
																																	$PERIODO = $_POST['OPERIODO'];
																																}

																																if (($_GET[periodo] <> "" or $_POST['OPERIODO'] <> "-1") and ($_POST['OQUINCENA'] <> "-1" and substr($_POST['ORIF'], 9, 1) <> "")) {
																																	$consulta_x = "SELECT date_format(Fecha_Ven,'%Y/%m/%d') as Fecha FROM CE_Calendario WHERE Periodo='" . $PERIODO . "' AND Rif LIKE '%" . substr($_POST['ORIF'], 9, 1) . "%' AND Tipo_Impuesto=" . $_POST['OTIPO'] . " AND Quincena=" . $_POST['OQUINCENA'] . ";";
																																	$tabla_x = mysql_query($consulta_x);

																																	if ($registro_x = mysql_fetch_object($tabla_x)) {
																																		if ($registro_x->Fecha <> '') {
																																			echo $registro_x->Fecha;
																																		} else {
																																			echo 'No Aplica';
																																		}
																																	}
																																}


																																?>">
					</div>
					</div>
				</td>

			</tr>
		</table>
		<table align="center" width="719" border="1">
			<tr>
				<td width="81" bgcolor="#CCCCCC">
					<div align="center"><strong>N&deg; Formulario </strong></div>
				</td>
				<td width="72" bgcolor="#FFFFFF">
					<div align="center">
						<input type="text" name="ONUMERO" size="12" value="<?php if ($_POST['CMDBUSCAR'] == 'Buscar') {
																				echo $_POST['ONUMERO'];
																			} ?>">
						<label></label>
					</div>
				</td>
				<td width="102" height="30" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Presentacion </strong></div>
				</td>
				<td width="112" bgcolor="#FFFFFF">
					<div align="center">
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA_PRE" size="8" readonly value="<?php if ($_POST['CMDBUSCAR'] == 'Buscar') {
																																	echo $_POST[OFECHA_PRE];
																																} ?>">
					</div>
				</td>

				<td width="44" height="30" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Pago</strong></div>
				</td>
				<td width="108" bgcolor="#FFFFFF">
					<div align="center">
						<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA_PAGO" size="8" readonly value="<?php if ($_POST['CMDBUSCAR'] == 'Buscar') {
																																		echo $_POST['OFECHA_PAGO'];
																																	} ?>">
					</div>
				</td>

				<td width="65" bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td width="82" bgcolor="#FFFFFF">
					<div align="center"><a href="javascript:NewCssCal('OFECHA','YYYYMMDD')"></a>
						<input type="text" name="OMONTO" maxlength="15" size="10" value="<?php if ($_POST['CMDBUSCAR'] == 'Buscar') {
																								echo $_POST['OMONTO'];
																							} ?>">
					</div>
				</td>
			</tr>
		</table>

		<table align="center" width="719" border="1">
			<tr>
				<td width="98" height="30" bgcolor="#CCCCCC">
					<div align="center"><strong>Rif</strong></div>
				</td>
				<td width="188" bgcolor="#FFFFFF">
					<div align="center">
						<input type="text" name="ORIF" maxlength="10" size="10" value="<?php echo strtoupper($_POST['ORIF']); ?>">
						<label>
							<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
						</label>
					</div>
				</td>
				<td width="102" bgcolor="#CCCCCC">
					<div align="center"><strong>Tel&eacute;fono</strong></div>
				</td>
				<?php
				if ($_POST['ORIF'] <> "") {
					// BUSQUEDA DEL CONTRIBUYENTE
					$consulta_x = "SELECT * FROM contribuyentes WHERE Rif='" . $_POST['ORIF'] . "';";
					$tabla_x = mysql_query($consulta_x);
					if ($registro_x = mysql_fetch_object($tabla_x)) {
						$Contribuyente = $registro_x->contribuyente;
						$Telefono = $registro_x->Telefonos;
					} else {
						$Contribuyente = '<<< No existe Contribuyente Especial con ese Rif >>>';
					}
					// FIN
				}
				?>
				<td width="303" bgcolor="#FFFFFF">
					<div align="center"><?php echo $Telefono; ?></div>
				</td>
			</tr>
			<tr>

				<td width="98" height="30" bgcolor="#CCCCCC">
					<div align="center"><strong>Contribuyente</strong></div>
				</td>
				<td colspan="3" bgcolor="#FFFFFF">
					<div align="center"><?php echo $Contribuyente; ?></div>
				</td>
			</tr>
		</table>

		<p align="center"><span class="Estilo16">&lt; Para separar los decimales utilice coma &quot;,&quot; NO el punto &quot;.&quot; &gt;</span></p>
		<p align="center">
			<input type="submit" class="boton" name="CMDAGREGAR" value="Agregar">
		</p>
		<p align="center">&nbsp;</p>
	</form>

	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>