<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
    echo '<tr><td colspan="7">Sesión no válida.</td></tr>';
    exit();
}
// Filtros desde GET
$filtro = '';
if (isset($_GET['division']) && $_GET['division'] != '0') {
    $filtro .= 'division=' . intval($_GET['division']) . ' AND ';
} elseif ($_SESSION['DIVISION_USUARIO'] != 9) {
    $filtro .= 'division=' . intval($_SESSION['DIVISION_USUARIO']) . ' AND ';
}
$filtro2 = '';
if (isset($_GET['fecha']) && $_GET['fecha'] != '0') {
    list($mes, $anno) = explode('-', $_GET['fecha']);
    $filtro2 = 'month(fecha)=' . intval($mes) . ' and year(fecha)=' . intval($anno) . ' AND ';
}
$consulta = "SELECT * FROM vista_alm_solicitud WHERE $filtro $filtro2 status=5 ORDER BY id_solicitud DESC;";
$tabla = $_SESSION['conexionsqli']->query($consulta);
$i = 0;
while ($registro = $tabla->fetch_object()) {
    $i++;
    echo '<tr>';
    echo '<td>' . $i . '</td>';
    echo '<td>' . voltea_fecha($registro->fecha) . '</td>';
    echo '<td>' . $registro->id_solicitud . '</td>';
    echo '<td>' . $registro->descripcion . '</td>';
    echo '<td>' . $registro->funcionario . '</td>';
    echo '<td><button type="button" class="btn btn-outline-danger btn-despachar-modal" data-url="7despachar_solicitud.php?solicitud=' . $registro->id_solicitud . '">Despachar</button></td>';
    echo '<td><a tooltip="Pdf" href="../almacen/formatos/x_solicitud.php?solicitud=' . $registro->id_solicitud . '" target="_blank"><i class="fa-regular fa-file-pdf fa-2x"></i></a></td>';
    echo '</tr>';
}
if ($i == 0) {
    echo '<tr><td colspan="7">No Existe Información</td></tr>';
}
