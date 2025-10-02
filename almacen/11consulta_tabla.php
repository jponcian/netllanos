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

// Obtener la fecha desde GET
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
if ($fecha == '') {
    echo '<div class="alert alert-warning">Seleccione una fecha para ver los ingresos.</div>';
    return;
}
list($mes, $anno) = explode('-', $fecha);

?>
<table class="table table-bordered table-hover align-middle text-center mt-3">
    <thead class="table-danger">
        <tr class="bg-primary text-white">
            <th height="30" class="text-center">#</th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Numero</th>
            <th class="text-center">Division</th>
            <th class="text-center">Funcionario</th>
            <th class="text-center">Ingreso</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $consulta = "SELECT * FROM vista_alm_ingreso WHERE month(fecha)=" . intval($mes) . " and year(fecha)=" . intval($anno) . " ORDER BY id_ingreso DESC;";
        // Usar la conexión central en sesión (se asume siempre definida)
        $tabla = $_SESSION['conexionsqli']->query($consulta);
        $i = 0;
        if ($tabla) {
            while ($registro = $tabla->fetch_object()) {
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
                        <div align="center"><?php echo $registro->ingreso; ?></div>
                    </td>
                    <td>
                        <div align="left"><?php echo $registro->descripcion; ?></div>
                    </td>
                    <td>
                        <div align="left"><?php echo $registro->funcionario; ?></div>
                    </td>
                    <td>
                        <a href="../almacen/formatos/x_ingreso.php?ingreso=<?php echo $registro->id_ingreso; ?>"
                            target="_blank"><i class="fa-regular fa-file-pdf fa-2x"></i></a>
                    </td>
                </tr>
                <?php
            }
        }
        $_SESSION['VARIABLE1'] = $i;
        ?>
    </tbody>
</table>