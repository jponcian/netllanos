<?php 
session_start();
include "../conexion.php";
include "../auxiliar.php";
//--------------
?>
<table border=1 align=center>
<tbody>
  <tr>
	<td bgcolor="#FF0000" height="40" colspan="12" align="center"><p class="Estilo7"><u>Sanciones actuales aplicadas al Contribuyente</u></p></td>
  </tr>
  <tr height="35">
<td bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Num</strong></div></td>			  
<td bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Concepto</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Tributo</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Periodo </strong></div></td>
<td bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Monto Original BsS.</strong></div></td>
<!--<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>U.T. Original </strong></div></td>-->
<td bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Monto BsS.</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>U.T.</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Concurrencia</strong></div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo8"><strong>Recargo Especial </strong></div></td>
    </tr>
<?php 
//----------------------- MONTAJE DE LOS DATOS
	$consulta = "SELECT * FROM vista_liquidaciones WHERE id_resolucion<=0 AND status>=".$_GET['status1']." AND status<=".$_GET['status2']." AND origen_liquidacion=0".$_GET['origen']." AND anno_expediente=".$_GET['anno']." AND num_expediente=".$_GET['numero']." AND sector=".$_GET['sede'].";";
$tabla = mysql_query($consulta);
//echo $consulta;
$monto_actual = 0;
$monto_original = 0;
$i=0;

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr >
	 <?php 
	 $monto_actual = $monto_actual+ ($registro->monto_bs/$registro->concurrencia)*$registro->especial;
	 $monto_original = $monto_original + $registro->monto_bs;
	 ?>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo $i; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="left" class="Estilo8"><?php echo $registro->concepto; ?></div></td>
<td bgcolor="#FFFFFF" ><div class="Estilo8"><?php echo $registro->siglas; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo voltea_fecha($registro->periodoinicio) .' al ' . voltea_fecha($registro->periodofinal); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_bs)); ?></div></td>
<!--<td bgcolor="#FFFFFF" ><div align="right" class="Estilo8"><?php //echo formato_moneda(($registro->monto_ut)); ?></div></td>-->
<td bgcolor="#FFFFFF" ><div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_bs/$registro->concurrencia)*$registro->especial); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_ut/$registro->concurrencia)*$registro->especial); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php if ($registro->concurrencia>1) {echo 'Si';} else {echo 'No';} ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php if ($registro->especial>1) {echo 'Si';} else {echo 'No';} ?></div></td>
 </tr>
	<?php
	}	
	?>	
  <tr height="35">
<td bgcolor="#CCCCCC" colspan="4"><div align="center" class="Estilo8"><strong>Resumen de la Deuda =&gt;</strong></div></td>			  
<td bgcolor="#CCCCCC" ><div align="right" class="Estilo8"><strong><?php echo formato_moneda($monto_original); ?></strong></div></td>
<td bgcolor="#CCCCCC" ><div align="right" class="Estilo8"><strong><?php echo formato_moneda($monto_actual); ?></strong></div></td>
<td bgcolor="#CCCCCC" colspan="3"><div align="center" class="Estilo8"><strong></strong>---------------------------------------</div></td>
    </tr>
</tbody>
    </table>
         </p> 