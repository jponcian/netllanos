<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 77;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['AREA'] = $_POST['OAREA'];
$_SESSION['DIVISION'] = $_POST['ODIVISION'];
$_SESSION['SEDE'] = $_POST['OSEDE'];
//---------------

?>
<html>

<head>
	<!--<link rel="stylesheet" type="text/css" href="../plugins/datetimepicker-master/jquery.datetimepicker.min.css">
-->
	<link rel="stylesheet" href="../plugins/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="../plugins/jquery-ui/jquery-ui.theme.min.css" />
	<link rel="stylesheet" href="../plugins/alertify/css/alertify.css">
	<link rel="stylesheet" href="../plugins/alertify/css/themes/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="../plugins/fontawesome/css/all.css">
	<link rel="stylesheet" type="text/css" href="../plugins/bootstrap/css/bootstrap.min.css">

	<script language="JavaScript" src="../plugins/bootstrap/js/bootstrap.min.js"></script>
	<script language="JavaScript" src="../plugins/alertify/alertify.js"></script>
	<script language="JavaScript" src="../plugins/jquery/jquery.min.js"></script>
	<script language="JavaScript" src="../plugins/jquery-ui/jquery-ui.min.js"></script>
	<!--<script language="JavaScript" src="../plugins/datetimepicker-master/jquery.datetimepicker.full.js"></script>
<script language="JavaScript" src="../plugins/bootstrap-datepicker.js"></script>-->


	<title>Men&uacute; de Reportes</title>
	<!--<style type="text/css">

.Estilomenun {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
body {
	background-image: url();
}
.Estilo1 {
	font-size: 18px;
	font-weight: bold;
	color:#FF0000;
}
.Estilo2 {color: #FFFFFF}
.Estilo3 {font-size: 24px}
.Estilo5 {color: #FFFFFF; font-size: 22px; }
.Estilo10 {font-size: 20px}
.Estilo7 {font-size: 22px}
.Estilo8 {color: #000000}
.Estilo77 {
	font-size: 18px;
	font-weight: bold;
	color: #FFFFFF;
}


  </style>-->
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<!--<meta http-equiv="refresh" content="30">-->
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

	<form name="form1" method="post" onSubmit="return evitar();">

		<table width="60%" border="1" align="center">
			<tr>
				<td height="40" align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo77"><u>Inventario Bienes
							Nacional</u></span></td>
			</tr>
			<tr>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Dependencia:</strong></td>
				<td width="14%"><label><span class="">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="0">--> Todas <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
											$consulta_x = 'SELECT z_jefes_detalle.id_sector, z_sectores.nombre FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division INNER JOIN z_sectores ON z_sectores.id_sector = z_jefes_detalle.id_sector WHERE z_sectores.id_sector<=5 GROUP BY z_jefes_detalle.id_sector;';
										} else {
											$consulta_x = 'SELECT z_jefes_detalle.id_sector, z_sectores.nombre FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division INNER JOIN z_sectores ON z_sectores.id_sector = z_jefes_detalle.id_sector WHERE z_sectores.id_sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY z_jefes_detalle.id_sector;';
										}
										//-------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['id_sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
										}

										?>
							</select></span></label></td>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Divisi&oacute;n:</strong></td>
				<td colspan="1"><label><span class="">
							<select name="ODIVISION" size="1" onChange="this.form.submit()">
								<option value="0">--> Todas <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE id_sector=0' . $_POST['OSEDE'] . ' GROUP BY z_jefes_detalle.division';
										} else {
											$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE id_sector=0' . $_POST['OSEDE'] . ' GROUP BY z_jefes_detalle.division';
										}
										//-----------------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['ODIVISION'] == $registro_x['division']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['division'] . '>' . palabras($registro_x['descripcion']) . '</option>';
										}
										?>
							</select>
						</span></label></td>
				<td height="35" colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Area:</strong></td>
				<td colspan="1"><label><span class="">
							<select name="OAREA" id="OAREA" size="1" onChange="listar_bienes();">
								<option value="0">--> Todas <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT bn_areas.id_area, bn_areas.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE bn_areas.division=0' . $_POST['ODIVISION'] . ' GROUP BY bn_areas.id_area ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
										} else {
											$consulta_x = 'SELECT bn_areas.id_area, bn_areas.descripcion FROM bn_bienes INNER JOIN bn_areas ON bn_areas.id_area = bn_bienes.id_area INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.division WHERE bn_areas.division=0' . $_POST['ODIVISION'] . ' GROUP BY bn_areas.id_area ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
										}

										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OAREA'] == $registro_x['id_area']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_area'] . '>' . palabras($registro_x['descripcion']) . '</option>';
										}
										?>
							</select></span></label></td>
			</tr>
		</table>
		<br>
		<table width="60%" border="0" align="center">
			<tr align="right">
				<td colspan="5" align="right"><button onClick="limpiar();" type="button" id="botonb"
						class="btn btn-outline-danger" onClick="rep();"><i class="fas fa-history"></i> Reiniciar
						Listado</button>

			<tr>
				<td height="40">
					<strong>Opciones de Filtrado:</strong>
				</td>
				<td>
					<div class="form-check ">
						<label class="form-check-label">
							Pendiente<input type="radio" class="form-check-input" name="optradio" value="1"
								onClick="listar_bienes();" checked>

						</label>
					</div>
				</td>
				<td>
					<div class="form-check ">
						<label class="form-check-label">
							Revisados<input type="radio" class="form-check-input" name="optradio" value="2"
								onClick="listar_bienes();">

						</label>
					</div>
				</td>
				<td>
					<div class="form-check ">
						<label class="form-check-label">
							Ultimos Escaneos<input type="radio" class="form-check-input" name="optradio" value="5"
								onClick="listar_bienes();">

						</label>
					</div>
				</td>
				<td>
					<div class="form-check ">
						<label class="form-check-label">
							Ver Todos<input type="radio" class="form-check-input" name="optradio" value="3"
								onClick="listar_bienes();">
						</label>
					</div>
				</td>
			<tr>
				<td colspan="4">
					<p>
						<diw class="row ">
							<table align="center">
								<tr>
									<td>
										<div class="form-check ml-4">
											<strong>Verificar Bien Nacional => </strong>
										</div>
									</td>
									<td>
										<div class="form-check ml-8">
											<input name="obien" id="obien" type="text" size="10" class="form-control"
												onKeyUp="verificar(event,this.value)" onFocus="this.select()" />
										</div>
									</td>
								</tr>
							</table>
						</diw>
				</td>
			</tr>
			</tr>
		</table>
		<br>
		<div id="div1"></div>

	</form>

	<p>&nbsp;</p>
	<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>
