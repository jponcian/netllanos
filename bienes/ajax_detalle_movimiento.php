<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
  die("Acceso denegado");
}

if (!isset($_GET['reasignacion']) || !is_numeric($_GET['reasignacion'])) {
  die("ID de reasignación no válido.");
}

$id_reasignacion = (int)$_GET['reasignacion'];
$mysqli = $_SESSION['conexionsqli'];

$bienes = [];
$sql = "SELECT numero_bien, descripcion_bien 
        FROM vbienes_reasignaciones 
        WHERE id_reasignacion = ? 
        ORDER BY numero_bien";

if ($stmt = $mysqli->prepare($sql)) {
  $stmt->bind_param("i", $id_reasignacion);
  $stmt->execute();
  $resultado = $stmt->get_result();
  while ($fila = $resultado->fetch_assoc()) {
    $bienes[] = $fila;
  }
  $stmt->close();
}

?>
<div class="table-responsive">
  <table class="table table-sm table-striped table-hover mb-0">
    <thead class="table-light">
      <tr>
        <th>N° Bien</th>
        <th>Descripción</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($bienes)): ?>
        <tr>
          <td colspan="2" class="text-center text-muted py-4">No se encontraron bienes en este movimiento.</td>
        </tr>
      <?php else:
        foreach ($bienes as $bien): ?>
          <tr>
            <td><?php echo htmlspecialchars($bien['numero_bien']); ?></td>
            <td><?php echo htmlspecialchars($bien['descripcion_bien']); ?></td>
          </tr>
        <?php endforeach;
      endif; ?>
    </tbody>
  </table>
</div>