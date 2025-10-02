<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 115;
include "../validacion_usuario.php";
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
									$consulta_x = 'SELECT z_sectores.id_sector, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_sectores.id_sector<=5 group by nombre ORDER BY id_sector';
								} else {
									$consulta_x = 'SELECT z_sectores.id_sector, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_sectores.id_sector=' . $_SESSION['SEDE_USUARIO'] . ' group by nombre ORDER BY id_sector';
								}
								$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
								while ($registro_x = mysqli_fetch_array($tabla_x)) {
									echo '<option value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-6 my-1">
							<label for="ODIVISION" class="form-label small"><strong>División:</strong></label>
							<select name="ODIVISION" id="ODIVISION" class="form-select form-select-sm"
								onChange="cargar_combo2(3,this.value);">
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
							<label for="OAREA" class="form-label small"><strong>Área:</strong></label>
							<select name="OAREA" id="OAREA" class="form-select form-select-sm"
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
		<!-- <br> -->
		<div id="div2z"></div>
	</form>
	<?php include "../funciones/footNew.php"; ?>
</body>

</html>
<script language="JavaScript">
	function cargar_tabla() {
		// Leer valores desde selects o desde inputs ocultos si los selects están deshabilitados
		var sede1 = (document.form1.OSEDE && document.form1.OSEDE.value) ? document.form1.OSEDE.value : (document.querySelector('input[name="OSEDE"]') ? document.querySelector('input[name="OSEDE"]').value : '');
		var div1 = (document.form1.ODIVISION && document.form1.ODIVISION.value) ? document.form1.ODIVISION.value : (document.querySelector('input[name="ODIVISION"]') ? document.querySelector('input[name="ODIVISION"]').value : '');
		var area1 = (document.form1.OAREA && document.form1.OAREA.value) ? document.form1.OAREA.value : (document.querySelector('input[name="OAREA"]') ? document.querySelector('input[name="OAREA"]').value : '');
		// No cargar la tabla si no hay área seleccionada
		if (!area1 || area1 === '0') {
			$('#div1').html('');
			return;
		}
		$('#div1').load('11_bienes_por_reasignar.php?sede1=' + sede1 + '&div1=' + div1 + '&area1=' + area1);
	}
	function cargar_tabla2() {
		// Leer valores desde selects o inputs ocultos
		var sede1 = (document.form1.OSEDE && document.form1.OSEDE.value) ? document.form1.OSEDE.value : (document.querySelector('input[name="OSEDE"]') ? document.querySelector('input[name="OSEDE"]').value : '');
		var div1 = (document.form1.ODIVISION && document.form1.ODIVISION.value) ? document.form1.ODIVISION.value : (document.querySelector('input[name="ODIVISION"]') ? document.querySelector('input[name="ODIVISION"]').value : '');
		var area1 = (document.form1.OAREA && document.form1.OAREA.value) ? document.form1.OAREA.value : (document.querySelector('input[name="OAREA"]') ? document.querySelector('input[name="OAREA"]').value : '');
		if (area1 && area1 !== '0') {
			$('#div2z').load('11_bienes_reasignados.php?sede1=' + sede1 + '&div1=' + div1 + '&area1=' + area1);
		} else {
			// Si no hay área seleccionada, limpiar el contenedor de reasignados
			$('#div2z').html('');
		}
	}
	function cargar_combo(tipo, val) {
		Swal.fire({
			position: 'bottom-end',
			icon: 'info',
			title: 'Por favor espere la carga de datos...',
			showConfirmButton: false,
			timer: 3000,
			toast: true
		});
		return $.ajax({
			type: "POST",
			url: '11_combo.php?sede=' + document.form1.OSEDE.value,
			data: 'tipo=' + tipo,
			success: function (resp) {
				$('#ODIVISION').html(resp);
				// Limpiar área al cambiar división
				$('#OAREA').html('<option value="0">Seleccione</option>');
			}
		});
	}
	function cargar_combo2(tipo, val) {
		Swal.fire({
			position: 'bottom-end',
			icon: 'info',
			title: 'Por favor espere la carga de datos...',
			showConfirmButton: false,
			timer: 3000,
			toast: true
		});
		return $.ajax({
			type: "POST",
			url: '11_combo.php?sede=' + document.form1.OSEDE.value + '&division=' + document.form1.ODIVISION.value,
			data: 'tipo=' + tipo,
			success: function (resp) {
				$('#OAREA').html(resp);
			}
		});
	}
	<?php
	// Si el usuario NO es administrador ni pertenece a la división 9, forzamos el origen al SEDE/DIV del usuario
	if (!($_SESSION['ADMINISTRADOR'] > 0 || $division == 9)) {
		$sede_user = isset($_SESSION['SEDE_USUARIO']) ? intval($_SESSION['SEDE_USUARIO']) : 0;
		$div_user = isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0;

		echo <<<JS
$(function(){
    // Fijar la sede del usuario y poblar su división vía AJAX
    var sedeSelect = $('#OSEDE');
    if (sedeSelect.val() !== '{$sede_user}') {
        sedeSelect.val('{$sede_user}');
    }

			cargar_combo(1, '{$sede_user}').done(function(){
				// Fijar la división del usuario (aunque la opción se haya insertado recién)
				var divisionSelect = $('#ODIVISION');
				divisionSelect.val('{$div_user}');

				// Llamamos a cargar_combo2 para poblar áreas; en su done manejamos la selección automática de área si hay una
				cargar_combo2(3, '{$div_user}').done(function() {
					// Si sólo hay una área válida además del option 0, seleccionarla automáticamente
					var areaSelect = $('#OAREA');
					var opts = areaSelect.find('option').filter(function(){ return this.value !== '0'; });
					if (opts.length === 1) {
						areaSelect.val(opts.first().val());
					}

					// Ahora que el combo de area está cargado, deshabilitamos los dos primeros selects
					$('#OSEDE, #ODIVISION').prop('disabled', true);
					$('input[name="OSEDE"].injected').remove();
					$('input[name="ODIVISION"].injected').remove();
					$('<input>').attr({type:'hidden',name:'OSEDE',value:'{$sede_user}'}).addClass('injected').appendTo('form[name="form1"]');
					$('<input>').attr({type:'hidden',name:'ODIVISION',value:'{$div_user}'}).addClass('injected').appendTo('form[name="form1"]');


					// Si ya hay un área seleccionada (no cero), cargar tablas
					var areaVal = areaSelect.val();
					if (areaVal && areaVal !== '0') {
						if (typeof cargar_tabla === 'function') cargar_tabla();
						if (typeof cargar_tabla2 === 'function') cargar_tabla2();
					}

				});
			});
});
JS;
	}
	?>
</script>