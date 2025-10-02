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
    <!-- Select2 removed: using native selects -->
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

        /* Alineación y tamaño para Select2 en este formulario */
        /* Aplica al contenedor generado por Select2 adyacente al select original */
        #OFUNCIONARIO.select2-hidden-accessible+.select2-container {
            width: 100% !important;
            float: left !important;
        }

        #OFUNCIONARIO.select2-hidden-accessible+.select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px) !important;
            /* intenta aproximar la altura de .form-select */
            display: flex !important;
            align-items: center !important;
            padding: .375rem .75rem !important;
        }

        #OFUNCIONARIO.select2-hidden-accessible+.select2-container .select2-selection__rendered {
            line-height: normal !important;
            text-align: left !important;
            padding-left: 0 !important;
        }

        /* Tamaños para los selects en este formulario */
        .small-select {
            max-width: 220px;
        }

        .medium-select {
            max-width: 420px;
        }

        .large-select {
            max-width: 620px;
        }

        /* Alinear etiquetas a la izquierda y darles más ancho visual */
        .col-form-label.fw-bold {
            text-align: left;
            padding-right: 1rem;
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
                        <label for="OSEDE" class="col-md-3 col-form-label fw-bold">Dependencia:</label>
                        <div class="col-md-9">
                            <select name="OSEDE" id="OSEDE" class="form-select small-select"
                                onChange="cargar_combo(1,this.value);">
                                <option value="0">-- Seleccione --</option>
                                <?php
                                $consulta_x = "";
                                if ($_SESSION['ADMINISTRADOR'] > 0 || $division == 9) {
                                    $consulta_x = 'SELECT z_sectores.id_sector, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector where z_sectores.id_sector <=5 group by nombre ORDER BY id_sector';
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
                        <label for="ODIVISION" class="col-md-3 col-form-label fw-bold">División:</label>
                        <div class="col-md-9">
                            <select name="ODIVISION" id="ODIVISION" class="form-select medium-select"
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
                        <label for="OFUNCIONARIO" class="col-md-3 col-form-label fw-bold">Funcionario a Asignar:</label>
                        <div class="col-md-9">
                            <select name="OFUNCIONARIO" id="OFUNCIONARIO" class="form-select select2 large-select">
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
            <br>
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

            // Compatibilidad: definir wrappers si el código legacy usa cargar_combo2 / cargar_tabla2
            if (typeof window.cargar_combo2 !== 'function') {
                window.cargar_combo2 = function (tipo, division_id) {
                    const sede_id = $('#OSEDE').val() || 0;
                    if (typeof window.cargar_combo === 'function') {
                        // Llamamos a la implementación moderna reusando la firma (elementId, tipo, sede, division)
                        return cargar_combo('#OFUNCIONARIO', tipo, sede_id, division_id);
                    }
                    console.warn('cargar_combo no está definida; cargar_combo2 fue llamada pero no pudo completar la acción');
                };
            }

            if (typeof window.cargar_tabla2 !== 'function') {
                window.cargar_tabla2 = function () {
                    if (typeof window.cargar_tabla === 'function') {
                        return cargar_tabla();
                    }
                    console.warn('cargar_tabla no está definida; cargar_tabla2 fue llamada pero no pudo completar la acción');
                };
            }
        });

        function cargar_combo(elementId, tipo, sede_id, division_id) {
            // SweetAlert2 toast: mensaje breve de actualización
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'info',
                title: 'Actualizando lista...',
                showConfirmButton: false,
                timer: 1200
            });
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar la lista.'
                    });
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cargar la tabla de bienes.'
                        });
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