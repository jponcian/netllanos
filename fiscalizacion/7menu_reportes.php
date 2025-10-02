<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 147;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Men&uacute; de Reportes</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<script src="../CONTROL_TRIBUTARIO/jquery/jquery.js" type="text/javascript"></script>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<script>
		$(document).ready(function() {
			//código a ejecutar cuando el DOM está listo para recibir instrucciones.
			//alert("Done...");

			$('#CMDREPORTE').on('click', function() {
				var opcion = $('input:radio[name=OOPCION]:checked').val();
				switch (opcion) {
					case '1':
						var archivo = "SIGER/prov_emitidas.php";
						break;
					case '2':
						var archivo = "SIGER/prov_notificadas.php";
						break;
					case '3':
						var archivo = "SIGER/prov_concluidas_act.php";
						break;
					case '4':
						var archivo = "SIGER/prov_concluidas_ant.php";
						break;
					case '5':
						var archivo = "SIGER/actas_notificadas.php";
						break;
					case '6':
						var archivo = "SIGER/actas_no_aceptadas.php";
						break;
					case '7':
						var archivo = "SIGER/actas_aceptadas_total.php";
						break;
					case '8':
						var archivo = "SIGER/actas_aceptadas_parcial.php";
						break;
					case '9':
						var archivo = "SIGER/Resol_emitidas_vdf.php";
						break;
					case '10':
						var archivo = "SIGER/Resol_notificadas_vdf.php";
						break;
					case '11':
						var archivo = "SIGER/Resol_pagadas_vdf.php";
						break;
					case '12':
						var archivo = "SIGER/Resol_allan_emitidas.php";
						break;
					case '13':
						var archivo = "SIGER/Resol_allan_notificadas.php";
						break;
					case '14':
						var archivo = "SIGER/Resol_allan_pagadas.php";
						break;
					case '15':
						var archivo = "SIGER/casos_proceso_act.php";
						break;
					case '16':
						var archivo = "SIGER/casos_proceso_ant.php";
						break;
					case '17':
						var archivo = "SIGER/programas_control_fiscal.php";
						break;
					case '18':
						var archivo = "SIGER/prov_concluidas_sanc_act.php";
						break;
					case '19':
						var archivo = "SIGER/prov_concluidas_sanc_ant.php";
						break;
					case '20':
						var archivo = "SIGER/prov_anuladas.php";
						break;
					case '21':
						var archivo = "SIGER/fiscalizaciones.php";
						break;
				}
				var sede = $('#OSEDE').val();
				var inicio = $('#OINICIO').val();
				var fin = $('#OFIN').val();
				var datos = 'sede=' + sede + '&inicio=' + inicio + '&fin=' + fin;
				if (opcion === '15' || opcion === '16') {
					window.open(archivo + '?' + datos, '_blank');
				} else {
					if ($('#OINICIO').val() && $('#OFIN').val()) {
						window.open(archivo + '?' + datos, '_blank');
					} else {
						alert("!!!...Por favor indique el periodo...!!!");
					}
				}
			});

		});
	</script>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" id="form1" method="post" action="#vista">
		<?php
		$sql_jefe = "SELECT rol FROM z_empleados WHERE cedula = " . $_SESSION['CEDULA_USUARIO'];
		$tabla_jefe = mysql_query($sql_jefe);
		$valor = mysql_fetch_object($tabla_jefe);
		$rol = $valor->rol;
		?>

		<table width="30%" border="1" align="center">
			<tr>
				<td height="33" colspan="2" align="center" bgcolor="#FF0000"><strong class="Estilo1 Estilo151">Dependencia</strong></td>
				<td colspan="4" align="center" bgcolor="#FF0000"><strong class="Estilo1 Estilo151">Fecha
					</strong></td>
			</tr>
			<tr>
				<td colspan="2"><span class="Estilo1">
						<select name="OSEDE" id="OSEDE" size="1">
							<?php
							if ($_SESSION['ADMINISTRADOR'] > 0 or $rol == 'J') {
								echo '<option ';
								if ($_POST['OSEDE'] == 0) {
									echo 'selected="selected" ';
								}
								echo ' value=0>Gerencia</option>';
								//------------
								$consulta_x = 'SELECT id_sector as sector, nombre as dependencia FROM z_sectores';
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
								}
							} else {
								if ($_SESSION['SEDE_USUARIO'] == 1) {
									echo '<option ';
									if ($_POST['OSEDE'] == 0) {
										echo 'selected="selected" ';
									}
									echo ' value=0>Gerencia</option>';
								}
								// --- VALIDACION DEL ORIGEN DEL USUARIO
								// -------------------------------------
								$consulta_x = 'SELECT id_sector as sector, nombre as dependencia FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'];
								$tabla_x = mysql_query($consulta_x);
								if ($registro_x = mysql_fetch_array($tabla_x)) {
									echo '<option ';
									if ($_POST['OSEDE'] == $registro_x['sector']) {
										echo 'selected="selected" ';
									}
									echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
								}
							}
							?>
						</select>
					</span></td>
				<td width="17%" bgcolor="#CCCCCC"><strong>Desde:</strong></td>
				<td><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OINICIO" id="OINICIO" size="10" readonly value="<?php echo $_POST['OINICIO']; ?>" />
						</div>
					</label></td>
				<td width="16%" bgcolor="#CCCCCC"><strong>
						Hasta:</strong></td>
				<td><label>
						<div align="center">
							<input onclick='javascript:scwShow(this,event);' type="text" name="OFIN" id="OFIN" size="10" readonly value="<?php echo $_POST['OFIN']; ?>" />
						</div>
					</label></td>


			</tr>
		</table>
		<p></p>

		<p>
		<table width="40%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Providencias</u></span></td>
			</tr>
			<tr>
				<td height="37" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" id="OOPCION" type="radio" value="1" checked="checked" <?php if ($_POST['OOPCION'] == '1') {
																												echo 'checked="checked" ';
																											} ?>>
							</label>Emitidas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="2" <?php if ($_POST['OOPCION'] == '2') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Notificadas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="20" <?php if ($_POST['OOPCION'] == '20') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Anuladas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="3" <?php if ($_POST['OOPCION'] == '3') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Concluidas Año Actual
						</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="4" <?php if ($_POST['OOPCION'] == '4') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Concluidas Años Anteriores
						</strong></div>
				</td>
			</tr>
		</table>

		<table width="40%" border="1" align="center">
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="18" <?php if ($_POST['OOPCION'] == '18') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Concluidas Año Actual (Sancionadas)
						</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="19" <?php if ($_POST['OOPCION'] == '19') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Concluidas Años Anteriores
						</strong><strong>(Sancionadas)</strong></div>
				</td>
			</tr>

		</table>
		<p></p>

		<p>
		<table width="40%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Actas Fiscales</u></span></td>
			</tr>
			<tr>
				<td height="37" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" id="OOPCION" type="radio" value="5" <?php if ($_POST['OOPCION'] == '5') {
																								echo 'checked="checked" ';
																							} ?>>
							</label>Notificadas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="6" <?php if ($_POST['OOPCION'] == '6') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							No Aceptadas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="7" <?php if ($_POST['OOPCION'] == '7') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Aceptadas Total
						</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="8" <?php if ($_POST['OOPCION'] == '8') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Aceptadas Parcial
						</strong></div>
				</td>
			</tr>
		</table>
		<p></p>

		<p>
		<table width="40%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Resoluciones VDF</u></span></td>
			</tr>
			<tr>
				<td height="37" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" id="OOPCION" type="radio" value="9" <?php if ($_POST['OOPCION'] == '9') {
																								echo 'checked="checked" ';
																							} ?>>
							</label>Emitidas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="10" <?php if ($_POST['OOPCION'] == '10') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Notificadas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="11" <?php if ($_POST['OOPCION'] == '11') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Pagadas
						</strong></div>
				</td>
			</tr>
		</table>
		<p></p>

		<p>
		<table width="40%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Resoluciones Allanamiento</u></span></td>
			</tr>
			<tr>
				<td height="37" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" id="OOPCION" type="radio" value="12" <?php if ($_POST['OOPCION'] == '12') {
																								echo 'checked="checked" ';
																							} ?>>
							</label>Emitidas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="13" <?php if ($_POST['OOPCION'] == '13') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Notificadas</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="14" <?php if ($_POST['OOPCION'] == '14') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Pagadas
						</strong></div>
				</td>
			</tr>
		</table>
		<p></p>

		<p>
		<table width="40%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Otros Reportes</u></span></td>
			</tr>
			<tr>
				<td height="37" bgcolor="#CCCCCC">
					<div align="center"><strong>
							<label>
								<input name="OOPCION" id="OOPCION" type="radio" value="15" <?php if ($_POST['OOPCION'] == '15') {
																								echo 'checked="checked" ';
																							} ?>>
							</label>Casos Proceso Año Actual</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="16" <?php if ($_POST['OOPCION'] == '16') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Casos Proceso Años Anteriores</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="17" <?php if ($_POST['OOPCION'] == '17') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Otros Programas de Control Fiscal
						</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>
							<input name="OOPCION" id="OOPCION" type="radio" value="21" <?php if ($_POST['OOPCION'] == '21') {
																							echo 'checked="checked" ';
																						} ?>>
							<label></label>
							Detalle Investigaciones Fiscales
						</strong></div>
				</td>
			</tr>
		</table>

		<p>&nbsp;</p>
	</form>

	<p>
		<?php

		if ($_POST['OOPCION'] <> "" and $_POST['OSEDE'] > 0 and $_POST['OINICIO'] <> "" and $_POST['OFIN'] <> "") {
			$_SESSION['OSEDE'] = $_POST['OSEDE'];
			$_SESSION['FECHA1'] = $_POST['OINICIO'];
			$_SESSION['FECHA2'] = $_POST['OFIN'];

			switch ($_POST['OOPCION']) {
				case 1:
					$reporte = "SIGER/prov_emitidas.php";
					break;
			}
		}
		?>
	<table width=300 align=center border=0>
		<td colSpan=1>
			<div align="center">
				<form name="form3" method="post" action="<?php echo $reporte ?>" target="_blank">
					<input type="button" name="CMDREPORTE" id="CMDREPORTE" value="Ver Reporte (Exportar a Excel)">
				</form>
			</div>
		</td>
		</tr>
	</table>

	<a name="vista"></a>
	<p>&nbsp;
	</p>
	<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>

</body>

</html>