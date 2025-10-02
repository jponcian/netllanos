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
	<span style="font-weight:600;font-size:1.15rem;color:#d32f2f;">Anular Solicitud
		#<?php echo $registro->id_solicitud; ?></span>
</div>
<div class="mb-2" style="font-size:0.98rem;">
	<strong>Funcionario:</strong> <?php echo $registro->funcionario; ?><br>
	<strong>Dependencia:</strong> <?php echo $registro->descripcion; ?><br>
	<strong>Fecha:</strong> <?php echo voltea_fecha($registro->fecha); ?>
</div>
<hr style="margin:10px 0;">

<form method="post" id="form-anular-aut" autocomplete="off" data-solicitud="<?php echo intval($_GET['solicitud']); ?>">
	<div id="alerta-global-anular-aut" class="alert alert-danger py-2 px-3"
		style="display:none;font-size:0.85rem;margin-bottom:8px;"></div>
	<div class="mb-2 position-relative">
		<label for="ONOTAS" style="font-weight:500;">Motivo de anulación:</label>
		<div class="position-relative">
			<input type="text" name="ONOTAS" id="ONOTAS" class="form-control" maxlength="255" autocomplete="off"
				placeholder="Obligatorio">
			<span id="ONOTAS_ICON" class="valid-icon" aria-hidden="true"
				style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#198754;font-size:1rem;line-height:1;"><i
					class="fa-solid fa-check"></i></span>
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
				</tr>
			</thead>
			<tbody>
				<?php foreach ($detalles as $i => $detalle): ?>
					<tr>
						<td><?php echo $i + 1; ?></td>
						<td><?php echo $detalle->descripcion; ?></td>
						<td align="center"><?php echo $detalle->cant_solicitada; ?></td>
						<td align="center"><?php echo $detalle->cantidad; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="d-flex justify-content-end gap-2 mt-2">
		<button type="button" class="btn btn-danger" id="btn-guardar-anular-aut"
			style="background:#d32f2f;border-color:#d32f2f;">
			<i class="fa-solid fa-ban me-1"></i>Anular
		</button>
		<button type="button" class="btn btn-secondary"
			onclick="document.getElementById('aprobarModalCustom').classList.remove('show')">
			<i class="fa-solid fa-xmark me-1"></i>Cancelar
		</button>
	</div>
</form>
<style>
	#ONOTAS.is-valid {
		border-color: #198754 !important;
		padding-right: 2.1rem;
		box-shadow: 0 0 0 .15rem rgba(25, 135, 84, .15);
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
	(function initValidacionAnularAut() {
		const form = document.getElementById('form-anular-aut');
		if (!form) return;
		const motivo = form.querySelector('#ONOTAS');
		const icon = document.getElementById('ONOTAS_ICON');
		const alertGlobal = document.getElementById('alerta-global-anular-aut');
		const btn = document.getElementById('btn-guardar-anular-aut');
		const solicitudId = form.getAttribute('data-solicitud');

		function limpiarCampo() {
			motivo.classList.remove('is-valid', 'is-invalid');
			const fb = document.getElementById('err_ONOTAS');
			if (fb) { fb.textContent = ''; fb.classList.remove('show'); }
		}
		function setError(msg) {
			if (msg) {
				motivo.classList.add('is-invalid'); motivo.classList.remove('is-valid');
				const fb = document.getElementById('err_ONOTAS'); if (fb) { fb.textContent = msg; fb.classList.add('show'); }
				if (icon) icon.style.display = 'none';
			} else {
				motivo.classList.add('is-valid'); motivo.classList.remove('is-invalid');
				const fb = document.getElementById('err_ONOTAS'); if (fb) { fb.textContent = ''; fb.classList.remove('show'); }
				if (icon) icon.style.display = 'inline';
			}
		}
		function validar() {
			const v = motivo.value.trim();
			if (v.length === 0) { setError('Debe indicar el motivo de anulación.'); return false; }
			setError(''); return true;
		}
		function validarFormulario(showGlobal = true) {
			const ok = validar();
			if (!ok && showGlobal && alertGlobal) { alertGlobal.innerHTML = 'Debe indicar el motivo de anulación.'; alertGlobal.style.display = 'block'; }
			else if (ok && alertGlobal) { alertGlobal.style.display = 'none'; alertGlobal.textContent = ''; }
			return ok;
		}
		motivo.addEventListener('input', () => { validar(); if (alertGlobal) alertGlobal.style.display = 'none'; });
		motivo.addEventListener('blur', validar);
		if (btn) {
			btn.addEventListener('click', () => {
				if (!validarFormulario(true)) { motivo.focus(); return; }
				if (!solicitudId) {
					if (alertGlobal) { alertGlobal.innerHTML = 'No se identificó la solicitud.'; alertGlobal.style.display = 'block'; }
					return;
				}
				const originalHtml = btn.innerHTML;
				btn.disabled = true;
				btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Anulando...';
				const fd = new FormData();
				fd.append('CMDGUARDAR', 'Guardar');
				fd.append('ONOTAS', motivo.value.trim());
				fetch('9anular_autorizacion_guardar.php?solicitud=' + encodeURIComponent(solicitudId), { method: 'POST', body: fd })
					.then(r => r.text())
					.then(txt => {
						if (/Procesada\s*Exitosamente/i.test(txt)) {
							if (typeof Swal !== 'undefined') {
								Swal.fire({ icon: 'success', title: 'Anulada', text: 'La autorización fue anulada exitosamente.', timer: 1500, showConfirmButton: false });
							} else { alert('Autorización anulada.'); }
							// Cerrar modal si comparte id con aprobar (ajustar si diferente)
							const modal = document.getElementById('aprobarModalCustom');
							if (modal) modal.classList.remove('show');
							if (typeof ver === 'function') ver();
						} else {
							if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', html: txt.substring(0, 400) }); else alert('Error: ' + txt);
						}
					})
					.catch(err => { if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Fallo de comunicación' }); else alert('Fallo: ' + err); })
					.finally(() => { btn.disabled = false; btn.innerHTML = originalHtml; });
			});
		}
		limpiarCampo();
	})();
</script>