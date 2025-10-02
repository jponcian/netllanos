<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
?>
<table class="formateada" align=center width="40%">
<tbody>
  <tr>
	<td bgcolor="#FF0000" height="40" colspan="6" align="center"><p class="Estilo7"><u>Planillas Registradas</u></p></td>
  </tr>
  <tr>
<th ><div align="center" class="Estilo8"><strong>Numero de Planilla o Declaracion</strong></div></th>		
<th ><div align="center" class="Estilo8"><strong>Periodo de Imposici&oacute;n</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>Concepto</strong></div></th>		
<th ><div align="center" class="Estilo8"><strong>Fecha de Vencimiento</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>Monto</strong></div></th>	
<th bgcolor="#CCCCCC"><div align="center"><strong>Opcion</strong></div></th>
 </tr>
<?php 
//--------
$consulta = "SELECT id_detalle, alm_solicitudes_detalle_tmp.cantidad, descripcion FROM alm_solicitudes_detalle_tmp, alm_inventario WHERE alm_solicitudes_detalle_tmp.id_articulo = alm_inventario.id_articulo AND alm_solicitudes_detalle_tmp.usuario = ".$_SESSION['CEDULA_USUARIO'].";";
//echo $consulta ;
//----------------------- MONTAJE DE LOS DATOS
$i=0;

$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr id="fila<?php echo $i; ?>">
<td ><div align="center" class="Estilo15"><?php echo $i; ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo mayuscula($registro->descripcion); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo mayuscula($registro->descripcion); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo mayuscula($registro->descripcion); ?></div></td>
<td ><div align="center" class="Estilo15"><?php echo ($registro->cantidad); ?></div></td>
<td ><div align="center"><span class="Estilo15"><input type="button" class="boton" value="Eliminar" onclick="eliminar(<?php echo $registro->id_detalle;?>)" /></span></div></td>
 </tr>
	<?php
	}	
	?>		
</tbody>
</table>
