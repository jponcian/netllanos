<?php
session_start();
include "../conexion.php";
include "../funciones/auxiliar_php.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 35;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['SEDE'] = $_POST['OSEDE'];
?>
<!doctype html>
<html>

<head>
	<title>Gesti&oacute;n de Expediente Sucesiones</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>
	<script type="text/javascript" src="../funciones/jquery.js"></script>
</head>
<script type="text/javascript">
	$(document).ready(function() {
		//alert("JQuery done");
		$('#incluir').hide();
		$("#CMDGUARDAR").hide();

		$('#ONUM').change(function() {
			$('#incluir').show();
			$("#CMDGUARDAR").show();
			var sector = $('#OSEDE').val();
			var anno = $('#OANNO').val();
			var numero = $('#ONUM').val();
			var datos = "&sector=" + sector + '&anno=' + anno + '&numero=' + numero;
			//alert(datos);
			$.ajax({
				url: "4-4buscar_expediente.php",
				type: "POST",
				data: datos,
				dataType: "json",
				success: function(r) {
					if (r.permitido == true) {
						$('#OFECHA').html(r.fecha_registro);
						$('#FECHAR').val(r.fecha_registro);
						$('#ORIF').val(r.rif);
						$('#OFECHAF').val(r.fecha_fall);
						$('#OCEDULA').val(r.cedula);
						$('#OSUCESION').val(r.sucesion);
						$('#OCOORDINADOR').val(r.coordinador);
						$('#OFUNCIONARIO').val(r.funcionario);
						$('#OID').val(r.indice);
						$("#CMDGUARDAR").attr('value', 'Modificar');
						busca_rif();
					} else {
						$("#CMDGUARDAR").attr('value', 'Guardar');
						$('#OFECHA').html("<?php echo date("d/m/Y"); ?>");
						$('#FECHAR').val("<?php echo date("d/m/Y"); ?>");
						$('#ORIF').val(r.rif);
						$('#OFECHAF').val(r.fecha_fall);
						$('#OCEDULA').val(r.cedula);
						$('#OSUCESION').val(r.sucesion);
						$('#OCOORDINADOR').val(r.coordinador);
						$('#OFUNCIONARIO').val(r.funcionario);
						$('#OID').val(r.indice);
						$('#ONOMBRE').html("");
						$('#ODIRECCION').html("");
						$('#OCOORDINADOR').val(-1);
						$('#OFUNCIONARIO').val(-1);
					}
					buscar_resolucion();
				}
			});
		});

		$('#ORIF').blur(function() {
			//alert("Salio");
			busca_rif();
		});

		$('#CMDGUARDAR').click(function() {
			if (validar_vacios()) {
				if ($("#CMDGUARDAR").val() == "Modificar") {
					var accion = "Modificar";
				} else {
					var accion = "Agregar";
				}
				var sector = $('#OSEDE').val();
				var anno = $('#OANNO').val();
				var numero = $('#ONUM').val();
				var fechareg = $('#FECHAR').val();
				var rif = $('#ORIF').val();
				var fechafall = $('#OFECHAF').val();
				var ced = $('#OCEDULA').val();
				var sucesion = $('#OSUCESION').val();
				var coord = $('#OCOORDINADOR').val();
				var func = $('#OFUNCIONARIO').val();
				var indice = $('#OID').val();
				var datos = "rif=" + rif + "&fechareg=" + fechareg + "&fechafall=" + fechafall + "&ced=" + ced + "&sucesion=" + sucesion + "&coord=" + coord + "&func=" + func + "&accion=" + accion + "&sector=" + sector + '&anno=' + anno + '&numero=' + numero + '&indice=' + indice;
				//alert(datos);
				$.ajax({
					url: "4-2procesar.php",
					type: "POST",
					data: datos,
					dataType: "json",
					success: function(r) {
						alert(r.mensaje)
						if (r.procesado == true) {
							$('#form1').reset();
						}
					}
				});
			} else {
				alert("!!!...Existen campos vac�os, por favor verifique...!!!");
			}
		});

		function busca_rif() {
			var rif = $('#ORIF').val();
			var datos = "rif=" + rif;
			$.ajax({
				url: "4-3buscar_rif.php",
				type: "POST",
				data: datos,
				dataType: "json",
				success: function(r) {
					//alert(r.permitido);
					$('#RIF').val(r.rif);
					$('#ONOMBRE').html(r.contribuyente);
					$('#ODIRECCION').html(r.direccion);
				}
			});
		}

		function buscar_resolucion() {
			var sector = $('#OSEDE').val();
			var anno = $('#OANNO').val();
			var numero = $('#ONUM').val();
			var datos = "sector=" + sector + "&anno=" + anno + "&numero=" + numero;
			//alert(datos);
			$.ajax({
				url: "4-4buscar_resolucion.php",
				type: "POST",
				data: datos,
				dataType: "json",
				success: function(r) {
					if (r.permitido == true) {
						$('#ORIF').attr("readonly", "readonly");
						$('#OCEDULA').attr("readonly", "readonly");
						$('#OSUCESION').attr("readonly", "readonly");
						$('#OFECHAF').attr("disabled", true);
						$('#RESOLUCION').val(r.resolucion);
					} else {
						$('#ORIF').removeAttr("readonly");
						$('#OCEDULA').removeAttr("readonly");
						$('#OSUCESION').removeAttr("readonly");
						$('#OFECHAF').removeAttr("disabled");
						$('#RESOLUCION').val(0);
					}
				}
			});
		}

		function validar_vacios() {
			if ($('#RIF').val() == "" || $('#OFECHAF').val() == "" || $('#OCEDULA').val() == "" || $('#OSUCESION').val() == "" || $('#OCOORDINADOR').val() == -1 || $('#OFUNCIONARIO').val() == -1) {
				return false;
			} else {
				return true;
			}
		}

		$.fn.reset = function() {
			$(this).each(function() {
				this.reset();
			});
			$('#ONOMBRE').html("");
			$('#ODIRECCION').html("");
			$('#incluir').hide();
			$("#CMDGUARDAR").hide();
		}

	});
