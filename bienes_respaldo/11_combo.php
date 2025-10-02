<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

header('Content-Type: text/html; charset=iso-8859-1');

$tipo = isset($_POST['tipo']) ? intval($_POST['tipo']) : 1;
$sede = isset($_GET['sede']) ? intval($_GET['sede']) : 0;
$division = isset($_GET['division']) ? intval($_GET['division']) : 0;

if ($tipo == 1) {
    // Combo de divisiones según sede
    echo '<option value="0">Seleccione</option>';
    $sede_int = isset($sede) && $sede > 0 ? intval($sede) : 0;
    if ($sede_int > 0) {
        $where = 'z_jefes_detalle.id_sector = ' . $sede_int;
    } else {
        $where = '1=1';
    }
    $sql = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE ' . $where . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
    $res = mysqli_query($_SESSION['conexionsqli'], $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<option value="' . $row['division'] . '">' . palabras($row['descripcion']) . '</option>';
    }
} elseif ($tipo == 3) {
    // Combo de áreas según división
    echo '<option value="0">Seleccione</option>';
    $sql = "SELECT id_area, descripcion FROM bn_areas WHERE division = $division ORDER BY descripcion";
    $res = mysqli_query($_SESSION['conexionsqli'], $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<option value="' . $row['id_area'] . '">' . palabras($row['descripcion']) . '</option>';
    }
}
