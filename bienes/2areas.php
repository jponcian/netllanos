<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: ../index.php?errorusuario=val");
	exit();
}
$acceso = 74;
include "../validacion_usuario.php";
?>
<html>

<head>
	<?php include "../funciones/headNew.php"; ?>
</head>

<body style="background: transparent !important;">

	<div class="row mb-3" style="max-width: 950px; margin: 15px auto; align-items: flex-end;">
		<div class="col-md-4" style="flex: 0 0 30%; max-width: 30%;">
			<div class="form-group mb-0">
				<select id="filtroSector" class="form-control">
					<option value="0">Seleccione el Sector</option>
					<?php
					$mysqli = $_SESSION['conexionsqli'];
					$resSectores = $mysqli->query("SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5 ORDER BY id_sector");
					while ($row = $resSectores->fetch_assoc()) {
						echo '<option value="' . $row['id_sector'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="col-md-5">
			<div class="form-group mb-0">
				<select id="filtroDivision" class="form-control"></select>
			</div>
		</div>
		<div class="col-md-3 d-flex align-items-end">
			<button type="button" id="btnNuevaArea" class="btn btn-danger btn-lg w-100 fw-bold"
				style="font-size:1.2em; height: 48px;" onclick="abrirModalArea()">
				<i class="fa fa-plus" aria-hidden="true"></i> Nueva Área
			</button>
		</div>
	</div>
	<?php include "../funciones/footNew.php"; ?>

	<div id="divAreas" style="max-width: 950px; margin: 0 auto;">
		<?php include "2tabla_areas.php"; ?>
	</div>

	<?php //include "2modal_area.php"; ?>


	<!-- JS personalizado: mover después de cargar jQuery -->
</body>
<script>
	function cargarFiltroDivisiones(sector_id, selected) {
		if (!sector_id) return;
		$.getJSON('2combo_areas.php?combo=division&id_sector=' + sector_id, function (data) {
			var select = $('#filtroDivision');
			select.empty().append('<option value="0">Seleccione la División</option>');
			$.each(data, function (i, item) {
				select.append($('<option>', { value: item.id, text: item.nombre }));
			});
			if (selected) select.val(selected);
		});
	}
	function recargarTablaAreas() {
		var sector_id = $('#filtroSector').val();
		var division_id = $('#filtroDivision').val();
		if (sector_id > 0 && division_id > 0) {
			$('#divAreas').css('opacity', '0.5');
			$('#divAreas').load('2tabla_areas.php', { filtro_sector: sector_id, filtro_division: division_id }, function () {
				$('#divAreas').css('opacity', '1');
				if ($('#tablaAreas').length) {
					$('#tablaAreas').DataTable();
				}
			});
		} else {
			$('#divAreas').html('<div class="alert alert-info">Seleccione sector y división para ver las áreas.</div>');
		}
	}
	$(document).ready(function () {
		$('#filtroSector').change(function () {
			var sector_id = $(this).val();
			cargarFiltroDivisiones(sector_id);
			$('#filtroDivision').val('0');
			$('#divAreas').html('');
			// Deshabilitar la opción 'Seleccione el Sector' después de seleccionar un sector válido
			if (sector_id !== '0') {
				$('#filtroSector option[value="0"]').prop('disabled', true);
			} else {
				$('#filtroSector option[value="0"]').prop('disabled', false);
			}
		});
		$('#filtroDivision').change(function () {
			recargarTablaAreas();
		});
		// Mover aquí el listener de OSECTOR si existe en el modal
		if ($('#OSECTOR').length) {
			$('#OSECTOR').change(function () {
				var sector_id = $(this).val();
				if (sector_id != '0') {
					if (typeof cargarDivisiones === 'function') {
						cargarDivisiones(sector_id);
					}
					// Deshabilitar la opción 'Seleccione el Sector' en el modal
					$('#OSECTOR option[value="0"]').prop('disabled', true);
				} else {
					$('#ODIVISION').html('<option value="0" disabled selected>Seleccione la División</option>');
					$('#OSECTOR option[value="0"]').prop('disabled', false);
				}
			});
		}
	});
</script>

</html>