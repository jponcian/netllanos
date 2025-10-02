<form id="form1a" name="form1a" method="post" action="">
<table class="formateada" width="50%" border=1 align=center>
<tbody>

  <tr>
	<td bgcolor="#FF0000" height="27" colspan="5" align="center"><p class="Estilo7"><u>Areas Registradas</u></p></td>
  </tr>
  <tr>
<?php if ($mostrarboton <> 'NO') { ?>
<th height=27><div align="center" class="Estilo8"><strong>Sel</strong></div></td>	
<?php } ?>
<th width="10"><div align="center" class="Estilo8"><strong>Numero</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>Dependencia</strong></div></td>	
<th ><div align="center" class="Estilo8"><strong>Divisi&oacute;n</strong></div>
</td>	
<th ><div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div></td>
    </tr>
<?php 
//-------- ELIMINAR
$consulta = "SELECT bn_areas.descripcion, bn_areas.id_area, z_jefes_detalle.descripcion AS division, z_sectores.nombre FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.division=".$_POST['ODIVISION']." ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC";
$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	if ($_POST[$registro->id_area]==$registro->id_area)
		{
		if ($_POST['CMDELIMINAR']=='Eliminar')
			{
			$consultaxx = "SELECT id_area FROM bn_bienes WHERE id_area=".$registro->id_area;
			$tablaxx = mysql_query ($consultaxx);
			$registroxx = mysql_fetch_object($tablaxx);
			//----------------
			if ($registroxx->id_area>0)
				{
				echo "<script type=\"text/javascript\">alert('¡¡¡El Area posee Bienes Nacionales registrados, no puede ser eliminada!!!');</script>";
				}
			else
				{
				$consulta_a = "UPDATE bn_areas SET borrado=1 WHERE id_area=".$registro->id_area.";";
				$tabla_a = mysql_query($consulta_a);
				//--------------
				echo "<script type=\"text/javascript\">alert('¡¡¡Area Eliminada Exitosamente!!!');</script>";
				}
			}
		}
	}
	
//----------------------- MONTAJE DE LOS DATOS
$i=0;

//--------
$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr id="fila<?php echo $i; ?>">
<?php if ($mostrarboton <> 'NO') { ?>
<td ><div align="center" class="Estilo8"><input type="checkbox" name="<?php echo $registro->id_area; ?>" value="<?php echo $registro->id_area; ?>" onClick="marcar(this,<?php echo $i; ?>)"/></div></td>			  
<?php } ?>
<td ><div align="center" class="Estilo8"><?php echo $i; ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo mayuscula($registro->nombre); ?></div></td>
<td ><div align="left" class="Estilo8"><?php echo $registro->division; ?></div></td>
<td ><div align="left" class="Estilo8"><?php echo $registro->descripcion; ?></div></td>
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
           <input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />
</p> <?php
		 } ?>
         <p>&nbsp;</p>
</form>