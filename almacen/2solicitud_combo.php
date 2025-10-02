<?php
session_start();
include "../conexion.php";
?>
<div class="row justify-content-center mb-3">
	<div class="col-md-6">
		<div class="card shadow border-0">
			<div class="card-header bg-danger text-white text-center">
				<h5 class="mb-0"><i class="fa fa-plus-circle me-2"></i>Listado de Artículos</h5>
			</div>
			<div class="card-body">
				<div class="row g-3 align-items-end">
					<div class="col-md-7">
						<label for="OARTICULO" class="form-label fw-bold">Artículo</label>
						<select class="form-select select2 py-2" name="OARTICULO" id="OARTICULO"
							style="width: 100%; height: 38px; min-height: 38px;">
							<option value="">Seleccione un artículo</option>
							<?php
							$consulta_x = "SELECT id_articulo, descripcion FROM alm_inventario WHERE cantidad>0 AND (numero_bien='' or numero_bien is null) AND id_articulo NOT IN (SELECT id_articulo FROM alm_solicitudes_detalle_tmp WHERE usuario= " . $_SESSION['CEDULA_USUARIO'] . ") ORDER BY descripcion;";
							$mysqli = $_SESSION['conexionsqli'];
							$tabla_x = $mysqli->query($consulta_x);
							while ($registro_x = $tabla_x->fetch_object()) {
								echo '<option value="' . $registro_x->id_articulo . '">' . htmlspecialchars($registro_x->descripcion) . '</option>';
							}
							?>
						</select>
					</div>
					<div class="col-md-2">
						<label for="OCANTIDAD" class="form-label fw-bold">Cantidad</label>
						<input type="number" min="1" class="form-control text-center" name="OCANTIDAD" id="OCANTIDAD"
							value="1" placeholder="1">
					</div>
					<div class="col-md-3 text-center">
						<button type="button" class="btn btn-danger w-100" onClick="agregar()">
							<i class="fa fa-plus me-2"></i>Agregar
						</button>
					</div>
				</div>
				<div class="mt-3 text-muted small text-center">Seleccione un artículo y la cantidad, luego presione
					<strong>Agregar</strong>.
				</div>
			</div>
		</div>
	</div>
</div>