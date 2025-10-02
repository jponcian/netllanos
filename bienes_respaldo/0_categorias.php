<form id="form1a" name="form1a" method="post" action="">
<table class="formateada" width="40%" border=1 align=center>
<tbody>

  <tr>
	<td bgcolor="#FF0000" height="27" colspan="4" align="center"><p class="Estilo7"><u>Categor&iacute;as Registradas</u></p></td>
  </tr>
  <tr>
<th height=27><div align="center" class="Estilo8"><strong>Sel</strong></div></td>	
<th width="10"><div align="center" class="Estilo8"><strong>Numero</strong></div></td>
<th ><div align="center" class="Estilo8"><strong>C&oacute;digo</strong></div>
</td>	
<th ><div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div></td>
    </tr>
<?php 
//-------- ELIMINAR
$consulta = "SELECT * FROM bn_categorias ORDER BY descripcion;";
$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	if ($_POST[$registro->id_categoria]==$registro->id_categoria)
		{
		if ($_POST['CMDELIMINAR']=='Eliminar')
			{
			$consulta_a = "DELETE FROM bn_categorias WHERE id_categoria=".$registro->id_categoria.";";
			$tabla_a = mysql_query($consulta_a);
			//--------------
			echo "<script type=\"text/javascript\">alert('¡¡¡Categoría Eliminada Exitosamente!!!');</script>";
			}
		}
	}
	
//----------------------- MONTAJE DE LOS DATOS
$i=0;

$consulta = "SELECT * FROM bn_categorias ORDER BY descripcion;";
$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr id="fila<?php echo $i; ?>">
<td ><div align="center" class="Estilo8"><input type="checkbox" name="<?php echo $registro->id_categoria; ?>" value="<?php echo $registro->id_categoria; ?>" onClick="marcar(this,<?php echo $i; ?>)"/></div></td>			  
<td ><div align="center" class="Estilo8"><?php echo $i; ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo $registro->codigo; ?></div></td>
<td ><div align="center" class="Estilo8"><?php echo $registro->descripcion; ?></div></td>
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