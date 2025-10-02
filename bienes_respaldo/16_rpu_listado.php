<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if (!isset($_SESSION['conexionsqli'])) {
    exit('Error de conexión.');
}
$conn = $_SESSION['conexionsqli'];

$sede = isset($_GET['sede1']) ? intval($_GET['sede1']) : 0;
$division = isset($_GET['div1']) ? intval($_GET['div1']) : 0;

$sql = "SELECT * FROM vbienes_rpu WHERE id_division = ? AND borrado = 0 AND por_reasignar = 0 ORDER BY id_area, inf_ci_asignado, descripcion_bien, numero_bien";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $division);
$stmt->execute();
$result = $stmt->get_result();

$i = 0;
$asignados = 0;
?>

<div class="mb-3">
    <input type="text" id="obuscar" class="form-control" placeholder="Escriba aquí para buscar en la tabla...">
</div>

<table class="table table-bordered table-striped table-hover datatable dt-responsive nowrap" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th class="text-center" style="width: 5%;"><input type="checkbox" id="checkAllBienes"></th>
            <th class="text-center" style="width: 5%;">Item</th>
            <th class="text-center">Número Bien</th>
            <th>Descripción</th>
            <th>Área</th>
            <th>Funcionario Asignado</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            $i++;
            $is_asignado = $row['inf_ci_asignado'] > 0;
            if ($is_asignado) {
                $asignados++;
            }
        ?>
            <tr class="<?= $is_asignado ? 'table-success' : '' ?>">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input check-bien" name="bienes_a_asignar[]" value="<?= $row['id_bien'] ?>">
                </td>
                <td class="text-center"><?= $i ?></td>
                <td class="text-center"><?= htmlspecialchars($row['numero_bien']) ?></td>
                <td><?= htmlspecialchars($row['descripcion_bien']) ?></td>
                <td>
                    <a href="formatos/rpu.php?area=<?= $row['id_area'] ?>" target="_blank">
                        <?= htmlspecialchars(palabras($row['area'])) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars(funcionario($row['inf_ci_asignado'])) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div class="alert alert-info text-center mt-3">
    <strong>EXISTEN <?= $i ?> BIENES REGISTRADOS | <?= $asignados ?> ASIGNADOS | <span class="text-danger"><?= $i - $asignados ?> PENDIENTES</span></strong>
</div>

<script>
    $(document).ready(function() {
        // Re-inicializar datatable si es necesario, o aplicar filtro
        const table = $('.datatable').DataTable();
        $('#obuscar').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Lógica para el checkbox principal
        $('#checkAllBienes').on('click', function() {
            $('.check-bien').prop('checked', this.checked);
        });
    });
</script>
