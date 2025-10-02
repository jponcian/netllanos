<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: ../index.php?errorusuario=val");
	exit();
}

$acceso = 74;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<?php include "../funciones/headNew.php"; ?>
	<title>Registro de Bien Nacional</title>
	<style>
		/* Feedback visual para select2 (categoría) */
		#OCATEGORIA.is-valid+.select2-container .select2-selection {
			border-color: #198754 !important;
			box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.1) !important;
		}

		#OCATEGORIA.is-invalid+.select2-container .select2-selection {
			border-color: #dc3545 !important;
			box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.1) !important;
		}

		/* Estilos del modal y validación (copiados de 15articulos.php) */
		.modal {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, .5);
			display: none;
			align-items: center;
			justify-content: center;
			z-index: 1050;
			/* Bootstrap default, menor que SweetAlert (1060) */
		}

		.modal.show {
			display: flex !important;
		}

		.modal-backdrop {
			z-index: 1040 !important;
		}

		/* Asegurar que SweetAlert siempre esté por encima del modal */
		.swal2-container {
			z-index: 1060 !important;
		}

		.modal-dialog {
			width: 90%;
			max-width: 600px;
		}

		.modal-content {
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 10px 25px rgba(0, 0, 0, .2);
			overflow: hidden;
		}

		.modal-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 12px 16px;
			border-bottom: 1px solid #eee;
			background: #007bff;
			color: #fff;
		}

		.modal-title {
			margin: 0;
			font-size: 18px;
			font-weight: 600;
		}

		.modal-body {
			padding: 16px;
			max-height: 70vh;
			overflow-y: auto;
		}

		.modal-footer {
			padding: 12px 16px;
			border-top: 1px solid #eee;
			display: flex;
			gap: 8px;
			justify-content: flex-end;
			background: #f8f9fa;
		}

		.form-control.is-invalid {
			border-color: #ced4da !important;
			box-shadow: none !important;
		}

		.invalid-feedback {
			display: none;
			color: #dc3545;
			font-size: 12px;
			margin-top: 4px;
		}

		.invalid-feedback.show {
			display: block;
		}

		.alert {
			padding: 8px 12px;
			border-radius: 4px;
			margin-bottom: 10px;
			display: none;
		}

		.alert.show {
			display: block;
		}

		.alert-warning {
			background: #fff3cd;
			color: #856404;
			border: 1px solid #ffeeba;
		}

		.form-control {
			width: 100%;
			padding: 8px 10px;
			border: 1px solid #ced4da;
			border-radius: 4px;
			min-height: 38px;
			height: 38px;
		}

		/* Select2 para categoría igual altura que input */
		#OCATEGORIA.select2-hidden-accessible+.select2-container .select2-selection {
			min-height: 38px !important;
			height: 38px !important;
			padding-top: 4px;
			padding-bottom: 4px;
			font-size: 1rem;
		}

		.btn {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 6px 12px;
			border-radius: 4px;
			border: 1px solid transparent;
			cursor: pointer;
		}

		.btn-primary {
			background: #007bff;
			color: #fff;
			border-color: #007bff;
		}

		.btn-success {
			background: #198754;
			color: #fff;
			border-color: #198754;
		}

		.btn-secondary {
			background: #6c757d;
			color: #fff;
			border-color: #6c757d;
		}

		.form-row {
			display: flex;
			flex-wrap: wrap;
			gap: 15px;
			margin-bottom: 15px;
		}

		.form-group {
			flex: 1;
			min-width: 150px;
		}
	</style>
</head>

