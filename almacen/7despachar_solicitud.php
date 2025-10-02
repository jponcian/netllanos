<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
	exit('<div class="alert alert-danger">Sesión no válida.</div>');
}
// Usar conexión mysqli moderna
$con = $_SESSION['conexionsqli'];
$id_solicitud = intval($_GET['solicitud']);
$consulta = "SELECT * FROM vista_alm_solicitud WHERE id_solicitud=? ORDER BY descripcion;";
$stmt = $con->prepare($consulta);
$stmt->bind_param('i', $id_solicitud);
$stmt->execute();
$tabla = $stmt->get_result();
$registro = $tabla->fetch_object();
$stmt->close();

$consultax = "SELECT * FROM vista_alm_detalle_solicitud WHERE id_solicitud=? ORDER BY descripcion;";
$stmtx = $con->prepare($consultax);
$stmtx->bind_param('i', $id_solicitud);
$stmtx->execute();
$tablax = $stmtx->get_result();
$detalles = [];
while ($row = $tablax->fetch_object()) {
	$detalles[] = $row;
}
$stmtx->close();
?>
<div style="max-width:600px;margin:auto;">
	<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;justify-content:left;">
		<span style="color:#d32f2f;font-size:1.5rem;"><i class="fa-solid fa-truck"></i></span>
		<span style="font-weight:600;font-size:1.15rem;color:#d32f2f;">Despachar Solicitud
			#<?php echo $registro->id_solicitud; ?></span>
	</div>
	<div class="mb-2" style="font-size:0.98rem;">
		<strong>Funcionario:</strong> <?php echo $registro->funcionario; ?><br>
		<strong>Dependencia:</strong> <?php echo $registro->descripcion; ?><br>
		<strong>Fecha:</strong> <?php echo voltea_fecha($registro->fecha); ?>
	</div>
	<hr style="margin:10px 0;">
	<form method="post" id="form-despachar" autocomplete="off"
		data-solicitud="<?php echo intval($_GET['solicitud']); ?>">
		<div class="mb-2">
			<table class="table table-sm table-bordered" style="font-size:0.97rem;">
				<thead style="background:#bbdefb;">
					<tr>
						<th align="center">Item</th>
						<th align="center">Descripción</th>
						<th align="center">Cant. Solicitada</th>
						<th align="center">Cant. Aprobada</th>
						<th align="center">Disponible</th>
						<th align="center">Cant. a Despachar</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($detalles as $i => $detalle): ?>
						<tr>
							<td align="center"><?php echo $i + 1; ?></td>
							<td><?php echo $detalle->descripcion; ?></td>
							<td align="center"><?php echo $detalle->cant_solicitada; ?></td>
							<td align="center"><?php echo $detalle->cant_aprobada; ?></td>
							<td align="center"><?php echo $detalle->cantidad; ?></td>
							<td>
								<input type="number" min="0" max="<?php echo $detalle->cant_aprobada; ?>"
									name="<?php echo $detalle->id_detalle; ?>"
									value="<?php echo $detalle->cant_aprobada; ?>"
									class="form-control form-control-sm text-center"
									style="max-width:60px;display:inline-block;border-color:#1976d2;">
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="d-flex justify-content-end gap-2 mt-2">
			<button type="button" class="btn btn-danger" style="background:#d32f2f;border-color:#d32f2f;"
				onclick="guardarDespacho()">
				<i class="fa-solid fa-check me-1"></i>Guardar
			</button>
			<button type="button" class="btn btn-secondary"
				onclick="(function(){var btn=document.getElementById('despacharModalClose'); if(btn){ btn.click(); } else { var m=document.getElementById('despacharModalCustom'); if(m){ m.style.display='none'; m.classList.remove('show'); } } })();">
				<i class="fa-solid fa-xmark me-1"></i>Cancelar
			</button>
		</div>
	</form>
</div>