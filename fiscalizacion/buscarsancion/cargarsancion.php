<?php

include('../../conexion.php');

$texto = $_GET['texto'];
$tributo = $_GET['tributo'];
if ($tributo==0)
{
	$buscartributo = "";
} else 
{
	$buscartributo = " and a_tributos.id_tributo=".$tributo; 
} 
mysql_query("SET NAMES 'utf8'");


if (is_numeric($texto))
{
	$sql = "SELECT Codigo, Sancion, Ley, siglas, Aplicacion, Art_COT, Art_Ley_Rgto, Art_Regla, UT FROM vista_fisc_consultar_sanciones WHERE Codigo =".$texto.$buscartributo; //echo $sql;
}
else
{
	$sql = "SELECT Codigo, Sancion, Ley, siglas, Aplicacion, Art_COT, Art_Ley_Rgto, Art_Regla, UT FROM vista_fisc_consultar_sanciones WHERE Sancion LIKE '%".$texto."%'".$buscartributo; //echo $sql;
}
//echo $sql;

$q = mysql_query($sql);

print '	<table width="100%" style="margin-left:auto; margin-right:auto;" border="0" cellspacing="3" cellpadding="0">
';
$cantidad = mysql_num_rows($q);
if ($cantidad > 0)
{
	while ($datos = mysql_fetch_array($q))
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
		<td>'.$datos['Sancion'].'</td>
	  </tr>
	  <tr bgcolor="'.$color.'">
		<td>Ley: </td>
		<td>'.$datos['Ley'].'</td>
	  </tr>';
	  print '<tr bgcolor="'.$color.'">
		<td>Tributo: </td>
		<td>'.$datos['siglas'].'</td>
	  </tr>';
	
	  if ($datos['Aplicacion']==13 or $datos['Aplicacion']==15) { $aplicacion = "TERMINO MEDIO";}
	  if ($datos['Aplicacion']==9) { $aplicacion = "INCREMENTO";}
	  if ($datos['Aplicacion']==14) { $aplicacion = "UT POR FACTURA";}
	  if ($datos['Aplicacion']==10 or $datos['Aplicacion']==12 or $datos['Aplicacion']==11 or $datos['Aplicacion']==17) { $aplicacion = "PORCENTUAL";}
	  
	  print '<tr bgcolor="'.$color.'">
		<td>Aplicacion: </td>
		<td>'.$datos['Aplicacion'].'</td>
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
		<td>'.$datos['UT'].'</td>
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