<?php

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");

$sqltipo = "SELECT
resoluciones.id_sector,
resoluciones.id_origen,
resoluciones.anno_expediente,
resoluciones.num_expediente,
liquidacion.fecha_pag
FROM
liquidacion
INNER JOIN resoluciones ON liquidacion.sector = resoluciones.id_sector AND liquidacion.origen_liquidacion = resoluciones.id_origen AND liquidacion.anno_expediente = resoluciones.anno_expediente AND liquidacion.num_expediente = resoluciones.num_expediente
WHERE
liquidacion.fecha_pag IS NOT NULL AND
liquidacion.fecha_not IS NOT NULL
GROUP BY
resoluciones.id_sector,
resoluciones.id_origen,
resoluciones.anno_expediente,
resoluciones.num_expediente";
//echo $sqltipo.'<br>';

$tablaActas = $con->query($sqltipo);
$i = 1;
while ($reg = $tablaActas->fetch_object())
{
    if ($reg->fecha_pag != null and $reg->fecha_pag != '0000-00-00')
    {
        $sql_update = "UPDATE resoluciones SET fecha_liq_pago = '".$reg->fecha_pag."' WHERE id_sector = ".$reg->id_sector." AND anno_expediente = ".$reg->anno_expediente." AND id_origen = ".$reg->id_origen." AND num_expediente = ".$reg->num_expediente;

        //echo $sql_update.'<br>';

        $tabla_update = $con->query($sql_update);
        $i++;
    }
}

echo "Actualizados un total de ".$i." registros";
?>