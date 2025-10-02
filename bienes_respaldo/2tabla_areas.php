<?php
session_start();
include_once "../conexion.php";

if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo '<div class="alert alert-danger">Error de conexión a la base de datos.</div>';
    exit();
}
$mysqli = $_SESSION['conexionsqli'];

// Filtros obligatorios
$id_sector = isset($_POST['filtro_sector']) ? intval($_POST['filtro_sector']) : 0;
$id_division = isset($_POST['filtro_division']) ? intval($_POST['filtro_division']) : 0;

$where = "a.borrado=0";
$params = [];
$types = "";

if ($id_sector > 0) {
    $where .= " AND d.id_sector = ?";
    $params[] = $id_sector;
    $types .= "i";
} else {
    $where .= " AND 1=0";
}
if ($id_division > 0) {
    $where .= " AND a.division = ?";
    $params[] = $id_division;
    $types .= "i";
} else {
    $where .= " AND 1=0";
}


$sql = "SELECT a.id_area, a.descripcion, a.division, d.id_sector, d.descripcion as nombre_division FROM bn_areas a JOIN z_jefes_detalle d ON a.division = d.division WHERE $where ORDER BY a.descripcion";

$stmt = $mysqli->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        // Solución compatible: bind_param dinámico
        $bind_names[] = $types;
        foreach ($params as $key => $value) {
            $bind_names[] = &$params[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<div class="tabla-wrapper" style="max-width: 900px; margin: 0 auto;">
    <div class="TituloSeccionDanger" style="text-align:center;">
        <p class="Estilo3" style="margin: 0; font-size: 1.3em; font-weight: bold; display: inline-block;">
            <i class="fa fa-list" aria-hidden="true"></i> Áreas Registradas
        </p>
    </div>
    <table id="tablaAreas" class="datatabla formateada display" border="1" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px; text-align:center;">Ítem</th>
                <th style="text-align:left;">Descripción</th>
                <th style="text-align:center;">División</th>
                <th style="width:120px; text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $i++;
                    echo '<tr id="fila' . $row['id_area'] . '">';
                    echo '<td align="center">' . $i . '</td>';
                    echo '<td>' . htmlspecialchars($row['descripcion']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($row['nombre_division']) . '</td>';
                    echo '<td align="center">';
                    echo '<button type="button" class="btn btn-primary btn-sm me-1" title="Editar" aria-label="Editar" onclick="editarArea(' . $row['id_area'] . ')"><i class="fa fa-pencil-alt" aria-hidden="true"></i></button>';
                    echo '<button type="button" class="btn btn-danger btn-sm" title="Eliminar" aria-label="Eliminar" onclick="eliminarArea(' . $row['id_area'] . ')"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>
<script src="../lib/datatable.js"></script>
<script>
    function editarArea(id_area) {
        $.getJSON('2guardar_area.php', { accion: 'consultar', id: id_area }, function (data) {
            if (data && data.id_area) {
                $("#ID_AREA").val(data.id_area);
                $("#OAREA").val(data.descripcion);
                $("#ODIVISION").val(data.division).trigger('change');
                setTimeout(function () {
                    $("#OSECTOR").val(data.id_sector);
                }, 200);
                $("#modalTitulo").html('<i class="fa fa-edit me-2"></i> Editar Área');
                limpiarValidacion();
                var modal = document.getElementById('modalArea');
                if (window.bootstrap && bootstrap.Modal) {
                    var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                    modalInstance.show();
                } else {
                    $("#modalArea").addClass('show').css('display', 'flex');
                }
            } else {
                Swal.fire('Error', 'No se pudo obtener la información del área.', 'error');
            }
        });
    }
</script>