<body style="background: transparent !important;">


	<!-- Modal para Registrar/Editar Bien -->
	<div id="modalBien" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalTitulo" aria-hidden="true"
		style="display:none;">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content shadow-lg border-0">
				<div class="modal-header" style="background:#d32f2f;color:#fff;align-items:center;gap:10px;">
					<h5 class="modal-title fw-bold" id="modalTitulo" style="font-size:1.25rem;letter-spacing:0.5px;">
						<i id="modalIcon" class="fa fa-plus" aria-hidden="true"></i>
						<span id="modalTituloText">Registrar Bien Nacional</span>
					</h5>
					<button type="button" class="btn-close btn-close-white ms-auto" aria-label="Cerrar"
						onclick="cerrarModalBien()"></button>
				</div>
				<div class="modal-body py-3 px-4">
					<div id="formAlert" class="alert alert-warning d-none" role="alert"></div>
					<form id="formBien" autocomplete="off">
						<input type="hidden" id="ID_BIEN" name="ID_BIEN" value="">
						<div class="row g-3">
							<div class="col-md-6">
								<select id="OSEDE" name="OSEDE" class="form-control" required
									title="Seleccione el Sector">
									<option value="0" disabled selected>Seleccione el Sector</option>
								</select>
								<div class="invalid-feedback" id="err_OSEDE"></div>
							</div>
							<div class="col-md-6">
								<select id="ODIVISION" name="ODIVISION" class="form-control" required
									title="Seleccione la División">
									<option value="0" disabled selected>Seleccione la División</option>
								</select>
								<div class="invalid-feedback" id="err_ODIVISION"></div>
							</div>
							<div class="col-md-12">
								<select id="OAREA" name="OAREA" class="form-control" required
									title="Seleccione el Área">
									<option value="0" disabled selected>Seleccione el Área</option>
								</select>
								<div class="invalid-feedback" id="err_OAREA"></div>
							</div>
							<div class="col-md-12">
								<select id="OCATEGORIA" name="OCATEGORIA" class="form-control select2" required
									style="width: 100%;" title="Seleccione la Categoría">
									<option value="0" disabled selected>Seleccione la Categoría</option>
								</select>
								<div class="invalid-feedback" id="err_OCATEGORIA"></div>
							</div>
							<div class="col-md-4">
								<input type="text" class="form-control text-center" id="OBIEN" name="OBIEN" required
									maxlength="6" placeholder="N° Bien">
								<div class="invalid-feedback" id="err_OBIEN"></div>
							</div>
							<div class="col-md-4">
								<select id="OCONSERVACION" name="OCONSERVACION" class="form-control" required
									title="Estado Conservación">
									<option value="">Estado Conservación</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
								</select>
								<div class="invalid-feedback" id="err_OCONSERVACION"></div>
							</div>
							<div class="col-md-4">
								<input type="number" class="form-control text-center" id="OVALOR" name="OVALOR" min="0"
									step="0.01" required placeholder="Valor (Bs.)">
								<div class="invalid-feedback" id="err_OVALOR"></div>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" id="ODESCRIPCION" name="ODESCRIPCION" required rows="3"
									placeholder="Descripción"></textarea>
								<div class="invalid-feedback" id="err_ODESCRIPCION"></div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer bg-light border-0 d-flex justify-content-end gap-2">
					<button type="button" class="btn btn-danger px-4" style="background:#d32f2f;border-color:#d32f2f;"
						onclick="guardarBien()">
						<i class="fa-solid fa-check me-1"></i> Guardar
					</button>
					<button type="button" class="btn btn-secondary px-4" onclick="cerrarModalBien()">
						<i class="fa fa-times" aria-hidden="true"></i> Cancelar
					</button>
				</div>
			</div>
		</div>
	</div>


	<!-- Controles de Filtro -->

	<div class="row mb-3" style="max-width: 1100px; margin: 15px auto; align-items: flex-end;">
		<div class="col-md-3" style="min-width:180px; max-width:220px;">
			<div class="form-group mb-0">
				<label for="filtroOSEDE">Filtrar por Sede:</label>
				<select id="filtroOSEDE" class="form-control" style="width: 200px; display: inline-block;"></select>
			</div>
		</div>
		<div class="col-md-9" style="display: flex; align-items: flex-end; gap: 10px;">
			<div class="form-group mb-0" style="flex:1;">
				<input type="text" id="obuscar_bienes" class="form-control" placeholder="Escriba para buscar...">
			</div>
			<button type="button" class="btn btn-danger btn-sm" style="height:38px; margin-bottom:2px;"
				onclick="abrirModalBien()">
				<i class="fa fa-plus" aria-hidden="true"></i> Nuevo
			</button>
		</div>
	</div>

	<?php include "../funciones/footNew.php"; ?>

	<!-- Div para la tabla de bienes -->
	<div id="divBienes">
		<?php include "3tabla.php"; ?>
	</div>

	<!-- <script src="js/app-alerts.js"></script> -->
	<script>
		// Lógica JS para manejar el modal, carga de datos y validación

		function limpiarValidacion() {
			$('#formAlert').removeClass('show alert-danger alert-warning').text('');
			$('#formBien .form-control').removeClass('is-invalid is-valid');
			$('.invalid-feedback').removeClass('show').text('');
		}

		function setError(inputId, message) {
			const input = $('#' + inputId);
			const fb = $('#err_' + inputId);
			if (message) {
				input.removeClass('is-valid').addClass('is-invalid');
				fb.text(message).addClass('show');
			} else {
				input.removeClass('is-invalid').addClass('is-valid');
				fb.text('').removeClass('show');
			}
		}

		function validarFormulario() {
			let ok = true;
			limpiarValidacion();
			// Validar y marcar cada campo
			if (!$('#OSEDE').val() || $('#OSEDE').val() == '0') { setError('OSEDE', 'Seleccione una dependencia.'); ok = false; } else { setError('OSEDE', ''); }
			if (!$('#ODIVISION').val() || $('#ODIVISION').val() == '0') { setError('ODIVISION', 'Seleccione una división.'); ok = false; } else { setError('ODIVISION', ''); }
			if (!$('#OAREA').val() || $('#OAREA').val() == '0') { setError('OAREA', 'Seleccione un área.'); ok = false; } else { setError('OAREA', ''); }
			if (!$('#OCATEGORIA').val() || $('#OCATEGORIA').val() == '0') { setError('OCATEGORIA', 'Seleccione una categoría.'); ok = false; } else { setError('OCATEGORIA', ''); }
			if (!$('#OBIEN').val().trim()) { setError('OBIEN', 'Ingrese el número del bien.'); ok = false; } else { setError('OBIEN', ''); }
			if (!$('#ODESCRIPCION').val().trim()) { setError('ODESCRIPCION', 'Ingrese la descripción.'); ok = false; } else { setError('ODESCRIPCION', ''); }
			if (!$('#OCONSERVACION').val() || $('#OCONSERVACION').val() == '0') { setError('OCONSERVACION', 'Seleccione el estado.'); ok = false; } else { setError('OCONSERVACION', ''); }
			const valor = parseFloat($('#OVALOR').val());
			if (isNaN(valor) || valor < 0) { setError('OVALOR', 'Ingrese un valor válido.'); ok = false; } else { setError('OVALOR', ''); }

			if (!ok) {
				$('#formAlert').addClass('alert-warning show').text('Por favor corrija los campos marcados.');
			}
			return ok;
		}

		function abrirModalBien(id = null) {
			$("#formBien")[0].reset();
			$("#ID_BIEN").val("");
			$("#modalTituloText").text(id ? 'Editar Bien Nacional' : 'Registrar Bien Nacional');
			$("#modalIcon").removeClass('fa-plus fa-edit').addClass(id ? 'fa-edit' : 'fa-plus');
			limpiarValidacion();

			// Cargar combos iniciales
			cargarSedes();
			cargarCategorias();

			$('#ODIVISION').html('<option value="0" disabled selected>Seleccione la División</option>');
			$('#OAREA').html('<option value="0" disabled selected>Seleccione el Área</option>');

			// Mostrar el modal usando Bootstrap 5 o fallback
			var modal = document.getElementById('modalBien');
			if (window.bootstrap && bootstrap.Modal) {
				var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
				modalInstance.show();
				setTimeout(function () { $('#OBIEN').trigger('focus'); }, 80);
			} else {
				// Fallback: quitar cualquier backdrop anterior y mostrar modal correctamente
				$('.modal-backdrop').remove();
				$('body').removeClass('modal-open');
				$("#modalBien").addClass('show').css('display', 'flex');
				setTimeout(function () { $('#OBIEN').trigger('focus'); }, 80);
			}
		}

		function cerrarModalBien() {
			var modal = document.getElementById('modalBien');
			if (window.bootstrap && bootstrap.Modal) {
				var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
				modalInstance.hide();
			} else {
				$("#modalBien").hide();
			}
		}

		// Carga de combos con callbacks para edición confiable
		function cargarSedes(callback, selected) {
			$.getJSON('3combo.php?combo=sede', function (data) {
				var select = $('#OSEDE');
				select.empty().append('<option value="0" disabled selected>Seleccione el Sector</option>');
				$.each(data, function (i, item) {
					select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
				});
				if (selected) select.val(selected);
				if (typeof callback === 'function') callback();
			});
		}

		function cargarDivisiones(id_sede, callback, selected) {
			if (!id_sede) return;
			$.getJSON('3combo.php?combo=division&id_sede=' + id_sede, function (data) {
				var select = $('#ODIVISION');
				select.empty().append('<option value="0" disabled selected>Seleccione la División</option>');
				$.each(data, function (i, item) {
					select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
				});
				if (selected) select.val(selected);
				if (typeof callback === 'function') callback();
			});
		}

		function cargarAreas(id_division, callback, selected) {
			if (!id_division) return;
			$.getJSON('3combo.php?combo=area&id_division=' + id_division, function (data) {
				var select = $('#OAREA');
				select.empty().append('<option value="0" disabled selected>Seleccione el Área</option>');
				$.each(data, function (i, item) {
					select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
				});
				if (selected) select.val(selected);
				if (typeof callback === 'function') callback();
			});
		}

		function cargarCategorias(callback, selected) {
			$.getJSON('3combo.php?combo=categoria', function (data) {
				var select = $('#OCATEGORIA');
				select.empty().append('<option value="0" disabled selected>Seleccione la Categoría</option>');
				$.each(data, function (i, item) {
					select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
				});
				if (selected) select.val(selected);
				if (typeof callback === 'function') callback();
			});
		}

		$('#OSEDE').change(function () {
			var sede_id = $(this).val();
			if (sede_id != '0') {
				cargarDivisiones(sede_id, function () {
					$('#OAREA').html('<option value="0" disabled selected>Seleccione el Área</option>');
				});
			} else {
				$('#ODIVISION').html('<option value="0" disabled selected>Seleccione la División</option>');
				$('#OAREA').html('<option value="0" disabled selected>Seleccione el Área</option>');
			}
		});

		$('#ODIVISION').change(function () {
			var division_id = $(this).val();
			if (division_id != '0') {
				cargarAreas(division_id);
			} else {
				$('#OAREA').html('<option value="0" disabled selected>Seleccione el Área</option>');
			}
		});

		// Validación visual en tiempo real para OCATEGORIA con select2
		function setCategoriaValidation() {
			var categoria_id = $('#OCATEGORIA').val();
			var $select2 = $('#OCATEGORIA').next('.select2-container');
			if (categoria_id && categoria_id !== '0') {
				$('#OCATEGORIA').removeClass('is-invalid').addClass('is-valid');
				$select2.removeClass('is-invalid').addClass('is-valid');
				$.getJSON('3combo.php?combo=formato_categoria&id_categoria=' + categoria_id, function (data) {
					if (data && data.formato) {
						$('#ODESCRIPCION').val(data.formato);
					}
				});
			} else {
				$('#OCATEGORIA').removeClass('is-valid').addClass('is-invalid');
				$select2.removeClass('is-valid').addClass('is-invalid');
			}
		}
		$('#OCATEGORIA').on('change blur', setCategoriaValidation);
		// Forzar validación visual al abrir modal o cargar datos
		$(document).on('select2:open select2:close', '#OCATEGORIA', setCategoriaValidation);

		function cargarFiltroSedes() {
			$.getJSON('3combo.php?combo=sede', function (data) {
				var select = $('#filtroOSEDE');
				var valorActual = select.val(); // Guardar valor actual
				select.empty().append('<option value="0">Seleccione la Sede</option>');
				$.each(data, function (i, item) {
					select.append($('<option>', {
						value: item.id,
						text: item.nombre
					}));
				});
				if (valorActual) {
					select.val(valorActual); // Restaurar valor
				}
			});
		}

		function recargarTabla() {
			var sede_id = $('#filtroOSEDE').val();
			$('#divBienes').css('opacity', '0.5');
			$('#divBienes').load('3tabla.php', { filtro_sede: sede_id }, function () {
				$('#divBienes').css('opacity', '1');
				// Aplicar SIEMPRE el valor del buscador tras recargar (aunque sea vacío)
				var busqueda = $('#obuscar_bienes').val() || '';
				aplicarBusquedaDataTable(busqueda);
			});
		}

		// Aplica búsqueda a DataTable con reintentos hasta que esté listo
		function aplicarBusquedaDataTable(valor, intento) {
			intento = intento || 0;
			var maxIntentos = 20; // ~2s si usamos 100ms
			// Si el plugin está cargado y la tabla ya es DataTable
			if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablaBienes')) {
				var api = $('#tablaBienes').DataTable();
				api.search(valor).draw();
				return true;
			}
			// Si existe la tabla y el plugin está cargado, esperar a que inicialice
			if ($('#tablaBienes').length && $.fn.DataTable && intento < maxIntentos) {
				return setTimeout(function () { aplicarBusquedaDataTable(valor, intento + 1); }, 100);
			}
			// Si aún no, seguir intentando un poco más (por si scripts están ejecutándose)
			if (intento < maxIntentos) {
				return setTimeout(function () { aplicarBusquedaDataTable(valor, intento + 1); }, 100);
			}
			return false;
		}

		$(document).ready(function () {
			cargarFiltroSedes();

			// Feedback visual en tiempo real para selects y inputs (excepto OCATEGORIA)
			$('#formBien select:not(#OCATEGORIA), #formBien input, #formBien textarea').on('change keyup blur', function () {
				const id = $(this).attr('id');
				if (!id) return;
				// Validación individual
				if ($(this).is('select')) {
					if ($(this).val() && $(this).val() !== '0') {
						$(this).removeClass('is-invalid').addClass('is-valid');
					} else {
						$(this).removeClass('is-valid').addClass('is-invalid');
					}
				} else if ($(this).is('[type="number"]')) {
					const val = parseFloat($(this).val());
					if (!isNaN(val) && val >= 0) {
						$(this).removeClass('is-invalid').addClass('is-valid');
					} else {
						$(this).removeClass('is-valid').addClass('is-invalid');
					}
				} else {
					if ($(this).val().trim() !== '') {
						$(this).removeClass('is-invalid').addClass('is-valid');
					} else {
						$(this).removeClass('is-valid').addClass('is-invalid');
					}
				}
			});

			// Forzar validación visual de OCATEGORIA al cargar datos o abrir modal
			setTimeout(setCategoriaValidation, 200);

			// Listener para el filtro de sede
			$('#filtroOSEDE').change(function () {
				recargarTabla();
			});

			// Listener para el campo de búsqueda.
			// Usamos 'keyup' para que reaccione mientras el usuario escribe.
			// Se delega el evento por si la tabla se redibuja.
			$(document).on('keyup', '#obuscar_bienes', function () {
				aplicarBusquedaDataTable(this.value || '');
			});

			// Carga inicial de la tabla
			recargarTabla();
		});

		function guardarBien() {
			if (!validarFormulario()) return;

			var datos = $('#formBien').serialize();

			// Mostrar loader en el botón Guardar
			var $btnGuardar = $(".modal-footer .btn-danger");
			var btnHtml = $btnGuardar.html();
			$btnGuardar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

			$.ajax({
				url: '3guardar.php',
				type: 'POST',
				data: datos,
				dataType: 'json',
				success: function (response) {
					if (response.tipo === 'exito') {
						Swal.fire({
							title: '¡Guardado!',
							text: response.msj,
							icon: 'success',
							timer: 1500,
							showConfirmButton: false
						}).then(() => {
							cerrarModalBien();
							recargarTabla();
						});
					} else {
						Swal.fire({
							title: 'Error',
							text: response.msj,
							icon: 'error',
							confirmButtonText: 'Aceptar'
						});
					}
				},
				error: function () {
					Swal.fire({
						title: 'Error',
						text: 'Error al conectar con el servidor.',
						icon: 'error',
						confirmButtonText: 'Aceptar'
					});
				},
				complete: function () {
					// Restaurar botón y asegurar cierre de modal/backdrop
					$btnGuardar.prop('disabled', false).html(btnHtml);
					// Si el modal sigue abierto y hay backdrop, forzar cierre
					var modal = document.getElementById('modalBien');
					if (modal && $(modal).is(':visible')) {
						cerrarModalBien();
					}
					// Eliminar backdrop manualmente si quedara pegado
					$('.modal-backdrop').remove();
					$('body').removeClass('modal-open');
				}
			});
		}

		function eliminarBien(id_bien) {
			Swal.fire({
				title: '¿Está seguro?',
				text: "¡No podrá revertir esta acción!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Sí, ¡eliminar!',
				cancelButtonText: 'Cancelar'
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: '3eliminar.php',
						type: 'POST',
						data: { id: id_bien },
						dataType: 'json',
						success: function (response) {
							if (response.tipo === 'exito') {
								Swal.fire({
									title: '¡Eliminado!',
									text: response.msj,
									icon: 'success',
									timer: 1500,
									showConfirmButton: false
								});
								recargarTabla(); // Recargar la tabla
							} else {
								Swal.fire('Error', response.msj, 'error');
							}
						},
						error: function () {
							Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
						}
					});
				}
			});
		}

	</script>
</body>

</html>