<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "../conexion.php";
include_once "../funciones.php"; // Asegúrate de que las funciones como formato_moneda y palabras estén disponibles

// Asegurarse de que la conexión mysqli esté disponible
if (!isset($_SESSION['conexionsqli']) || !$_SESSION['conexionsqli']) {
    echo '<div class="alert alert-danger">Error de conexión a la base de datos.</div>';
    exit();
}
$mysqli = $_SESSION['conexionsqli'];

// La lógica de eliminación se ha movido a 3eliminar.php

// Filtros para la consulta
$filtro_sede = isset($_POST['filtro_sede']) ? intval($_POST['filtro_sede']) : 0;
$where = "b.borrado = 0";
if ($filtro_sede > 0) {
    $where .= " AND d.id_sector = $filtro_sede";
} else {
    // Si no hay filtro de sede (carga inicial), no se muestra nada.
    $where .= " AND 1 = 0";
}
?>

<div class="tabla-wrapper" style="max-width: 1100px; margin: 0 auto;">
    <div class="TituloSeccionDanger" style="text-align:center;">
        <p class="Estilo3" style="margin: 0; font-size: 1.3em; font-weight: bold; display: inline-block;">
            <i class="fa fa-list" aria-hidden="true"></i> Bienes Nacionales Registrados
        </p>
    </div>
    <table id="tablaBienes" class="datatabla formateada " border="1" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px; text-align:center;">Ítem</th>
                <th style="text-align:center;">Categoría</th>
                <th style="text-align:center;">N° Bien</th>
                <th class="text-start">Descripción</th>
                <th style="text-align:center;">Estado</th>
                <th style="text-align:right;">Valor (Bs.)</th>
                <th class="text-start">Área</th>
                <th style="width:120px; text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT b.id_bien, c.codigo as codigo_cat, b.numero_bien, b.descripcion_bien, b.conservacion, b.valor, a.descripcion as area_desc 
                    FROM bn_bienes b 
                    JOIN bn_categorias c ON b.id_categoria = c.id_categoria 
                    JOIN bn_areas a ON b.id_area = a.id_area 
                    JOIN z_jefes_detalle d ON a.division = d.division 
                    WHERE $where 
                    ORDER BY b.descripcion_bien, b.numero_bien";

            $result = $mysqli->query($sql);
            $i = 0;
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $i++;
                    echo '<tr id="fila' . $row['id_bien'] . '">';
                    echo '<td align="center">' . $i . '</td>';
                    echo '<td>' . htmlspecialchars($row['codigo_cat']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($row['numero_bien']) . '</td>';
                    echo '<td class="text-start">' . htmlspecialchars($row['descripcion_bien']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($row['conservacion']) . '</td>';
                    echo '<td align="right">' . htmlspecialchars($row['valor']) . '</td>';
                    echo '<td class="text-start">' . htmlspecialchars(($row['area_desc'])) . '</td>';
                    echo '<td align="center">';
                    // Botón de editar
                    echo '<button type="button" class="btn btn-primary btn-sm me-1" title="Editar" aria-label="Editar" onclick="editarBien(' . $row['id_bien'] . ')">'
                        . '<i class="fa fa-pencil-alt" aria-hidden="true"></i>'
                        . '</button>';
                    // Botón de eliminar
                    echo '<button type="button" class="btn btn-danger btn-sm" title="Eliminar" aria-label="Eliminar" onclick="eliminarBien(' . $row['id_bien'] . ')">'
                        . '<i class="fa fa-trash" aria-hidden="true"></i>'
                        . '</button>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>
<script language="JavaScript" src="../lib/datatable.js"></script>
<script>
    // Función para editar bien nacional
    function editarBien(id_bien) {
        // Obtener datos del bien por AJAX y abrir el modal en modo edición
        $.getJSON('3guardar.php', { accion: 'consultar', id: id_bien }, function (data) {
            if (data && data.id_bien) {
                // Llenar el formulario del modal con los datos recibidos
                $("#ID_BIEN").val(data.id_bien);
                // Cargar combos en cascada y setear valores
                if (window.parent && window.parent.cargarSedes) {
                    // Si la función está en el padre (por include), úsala
                    window.parent.cargarSedes(function () {
                        $('#OSEDE').val(data.id_sede);
                        cargarDivisiones(data.id_sede, function () {
                            $('#ODIVISION').val(data.id_division);
                            cargarAreas(data.id_division, function () {
                                $('#OAREA').val(data.id_area);
                            }, data.id_area);
                        }, data.id_division);
                    }, data.id_sede);
                } else {
                    // Si está en el mismo contexto
                    cargarSedes(function () {
                        $('#OSEDE').val(data.id_sede);
                        cargarDivisiones(data.id_sede, function () {
                            $('#ODIVISION').val(data.id_division);
                            cargarAreas(data.id_division, function () {
                                $('#OAREA').val(data.id_area);
                            }, data.id_area);
                        }, data.id_division);
                    }, data.id_sede);
                }
                cargarCategorias(function () {
                    $('#OCATEGORIA').val(data.id_categoria);
                }, data.id_categoria);
                $("#OBIEN").val(data.numero_bien);
                $("#ODESCRIPCION").val(data.descripcion_bien);
                $("#OCONSERVACION").val(data.conservacion);
                // Mostrar el valor completo, sin notación científica, hasta 8 decimales
                if (data.valor !== undefined && data.valor !== null) {
                    let val = Number(data.valor);
                    if (!isNaN(val)) {
                        // Si es entero, mostrar sin decimales; si tiene decimales, hasta 8
                        let valStr = (Math.floor(val) === val) ? val.toString() : val.toFixed(8).replace(/0+$/, '').replace(/\.$/, '');
                        $("#OVALOR").val(valStr);
                    } else {
                        $("#OVALOR").val(data.valor);
                    }
                } else {
                    $("#OVALOR").val('');
                }
                $("#modalTitulo").html('<i class="fa fa-edit me-2"></i> Editar Bien Nacional');
                limpiarValidacion();
                // Mostrar el modal
                var modal = document.getElementById('modalBien');
                if (window.bootstrap && bootstrap.Modal) {
                    var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                    modalInstance.show();
                } else {
                    $("#modalBien").addClass('show').css('display', 'flex');
                }
            } else {
                Swal.fire('Error', 'No se pudo obtener la información del bien.', 'error');
            }
        });
    }
</script>