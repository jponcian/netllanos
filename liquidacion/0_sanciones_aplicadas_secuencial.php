<table class="formateada" width="60%" border=1 align=center>
<tbody>
  <tr>
	<td bgcolor="#FF0000" height="40" colspan="12" align="center"><p class="Estilo7"><u>Sanciones actuales aplicadas al Contribuyente</u></p></td>
  </tr>
  <tr height="35">
<td bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Num</strong></div></td>			  
<td bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Concepto</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Tributo</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Periodo </strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Liquidacion</strong></div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo8"><strong>Secuencial</strong></div></td>
<td bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Monto BsS.</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>U.T.</strong></div></td>
    </tr>
<?php 
//----------------------- MONTAJE DE LOS DATOS
	$consulta = "SELECT * FROM vista_liquidaciones WHERE id_resolucion<=0 AND status>=$status AND status<=$status2 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla = mysql_query($consulta);

$i=0;

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr id="fila<?php echo $registro->liquidacion; ?>">
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo $i; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="left" class="Estilo8"><?php echo $registro->concepto; ?></div></td>
<td bgcolor="#FFFFFF" ><div class="Estilo8"><?php echo $registro->siglas; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo voltea_fecha($registro->periodoinicio) .' al ' . voltea_fecha($registro->periodofinal); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo $registro->liquidacion; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo $registro->secuencial; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_bs/$registro->concurrencia)*$registro->especial); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="right" class="Estilo8"><?php echo formato_moneda(($registro->monto_ut/$registro->concurrencia)*$registro->especial); ?></div></td>
 </tr>
	<?php
	}	
	?>		
</tbody>
    </table>
         </p> 