<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$response = array('success' => false, 'message' => 'Error desconocido.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $f = 0;
    if (!empty($_POST['ODIVISION']) && $_POST['ODIVISION'] > 0) {
        $division_actual_post = $_POST['ODIVISION'];

        // Usamos una consulta preparada para seguridad
        $stmt = mysqli_prepare($_SESSION['conexionsqli'], "SELECT * FROM vista_bienes_reasignaciones_pendientes WHERE id_division_actual = ? AND por_reasignar = 1");
        mysqli_stmt_bind_param($stmt, "i", $division_actual_post);
        mysqli_stmt_execute($stmt);
        $tabla = mysqli_stmt_get_result($stmt);

        $bienes_a_procesar = [];
        $todos_los_registros = [];
        $seleccionados_ids = isset($_POST['bienes']) && is_array($_POST['bienes']) ? array_map('intval', $_POST['bienes']) : [];

        while ($registro = mysqli_fetch_object($tabla)) {
            $todos_los_registros[] = $registro;
            if (!empty($seleccionados_ids) && in_array(intval($registro->id_bien), $seleccionados_ids, true)) {
                $bienes_a_procesar[] = $registro;
            }
        }

        if (!empty($bienes_a_procesar)) {
            $id_division_actual = $bienes_a_procesar[0]->id_division_actual;
            $id_division_destino = $bienes_a_procesar[0]->id_division_destino;

            foreach ($bienes_a_procesar as $registro) {
                // Guardar detalle
                $stmt_detalle = mysqli_prepare($_SESSION['conexionsqli'], "INSERT INTO bn_reasignaciones_detalle (id_bien, id_area_anterior, id_area_destino, usuario) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt_detalle, "iiis", $registro->id_bien, $registro->id_area_actual, $registro->id_area_destino, $_SESSION['CEDULA_USUARIO']);
                mysqli_stmt_execute($stmt_detalle);

                // Marcar bien
                $stmt_marcar = mysqli_prepare($_SESSION['conexionsqli'], "UPDATE bn_bienes SET por_reasignar=2, usuario=? WHERE id_bien = ?");
                mysqli_stmt_bind_param($stmt_marcar, "si", $_SESSION['CEDULA_USUARIO'], $registro->id_bien);
                mysqli_stmt_execute($stmt_marcar);

                $f++;
            }

            // Restaurar los bienes que NO fueron seleccionados: poner por_reasignar=0 y eliminar de bn_bienes_x_reasignar
            foreach ($todos_los_registros as $registro_all) {
                $id_bien_all = intval($registro_all->id_bien);
                if (!in_array($id_bien_all, $seleccionados_ids, true)) {
                    // Actualizar bn_bienes
                    $stmt_reset = mysqli_prepare($_SESSION['conexionsqli'], "UPDATE bn_bienes SET por_reasignar=0, usuario=? WHERE id_bien = ?");
                    mysqli_stmt_bind_param($stmt_reset, "si", $_SESSION['CEDULA_USUARIO'], $id_bien_all);
                    mysqli_stmt_execute($stmt_reset);

                    // Eliminar de bn_bienes_x_reasignar
                    $stmt_del = mysqli_prepare($_SESSION['conexionsqli'], "DELETE FROM bn_bienes_x_reasignar WHERE id_bien = ?");
                    mysqli_stmt_bind_param($stmt_del, "i", $id_bien_all);
                    mysqli_stmt_execute($stmt_del);
                }
            }

            // Generar la reasignación
            $maximo = 0;
            if ($id_division_actual != $id_division_destino) {
                $anno_actual = date('Y');
                $stmt_max = mysqli_prepare($_SESSION['conexionsqli'], "SELECT MAX(numero) AS maximo FROM bn_reasignaciones WHERE division_actual = ? AND anno = ?");
                mysqli_stmt_bind_param($stmt_max, "is", $id_division_actual, $anno_actual);
                mysqli_stmt_execute($stmt_max);
                $resultado_max = mysqli_stmt_get_result($stmt_max);
                $registrox = mysqli_fetch_object($resultado_max);
                $maximo = ($registrox && $registrox->maximo > 0) ? $registrox->maximo + 1 : 1;
            }

            // Insertar la reasignación
            $fecha_actual = date('Y-m-d');
            $stmt_insert_reasig = mysqli_prepare($_SESSION['conexionsqli'], "INSERT INTO bn_reasignaciones (division_actual, division_destino, anno, numero, fecha, usuario) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert_reasig, "iiisss", $id_division_actual, $id_division_destino, $anno_actual, $maximo, $fecha_actual, $_SESSION['CEDULA_USUARIO']);
            mysqli_stmt_execute($stmt_insert_reasig);
            $id_reasignacion = mysqli_insert_id($_SESSION['conexionsqli']);

            // Actualizar detalle con el ID de la reasignación
            $stmt_update_detalle = mysqli_prepare($_SESSION['conexionsqli'], "UPDATE bn_reasignaciones_detalle SET id_reasignacion = ? WHERE id_reasignacion = 0 AND usuario = ?");
            mysqli_stmt_bind_param($stmt_update_detalle, "is", $id_reasignacion, $_SESSION['CEDULA_USUARIO']);
            mysqli_stmt_execute($stmt_update_detalle);

            $response['success'] = true;
            $response['message'] = "$f bien(es) enviado(s) a reasignación correctamente.";
        } else {
            $response['message'] = 'No se seleccionaron bienes para procesar.';
        }
    } else {
        $response['message'] = 'Faltan datos para procesar la solicitud.';
    }
} else {
    $response['message'] = 'Método de solicitud no válido.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>