<script language="JavaScript">
	function evitar() {
		return false;
	}
	//------------------
	function rep() {
		if (document.form1.optradio.value > 0) {
			//{
			window.open("reportes/2_verificados.php", "_blank");
			//}
		}
	}
	//--------------------- PARA BUSCAR
	function listar_bienes() {
		$('#div1').html('<div align="center"><img width="125" height="125" src="../images/espera.gif"/><br/>Un momento, por favor...</div>');
		$('#div1').load('5a_tabla.php?id=' + document.form1.OAREA.value + '&tipo=' + document.form1.optradio.value);
		document.form1.obien.value = '';
		$('#obien').focus();
	}
	//----------------------------
	function cambiar(id, revisado) {
		alertify.confirm("Estas seguro de Cambiar el Estatus?",
			function () {
				var parametros = "id=" + id + "&revisado=" + revisado;
				$.ajax({
					url: "5c_reiniciar.php",
					type: "POST",
					data: parametros,
					success: function (r) {
						alertify.success('Bien Nacional procesado correctamente');
						//--------------
						listar_bienes();
						//document.form1.obien.value='';
						//$('#obien').focus();
					}
				});
			});
	}
	//----------------------------
	function limpiar() {
		alertify.confirm("Estas seguro de Reiniciar la Dependencia?",
			function () {
				var parametros = "id=" + document.form1.OAREA.value;
				$.ajax({
					url: "15a_reiniciar.php",
					type: "POST",
					data: parametros,
					success: function (r) {
						alertify.success('Listado Reiniciado Correctamente');
						//--------------
						listar_bienes();
						document.form1.obien.value = '';
						$('#obien').focus();
					}
				});
			});
	}
	//----------------------------
	function verificar(e, id) {
		(e.keyCode) ? k = e.keyCode : k = e.which;
		// Si la tecla pulsada es enter (codigo ascii 13)
		if (k == 13) {
			alertify.notify('Procesando...');
			//----------
			var parametros = "id=" + id;
			$.ajax({
				url: "15b_reasignar.php?dir=" + document.form1.OAREA.value,
				dataType: "json",
				type: "POST",
				data: parametros,
				success: function (data) {
					if (data.tipo == "info") {
						alertify.success(data.msg);
					} else {
						alertify.alert(data.msg);
					}
					//	alertify.success(data.msg);
					//--------------
					listar_bienes();
					document.form1.obien.value = '';
					document.form1.obien.focus;
				}
			});
		}

	}
</script>

</html>