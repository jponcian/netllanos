<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$consulta = "SELECT alm_ingresos_detalle_tmp.id_detalle, alm_ingresos_detalle_tmp.cantidad, alm_ingresos_detalle_tmp.precio, descripcion FROM alm_ingresos_detalle_tmp, alm_inventario WHERE alm_ingresos_detalle_tmp.id_articulo = alm_inventario.id_articulo AND alm_ingresos_detalle_tmp.usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
$mysqli = $_SESSION['conexionsqli'];
$tabla = $mysqli->query($consulta);

if (!$tabla || $tabla->num_rows == 0) {
?>
    <div class="alert alert-secondary" role="alert">No hay artículos en el ingreso.</div>
<?php
} else {
    $i = 0;
    echo '<div class="list-group mt-3">';
    while ($registro = $tabla->fetch_object()) {
        $i++;
        $id_detalle = (int)$registro->id_detalle;
        $cantidad = (int)$registro->cantidad;
        $precio = (float)$registro->precio;
        $descripcion = htmlspecialchars(($registro->descripcion));

        echo '<div class="list-group-item articulo-item position-relative d-flex flex-column flex-md-row align-items-start align-items-md-center">';
        
        // Botón eliminar
        echo '  <button type="button" class="btn2 btn-sm2 btn-outline-danger btn-eliminar-art2 btn-eliminar-art position-absolute" style="top:2px;right:2px;padding:2px 4px;" onclick="eliminar(' . $id_detalle . ')" title="Eliminar"><i class="fa fa-times" style="font-size:0.85rem;"></i></button>';
        
        // Descripción
        echo '  <div class="ms-2 me-auto" style="flex:1 1 60%; text-align:left;">';
        echo '    <div class="fw-bold text-start">' . $i . '. ' . $descripcion . '</div>';
        echo '  </div>';
        
        // Precio
        echo '  <div class="me-3 text-end" style="min-width:100px;">';
        echo '    <small class="text-muted">Precio:</small> <div>' . number_format($precio, 2, ',', '.') . '</div>';
        echo '  </div>';

        // Cantidad
        echo '  <div class="me-3 text-end" style="min-width:100px;">';
        echo '    <small class="text-muted">Cantidad:</small> <div>' . $cantidad . '</div>';
        echo '  </div>';

        echo '</div>';
    }
    echo '</div>';
}
?>
