<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$reasignar = 'SI';
$eliminar = 'SI';
$status = 1;
$filtro = 'id_division_actual<>id_division_destino';
$sede = isset($_GET['sede1']) ? intval($_GET['sede1']) : 0;
$division = isset($_GET['div1']) ? intval($_GET['div1']) : 0;

if ($sede > 0) {
  $filtro .= ' AND id_sector_actual=' . $sede;
} else {
  $filtro .= ' AND id_sector_actual=0';
}
if ($division > 0) {
  $filtro .= ' AND id_division_actual=' . $division;
} else {
  $filtro .= ' AND id_division_actual=0';
}

$consulta = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vista_bienes_reasignaciones_pendientes WHERE $filtro AND borrado=0 AND por_reasignar=$status ORDER BY id_area_destino, descripcion_bien, numero_bien";
$tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);
$i = 0;
?>
<div role="document" style="width: 80%; margin: 0 auto;">
  <h3 class="text-success" style="text-align:center; margin-bottom:20px;">Bienes pendientes por Reasignar</h3>
  <table class="formateada table table-bordered table-success" width="100%" align="center" style="background-color: #d4edda; border-color: #c3e6cb;">
    <thead>
      <tr>
        <!-- Columna de icono eliminar eliminada -->
        <th width="30">
          <div align="center"><strong>Item</strong></div>
        </th>
        <th>
          <div align="center"><strong>N° Bien</strong></div>
        </th>
        <th>
          <div align="center"><strong>Descripción</strong></div>
        </th>
        <th>
          <div align="center"><strong>División Actual</strong></div>
        </th>
        <th>
          <div align="center"><strong>Área Actual</strong></div>
        </th>
        <th>
          <div align="center"><strong>División Destino</strong></div>
        </th>
        <th>
          <div align="center"><strong>Área Destino</strong></div>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php while ($registro = mysqli_fetch_object($tabla)) {
        $i++; ?>
        <tr id="fila<?php echo $i . $registro->id_bien; ?>" style="cursor:pointer;" onclick="eliminarRegistro('<?php echo $registro->id_bien; ?>', 'fila<?php echo $i . $registro->id_bien; ?>')">
          <!-- Celda de icono eliminar eliminada -->
          <td>
            <div align="center" style="font-size:1.3em;"><?php echo $i; ?></div>
          </td>
          <td>
            <div align="center" style="font-size:1.3em;"><?php echo $registro->numero_bien; ?></div>
          </td>
          <td>
            <div align="left" style="font-size:1.3em;"><?php echo ucfirst($registro->descripcion_bien2); ?></div>
          </td>
          <td>
            <div align="left"><?php echo palabras($registro->division_actual); ?></div>
          </td>
          <td>
            <div align="left"><?php echo palabras($registro->area_actual); ?></div>
          </td>
          <td>
            <div align="left"><?php echo palabras($registro->division_destino); ?></div>
          </td>
          <td>
            <div align="left"><?php echo palabras($registro->area_destino); ?></div>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</div>
<script>
  function eliminarRegistro(idBien, filaId) {
    var formData = new FormData();
    formData.append(idBien, idBien);
    formData.append('CMDELIMINAR', 'Eliminar');
    fetch('4_borrar.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        Swal.fire({
          position: 'bottom-end',
          icon: 'success',
          title: 'Registro eliminado',
          showConfirmButton: false,
          timer: 2000,
          toast: true
        });
        var fila = document.getElementById(filaId);
        if (fila) fila.remove();
        cargar_tabla();
      })
      .catch(error => {
        Swal.fire({
          position: 'bottom-end',
          icon: 'error',
          title: 'Error al eliminar',
          showConfirmButton: false,
          timer: 2000,
          toast: true
        });
      });
  }
</script>

<!-- <script src="../lib/datatable.js"></script> -->