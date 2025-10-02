<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
    http_response_code(401);
    exit('<div class="alert alert-danger">Sesión no válida.</div>');
}

$id = isset($_GET['reasignacion']) ? intval($_GET['reasignacion']) : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('<div class="alert alert-warning">Movimiento inválido.</div>');
}

$mysqli = $_SESSION['conexionsqli'];

// Cabecera del movimiento
$sqlCab = "SELECT r.id_reasignacion, r.numero, r.fecha,
                  zact.descripcion AS division_origen,
                  zdes.descripcion AS division_destino
           FROM bn_reasignaciones r
           JOIN bn_reasignaciones_detalle d ON d.id_reasignacion = r.id_reasignacion
           JOIN bn_areas aact ON aact.id_area = d.id_area_anterior
           JOIN bn_areas ades ON ades.id_area = d.id_area_destino
           JOIN z_jefes_detalle zact ON zact.division = aact.division
           JOIN z_jefes_detalle zdes ON zdes.division = ades.division
           WHERE r.id_reasignacion = ?
           GROUP BY r.id_reasignacion, r.numero, r.fecha, zact.descripcion, zdes.descripcion
           LIMIT 1";
$stmt = $mysqli->prepare($sqlCab);
$stmt->bind_param('i', $id);
$stmt->execute();
$cab = $stmt->get_result()->fetch_object();
$stmt->close();

if (!$cab) {
    http_response_code(404);
    exit('<div class="alert alert-info">No se encontró el movimiento.</div>');
}

// Detalle de bienes del movimiento
$sqlDet = "SELECT b.numero_bien, b.descripcion_bien,
                  cat.descripcion AS categoria,
                  aact.descripcion AS area_origen,
                  ades.descripcion AS area_destino
           FROM bn_reasignaciones_detalle d
           JOIN bn_bienes b ON b.id_bien = d.id_bien
           JOIN bn_categorias cat ON cat.id_categoria = b.id_categoria
           JOIN bn_areas aact ON aact.id_area = d.id_area_anterior
           JOIN bn_areas ades ON ades.id_area = d.id_area_destino
           WHERE d.id_reasignacion = ? AND d.borrado = 0
           ORDER BY b.numero_bien";
$stmt2 = $mysqli->prepare($sqlDet);
$stmt2->bind_param('i', $id);
$stmt2->execute();
$det = $stmt2->get_result();
$items = [];
while ($row = $det->fetch_object()) {
    $items[] = $row;
}
$stmt2->close();
?>
<div class="mb-2" style="font-size:0.98rem;">
    <div class="d-flex align-items-center" style="gap:8px;">
        <span style="font-size:1.25rem;color:#dc3545;"><i class="fas fa-exchange-alt"></i></span>
        <strong>Movimiento:</strong> #<?php echo htmlspecialchars($cab->numero ?: $cab->id_reasignacion); ?>
    </div>
    <div><strong>Fecha:</strong> <?php echo htmlspecialchars(voltea_fecha($cab->fecha)); ?></div>
    <div><strong>De:</strong> <?php echo htmlspecialchars($cab->division_origen); ?></div>
    <div><strong>A:</strong> <?php echo htmlspecialchars($cab->division_destino); ?></div>
</div>
<hr class="my-2" />
<div class="table-responsive">
    <table class="table table-sm table-bordered mb-0" style="font-size:0.95rem;">
        <thead class="thead-light">
            <tr>
                <th style="width:56px;">#</th>
                <th>N° Bien</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Área Origen</th>
                <th>Área Destino</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)) { ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Sin bienes en este movimiento.</td>
                </tr>
            <?php } else {
                foreach ($items as $i => $d) { ?>
                    <tr>
                        <td class="text-center"><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($d->numero_bien); ?></td>
                        <td><?php echo htmlspecialchars($d->descripcion_bien); ?></td>
                        <td><?php echo htmlspecialchars($d->categoria); ?></td>
                        <td><?php echo htmlspecialchars($d->area_origen); ?></td>
                        <td><?php echo htmlspecialchars($d->area_destino); ?></td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
</div>
<style>
    .modal .table thead th {
        background: #f8f9fa;
    }
</style>