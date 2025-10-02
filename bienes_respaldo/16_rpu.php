<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}

$acceso = 165;
include "../validacion_usuario.php";

// Using list() for PHP < 8.0, or array destructuring for PHP >= 8.0
// Assuming PHP < 8.0 based on the original code's style
list($funcionario, $cargo1, $cargo2, $division) = funcion_funcionario($_SESSION['CEDULA_USUARIO']);

// Using mysqli for modern PHP, assuming $_SESSION['conexionsqli'] is a mysqli connection object
$conn = $_SESSION['conexionsqli'];
?>
<html>

<head>
    <?php include "../funciones/head.php"; ?>
    <title>Asignaci&oacute;n de Bienes Nacionales (RPU)</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body {
            background: transparent !important;
        }

        .card-header.bg-danger {
            background-color: #dc3545 !important;
            opacity: 1 !important;
            color: #fff !important;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>

<body style="background: transparent !important;">
    <div class="container mt-4">
        <?php include "menu.php"; ?>
        <form name="form1" id="formAsignacion" method="post">
            <div class="card mt-4">
                <div class="card-header bg-danger text-white text-center">
                    <h4>Asignaci&oacute;n de Bienes Nacionales (RPU)</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label for="OSEDE" class="col-md-2 col-form-label fw-bold">Dependencia:</label>
                        <div class="col-md-10">
                            <select name="OSEDE" id="OSEDE" class="form-select" onChange="cargar_combo(1,this.value);">
                                <option value="0">-- Seleccione --</option>
                                <?php
                                $consulta_x = "";
                                if ($_SESSION['ADMINISTRADOR'] > 0 || $division == 9) {
                                    $consulta_x = 'SELECT z_sectores.id_sector, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector group by nombre ORDER BY id_sector';
                                } else {
                                    $consulta_x = 'SELECT z_sectores.id_sector, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_sectores.id_sector=' . $_SESSION['SEDE_USUARIO'] . ' group by nombre ORDER BY id_sector';
                                }
                                $tabla_x = mysql_query($consulta_x);
                                while ($registro_x = mysql_fetch_array($tabla_x)) {
                                    echo '<option ';
                                    if ($_POST['OSEDE'] == $registro_x['id_sector']) {
                                        echo 'selected="selected" ';
                                    }
                                    echo ' value=' . $registro_x['id_sector'] . '>' . htmlspecialchars($registro_x['nombre']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="ODIVISION" class="col-md-2 col-form-label fw-bold">División:</label>
                        <div class="col-md-10">
                            <select name="ODIVISION" id="ODIVISION" class="form-select"
                                onChange="cargar_combo2(3,this.value); cargar_tabla2();">
                                <option value="0">-- Seleccione --</option>
                                <?php
                                $consulta_x = '';
                                if ($_SESSION['ADMINISTRADOR'] > 0 || $division == 9) {
                                    $consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = ' . $_POST['OSEDE'] . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
                                } else {
                                    $consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = ' . $_POST['OSEDE'] . ' and z_jefes_detalle.division = ' . $_SESSION['DIVISION_USUARIO'] . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
                                }
                                $tabla_x = mysql_query($consulta_x);
                                while ($registro_x = mysql_fetch_array($tabla_x)) {
                                    echo '<option ';
                                    if ($_POST['ODIVISION'] == $registro_x['division']) {
                                        echo 'selected="selected" ';
                                    }
                                    echo ' value=' . $registro_x['division'] . '>' . htmlspecialchars($registro_x['descripcion']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="OFUNCIONARIO" class="col-md-2 col-form-label fw-bold">Funcionario a Asignar:</label>
                        <div class="col-md-10">
                            <select name="OFUNCIONARIO" id="OFUNCIONARIO" class="form-select select2"
                                style="width: 100%;">
                                <option value="0">-- Seleccione --</option>
                                <?php
                                $consulta_x = "SELECT * FROM z_empleados WHERE sector=" . $_POST['OSEDE'] . " AND division=" . $_POST['ODIVISION'] . " ORDER BY Nombres";
                                $tabla_x = mysql_query($consulta_x);
                                while ($registro_x = mysql_fetch_array($tabla_x)) {
                                    echo '<option ';
                                    if ($_POST['OFUNCIONARIO'] == $registro_x['cedula']) {
                                        echo 'selected="selected" ';
                                    }
                                    echo ' value=' . $registro_x['cedula'] . '>' . htmlspecialchars($registro_x['Nombres'] . " " . $registro_x['Apellidos']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div id="div2">
                <div class="alert alert-secondary text-center">Seleccione una dependencia y división para ver los
                    bienes.</div>
            </div>

            <div class="text-center mt-3">
                <button type="button" id="btnAsignar" class="btn btn-danger"><i class="fas fa-check me-2"></i>Asignar
                    Bienes Seleccionados</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $(".select2").select2();

            $('#OSEDE').on('change', function () {
                const sede_id = $(this).val();
                cargar_combo('#ODIVISION', 1, sede_id, 0);
                cargar_tabla();
            });

            $('#ODIVISION').on('change', function () {
                const sede_id = $('#OSEDE').val();
                const division_id = $(this).val();
                cargar_combo('#OFUNCIONARIO', 3, sede_id, division_id);
                cargar_tabla();
            });

            $('#btnAsignar').on('click', function () {
                asignarBienes();
            });

            // ...existing code...
        });

        function cargar_combo(elementId, tipo, sede_id, division_id) {
            alertify.message("Actualizando lista...");
            $.ajax({
                type: "POST",
                url: `16_combo.php?sede=${sede_id}&division=${division_id}`,
                data: { tipo: tipo },
                success: function (resp) {
                    $(elementId).html(resp).trigger('change.select2');
                    if (elementId == '#ODIVISION') {
                        // Cargar funcionarios después de cargar divisiones
                        cargar_combo('#OFUNCIONARIO', 3, sede_id, $(elementId).val());
                    }
                },
                error: function () {
                    alertify.error('Error al cargar la lista.');
                }
            });
        }

        function cargar_tabla() {
            const sede = $('#OSEDE').val();
            const division = $('#ODIVISION').val();

            if (sede > 0 && division > 0) {
                $('#div2').html('<div class="alert alert-info text-center">Cargando bienes...</div>');
                $('#div2').load(`16_rpu_listado.php?sede1=${sede}&div1=${division}`, function (response, status, xhr) {
                    if (status == "error") {
                        alertify.error("Error al cargar la tabla de bienes.");
                        $('#div2').html('<div class="alert alert-danger text-center">Error al cargar los datos.</div>');
                    }
                });
            } else {
                $('#div2').html('<div class="alert alert-secondary text-center">Seleccione una dependencia y división para ver los bienes.</div>');
            }
        }

        function asignarBienes() {
            const funcionario = $('#OFUNCIONARIO').val();
            if (funcionario <= 0) {
                Swal.fire('Atención', 'Debe seleccionar un funcionario para poder asignar los bienes.', 'warning');
                return;
            }

            const bienes = $("input[name='bienes_a_asignar[]']:checked").map(function () {
                return $(this).val();
            }).get();

            if (bienes.length === 0) {
                Swal.fire('Atención', 'Debe seleccionar al menos un bien para asignar.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('OFUNCIONARIO', funcionario);
            bienes.forEach(bien => formData.append('bienes[]', bien));

            fetch('16_asignar_bienes.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('¡Éxito!', data.message, 'success');
                        cargar_tabla(); // Recargar la tabla para ver los cambios
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un problema de comunicación con el servidor.', 'error');
                });
        }

    </script>
</body>

</html>