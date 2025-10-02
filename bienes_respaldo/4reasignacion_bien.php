<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 75;
include "../validacion_usuario.php";
// mantenimiento();
list($funcionario, $cargo1, $cargo2, $division) = funcion_funcionario($_SESSION['CEDULA_USUARIO']);
?>

<!DOCTYPE html>
<html>

<head>
	<?php include "../funciones/headNew.php"; ?>
</head>

<body style="background: transparent !important;">
	<form name="form1" id="form1" method="post">
		<div class="mx-auto" style="width:70%;">
			<div class="card mb-2">
				<div class="card-header bg-danger text-white text-center py-2">
					<u>Origen Bien Nacional</u>
				</div>
				<div class="card-body py-2">
					<div class="row align-items-center">
						<div class="col-md-6 my-1">
							<label for="OSEDE" class="form-label small"><strong>Dependencia:</strong></label>
							<select name="OSEDE" id="OSEDE" class="form-select form-select-sm"
								onChange="cargar_combo(1,this.value);">
								<option value="0">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0 || $division == 9) {
									$consulta_x = 'SELECT z_sectores.id_sector, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_sectores.id_sector <= 5 group by nombre ORDER BY id_sector';
								} else {
									$consulta_x = 'SELECT z_sectores.id_sector, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_sectores.id_sector=' . $_SESSION['SEDE_USUARIO'] . ' AND z_sectores.id_sector <= 5 group by nombre ORDER BY id_sector';
								}
								$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
								while ($registro_x = mysqli_fetch_array($tabla_x)) {
									echo '<option ';
									// if ($_POST['OSEDE'] == $registro_x['id_sector']) {
									// 	echo 'selected="selected" ';
									// }
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-6 my-1">
							<label for="ODIVISION" class="form-label small"><strong>División:</strong></label>
							<select name="ODIVISION" id="ODIVISION" class="form-select form-select-sm">
								<option value="0">Seleccione</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="card mb-2">
				<div class="card-header bg-danger text-white text-center py-2">
					<u>Destino Bien Nacional</u>
				</div>
				<div class="card-body py-2">
					<div class="row align-items-center">
						<div class="col-md-4 my-1">
							<label for="OSEDE2" class="form-label small"><strong>Dependencia:</strong></label>
							<select name="OSEDE2" id="OSEDE2" class="form-select form-select-sm"
								onChange="cargar_combo2(2,this.value);">
								<option value="0">Seleccione</option>
								<?php
								$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
								$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
								while ($registro_x = mysqli_fetch_array($tabla_x)) {
									echo '<option ';
									// if ($_POST['OSEDE2'] == $registro_x['id_sector']) {
									// 	echo 'selected="selected" ';
									// }
									echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
								if ($_SESSION['ADMINISTRADOR'] > 0 || $division == 9) {
									echo '<option ';
									if ($_POST['OSEDE2'] == 100) {
										echo 'selected="selected" ';
									}
									echo ' value=100>Caracas</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-4 my-1">
							<label for="ODIVISION2" class="form-label small"><strong>División:</strong></label>
							<select name="ODIVISION2" id="ODIVISION2" class="form-select form-select-sm"
								onChange="cargar_combo3(3,this.value);">
								<option value="0">Seleccione</option>
							</select>
						</div>
						<div class="col-md-4 my-1">
							<label for="OAREA2" class="form-label small"><strong>Área:</strong></label>
							<select name="OAREA2" id="OAREA2" class="form-select form-select-sm"
								onChange="cargar_tabla();cargar_tabla2();">
								<option value="0">Seleccione</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div id="div1"></div>
		<br>
		<div id="div2z"></div>
	</form>
	<?php include "../funciones/footNew.php"; ?>
</body>

</html>
<script language="JavaScript">
	window.cargar_tabla = function (attempts) {
		attempts = attempts || 0;
		if (window.reasignInit && attempts < 5) {
			setTimeout(function () { window.cargar_tabla(attempts + 1); }, 200);
			return;
		}
		// obtener valores desde selects o hidden inputs si select está vacío
		var sede1 = document.form1.OSEDE && document.form1.OSEDE.value ? document.form1.OSEDE.value : (document.querySelector('input[name="OSEDE"]') ? document.querySelector('input[name="OSEDE"]').value : '');
		var div1 = document.form1.ODIVISION && document.form1.ODIVISION.value ? document.form1.ODIVISION.value : (document.querySelector('input[name="ODIVISION"]') ? document.querySelector('input[name="ODIVISION"]').value : '');
		var sede2 = document.form1.OSEDE2 && document.form1.OSEDE2.value ? document.form1.OSEDE2.value : (document.querySelector('input[name="OSEDE2"]') ? document.querySelector('input[name="OSEDE2"]').value : '');
		var div2 = document.form1.ODIVISION2 && document.form1.ODIVISION2.value ? document.form1.ODIVISION2.value : (document.querySelector('input[name="ODIVISION2"]') ? document.querySelector('input[name="ODIVISION2"]').value : '');
		var area1 = document.form1.OAREA2 && document.form1.OAREA2.value ? document.form1.OAREA2.value : (document.querySelector('input[name="OAREA2"]') ? document.querySelector('input[name="OAREA2"]').value : '');
		$('#div1').load('4_bienes_por_reasignar.php?sede1=' + sede1 + '&div1=' + div1 + '&sede2=' + sede2 + '&div2=' + div2 + '&area1=' + area1);
	}

	window.cargar_tabla2 = function (attempts) {
		attempts = attempts || 0;
		if (window.reasignInit && attempts < 5) {
			setTimeout(function () { window.cargar_tabla2(attempts + 1); }, 200);
			return;
		}
		var sede1 = document.form1.OSEDE && document.form1.OSEDE.value ? document.form1.OSEDE.value : (document.querySelector('input[name="OSEDE"]') ? document.querySelector('input[name="OSEDE"]').value : '');
		var div1 = document.form1.ODIVISION && document.form1.ODIVISION.value ? document.form1.ODIVISION.value : (document.querySelector('input[name="ODIVISION"]') ? document.querySelector('input[name="ODIVISION"]').value : '');
		var sede2 = document.form1.OSEDE2 && document.form1.OSEDE2.value ? document.form1.OSEDE2.value : (document.querySelector('input[name="OSEDE2"]') ? document.querySelector('input[name="OSEDE2"]').value : '');
		var div2 = document.form1.ODIVISION2 && document.form1.ODIVISION2.value ? document.form1.ODIVISION2.value : (document.querySelector('input[name="ODIVISION2"]') ? document.querySelector('input[name="ODIVISION2"]').value : '');
		var area1 = document.form1.OAREA2 && document.form1.OAREA2.value ? document.form1.OAREA2.value : (document.querySelector('input[name="OAREA2"]') ? document.querySelector('input[name="OAREA2"]').value : '');
		$('#div2z').load('4_bienes_reasignados.php?sede1=' + sede1 + '&div1=' + div1 + '&sede2=' + sede2 + '&div2=' + div2 + '&area1=' + area1);
	}

	function cargar_combo(tipo, val) {
		return $.ajax({
			type: "POST",
			url: '4_combo.php?sede=' + document.form1.OSEDE.value,
			data: 'tipo=' + tipo,
			success: function (resp) {
				$('#ODIVISION').html(resp);
			}
		});
		Swal.fire({
			position: 'bottom-end',
			icon: 'info',
			title: 'Por favor espere la carga de datos...',
			showConfirmButton: false,
			timer: 3000,
			toast: true
		});
	}

	function cargar_combo2(tipo, val) {
		return $.ajax({
			type: "POST",
			// pasar la división correcta (ODIVISION2) para poblar el combo de destino
			url: '4_combo.php?sede=' + document.form1.OSEDE2.value + '&division=' + document.form1.ODIVISION2.value,
			data: 'tipo=' + tipo,
			success: function (resp) {
				$('#ODIVISION2').html(resp);
			}
		});
		Swal.fire({
			position: 'bottom-end',
			icon: 'info',
			title: 'Por favor espere la carga de datos...',
			showConfirmButton: false,
			timer: 3000,
			toast: true
		});
	}

	function cargar_combo3(tipo, val) {
		return $.ajax({
			type: "POST",
			url: '4_combo.php?sede=' + document.form1.OSEDE2.value + '&division=' + document.form1.ODIVISION2.value,
			data: 'tipo=' + tipo,
			success: function (resp) {
				$('#OAREA2').html(resp);
			}
		});
		Swal.fire({
			position: 'bottom-end',
			icon: 'info',
			title: 'Por favor espere la carga de datos...',
			showConfirmButton: false,
			timer: 3000,
			toast: true
		});
	}

	// Flag para evitar que los handlers reaccionen durante la inicialización automatizada
	window.reasignInit = false;

	// Validación especial para selects según División
	$(document).ready(function () {
		$('#ODIVISION').on('change', function () {
			// Si estamos en fase de inicialización programática, no ejecutar la lógica
			if (window.reasignInit) return;
			var val = $(this).val();
			if (val != '9') {
				// Seleccionar automáticamente los valores requeridos y cargar combos/tablas
				$('#OSEDE2').val('1');
				cargar_combo2(2, '1');
				setTimeout(function () {
					$('#ODIVISION2').val('9');
					cargar_combo3(3, '9');
					setTimeout(function () {
						$('#OAREA2').val('17');
						$('#OAREA2').trigger('change'); // Dispara el evento para que cargar_tabla2() también se ejecute
						cargar_tabla();
					}, 300);
				}, 300);
			}
		});
	});
</script>

<?php
// Si el usuario NO es administrador ni pertenece a la división 9, forzamos el origen al SEDE/DIV del usuario y el destino a: sede=1, división=9, área=17
if (!($_SESSION['ADMINISTRADOR'] > 0 || $division == 9)) {
	// Obtener descripciones para mostrar en los selects (origen y destino)
	$sede_user = isset($_SESSION['SEDE_USUARIO']) ? intval($_SESSION['SEDE_USUARIO']) : 0;
	$div_user = isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0;
	$sede_desc = '';
	$div_user_desc = '';
	$div9_desc = '';
	$area17_desc = '';
	$q0 = "SELECT nombre FROM z_sectores WHERE id_sector = $sede_user LIMIT 1";
	$r0 = mysqli_query($_SESSION['conexionsqli'], $q0);
	if ($r0 && $row0 = mysqli_fetch_assoc($r0))
		$sede_desc = $row0['nombre'];
	$qdiv = "SELECT descripcion FROM z_jefes_detalle WHERE division = $div_user LIMIT 1";
	$rdiv = mysqli_query($_SESSION['conexionsqli'], $qdiv);
	if ($rdiv && $rowd = mysqli_fetch_assoc($rdiv))
		$div_user_desc = $rowd['descripcion'];
	$q1 = "SELECT descripcion FROM z_jefes_detalle WHERE division = 9 LIMIT 1";
	$r1 = mysqli_query($_SESSION['conexionsqli'], $q1);
	if ($r1 && $row1 = mysqli_fetch_assoc($r1))
		$div9_desc = $row1['descripcion'];
	$q2 = "SELECT descripcion FROM bn_areas WHERE id_area = 17 LIMIT 1";
	$r2 = mysqli_query($_SESSION['conexionsqli'], $q2);
	if ($r2 && $row2 = mysqli_fetch_assoc($r2))
		$area17_desc = $row2['descripcion'];

	if (trim($sede_desc) == '')
		$sede_desc = 'Sede ' . $sede_user;
	if (trim($div_user_desc) == '')
		$div_user_desc = 'División ' . $div_user;
	if (trim($div9_desc) == '')
		$div9_desc = 'Administración';
	if (trim($area17_desc) == '')
		$area17_desc = 'Administración - Área 17';

	$sede_desc_js = json_encode($sede_desc);
	$div_user_desc_js = json_encode($div_user_desc);
	$div9_desc_js = json_encode($div9_desc);
	$area17_desc_js = json_encode($area17_desc);

	// Insertamos script que setea selects de ORIGEN y DESTINO, los deshabilita y agrega inputs ocultos para garantizar el envío
	// Usamos heredoc para construir el JS de forma segura y encadenar las llamadas
	$js = <<<JS
	<script>
	$(function(){
		window.reasignInit = true;
		// Origen: fijar sede del usuario y poblar su división vía AJAX
		document.form1.OSEDE.value = '{$sede_user}';
		// Esperar a que el combo termine de poblarse antes de fijar la división
		cargar_combo(1, '{$sede_user}').done(function(){
			$('#ODIVISION').val('{$div_user}');
			$('#OSEDE,#ODIVISION').prop('disabled', true);
			$('input[name="OSEDE"].injected').remove();
			$('input[name="ODIVISION"].injected').remove();
			$('<input>').attr({type:'hidden',name:'OSEDE',value:'{$sede_user}'}).addClass('injected').appendTo('form[name="form1"]');
			$('<input>').attr({type:'hidden',name:'ODIVISION',value:'{$div_user}'}).addClass('injected').appendTo('form[name="form1"]');
			// Cargar tabla de bienes con el origen ya fijado
			cargar_tabla();
		});

		// Destino: no llamar a los AJAX que pueden filtrar por sesión; inyectamos directamente las opciones
		setTimeout(function(){
			$('#OSEDE2').val('1');
			$('#ODIVISION2').html('<option value="9">' + {$div9_desc_js} + '</option>');
			$('#ODIVISION2').val('9');
			$('#OAREA2').html('<option value="17">' + {$area17_desc_js} + '</option>');
			$('#OAREA2').val('17');
			$('#OSEDE2,#ODIVISION2,#OAREA2').prop('disabled', true);
			$('input[name="OSEDE2"].injected').remove();
			$('input[name="ODIVISION2"].injected').remove();
			$('input[name="OAREA2"].injected').remove();
			$('<input>').attr({type:'hidden',name:'OSEDE2',value:'1'}).addClass('injected').appendTo('form[name="form1"]');
			$('<input>').attr({type:'hidden',name:'ODIVISION2',value:'9'}).addClass('injected').appendTo('form[name="form1"]');
			$('<input>').attr({type:'hidden',name:'OAREA2',value:'17'}).addClass('injected').appendTo('form[name="form1"]');
			// Cargar tabla reasignados con destino ya fijado
			cargar_tabla2();
			window.reasignInit = false;
		}, 650);
	});
	</script>

JS;
	echo $js;
}
?>