<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}

$acceso = 77;
include "../validacion_usuario.php";

// Conexión mysqli desde sesión
$mysqli = isset($_SESSION['conexionsqli']) && $_SESSION['conexionsqli'] instanceof mysqli ? $_SESSION['conexionsqli'] : null;
if (!$mysqli) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

// Estos valores ahora se manejan del lado del cliente, pero los dejamos por si se usan en otros includes
$_SESSION['AREA'] = isset($_POST['OAREA']) ? $_POST['OAREA'] : '';
$_SESSION['DIVISION'] = isset($_POST['ODIVISION']) ? $_POST['ODIVISION'] : '';

$selSede = isset($_POST['OSEDE']) ? intval($_POST['OSEDE']) : 0;
$selDivision = isset($_POST['ODIVISION']) ? intval($_POST['ODIVISION']) : 0;
$selAnno = isset($_POST['OANNO']) ? intval($_POST['OANNO']) : 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Reasignaciones</title>
    <?php include "../funciones/headNew.php"; ?>
</head>

<body style="background: transparent !important;">
    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white text-center">
                        <h5 class="card-title mb-0"><i class="fas fa-filter mr-2"></i> Filtros del Reporte de Reasignaciones</h5>
                    </div>
                    <div class="card-body">
                        <form name="form1" id="form1" method="post">
                            <div class="row align-items-end">
                                <div class="form-group col-md-4">
                                    <label for="OSEDE" class="small font-weight-bold">Dependencia</label>
                                    <select name="OSEDE" id="OSEDE" class="form-control form-control-sm select2">
                                        <option value="0">-- Seleccione --</option>
                                        <?php
                                        $consulta_x = 'SELECT id_sector_actual, sector_actual FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual<>id_division_destino AND numero>0 GROUP BY id_sector_actual ORDER BY id_sector_actual';
                                        $tabla_x = mysqli_query($mysqli, $consulta_x);
                                        while ($registro_x = mysqli_fetch_array($tabla_x)) {
                                            echo '<option value="' . $registro_x['id_sector_actual'] . '">' . htmlspecialchars($registro_x['sector_actual']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="ODIVISION" class="small font-weight-bold">División</label>
                                    <select name="ODIVISION" id="ODIVISION" class="form-control form-control-sm select2">
                                        <option value="0">-- Seleccione --</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="OANNO" class="small font-weight-bold">Año</label>
                                    <select name="OANNO" id="OANNO" class="form-control form-control-sm select2">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-list-ul mr-2"></i> Resultados</h5>
                            <div class="col-md-5">
                                <input placeholder="Buscar en resultados..." name="obuscar" id="obuscar" type="text" class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" id="tablaResultados">
                                <thead class="thead-light">
                                    <tr>
                                        <?php if (isset($eliminar) && ($eliminar == 'SI' or $reasignar == 'SI')) { ?>
                                            <th style="width: 40px;">Sel</th>
                                        <?php } ?>
                                        <th>N°</th>
                                        <th>Año</th>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>División Anterior</th>
                                        <th>División Actual</th>
                                        <th>Área Anterior</th>
                                        <th>Área Actual</th>
                                        <th>Documentos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los resultados se cargarán aquí por AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if (isset($eliminar) && $eliminar == 'SI') { ?>
                            <button type="submit" class="btn btn-danger" name="CMDELIMINAR"><i class="fas fa-trash-alt mr-2"></i>Eliminar Seleccionados</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "../funciones/footNew.php"; ?>
    <script>
        const toast = (icon, msg) => Swal.fire({ position: 'bottom-end', icon, title: msg, showConfirmButton: false, timer: 1500, toast: true });

        function cargar_combo(tipo, val) {
            toast('info', 'Cargando divisiones...');
            $('#ODIVISION').html('<option value="0">-- Seleccione --</option>').trigger('change');
            $('#OANNO').html('<option value="0">-- Todos --</option>').trigger('change');
            
            if (val > 0) {
                $.ajax({
                    type: "POST",
                    url: '8_combo.php?sede=' + val,
                    data: 'tipo=' + tipo,
                    success: function(resp) {
                        $('#ODIVISION').html(resp).trigger('change');
                    }
                });
            } else {
                 clearTable();
            }
        }

        function cargar_combo2(tipo, val) {
            toast('info', 'Cargando años...');
            $('#OANNO').html('<option value="0">-- Todos --</option>').trigger('change');

            if (val > 0) {
                $.ajax({
                    type: "POST",
                    url: '8_combo.php?sede=' + $('#OSEDE').val() + '&division=' + val,
                    data: 'tipo=' + tipo,
                    success: function(resp) {
                        $('#OANNO').html(resp).trigger('change');
                    }
                });
            } else {
                clearTable();
            }
        }

        function cargar_tabla_resultados() {
            const sede = $('#OSEDE').val() || 0;
            const division = $('#ODIVISION').val() || 0;
            const anno = $('#OANNO').val() || 0;

            if (sede > 0 && division > 0 && anno > 0) {
                toast('info', 'Cargando resultados...');
                $.get(`8_tabla_resultados.php?sede=${sede}&division=${division}&anno=${anno}`, function(data) {
                    $('#tablaResultados tbody').html(data);
                });
            } else {
                clearTable();
            }
        }

        function clearTable() {
            const num_cols = $('#tablaResultados thead th').length;
            $('#tablaResultados tbody').html(`<tr><td colspan="${num_cols}" class="text-center font-italic text-muted py-3">Seleccione todos los filtros para mostrar los resultados.</td></tr>`);
        }

        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });

            $('#OSEDE').on('change', function() { cargar_combo(1, $(this).val()); });
            $('#ODIVISION').on('change', function() { cargar_combo2(2, $(this).val()); });
            $('#OANNO').on('change', cargar_tabla_resultados);

            $('#obuscar').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $("#tablaResultados tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            clearTable(); // Estado inicial de la tabla

            <?php
            if (isset($g) && $g == 1) { echo 'toast("success", "Movimiento Realizado!");'; }
            if (isset($g) && $g > 1) { echo 'toast("success", "Movimientos Realizados!");'; }
            if (isset($f) && $f > 0) { echo 'toast("success", "Bien Devuelto Exitosamente!");'; }
            ?>
        });
    </script>
</body>
</html>