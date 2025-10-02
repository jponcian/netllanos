<?php
session_start();
include "../conexion.php";
//--------
$info = array();
$tipo = 'info';
//-------------

//PARA GUARDAR
if (trim($_POST['OARTICULO']) <> '' and trim($_POST['OCANTIDAD']) > 0 and trim($_POST['OPRECIO']) > 0) {
    //PARA GUARDAR EL DETALLE
    $consulta = "INSERT INTO alm_ingresos_detalle_tmp ( precio, id_articulo, cantidad, usuario ) SELECT '" . (trim($_POST['OPRECIO'])) . "' AS Expr2, '" . (trim($_POST['OARTICULO'])) . "' AS Expr1, '" . ($_POST['OCANTIDAD']) . "' AS Expr2, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr3;";
    $mysqli = $_SESSION['conexionsqli'];
    $mysqli->query($consulta);
    // MENSAJE DE GUARDADO
    $mensaje = 'El Articulo fue Agregado Exitosamente!';
    //--------------------
} else {
    $mensaje = 'Por favor rellene todos los Campos!';
    $tipo = 'alerta';
    //--------------------
}

$info = array("msj" => $mensaje, "tipo" => $tipo);
echo json_encode($info);
