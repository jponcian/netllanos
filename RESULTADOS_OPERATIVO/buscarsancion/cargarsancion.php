<?php

include "conexion.php";

$texto = $_GET['texto'];
$tributo = $_GET['tributo'];
if ($tributo==0)
{
	$buscartributo = "";
} else 
{
	$buscartributo = "and Sancion.Tributo=".$tributo; 
} 

$sql = "SELECT Sancion.Codigo, Sancion.Sancion, Leyes.Ley, Sancion.Art_COT, Sancion.Art_Ley_Rgto, Sancion.Art_Regla, Sancion.Tributo, Sancion.UT_Minimo, Sancion.UT_Maximo, Sancion.Aplicacion FROM Leyes INNER JOIN Sancion ON Leyes.Codigo = Sancion.Ley WHERE (Sancion.Sancion LIKE '%$texto%' OR Sancion.Codigo LIKE '%$texto%') and Sancion.Codigo>299 $buscartributo ORDER BY Sancion.Codigo DESC";
//echo $sql;

$q = odbc_exec ($_SESSION["conexion"], $sql);

print '	<table width="100%" style="margin-left:auto; margin-right:auto;" border="0" cellspacing="3" cellpadding="0">
';
if ($datos = odbc_fetch_array($q))
{
	while ($datos = odbc_fetch_array($q))
	{
		if ($color=="#D0D6DF") {
			$color="#B4B4F3";
		} else {
			$color="#D0D6DF";
		}
		//echo '<tr bgcolor="'.$color.'"><td>';
	
		print '
	  <tr bgcolor="'.$color.'">
		<td>Codigo: </td>
		<td>'.$datos['Codigo'].'</td>
	  </tr>
	  <tr bgcolor="'.$color.'">
		<td>Sancion: </td>
		<td>'.utf8_encode($datos['Sancion']).'</td>
	  </tr>
	  <tr bgcolor="'.$color.'">
		<td>Ley: </td>
		<td>'.utf8_encode($datos['Ley']).'</td>
	  </tr>';
	  if ($datos['Tributo']==1) { $tributo = "IVA";}
	  if ($datos['Tributo']==3) { $tributo = "ISLR";}
	  if ($datos['Tributo']==7) { $tributo = "ISAEA";}
	  if ($datos['Tributo']==8) { $tributo = "COT";}
	  if ($datos['Tributo']==9) { $tributo = "ISDRC";}
	  if ($datos['Tributo']==13) { $tributo = "IAJEA";}
	  print '<tr bgcolor="'.$color.'">
		<td>Tributo: </td>
		<td>'.$tributo.'</td>
	  </tr>';
	
	  if ($datos['Aplicacion']==13 or $datos['Aplicacion']==15) { $aplicacion = "TERMINO MEDIO";}
	  if ($datos['Aplicacion']==9) { $aplicacion = "INCREMENTO";}
	  if ($datos['Aplicacion']==14) { $aplicacion = "UT POR FACTURA";}
	  if ($datos['Aplicacion']==10 or $datos['Aplicacion']==12 or $datos['Aplicacion']==11 or $datos['Aplicacion']==17) { $aplicacion = "PORCENTUAL";}
	  
	  print '<tr bgcolor="'.$color.'">
		<td>Aplicacion: </td>
		<td>'.$aplicacion.'</td>
	  </tr>
	  <tr bgcolor="'.$color.'">
		<td>Art. COT: </td>
		<td>'.$datos['Art_COT'].'</td>
	  </tr>
	  <tr bgcolor="'.$color.'">
		<td>Art. Ley: </td>
		<td>'.$datos['Art_Ley_Rgto'].'</td>
	  </tr>
	  <tr bgcolor="'.$color.'">
		<td>Art. Reg/Prov: </td>
		<td>'.$datos['Art_Regla'].'</td>
	  </tr>
	  <tr bgcolor="'.$color.'">
		<td>UT Min - UT Max: </td>
		<td>'.number_format($datos['UT_Minimo'],0)." - ".number_format($datos['UT_Maximo'],0).'</td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
	  </tr>';
	  
	}
}
else
{
	print '<tr>
		<td>!...No existen sanciones con los parametros indicados...¡</td>
	  </tr>';
}
print '</table>';


?>