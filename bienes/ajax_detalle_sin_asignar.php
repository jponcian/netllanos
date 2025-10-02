<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if (!isset($_SESSION['VERIFICADO']) || $_SESSION['VERIFICADO'] != "SI") {
  die("Acceso denegado");
}

if (!isset($_GET['area_id']) || !is_numeric($_GET['area_id'])) {
  die("ID de área no válido.");
}

$id_area = (int)$_GET['area_id'];

// Prevenir acceso a áreas excluidas
if (in_array($id_area, [91, 199])) {
    die("Acceso a esta área no está permitido.");
}

$mysqli = $_SESSION['conexionsqli'];

$bienes = [];
$sql = "SELECT numero_bien, descripcion_bien 
        FROM bn_bienes 
        WHERE id_area = ? 
          AND (inf_ci_asignado IS NULL OR inf_ci_asignado = 0 OR inf_ci_asignado = '')
          AND borrado = 0
        ORDER BY numero_bien";

if ($stmt = $mysqli->prepare($sql)) {
  $stmt->bind_param("i", $id_area);
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
          <td colspan="2" class="text-center text-muted py-4">No se encontraron bienes sin asignar en esta área.</td>
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