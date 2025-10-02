<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}
$acceso = 163;
include "../validacion_usuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresos Registrados</title>
    <?php include "../funciones/headNew.php"; ?>
    <style>
        body {
            background: transparent !important;
        }

        .formateada {
            border-collapse: collapse;
            width: 100%;
        }

        .formateada th,
        .formateada td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .TituloTablaP {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<?php //include "../funciones/mensajes.php"; ?>

<body>
    <?php //include "menu.php"; ?>
    <div class="mx-auto d-block mt-4" style="width:85%;max-width:1200px;">
        <div class="card border-danger p-0 m-0">
            <div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
                <p class="Estilo3" style="margin:0;font-size:1.15rem;">
                    <i class="fa-solid fa-file-invoice"></i> <strong>Ingresos Registrados</strong>
                </p>
            </div>
            <div class="card-body p-2">
                <form id="filtrosForm" name="form1" method="post" autocomplete="off">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-md-6">
                            <label for="OMES" class="fw-bold">Fecha:</label>
                            <select name="OMES" id="OMES" class="form-select form-select-sm" onChange="ver1();">
                                <option value="">--Seleccione--</option>
                                <?php
                                // seleccionar por defecto: si viene por POST usarlo, si no usar mes-año actual
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['OMES'])) {
                                    $selected_fecha = $_POST['OMES'];
                                } else {
                                    $selected_fecha = date('n-Y'); // formato: mes(ej. 9)-Año(ej. 2025)
                                }

                                $consulta_x = 'SELECT month(fecha) as mes, year(fecha) as anno FROM alm_ingresos GROUP BY year(fecha), month(fecha) ORDER BY fecha DESC;';
                                $fechas = array();
                                // Usar la conexión central en sesión (se asume siempre definida)
                                $tabla_x = $_SESSION['conexionsqli']->query($consulta_x);

                                if ($tabla_x) {
                                    while ($registro_x = $tabla_x->fetch_object()) {
                                        $valor = $registro_x->mes . '-' . $registro_x->anno;
                                        $fechas[$valor] = array('mes' => $registro_x->mes, 'anno' => $registro_x->anno);
                                    }
                                }

                                // Asegurar que el mes-anno actual esté en la lista (aunque no haya ingresos)
                                $current_val = date('n') . '-' . date('Y');
                                if (!array_key_exists($current_val, $fechas)) {
                                    list($c_mes, $c_anno) = explode('-', $current_val);
                                    $fechas = array($current_val => array('mes' => intval($c_mes), 'anno' => intval($c_anno))) + $fechas;
                                }

                                // Imprimir opciones en el orden obtenido
                                foreach ($fechas as $valor => $info) {
                                    $sel = ($valor == $selected_fecha) ? ' selected' : '';
                                    echo '<option value="' . $valor . '"' . $sel . '>';
                                    echo $_SESSION['meses_anno'][$info['mes']] . ' ' . $info['anno'];
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="div1" class="mt-2">
                </div>
            </div>
        </div>
    </div>
    <?php include "../funciones/footNew.php"; ?>
    <script>
        function ver1() {
            var fecha = document.getElementById("OMES").value;
            $('#div1').load('11consulta_tabla.php?fecha=' + fecha);
        }
        // auto cargar si hay un valor seleccionado (por defecto el mes-anno actual)
        document.addEventListener('DOMContentLoaded', function () {
            var sel = document.getElementById('OMES');
            if (sel && sel.value) {
                ver1();
            }
        });
    </script>
</body>

</html>