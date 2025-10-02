<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$info = array();
$tipo = 'info';

$mysqli = $_SESSION['conexionsqli'];
$consulta = "SELECT id_articulo FROM alm_ingresos_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
$tablax = $mysqli->query($consulta);

if ($tablax->num_rows > 0) {
    $division = $_SESSION['DIVISION_USUARIO'];

    $consultax = 'SELECT max(ingreso) as numero FROM alm_ingresos WHERE year(fecha)=year(date(now())) and status<>99;';
    $tablax2 = $mysqli->query($consultax);
    $registrox2 = $tablax2->fetch_object();
    $numero = ($registrox2 && $registrox2->numero) ? $registrox2->numero + 1 : 1;

    $consulta_insert_ingreso = "INSERT INTO alm_ingresos ( ingreso, fecha, division, funcionario, status, usuario ) VALUES (?, NOW(), ?, ?, '0', ?)";
    if ($stmt_ingreso = $mysqli->prepare($consulta_insert_ingreso)){
        $formatted_numero = "0" . $numero;
        $cedula_usuario = $_SESSION['CEDULA_USUARIO'];
        $stmt_ingreso->bind_param("sisi", $formatted_numero, $division, $cedula_usuario, $cedula_usuario);
        $stmt_ingreso->execute();
        $ingreso_id = $stmt_ingreso->insert_id;
        $stmt_ingreso->close();

        $consultay = "SELECT * FROM alm_ingresos_detalle_tmp WHERE usuario= '" . $_SESSION['CEDULA_USUARIO'] . "';";
        $tablay = $mysqli->query($consultay);
        while ($registroy = $tablay->fetch_object()) {
            $consulta_insert_detalle = "INSERT INTO alm_ingresos_detalle (id_ingreso, id_articulo, precio, cantidad, usuario) VALUES (?, ?, ?, ?, ?)";
            if($stmt_detalle = $mysqli->prepare($consulta_insert_detalle)){
                $stmt_detalle->bind_param("iidis", $ingreso_id, $registroy->id_articulo, $registroy->precio, $registroy->cantidad, $_SESSION['CEDULA_USUARIO']);
                $stmt_detalle->execute();
                $stmt_detalle->close();
            }

            $consulta_update_inv = "UPDATE alm_inventario SET precio=?, cantidad=cantidad+?, usuario=? WHERE id_articulo=?";
            if($stmt_update = $mysqli->prepare($consulta_update_inv)){
                $cantidad_update = "0" . $registroy->cantidad;
                $stmt_update->bind_param("disi", $registroy->precio, $cantidad_update, $_SESSION['CEDULA_USUARIO'], $registroy->id_articulo);
                $stmt_update->execute();
                $stmt_update->close();
            }
        }

        $consultad = "DELETE FROM alm_ingresos_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
        $mysqli->query($consultad);

        $mensaje = 'El Ingreso fue Registrado Exitosamente!';
        $info['tipo'] = 'success';
    } else {
        $mensaje = 'Error al preparar la consulta de ingreso.';
        $info['tipo'] = 'alerta';
    }
} else {
    $mensaje = 'No hay Articulos Registrados!';
    $info['tipo'] = 'alerta';
}

$info["msj"] = $mensaje;
echo json_encode($info);
?>
