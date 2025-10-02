<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

$sede = isset($_GET['sede']) ? intval($_GET['sede']) : 0;
$division = isset($_GET['division']) ? intval($_GET['division']) : 0;

if ($sede > 0 && $division > 0) {
    $sql = "SELECT * FROM vbienes_pendientes WHERE id_sector_actual = $sede AND id_division_actual = $division AND por_reasignar = 1 AND interno = 1 ORDER BY descripcion_bien, numero_bien";
    $res = mysqli_query($_SESSION['conexionsqli'], $sql);
    echo '<form id="formBienesPendientes">';
    echo '<table class="datatabla formateada" style="border-color:#cfe2ff;"><thead class="thead-light"><tr>';
    echo '<th class="text-center align-middle"><input type="checkbox" id="checkAll" style="accent-color:#dc3545;" onclick="toggleAllChecks(this)"></th>';
    echo '<th>Descripción</th><th>Número</th><th>Área Actual</th><th>Área Destino</th></tr></thead><tbody>';
    $i = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        $i++;
        echo '<tr class="fila-bien">';
        echo '<td><input type="checkbox" name="bien[]" value="' . $row['id_bien'] . '" class="check-bien" style="accent-color:#dc3545;" onchange="resaltarFila(this)"></td>';
        echo '<td class="text-start">' . htmlspecialchars($row['descripcion_bien']) . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row['numero_bien']) . '</td>';
        echo '<td class="text-start">' . palabras($row['area_actual']) . '</td>';
        echo '<td class="text-start">' . palabras($row['area_destino']) . '</td>';
        echo '</tr>';
    }
    if ($i == 0) {
        echo '<tr><td colspan="5" class="text-center text-muted">No hay bienes pendientes.</td></tr>';
    }
    echo '</tbody></table>';
    if ($i > 0) {
        echo '<div class="mt-2 text-center"><button id="btnEnviarMovimiento" type="button" class="btn btn-danger btn-sm" onclick="enviarMovimiento()" disabled>Enviar Movimiento</button></div>';
    }
    echo '</form>';
} else {
    echo '<div class="alert alert-info">Seleccione Dependencia y División para ver los bienes pendientes.</div>';
}
?>
<style>
    .fila-bien.seleccionada {
        background-color: #f8d7da !important;
    }
