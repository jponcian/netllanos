<?php
session_start();
include "../conexion.php";
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 159;
include "../validacion_usuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Solicitudes por Anular</title>

	<link rel="stylesheet" href="css/custom-ui.css?v=1">
</head>


<body style="background: transparent !important;">
	<?php include "menu.php"; ?>
	<div class="mx-auto d-block" style="width:85%;max-width:1200px;">
		<div class="card border-danger p-0 m-0">
			<div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
				<p class="Estilo3" style="margin:0;font-size:1.15rem;">
					<i class="fa-solid fa-ban"></i> <strong>Solicitudes (Disponibles para Anular)</strong>
				</p>
			</div>
			<div class="card-body p-2">
				<form id="filtrosForm" name="filtrosForm" method="post" autocomplete="off">
					<div class="row mb-2">
						<div class="col-md-6">
							<label class="fw-bold">División:</label>
							<select name="txt_division" id="txt_division" class="form-select form-select-sm"
								onChange="ver()">
								<?php
								if ($_SESSION['DIVISION_USUARIO'] == 9) {
									echo '<option value="0">-------- TODAS --------</option>';
									$consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud WHERE status=0 GROUP BY descripcion;';
								} else {
									$consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud WHERE status=0 and division=' . $_SESSION['DIVISION_USUARIO'] . ' GROUP BY descripcion;';
								}
								$tabla_x = $_SESSION['conexionsqli']->query($consulta_x);
								while ($registro_x = $tabla_x->fetch_object()) {
									echo '<option value="' . $registro_x->division . '">' . $registro_x->descripcion . '</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-6">
							<label class="fw-bold">Fecha:</label>
							<select name="txt_fecha" id="txt_fecha" class="form-select form-select-sm" onChange="ver()">
								<option value="0">Todas</option>
								<?php
								$consulta_x = 'SELECT month(fecha) as mes, year(fecha) as anno FROM alm_solicitudes WHERE 1=1 and status=0 GROUP BY year(fecha), month(fecha) ORDER BY fecha DESC;';
								$tabla_x = $_SESSION['conexionsqli']->query($consulta_x);
								while ($registro_x = $tabla_x->fetch_object()) {
									echo '<option';
									if (isset($_POST['OMES']) && $_POST['OMES'] == $registro_x->mes . '-' . $registro_x->anno) {
										echo ' selected="selected" ';
									}
									echo ' value="' . $registro_x->mes . '-' . $registro_x->anno . '">';
									echo $_SESSION['meses_anno'][$registro_x->mes] . ' ' . $registro_x->anno;
									echo '</option>';
								}
								?>
							</select>
						</div>
					</div>
				</form>
				<div id="div1">
					<?php include "8tabla_solicitudes.php"; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal personalizado para anular solicitud (igual al de aprobar) -->
	<div id="anularModalCustom">
		<div id="anularModalContent">
			<div id="anularModalHeader">
				<span id="anularModalTitle">Anular Solicitud</span>
				<button id="anularModalClose" title="Cerrar">&times;</button>
			</div>
			<div id="anularModalBody">
				<!-- Aquí se carga el formulario -->
			</div>
		</div>
	</div>

	<script>
		// Recarga el listado y delega los botones Procesar
		function ver() {
			var division = document.getElementById("txt_division").value;
			var fecha = document.getElementById("txt_fecha").value;
			$('#div1').load('8tabla_solicitudes.php?division=' + division + '&fecha=' + fecha, function () {
				document.querySelectorAll('.btn-anular-modal').forEach(btn => {
					btn.onclick = function (e) {
						e.preventDefault();
						showAnularModal(this.dataset.url);
					};
				});
			});
		}

		// Modal personalizado para anular solicitud (igual al de aprobar)
		function showAnularModal(url) {
			const modal = document.getElementById('anularModalCustom');
			const body = document.getElementById('anularModalBody');
			body.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-danger"></div> Cargando...</div>';
			modal.classList.add('show');
			fetch(url)
				.then(r => r.text())
				.then(html => {
					body.innerHTML = html;
					// Asignar evento al botón Guardar del modal (por id)
					const btnGuardar = document.getElementById('btn-anular-solicitud');
					const form = document.getElementById('form-anular');
					if (btnGuardar && form) {
						btnGuardar.onclick = function (e) {
							e.preventDefault();
							var motivo = document.getElementById('ONOTAS').value.trim();
							if (!motivo) {
								document.getElementById('ONOTAS').classList.add('is-invalid');
								if (typeof Swal !== 'undefined') {
									setTimeout(function () {
										var swal = document.querySelector('.swal2-container');
										if (swal) swal.style.zIndex = 20000;
									}, 10);
									Swal.fire({
										icon: 'warning',
										title: 'Campo requerido',
										text: 'Debe indicar el motivo de anulación.',
										didOpen: () => {
											setTimeout(function () {
												var swal = document.querySelector('.swal2-container');
												if (swal) swal.style.zIndex = 20000;
											}, 10);
										}
									});
								} else {
									alert('Debe indicar el motivo de anulación.');
								}
								return;
							} else {
								document.getElementById('ONOTAS').classList.remove('is-invalid');
							}
							// Enviar AJAX para anular la solicitud
							var solicitud = form.getAttribute('data-solicitud') || '';
							if (!solicitud) {
								var urlParams = new URLSearchParams(window.location.search);
								solicitud = urlParams.get('solicitud') || '';
							}
							if (!solicitud) {
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: 'No se pudo determinar el número de solicitud.'
								});
								return;
							}
							var formData = new FormData(form);
							formData.set('btn-anular-solicitud', 'Anular Solicitud');
							fetch('8anular_solicitud_guardar.php?solicitud=' + encodeURIComponent(solicitud), {
								method: 'POST',
								body: formData
							})
								.then(r => r.text())
								.then(resp => {
									if (/Procesada\s*Exitosamente|anulada\s*exitosamente|guardado\s*con\s*éxito/i.test(resp)) {
										modal.classList.remove('show');
										ver();
										Swal.fire({
											icon: 'success',
											title: 'Solicitud anulada',
											text: 'La solicitud fue anulada exitosamente.',
											confirmButtonColor: '#d32f2f'
										});
									} else {
										// Si la respuesta parece un error, mostrarla en el modal
										body.innerHTML = resp;
										// Volver a asignar el evento si el formulario se recarga
										showAnularModal(url);
									}
								})
								.catch(function (error) {
									alert('Error al anular: ' + error);
								});
						};
					}
				});
		}
		document.getElementById('anularModalClose').onclick = function () {
			document.getElementById('anularModalCustom').classList.remove('show');
		};
		// Inicializar
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', ver);
		} else {
			ver();
		}


	</script>

</body>

</html>