<?php
ob_end_clean();
session_start();
include "../conexion.php";
include "../auxiliar.php";

error_reporting(0);

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 13;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<title>Carga de Archivo TXT</title>
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

	<form name="form1" method="post" action="" enctype="multipart/form-data">
		<table width="500" border=1 align=center>
			<tr>
				<td height="40" align="center" bgcolor="#FF0000" colspan="3">
					<p class="Estilo7 Estilo1"><u>CARGA DE PLANILLAS (COLECTOR)</u></p>
				</td>
			</tr>
			<tr>
				<td width="70" bgcolor="#FF0000" align="center"><strong class="Estilo7 Estilo1">DEPENDENCIA</strong></td>
				<td width="300" bgcolor="#FF0000" align="center"><strong class="Estilo7 Estilo1">AGENCIA</strong></td>
				<td width="30" bgcolor="#FF0000" align="center"><strong class="Estilo7 Estilo1">FECHA</strong></td>
			</tr>
			<tr>
				<td align="center"><label></label>

					<select name="OSEDE" size="1" onChange="this.form.submit()">
						<option value="-1">Seleccione</option>
						<?php
						if ($_SESSION['ADMINISTRADOR'] > 0) {
							$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OSEDE'] == $registro_x['id_sector']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
							}
						} else {
							$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['OSEDE'] == $registro_x['id_sector']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
							}
						}
						?>
					</select>
				</td>

				<td align="center"><label></label>
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
				</td>
				<td align="center"><input onclick='javascript:scwShow(this,event);' name="OFECHA" type="text" size="10" readonly value="<?php echo $_POST['OFECHA'] ?>"></td>
			</tr>
			<tr>
				<td height="40" bgcolor="#FF0000" align="center" colspan="3"><strong class="Estilo7 Estilo1">COPIE Y PEGUE EL CONTENIDO DEL REPORTE AQUI</strong></td>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<p align="center">
						<textarea cols="85" rows="15" name="OTEXTO"><?php echo $_POST['OTEXTO']; ?>
                </textarea>
					</p>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="3" height="40" bgcolor="#CCCCC"><input name="CMDPROCESAR" type="submit" value="Procesar"></td>
			</tr>
		</table>
		<!--AQUI SE HACE EL CICLO-->
		<?php
		$texto = trim($_POST['OTEXTO']);
		if ($_POST['OSEDE'] > 0 and $_POST['OBANCO'] > 0 and $_POST['OFECHA'] <> "" and $texto <> "") {
		?>
			<br />
			<table align="center" border="1" style=" font-family:Arial, Helvetica, sans-serif; font-size:14px;">
				<tr bgcolor="#FF0000" class="Estilo7 Estilo1">
					<td align="center" colspan="6">PLANILLAS INGRESADAS</td>
				</tr>
				<tr bgcolor="#FF0000" class="Estilo7 Estilo1">
					<td>Item</td>
					<td>Forma</td>
					<td>Rif</td>
					<td>Periodo</td>
					<td>Planilla</td>
					<td>Monto</td>
				</tr>

			<?php

			//------------------
			list($banco, $agencia) = explode('-', $_POST['OBANCO']);

			$fecha_recaudacion = voltea_fecha($_POST['OFECHA']);

			$tama�o = strlen(trim($_POST['OTEXTO']));
			$texto = trim($_POST['OTEXTO']);
			$i = 0;
			$z = 0;
			$linea = 1;

			while ($i < $tama�o) {
				//-------------------------------------------- PARTE 1
				$ciclo1 = 0;
				$forma = '';
				$j = 0;
				$i = $i - $z;
				//-------------
				while ($ciclo1 == 0) {
					//----- SI ES UN ESPACIO SE SALTA A LA SIGUIENTE PALABRA
					if (substr($texto, $i, 1) == ' ') {
						$i++;
					}
					//-----------
					$forma = $forma . trim(substr($texto, $i, 1));
					//---- YA SE PUEDE BUSCAR LA FORMA EN LA TABLA
					if (strlen(trim($forma)) > 1) {
						$consulta = "SELECT Numero, Tipo FROM ce_cal_tip_obligaciones WHERE forma_juridico=0" . $forma . " or forma_natural=0" . $forma . ";";
						//echo $consulta;
						$tabla_xxx = mysql_query($consulta);
						if ($registro_xxx = mysql_fetch_object($tabla_xxx)) {
							echo '<tr><td><p align="center">' . $linea . '</p></td>';
							echo '<td><p align="left">' . $registro_xxx->Numero . ' - ' . $registro_xxx->Tipo . '</p></td>';
							$forma = '';
							$valor_forma = $registro_xxx->Numero;
							$j = 0;
							$i++;
							//-------------------------------------------- PARTE 2
							$ciclo2 = 0;
							$rif = '';
							$j = 0;
							while ($ciclo2 == 0) {
								$rif = $rif . trim(substr($texto, $i, 1));
								//---- YA SE PUEDE BUSCAR EL RIF EN LA TABLA
								if (strlen(trim($rif)) == 10) {
									$consulta = "SELECT rif FROM contribuyentes WHERE rif='" . $rif . "';";
									$tabla_xxx = mysql_query($consulta);
									if ($registro_xxx = mysql_fetch_object($tabla_xxx)) {
										echo '<td><p align="center">' . $registro_xxx->rif . '</p></td>';
										$rif = '';
										$valor_rif = $registro_xxx->rif;
										$i++;
										//-------------------------------------------- PARTE 3
										$ciclo3 = 0;
										$periodo = '';
										$j = 0;
										$guion = 0;
										//-----------
										while ($ciclo3 == 0) {
											// PARA ADELANTAR HASTA EL GUION
											while ($guion == 0) {
												if (substr($texto, $i, 1) == '-') {
													$guion = 1;
													$i = $i - 3;
												}
												$i++;
											}
											//----- SI ES UN ESPACIO SE SALTA A LA SIGUIENTE PALABRA
											if (substr($texto, $i, 1) == ' ') {
												$periodo = '';
												$i++;
											}
											//--------
											$periodo = $periodo . trim(substr($texto, $i, 1));
											//---- YA SE PUEDE BUSCAR EL PERIODO EN LA TABLA
											if (strlen(trim($periodo)) == 7) {
												list($a, $b) = explode('-', $periodo);
												$periodo = $a . '/' . $b;
												//--------------
												$consulta = "SELECT Periodo FROM ce_calendario WHERE Periodo='" . $periodo . "';";
												$tabla_xxx = mysql_query($consulta);
												if ($registro_xxx = mysql_fetch_object($tabla_xxx)) {
													echo '<td><p align="center">' . $registro_xxx->Periodo . '</p></td>';
													$periodo = '';
													$valor_periodo = $registro_xxx->Periodo;
													$i++;
													//-------------------------------------------- PARTE 4
													$ciclo4 = 0;
													$formulario = '';
													$j = 0;
													while ($ciclo4 == 0) {
														$formulario = $formulario . trim(substr($texto, $i, 1));
														//---- NUMERO DE FORMULARIO
														if (strlen(trim($formulario)) == 10) {
															$planilla = $formulario;
															echo '<td><p align="center">' . $formulario . '</p></td>';
															$formulario = '';
															$i++;
															$i++;
															//$valor_formulario = $formulario;
															//-------------------------------------------- PARTE 5
															$ciclo5 = 0;
															$monto = '';
															$j = 0;
															$coma = 0;
															while ($ciclo5 == 0) {
																$monto = $monto . trim(substr($texto, $i, 1));
																//----SALTO DE LINEA
																if (substr($texto, $i, 1) == ',') {
																	$coma = 1;
																}
																if ($coma > 0) {
																	$coma++;
																}
																//--------------------------------------------
																//----MONTO DE LA PLANILLA
																if (substr($texto, $i, 1) == ' ' or $coma == 4) {
																	$cantidad = $monto;
																	echo '<td><p align="right">' . $monto . '</p></td>';
																	$monto = '';
																	$valor_monto = $monto;
																	//--------
																	$j = -5;
																	$z = 4;
																	//--------
																	$ciclo1 = 1;
																	$ciclo2 = 1;
																	$ciclo3 = 1;
																	$ciclo4 = 1;
																	$ciclo5 = 1;
																	//------------ PARA GUARDAR XXXXXXXXXXXXXXXXX
																	if ($valor_rif <> "") {
																		$sql = "SELECT * FROM ce_pagos WHERE Numero=" . $planilla . " and Tipo_Impuesto=" . $valor_forma;
																		$tabla = mysql_query($sql);
																		$existe = mysql_num_rows($tabla);
																		//---- PARA ELIMINAR LA PLANILLA Y AGREGARLA DE NUEVO
																		$existe = 0;
																		//----
																		if ($existe < 1) {
																			$guardar = guardar_planilla($valor_rif, $valor_periodo, $valor_forma, $agencia, $fecha_recaudacion, $cantidad, $planilla);
																		}
																	}
																	//------------ HASTA AQUI XXXXXXXXXXXXXXXXXXX
																}
																//--------------------------------------------
																$i++;
																$j++;
																if ($j > 50) {
																	$ciclo1 = 1;
																	$ciclo2 = 1;
																	$ciclo3 = 1;
																	$ciclo4 = 1;
																	$ciclo5 = 1;
																	$i = $tama�o;
																	$j = 0;
																	echo "<script type=\"text/javascript\">alert('���Error en el Monto!!!');</script>";
																}
															}
															//-------------------------------------------- FIN PARTE 5
														}
														//--------------------------------------------
														$i++;
														$j++;
														if ($j > 50) {
															$ciclo1 = 1;
															$ciclo2 = 1;
															$ciclo3 = 1;
															$ciclo4 = 1;
															$ciclo5 = 1;
															$i = $tama�o;
															$j = 0;
															echo "<script type=\"text/javascript\">alert('���Error en el Numero de Formulario!!!');</script>";
														}
													}
													//-------------------------------------------- FIN PARTE 4
												}
											}
											//----- SI TIENE MAS DE 7 CARACTERES SE PASA A LA SIGUIENTE PALABRA
											if (strlen(trim($periodo)) >= 7) {
												$espacio = 'no';
												while ($espacio == 'no') {
													if (substr($texto, $i, 1) == ' ') {
														$periodo = '';
														$espacio = 'si';
														$i--;
													}
													$i++;
												}
											}
											//--------------------------------------------
											$i++;
											$j++;
											if ($j > 50) {
												$ciclo1 = 1;
												$ciclo2 = 1;
												$ciclo3 = 1;
												$ciclo4 = 1;
												$ciclo5 = 1;
												$i = $tama�o;
												$j = 0;
												echo "<script type=\"text/javascript\">alert('���Error en el Periodo!!!');</script>";
											}
										}
										//-------------------------------------------- FIN PARTE 3
									}
								}
								//--------------------------------------------
								$i++;
								$j++;
								if ($j > 15) {
									$ciclo1 = 1;
									$ciclo2 = 1;
									$ciclo3 = 1;
									$ciclo4 = 1;
									$ciclo5 = 1;
									$i = $tama�o;
									$j = 0;
									echo "<script type=\"text/javascript\">alert('���Error en el Rif!!!');</script>";
								}
							}
							//-------------------------------------------- FIN PARTE 2
						}
					}
					//-------------------------------------------- FIN PARTE 1
					$i++;
					$j++;
					if ($j > 10) {
						$ciclo1 = 1;
						$i = $tama�o;
						$j = 0;
						echo "<script type=\"text/javascript\">alert('���Error en la Forma!!!');</script>";
					}
					echo '</tr>';
				}
				//--------------------------------------------
				$linea++;
			}
		}

		function guardar_planilla($rif, $periodo, $forma, $agencia, $fecha_recaudacion, $monto, $documento)
		{
			//DAMOS FORMATO VALIDO AL MONTO
			$monto = str_replace(".", "", $monto);
			$monto = str_replace(",", ".", $monto);

			//DETERMINAMOS SI TIENE QUINCENA
			$sqlquincena = mysql_query("SELECT Rif,Periodo,Quincena FROM ce_calendario WHERE Quincena>0 AND Rif LIKE '%" . substr($rif, 9, 1) . "%' AND Tipo_Impuesto=" . $forma);
			$reg_quincena = mysql_fetch_object($sqlquincena);

			//BUSCAMOS SI EL CONTRIBUYENTE HA PAGO ALGUNA QUINCENA CON ESE PERIODO
			if ($reg_quincena->Quincena > 0) {
				$sqlbuscar = mysql_query("SELECT Numero FROM ce_pagos WHERE Periodo='" . $periodo . "' AND Rif ='" . $rif . "' AND Tipo_Impuesto=" . $forma . "");
				$reg_buscar = mysql_fetch_object($sqlbuscar);
				if ($reg_buscar->Numero <> "") {
					$quincena = 2;
				} else {
					$quincena = 1;
				}
			} else {
				$quincena = 0;
			}

			//BUSCAMOS LA FECHA DE VENCIMIENTO
			$sqlfecha = mysql_query("SELECT date_format(Fecha_Ven,'%Y/%m/%d') as Fecha FROM CE_Calendario WHERE Periodo='" . $periodo . "' AND Rif LIKE '%" . substr($rif, 9, 1) . "%' AND Tipo_Impuesto=" . $forma . " AND Quincena=" . $quincena . "");
			$reg_fecha = mysql_fetch_object($sqlfecha);
			$fecha_vencimiento = $reg_fecha->Fecha;

			//BUSCAMOS EL SECTOR
			$sqlsector = "SELECT id_agencia, sector FROM a_agencia WHERE id_agencia=" . $agencia;
			$sqlsector = mysql_query($sqlsector);
			if ($reg_sector = mysql_fetch_object($sqlsector)) {
				$sector = $reg_sector->sector;
				$id_agencia = $reg_sector->id_agencia;
			} else {
				//EN CASO QUE LA AGENCIA NO ESTE REGISTRADA GUARDAMOS CERO PARA GENERAR REPORTE
				$sector = 0;
				$id_agencia = 0;
			}

			//------- PARA ELIMINAR LA PLANILLA Y AGREGARLA DE NUEVO
			$sql_del = "DELETE FROM ce_pagos WHERE Tipo_Impuesto='" . $forma . "' and Numero=" . $documento . ";"; //
			$sql_del = mysql_query($sql_del);
			//------ 

			//REGISTRAMOS LA PLANILLA
			$sql_add = "INSERT INTO ce_pagos (Rif,Tipo_Impuesto,Numero,Periodo,Quincena,Fecha_Ven,Fecha_Presentacion,Fecha_Pago,Agencia,Monto,Sector,txt) VALUES ('" . $rif . "'," . $forma . "," . $documento . ",'" . $periodo . "'," . $quincena . ",'" . $fecha_vencimiento . "','" . $fecha_recaudacion . "','" . $fecha_recaudacion . "'," . $id_agencia . "," . $monto . "," . $sector . ",1)";
			$sql_add = mysql_query($sql_add);
		}
			?>

			</table>

			<!--AQUI FINALIZA EL CICLO-->
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