</style>
<script src="../lib/datatable.js"></script>
<script>
    function resaltarFila(checkbox) {
        var fila = checkbox.closest('tr');
        if (!fila) return;
        if (checkbox.checked) {
            fila.classList.add('seleccionada');
        } else {
            fila.classList.remove('seleccionada');
        }
        updateMasterCheckbox();
        updateEnviarButton();
    }

    // Al seleccionar/deseleccionar todos, resalta todas las filas
    function toggleAllChecks(master) {
        var checks = document.querySelectorAll('.check-bien');
        for (var i = 0; i < checks.length; i++) {
            checks[i].checked = master.checked;
            var fila = checks[i].closest('tr');
            if (master.checked) fila.classList.add('seleccionada'); else fila.classList.remove('seleccionada');
        }
        updateEnviarButton();
    }

    function enviarMovimiento() {
        // Contar bienes seleccionados
        var seleccionados = document.querySelectorAll('.check-bien:checked').length;
        if (seleccionados === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Seleccione al menos un bien',
                text: 'No hay bienes seleccionados para enviar la reasignación.',
                toast: true,
                position: 'bottom-end',
                timer: 2500,
                showConfirmButton: false
            });
            return;
        }

        // Confirmación antes de enviar
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Se enviarán ' + seleccionados + ' bien(es) para reasignación. ¿Desea continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // Construir FormData de forma segura y obtener OSEDE/ODIVISION desde select o inputs inyectados
            var form = document.getElementById('formBienesPendientes');
            var formData = new FormData(form);
            formData.append('CMDAPROBAR', 'Enviar Movimiento');

            function obtenerValorOSEDE() {
                var el = document.getElementById('OSEDE');
                if (el && el.value) return el.value;
                var h = document.querySelector('input[name="OSEDE"]');
                return h ? h.value : '';
            }
            function obtenerValorODIVISION() {
                var el = document.getElementById('ODIVISION');
                if (el && el.value) return el.value;
                var h = document.querySelector('input[name="ODIVISION"]');
                return h ? h.value : '';
            }

            formData.append('OSEDE', obtenerValorOSEDE());
            formData.append('ODIVISION', obtenerValorODIVISION());

            // Deshabilitar botón mientras se procesa
            var btn = document.getElementById('btnEnviarMovimiento');
            if (btn) btn.disabled = true;

            fetch('12_procesar_movimiento.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.msg,
                            icon: 'success'
                        }).then(() => {
                            // Preparar la interfaz para una nueva solicitud.
                            try {
                                if (typeof document.form1 !== 'undefined') {
                                    var f = document.form1;
                                    var sedeEl = f.OSEDE || null;
                                    var divEl = f.ODIVISION || null;
                            if (sedeEl) {
                                        if (sedeEl.disabled) {
                                            // Usuario NO-admin: los selects están fijados. Recargamos la tabla
                                            // para reflejar los bienes restantes después del envío.
                                            if (typeof cargar_tabla === 'function') {
                                                try {
                                                    cargar_tabla();
                                                } catch (e) {
                                                    $('#div1').html('');
                                                }
                                            } else {
                                                $('#div1').html('');
                                            }
                                        } else {
                                            // Usuario admin: limpiar selects y tabla para nueva operación
                                            sedeEl.selectedIndex = 0;
                                            if (divEl) divEl.innerHTML = '<option value="0">Seleccione</option>';
                                            // eliminar inputs inyectados si existieran
                                            try {
                                                $('input.injected[name="OSEDE"], input.injected[name="ODIVISION"]').remove();
                                            } catch (e) {}
                                            $('#div1').html('');
                                        }
                                    } else {
                                        // No existe el form padre: limpiar el fragmento
                                        $('#div1').html('');
                                    }
                                } else {
                                    $('#div1').html('');
                                }
                            } catch (e) {
                                // En caso de cualquier error inesperado, limpiar la tabla para una nueva solicitud
                                $('#div1').html('');
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg,
                            icon: 'error'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error de Comunicación',
                        text: 'No se pudo conectar con el servidor.',
                        icon: 'error'
                    });
                })
                .finally(() => {
                    if (btn) btn.disabled = false;
                });
        });
    }
    // Inicializar: si hay checkboxes ya marcados al cargar (se carga por AJAX), resaltar sus filas
    (function initChecks() {
        var checks = document.querySelectorAll('.check-bien');
        for (var i = 0; i < checks.length; i++) {
            // asegurar que onchange esté conectado (en caso de carga dinámica)
            checks[i].addEventListener('change', function () { resaltarFila(this); });
            if (checks[i].checked) {
                var fila = checks[i].closest('tr'); if (fila) fila.classList.add('seleccionada');
            }
        }
        updateMasterCheckbox();
        updateEnviarButton();
    })();

    function updateMasterCheckbox() {
        var checks = document.querySelectorAll('.check-bien');
        var master = document.getElementById('checkAll');
        if (!master) return;
        if (checks.length === 0) { master.checked = false; master.indeterminate = false; return; }
        var checkedCount = 0;
        for (var i = 0; i < checks.length; i++) if (checks[i].checked) checkedCount++;
        master.checked = (checkedCount === checks.length);
        master.indeterminate = (checkedCount > 0 && checkedCount < checks.length);
    }

    function updateEnviarButton() {
        var checks = document.querySelectorAll('.check-bien');
        var btn = document.getElementById('btnEnviarMovimiento');
        if (!btn) return;
        var any = false;
        for (var i = 0; i < checks.length; i++) { if (checks[i].checked) { any = true; break; } }
        btn.disabled = !any;
    }
</script>