<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
    http_response_code(403);
    exit("Acceso denegado.");
}

// Necesario para obtener los permisos de $eliminar y $reasignar
$acceso = 77;
include "../validacion_usuario.php";

$mysqli = isset($_SESSION['conexionsqli']) && $_SESSION['conexionsqli'] instanceof mysqli ? $_SESSION['conexionsqli'] : null;
if (!$mysqli) {
    http_response_code(500);
    exit("Error de conexión a la base de datos.");
}

$selSede = isset($_GET['sede']) ? intval($_GET['sede']) : 0;
$selDivision = isset($_GET['division']) ? intval($_GET['division']) : 0;
$selAnno = isset($_GET['anno']) ? intval($_GET['anno']) : 0;

$i = 0;
if ($selSede > 0 && $selDivision > 0 && $selAnno > 0) {
    $filtro = 'id_division_actual<>id_division_destino';
    $filtro .= ' AND id_sector_actual=' . $selSede;
    $filtro .= ' AND id_division_actual=' . $selDivision;
    $filtro .= ' AND anno=' . $selAnno;

    $consulta = "SELECT * FROM vista_bienes_reasignaciones_solicitadas WHERE $filtro GROUP BY anno, numero ORDER BY anno DESC, numero DESC";
    $tabla = mysqli_query($mysqli, $consulta);
    
    while ($registro = mysqli_fetch_object($tabla)) {
        $i++;
?>
    <tr>
        <?php if (isset($eliminar) && ($eliminar == 'SI' or $reasignar == 'SI')) { ?>
            <td class="text-center"><input type="checkbox" name="bienes_sel[]" value="<?php echo $registro->id_bien; ?>" /></td>
        <?php } ?>
        <td class="text-center"><?php echo $i; ?></td>
        <td class="text-center"><?php echo htmlspecialchars($registro->anno); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($registro->numero); ?></td>
        <td class="text-center"><?php echo voltea_fecha($registro->fecha); ?></td>
        <td><?php echo htmlspecialchars(palabras($registro->division_actual)); ?></td>
        <td><?php echo htmlspecialchars(palabras($registro->division_destino)); ?></td>
        <td><?php echo htmlspecialchars(palabras($registro->area_actual)); ?></td>
        <td><?php echo htmlspecialchars(palabras($registro->area_destino)); ?></td>
        <td class="text-center">
            <a href="formatos/x_memorando_reasignacion.php?id=<?php echo $registro->id_reasignacion; ?>" target="_blank" class="btn btn-sm btn-info" title="Memorando"><i class="fas fa-file-alt"></i></a>
            <a href="reportes/x_reasignacion.php?comprobante=31&id=<?php echo $registro->id_reasignacion; ?>" target="_blank" class="btn btn-sm btn-success" title="Entrega"><i class="fas fa-truck"></i></a>
            <a href="reportes/x_reasignacion.php?comprobante=21&id=<?php echo $registro->id_reasignacion; ?>" target="_blank" class="btn btn-sm btn-warning" title="Recepción"><i class="fas fa-check"></i></a>
        </td>
    </tr>
<?php
    } // fin while
}

if ($i === 0) {
     $num_cols = 9;
     if (isset($eliminar) && ($eliminar == 'SI' or $reasignar == 'SI')) { $num_cols = 10; }
     echo '<tr><td colspan="' . $num_cols . '" class="text-center font-italic text-muted py-3">No se encontraron resultados para los filtros seleccionados.</td></tr>';
}
?>
