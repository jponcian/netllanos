<?php
session_start();
include "../conexion.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 158;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	

// PARA ELIMINAR EL TEMPORAL
$mysqli = $_SESSION['conexionsqli'];
$consultad = "DELETE FROM alm_solicitudes_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
$mysqli->query($consultad);

header('Content-Type: text/html; charset=utf-8');
?>
<html>

<head>
	<meta charset="UTF-8">
	<?php include "../funciones/headNew.php"; ?>
	<link rel="stylesheet" href="css/custom-ui.css?v=1">
</head>

<body style="background: transparent !important;">
	<div id="mainContent">
		<form name="form1" id="form1" method="post" class="needs-validation" novalidate>
			<?php
			if ($_SESSION['VERIFICADO'] == 'SI') {
				$mysqli = $_SESSION['conexionsqli'];
				$consulta = 'SELECT z_empleados.cedula, Apellidos as apellidos, Nombres as nombres, descripcion as division FROM z_empleados , z_jefes_detalle WHERE z_jefes_detalle.division = z_empleados.division AND z_empleados.cedula=0' . $_SESSION['CEDULA_USUARIO'] . ';';
				$tabla = $mysqli->query($consulta);
				if ($registro = $tabla->fetch_assoc()) {
					$usuario = strtoupper($registro['nombres'] . ' ' . $registro['apellidos']);
					$dependencia = strtoupper($registro['division']);
				}
			}
			?>

			<div class="row justify-content-center mb-2">
				<div class="col-md-6">
					<div class="card shadow border-0">
						<div class="card-header bg-danger text-white text-center2">
							<h5 class="mb-0"><i class="fa fa-file-alt me-2"></i>Datos para la Solicitud</h5>
						</div>
						<div class="card-body">
							<div class="mb-1 row align-items-center">
								<label class="col-sm-4 col-form-label small fw-bold">Nombres:</label>
								<div class="col-sm-8 d-flex align-items-center">
									<span class="form-control-plaintext small mb-0"><?php echo $usuario; ?></span>
								</div>
							</div>
							<div class="mb-1 row align-items-center">
								<label class="col-sm-4 col-form-label small fw-bold">Dependencia:</label>
								<div class="col-sm-8 d-flex align-items-center">
									<span class="form-control-plaintext small mb-0"><?php echo $dependencia; ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-12">
					<div id="div2">
						<?php include "2solicitud_combo.php"; ?>
					</div>
				</div>
			</div>

			<div class="row mb-4">
				<div class="col-12 text-center2 d-flex justify-content-center align-items-center gap-3">
					<button type="button" id="guardarBtn" class="btn2 btn-success2 btn-lg px-5 shadow me-2"
						name="CMDGUARDAR" onClick="guardar()" disabled data-bs-toggle="tooltip"
						title="Agregue artículos para habilitar Guardar">
						<i class="fa fa-save me-2"></i>Guardar
					</button>
					<button class="btn2 btn-danger2 btn-lg rounded-circle ms-2" id="openCartBtn"
						aria-controls="cartOffcanvas" type="button" style="position:static;">
						<i class="fa-regular fa-folder" aria-hidden="true"></i>
						<span id="cartCount" class="badge bg-light text-dark">0</span>
					</button>
				</div>
			</div>
		</form>

		<footer class="text-center2 mt-4">
			<?php //include "../pie.php"; 
			?>
		</footer>

	</div>
	<!-- Contenedor para mostrar el PDF de la solicitud sin salir de la página (fuera de mainContent para no ocultarlo) -->
	<div id="pdfPreviewContainer" class="row mb-3 d-none">
		<div class="col-12">
			<div class="card shadow border-0">
				<div class="card-header bg-secondary text-white small">
					Vista de la Solicitud
					<button type="button" class="btn btn-sm btn-light float-end"
						onclick="closePdfPreview()">Cerrar</button>
				</div>
				<div class="card-body p-0">
					<iframe id="pdfPreviewIframe" src="" style="width:100%;height:600px;border:0;"
						title="Solicitud PDF"></iframe>
				</div>
			</div>
		</div>
	</div>
	<?php include "../funciones/footNew.php"; ?>
</body>

</html>

