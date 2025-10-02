<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//--------
$reasignar = 'SI';
$status = 2;
//$filtro = 'id_division_actual<>id_division_destino';
$sede = $_GET['sede1'];
$division = $_GET['div1'];
$division2 = $_GET['div2'];
?>
<div role="document" style="width: 80%; margin: 0 auto;">

  <div align="center">
    <input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text" style="width: 65%;" class="form-control" />
  </div>
  <table class="datatabla formateada" width="100%" border=1 align=center style="background-color: whitesmoke">

    <!--
    <tr>
      <td bgcolor="#FF0000" height="40" colspan="10" align="center"><p class="Estilo7"><u>Listado de Reasignaciones pendientes de Aprobar </u></p></td>
    </tr>
-->
    <thead>
      <tr>
        <?php if ($eliminar == 'SI' or $reasignar == 'SI') { ?>
          <th width="44" height=41>
            <div align="center" class="Estilo8"><strong>Sel</strong></div>
          </th>
        <?php } ?>
        <th width="54">
          <div align="center" class="Estilo8"><strong>Item</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Categoria</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Numero Bien</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Divisi&oacute;n Actual</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Area Actual</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Divisi&oacute;n Destino</strong></div>
        </th>
        <th>
          <div align="center" class="Estilo8"><strong>Area Destino</strong></div>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php
      $filtro = '1=1';
      if ($sede > 0) {
        $filtro = $filtro . ' AND id_sector_actual=' . $sede;
      } else {
        $filtro = $filtro . ' AND id_sector_actual=0';
      }
      if ($division > 0) {
        $filtro = $filtro . ' AND id_division_actual=' . $division;
      } else {
        $filtro = $filtro . ' AND id_division_actual=0';
      }
      if ($division2 > 0) {
        $filtro = $filtro . ' AND id_division_destino=' . $division2;
      } else {
        $filtro = $filtro . ' AND id_division_destino=0';
      }

      //-------- 
      $consulta = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vista_bienes_reasignaciones_solicitadas WHERE $filtro AND borrado=0 AND por_reasignar=$status ORDER BY id_area_actual, descripcion_bien, numero_bien";
      //echo $consulta;
      //----------------------- MONTAJE DE LOS DATOS
      $i = 0;

      $tabla = mysql_query($consulta);

      while ($registro = mysql_fetch_object($tabla)) {
        $i++;
      ?>
        <tr id="fila<?php echo $i . $registro->id_bien; ?>">
          <?php if ($eliminar == 'SI' or $reasignar == 'SI') { ?>
            <td>
              <div align="center" class="Estilo8 Estilo1">
                <input type="checkbox" name="<?php echo $registro->id_bien; ?>" value="<?php echo $registro->id_bien; ?>" onClick="marcar(this,<?php echo $i . $registro->id_bien; ?>)" />
              </div>
            </td>
          <?php } ?>
          <td>
            <div align="center" class="Estilo15"><?php echo $i; ?></div>
          </td>
          <td>
            <div align="center" class="Estilo15"><?php echo mayuscula($registro->codigo_categoria); ?></div>
          </td>
          <td>
            <div align="center" class="Estilo15"><?php echo ($registro->numero_bien); ?></div>
          </td>
          <td>
            <div align="left" class="Estilo15"><?php echo ucfirst($registro->descripcion_bien2); ?></div>
          </td>
          <td>
            <div align="left" class="Estilo15"><?php echo palabras($registro->division_actual); ?></div>
          </td>
          <td>
            <div align="left" class="Estilo15"><?php echo palabras($registro->area_actual); ?></div>
          </td>
          <td>
            <div align="left" class="Estilo15"><?php echo palabras($registro->division_destino); ?></div>
          </td>
          <td>
            <div align="left" class="Estilo15"><?php echo palabras($registro->area_destino); ?></div>
          </td>
        </tr>
      <?php
      }
      ?>
    </tbody>

  </table>
  <p>
</div>
</p>
<?php
if (
  $eliminar == 'SI'
  and $i > 0
) {
?>
  <p align="center">
    <input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />
  </p>
<?php
}
?>

</html>
<p></p>
<a name="vista"></a>
<?php if ($division > 0 and $i > 0) { ?>
  <p align="center">
    <input type="submit" class="boton" name="CMDAPROBAR" value="Aprobar Reasignacion" />
    <input type="submit" class="boton" name="CMDDEVOLVER" value="Devolver Reasignacion" />
  </p>
<?php } ?>
<p align="center">&nbsp;</p>
<script language="JavaScript" src="../lib/datatable.js"></script>