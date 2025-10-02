<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//--------
$reasignar = 'SI';
$status = 1;
$filtro = 'id_division_actual<>id_division_destino';
$sede = $_GET['sede'];
$division = $_GET['div'];
// Determinar si el usuario es administrador o pertenece a división 9
$is_admin = (isset($_SESSION['ADMINISTRADOR']) && $_SESSION['ADMINISTRADOR'] > 0) || (isset($_SESSION['DIVISION_USUARIO']) && $_SESSION['DIVISION_USUARIO'] == 9);
$force_checked = !$is_admin;
?>
<div role="document" style="width: 100%; margin: 0 auto;">
	<div align="center">
		<input placeholder="Escriba aqui la informacion a buscar..." name="obuscar" id="obuscar" type="text"
			style="width: 65%;" class="form-control" />
	</div>
	<table class="datatabla formateada" width="100%" border="1" align="center" style="background-color: whitesmoke">
		<thead>
			<tr>
				<?php if ($reasignar == 'SI') { ?>
					<th width="5%" class="text-center">
						<input type="checkbox" onclick="marcarTodos(this);" title="Seleccionar Todos">
					</th>
				<?php } ?>
				<th width="5%" class="text-center">Item</th>
				<th class="text-center">Categoría</th>
				<th class="text-center">Número Bien</th>
				<th>Descripción</th>
				<!-- <th>División Actual</th> -->
				<th>Área Actual</th>
				<th>División Destino</th>
				<th>Área Destino</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($sede > 0) {
				$filtro = $filtro . ' AND id_sector_actual=' . $sede;
			} else {
				$filtro = $filtro . ' AND id_sector_actual=0';
			}
			if ($division > 0) {
				$filtro = $filtro . ' AND id_division_actual=' . $division;
			} else {
				$filtro = $filtro . ' AND id_division_actual=0';
			}

			$consulta = "SELECT *, lower(descripcion_bien) as descripcion_bien2 FROM vista_bienes_reasignaciones_pendientes WHERE $filtro AND borrado=0 AND por_reasignar=" . $status . " ORDER BY id_area_destino, descripcion_bien, numero_bien";
			$i = 0;
			$tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);

			while ($registro = mysqli_fetch_object($tabla)) {
				$i++;
				?>
				<tr id="fila<?php echo $i . $registro->id_bien; ?>">
					<?php if ($reasignar == 'SI') { ?>
						<td class="text-center">
							<?php $checkedAttr = $force_checked ? 'checked' : ''; ?>
							<input type="checkbox" name="bienes[]" value="<?php echo $registro->id_bien; ?>" <?php echo $checkedAttr; ?> onclick="marcar(this, '<?php echo $i . $registro->id_bien; ?>')">
						</td>
					<?php } ?>
					<td class="text-center"><?php echo $i; ?></td>
					<td class="text-center"><?php echo mayuscula($registro->codigo_categoria); ?></td>
					<td class="text-center"><?php echo ($registro->numero_bien); ?></td>
					<td class="text-start"><?php echo ucfirst($registro->descripcion_bien2); ?></td>
					<!-- <td><?php //echo palabras($registro->division_actual); ?></td> -->
					<td class="text-start"><?php echo palabras($registro->area_actual); ?></td>
					<td class="text-start"><?php echo palabras($registro->division_destino); ?></td>
					<td class="text-start"><?php echo palabras($registro->area_destino); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>

<?php if ($division > 0 && $i > 0) { ?>
	<p align="center" class="my-3">
		<input type="submit" class="btn btn-danger" name="CMDAPROBAR" value="Enviar Reasignación" />
	</p>
<?php } ?>

<script src="../lib/datatable.js"></script>

<script>
	function marcar(checkbox, idFila) {
		var fila = document.getElementById('fila' + idFila);
		if (checkbox.checked) {
			fila.style.backgroundColor = '#FFDDC2'; // Un color suave para resaltar
			fila.style.fontWeight = 'bold';
		} else {
			fila.style.backgroundColor = '';
			fila.style.fontWeight = 'normal';
		}
	}

	function marcarTodos(source) {
		let checkboxes = document.getElementsByName('bienes[]');
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i] != source) {
				checkboxes[i].checked = source.checked;
				marcar(checkboxes[i], checkboxes[i].getAttribute('value') ? (i + 1) + checkboxes[i].value : '');
				// Disparar evento change para que los listeners delegados detecten el cambio
				if (typeof jQuery !== 'undefined') {
					jQuery(checkboxes[i]).trigger('change');
				} else {
					var evt = document.createEvent('HTMLEvents');
					evt.initEvent('change', true, false);
					checkboxes[i].dispatchEvent(evt);
				}
			}
		}
		// Actualizar el estado del botón enviar (si la función existe en la página contenedora)
		if (typeof updateEnviarButton === 'function') updateEnviarButton();
	}
</script>