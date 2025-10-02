            <table width="75%" border=1 align=center>
<?php 
$consulta = "SELECT contribuyente, rif, direccion FROM vista_contribuyentes_direccion WHERE (((rif)='".$_GET['rif']."'));";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla)
?>			  
<td bgcolor="#FF0000" height="27" colspan="6" align="center"><p class="Estilo7"><u>Contribuyente</u></p>              </td>
</tr><tr>
<td bgcolor="#CCCCCC" height=27><div align="center" class="Estilo14">Rif</div></td>
<td bgcolor="#CCCCCC" colspan="3" ><div align="center" class="Estilo14">Contribuyente</div></td>
</tr>
<tr>
<tr>
<td width="19%" height=27><div align="center" class="Estilo15"><?php echo formato_rif($registro->rif);?></div></td>
<td colspan="3" ><div align="left" class="Estilo15"><?php echo $registro->contribuyente;?></div></td>
</tr> 
</tr><tr>
	<td bgcolor="#CCCCCC" height=27 align="center" colspan="6"><div align="center" class="Estilo14">Domicilio Fiscal</div></td>
</tr>
</tr><tr>
	<td colspan="6" ><div align="left" class="Estilo15"><?php echo $registro->direccion;?></div></td>
</tr>
</table>

<p></p>

<table width="75%" border=1 align=center>
	<tr>
 <td bgcolor="#FF0000" height="27" colspan="8" align="center"><p class="Estilo7"><u>Resumen del Acta</u></p>              </td>
 </tr>
<tr>
  <td bgcolor="#CCCCCC" ><div align="center" class="Estilo5"><strong><span class="Estilo13">Numero</span></a></strong></div></td>
  <td bgcolor="#CCCCCC"  ><div align="center" class="Estilo13"><strong>Ejercicio o Periodo</strong></div></td>
  <td bgcolor="#CCCCCC"  ><div align="center" class="Estilo13"><strong>Reparo</strong></div></td>
  <td bgcolor="#CCCCCC" ><div align="center" class="Estilo13"><strong>Impto Omitido</strong></div></td>
  <td bgcolor="#CCCCCC" ><div align="center" class="Estilo13"><strong>Monto Pagado</strong></div></td>
  <td bgcolor="#CCCCCC" ><div align="center" class="Estilo13"><strong>Notificacion</strong></div></td>
  <td bgcolor="#CCCCCC" ><div align="center" class="Estilo13"><strong>Fecha Recepcion</strong></div></td>
</tr> 
<?php
global $monto_revocado; 

	$consulta = "SELECT reparo, impuesto_omitido, monto_pagado, numacta, periodo_desde, periodo_hasta, date_format(fecha_notificacion, '%d-%m-%Y') as fecha_notificacion, date_format(fecha_recepcion_sumario, '%d-%m-%Y') as fecha_recepcion_sumario, id_expediente FROM vista_sumario_exp_transferido WHERE status=".$_GET['status']." AND rif='".$_GET['rif']."' AND anno=".$_GET['anno']." AND numero=".$_GET['num']." AND sector=".$_GET['sector']." ORDER BY anno, numero;";
	//echo $consulta;
	$tabla = mysql_query($consulta);
				
	while ($registro = mysql_fetch_object($tabla))
	{ 
		echo '<tr><td   ><div align="center" class="Estilo15">';				
		echo $registro->numacta;
		echo '</div></td><td height=27><div align="center" class="Estilo15">';	
		echo date("d/m/Y", strtotime(voltea_fecha($registro->periodo_desde))).' al '.date("d/m/Y", strtotime(voltea_fecha($registro->periodo_hasta)));
		echo '</div></td><td ><div align="center" class="Estilo15">';
		echo '<label>'.formato_moneda($registro->reparo).'</label>';	
		echo '</div></td><td ><div align="center" class="Estilo15">';
		echo '<label>'.formato_moneda($registro->impuesto_omitido).'</label>';	
		echo '</div></td><td ><div align="center" class="Estilo15">';
		echo '<label>'.formato_moneda($registro->monto_pagado).'</label>';	
		echo '</div></td><td ><div align="center" class="Estilo15">';
		echo '<label>'.$registro->fecha_notificacion.'</label>';	
		echo '</div></td><td ><div align="center" class="Estilo15">';
		echo '<label>'.$registro->fecha_recepcion_sumario.'</label>';	
		echo '</div></td> </tr>';
		if ($concluir == 'Si')
		{
			$monto_revocado += $registro->impuesto_omitido - $registro->monto_pagado;
		}
		$id = $registro->id_expediente;
	}

	//OBTENEMOS LOS MONTOS PAGADOS EN SUMARIO
	$consulta_l = "SELECT sum(sumario_pagos.monto_pagado) as monto FROM sumario_pagos WHERE sumario_pagos.id_expediente = ".$id;
	$tabla_l = mysql_query ($consulta_l);						
	$valor = mysql_fetch_object($tabla_l);
	$monto = $valor->monto;
	/*
	if ($concluir == 'Si')
	{

		$_POST['OMONTOACTA']=$monto_revocado - $monto;
		if ($_POST['OMONTO']=='')
		{
			$_POST['OMONTO']=$monto_revocado - $monto;
		}
	}
	*/

?>	
</table>
<p></p>
<?php 
	$_SESSION['ANNO_PRO']=$_GET['anno'];
	$_SESSION['NUM_PRO']=$_GET['num'];
	$_SESSION['ORIGEN'] = 4;
	$_SESSION['SEDE'] = $_GET['sector'];
	$mostrarboton = 'NO';
	$serie = "1=1";
	include "../funciones/0_sanciones_aplicadas.php"; 
?> 
<p></p>
