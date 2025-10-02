<br>
<table align="center" width="65%" border=1 align=center style="background-color: whitesmoke">
<tbody>
  <tr>
    <td bgcolor="#FF0000" height="40" colspan="10" align="center"><p class="Estilo7"><u>Listado de Movimientos pendientes de Confirmar</u></p></td>
  </tr>
  <tr>
    <?php if ($eliminar == 'SI' or $reasignar=='SI') { ?>
    <th width="44" height=41 ><div align="center" class="Estilo8"><strong>Sel</strong></div></th>
    <?php } ?>
    <th width="54"><div align="center" class="Estilo8"><strong>Item</strong></div></th>
    <th ><div align="center" class="Estilo8"><strong>Categoria</strong></div></th>
    <th ><div align="center" class="Estilo8"><strong>Numero Bien</strong></div></th>
    <th ><div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div></th>
    <th ><div align="center" class="Estilo8"><strong>Divisi&oacute;n Actual</strong></div></th>
    <th ><div align="center" class="Estilo8"><strong>Area Actual</strong></div></th>
    <th ><div align="center" class="Estilo8"><strong>Divisi&oacute;n Destino</strong></div></th>
    <th ><div align="center" class="Estilo8"><strong>Area Destino</strong></div></th>
  </tr>
  <?php

  if ( $sede > 0 ) {
    $filtro = $filtro . ' AND id_sector_actual=' . $sede;
  } else {
    $filtro = $filtro . ' AND id_sector_actual=0';
  }
  if ( $division > 0 ) {
    $filtro = $filtro . ' AND id_division_actual=' . $division;
  } else {
    $filtro = $filtro . ' AND id_division_actual=0';
  }

  //-------- 
  $consulta = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vista_bienes_reasignaciones_pendientes WHERE $filtro AND borrado=0 AND por_reasignar=" . $status . " GROUP BY numero_bien ORDER BY id_area_destino, descripcion_bien, numero_bien";
//  echo $consulta;
  //----------------------- MONTAJE DE LOS DATOS
  $i = 0;

  $tabla = mysql_query( $consulta );

  while ( $registro = mysql_fetch_object( $tabla ) ) {
    $i++;
    ?>
  <tr id="fila<?php echo $i.$registro->id_bien; ?>">
    <?php if ($eliminar == 'SI' or $reasignar=='SI') { ?>
    <td ><div align="center" class="Estilo8 Estilo1">
        <input type="checkbox" name="<?php echo $registro->id_bien; ?>" value="<?php echo $registro->id_bien; ?>" onClick="marcar(this,<?php echo $i.$registro->id_bien; ?>)"/>
      </div></td>
    <?php } ?>
    <td ><div align="center" class="Estilo15"><?php echo $i; ?></div></td>
    <td ><div align="center" class="Estilo15"><?php echo mayuscula($registro->codigo_categoria); ?></div></td>
    <td ><div align="center" class="Estilo15"><?php echo ($registro->numero_bien); ?></div></td>
    <td ><div align="left" class="Estilo15"><?php echo ucfirst($registro->descripcion_bien2); ?></div></td>
    <td ><div align="left" class="Estilo15"><?php echo palabras($registro->division_actual); ?></div></td>
    <td ><div align="left" class="Estilo15"><?php echo palabras($registro->area_actual); ?></div></td>
    <td ><div align="left" class="Estilo15"><?php echo palabras($registro->division_destino); ?></div></td>
    <td ><div align="left" class="Estilo15"><?php echo palabras($registro->area_destino); ?></div></td>
  </tr>
  <?php
  }
  ?>
</tbody>
</table>
</p>
<?php
if ( $eliminar == 'SI'
  and $i > 0 ) {
  ?>
<p align="center">
  <input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />
</p>
<?php
}
?>
