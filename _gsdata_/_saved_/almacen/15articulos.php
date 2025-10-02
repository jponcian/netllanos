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

	// include "../funciones/head_fecha.php";
	?>
	<?php include "../funciones/mensajes.php"; ?>
	<title>Registro de Articulos</title>
</head>

<body style="background: transparent !important;">
	<p>
		<?php //include "../titulo.php";
		?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<div id="div1"><?php include "15tabla.php"; ?></div>
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
			background: #dc3545;
			color: #fff;
		}

		.modal-title {
			margin: 0;
			font-size: 18px;
			font-weight: 600;
			color: #fff;
		}

		.modal-body {
			padding: 16px;
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

		.form-control {
			width: 100%;
			padding: 8px 10px;
			border: 1px solid #ced4da;
			border-radius: 4px;
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
	</style>
	<div id="modalArticulo" class="modal" tabindex="-1" role="dialog" style="display:none;">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalTitulo">Registrar Artículo</h5>
					<button type="button" class="close" onclick="cerrarModalArticulo()"
						aria-label="Cerrar">&times;</button>
				</div>
				<div class="modal-body">
					<div id="formAlert" class="alert alert-warning" role="alert"></div>
					<form id="formArticulo">
						<input type="hidden" id="ID_ARTICULO" name="ID_ARTICULO" value="">
						<div class="form-group">
							<label for="OCAT">Categoría:</label>
							<select id="OCAT" name="OCAT" class="form-control" required></select>
							<div class="invalid-feedback" id="err_OCAT"></div>
						</div>
						<div class="form-group">
							<label for="ODESCRIPCION">Descripción:</label>
							<input type="text" class="form-control" id="ODESCRIPCION" name="ODESCRIPCION" required
								maxlength="100">
							<div class="invalid-feedback" id="err_ODESCRIPCION"></div>
						</div>
						<div class="form-group">
							<label for="OTIPO">Unidad:</label>
							<select id="OTIPO" name="OTIPO" class="form-control" required>
								<option value="UN">Unidad</option>
								<option value="CAJA">Caja</option>
							</select>
							<div class="invalid-feedback" id="err_OTIPO"></div>
						</div>
						<div class="form-group">
							<label for="OPRECIO">Precio:</label>
							<input type="number" class="form-control" id="OPRECIO" name="OPRECIO" min="0" step="0.01"
								required>
							<div class="invalid-feedback" id="err_OPRECIO"></div>
						</div>
						<div class="form-group">
							<label for="OCANTIDAD">Cantidad:</label>
							<input type="number" class="form-control" id="OCANTIDAD" name="OCANTIDAD" min="0" step="1"
								required>
							<div class="invalid-feedback" id="err_OCANTIDAD"></div>
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
	<script>
		// ---- Validación inline moderna (sin SweetAlert para validación) ----
		function limpiarValidacion() {
			$('#formAlert').removeClass('show alert-danger alert-warning').text('');
			$('#formArticulo .form-control').removeClass('is-invalid');
			$('.invalid-feedback').removeClass('show').text('');
		}

		function setError(inputId, message) {
			const input = $('#' + inputId);
			const fb = $('#err_' + inputId);
			if (message) {
				input.addClass('is-invalid');
				fb.text(message).addClass('show');
			} else {
				input.removeClass('is-invalid');
				fb.text('').removeClass('show');
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

		// Real-time validation
		$(document).on('input change', '#OCAT, #ODESCRIPCION, #OTIPO, #OPRECIO, #OCANTIDAD', function () {
			const id = this.id;
			if (id === 'OCAT' && $(this).val()) setError(id);
			if (id === 'ODESCRIPCION' && $(this).val().trim()) setError(id);
			if (id === 'OTIPO' && $(this).val()) setError(id);
			if (id === 'OPRECIO') {
				const v = parseFloat($(this).val());
				if (!isNaN(v) && v >= 0) setError(id);
			}
			if (id === 'OCANTIDAD') {
				const v = parseInt($(this).val(), 10);
				if (!isNaN(v) && v >= 0) setError(id);
			}
		});

		function abrirModalArticulo(id = null) {
			// Limpiar formulario
			$("#formArticulo")[0].reset();
			$("#ID_ARTICULO").val("");
			$("#modalTitulo").text(id ? "Editar Artículo" : "Registrar Artículo");
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
	<?php include "../funciones/pie_fecha.php"; ?>
</body>

</html>

<script src="js/app-alerts.js"></script>