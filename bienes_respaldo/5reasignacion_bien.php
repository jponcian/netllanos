<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 113;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
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
								$consulta_x = 'SELECT id_sector_actual, sector_actual FROM vista_bienes_reasignaciones_pendientes WHERE por_reasignar = 1 GROUP BY id_sector_actual';
								$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
								while ($registro_x = mysqli_fetch_array($tabla_x)) {
									echo '<option value=' . $registro_x['id_sector_actual'] . '>' . $registro_x['sector_actual'] . '</option>';
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
		</div>
		<br>
		<div class="mx-auto" style="width:70%;">
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

	<script language="JavaScript">
		$(document).ready(function () {
			$('#form1').on('submit', function (e) {
				e.preventDefault();
				// Verificar si hay al menos un bien seleccionado
				var selected = $('input[name="bienes[]"]:checked').length;
				if (selected === 0) {
					Swal.fire({
						icon: 'warning',
						title: 'Seleccione al menos un bien',
						text: 'No hay bienes seleccionados para enviar la reasignación.',
						toast: true,
						position: 'bottom-end',
						timer: 2500,
						showConfirmButton: false
					});
					return;
				}

				Swal.fire({
					title: '¿Está seguro?',
					text: "Esta acción enviará los bienes seleccionados para su reasignación.",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Sí, ¡enviar!',
					cancelButtonText: 'Cancelar'
				}).then((result) => {
					if (result.isConfirmed) {
						var formData = $(this).serialize();

						$.ajax({
							type: "POST",
							url: "5_procesar_reasignacion.php",
							data: formData,
							dataType: 'json',
							success: function (response) {
								if (response.success) {
									Swal.fire({
										title: '¡Éxito!',
										text: response.message,
										icon: 'success'
									}).then(() => {
										// Reiniciar los selects y la tabla
										$('#OSEDE').prop('selectedIndex', 0);
										$('#ODIVISION').html('<option value="0">Seleccione</option>');
										$('#div1').html('');
									});
								} else {
									Swal.fire({
										title: 'Error',
										text: response.message,
										icon: 'error'
									});
								}
							},
							error: function () {
								Swal.fire({
									title: 'Error de Comunicación',
									text: 'No se pudo conectar con el servidor.',
									icon: 'error'
								});
							}
						});
					}
				});
			});
		});

		function cargar_tabla(callback) {
			var sede = document.form1.OSEDE.value;
			var division = document.form1.ODIVISION.value;

			if (sede > 0 && division > 0) {
				$('#div1').load('5a_tabla.php?sede=' + sede + '&div=' + division, function () {
					// Después de cargar la tabla, asegurar estado del botón enviar
					updateEnviarButton();
					if (typeof callback === 'function') {
						callback();
					}
				});
				Swal.fire({
					position: 'bottom-end',
					icon: 'info',
					title: 'Cargando la tabla...',
					showConfirmButton: false,
					timer: 1500,
					toast: true
				});
			} else {
				$('#div1').html(''); // Limpia la tabla si no hay selección
				updateEnviarButton();
			}
		}

		function cargar_combo(tipo, val) {
			return $.ajax({
				type: "POST",
				url: '5_combo.php?sede=' + encodeURIComponent(document.form1.OSEDE.value),
				data: 'tipo=' + tipo,
				beforeSend: function () {
					Swal.fire({
						position: 'bottom-end',
						icon: 'info',
						title: 'Cargando divisiones...',
						showConfirmButton: false,
						timer: 800,
						toast: true
					});
				},
				success: function (resp) {
					$('#ODIVISION').html(resp);
					// Limpiar la tabla al cambiar la dependencia
					$('#div1').html('');
				}
			});
		}

		// Habilita/deshabilita el input submit que venga dentro de #div1 según selección de bienes[]
		function updateEnviarButton() {
			var checks = $('#div1').find('input[name="bienes[]"]:checked').length;
			var btn = $('#div1').find('input[name="CMDAPROBAR"]');
			if (btn.length === 0) return; // no hay botón en la tabla cargada
			btn.prop('disabled', checks === 0);
		}

		// Delegación: cuando cambie cualquier checkbox cargado dinámicamente, actualizar el botón
		$(document).on('change', 'input[name="bienes[]"]', function () {
			updateEnviarButton();
		});
	</script>
	<?php
	// Si el usuario NO es administrador ni pertenece a la división 9, forzamos el origen al SEDE/DIV del usuario
	if (!($_SESSION['ADMINISTRADOR'] > 0 || (isset($_SESSION['DIVISION_USUARIO']) && $_SESSION['DIVISION_USUARIO'] == 9))) {
		$sede_user = isset($_SESSION['SEDE_USUARIO']) ? intval($_SESSION['SEDE_USUARIO']) : 0;
		$div_user = isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0;
		$sede_desc = '';
		$div_desc = '';
		// obtener descripciones para mostrar en los selects si es posible
		$q0 = "SELECT nombre FROM z_sectores WHERE id_sector = $sede_user LIMIT 1";
		$r0 = mysqli_query($_SESSION['conexionsqli'], $q0);
		if ($r0 && $row0 = mysqli_fetch_assoc($r0))
			$sede_desc = $row0['nombre'];
		$q1 = "SELECT descripcion FROM z_jefes_detalle WHERE division = $div_user LIMIT 1";
		$r1 = mysqli_query($_SESSION['conexionsqli'], $q1);
		if ($r1 && $row1 = mysqli_fetch_assoc($r1))
			$div_desc = $row1['descripcion'];
		if (trim($sede_desc) == '')
			$sede_desc = 'Sede ' . $sede_user;
		if (trim($div_desc) == '')
			$div_desc = 'División ' . $div_user;

		$sede_desc_js = json_encode($sede_desc);
		$div_desc_js = json_encode($div_desc);

		$js = <<<JS
	<script>
	$(function(){
		window.reasignInit = true;
		// Fijar la sede del usuario y poblar su división vía AJAX
		document.form1.OSEDE.value = '{$sede_user}';
		cargar_combo(1, '{$sede_user}').done(function(){
			// Si el combo devolvió opciones, fijar la división del usuario
			$('#ODIVISION').val('{$div_user}');
			
			// Cargar la tabla con los valores ya fijados
			cargar_tabla(function() {
				// Marcar por defecto los checkboxes de bienes[] y actualizar el botón enviar
				var checks = $('#div1').find('input[name="bienes[]"]');
				if (checks.length > 0) {
					checks.prop('checked', true).trigger('change');
					updateEnviarButton();
				}
			});

			// Deshabilitar selects y agregar inputs ocultos para garantizar el POST
			$('#OSEDE,#ODIVISION').prop('disabled', true);
			$('input[name="OSEDE"].injected').remove();
			$('input[name="ODIVISION"].injected').remove();
			$('<input>').attr({type:'hidden',name:'OSEDE',value:'{$sede_user}'}).addClass('injected').appendTo('form[name="form1"]');
			$('<input>').attr({type:'hidden',name:'ODIVISION',value:'{$div_user}'}).addClass('injected').appendTo('form[name="form1"]');

					// Fijar destino a Administración si aplica: OSEDE2=1, ODIVISION2=9, OAREA2=17
					// Si los selects existen en esta vista, deshabilitarlos e inyectar inputs ocultos
					if (document.form1.OSEDE2) {
						$('#OSEDE2').val('1');
						$('#ODIVISION2').html('<option value="9">Administración</option>');
						$('#ODIVISION2').val('9');
						$('#OAREA2').html('<option value="17">Administración - Área 17</option>');
						$('#OAREA2').val('17');
						$('#OSEDE2,#ODIVISION2,#OAREA2').prop('disabled', true);
						$('input[name="OSEDE2"].injected').remove();
						$('input[name="ODIVISION2"].injected').remove();
						$('input[name="OAREA2"].injected').remove();
						$('<input>').attr({type:'hidden',name:'OSEDE2',value:'1'}).addClass('injected').appendTo('form[name="form1"]');
						$('<input>').attr({type:'hidden',name:'ODIVISION2',value:'9'}).addClass('injected').appendTo('form[name="form1"]');
						$('<input>').attr({type:'hidden',name:'OAREA2',value:'17'}).addClass('injected').appendTo('form[name="form1"]');
					}
			window.reasignInit = false;
		});
	});
	</script>
JS;
		echo $js;
	}
	?>
</body>

</html>