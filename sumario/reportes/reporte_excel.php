<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

include "../../conexion.php";
include "../../funciones/auxiliar_php.php";

//BUSCAMOS LA REGION
$consulta_x = "SELECT nombre FROM z_region";
$tabla_x = mysql_query($consulta_x);
$regstro_x = mysql_fetch_object($tabla_x);
$Region = $regstro_x->nombre;

//BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
$consulta_x = "SELECT nombre, tipo_division FROM z_sectores WHERE id_sector=1";
$tabla_x = mysql_query($consulta_x);
$regstro_x = mysql_fetch_object($tabla_x);
$area = $regstro_x->tipo_division;
$dependencia = $regstro_x->nombre;

echo $Region.'<br/>';
echo $area.' Sumario Administrativo'.'<br/>';
echo 'REPORTE ACTUALIZADO AL: '.date("d-m-Y").'<br/>';

//IMPRIMIMOS LA CABECERA DEL CUADRO
?>
<table width="100%" border="1" bordercolor="#999999" style="font-size:11px" cellpadding="1" cellspacing="0">
	<tr bgcolor="#FF0000" style="color:#FFFFFF">
		<td><div align="center" class="Estilo7"><strong>SEDE/SECTOR</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>N° CORRELATIVO</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>NUMERO DE PROVIDENCIA</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>NOMBRE DEL CONTRIBUYENTE</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>RIF DEL CONTRIBUYENTE</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>PROGRAMA</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>NUMERO ACTA REPARO</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>FECHA INGRESO EXPEDIENTE</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>FECHA ASIGNACION PONENTE</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>NOMBRE PONENTE</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>DEVUELTO A FISCALIZACION</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>FECHA CULMINACIÓN EXPEDIENTE</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>MONTO REPARADO</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>MONTO CONFIRMADO</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>MONTO REVOCADO</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>Total Tributo</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>Total Multa</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>Total Intereses</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>Total Monto Liquidado</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>NUMERO RESOLUCIÓN CULMINATORIA</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>FECHA NOTIFICACION RESOLUCION C.</strong></div></td>
		<td><div align="center" class="Estilo7"><strong>OBSERVACIONES</strong></div></td>
	</tr>
<?php
$sql = "SELECT nombre, providencia, contribuyente, rif, programa, numacta, fecha_recepcion, fecha_asignacion_ponente, ponente, fecha_devuelto_fiscalizacion, fecha_culminacion, monto_reparo, monto_confirmado, monto_revocado, total_tributo, multa, intereses, resolucion_sumario FROM vista_sumario_reporte WHERE fecha BETWEEN '".voltea_fecha($_SESSION['INICIO'])."' AND '".voltea_fecha($_SESSION['FIN'])."'";
$result = mysql_query($sql);
$i=1;
while ($valor = mysql_fetch_object($result))
{
?>
	<tr bgcolor="#FFFFFF">
		<td align="center"><?php echo $valor->nombre ?></td>
		<td align="center"><?php echo $i ?></td>
		<td align="center"><?php echo $valor->providencia ?></td>
		<td><?php echo $valor->contribuyente ?></td>
		<td align="center"><?php echo formato_rif($valor->rif) ?></td>
		<td align="center"><?php echo $valor->programa ?></td>
		<td align="center"><?php echo $valor->numacta ?></td>
		<td align="center"><?php echo $valor->fecha_recepcion ?></td>
		<td align="center"><?php echo $valor->fecha_asignacion_ponente ?></td>
		<td align="center"><?php echo $valor->ponente ?></td>
		<td align="center"><?php echo $valor->fecha_devuelto_fiscalizacion ?></td>
		<td align="center"><?php echo $valor->fecha_culminacion ?></td>
		<td align="right"><?php echo formato_moneda($valor->monto_reparo) ?></td>
		<td align="right"><?php echo formato_moneda($valor->monto_confirmado) ?></td>
		<td align="right"><?php echo formato_moneda($valor->monto_revocado) ?></td>
		<td align="right"><?php echo formato_moneda($valor->total_tributo) ?></td>
		<td align="right"><?php echo formato_moneda($valor->multa) ?></td>
		<td align="right"><?php echo formato_moneda($valor->intereses) ?></td>
		<td align="right"><?php echo formato_moneda(($valor->total_tributo + $valor->multa + $valor->intereses)) ?></td>
		<td align="center"><?php echo $valor->resolucion_sumario ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<?php
	$i++;
}
?>
</table>


