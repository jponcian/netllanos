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
	<span style="color:#d32f2f;font-size:1.5rem;"><i class="fa-solid fa-ban"></i></span>
	<span style="font-weight:600;font-size:1.15rem;color:#d32f2f;">Anular Solicitud
		#<?php echo $registro->solicitud; ?></span>
</div>
<div class="mb-2" style="font-size:0.98rem;">
	<strong>Funcionario:</strong> <?php echo $registro->funcionario; ?><br>
	<strong>Dependencia:</strong> <?php echo $registro->descripcion; ?><br>
	<strong>Fecha:</strong> <?php echo voltea_fecha($registro->fecha); ?>
</div>
<hr style="margin:10px 0;">
<form method="post" name="form-anular" id="form-anular" autocomplete="off"
	data-solicitud="<?php echo intval($_GET['solicitud']); ?>">
	<div id="alerta-global-anular" class="alert alert-danger py-2 px-3"
		style="display:none;font-size:0.85rem;margin-bottom:8px;"></div>
	<div class="mb-2 position-relative" style="max-width:100%;">
		<label for="ONOTAS" style="font-weight:500;">Motivo de anulación:</label>
		<div class="position-relative">
			<input type="text" name="ONOTAS" id="ONOTAS" class="form-control" maxlength="255" autocomplete="off">
			<span id="ONOTAS_ICON" class="valid-icon" aria-hidden="true"
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
		<button type="button" name="btn-anular-solicitud" id="btn-anular-solicitud" class="btn btn-danger"
			style="background:#d32f2f;border-color:#d32f2f;">
			<i class="fa-solid fa-ban me-1"></i>Anular Solicitud
		</button>
		<button type="button" class="btn btn-secondary"
			onclick="document.getElementById('anularModalCustom').classList.remove('show')">
			<i class="fa-solid fa-xmark me-1"></i>Cancelar
		</button>
</form>
<style>
	/* Estados visuales personalizados para el campo ONOTAS */
	#ONOTAS.is-valid {
		border-color: #198754 !important;
		padding-right: 2.2rem;
	}

	#ONOTAS.is-invalid {
		border-color: #dc3545 !important;
	}

	#ONOTAS:focus {
		box-shadow: 0 0 0 .2rem rgba(25, 135, 84, .15);
	}

	.valid-icon {
		transition: opacity .18s ease, transform .18s ease;
		pointer-events: none;
		/* asegurar nitidez */
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
	// === Validación estructurada del modal de anulación ===
	(function initValidacionAnular() {
		const form = document.getElementById('form-anular');
		if (!form) return;
		const motivo = form.querySelector('#ONOTAS');
		const icon = document.getElementById('ONOTAS_ICON');
		const alertGlobal = document.getElementById('alerta-global-anular');

		function limpiarCampo(campo) {
			campo.classList.remove('is-valid', 'is-invalid');
			const fb = document.getElementById('err_' + campo.id);
			if (fb) { fb.textContent = ''; fb.classList.remove('show'); }
		}

		function setError(campo, msg) {
			if (!campo) return;
			if (msg) {
				campo.classList.add('is-invalid');
				campo.classList.remove('is-valid');
				const fb = document.getElementById('err_' + campo.id);
				if (fb) { fb.textContent = msg; fb.classList.add('show'); }
			} else {
				campo.classList.add('is-valid');
				campo.classList.remove('is-invalid');
				const fb = document.getElementById('err_' + campo.id);
				if (fb) { fb.textContent = ''; fb.classList.remove('show'); }
			}
		}

		function validarMotivo() {
			const val = motivo.value.trim();
			if (val.length === 0) {
				setError(motivo, 'Debe indicar el motivo de anulación.');
				if (icon) icon.style.display = 'none';
				return false;
			}
			setError(motivo, '');
			if (icon) icon.style.display = 'inline';
			return true;
		}

		function validarFormulario(showGlobal = true) {
			let ok = true;
			if (!validarMotivo()) ok = false;
			if (!ok && showGlobal && alertGlobal) {
				// Sólo un posible mensaje global aquí: motivo faltante
				alertGlobal.innerHTML = 'Debe indicar el motivo de anulación.';
				alertGlobal.style.display = 'block';
			} else if (ok && alertGlobal) {
				alertGlobal.style.display = 'none';
				alertGlobal.textContent = '';
			}
			return ok;
		}

		// Eventos
		motivo.addEventListener('input', () => { validarMotivo(); if (alertGlobal) { alertGlobal.style.display = 'none'; } });
		motivo.addEventListener('blur', validarMotivo);

		const btn = document.getElementById('btn-anular-solicitud');
		if (btn) {
			btn.addEventListener('click', () => {
				if (!validarFormulario(true)) {
					motivo.focus();
					return;
				}
				// Si existe lógica externa que procesa la anulación, se deja continuar.
				// Aquí no se hace fetch porque lo asume el flujo existente fuera del modal.
			});
		}

		// Estado inicial neutro
		limpiarCampo(motivo);
	})();
</script>
</div>