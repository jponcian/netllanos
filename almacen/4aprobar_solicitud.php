<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
	exit('<div class="alert alert-danger">Sesión no válida.</div>');
}

$consulta = "SELECT * FROM vista_alm_solicitud WHERE id_solicitud=" . intval($_GET['solicitud']) . " ORDER BY descripcion;";
$tabla = $_SESSION['conexionsqli']->query($consulta);
$registro = $tabla->fetch_object();

$consultax = "SELECT * FROM vista_alm_detalle_solicitud WHERE id_solicitud=" . intval($_GET['solicitud']) . " ORDER BY descripcion;";
$tablax = $_SESSION['conexionsqli']->query($consultax);
$detalles = [];
while ($row = $tablax->fetch_object()) {
	$detalles[] = $row;
}
?>
<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;justify-content:left;">
	<span style="color:#d32f2f;font-size:1.5rem;"><i class="fa-solid fa-truck"></i></span>
	<span style="font-weight:600;font-size:1.15rem;color:#d32f2f;">Aprobar Solicitud
		#<?php echo $registro->solicitud; ?></span>
</div>
<div class="mb-2" style="font-size:0.98rem;">
	<strong>Funcionario:</strong> <?php echo $registro->funcionario; ?><br>
	<strong>Dependencia:</strong> <?php echo $registro->descripcion; ?><br>
	<strong>Fecha:</strong> <?php echo voltea_fecha($registro->fecha); ?>
</div>
<hr style="margin:10px 0;">
<form method="post" id="form-aprobar" autocomplete="off" data-solicitud="<?php echo intval($_GET['solicitud']); ?>">
	<div id="formAprobarAlert" class="alert alert-warning" style="display:none;margin-bottom:10px;"></div>
	<div class="mb-2" style="max-width:100%;">
		<label for="ONOTAS" style="font-weight:500;">Notas:</label>
		<div class="position-relative">
			<input type="text" name="ONOTAS" id="ONOTAS" class="form-control" maxlength="255" autocomplete="off"
				placeholder="(Opcional)">
			<span id="ONOTAS_ICON_APROBAR" class="valid-icon" aria-hidden="true"
				style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#198754;font-size:1rem;line-height:1;">
				<i class="fa-solid fa-check"></i>
			</span>
		</div>
		<div class="invalid-feedback" id="err_ONOTAS"></div>
	</div>
	<div class="mb-2">
		<table class="table table-sm table-bordered" style="font-size:0.97rem;">
			<thead style="background:#f8d7da;">
				<tr>
					<th>Item</th>
					<th>Descripción</th>
					<th>Cant. Solicitada</th>
					<th>Disponible</th>
					<th>Cant. a Aprobar</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($detalles as $i => $detalle): ?>
					<tr>
						<td><?php echo $i + 1; ?></td>
						<td><?php echo $detalle->descripcion; ?></td>
						<td align="center"><?php echo $detalle->cant_solicitada; ?></td>
						<td align="center"><?php echo $detalle->cantidad; ?></td>
						<td>
							<div style="display:flex;flex-direction:column;align-items:center;">
								<input type="number" name="<?php echo $detalle->id_detalle; ?>" value="0"
									class="form-control form-control-sm text-center aprobar-cant-input"
									style="max-width:70px;display:inline-block;" min="0" step="1" pattern="\d*"
									inputmode="numeric" data-solicitada="<?php echo $detalle->cant_solicitada; ?>"
									data-disponible="<?php echo $detalle->cantidad; ?>"
									data-max="<?php echo min($detalle->cant_solicitada, $detalle->cantidad); ?>">
								<div class="invalid-feedback text-center p-0 m-0" style="font-size:0.65rem;line-height:1;"
									data-error-for="<?php echo $detalle->id_detalle; ?>"></div>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="d-flex justify-content-end gap-2 mt-2">
		<button type="button" class="btn btn-danger" style="background:#d32f2f;border-color:#d32f2f;"
			id="btn-aprobar-guardar">
			<i class="fa-solid fa-check me-1"></i>Guardar
		</button>
		<button type="button" class="btn btn-secondary"
			onclick="document.getElementById('aprobarModalCustom').classList.remove('show')">
			<i class="fa-solid fa-xmark me-1"></i>Cancelar
		</button>
	</div>
