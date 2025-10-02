<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
    die("Acceso denegado");
}

$division = isset($_GET['division']) ? intval($_GET['division']) : 0;
$fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

if (empty($fecha_desde) || empty($fecha_hasta)) {
    echo "<div class='alert alert-warning'>Por favor, seleccione un rango de fechas.</div>";
    exit;
}

$con = $_SESSION['conexionsqli'];

if ($division > 0) {
    $sql = "SELECT divi.descripcion as division, a.descripcion as articulo, SUM(sd.cant_despachada) as cantidad 
            FROM alm_solicitudes s 
            JOIN alm_solicitudes_detalle sd ON s.id_solicitud = sd.id_solicitud 
            JOIN alm_inventario a ON sd.id_articulo = a.id_articulo 
            JOIN z_jefes_detalle divi ON s.division = divi.division 
            WHERE s.status = 10 AND sd.cant_despachada > 0 AND s.division = ? AND s.fecha_despacho BETWEEN ? AND ? 
            GROUP BY divi.descripcion, a.descripcion 
            ORDER BY SUM(sd.cant_despachada) DESC, a.descripcion ASC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $division, $fecha_desde, $fecha_hasta);
} else {
    $sql = "SELECT 'RESUMEN' as division, a.descripcion as articulo, SUM(sd.cant_despachada) as cantidad 
            FROM alm_solicitudes s 
            JOIN alm_solicitudes_detalle sd ON s.id_solicitud = sd.id_solicitud 
            JOIN alm_inventario a ON sd.id_articulo = a.id_articulo 
            WHERE s.status = 10 AND sd.cant_despachada > 0 AND s.fecha_despacho BETWEEN ? AND ? 
            GROUP BY a.descripcion 
            ORDER BY SUM(sd.cant_despachada) DESC, a.descripcion ASC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $fecha_desde, $fecha_hasta);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
?>
<div class="table-responsive">
    <table class="table table-bordered table-sm table-hover" id="reporte_tabla">
        <thead class="table-danger">
            <tr>
                <th>División</th>
                <th>Artículo</th>
                <th>Cantidad Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['division']); ?></td>
                    <td><?php echo htmlspecialchars($row['articulo']); ?></td>
                    <td><?php echo $row['cantidad']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php
} else {
    echo "<div class='alert alert-info'>No se encontraron artículos despachados para los filtros seleccionados.</div>";
}

$stmt->close();
?>
