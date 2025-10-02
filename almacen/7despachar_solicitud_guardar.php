<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_GET['solicitud']) &&
    isset($_POST['CMDGUARDAR']) && $_POST['CMDGUARDAR'] === 'Guardar'
) {
    $id_solicitud = intval($_GET['solicitud']);
    $con = $_SESSION['conexionsqli'];
    // Actualizar cabecera de la solicitud
    $consulta = "UPDATE alm_solicitudes SET fecha_despacho=NOW(), status=10, despacho='" . $_SESSION['CEDULA_USUARIO'] . "', usuario='" . $_SESSION['CEDULA_USUARIO'] . "' WHERE id_solicitud=" . $id_solicitud . ";";
    $con->query($consulta);

    $consultax = "SELECT * FROM vista_alm_detalle_solicitud WHERE id_solicitud=" . $id_solicitud . " ORDER BY descripcion;";
    $tablax = $con->query($consultax);
    if ($tablax) {
        while ($registrox = $tablax->fetch_object()) {
            $id_detalle = $registrox->id_detalle;
            $id_articulo = $registrox->id_articulo;
            $cant_despachar = isset($_POST[$id_detalle]) ? intval($_POST[$id_detalle]) : 0;
            // Actualizar detalle
            $consultad = "UPDATE alm_solicitudes_detalle SET cant_despachada=$cant_despachar, usuario='" . $_SESSION['CEDULA_USUARIO'] . "' WHERE id_detalle=$id_detalle;";
            $con->query($consultad);
            // Rebajar inventario
            $consultai = "UPDATE alm_inventario SET cantidad=cantidad-$cant_despachar, usuario='" . $_SESSION['CEDULA_USUARIO'] . "' WHERE id_articulo=$id_articulo;";
            $con->query($consultai);
        }
    }
    exit;
}
exit;
