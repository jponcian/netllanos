<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['solicitud']) && $_POST['CMDGUARDAR'] == "Guardar") {
    $id_solicitud = intval($_GET['solicitud']);
    $consulta = "UPDATE alm_solicitudes SET fecha_aprobacion=CURDATE(), status=5, aprobador='" . $_SESSION['CEDULA_USUARIO'] . "', notas='" . $_POST['ONOTAS'] . "', usuario='" . $_SESSION['CEDULA_USUARIO'] . "' WHERE id_solicitud=" . $id_solicitud . ";";
    $_SESSION['conexionsqli']->query($consulta);

    $consultax = "SELECT * FROM vista_alm_detalle_solicitud WHERE id_solicitud=" . $id_solicitud . " ORDER BY descripcion;";
    $tablax = $_SESSION['conexionsqli']->query($consultax);
    if ($tablax) {
        while ($registrox = $tablax->fetch_object()) {
            $cant_aprobada = isset($_POST[$registrox->id_detalle]) ? intval($_POST[$registrox->id_detalle]) : 0;
            $consultai = "UPDATE alm_solicitudes_detalle SET cant_aprobada=$cant_aprobada, usuario='" . $_SESSION['CEDULA_USUARIO'] . "' WHERE id_detalle=" . $registrox->id_detalle . ";";
            $_SESSION['conexionsqli']->query($consultai);
        }
    }
    echo 'Procesada Exitosamente';
    exit;
}
exit;
