<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['ingreso'])
    && isset($_POST['ONOTAS'])
    && trim($_POST['ONOTAS']) !== ''
) {
    $id_ingreso = intval($_GET['ingreso']);
    $usuario = $_SESSION['CEDULA_USUARIO'];
    $notas = $_POST['ONOTAS'];
    $conex = $_SESSION['conexionsqli'];

    $consulta = "UPDATE alm_ingresos SET fecha_anulacion=CURDATE(), status=99, anulador='$usuario', usuario='$usuario', notas='$notas' WHERE id_ingreso=$id_ingreso;";

    $conex->query($consulta);

    $consultax = "SELECT id_articulo, cantidad FROM alm_ingresos_detalle WHERE id_ingreso=$id_ingreso;";
    $tablax = $conex->query($consultax);
    if ($tablax) {
        while ($registrox = $tablax->fetch_object()) {
            $consultai = "UPDATE alm_inventario SET cantidad = cantidad - {$registrox->cantidad}, usuario='$usuario' WHERE id_articulo={$registrox->id_articulo};";
            $conex->query($consultai);
        }
    }
    echo 'Procesada Exitosamente';
    exit;
}
echo 'Error al procesar la anulación';
exit;
