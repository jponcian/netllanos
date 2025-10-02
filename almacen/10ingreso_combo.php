<?php
session_start();
include "../conexion.php";
$mysqli = $_SESSION['conexionsqli'];
?>
<div class="row justify-content-center mb-3">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header bg-danger text-white text-center">
                <h5 class="mb-0"><i class="fa fa-plus-circle me-2"></i>Agregar Artículos al Ingreso</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="OARTICULO" class="form-label fw-bold">Descripción</label>
                        <select class="form-select select2 align-middle" name="OARTICULO" id="OARTICULO"
                            style="width: 100%; min-height:38px; height:38px;">
                            <option value="">Seleccione un artículo</option>
                            <?php
                            $consulta_x = "SELECT id_articulo, descripcion FROM alm_inventario WHERE (numero_bien='' or numero_bien is null) AND id_articulo NOT IN (SELECT id_articulo FROM alm_ingresos_detalle_tmp WHERE usuario=" . $_SESSION['CEDULA_USUARIO'] . ") ORDER BY descripcion;";
                            $tabla_x = $mysqli->query($consulta_x);
                            while ($registro_x = $tabla_x->fetch_object()) {
                                echo '<option value="' . $registro_x->id_articulo . '">' . $registro_x->descripcion . '</option>';
                            }
                            ?>
                        </select>
                        <!-- Mensaje de error eliminado -->
                    </div>
                    <div class="col-md-2">
                        <label for="OPRECIO" class="form-label fw-bold">Precio</label>
                        <input type="text" name="OPRECIO" id="OPRECIO" class="form-control" style="text-align:right"
                            maxlength="10" onkeypress="return soloNumeros(event, true);">
                        <!-- Mensaje de error eliminado -->
                    </div>
                    <div class="col-md-2">
                        <label for="OCANTIDAD" class="form-label fw-bold">Cantidad</label>
                        <input type="number" name="OCANTIDAD" id="OCANTIDAD" class="form-control"
                            style="text-align:center" min="1" value="1">
                        <!-- Mensaje de error eliminado -->
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn btn-danger w-100" name="CMDAGREGAR" onclick="agregar()">
                            <i class="fa fa-plus me-2"></i>Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function soloNumeros(evt, permiteDecimal) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        var a = evt.target.value;
        if (permiteDecimal && charCode == 46 && a.indexOf('.') != -1) {
            return false;
        }
        if (charCode == 46 && permiteDecimal) {
            return true;
        }
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    // Feedback visual en tiempo real para el combo de ingreso de artículos
    document.addEventListener('DOMContentLoaded', function () {
        const articulo = document.getElementById('OARTICULO');
        const precio = document.getElementById('OPRECIO');
        const cantidad = document.getElementById('OCANTIDAD');
        if (articulo) {
            articulo.addEventListener('change', function () {
                if (this.value && this.value !== '') {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        }
        if (precio) {
            precio.addEventListener('input', function () {
                const val = parseFloat(this.value);
                if (!isNaN(val) && val > 0) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        }
        if (cantidad) {
            cantidad.addEventListener('input', function () {
                const val = parseInt(this.value);
                if (!isNaN(val) && val > 0) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        }
    });

    $(document).ready(function () {
        $('#OARTICULO').select2({
            // theme: "bootstrap-5",
            // dropdownParent: $('#OARTICULO').parent()
        });
        // Ajuste visual para igualar altura con los inputs
        setTimeout(function () {
            $('.select2-selection').css({
                'min-height': '38px',
                'height': '38px',
                'padding-top': '4px',
                'padding-bottom': '4px',
                'font-size': '1rem'
            });
        }, 100);
    });
</script>