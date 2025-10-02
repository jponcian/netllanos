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
	<?php include "../funciones/headNew.php"; ?>
	<link rel="stylesheet" href="css/custom-ui.css?v=1">
</head>


<body style="background: transparent !important;">
	<div class="mx-auto d-block" style="width:85%;max-width:1200px;">
		<div class="card border-danger p-0 m-0">
			<div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
				<p class="Estilo3" style="margin:0;font-size:1.15rem;">
					<i class="fa-solid fa-circle-check"></i> <strong>Solicitudes por Aprobar</strong>
				</p>
			</div>
			<div class="card-body p-2">
				<form id="filtrosForm" method="post" autocomplete="off">
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
					<?php include "4tabla_solicitudes.php"; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal personalizado para aprobar solicitud -->
	<div id="aprobarModalCustom">
		<div id="aprobarModalContent">
			<div id="aprobarModalHeader">
				<span id="aprobarModalTitle">Aprobar Solicitud</span>
				<button id="aprobarModalClose" title="Cerrar">&times;</button>
			</div>
			<div id="aprobarModalBody">
				<!-- Aquí se carga el formulario -->
			</div>
		</div>
	</div>

	<?php include "../funciones/footNew.php"; ?>
	<script>
		// Recarga el listado y delega los botones Procesar
		function ver() {
			var division = document.getElementById("txt_division").value;
			var fecha = document.getElementById("txt_fecha").value;
			$('#div1').load('4tabla_solicitudes.php?division=' + division + '&fecha=' + fecha, function () {
				document.querySelectorAll('.btn-aprobar-modal').forEach(btn => {
					btn.onclick = function (e) {
						e.preventDefault();
						showAprobarModal(this.dataset.url);
					};
				});
			});
		}

		// Modal personalizado para aprobar solicitud
		function showAprobarModal(url) {
			const modal = document.getElementById('aprobarModalCustom');
			const body = document.getElementById('aprobarModalBody');
			body.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-danger"></div> Cargando...</div>';
			modal.classList.add('show');
			fetch(url)
				.then(r => r.text())
				.then(html => {
					body.innerHTML = html;
					// Ejecutar scripts embebidos para que la validación funcione
					const scripts = Array.from(body.querySelectorAll('script'));
					scripts.forEach(sc => {
						const newSc = document.createElement('script');
						if (sc.src) { newSc.src = sc.src; } else { newSc.textContent = sc.textContent; }
						body.appendChild(newSc);
					});
				});
		}
		document.getElementById('aprobarModalClose').onclick = function () {
			document.getElementById('aprobarModalCustom').classList.remove('show');
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