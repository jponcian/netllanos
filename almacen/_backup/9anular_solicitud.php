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
<div class="modal-header bg-danger text-white" style="display:flex;align-items:center;gap:10px;">
	<span style="font-size:2rem;"><i class="fa-solid fa-ban"></i></span>
	<h5 class="modal-title m-0" style="font-weight:600;font-size:1.15rem;">Anular Solicitud #<?php echo $registro->id_solicitud; ?></h5>
</div>
<div class="mb-2" style="font-size:0.98rem;">
	<strong>Funcionario:</strong> <?php echo $registro->funcionario; ?><br>
	<strong>Dependencia:</strong> <?php echo $registro->descripcion; ?><br>
	<strong>Fecha:</strong> <?php echo voltea_fecha($registro->fecha); ?>
</div>
<hr style="margin:10px 0;">
<form method="post" id="form-anular" autocomplete="off" action="9anular_solicitud_guardar.php?solicitud=<?php echo intval($_GET['solicitud']); ?>">
	<div class="mb-2">
		<label for="ONOTAS" style="font-weight:500;">Motivo de anulación:</label>
		<input type="text" name="ONOTAS" id="ONOTAS" class="form-control" maxlength="255" style="border-color:#d32f2f;">
	</div>
	<div class="mb-2">
		<table class="table table-sm table-bordered" style="font-size:0.97rem;">
			<thead style="background:#f8d7da;">
				<tr>
					<th>Item</th>
					<th>Descripción</th>
					<th>Cant. Solicitada</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($detalles as $i => $detalle): ?>
					<tr>
						<td><?php echo $i + 1; ?></td>
						<td><?php echo $detalle->descripcion; ?></td>
						<td align="center"><?php echo $detalle->cant_solicitada; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="d-flex justify-content-end gap-2 mt-2">
	<button type="button" id="btn-anular-confirm" name="CMDANULAR" value="Anular Solicitud" class="btn btn-danger" style="background:#d32f2f;border-color:#d32f2f;">
			<i class="fa-solid fa-ban me-1"></i>Anular Solicitud
		</button>
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
			<i class="fa-solid fa-xmark me-1"></i>Cancelar
		</button>
	</div>
</form>

<script>
document.getElementById('btn-anular-confirm').addEventListener('click', function(e) {
	var form = document.getElementById('form-anular');
	var notas = document.getElementById('ONOTAS').value.trim();
	if (!notas) {
		Swal.fire({
			icon: 'warning',
			title: 'Campo requerido',
			text: 'Debe indicar el motivo de anulación.'
		});
		return false;
	}
	Swal.fire({
		title: '¿Está seguro de anular esta solicitud?',
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Sí, anular',
		cancelButtonText: 'Cancelar',
		reverseButtons: true
	}).then((result) => {
		if (result.isConfirmed) {
			var formData = new FormData(form);
			fetch(form.action, {
				method: 'POST',
				body: formData
			})
			.then(r => r.ok ? Promise.resolve() : Promise.reject())
			.then(() => {
				Swal.fire({
					icon: 'success',
					title: 'Solicitud anulada exitosamente',
					showConfirmButton: false,
					timer: 1500
				});
				// Cerrar el modal y recargar la tabla
				const modal = bootstrap.Modal.getInstance(document.getElementById('anularModalCustom'));
				if (modal) modal.hide();
				setTimeout(() => { window.location.reload(); }, 1600);
			})
			.catch(() => {
				Swal.fire({
					icon: 'error',
					title: 'Error al anular',
					text: 'No se pudo completar la anulación.'
				});
			});
		}
	});
});
</script>
</script>
