<?php
session_start();
include "../conexion.php";
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 161; // Access code for this module
include "../validacion_usuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<?php include "../funciones/headNew.php"; ?>
	<style>
		/* Fallback local styles para el modal de anular: asegura overlay y oculto por defecto */
		#anularModalCustom {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100vw;
			height: 100vh;
			background: rgba(0, 0, 0, 0.45);
			align-items: center;
			justify-content: center;
			z-index: 9999;
			padding: 12px;
			box-sizing: border-box;
		}

		#anularModalCustom.show {
			display: flex;
		}

		#anularModalContent {
			background: #fff;
			padding: 20px 22px;
			border-radius: 12px;
			min-width: 340px;
			width: 40%;
			max-width: 540px;
			text-align: left;
			position: relative;
			box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12);
			border-top: 6px solid #d32f2f;
			max-height: 85vh;
			overflow-y: auto;
			display: flex;
			flex-direction: column;
			gap: 10px;
		}

		#anularModalClose {
			background: none;
			border: none;
			font-size: 1.5rem;
			color: #d32f2f;
			cursor: pointer;
			line-height: 1;
		}

		@media (max-width: 600px) {
			#anularModalContent {
				width: 98vw;
				min-width: 0;
				max-width: 99vw;
			}
		}
	</style>
</head>


