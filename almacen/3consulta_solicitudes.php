<?php
session_start();
include "../conexion.php";

if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}

$acceso = 162;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes Realizadas</title>
    <?php include "../funciones/headNew.php"; ?>
    <!-- Si custom-ui.css es relevante para el estilo general, incluirlo aquí -->
    <!-- <link rel="stylesheet" href="css/custom-ui.css?v=1"> -->
    <style>
        /* Estilos personalizados para anular o complementar Bootstrap */
        body {
            background: transparent !important;
        }

        /* Mantener .formateada y .TituloTablaP si se usan en 3z1detalle.php y no son cubiertos por Bootstrap */
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
            <!-- Cambiado a border-primary para un estilo diferente al de anular -->
            <div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
                <p class="Estilo3" style="margin:0;font-size:1.15rem;">
                    <i class="fa-solid fa-clipboard-list"></i> <strong>Solicitudes Registradas</strong>
                    <!-- Icono de ejemplo -->
                </p>
            </div>
            <div class="card-body p-2">
                <form id="filtrosForm" name="form1" method="post" autocomplete="off">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="txt_division" class="fw-bold">División:</label>
                            <select name="txt_division" id="txt_division" class="form-select form-select-sm"
                                onChange="ver()">
                                <option value="-1">-------- Seleccione --------</option>
                                <?php
                                if ($_SESSION['DIVISION_USUARIO'] == 9) {
                                    echo '<option value="0">-------- TODAS --------</option>';
                                    $consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud GROUP BY descripcion;';
                                } else {
                                    $consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud WHERE division=' . $_SESSION['DIVISION_USUARIO'] . ' GROUP BY descripcion;';
                                }
                                //--------------------
                                // Asumiendo que mysql_query sigue siendo usado aquí, aunque 8anular_solicitudes.php usa mysqli
                                $tabla_x = mysql_query($consulta_x);
                                while ($registro_x = mysql_fetch_object($tabla_x)) {
                                    echo '<option value="';
                                    echo $registro_x->division;
                                    echo '">';
                                    echo $registro_x->descripcion;
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="txt_fecha" class="fw-bold">Fecha:</label>
                            <select name="txt_fecha" id="txt_fecha" class="form-select form-select-sm" onChange="ver()">
                                <?php
                                //--------------------
                                $consulta_x = 'SELECT month(fecha) as mes, year(fecha) as anno FROM alm_solicitudes WHERE 1=1 GROUP BY year(fecha), month(fecha) ORDER BY fecha DESC;';
                                // Asumiendo que mysql_query sigue siendo usado aquí
                                $tabla_x = mysql_query($consulta_x);
                                while ($registro_x = mysql_fetch_object($tabla_x)) {
                                    echo '<option';
                                    if (isset($_POST['OMES']) && $_POST['OMES'] == $registro_x->mes . '-' . $registro_x->anno) {
                                        echo ' selected="selected" ';
                                    }
                                    echo ' value="';
                                    echo $registro_x->mes . '-' . $registro_x->anno;
                                    echo '">';
                                    echo $_SESSION['meses_anno'][$registro_x->mes] . ' ' . $registro_x->anno;
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="div1" class="mt-2"> <!-- Añadido mt-2 para un pequeño margen superior -->
                </div>
            </div>
        </div>
    </div>

    <?php include "../funciones/footNew.php"; ?>
    <script language="JavaScript">
        //------------------------------
        function ver() {
            var division = document.getElementById("txt_division").value;
            var fecha = document.getElementById("txt_fecha").value;
            // Se mantiene el .load de jQuery
            $('#div1').load('3z1detalle.php?division=' + division + '&fecha=' + fecha);
            //alertify.success(division+fecha);
        }
    </script>
</body>

</html>