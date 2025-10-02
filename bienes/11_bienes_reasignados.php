<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$sede = isset($_GET['sede1']) ? intval($_GET['sede1']) : 0;
$division = isset($_GET['div1']) ? intval($_GET['div1']) : 0;
$area = isset($_GET['area1']) ? intval($_GET['area1']) : 0;

if ($sede > 0 && $division > 0 && $area > 0) {
    $sql = "SELECT bn_bienes.*, bn_areas.descripcion AS area_actual, bn_bienes_x_reasignar.fecha, bn_areas_destino.descripcion AS area_destino FROM bn_bienes INNER JOIN bn_areas ON bn_bienes.id_area = bn_areas.id_area INNER JOIN bn_bienes_x_reasignar ON bn_bienes.id_bien = bn_bienes_x_reasignar.id_bien INNER JOIN bn_areas AS bn_areas_destino ON bn_bienes_x_reasignar.id_area_destino = bn_areas_destino.id_area WHERE bn_bienes_x_reasignar.id_area_destino = $area AND bn_bienes.por_reasignar = 1 AND bn_bienes_x_reasignar.interno = 1 ORDER BY bn_bienes_x_reasignar.fecha DESC, bn_bienes.descripcion_bien ASC, bn_bienes.numero_bien ASC";
    $res = mysqli_query($_SESSION['conexionsqli'], $sql);
    $i = 0;
    echo '<div role="document" style="width: 80%; margin: 0 auto;">';
    // Título con estilo usando color 'danger' y fondo elegante para mejorar la legibilidad
    echo '<div class="d-flex justify-content-center my-3">';
    echo '<div class="px-3 py-2 rounded-3 shadow-sm" style="background: linear-gradient(90deg,#b02a37,#dc3545); max-width:900px; width:100%;">';
    echo '<h3 class="fw-bold text-white mb-0" style="font-family: system-ui, -apple-system, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial; font-size:1.15rem; letter-spacing:0.35px;">Bienes pendientes por Reasignar</h3>';
    echo '<p class="text-white-50 small mb-0">Listado de bienes marcados para reasignación interna</p>';
    echo '</div>';
    echo '</div>';
    echo '<table class="formateada table table-bordered table-success" width="100%" align="center" style="background-color: #d4edda; border-color: #c3e6cb;">';
    echo '<thead><tr>';
    echo '<th width="30"><div align="center"><strong>Item</strong></div></th>';
    echo '<th><div align="center"><strong>N° Bien</strong></div></th>';
    echo '<th><div align="center"><strong>Descripción</strong></div></th>';
    echo '<th><div align="center"><strong>Área Actual</strong></div></th>';
    echo '<th><div align="center"><strong>Área Destino</strong></div></th>';
    echo '</tr></thead><tbody>';
    while ($row = mysqli_fetch_assoc($res)) {
        $i++;
        echo '<tr id="fila' . $i . $row['id_bien'] . '" style="cursor:pointer;" onclick="eliminarReasignacion(' . $row['id_bien'] . ', \'fila' . $i . $row['id_bien'] . '\')">';
        echo '<td><div align="center" style="font-size:1.3em;">' . $i . '</div></td>';
        echo '<td><div align="center" style="font-size:1.3em;">' . htmlspecialchars($row['numero_bien']) . '</div></td>';
        echo '<td><div align="left" style="font-size:1.3em;">' . ucfirst(strtolower($row['descripcion_bien'])) . '</div></td>';
        echo '<td><div align="left">' . palabras($row['area_actual']) . '</div></td>';
        echo '<td><div align="left">' . palabras($row['area_destino']) . '</div></td>';
        echo '</tr>';
    }
    if ($i == 0) {
        echo '<tr><td colspan="5" class="text-center text-muted">No hay bienes reasignados.</td></tr>';
    }
    echo '</tbody></table></div>';
    ?>
    <script>
        function eliminarReasignacion(idBien, filaId) {
            // Ejecutar la eliminación sin confirmación
            var formData = new FormData();
            formData.append('id_bien', idBien);
            fetch('11_quitar_reasignacion.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        position: 'bottom-end',
                        icon: data.status === 'ok' ? 'success' : 'error',
                        title: data.msg,
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true
                    });
                    if (data.status === 'ok') {
                        var fila = document.getElementById(filaId);
                        if (fila) fila.remove();
                        if (typeof cargar_tabla === 'function') cargar_tabla();
                        if (typeof cargar_tabla2 === 'function') cargar_tabla2();
                    }
                })
                .catch(() => {
                    Swal.fire({
                        position: 'bottom-end',
                        icon: 'error',
                        title: 'Error al quitar la reasignación',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true
                    });
                });
        }
    </script>
    <?php
} else {
    echo '<div class="alert alert-info">Seleccione Dependencia, División y Área para ver los bienes reasignados.</div>';
}
