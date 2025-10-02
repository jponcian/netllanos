<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

// Endpoint 13: procesar aprobación/devolución de reasignaciones (JSON response)
$response = array('success' => false, 'message' => 'Petición no válida.');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'], $_POST['bienes'])) {
    $accion = $_POST['accion'];
    $bienes = $_POST['bienes'];
    $usuario = $_SESSION['CEDULA_USUARIO'];
    $contador = 0;

    if (empty($bienes)) {
        $response['message'] = 'No se seleccionaron bienes para procesar.';
        echo json_encode($response);
        exit;
    }

    if ($accion == 'aprobar') {
        foreach ($bienes as $id_bien) {
            $id_bien = intval($id_bien);

            // Obtener el área de destino del bien
            $stmt_area = mysqli_prepare($_SESSION['conexionsqli'], "SELECT id_area_destino FROM vista_bienes_reasignaciones_pendientes WHERE id_bien = ?");
            mysqli_stmt_bind_param($stmt_area, "i", $id_bien);
            mysqli_stmt_execute($stmt_area);
            $res_area = mysqli_stmt_get_result($stmt_area);

            if ($reg_area = mysqli_fetch_object($res_area)) {
                // Actualizar el bien a su nueva área
                $stmt_update = mysqli_prepare($_SESSION['conexionsqli'], "UPDATE bn_bienes SET por_reasignar=0, id_area=?, usuario=? WHERE id_bien = ?");
                mysqli_stmt_bind_param($stmt_update, "isi", $reg_area->id_area_destino, $usuario, $id_bien);
                mysqli_stmt_execute($stmt_update);

                // Eliminar de la tabla de pendientes
                $stmt_delete = mysqli_prepare($_SESSION['conexionsqli'], "DELETE FROM bn_bienes_x_reasignar WHERE id_bien = ?");
                mysqli_stmt_bind_param($stmt_delete, "i", $id_bien);
                mysqli_stmt_execute($stmt_delete);

                $contador++;
            }
        }
        $response['success'] = true;
        $response['message'] = "$contador bien(es) ha(n) sido reasignado(s) y aprobado(s) exitosamente.";

    } elseif ($accion == 'devolver') {
        foreach ($bienes as $id_bien) {
            $id_bien = intval($id_bien);

            // Marcar el bien como no pendiente de reasignación
            $stmt_update = mysqli_prepare($_SESSION['conexionsqli'], "UPDATE bn_bienes SET por_reasignar=0, usuario=? WHERE id_bien = ?");
            mysqli_stmt_bind_param($stmt_update, "si", $usuario, $id_bien);
            mysqli_stmt_execute($stmt_update);

            // Marcar como borrado en el detalle de la reasignación
            $stmt_detalle = mysqli_prepare($_SESSION['conexionsqli'], "UPDATE bn_reasignaciones_detalle SET borrado=1, usuario=? WHERE id_bien = ? AND id_reasignacion IN (SELECT id_reasignacion FROM bn_reasignaciones WHERE estado=2)");
            mysqli_stmt_bind_param($stmt_detalle, "si", $usuario, $id_bien);
            mysqli_stmt_execute($stmt_detalle);

            // Eliminar de la tabla de pendientes
            $stmt_delete = mysqli_prepare($_SESSION['conexionsqli'], "DELETE FROM bn_bienes_x_reasignar WHERE id_bien = ?");
            mysqli_stmt_bind_param($stmt_delete, "i", $id_bien);
            mysqli_stmt_execute($stmt_delete);

            $contador++;
        }
        $response['success'] = true;
        $response['message'] = "$contador bien(es) ha(n) sido devuelto(s) a su división de origen.";
    } else {
        $response['message'] = 'Acción no reconocida.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>