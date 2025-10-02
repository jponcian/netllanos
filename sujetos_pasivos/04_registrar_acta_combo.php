<?php
session_start();
include "../conexion.php";
?>
<table class="formateada" border=1 width="480" align=center>
<tbody>
  <tr>
	<td bgcolor="#FF0000" height="40" colspan="6" align="center"><p class="Estilo7"><u>Planillas</u></p></td>
  </tr>
  <tr>
<th ><div align="center" class="Estilo8"><strong>Numero de Planilla o Declaracion</strong></div></th>		
<th ><div align="center" class="Estilo8"><strong>Periodo de Imposici&oacute;n</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>Concepto</strong></div></th>		
<th ><div align="center" class="Estilo8"><strong>Fecha de Vencimiento</strong></div></th>	
<th ><div align="center" class="Estilo8"><strong>Monto</strong></div></th>	
<th bgcolor="#CCCCCC"><div align="center"><strong>Opcion</strong></div></th>
 </tr>
	 <tr id="fila1">
<td ><div align="center" class="Estilo15">
  <input type="text" name="OMONTO2" id="OMONTO2" size="20" maxlength="20" style="text-align:center" />
</div></td>
<td ><div align="center" class="Estilo15">
  <input type="text" name="OMONTO22" id="OMONTO22" size="20" maxlength="20" style="text-align:center" />
</div></td>
<td ><div align="center" class="Estilo15">
  <input type="text" name="OMONTO23" id="OMONTO23" size="20" maxlength="20" style="text-align:center" />
</div></td>
<td ><div align="center" class="Estilo15">
  <input type="text" name="OMONTO5" id="OMONTO5" size="10" maxlength="10" style="text-align:center" />
</div></td>
<td ><div align="center" class="Estilo15">
  <input type="text" name="OMONTO" id="OMONTO" size="12" maxlength="12" style="text-align:center">
</div></td>
<td ><div align="center"><span class="Estilo15"><input type="button" class="boton" value="Agregar" onClick="agregar()" /></span></div></td>
 </tr>	
</tbody>
</table>