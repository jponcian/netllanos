<?php
session_start();
include __DIR__ . '/../conexion.php';
header('Content-Type: text/html; charset=utf-8');

$mysqli = $_SESSION['conexionsqli'];

$interno = isset($_GET['interno']) ? (int) $_GET['interno'] : null;
$divActual = isset($_GET['div_actual']) ? (int) $_GET['div_actual'] : null;
$divDest = isset($_GET['div_dest']) ? (int) $_GET['div_dest'] : null;

if ($divDest === null || $divActual === null || $interno === null) {
    echo '<div class="alert alert-warning">Parámetros incompletos para mostrar el detalle.</div>';
    exit;
}

$sql = "SELECT id_bien, numero_bien, descripcion_bien, division_actual, area_actual, division_destino, area_destino, interno
        FROM vbienes_pendientes
        WHERE por_reasignar = 2
          AND interno = ?
          AND id_division_actual = ?
          AND id_division_destino = ?
        ORDER BY numero_bien DESC";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('iii', $interno, $divActual, $divDest);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo '<div class="text-center text-muted p-4">No se encontraron bienes para esta reasignación.</div>';
        $stmt->close();
        exit;
    }

    echo '<div class="table-responsive">';
    echo '<table class="table table-sm table-striped table-hover mb-0">';
    echo '<thead class="table-light"><tr><th>Número</th><th>Descripción</th><th>Área Origen</th><th>Área Destino</th></tr></thead>';
    echo '<tbody>';
    while ($row = $res->fetch_assoc()) {
        $numero = htmlspecialchars($row['numero_bien']);
        $desc = htmlspecialchars($row['descripcion_bien']);
        $areaOrigen = htmlspecialchars(isset($row['area_actual']) ? $row['area_actual'] : '');
        $areaDest = htmlspecialchars(isset($row['area_destino']) ? $row['area_destino'] : '');
        echo '<tr>';
        echo '<td>' . $numero . '</td>';
        echo '<td>' . $desc . '</td>';
        echo '<td>' . $areaOrigen . '</td>';
        echo '<td>' . $areaDest . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';

    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Error en la consulta.</div>';
}

?>