<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
mysql_query("SET NAMES 'utf8'");

$area = $_GET['area'];
$division = $_GET['division'];
$sede = $_GET['sede'];

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}

?>
	<table class="formateada" width="100%" border=1 >
<tbody>
<!--
  <tr>
	<td colspan="17" class="Estilo8" align="center"><h1>Listado de Bienes</h1></td>
  </tr>
  <tr>
<th ><div align="center" class="Estilo8"><strong>ITEM</strong></div></th>
<th ><div align="center" class="Estilo8"><strong>FECHA DE ADQUISICION DEL BIEN</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>NUMERO DE FACTURA</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>CONDICION DE LA ADQUISICION</strong></div></th>		
<th ><div align="center" class="Estilo8"><strong>NRO. BIEN NACIONAL</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>SERIAL</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>DESCRIPCIÓN</strong></div></th>		
<th ><div align="center" class="Estilo8"><strong>MARCA</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>MODELO</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>CODIGO DEL CATALOGO</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>COLOR</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>COSTO UNITARIO (Bs.)</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>CODIGO</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>ESTADO CONSERVACIÓN</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>UBICACIÓN</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>RPU</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>OBSERVACIONES</strong></div></th>	
 </tr>
-->
<?php
$filtro = '1=1';
if ($sede==0) 		{ $filtro = " 1=1 "; } 		else { $filtro = ' id_sector='.$sede; }
if ($division>0)	{ $filtro = $filtro . ' AND id_division='.$division; }  
if ($area>0)		{ $filtro = $filtro . ' AND id_area='.$area; }  

//-------- 
$consulta = "SELECT *, if (conservacion=1,'MUY BUENO',if (conservacion=2,'BUENO',if (conservacion=3,'REGULAR','MALO'))) as conservacion2 FROM vista_bienes_html WHERE $filtro AND borrado=0 ORDER BY division, area, numero_bien";
//echo $consulta;
//----------------------- MONTAJE DE LOS DATOS
$i=0;

$tabla = mysql_query ($consulta);

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	?>
	 <tr id="fila<?php echo $i.$registro->id_bien; ?>">
<?php if ($eliminar == 'SI' or $reasignar=='SI') { ?>
<td ><div align="center" class="Estilo8 Estilo1"><input type="checkbox" name="<?php echo $registro->id_bien; ?>" value="<?php echo $registro->id_bien; ?>" onClick="marcar(this,<?php echo $i.$registro->id_bien; ?>)"/></div></td>			  
<?php } ?>
<td ><div align="center" class="Estilo15"><?php echo $i; ?></div></td>
<td ><div align="center" class="Estilo15"><?php if (substr(voltea_fecha($registro->fecha),0,4)=='0000') {	echo '';	} else { echo voltea_fecha($registro->fecha);	} ?></div></td>
<td ><div align="center" class="Estilo15"><?php echo ($registro->factura); ?></div></td>
<td ><div align="center" class="Estilo15"><?php echo ($registro->condicion); ?></div></td>
<td ><div align="center" class="Estilo15"><?php echo ($registro->numero_bien); ?></div></td>
<td ><div align="center" class="Estilo15"><?php echo ($registro->serial); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->descripcion_bien); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->marca); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->modelo); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->codigo); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->color); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->valor); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->conservacion); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->conservacion2); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->division); ?></div></td>
<td ><div align="left" class="Estilo15"><?php echo ($registro->funcionario); ?></div></td>
<!--<td ><div align="left" class="Estilo15"><?php //echo ($registro->area); ?></div></td>-->
<!--		 palabras-->
 </tr>
	<?php
	}	
	?>		
</tbody>
</table>