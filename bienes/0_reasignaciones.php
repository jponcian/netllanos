<table width="70%" border=1 align=center>
<tbody>

  <tr>
	<td bgcolor="#FF0000" height="40" colspan="10" align="center"><p class="Estilo7"><u>Bienes Registrados</u></p></td>
  </tr>
  </tbody>
  </table>
  <table id="tabla1" width="70%" border=1 align=center>
  <tbody>
  <tr>
<?php if ($eliminar == 'SI' or $reasignar=='SI') { ?>
<th width="44" height=41 bgcolor="#CCCCCC"><div align="center" class="Estilo8"><strong>Sel</strong></div></th>	
<?php } ?>
<th bgcolor="#CCCCCC" width="54"><div align="center" class="Estilo8"><strong>Item</strong></div></th>
<th bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Categoria</strong></div></th>
<th bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Numero Bien</strong></div></th>	
<th bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div></th>	
<th bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Estado</strong></div></th>	
<th bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Valor BsS</strong></div></th>	
<th bgcolor="#CCCCCC" ><div align="center" class="Estilo8"><strong>Area</strong></div></th>	
 </tr>
<?php 
$filtro = '1=1';
if ($sede>0) 		{ $filtro = $filtro . ' AND id_sector='.$sede; } 		else { $filtro = $filtro . ' AND id_sector=0'; }
if ($division>0) 	{ $filtro = $filtro . ' AND id_division='.$division; } 	else { $filtro = $filtro . ' AND id_division=0'; }
if ($area>0)		{ $filtro = $filtro . ' AND id_area='.$area; } 			else { $filtro = $filtro . ' AND id_area=0'; }

//-------- ELIMINAR
$consulta = "SELECT * FROM vista_bienes_nacionales WHERE $filtro AND borrado=0 ORDER BY descripcion_bien, numero_bien";
$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	if ($_POST[$registro->id_bien]==$registro->id_bien)
		{
		if ($_POST['CMDELIMINAR']=='Eliminar')
			{
			$consulta_a = "UPDATE bn_bienes SET borrado=1 WHERE id_bien=".$registro->id_bien.";";
			$tabla_a = mysql_query($consulta_a);
			//--------------
			echo "<script type=\"text/javascript\">alert('¡¡¡Bien Eliminado Exitosamente!!!');</script>";
			}
		}
	}
	
//----------------------- MONTAJE DE LOS DATOS
$i=0;

$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr>
<?php if ($eliminar == 'SI' or $reasignar=='SI') { ?>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><input type="checkbox" name="<?php echo $registro->id_bien; ?>" value="<?php echo $registro->id_bien; ?>" /></div></td>			  
<?php } ?>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo $i; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo mayuscula($registro->codigo); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo ($registro->numero_bien); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="left" class="Estilo8"><?php echo mayuscula($registro->descripcion_bien); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center" class="Estilo8"><?php echo ($registro->conservacion); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="right" class="Estilo8"><?php echo formato_moneda($registro->valor); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="left" class="Estilo8"><?php echo mayuscula($registro->area); ?></div></td>

 </tr>
	<?php
	}	
	?>		
</tbody>
</table>
         </p> 
		 <?php if ($eliminar == 'SI')
		 { ?>
         <p align="center">
           <input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />
</p> <?php
		 } ?>
		 
<script type="text/javascript">
function prueba(nombre)
{
	if (document.getElementsByID(nombre).cheked)
	{
		alert('Esta Marcado');
	}
}
</script>