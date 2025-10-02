<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//--------------
$division = $_GET['division'];
list($mes, $anno) = explode("-", $_GET['fecha']);
?>
<table class="table table-bordered table-sm align-middle text-center mt-3">
  <thead class="table-danger">
    <tr>
      <th>#</th>
      <th>Fecha</th>
      <th>Número</th>
      <th>División</th>
      <th>Funcionario</th>
      <th>Opción</th>
      <th>Ingreso</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if ($division == 0) {
      $filtro = '';
    } else {
      $filtro = 'division=' . $division . ' AND ';
    }
    if ($_GET['fecha'] == 0) {
      $filtro2 = '';
    } else {
      $filtro2 = "month(fecha)=" . $mes . " and year(fecha)=" . $anno . " AND";
    }
    // CONSULTA DE LOS INGRESOS
    $consulta = "SELECT * FROM vista_alm_ingreso WHERE $filtro $filtro2 status<>99 ORDER BY id_ingreso DESC;";
    $tabla = $_SESSION['conexionsqli']->query($consulta);
    $i = 0;

    while ($registro = $tabla->fetch_object()) {
      $i++;
    ?>
      <tr>
        <td><?php echo $i; ?></td>
        <td><?php echo voltea_fecha($registro->fecha); ?></td>
        <td><?php echo $registro->ingreso; ?></td>
        <td class="text-start"><?php echo $registro->descripcion; ?></td>
        <td class="text-start"><?php echo $registro->funcionario; ?></td>
        <td>
          <button type="button" class="btn btn-outline-danger btn-anular-modal" data-url="12anular_ingreso_modal.php?ingreso=<?php echo $registro->id_ingreso; ?>">Anular</button>
        </td>
        <td>
          <a href="../almacen/formatos/x_ingreso.php?ingreso=<?php echo $registro->id_ingreso; ?>" target="_blank"><i class="fa-regular fa-file-pdf fa-2x"></i></a>
        </td>
      </tr>
    <?php
    }

    if ($i == 0) {
    ?>
      <tr>
        <td colspan="7" class="text-center">No Existe Información</td>
      </tr>
    <?php
    }
    ?>
  </tbody>
</table>
