<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//--------------
$division = $_GET['division'];
if ($division == '-1') {
    echo '<div class="alert alert-warning">Seleccione una opción para ver las solicitudes.</div>';
    return;
}
if ($_GET['fecha'] == '') {
    echo '<div class="alert alert-warning">Seleccione una fecha para ver los ingresos.</div>';
    return;
}
list($mes, $anno) = explode("-", $_GET['fecha']);
?>
<!-- <table class="table table-striped table-hover table-bordered"> -->
<table class="table table-bordered table-hover align-middle text-center mt-3">
    <thead class="table-danger">
        <tr class="bg-primary text-white"> <!-- Aplicando el estilo de la cabecera del card -->
            <th height="30" class="text-center">#</th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Numero</th>
            <th class="text-center">Division</th>
            <th class="text-center">Funcionario</th>
            <th class="text-center">Status</th>
            <th class="text-center">Solicitud</th>
        </tr>
    </thead>
    <tbody>

        <?php
        if ($division == 0) {
            $filtro = '';
        } else {
            $filtro = 'division=' . $division . ' AND ';
        }
        // CONSULTA DE LAS SOLICITUDES
        $consulta = "SELECT * FROM vista_alm_solicitud WHERE " . $filtro . " month(fecha)=" . $mes . " and year(fecha)=" . $anno . " ORDER BY id_solicitud DESC;";
        $tabla = mysql_query($consulta);
        //echo $consulta;
        
        $i = 0;

        while ($registro = mysql_fetch_object($tabla)) {
            $i++;
            ?>
            <tr>
                <td height=27>
                    <div align="center"><?php echo $i; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo voltea_fecha($registro->fecha); ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $registro->solicitud; ?></div>
                </td>
                <td>
                    <div align="left"><?php echo $registro->descripcion; ?></div>
                </td>
                <td>
                    <div align="left"><?php echo $registro->funcionario; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo status_almacen($registro->status); ?></div>
                </td>
                <td>
                    <a href="../almacen/formatos/x_solicitud.php?solicitud=<?php echo $registro->id_solicitud; ?>"
                        target="_blank"><i class="fa-regular fa-file-pdf fa-2x"></i></a>
                </td>
            </tr>
            <?php
        }
        $_SESSION['VARIABLE1'] = $i;
        ?>
    </tbody>
</table>