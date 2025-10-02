<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Documento sin t&iacute;tulo</title>
</head>

<body style="background: transparent !important;">
	<form id="form1" name="form1" method="post" action="">
		<p align="center">&nbsp;</p>
		<p align="center">
			<textarea cols="60" rows="15" name="OTEXTO"><?php echo $_POST['OTEXTO']; ?>
</textarea>
		</p>
		<p align="center">

			<input type="submit" class="boton" name="Submit" value="Enviar" />
		</p>

		<table align="center" border="1">

			<?php
			error_reporting(0);
			session_start();
			include "../conexion.php";
			include "../auxiliar.php";

			//------------------

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
						$tabla_xxx = mysql_query($consulta);
						if ($registro_xxx = mysql_fetch_object($tabla_xxx)) {
							echo '<tr><td><p align="center">' . $linea . '</p></td>';
							echo '<td><p align="center">' . $registro_xxx->Numero . ' - ' . $registro_xxx->Tipo . '</p></td>';
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
															echo '<td><p align="center">' . $formulario . '</p></td>';
															$formulario = '';
															$i++;
															$i++;
															$valor_formulario = $formulario;
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
																	echo '<td><p align="center">' . $monto . '</p></td>';
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

			?>

		</table>
	</form>

</body>

</html>