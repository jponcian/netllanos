<table width="70%" border=1 align=center>
<tbody>
<tr>
<td bgcolor="#FF0000" height="25" colspan="11" align="center"><p class="Estilo7"><u>Acta(s) actual(es) aplicada(s) al Contribuyente</u></p></td>
</tr>
<tr>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Acta</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Fecha</strong></div></td>			  
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Monto Reparo</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Impuesto Pagado</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Impuesto Omitido</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Fecha Not</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Monto Pagado</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Multa Bs.</strong></div></td>
<td bgcolor="#CCCCCC"  ><div align="center" class="Estilo8"><strong>Inter&eacute;s Bs. </strong></div></td>
 </tr>
 	<?php $i=0;
	$consulta_x = "SELECT fis_actas_detalle.COT, Sum(fis_actas_detalle.monto_pagado) as pagado, Sum(fis_actas_detalle.interes) as monto_interes, Sum(fis_actas_detalle.multa_actual) as monto_multa, Sum(fis_actas_detalle.reparo) as monto_reparo, Sum(fis_actas_detalle.impuesto_pagado) as monto_pagado, Sum(fis_actas_detalle.impuesto_omitido) as monto_impuesto, expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector, fis_actas.numero as acta, fis_actas.fecha, fis_actas.fecha_notificacion FROM expedientes_fiscalizacion, fis_actas, fis_actas_detalle WHERE fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero AND fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.id_acta = fis_actas_detalle.id_acta AND expedientes_fiscalizacion.numero = ".$_SESSION['NUM_PRO']." AND expedientes_fiscalizacion.anno = ".$_SESSION['ANNO_PRO']." AND expedientes_fiscalizacion.sector = ".$_SESSION['SEDE']." GROUP BY expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector";
	$tabla_x = mysql_query ($consulta_x);
	//---------------
	while ($registro_x = mysql_fetch_object($tabla_x))
{
$i++; 		
?> 
 <tr>
  <td  ><div align="center"><span class="Estilo15"><?php echo $registro_x->acta;?></span></div></td>
  <td  ><div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_x->fecha);?></span></div></td>
  <td  ><div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_reparo);?></span></div></td>
  <td  ><div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_pagado);?></span></div></td>
  <td  ><div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_impuesto);?></span></div></td>
  <td  ><div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_x->fecha);?></span></div></td>
<td  ><div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->pagado);?></span></div></td>
  <td  ><div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_multa);?></span></div></td>
  <td  ><div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_interes);?></span></div></td>  
</tr>
<?php
}
?>
         </tbody></table>                                                                                                      