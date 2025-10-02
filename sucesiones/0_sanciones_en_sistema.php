<table class="formateada" width="70%" border=1 align=center>
<tbody>
  <tr>
	<td bgcolor="#FF0000" height="27" colspan="12" align="center"><p class="Estilo7"><u>Sanciones actuales aplicadas al Contribuyente</u></p></td>
  </tr>
  <tr>
 <?php if ($mostrarboton <> 'NO')
 { ?>
 <th height=27><div align="center" class="Estilo8"><strong>Sel</strong></div></td>	
 <?php } ?>
<th height=27><div align="center" class="Estilo8"><strong>Num</strong></div></td>			  
<?php if ($_SESSION['ADMINISTRADOR']>0) {  ?> 
<th ><div align="center" class="Estilo8"><strong>Aplicaci&oacute;n</strong></div></td>
<?php } ?>
<th ><div align="center" class="Estilo8"><strong>Sancion</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>Periodo </strong></div></td>
<th ><div align="center" class="Estilo8"><strong>Monto Original BsS.</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>U.T. Original </strong></div></td>
<th ><div align="center" class="Estilo8"><strong>Monto BsS.</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>U.T.</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>Concurrencia</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>Recargo Especial </strong></div></td>
    </tr>
<?php

//-------- CONCURRENCIA O ELIMINAR
$consulta = "SELECT id_liquidacion, concurrencia FROM vista_sanciones_aplicadas WHERE serie<>29 AND $serie AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
//echo $consulta;
$tabla = mysql_query($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	if ($_POST[$registro->id_liquidacion]==$registro->id_liquidacion)
		{
		if ($_POST['CMDCONCURRENCIA']=='Concurrencia')
			{
			// AGREGAR CONCURRENCIA
			if ($registro->concurrencia==1)
				{
				$consulta_a = "UPDATE liquidacion SET concurrencia=2 WHERE id_liquidacion=".$registro->id_liquidacion.";";
				$tabla_a = mysql_query($consulta_a);
				//--------------
				echo "<script type=\"text/javascript\">alert('¡¡¡Concurrencia Aplicada Exitosamente!!!');</script>";
				}
			else
				{
				$consulta_a = "UPDATE liquidacion SET concurrencia=1 WHERE id_liquidacion=".$registro->id_liquidacion.";";
				$tabla_a = mysql_query($consulta_a);
				//--------------
				echo "<script type=\"text/javascript\">alert('¡¡¡Concurrencia Eliminada Exitosamente!!!');</script>";
				}			
			}
		if ($_POST['CMDELIMINAR']=='Eliminar')
			{
			$consulta_a = "DELETE FROM liquidacion WHERE id_liquidacion=".$registro->id_liquidacion." AND secuencial=999999;";
			$tabla_a = mysql_query($consulta_a);
			//--------------
			echo "<script type=\"text/javascript\">alert('¡¡¡Sanción Eliminada Exitosamente!!!');</script>";
			}
		}
	}
	
//----------------------- MONTAJE DE LOS DATOS
	$consulta = "SELECT id_liquidacion, id_sancion, sancion, periodoinicio, periodofinal, nombre, siglas, monto_ut, monto_bs, concurrencia, especial, fecha_presentacion, fecha_vencimiento, fecha_pago, monto_pagado, planilla, id_liquidacion, aplicacion FROM vista_sanciones_aplicadas WHERE id_resolucion<=0 AND serie<>29 AND ".$serie." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla = mysql_query($consulta);

$i=0;

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr id="fila<?php echo $registro->id_liquidacion; ?>">
 <?php if ($mostrarboton <> 'NO')
 { ?>
 <td ><div align="center" class="Estilo8"><input type="checkbox" name="<?php echo $registro->id_liquidacion; ?>" value="<?php echo $registro->id_liquidacion; ?>"  onClick="marcar(this,<?php echo $registro->id_liquidacion; ?>)"/></div></td>
 <?php } ?>			  
<td ><div align="center" class="Estilo8"><?php echo $i; ?></div></td>
<?php if ($_SESSION['ADMINISTRADOR']>0) {  ?> 
<td ><div align="center" class="Estilo8"><?php echo $registro->aplicacion; ?></div></td>
<?php } ?>
<td ><div align="center" class="Estilo8"><?php echo $registro->id_sancion; ?></div></td>
<td ><div class="Estilo8"><?php echo $registro->sancion; ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo date("d/m/Y",strtotime(voltea_fecha($registro->periodoinicio))) .' al ' . date("d/m/Y",strtotime(voltea_fecha($registro->periodofinal))); ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo formato_moneda(($registro->monto_bs)); ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo formato_moneda(($registro->monto_ut)); ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo formato_moneda(($registro->monto_bs/$registro->concurrencia)*$registro->especial); ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo formato_moneda(($registro->monto_ut/$registro->concurrencia)*$registro->especial); ?></div></td>
<td ><div align="center" class="Estilo8"><?php if ($registro->concurrencia>1) {echo 'Si';} else {echo 'No';} ?></div></td>
<td ><div align="center" class="Estilo8"><?php if ($registro->especial>1) {echo 'Si';} else {echo 'No';} ?></div></td>
 </tr>
	<?php
	}	
	?>		
</tbody>
    </table>
         </p> 
		 <?php if ($mostrarboton <> 'NO')
		 { ?>
         <p align="center">
           <input type="submit" class="boton" name="CMDCONCURRENCIA" value="Concurrencia" />
           <input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />
         </p> <?php
		 } ?>
         <p>&nbsp;</p>