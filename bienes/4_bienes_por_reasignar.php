<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$reasignar = 'SI';
$division = isset($_GET['div1']) ? intval($_GET['div1']) : (isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0);
$area = isset($_GET['area1']) ? intval($_GET['area1']) : 0;

$filtro = '1=1';
if ($division > 0) {
  $filtro .= ' AND id_division=' . $division;
}
if ($area > 0) {
  $filtro .= ' AND id_area<>' . $area;
}

$consulta = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vista_bienes_nacionales WHERE $filtro AND borrado=0 AND por_reasignar=0 ORDER BY id_area, descripcion_bien, numero_bien";
$tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);
$i = 0;
?>
<div role="document" style="width: 80%; margin: 0 auto;">
  <div align="center">
    <input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text"
      style="width: 65%;" class="form-control" />
  </div>
  <table class="datatabla formateada" width="100%" border="1" align="center" style="background-color: whitesmoke">
    <thead>
      <tr>
        <!-- Columna de icono agregar eliminada -->
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
          <div align="center"><strong>Area</strong></div>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php while ($registro = mysqli_fetch_object($tabla)) {
        $i++; ?>
        <tr id="fila<?php echo $i; ?>" style="cursor:pointer;"
          onclick="reasignarRegistro('<?php echo $registro->id_bien; ?>', 'fila<?php echo $i; ?>')">
          <!-- Celda de icono agregar eliminada -->
          <td>
            <div align="center" style="font-size:1.3em;"><?php echo $i; ?></div>
          </td>

          <td>
            <div align="center" style="font-size:1.3em;"><?php echo palabras($registro->numero_bien); ?></div>
          </td>
          <td>
            <div align="left" style="font-size:1.3em;"><?php echo ucfirst($registro->descripcion_bien2); ?></div>
          </td>
          <td>
            <div align="left"><?php echo palabras($registro->area); ?></div>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <!-- Botón Reasignar removido -->
</div>

<script>
  function reasignarRegistro(idBien, filaId) {
    var formData = new FormData();
    formData.append('id_bien', idBien);
    var area2 = document.getElementById('OAREA2');
    if (area2) {
      formData.append('OAREA2', area2.value);
    }
    formData.append('CMDREASIGNAR', 'Reasignar Bien');
    fetch('4_guardar.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.text())
      .then(data => {
        Swal.fire({
          position: 'bottom-end',
          icon: 'success',
          title: 'Reasignación realizada',
          showConfirmButton: false,
          timer: 2500,
          toast: true
        });
        var fila = document.getElementById(filaId);
        if (fila) fila.remove();
        var inputBuscar = document.getElementById('obuscar');
        if (inputBuscar && inputBuscar.value.trim() !== '') {
          inputBuscar.value = '';
          // intentar usar la función global del padre si existe
          if (window && window.parent && typeof window.parent.cargar_tabla === 'function') {
            try { window.parent.cargar_tabla(); } catch (e) { console.error(e); }
          } else if (typeof cargar_tabla === 'function') {
            try { cargar_tabla(); } catch (e) { console.error(e); }
          }
        }
        // recargar la tabla de reasignados
        if (window && window.parent && typeof window.parent.cargar_tabla2 === 'function') {
          try { window.parent.cargar_tabla2(); } catch (e) { console.error(e); }
        } else if (typeof cargar_tabla2 === 'function') {
          try { cargar_tabla2(); } catch (e) { console.error(e); }
        }
      })
      .catch(error => {
        Swal.fire({
          position: 'bottom-end',
          icon: 'error',
          title: 'Error en la reasignación',
          showConfirmButton: false,
          timer: 2500,
          toast: true
        });
      });
  }
</script>

<script src="../lib/datatable.js"></script>