<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$sede1 = intval($_GET['sede1']);
$div1 = intval($_GET['div1']);

$filtro = "por_reasignar = 2 AND id_sector_actual = $sede1 AND id_division_actual = $div1";
$consulta = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vbienes_pendientes WHERE $filtro AND borrado=0 AND interno=1 ORDER BY id_area_actual, descripcion_bien, numero_bien";
$tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);
$i = 0;
?>
<div role="document" style="width: 80%; margin: 0 auto;">
    <div align="center">
        <input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text"
            style="width: 65%;" class="form-control" />
    </div>
    <table class="datatabla formateada" width="100%" border="1" align="center" style="background-color: whitesmoke">
        <thead>
            <tr>
                <th width="5%" class="text-center"><input type="checkbox" onclick="marcarTodos(this);"
                        title="Seleccionar Todos"></th>
                <th width="5%" class="text-center">Item</th>
                <th class="text-center">Categoría</th>
                <th class="text-center">Número Bien</th>
                <th>Descripción</th>
                <th>Área Actual</th>
                <th>División Destino</th>
                <th>Área Destino</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($registro = mysqli_fetch_object($tabla)) {
                $i++; ?>
                <tr id="fila<?php echo $registro->id_bien; ?>">
                    <td class="text-center">
                        <input type="checkbox" name="bienes[]" value="<?php echo $registro->id_bien; ?>"
                            onclick="marcar(this, '<?php echo $registro->id_bien; ?>')">
                    </td>
                    <td class="text-center"><?php echo $i; ?></td>
                    <td class="text-center"><?php echo mayuscula($registro->codigo_categoria); ?></td>
                    <td class="text-center"><?php echo $registro->numero_bien; ?></td>
                    <td><?php echo ucfirst($registro->descripcion_bien2); ?></td>
                    <td><?php echo palabras($registro->area_actual); ?></td>
                    <td><?php echo palabras($registro->division_destino); ?></td>
                    <td><?php echo palabras($registro->area_destino); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php if ($i > 0) { ?>
    <div class="text-center my-3">
        <button type="button" class="btn btn-danger" onclick="procesarAccion('aprobar')"><i
                class="fas fa-check me-2"></i>Aprobar Reasignación</button>
        <button type="button" class="btn btn-secondary" onclick="procesarAccion('devolver')"><i
                class="fas fa-undo me-2"></i>Devolver Reasignación</button>
    </div>
<?php } ?>

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
</script>