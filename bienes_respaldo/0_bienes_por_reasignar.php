<div role="document" style="width: 80%; margin: 0 auto;">

  <div align="center">
    <input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text" style="width: 65%;" class="form-control" />
  </div>
  <table class="datatabla formateada" width="100%" border=1 align=center style="background-color: whitesmoke">
    <thead>
      <tr>
        <?php if ($eliminar == 'SI' or $reasignar == 'SI') { ?>
          <th width="35">
            <div align="center" class="Estilo8"><strong>Sel</strong></div>
          </th>
        <?php } ?>
        <th width="35">
          <div align="center" class="Estilo8"><strong>Item</strong></div>
        </th>
        <th width="91">
          <div align="center" class="Estilo8"><strong>Categoria</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Numero Bien</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Area</strong></div>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php
      $filtro = '1=1';
      if ($division > 0) {
        $filtro = $filtro . ' AND id_division=' . $division;
      } else {
        $filtro = $filtro . ' AND id_division=0';
      }
      if ($area > 0) {
        $filtro = $filtro . ' AND id_area<>' . $area;
      }

      //--------
      $consulta = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vista_bienes_nacionales WHERE $filtro AND borrado=0 AND por_reasignar=0 ORDER BY id_area, descripcion_bien, numero_bien";
      //echo $consulta ;
      //----------------------- MONTAJE DE LOS DATOS
      $i = 0;

      $tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);

      while ($registro = mysqli_fetch_object($tabla)) {
        $i++;
      ?>
        <tr id="fila<?php echo $i; ?>">
          <?php if ($eliminar == 'SI' or $reasignar == 'SI') { ?>
            <td>
              <div align="center" class="Estilo8">
                <input type="checkbox" name="<?php echo $registro->id_bien; ?>" value="<?php echo $registro->id_bien; ?>" onClick="marcar(this,<?php echo $i; ?>)" />
              </div>
            </td>
          <?php } ?>
          <td>
            <div align="center" class="Estilo15"><?php echo $i; ?></div>
          </td>
          <td>
            <div align="center" class="Estilo15"><?php echo mayuscula($registro->codigo); ?></div>
          </td>
          <td>
            <div align="center" class="Estilo15"><?php echo palabras($registro->numero_bien); ?></div>
          </td>
          <td>
            <div align="left" class="Estilo15"><?php echo ucfirst($registro->descripcion_bien2); ?></div>
          </td>
          <td>
            <div align="left" class="Estilo15"><?php echo palabras($registro->area); ?></div>
          </td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
  <p>
</div>