<body style="background: transparent !important;">

	<div class="card border-danger p-0 m-0">
		<div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
			<p class="Estilo3" style="margin:0;font-size:1.15rem;">
				<i class="fa-solid fa-ban"></i> <strong>Autorizadas (Disponibles)</strong>
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
								$consulta_x = 'SELECT division, descripcion FROM vista_alm_ingreso WHERE status <> 99 GROUP BY descripcion;';
							} else {
								$consulta_x = 'SELECT division, descripcion FROM vista_alm_ingreso WHERE status <> 99 and division=' . $_SESSION['DIVISION_USUARIO'] . ' GROUP BY descripcion;';
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
							// Selección por defecto del mes-anno: usar POST['OMES'] si existe, sino el mes y año actuales
							$selectedFecha = isset($_POST['OMES']) && $_POST['OMES'] !== '' ? $_POST['OMES'] : (date('n') . '-' . date('Y'));
							$consulta_x = 'SELECT month(fecha) as mes, year(fecha) as anno FROM alm_ingresos WHERE status<>99 GROUP BY year(fecha), month(fecha) ORDER BY fecha DESC;';
							$tabla_x = $_SESSION['conexionsqli']->query($consulta_x);
							while ($registro_x = $tabla_x->fetch_object()) {
								$val = $registro_x->mes . '-' . $registro_x->anno;
								echo '<option value="' . $val . '"' . ($selectedFecha == $val ? ' selected="selected"' : '') . '>';
								echo $_SESSION['meses_anno'][$registro_x->mes] . ' ' . $registro_x->anno;
								echo '</option>';
							}
							?>
						</select>
					</div>
				</div>
			</form>
			<div id="div1">

			</div>
		</div>
	</div>

	<!-- Modal personalizado para anular ingreso -->
	<div id="anularModalCustom">
		<div id="anularModalContent">
			<div id="anularModalHeader">
				<span id="anularModalTitle">Anular Ingreso</span>
				<button id="anularModalClose" title="Cerrar">&times;</button>
			</div>
			<div id="anularModalBody">
				<!-- Aquí se carga el formulario -->
			</div>
		</div>
	</div>


	<?php include "../funciones/footNew.php"; ?>
	<script>
		function ver1() {
			var fecha = document.getElementById("OMES").value;
			$('#div1').load('11consulta_tabla.php?fecha=' + fecha);
		}
	</script>

	<script>
		// Recarga el listado de ingresos
		function ver() {
			var division = document.getElementById("txt_division").value;
			var fecha = document.getElementById("txt_fecha").value;
			$('#div1').html('<div class="text-center p-4"><div class="spinner-border text-danger"></div> Cargando...</div>');
			$('#div1').load('12tabla_ingresos.php?division=' + division + '&fecha=' + fecha, function () {
				document.querySelectorAll('.btn-anular-modal').forEach(btn => {
					btn.onclick = function (e) {
						e.preventDefault();
						showAnularModal(this.dataset.url);
					};
				});
			});
		}

		/**
		 * Configura los eventos y la lógica para el formulario dentro del modal de anulación.
		 * Esta función se puede volver a ejecutar si el formulario se vuelve a renderizar (p. ej., después de un error de validación).
		 * @param {HTMLElement} modalBody El elemento del cuerpo del modal donde se encuentra el formulario.
		 * @param {string} formUrl La URL desde la que se cargó originalmente el formulario.
		 */
		function setupAnularForm(modalBody, formUrl) {
			const form = modalBody.querySelector('#form-anular');
			const btnGuardar = modalBody.querySelector('#btn-anular-ingreso');
			const motivoInput = modalBody.querySelector('#ONOTAS');

			// Si no se encuentran los elementos del formulario, es posible que se haya cargado un mensaje de error.
			if (!form || !btnGuardar || !motivoInput) {
				// Si la respuesta del servidor fue una cadena vacía, muestra un error claro.
				if (modalBody.innerText.trim() === '') {
					Swal.fire({
						icon: 'error',
						title: 'Error Inesperado',
						text: 'El servidor devolvió una respuesta vacía. La anulación pudo haber fallado.',
						confirmButtonColor: '#d32f2f'
					});
				}
				return;
			}

			const toggleSaveButton = () => {
				btnGuardar.disabled = motivoInput.value.trim() === '';
			};

			motivoInput.addEventListener('input', toggleSaveButton);
			toggleSaveButton(); // Establecer el estado inicial del botón

			form.onsubmit = function (e) {
				e.preventDefault();

				btnGuardar.disabled = true;
				btnGuardar.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...`;

				let ingreso = form.dataset.ingreso || '';
				if (!ingreso) {
					const searchString = formUrl.includes('?') ? formUrl.substring(formUrl.indexOf('?')) : '';
					if (searchString) {
						const urlParams = new URLSearchParams(searchString);
						ingreso = urlParams.get('ingreso') || '';
					}
				}

				if (!ingreso) {
					Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo determinar el número de ingreso.' });
					btnGuardar.disabled = false;
					btnGuardar.innerText = 'Anular Ingreso';
					return;
				}

				const formData = new FormData(form);
				formData.set('btn-anular-ingreso', 'Anular Ingreso');

				fetch('12anular_ingreso_guardar.php?ingreso=' + encodeURIComponent(ingreso), {
					method: 'POST',
					body: formData
				})
					.then(response => response.text())
					.then(text => {
						if (text.trim() === 'Procesada Exitosamente') {
							document.getElementById('anularModalCustom').classList.remove('show');
							ver();
							Swal.fire({
								icon: 'success',
								title: 'Ingreso Anulado',
								text: 'El ingreso fue anulado exitosamente.',
								confirmButtonColor: '#d32f2f'
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'No se pudo anular',
								text: text || 'Ocurrió un error desconocido.',
								confirmButtonColor: '#d32f2f'
							});
							btnGuardar.disabled = false;
							btnGuardar.innerHTML = 'Anular Ingreso';
						}
					})
					.catch(error => {
						console.error('Error de comunicación o formato:', error);
						Swal.fire({
							icon: 'error',
							title: 'Error de Comunicación',
							text: 'No se pudo contactar al servidor o la respuesta no fue válida.',
							confirmButtonColor: '#d32f2f'
						});
						btnGuardar.disabled = false;
						btnGuardar.innerHTML = 'Anular Ingreso';
					});
			};

			// Se asegura de que el onsubmit del formulario se dispare al hacer clic en el botón.
			btnGuardar.onclick = (e) => {
				e.preventDefault();
				form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
			};
		}

		// Muestra el modal personalizado para anular
		function showAnularModal(url) {
			const modal = document.getElementById('anularModalCustom');
			const body = document.getElementById('anularModalBody');
			body.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-danger"></div> Cargando...</div>';
			modal.classList.add('show');

			fetch(url)
				.then(response => {
					if (!response.ok) {
						throw new Error(`Error HTTP: ${response.status}`);
					}
					return response.text();
				})
				.then(html => {
					// Render directo (mantener encabezado) y ejecutar scripts; si luego decidimos remover duplicado, hacerlo editando el parcial.
					body.innerHTML = html;
					// Ejecutar scripts embebidos (validación campo motivo)
					const scripts = body.querySelectorAll('script');
					scripts.forEach(sc => {
						const s = document.createElement('script');
						if (sc.src) s.src = sc.src; else s.textContent = sc.textContent;
						document.head.appendChild(s);
					});
					// Fallback de validación si el script embebido no añadió listeners (por cualquier razón)
					(function fallbackValidacion() {
						const inp = body.querySelector('#ONOTAS');
						if (!inp) return;
						// Evitar duplicar si ya hay listener (marcamos con dataset)
						if (inp.dataset.vlistener) return; inp.dataset.vlistener = '1';
						const icon = body.querySelector('#ONOTAS_ICON');
						function aplicar() {
							if (inp.value.trim().length > 0) {
								inp.classList.add('is-valid'); inp.classList.remove('is-invalid'); if (icon) icon.style.display = 'inline';
							} else {
								inp.classList.remove('is-valid'); if (icon) icon.style.display = 'none';
							}
						}
						inp.addEventListener('input', aplicar);
					})();
					setupAnularForm(body, url);
				})
				.catch(error => {
					console.error('Error al cargar formulario de anulación:', error);
					body.innerHTML = `<div class="alert alert-danger p-2">No se pudo cargar el contenido. ${error.message}</div>`;
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