<!-- Modal para Registrar/Editar Área -->
<div id="modalArea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalTitulo" aria-hidden="true"
    style="display:none;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header" style="background:#d32f2f;color:#fff;align-items:center;gap:10px;">
                <h5 class="modal-title fw-bold" id="modalTitulo" style="font-size:1.25rem;letter-spacing:0.5px;">
                    Registrar Área
                </h5>
                <button type="button" class="btn-close btn-close-white ms-auto" aria-label="Cerrar"
                    onclick="cerrarModalArea()"></button>
            </div>
            <div class="modal-body py-3 px-4">
                <div id="formAlertArea" class="alert alert-warning d-none" role="alert"></div>
                <form id="formArea" autocomplete="off">
                    <input type="hidden" id="ID_AREA" name="ID_AREA" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <select id="OSECTOR" name="OSECTOR" class="form-control" required
                                title="Seleccione el Sector">
                                <option value="0" disabled selected>Seleccione el Sector</option>
                                <?php
                                $mysqli = $_SESSION['conexionsqli'];
                                $resSectores = $mysqli->query("SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5 ORDER BY id_sector");
                                while ($row = $resSectores->fetch_assoc()) {
                                    echo '<option value="' . $row['id_sector'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback" id="err_OSECTOR"></div>
                        </div>
                        <div class="col-md-6">
                            <select id="ODIVISION" name="ODIVISION" class="form-control" required
                                title="Seleccione la División">
                                <option value="0" disabled selected>Seleccione la División</option>
                            </select>
                            <div class="invalid-feedback" id="err_ODIVISION"></div>
                        </div>
                        <div class="col-md-12">
                            <input type="text" class="form-control" id="OAREA" name="OAREA" required maxlength="100"
                                placeholder="Descripción del Área">
                            <div class="invalid-feedback" id="err_OAREA"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger px-4" style="background:#d32f2f;border-color:#d32f2f;"
                    onclick="guardarArea()">
                    <i class="fa-solid fa-check me-1"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary px-4" onclick="cerrarModalArea()">
                    <i class="fa fa-times" aria-hidden="true"></i> Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function limpiarValidacionArea() {
        $('#formAlertArea').removeClass('show alert-danger alert-warning').text('');
        $('#formArea .form-control').removeClass('is-invalid is-valid');
        $('.invalid-feedback').removeClass('show').text('');
    }
    function abrirModalArea() {
        $("#formArea")[0].reset();
        $("#ID_AREA").val("");
        $("#modalTitulo").html('<i class="fa fa-cube me-2"></i> Registrar Área');
        limpiarValidacionArea();
        $('#OSECTOR').val('0');
        $('#ODIVISION').html('<option value="0" disabled selected>Seleccione la División</option>');
        var modal = document.getElementById('modalArea');
        if (window.bootstrap && bootstrap.Modal) {
            var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
            modalInstance.show();
        } else {
            $("#modalArea").addClass('show').css('display', 'flex');
        }
    }
    function cerrarModalArea() {
        var modal = document.getElementById('modalArea');
        if (window.bootstrap && bootstrap.Modal) {
            var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
            modalInstance.hide();
        } else {
            $("#modalArea").hide();
        }
    }
    // función cargarSectores eliminada, ya no se usa AJAX para sectores
    function cargarDivisiones(id_sector, selected) {
        if (!id_sector) return;
        $.getJSON('2combo_areas.php?combo=division&id_sector=' + id_sector, function (data) {
            var select = $('#ODIVISION');
            select.empty().append('<option value="0" disabled selected>Seleccione la División</option>');
            $.each(data, function (i, item) {
                select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
            });
            if (selected) select.val(selected);
        });
    }
    $(document).ready(function () {
        $('#OSECTOR').change(function () {
            var sector_id = $(this).val();
            if (sector_id != '0') {
                cargarDivisiones(sector_id);
            } else {
                $('#ODIVISION').html('<option value="0" disabled selected>Seleccione la División</option>');
            }
        });
        $('#ODIVISION').change(function () {
            // No dependientes
        });
    });
    function guardarArea() {
        let ok = true;
        limpiarValidacionArea();
        if (!$('#OSECTOR').val() || $('#OSECTOR').val() == '0') { $('#OSECTOR').addClass('is-invalid'); ok = false; }
        if (!$('#ODIVISION').val() || $('#ODIVISION').val() == '0') { $('#ODIVISION').addClass('is-invalid'); ok = false; }
        if (!$('#OAREA').val().trim()) { $('#OAREA').addClass('is-invalid'); ok = false; }
        if (!ok) {
            $('#formAlertArea').addClass('alert-warning show').text('Por favor corrija los campos marcados.');
            return;
        }
        var datos = $('#formArea').serialize();
        var $btnGuardar = $(".modal-footer .btn-danger");
        var btnHtml = $btnGuardar.html();
        $btnGuardar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        $.ajax({
            url: '2guardar_area.php',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function (response) {
                if (response.tipo === 'exito') {
                    Swal.fire({
                        title: '¡Guardado!',
                        text: response.msj,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        cerrarModalArea();
                        recargarTablaAreas();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.msj,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al conectar con el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            },
            complete: function () {
                $btnGuardar.prop('disabled', false).html(btnHtml);
            }
        });
    }
    function eliminarArea(id_area) {
        Swal.fire({
            title: '¿Está seguro?',
            text: "¡No podrá revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, ¡eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '2eliminar_area.php',
                    type: 'POST',
                    data: { id: id_area },
                    dataType: 'json',
                    success: function (response) {
                        if (response.tipo === 'exito') {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: response.msj,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            recargarTablaAreas();
                        } else {
                            Swal.fire('Error', response.msj, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                    }
                });
            }
        });
    }
</script>