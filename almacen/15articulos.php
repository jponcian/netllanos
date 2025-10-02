<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: ../index.php?errorusuario=val");
	exit();
}

//$acceso=999;
//------- VALIDACION ACCESO USUARIO
//include "../validacion/validacion_usuario.php";
//-----------------------------------

?>
<html>

<head>
	<?php include "../funciones/headNew.php";
	?>
	<title>Registro de Articulos</title>
</head>

<body style="background: transparent !important;">


	<style>
		/* Estilos del modal (sin depender de frameworks) */
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
			z-index: 9999;
		}

		.modal-dialog {
			width: 90%;
			max-width: 480px;
		}

		.modal-content {
			background: #fff;
			border-radius: 12px;
			box-shadow: 0 14px 30px rgba(0, 0, 0, .18);
			border: 1px solid rgba(0, 0, 0, .06);
			overflow: hidden;
			transform: translateY(6px);
			animation: modalIn .22s ease-out both;
		}

		@keyframes modalIn {
			from {
				opacity: 0;
				transform: translateY(14px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.modal-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 12px 16px;
			border-bottom: 1px solid rgba(255, 255, 255, .2);
			background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
			color: #fff;
			box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.05);
		}

		.modal-title {
			margin: 0;
			font-size: 1.15rem;
			font-weight: 700;
			letter-spacing: .2px;
			color: #fff;
			display: inline-flex;
			align-items: center;
			gap: 8px;
		}

		.modal-body {
			padding: 16px;
			background: #fff;
		}

		.modal-footer {
			padding: 12px 16px;
			border-top: 1px solid #eee;
			display: flex;
			gap: 8px;
			justify-content: flex-end;
			background: #f8f9fa;
		}

		/* Validación moderna inline */
		.form-control.is-invalid {
			border-color: #dc3545;
			box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.1);
		}

		.form-control.is-valid {
			border-color: #198754;
			box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.1);
		}

		/* Estados de foco y hover más sutiles */
		.form-control {
			width: 100%;
			padding: 8px 10px;
			border: 1px solid #ced4da;
			border-radius: 6px;
			transition: border-color .15s ease, box-shadow .15s ease;
		}

		.form-control:focus {
			outline: none;
			border-color: #86b7fe;
			box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .1);
		}

		/* Quitar iconos de fondo de Bootstrap para evitar duplicados dentro del input */
		#modalArticulo .form-control.is-valid,
		#modalArticulo .form-control.is-invalid,
		#modalArticulo select.form-control.is-valid,
		#modalArticulo select.form-control.is-invalid {
			background-image: none !important;
			padding-right: .75rem;
			/* ajustar padding si Bootstrap lo incrementó */
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
			display: block;
		}

		/* Espaciado y labels más legibles */
		.form-group {
			margin-bottom: 12px;
		}

		.form-group>label {
			display: block;
			font-weight: 600;
			margin-bottom: 4px;
			color: #333;
		}

		.btn {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 6px 12px;
			border-radius: 6px;
			border: 1px solid transparent;
			cursor: pointer;
			transition: transform .06s ease, box-shadow .15s ease, background-color .15s ease, opacity .15s ease;
		}

		.btn-success {
			background: #198754;
			color: #fff;
			border-color: #198754;
		}

		.btn-success:hover {
			filter: brightness(1.05);
			box-shadow: 0 6px 14px rgba(25, 135, 84, .25);
			transform: translateY(-1px);
		}

		.btn-success:active {
			transform: translateY(0);
			box-shadow: 0 2px 6px rgba(25, 135, 84, .25);
		}

		.btn-secondary {
			background: #6c757d;
			color: #fff;
			border-color: #6c757d;
		}

		.btn-secondary:hover {
			filter: brightness(1.05);
		}

		/* Botón cerrar minimalista */
		#modalArticulo .close {
			background: transparent;
			color: #fff;
			font-size: 24px;
			line-height: 1;
			opacity: .9;
			border: none;
		}

		#modalArticulo .close:hover {
			opacity: 1;
		}

		/* Icono de validación */
		/* Icono de validación (alineado a la derecha del input) */
		#modalArticulo .valid-icon {
			display: none;
			color: #198754;
			margin-left: 6px;
			vertical-align: middle;
		}

		/* Poner el input y el icono en la misma línea */
		#modalArticulo .form-group>.form-control {
			display: inline-block;
			width: calc(100% - 26px);
			vertical-align: middle;
		}

		#modalArticulo .form-group>.valid-icon {
			display: inline-block;
		}

		/* Dos columnas (Precio / Cantidad) */
		#modalArticulo .form-row.two-cols {
			display: flex;
			gap: 12px;
		}

		#modalArticulo .form-row.two-cols .form-group {
			flex: 1 1 0;
			margin-bottom: 12px;
		}

		@media (max-width: 576px) {
			#modalArticulo .form-row.two-cols {
				flex-direction: column;
				gap: 8px;
			}
		}
	</style>
	<div id="modalArticulo" class="modal" tabindex="-1" role="dialog" style="display:none;">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalTitulo"><i id="modalTituloIcon" class="fa fa-plus-circle"
							aria-hidden="true"></i> <span id="modalTituloText">Registrar Artículo</span></h5>
					<button type="button" class="close" onclick="cerrarModalArticulo()"
						aria-label="Cerrar">&times;</button>
				</div>
				<div class="modal-body">
					<div id="formAlert" class="alert alert-warning" role="alert"></div>
					<form id="formArticulo">
						<input type="hidden" id="ID_ARTICULO" name="ID_ARTICULO" value="">
						<!-- 1) Descripción -->
						<div class="form-group">
							<label for="ODESCRIPCION">Descripción:</label>
							<input type="text" class="form-control" id="ODESCRIPCION" name="ODESCRIPCION" required
								maxlength="100" placeholder="Ej: Guantes de nitrilo talla M">
							<i id="ok_ODESCRIPCION" class="fa fa-check valid-icon" aria-hidden="true"></i>
							<div class="invalid-feedback" id="err_ODESCRIPCION"></div>
						</div>

						<!-- 2) Categoría -->
						<div class="form-group">
							<label for="OCAT">Categoría:</label>
							<select id="OCAT" name="OCAT" class="form-control" required></select>
							<i id="ok_OCAT" class="fa fa-check valid-icon" aria-hidden="true"></i>
							<div class="invalid-feedback" id="err_OCAT"></div>
						</div>

						<!-- 3) Unidad -->
						<div class="form-group">
							<label for="OTIPO">Unidad:</label>
							<select id="OTIPO" name="OTIPO" class="form-control" required>
								<option value="UN">Unidad</option>
								<option value="CAJA">Caja</option>
							</select>
							<i id="ok_OTIPO" class="fa fa-check valid-icon" aria-hidden="true"></i>
							<div class="invalid-feedback" id="err_OTIPO"></div>
						</div>

						<!-- 4) Precio y Cantidad en dos columnas -->
						<div class="form-row two-cols">
							<div class="form-group">
								<label for="OPRECIO">Precio:</label>
								<input type="number" class="form-control" id="OPRECIO" name="OPRECIO" min="0"
									step="0.01" required placeholder="0.00">
								<i id="ok_OPRECIO" class="fa fa-check valid-icon" aria-hidden="true"></i>
								<div class="invalid-feedback" id="err_OPRECIO"></div>
							</div>
							<div class="form-group">
								<label for="OCANTIDAD">Cantidad:</label>
								<input type="number" class="form-control" id="OCANTIDAD" name="OCANTIDAD" min="0"
									step="1" required placeholder="0">
								<i id="ok_OCANTIDAD" class="fa fa-check valid-icon" aria-hidden="true"></i>
								<div class="invalid-feedback" id="err_OCANTIDAD"></div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="guardarArticulo()" id="btnGuardarArticulo">
						<i class="fa fa-save" aria-hidden="true"></i>
						Guardar
					</button>
					<button type="button" class="btn btn-secondary" onclick="cerrarModalArticulo()">
						<i class="fa fa-times" aria-hidden="true"></i>
						Cancelar
					</button>
				</div>
			</div>
		</div>
	</div>
	<?php include "../funciones/footNew.php"; ?>
	<div id="div1"><?php include "15tabla.php"; ?></div>
	<script>
		// ---- Validación consistente (clases is-valid/is-invalid + iconos + alerta global) ----
		function limpiarValidacion() {
			$('#formAlert').removeClass('show alert-danger alert-warning').text('');
			$('#formArticulo .form-control').removeClass('is-invalid is-valid');
			$('.invalid-feedback').removeClass('show').text('');
			$('.valid-icon').hide();
		}

		function setError(inputId, message) {
			const $input = $('#' + inputId);
			const $fb = $('#err_' + inputId);
			const $ok = $('#ok_' + inputId);
			if (message) {
				$input.addClass('is-invalid').removeClass('is-valid');
				$fb.text(message).addClass('show');
				$ok.hide();
			} else {
				$input.removeClass('is-invalid').addClass('is-valid');
				$fb.text('').removeClass('show');
				$ok.show();
			}
		}

		function validarFormulario() {
			let ok = true;
			limpiarValidacion();
			if (!$('#OCAT').val()) {
				setError('OCAT', 'Seleccione una categoría.');
				ok = false;
			}
			if (!$('#ODESCRIPCION').val().trim()) {
				setError('ODESCRIPCION', 'Ingrese la descripción.');
				ok = false;
			}
			if (!$('#OTIPO').val()) {
				setError('OTIPO', 'Seleccione la unidad.');
				ok = false;
			}
			const precio = parseFloat($('#OPRECIO').val());
			if (isNaN(precio) || precio < 0) {
				setError('OPRECIO', 'Ingrese un precio válido.');
				ok = false;
			}
			const cantidad = parseInt($('#OCANTIDAD').val(), 10);
			if (isNaN(cantidad) || cantidad < 0) {
				setError('OCANTIDAD', 'Ingrese una cantidad válida.');
				ok = false;
			}
			if (!ok) {
				$('#formAlert').addClass('alert-warning show').text('Por favor corrija los campos marcados.');
			}
			return ok;
		}

		// Validación en tiempo real + éxito con icono
		$(document).on('input change', '#OCAT, #ODESCRIPCION, #OTIPO, #OPRECIO, #OCANTIDAD', function () {
			const id = this.id;
			if (id === 'OCAT') {
				if ($(this).val()) setError(id); else setError(id, 'Seleccione una categoría.');
			}
			if (id === 'ODESCRIPCION') {
				if ($(this).val().trim()) setError(id); else setError(id, 'Ingrese la descripción.');
			}
			if (id === 'OTIPO') {
				if ($(this).val()) setError(id); else setError(id, 'Seleccione la unidad.');
			}
			if (id === 'OPRECIO') {
				const v = parseFloat($(this).val());
				if (!isNaN(v) && v >= 0) setError(id); else setError(id, 'Ingrese un precio válido.');
			}
			if (id === 'OCANTIDAD') {
				const v = parseInt($(this).val(), 10);
				if (!isNaN(v) && v >= 0) setError(id); else setError(id, 'Ingrese una cantidad válida.');
			}
		});

		function abrirModalArticulo(id = null) {
			// Limpiar formulario
			$("#formArticulo")[0].reset();
			$("#ID_ARTICULO").val("");
			$("#modalTituloText").text(id ? "Editar Artículo" : "Registrar Artículo");
			$("#modalTituloIcon")
				.removeClass('fa-plus-circle fa-edit')
				.addClass(id ? 'fa-edit' : 'fa-plus-circle');
			limpiarValidacion();
			// Cargar categorías por AJAX
			$.getJSON('15categorias.php', function (data) {
				var select = $('#OCAT');
				select.empty();
				select.append('<option value="">Seleccione</option>');
				$.each(data, function (i, item) {
					select.append('<option value="' + item.id_categoria + '">' + item.codigo + ' ' + item.descripcion + '</option>');
				});
				if (id) {
					cargarDatosArticulo(id);
				}
				// Siempre mostrar el modal después de cargar las categorías
				$("#modalArticulo").css('display', 'flex');
				setTimeout(function () { $('#ODESCRIPCION').trigger('focus'); }, 60);
			});
		}

		function cerrarModalArticulo() {
			$("#modalArticulo").hide();
		}

		function cargarDatosArticulo(id) {
			$.getJSON('15obtener.php?id=' + id, function (data) {
				if (data) {
					$('#ID_ARTICULO').val(data.id_articulo);
					$('#OCAT').val(data.id_categoria);
					$('#ODESCRIPCION').val(data.descripcion);
					$('#OTIPO').val(data.unidad);
					$('#OPRECIO').val(data.precio);
					$('#OCANTIDAD').val(data.cantidad);
				}
			});
		}

		function guardarArticulo() {
			if (!validarFormulario()) {
				return;
			}
			var datos = $("#formArticulo").serialize();
			var id = $('#ID_ARTICULO').val();
			var url = id ? '15modificar.php' : '15guardado.php';
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: datos,
				success: function (data) {
					if (data.tipo == "alerta") {
						// Mostrar mensaje general de validación del servidor
						$('#formAlert').removeClass('alert-danger').addClass('alert-warning show').text(data.msj);
					} else {
						cerrarModalArticulo();
						$('#div1').load('15tabla.php');
						Swal.fire({
							icon: 'success',
							title: 'Éxito',
							text: data.msj
						});
					}
				}
			});
		}

		function eliminar(id) {
			Swal.fire({
				title: '¿Está seguro de eliminar el registro?',
				text: 'Esta acción no se puede deshacer.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, eliminar',
				cancelButtonText: 'Cancelar'
			}).then((result) => {
				if (result.isConfirmed) {
					var parametros = 'id=' + id;
					$.ajax({
						url: '15eliminar.php',
						type: 'POST',
						dataType: 'json',
						data: parametros,
						success: function (data) {
							$('#div1').load('15tabla.php');
							if (data.tipo == 'alerta') {
								Swal.fire({
									icon: 'warning',
									title: 'Aviso',
									text: data.msj
								});
							} else {
								Swal.fire({
									icon: 'success',
									title: 'Eliminado',
									text: data.msj
								});
							}
						}
					});
				}
			});
		}
	</script>
	<?php //include "../funciones/pie_fecha.php"; ?>
</body>

</html>

<script src="js/app-alerts.js"></script>