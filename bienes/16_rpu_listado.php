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
<div align="center">
    <input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text"
        style="width: 65%;" class="form-control" />
</div>
<table class="datatabla formateada" width="100%" border="1" align="center" style="background-color: whitesmoke">
    <thead class="table-dark">
        <tr>
            <th class="text-center" style="width: 5%;"><input type="checkbox" id="checkAllBienes"></th>
            <th class="text-center" style="width: 5%">Item</th>
            <th class="text-start">Descripción</th>
            <th class="text-center">Número Bien</th>
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
                    <input type="checkbox" class="form-check-input check-bien" name="bienes_a_asignar[]"
                        value="<?= $row['id_bien'] ?>">
                </td>
                <td class="text-center"><?= $i ?></td>
                <td class="text-start"><?= htmlspecialchars($row['descripcion_bien']) ?></td>
                <td class="text-center"><?= htmlspecialchars($row['numero_bien']) ?></td>
                <td>
                    <a href="formatos/rpu.php?area=<?= $row['id_area'] ?>" target="_blank">
                        <?= htmlspecialchars(palabras($row['area'])) ?>
                    </a>
                </td>
                <td class="text-center">
                    <?php if ($row['inf_ci_asignado'] > 0) { ?>
                        <a href="#" class="quitar-funcionario"
                            data-id="<?= $row['id_bien'] ?>"><?= htmlspecialchars(funcionario($row['inf_ci_asignado'])) ?></a>
                    <?php } else { ?>
                        <span class="text-muted">Sin asignar</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<div class="alert alert-info text-center mt-3">
    <strong>EXISTEN <?= $i ?> BIENES REGISTRADOS | <?= $asignados ?> ASIGNADOS | <span
            class="text-danger"><?= $i - $asignados ?> PENDIENTES</span></strong>
</div>

<script src="../lib/datatable.js"></script>

<script>
    function marcar(checkbox, idFila) {
        var $fila = $('#fila' + idFila);
        if (checkbox.checked) {
            // fondo rojo suave tipo alert-danger
            $fila.css('background-color', '#f8d7da');
            // asegurar texto normal
            $fila.css('font-weight', 'normal');
        } else {
            // quitar estilos inline
            $fila.css('background-color', '');
            $fila.css('font-weight', '');
        }
    }

    function marcarTodos(source) {
        let checkboxes = document.getElementsByName('bienes[]');
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i] != source) {
                checkboxes[i].checked = source.checked;
                marcar(checkboxes[i], checkboxes[i].value);
            }
        }
    }

    // Handler para quitar funcionario asignado
    $(document).on('click', '.quitar-funcionario', function (e) {
        e.preventDefault();
        const idBien = $(this).data('id');
        const $link = $(this);
        Swal.fire({
            title: '¿Eliminar funcionario asignado?',
            text: 'Esta acción eliminara la asignación para este Bien Nacional.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // peticion POST
                fetch('16_quitar_funcionario.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: 'id_bien=' + encodeURIComponent(idBien)
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Eliminado', data.message, 'success');
                            // Recargar la tabla contenedora — asumimos que hay una función cargar_tabla global
                            if (typeof window.cargar_tabla === 'function') {
                                window.cargar_tabla();
                            } else {
                                // fallback: recargar la página
                                location.reload();
                            }
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    }).catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'No se pudo completar la petición.', 'error');
                    });
            }
        });
    });
</script>