</script>
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

	.Estilo7 {
		font-size: 18px;
		font-weight: bold;
		color: #FFFFFF;
	}

	.Estilo15 {
		font-size: 14px;
	}

	.Estilo16 {
		color: #000000;
		font-weight: bold;
	}
	-->
</style>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>
	<form id="form7" name="form7" method="post">
		<div align="center">
			<table width="47%" border="1" align="center">
				<tr>
					<td height="49" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente a Gestionar - Sucesiones</u></span></td>
				</tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<span class="Estilo1">
							<select name="OSEDE" id="OSEDE" size="1">
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
						</span>
					</div>
				</td>
				<td width="65" bgcolor="#CCCCCC" align="right"><strong>
						A&ntilde;o:</strong></td>
				<td width="139"><label>
						<div align="center">
							<select name="OANNO" id="OANNO" style="text-align:center" size="1">
								<option value="-1">Seleccione</option>
								<?php
								$i = 0;

								while ($i <= 30) {
									echo '<option';
									if ($_POST['OANNO'] == (date('Y') - $i)) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo date('Y') - $i;
									echo '">';
									echo date('Y') - $i;
									echo '</option>';
									$i++;
								}
								?>
							</select>
						</div>
					</label></td>
				<td width="95" align="right" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
				<td width="99"><label>
						<div align="center">
							<select name="ONUM" id="ONUM" style="text-align:center" size="1">
								<option value="-1">Seleccione</option>
								<?php
								$i = 999;

								while ($i >= 1) {
									echo '<option';
									if ($_POST['ONUM'] == ($i)) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $i;
									echo '">';
									echo $i;
									echo '</option>';
									$i--;
								}
								?>
							</select>
						</div>
					</label></td>
			</table>
			<p>&nbsp;</p>
		</div>
		<div id="incluir">
			<table width="60%" border="1" align="center">
				<tbody>
					<tr>
						<td height="45" colspan="4" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente</u></span></td>
					</tr>
					<tr>
						<td width="17%" bgcolor="#CCCCCC"><strong>Fecha Registro:</strong></td>
						<td width="20%">
							<p align="center" id="OFECHA"></p>
						</td>
						<td width="25%" bgcolor="#CCCCCC"><strong>Fecha Fallecimiento:</strong></td>
						<td width="38%"><input style="text-align:center" onClick="javascript:scwShow(this,event);" type="text" name="OFECHAF" id="OFECHAF" value="" readonly></td>
					</tr>
					<tr>
						<td width="17%" bgcolor="#CCCCCC"><strong>Numero de Rif:</strong></td>
						<td width="20%"><input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" maxlength="10" value=""></td>
						<td width="25%" bgcolor="#CCCCCC"><strong>Nombre/Raz&oacute;n Social:</strong></td>
						<td width="38%">
							<p id="ONOMBRE" class="Estilo15"></p>
						</td>
					</tr>
					<tr>
						<td width="17%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
						<td colspan="3">
							<p id="ODIRECCION" class="Estilo15"></p>
						</td>
					</tr>
				</tbody>
			</table>
			<table width="60%" border="1" align="center">
				<tbody>
					<tr>
						<td height="43" colspan="4" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Causante</u></span></td>
					</tr>
					<tr>
						<td width="15%" bgcolor="#CCCCCC"><strong>Cedula Identidad:</strong></td>
						<td width="23%"><input type="text" name="OCEDULA" id="OCEDULA" value="" maxlength="9"></td>
						<td width="10%" bgcolor="#CCCCCC"><strong>Nombre:</strong></td>
						<td width="52%"><input type="text" name="OSUCESION" id="OSUCESION" value="" size="70"></td>
					</tr>
				</tbody>
			</table>
			<table width="60%" border="1" align="center">
				<tbody>
					<tr>
						<td height="45" colspan="2" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos de los Funcionarios</u></span></td>
					</tr>
					<tr>
						<td width="16%" bgcolor="#CCCCCC"><strong>Coordinador:</strong></td>
						<td width="84%"><label>
								<select name="OCOORDINADOR" id="OCOORDINADOR" size="1">
									<option value="-1">Seleccione</option>
									<?php
									//--------------------
									$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Coordinador' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='" . strtoupper($_SESSION['NOMBRE_MODULO']) . "';";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x))
									//-------------
									{
										echo '<option';
										if ($_POST['OCOORDINADOR'] == $registro_x->cedula) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->cedula;
										echo '">';
										echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
										echo '</option>';
									}
									?>
								</select>
							</label></td>
					</tr>
					<tr>
						<td width="16%" bgcolor="#CCCCCC"><strong>Funcionario:</strong></td>
						<td width="84%"><label>
								<select name="OFUNCIONARIO" id="OFUNCIONARIO" size="1">
									<option value="-1">Seleccione</option>
									<?php
									//--------------------
									$consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Funcionario' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='" . strtoupper($_SESSION['NOMBRE_MODULO']) . "';";
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x))
									//-------------
									{
										echo '<option';
										if ($_POST['OFUNCIONARIO'] == $registro_x->cedula) {
											echo ' selected="selected" ';
										}
										echo ' value="';
										echo $registro_x->cedula;
										echo '">';
										echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
										echo '</option>';
									}
									?>
								</select>
							</label></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="botonera" align="center">
			<p></p>
			<input type="button" name="CMDGUARDAR" id="CMDGUARDAR" value="Guardar">
		</div>
		<input type="hidden" name="OID" id="OID" value="">
		<input type="hidden" name="FECHAR" id="FECHAR" value="">
		<input type="hidden" name="RIF" id="RIF" value="">
		<input type="hidden" name="RESOLUCION" id="RESOLUCION" value="0">

	</form>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>

</body>

</html>