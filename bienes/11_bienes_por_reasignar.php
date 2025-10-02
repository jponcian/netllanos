<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$sede = isset($_GET['sede1']) ? intval($_GET['sede1']) : 0;
$division = isset($_GET['div1']) ? intval($_GET['div1']) : 0;
$area = isset($_GET['area1']) ? intval($_GET['area1']) : 0;


if ($sede > 0 && $division > 0) {
    // Construir consulta: excluir bienes que pertenecen al área destino (si se proporcionó)
    $sedeInt = intval($sede);
    $divisionInt = intval($division);
    $areaInt = intval($area);
    $sql = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vista_bienes_nacionales WHERE id_sector = $sedeInt AND id_division = $divisionInt AND por_reasignar = 0";
    if ($areaInt > 0) {
        $sql .= " AND id_area <> $areaInt";
    }
    $sql .= " ORDER BY id_area, descripcion_bien, numero_bien";
    $res = mysqli_query($_SESSION['conexionsqli'], $sql);
    // Usar más espacio dentro del div contenedor para que la tabla ocupe más ancho
    echo '<div role="document" style="width: 80%; margin: 0 auto;">';
    // Título elegante para la tabla de bienes disponibles (ahora arriba)
    echo '<div class="d-flex justify-content-center my-3">';
    echo '<div class="px-3 py-2 rounded-3 shadow-sm" style="background: linear-gradient(90deg,#b02a37,#dc3545); max-width:900px; width:100%;">';
    echo '<h3 class="fw-bold text-white mb-0" style="font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; font-size:1.15rem; letter-spacing:0.35px;">Bienes disponibles para Reasignar</h3>';
    echo '<p class="text-white-50 small mb-0">Seleccione el bien que desea marcar para reasignación interna</p>';
    echo '</div>';
    echo '</div>';
    // Cuadro de búsqueda inmediatamente debajo del título
    echo '<div align="center" class="mb-2">';
    echo '<input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text" style="width: 90%; max-width:900px;" class="form-control" />';
    echo '</div>';
    echo '<table class="datatabla formateada" width="100%" border="1" align="center" style="background-color: whitesmoke">';
    echo '<thead><tr>';
    echo '<th width="30"><div align="center"><strong>Item</strong></div></th>';
    echo '<th><div align="center"><strong>N° Bien</strong></div></th>';
    echo '<th><div align="center"><strong>Descripción</strong></div></th>';
    echo '<th><div align="center"><strong>Area</strong></div></th>';
    echo '</tr></thead><tbody>';
    $i = 0;
    while ($row = mysqli_fetch_object($res)) {
        $i++;
        echo '<tr id="fila' . $i . '" style="cursor:pointer;" onclick="reasignarRegistro(' . $row->id_bien . ', \'fila' . $i . '\')">';
        echo '<td><div align="center" style="font-size:1.3em;">' . $i . '</div></td>';
        echo '<td><div align="center" style="font-size:1.3em;">' . palabras($row->numero_bien) . '</div></td>';
        echo '<td><div align="left" style="font-size:1.3em;">' . ucfirst($row->descripcion_bien2) . '</div></td>';
        echo '<td><div align="left">' . palabras($row->area) . '</div></td>';
        echo '</tr>';
    }
    if ($i == 0) {
        echo '<tr><td colspan="4" class="text-center text-muted">No hay bienes disponibles.</td></tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-info">Seleccione Dependencia, División y Área para ver los bienes disponibles.</div>';
}

?>
<script>
    function reasignarRegistro(idBien, filaId) {
        var formData = new FormData();
        formData.append('id_bien', idBien);
        var area = document.getElementById('OAREA');
        if (area) {
            formData.append('OAREA', area.value);
        }
        fetch('11_guardar.php', {
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
                    timer: 2500,
                    toast: true
                });
                if (data.status === 'ok') {
                    var fila = document.getElementById(filaId);
                    if (fila) fila.remove();
                    if (typeof cargar_tabla === 'function') cargar_tabla();
                    if (typeof cargar_tabla2 === 'function') cargar_tabla2();
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