<?php 
$consulta = 'SELECT vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.direccion FROM vista_sanciones_aplicadas INNER JOIN vista_contribuyentes_direccion ON vista_sanciones_aplicadas.rif = vista_contribuyentes_direccion.rif  WHERE num_expediente='.$_POST['ONUMERO'].' AND anno_expediente='.$_POST['OANNO'].' AND origen_liquidacion='.$_POST['OORIGEN'].' AND sector=0'.$_POST['OSEDE'].' GROUP BY vista_sanciones_aplicadas.sector, vista_sanciones_aplicadas.origen_liquidacion, vista_sanciones_aplicadas.anno_expediente, vista_sanciones_aplicadas.num_expediente;';
$tabla = mysql_query($consulta);
if ($registro = mysql_fetch_object($tabla))
	{
	$rif = $registro->rif ;
	$contribuyente = $registro->contribuyente ;
	$direccion = $registro->direccion ;			
	}
else
	{
	$rif = '';
	$contribuyente = '';
	$direccion = '';			
	}

?>	  <table width="55%" border=1 align=center>
              <tr>
           <td bgcolor="#FF0000" height="27" colspan="2" align="center"><p class="Estilo7"><u>Planillas de Liquidacion Generadas al Contribuyente </u></p> </td>
        </tr>
		<tr>
    <td bgcolor="#999999"   height=27><div align="center"><strong>Rif</strong></div></td>
    <td bgcolor="#999999"  ><div align="center"><strong>Contribuyente</strong></div></td> 
		</tr>
 <tr>
        <td bgcolor="#FFFFFF"   height=27><div align="center"> <?php echo $rif;?></div></td>
        <td bgcolor="#FFFFFF"  ><div align="center"><?php echo $contribuyente;?></div></td>
	</tr>
	<tr>
    <td bgcolor="#999999" colspan="2"  ><div align="center"><strong>Direcci&oacute;n</strong></div></td> 
	  </tr>
	 <tr>
        <td bgcolor="#FFFFFF" colspan="2"  ><div ><?php echo $direccion;?></div></td>
	</tr>
	</table>