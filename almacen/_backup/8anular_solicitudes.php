<?php
session_start();
include "../conexion.php";
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 158;
include "../validacion_usuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Solicitudes por Anular</title>
	<?php include "../funciones/head.php"; ?>
	<link rel="stylesheet" href="css/custom-ui.css?v=1">
</head>

<body style="background: transparent !important;">
	<?php include "menu.php"; ?>
	<div class="mx-auto d-block" style="width:85%;max-width:1200px;">
		<div class="card border-danger p-0 m-0">
			<div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
				<p class="Estilo3" style="margin:0;font-size:1.15rem;">
					<i class="fa-solid fa-ban"></i> <strong>Solicitudes por Anular</strong>
				</p>
			</div>
			<div class="card-body p-2">
				<form id="filtrosForm" method="post" autocomplete="off">
					<div class="row mb-2">
						<div class="col-md-6">
							<label class="fw-bold">División:</label>
							<select name="txt_division" id="txt_division" class="form-select form-select-sm" onChange="ver()">
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
					<?php include "8z1solicitudes.php"; ?>
				</div>
			</div>
		</div>
	</div>


		<!-- Modal Bootstrap para anular solicitud -->
		<div class="modal fade" id="anularModalCustom" tabindex="-1" aria-labelledby="anularModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header bg-danger text-white">
						<h5 class="modal-title" id="anularModalLabel"><i class="fa-solid fa-ban"></i> Anular Solicitud</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
					</div>
					<div class="modal-body" id="anularModalBody">
						<!-- Aquí se carga el contenido -->
					</div>
				</div>
			</div>
		</div>

<script>
// Recarga el listado y delega los botones Anular
function ver() {
	var division = document.getElementById("txt_division").value;
	var fecha = document.getElementById("txt_fecha").value;
	$('#div1').load('8z1solicitudes.php?division=' + division + '&fecha=' + fecha, function() {
		document.querySelectorAll('.btn-anular-modal').forEach(btn => {
			btn.onclick = function(e) {
				e.preventDefault();
				showAnularModal(this.dataset.url);
			};
		});
	});
}

// Modal Bootstrap para anular solicitud
function showAnularModal(url) {
	const modal = new bootstrap.Modal(document.getElementById('anularModalCustom'));
	document.getElementById('anularModalBody').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-danger"></div> Cargando...</div>';
	modal.show();
	fetch(url)
		.then(r => r.text())
		.then(html => {
			document.getElementById('anularModalBody').innerHTML = html;
			// Validación y confirmación al enviar el formulario
			var form = document.getElementById('form-anular');
			if (form) {
				form.addEventListener('submit', function(e) {
					var notas = document.getElementById('ONOTAS').value.trim();
					if (!notas) {
						e.preventDefault();
						Swal.fire({
							icon: 'warning',
							title: 'Campo requerido',
							text: 'Debe indicar el motivo de anulación.'
						});
						return false;
					}
					e.preventDefault();
					Swal.fire({
						title: '¿Está seguro de anular esta solicitud?',
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Sí, anular',
						cancelButtonText: 'Cancelar',
						reverseButtons: true
					}).then((result) => {
						if (result.isConfirmed) {
							form.submit();
						}
					});
					return false;
				});
			}
		});
}

// Inicializar eventos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
	ver();
});
</script>
</body>

</html>