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
      <th>Solicitud</th>
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
    // CONSULTA DE LAS SOLICITUDES
    $consulta = "SELECT * FROM vista_alm_solicitud WHERE $filtro $filtro2 status=5 ORDER BY id_solicitud DESC;";
    // Usar la conexión de sesión $_SESSION['conexionsqli']
    $tabla = $_SESSION['conexionsqli']->query($consulta);
    //echo $consulta;
    $i = 0;

    while ($registro = $tabla->fetch_object()) {
      $i++;
    ?>
      <tr>
        <td><?php echo $i; ?></td>
        <td><?php echo voltea_fecha($registro->fecha); ?></td>
        <td><?php echo $registro->solicitud; ?></td>
        <td class="text-start"><?php echo $registro->descripcion; ?></td>
        <td class="text-start"><?php echo $registro->funcionario; ?></td>
        <td>
          <button type="button" class="btn btn-outline-danger btn-aprobar-modal" data-url="9anular_autorizacion.php?solicitud=<?php echo $registro->id_solicitud; ?>">Anular</button>
        </td>
        <td>
          <a href="../almacen/formatos/x_solicitud.php?solicitud=<?php echo $registro->id_solicitud; ?>" target="_blank"><i class="fa-regular fa-file-pdf fa-2x"></i></a>
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

    $_SESSION['VARIABLE1'] = $i;

    ?>
  </tbody>
</table>