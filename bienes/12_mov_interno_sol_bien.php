<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 116;
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
		<div class="mx-auto" style="width:80%;">
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
								$consulta_x = 'SELECT id_sector_actual, sector_actual FROM vista_bienes_reasignaciones_pendientes WHERE por_reasignar = 1 GROUP BY id_sector_actual';
								$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
								while ($registro_x = mysqli_fetch_array($tabla_x)) {
									echo '<option value="' . $registro_x['id_sector_actual'] . '">' . palabras($registro_x['sector_actual']) . '</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-6 my-1">
							<label for="ODIVISION" class="form-label small"><strong>División:</strong></label>
							<select name="ODIVISION" id="ODIVISION" class="form-select form-select-sm"
								onChange="cargar_tabla();">
								<option value="0">Seleccione</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="card mb-2">
				<div class="card-header bg-danger text-white text-center py-2">
					<u>Bienes Nacionales Pendientes</u>
				</div>
				<div class="card-body py-2">
					<div id="div1"></div>
				</div>
			</div>
		</div>
	</form>
	<?php include "../funciones/footNew.php"; ?>
</body>

</html>
<script>
	function cargar_combo(tipo, val) {
		return $.ajax({
			type: "POST",
			url: '12_combo.php?sede=' + document.form1.OSEDE.value,
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
			timer: 2000,
			toast: true
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
		// Fijar la división del usuario
		var divisionSelect = $('#ODIVISION');
		divisionSelect.val('{$div_user}');

		// Deshabilitar selects y crear inputs ocultos para mantener los valores en envíos
		$('#OSEDE, #ODIVISION').prop('disabled', true);
		$('input[name="OSEDE"].injected').remove();
		$('input[name="ODIVISION"].injected').remove();
		$('<input>').attr({type:'hidden',name:'OSEDE',value:'{$sede_user}'}).addClass('injected').appendTo('form[name="form1"]');
		$('<input>').attr({type:'hidden',name:'ODIVISION',value:'{$div_user}'}).addClass('injected').appendTo('form[name="form1"]');

		// Cargar tabla si ya existe división válida
		var divVal = divisionSelect.val();
		if (divVal && divVal !== '0') {
			if (typeof cargar_tabla === 'function') cargar_tabla();
		}
	});
});
JS;
	}
	?>
	function cargar_tabla() {
		var sedeVal = (document.querySelector('input[name="OSEDE"]') ? document.querySelector('input[name="OSEDE"]').value : (document.form1.OSEDE && document.form1.OSEDE.value ? document.form1.OSEDE.value : ''));
		var divVal = (document.querySelector('input[name="ODIVISION"]') ? document.querySelector('input[name="ODIVISION"]').value : (document.form1.ODIVISION && document.form1.ODIVISION.value ? document.form1.ODIVISION.value : ''));
		// No cargar si faltan parámetros
		if (!sedeVal || sedeVal === '0' || !divVal || divVal === '0') {
			$('#div1').html('');
			return;
		}
		$('#div1').load('12_bienes_pendientes.php?sede=' + sedeVal + '&division=' + divVal, function () {
			// Marcar todos los checkboxes por defecto y actualizar estados
			$('#div1').find('.check-bien').each(function () {
				this.checked = true;
				var fila = this.closest('tr'); if (fila) fila.classList.add('seleccionada');
			});
			// Llamar a las funciones del fragmento para actualizar checkbox maestro y botón
			if (typeof updateMasterCheckbox === 'function') try { updateMasterCheckbox(); } catch (e) { }
			if (typeof updateEnviarButton === 'function') try { updateEnviarButton(); } catch (e) { }
		});
	}
</script>