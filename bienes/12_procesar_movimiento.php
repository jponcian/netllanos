<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

header('Content-Type: application/json');

if ($_POST['CMDAPROBAR'] !== 'Enviar Movimiento') {
    echo json_encode(['status' => 'error', 'msg' => 'Acción inválida']);
    exit();
}

$usuario = $_SESSION['CEDULA_USUARIO'];
$sede = isset($_POST['OSEDE']) ? intval($_POST['OSEDE']) : 0;
$division = isset($_POST['ODIVISION']) ? intval($_POST['ODIVISION']) : 0;
$bien_ids = isset($_POST['bien']) ? $_POST['bien'] : [];

if ($sede <= 0 || $division <= 0 || empty($bien_ids)) {
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
    exit();
}

$f = 0;
$id_division_actual = $division;
$id_division_destino = 0;

foreach ($bien_ids as $id_bien) {
    $id_bien = intval($id_bien);
    // Buscar info del bien
    $q = "SELECT id_area_actual, id_area_destino, id_division_destino FROM vista_bienes_reasignaciones_pendientes WHERE id_bien = $id_bien LIMIT 1";
    $r = mysqli_query($_SESSION['conexionsqli'], $q);
    $reg = mysqli_fetch_assoc($r);
    if ($reg) {
        $id_area_actual = $reg['id_area_actual'];
        $id_area_destino = $reg['id_area_destino'];
        $id_division_destino = $reg['id_division_destino'];
        // Insertar detalle
        $sql = "INSERT INTO bn_reasignaciones_detalle (id_bien, id_area_anterior, id_area_destino, usuario) VALUES ($id_bien, $id_area_actual, $id_area_destino, '$usuario')";
        mysqli_query($_SESSION['conexionsqli'], $sql);
        // Marcar bien
        $sql = "UPDATE bn_bienes SET por_reasignar=2, usuario='$usuario' WHERE id_bien = $id_bien";
        mysqli_query($_SESSION['conexionsqli'], $sql);
        $f++;
    }
}

if ($f > 0) {
    // Generar la reasignación si hay bienes
    $maximo = 1;
    $q = "SELECT Max(numero)+1 AS maximo FROM bn_reasignaciones WHERE division_actual=$id_division_actual AND anno=year(date(now()))";
    $r = mysqli_query($_SESSION['conexionsqli'], $q);
    if ($reg = mysqli_fetch_assoc($r)) {
        if ($reg['maximo'] > 0)
            $maximo = $reg['maximo'];
    }
    $sql = "INSERT INTO bn_reasignaciones (division_actual, division_destino, anno, numero, fecha, usuario) VALUES ($id_division_actual, $id_division_destino, year(now()), $maximo, now(), '$usuario')";
    mysqli_query($_SESSION['conexionsqli'], $sql);
    $id_reasignacion = mysqli_insert_id($_SESSION['conexionsqli']);
    // Actualizar detalle
    $sql = "UPDATE bn_reasignaciones_detalle SET id_reasignacion=$id_reasignacion WHERE id_reasignacion = 0 AND usuario='$usuario'";
    mysqli_query($_SESSION['conexionsqli'], $sql);
    $message = "$f bien(es) enviado(s) a reasignación correctamente.";
    echo json_encode(['status' => 'ok', 'msg' => $message, 'success' => true, 'count' => $f]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'No se seleccionaron bienes válidos', 'success' => false, 'count' => 0]);
}