<script>
	function agregar() {
		var parametros = $("#form1").serialize();
		$.ajax({
			type: 'POST',
			url: '2solicitud_agregar_articulo.php',
			dataType: "json",
			data: parametros,
			success: function (data) {
				if (data.tipo == "alerta") {
					Swal.fire({
						icon: 'warning',
						title: 'Atención',
						text: data.msj
					});
				} else {
					if (data.msj === 'El Articulo fue Agregado Exitosamente!') {
						Swal.fire({
							toast: true,
							position: 'bottom-end',
							icon: 'success',
							title: data.msj,
							showConfirmButton: false,
							timer: 3000,
							timerProgressBar: true
						});
					} else {
						Swal.fire({
							icon: 'success',
							title: 'Éxito',
							text: data.msj
						});
					}
				}
				$('#div1-offcanvas').load('2solicitud_tabla.php', function () {
					updateCartCount();
				});
				$('#div2').load('2solicitud_combo.php');
			}
		});
		return false;
	}

	// PARA GUARDAR
	function guardar() {
		Swal.fire({
			title: '¿Desea Generar la Solicitud?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Sí',
			cancelButtonText: 'No'
		}).then((result) => {
			if (result.isConfirmed) {
				var parametros = $("#form1").serialize();
				$.ajax({
					type: 'POST',
					url: '2solicitud_guardar.php',
					dataType: "json",
					data: parametros,
					success: function (data) {
						// (Depuración eliminada) Antes se mostraba console.log y alert si no venía PDF
						if (data && data.tipo === "alerta") {
							Swal.fire({
								icon: 'warning',
								title: 'Atención',
								text: data.msj
							});
							$('#pdfPreviewContainer').addClass('d-none');
						} else {
							Swal.fire({
								icon: 'success',
								title: 'Éxito',
								text: data.msj
							}).then((result) => {
								if (result.isConfirmed || result.isDismissed) {
									if (data && data.pdf) {
										try {
											// Construir ruta absoluta dinámica según el path actual
											var pdfUrl = data.pdf;
											if (!/^https?:\/\//.test(pdfUrl)) {
												var basePath = window.location.pathname;
												basePath = basePath.substring(0, basePath.lastIndexOf('/'));
												var almacenIndex = basePath.lastIndexOf('/almacen');
												var fullPath = (almacenIndex !== -1) ? basePath.substring(0, almacenIndex + 8) : basePath;
												pdfUrl = window.location.protocol + '//' + window.location.host + fullPath + '/formatos/x_solicitud.php?solicitud=' + data.solicitud;
											}
											$('#pdfPreviewIframe').attr('src', pdfUrl);
											$('#pdfPreviewContainer').removeClass('d-none');
											// Ocultamos SOLO el formulario, no el contenedor general
											$('#form1').addClass('d-none');
											$('#openCartBtn').addClass('d-none');
											document.getElementById('pdfPreviewContainer').scrollIntoView({
												behavior: 'smooth'
											});
										} catch (e) {
											console.error('Error al mostrar PDF:', e);
										}
									}
								}
							});
						}
					}
				});
			}
		});
		return false;
	}

	function closePdfPreview() {
		try {
			$('#pdfPreviewContainer').addClass('d-none');
			$('#pdfPreviewIframe').attr('src', '');
		} catch (e) {
			console.error(e);
		}
		// Restaurar solo el formulario
		$('#form1').removeClass('d-none');
		$('#openCartBtn').removeClass('d-none');
		$('#div1-offcanvas').load('2solicitud_tabla.php', function () {
			updateCartCount();
		});
		$('#div2').load('2solicitud_combo.php');
		var el = document.getElementById('mainContent');
		if (el) el.scrollIntoView({
			behavior: 'smooth'
		});
	}

	function eliminar(id) {
		var parametros = "id=" + id;
		$.ajax({
			url: "2solicitud_eliminar.php",
			type: "POST",
			data: parametros,
			success: function (r) {
				$('#div1-offcanvas').load('2solicitud_tabla.php', function () {
					updateCartCount();
				});
				Swal.fire({
					toast: true,
					position: 'bottom-end',
					icon: 'success',
					title: 'Registro Eliminado Correctamente',
					showConfirmButton: false,
					timer: 3000,
					timerProgressBar: true
				});
			}
		});
		return false;
	}

	function updateCartCount() {
		var count = $('#div1-offcanvas').find('table tbody tr').length;
		if (count === 0) {
			count = $('#div1-offcanvas').find('.list-group-item').length;
		}
		$('#cartCount').text(count);
		if (count > 0) {
			$('#guardarBtn').prop('disabled', false).attr('title', '').removeAttr('data-bs-original-title');
			$('#guardarBtn').removeClass('disabled');
		} else {
			$('#guardarBtn').prop('disabled', true).attr('data-bs-original-title', 'Agregue artículos al carrito para habilitar Guardar');
			$('#guardarBtn').addClass('disabled');
		}
	}

	$(document).ready(function () {
		updateCartCount();

		// Tooltips eliminados

		$('#openCartBtn').on('click', function (e) {
			e.preventDefault();
			$('#div1-offcanvas').load('2solicitud_tabla.php', function () {
				updateCartCount();
				var offcanvasEl = document.getElementById('cartOffcanvas');
				var bsOff = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
				bsOff.show();
			});
		});

		$(document).ajaxSuccess(function (event, xhr, settings) {
			if (!settings || !settings.url) return;
			var url = settings.url;
			if (url.indexOf('2solicitud_agregar_articulo.php') !== -1 || url.indexOf('2solicitud_eliminar.php') !== -1 || url.indexOf('2solicitud_guardar.php') !== -1) {
				var wasOpen = $('#cartOffcanvas').hasClass('show');
				$('#div1-offcanvas').load('2solicitud_tabla.php', function () {
					updateCartCount();
					if (wasOpen) {
						var offcanvasEl = document.getElementById('cartOffcanvas');
						var bsOff = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
						bsOff.show();
					}
				});
				$('#div2').load('2solicitud_combo.php', function () {
					if ($('#OARTICULO').length) {
						$('#OARTICULO').select2({
							dropdownParent: $('#OARTICULO').parent(),
							dropdownAutoWidth: true,
							width: '100%',
							minimumResultsForSearch: 10
						});
						// Forzar scroll en el dropdown
						setTimeout(function () {
							$('.select2-results__options').css({ 'max-height': '250px', 'overflow-y': 'auto' });
						}, 200);
					}
				});
			}
		});
	});
</script>

<!-- Offcanvas carrito -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title" id="cartOffcanvasLabel">
			<i class="fas fa-box-open me-2" aria-hidden="true"></i>Artículos en la Solicitud
		</h5>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body">
		<div id="div1-offcanvas">
			<?php include "2solicitud_tabla.php"; ?>
		</div>
	</div>
</div>