<?php
session_start();
include "../conexion.php";

if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}

$acceso = 162; // You might want to define a new access code for this report
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Artículos Despachados</title>
    <?php include "../funciones/headNew.php"; ?>
    <style>
        body {
            background: transparent !important;
        }
    </style>
</head>

<body>
    <div class="mx-auto d-block mt-4" style="width:85%;max-width:1200px;">
        <div class="card border-danger p-0 m-0">
            <div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
                <p class="Estilo3" style="margin:0;font-size:1.15rem;">
                    <i class="fa-solid fa-truck"></i> <strong>Reporte de Artículos Despachados</strong>
                </p>
            </div>
            <div class="card-body p-2">
                <form id="filtrosForm" name="form1" method="post" autocomplete="off">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label for="txt_division" class="fw-bold">División:</label>
                            <select name="txt_division" id="txt_division" class="form-select form-select-sm"
                                onChange="ver()">
                                <option value="-1">-------- Seleccione --------</option>
                                <?php
                                if ($_SESSION['DIVISION_USUARIO'] == 9) {
                                    echo '<option value="0" selected>-------- TODAS --------</option>';
                                    $consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud GROUP BY descripcion ORDER BY descripcion;';
                                    $tabla_x = $_SESSION['conexionsqli']->query($consulta_x);
                                } else {
                                    $consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud WHERE division=? GROUP BY descripcion ORDER BY descripcion;';
                                    $stmt = $_SESSION['conexionsqli']->prepare($consulta_x);
                                    $stmt->bind_param("i", $_SESSION['DIVISION_USUARIO']);
                                    $stmt->execute();
                                    $tabla_x = $stmt->get_result();
                                }
                                while ($registro_x = $tabla_x->fetch_object()) {
                                    echo '<option value="';
                                    echo $registro_x->division;
                                    echo '">';
                                    echo $registro_x->descripcion;
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="txt_fecha_desde" class="fw-bold">Desde:</label>
                            <input type="date" name="txt_fecha_desde" id="txt_fecha_desde"
                                class="form-control form-control-sm" onChange="ver()">
                        </div>
                        <div class="col-md-4">
                            <label for="txt_fecha_hasta" class="fw-bold">Hasta:</label>
                            <input type="date" name="txt_fecha_hasta" id="txt_fecha_hasta"
                                class="form-control form-control-sm" onChange="ver()">
                        </div>
                    </div>
                </form>
                <div id="div1" class="mt-2">
                </div>
            </div>
        </div>
    </div>

    <?php include "../funciones/footNew.php"; ?>
    <script language="JavaScript" src="../lib/datatable.js"></script>
    <script language="JavaScript">
        function ver() {
            var division = document.getElementById("txt_division").value;
            var fecha_desde = document.getElementById("txt_fecha_desde").value;
            var fecha_hasta = document.getElementById("txt_fecha_hasta").value;

            if (division != -1 && fecha_desde != "" && fecha_hasta != "") {
                $('#div1').load('16despachos_tabla.php?division=' + division + '&fecha_desde=' + fecha_desde + '&fecha_hasta=' + fecha_hasta);
            }
        }

        // Establecer valores por defecto: División = TODAS (si existe),
        // fechas desde lunes de esta semana hasta hoy, y cargar reporte
        document.addEventListener('DOMContentLoaded', function () {
            var sel = document.getElementById('txt_division');
            if (sel) {
                // Si existe la opción "0" (TODAS), usarla; si no, tomar la primera distinta de -1
                var optAll = sel.querySelector('option[value="0"]');
                if (optAll) {
                    sel.value = '0';
                } else {
                    for (var i = 0; i < sel.options.length; i++) {
                        if (sel.options[i].value !== '-1') { sel.value = sel.options[i].value; break; }
                    }
                }
            }

            // Calcular lunes de la semana actual y hoy
            var hoy = new Date();
            var dia = hoy.getDay(); // 0=Dom,1=Lun,...,6=Sab
            var diffALunes = (dia === 0) ? -6 : (1 - dia); // Si domingo, retrocede 6; si no, 1 - dia
            var lunes = new Date(hoy);
            lunes.setDate(hoy.getDate() + diffALunes);

            function pad(n) { return (n < 10 ? '0' + n : n); }
            function aISO(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }

            var desde = document.getElementById('txt_fecha_desde');
            var hasta = document.getElementById('txt_fecha_hasta');
            if (desde) desde.value = aISO(lunes);
            if (hasta) hasta.value = aISO(hoy);

            // Cargar tabla por defecto
            ver();
        });
    </script>
</body>

</html>