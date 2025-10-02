<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
    http_response_code(401);
    exit('<div class="alert alert-danger">Sesión no válida.</div>');
}

$id = isset($_GET['solicitud']) ? intval($_GET['solicitud']) : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('<div class="alert alert-warning">Solicitud inválida.</div>');
}

$mysqli = $_SESSION['conexionsqli'];

// Cabecera: vista_alm_solicitud puede tener informacion general de la solicitud
$consulta = "SELECT * FROM vista_alm_solicitud WHERE id_solicitud=? ORDER BY descripcion LIMIT 1";
$stmt = $mysqli->prepare($consulta);
$stmt->bind_param('i', $id);
$stmt->execute();
$cab = $stmt->get_result()->fetch_object();
$stmt->close();

if (!$cab) {
    http_response_code(404);
    exit('<div class="alert alert-info">No se encontró la solicitud.</div>');
}

// Detalle de items
$consultax = "SELECT * FROM vista_alm_detalle_solicitud WHERE id_solicitud=? ORDER BY descripcion";
$stmtx = $mysqli->prepare($consultax);
$stmtx->bind_param('i', $id);
$stmtx->execute();
$det = $stmtx->get_result();
$items = [];
while ($row = $det->fetch_object()) {
    $items[] = $row;
}
$stmtx->close();

?>
<div class="mb-2" style="font-size:0.98rem;">
    <div class="d-flex align-items-center" style="gap:8px;">
        <span style="font-size:1.25rem;color:#0d6efd;"><i class="fas fa-hashtag"></i></span>
        <strong>Solicitud:</strong> <?php echo htmlspecialchars(isset($cab->solicitud) ? $cab->solicitud : $id); ?>
    </div>
    <div><strong>Fecha:</strong> <?php echo isset($cab->fecha) ? voltea_fecha($cab->fecha) : '-'; ?></div>
    <div><strong>División:</strong>
        <?php echo htmlspecialchars(isset($cab->descripcion) ? $cab->descripcion : (isset($cab->division) ? $cab->division : '')); ?>
    </div>
    <?php if (isset($cab->funcionario) && $cab->funcionario !== '') { ?>
        <div><strong>Funcionario:</strong> <?php echo htmlspecialchars($cab->funcionario); ?></div>
    <?php } ?>
    <div class="mt-1">
        <span class="badge badge-secondary">Estatus:
            <?php
            $status = isset($cab->status) ? (int) $cab->status : -1;
            $txt = 'Desconocido';
            $badge = 'secondary';
            if ($status === 0) {
                $txt = 'Creada';
                $badge = 'warning';
            } elseif ($status === 5) {
                $txt = 'Aprobada';
                $badge = 'primary';
            } elseif ($status === 10) {
                $txt = 'Despachada';
                $badge = 'success';
            } elseif ($status === 99) {
                $txt = 'Anulada';
                $badge = 'danger';
            }
            echo '<span class="badge badge-' . $badge . '">' . $txt . '</span>';
            ?>
        </span>
    </div>
</div>
<hr class="my-2" />
<div class="table-responsive">
    <table class="table table-sm table-bordered mb-0" style="font-size:0.95rem;">
        <thead class="thead-light">
            <tr>
                <th style="width:56px;">#</th>
                <th>Artículo</th>
                <th style="width:120px;" class="text-center">Cant. Solicitada</th>
                <th style="width:120px;" class="text-center">Cant. Aprobada</th>
                <th style="width:120px;" class="text-center">Cant. Despachada</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)) { ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Sin detalles para esta solicitud</td>
                </tr>
            <?php } else {
                foreach ($items as $i => $d) { ?>
                    <tr>
                        <td class="text-center"><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars(isset($d->descripcion) ? $d->descripcion : ''); ?></td>
                        <td class="text-center"><?php echo (int) (isset($d->cant_solicitada) ? $d->cant_solicitada : 0); ?></td>
                        <td class="text-center"><?php echo (int) (isset($d->cant_aprobada) ? $d->cant_aprobada : 0); ?></td>
                        <td class="text-center"><?php echo (int) (isset($d->cant_despachada) ? $d->cant_despachada : 0); ?></td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
</div>
<div class="mt-2 small text-muted">
    <?php if (!empty($cab->fecha_aprobacion)) { ?>
        <span class="mr-3"><strong>Aprobación:</strong> <?php echo voltea_fecha($cab->fecha_aprobacion); ?></span>
    <?php } ?>
    <?php if (!empty($cab->fecha_despacho)) { ?>
        <span class="mr-3"><strong>Despacho:</strong> <?php echo voltea_fecha($cab->fecha_despacho); ?></span>
    <?php } ?>
</div>
<style>
    .modal .table thead th {
        background: #f8f9fa;
    }
</style>