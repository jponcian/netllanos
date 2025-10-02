<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
?>
<div class="container" style="max-width:900px; margin-top:10px;">



	<?php
	//--------
	$consulta = "SELECT id_detalle, alm_solicitudes_detalle_tmp.cantidad, descripcion FROM alm_solicitudes_detalle_tmp, alm_inventario WHERE alm_solicitudes_detalle_tmp.id_articulo = alm_inventario.id_articulo AND alm_solicitudes_detalle_tmp.usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
	//----------------------- MONTAJE DE LOS DATOS
	
	$mysqli = $_SESSION['conexionsqli'];
	$tabla = $mysqli->query($consulta);

	if (!$tabla || $tabla->num_rows == 0) {
		?>
		<div class="alert alert-secondary" role="alert">No hay artículos en la solicitud.</div>
		<?php
	} else {
		$i = 0;
		echo '<div class="list-group mt-3">';
		while ($registro = $tabla->fetch_object()) {
			$i++;
			$id_detalle = (int) $registro->id_detalle;
			$cantidad = (int) $registro->cantidad;
			$descripcion = htmlspecialchars(($registro->descripcion));
			echo '<div class="list-group-item sol-item position-relative d-flex flex-column flex-md-row align-items-start align-items-md-center" data-desc="' . strtolower($descripcion) . '">';
			// Botón eliminar arriba a la derecha
			echo '  <button type="button" class="btn2 btn-sm2 btn-outline-danger btn-eliminar-art2 btn-eliminar-art position-absolute" style="top:2px;right:2px;padding:2px 4px;" onclick="eliminar(' . $id_detalle . ')" title="Eliminar"><i class="fa fa-times" style="font-size:0.85rem;"></i></button>';
			// Descripción ocupa aún más espacio y alineada a la izquierda
			echo '  <div class="ms-2 me-auto" style="flex:1 1 100%; text-align:left;">';
			echo '    <div class="fw-bold sol-desc2 text-start" style="font-size:1.00rem;">' . $i . '. ' . $descripcion . '</div>';
			echo '  </div>';
			// cantidad mostrada como div
			echo '  <div class="me-3 text-end" style="min-width:120px;">';
			echo '    <div class="cart-qty-display2" data-id="' . $id_detalle . '" style="width:70px;display:inline-block;text-align:right;">' . $cantidad . '</div>';
			echo '  </div>';
			echo '</div>';
		}
		echo '</div>';
	}
	?>

</div>

<script>
	// Búsqueda en cliente para filtrar elementos ya cargados
	(function ($) {

		$(document).ready(function () {
			$('#obuscar').on('input', function () {
				var q = $(this).val().trim().toLowerCase();
				if (q === '') {
					$('.sol-item').show();
					return;
				}
				$('.sol-item').each(function () {
					var desc = $(this).data('desc') || '';
					if (desc.indexOf(q) !== -1) $(this).show();
					else $(this).hide();
				});

				// la cantidad es de solo lectura (div). No hay edición disponible.
			});
		});
	})(jQuery);
</script>