</form>
<style>
	/* Estilos explícitos para feedback visual en este modal */
	.aprobar-cant-input.is-valid {
		border-color: #198754 !important;
		box-shadow: 0 0 0 .15rem rgba(25, 135, 84, .15);
	}

	.aprobar-cant-input.is-invalid {
		border-color: #dc3545 !important;
		box-shadow: 0 0 0 .15rem rgba(220, 53, 69, .15);
	}

	#ONOTAS.is-valid {
		border-color: #198754 !important;
		box-shadow: 0 0 0 .15rem rgba(25, 135, 84, .15);
		padding-right: 2.1rem;
	}

	#ONOTAS.is-invalid {
		border-color: #dc3545 !important;
		box-shadow: 0 0 0 .15rem rgba(220, 53, 69, .15);
	}

	.valid-icon {
		transition: opacity .18s ease, transform .18s ease;
		pointer-events: none;
		-webkit-font-smoothing: antialiased;
	}

	#ONOTAS.is-valid+.valid-icon {
		opacity: 1;
		transform: translateY(-50%) scale(1);
	}

	#ONOTAS:not(.is-valid)+.valid-icon {
		opacity: 0;
		transform: translateY(-50%) scale(.85);
	}
</style>
<script>
	// Validación estilo registro de bienes aplicada a aprobar solicitud
	(function initValidacionAprobar() {
		const form = document.getElementById('form-aprobar');
		if (!form) return;
		const alertBox = document.getElementById('formAprobarAlert');
		const notas = document.getElementById('ONOTAS'); // opcional
		const iconNotas = document.getElementById('ONOTAS_ICON_APROBAR');
		const btnGuardar = document.getElementById('btn-aprobar-guardar');
		const inputs = Array.from(form.querySelectorAll('.aprobar-cant-input'));

		function limpiarValidacionAprobar() {
			if (alertBox) { alertBox.style.display = 'none'; alertBox.textContent = ''; }
			inputs.forEach(i => i.classList.remove('is-invalid', 'is-valid'));
			if (notas) notas.classList.remove('is-invalid', 'is-valid');
			const fbs = form.querySelectorAll('.invalid-feedback');
			fbs.forEach(fb => { fb.classList.remove('show'); fb.textContent = ''; });
		}

		function setErrorAprobar(input, message) {
			let feedbackEl = null;
			if (input === notas) feedbackEl = document.getElementById('err_ONOTAS');
			else feedbackEl = form.querySelector('.invalid-feedback[data-error-for="' + input.name + '"]');
			if (message) {
				input.classList.remove('is-valid');
				input.classList.add('is-invalid');
				if (feedbackEl) { feedbackEl.textContent = message; feedbackEl.classList.add('show'); }
				if (input === notas && iconNotas) iconNotas.style.display = 'none';
			} else {
				input.classList.remove('is-invalid');
				input.classList.add('is-valid');
				if (feedbackEl) { feedbackEl.textContent = ''; feedbackEl.classList.remove('show'); }
				if (input === notas && iconNotas) iconNotas.style.display = 'inline';
			}
		}

		function validarCantidadIndividual(input) {
			const raw = input.value.trim();
			if (raw === '') { input.value = '0'; }
			const v = parseInt(raw, 10);
			if (isNaN(v) || v < 0) { setErrorAprobar(input, 'Valor inválido'); return false; }
			const max = parseInt(input.dataset.max, 10);
			if (!isNaN(max) && v > max) { setErrorAprobar(input, 'Solicitado: ' + max); return false; }
			// 0 se marca inválido sin mensaje específico
			if (v === 0) { input.classList.add('is-invalid'); const fb = form.querySelector('.invalid-feedback[data-error-for="' + input.name + '"]'); if (fb) { fb.textContent = ''; fb.classList.remove('show'); } return false; }
			setErrorAprobar(input, ''); return true;
		}

		function hayAlMenosUnaMayorCero() {
			return inputs.some(i => { const v = parseInt(i.value, 10); return !isNaN(v) && v > 0; });
		}

		function validarFormularioAprobar(showGlobal = true) {
			let ok = true;
			let sinAprobados = false;
			let excedidos = false;
			// Notas opcional: sólo marcamos verde si tiene texto
			if (notas) {
				const t = notas.value.trim();
				if (t.length > 0) { setErrorAprobar(notas, ''); }
				else { notas.classList.remove('is-valid', 'is-invalid'); const fb = document.getElementById('err_ONOTAS'); if (fb) { fb.textContent = ''; fb.classList.remove('show'); } }
			}
			inputs.forEach(inp => {
				const raw = inp.value.trim();
				const v = parseInt(raw, 10);
				const max = parseInt(inp.dataset.max, 10);
				if (!validarCantidadIndividual(inp)) {
					if (!isNaN(v) && !isNaN(max) && v > max) excedidos = true;
				}
			});
			if (!hayAlMenosUnaMayorCero()) { sinAprobados = true; ok = false; }
			if (excedidos) ok = false;
			if (!ok && showGlobal && alertBox) {
				let msgs = [];
				if (sinAprobados) msgs.push('Debe aprobar al menos un artículo (> 0).');
				if (excedidos) msgs.push('No se puede aprobar más de lo solicitado.');
				alertBox.innerHTML = msgs.join('<br>');
				alertBox.style.display = 'block';
			} else if (ok && alertBox) { alertBox.style.display = 'none'; alertBox.textContent = ''; }
			return ok;
		}

		// Inicial: limpiar y validar cada input (sin mensaje global inicial)
		limpiarValidacionAprobar();
		inputs.forEach(inp => validarCantidadIndividual(inp));

		// Eventos en tiempo real
		inputs.forEach(inp => {
			inp.addEventListener('input', function () {
				validarCantidadIndividual(this);
				if (alertBox && alertBox.style.display === 'block') validarFormularioAprobar(false);
			});
			inp.addEventListener('blur', function () { validarCantidadIndividual(this); });
		});
		if (notas) {
			notas.addEventListener('input', function () {
				if (this.value.trim().length > 0) {
					setErrorAprobar(notas, '');
				} else {
					notas.classList.remove('is-valid', 'is-invalid');
					if (iconNotas) iconNotas.style.display = 'none';
					const fb = document.getElementById('err_ONOTAS');
					if (fb) { fb.textContent = ''; fb.classList.remove('show'); }
				}
			});
		}

		if (btnGuardar) {
			btnGuardar.addEventListener('click', function () {
				const valido = validarFormularioAprobar(true);
				if (!valido) return;
				const idSolicitud = form.getAttribute('data-solicitud');
				if (!idSolicitud) { if (alertBox) { alertBox.textContent = 'No se identificó la solicitud.'; alertBox.style.display = 'block'; } return; }
				// Construir FormData igual que backend espera (nombre = id_detalle, valor = cant aprobada)
				const fd = new FormData();
				fd.append('CMDGUARDAR', 'Guardar');
				if (notas && notas.value.trim().length > 0) fd.append('ONOTAS', notas.value.trim()); else fd.append('ONOTAS', '');
				inputs.forEach(inp => {
					const v = parseInt(inp.value, 10);
					if (!isNaN(v) && v > 0) fd.append(inp.name, v);
					else fd.append(inp.name, 0); // backend recorre todos
				});
				const originalHtml = btnGuardar.innerHTML;
				btnGuardar.disabled = true;
				btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
				fetch('4aprobar_solicitud_guardar.php?solicitud=' + encodeURIComponent(idSolicitud), { method: 'POST', body: fd })
					.then(r => r.text())
					.then(txt => {
						if (/Procesada\s*Exitosamente/i.test(txt)) {
							if (typeof Swal !== 'undefined') {
								Swal.fire({ icon: 'success', title: 'Aprobado', text: 'Solicitud procesada exitosamente', timer: 1400, showConfirmButton: false });
							}
							const modal = document.getElementById('aprobarModalCustom'); if (modal) modal.classList.remove('show');
							// Intentar refrescar listado: función ver() está en 4aprobar_solicitudes.php
							if (typeof ver === 'function') ver();
						} else {
							if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', html: txt.substring(0, 400) }); else alert('Error: ' + txt);
						}
					})
					.catch(err => { if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Fallo de comunicación' }); else alert('Fallo: ' + err); })
					.finally(() => { btnGuardar.disabled = false; btnGuardar.innerHTML = originalHtml; });
			});
		}
	})();
</script>