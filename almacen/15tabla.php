<?php
session_start();
include "../conexion.php";
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
  echo '<div class="alert alert-danger">Error de conexión a la base de datos.</div>';
  exit();
}
$mysqli = $_SESSION['conexionsqli'];
?>
<div class="tabla-wrapper" style="max-width: 1100px; margin: 0 auto;">

  <div class="TituloSeccionDanger">
    <p class="Estilo3"><i class="fa fa-list" aria-hidden="true"></i> Artículos Registrados</p>
  </div>
  <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom:10px;">
    <input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text"
      style="width: 65%;" class="form-control" />
    <button type="button" class="btn btn-danger btn-sm" onclick="abrirModalArticulo()">
      <i class="fa fa-plus" aria-hidden="true"></i>
      Agregar Artículo
    </button>
  </div>
  <table id="tablaArticulos" class="datatabla formateada display" border="1" style="width:100%">
    <thead>
      <tr>
        <th style="width:60px; text-align:center;">Num</th>
        <th style="text-align:center;">Categoria</th>
        <th style="text-align:center;">Descripcion</th>
        <th style="width:100px; text-align:center;">Tipo</th>
        <th style="width:120px; text-align:center;">Precio</th>
        <th style="width:120px; text-align:center;">Cantidad</th>
        <th style="width:160px; text-align:center;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT a.id_articulo, a.id_categoria, a.descripcion, a.unidad, a.cantidad, a.precio, c.codigo, c.descripcion as categoria FROM alm_inventario a JOIN bn_categorias c ON a.id_categoria = c.id_categoria ORDER BY a.descripcion;";
      $result = $mysqli->query($sql);
      $i = 0;
      while ($row = $result->fetch_assoc()) {
        $i++;
        echo '<tr id="fila' . $row['id_articulo'] . '">';
        echo '<td align="center">' . $i . '</td>';
        echo '<td>' . htmlspecialchars($row['codigo'] . ' ' . $row['categoria']) . '</td>';
        echo '<td align="left">' . htmlspecialchars($row['descripcion']) . '</td>';
        echo '<td align="center">' . htmlspecialchars($row['unidad']) . '</td>';
        // Formatear el precio con coma como separador decimal
        $precio_formateado = number_format($row['precio'], 2, ',', '.');
        echo '<td align="right">' . htmlspecialchars($precio_formateado) . '</td>';
        echo '<td align="center">' . htmlspecialchars($row['cantidad']) . '</td>';
        echo '<td align="center">';
        echo '<button type="button" class="btn btn-success btn-sm" title="Editar" aria-label="Editar" onclick="abrirModalArticulo(' . $row['id_articulo'] . ')">'
          . '<i class="fa fa-pencil" aria-hidden="true"></i>'
          . '</button> ';
        echo '<button type="button" class="btn btn-danger btn-sm" title="Eliminar" aria-label="Eliminar" onclick="eliminar(' . $row['id_articulo'] . ')">'
          . '<i class="fa fa-trash" aria-hidden="true"></i>'
          . '</button>';
        echo '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>
<br>
<?php //include "../funciones/footNew.php"; ?>
<script language="JavaScript" src="../lib/datatable.js"></script>