<html>
<head>
  <title>Status Derechos Pendientes</title>
  <style type="text/css">
<!--
.Estilomenun {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
body {
	background-image: url();
}
.Estilo7 {
	font-size: 18px;
	font-weight: bold;
	color: #FFFFFF;
}
-->
  </style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>
</head>

<body>
 <p>
   	<?php include "../titulo.php";?>
 </p>
 <p align="center">
<?php
	include "menu.php";
?>
</p>
		<form name="form1" method="post" action="#vista">
		  <table width="60%" align=center>
            <tbody>
				<tr>
                <td ><div align="center">
                  <p>&nbsp;</p>
                  <p><strong>Introduzca aqu&iacute; el numero de Rif del Contribuyente =&gt; </strong>
                    <input type="text" name="ORIF" value="<?php echo $_POST['ORIF'];?>" size="10">
                    <input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
                  </p>
                  <p>&nbsp;</p>
                </div>                  </td>
              </tr>
		
		</tbody></table>
		  <p></p>
	<?php 
		
		if ($_POST['CMDBUSCAR']=="Buscar")
		{	
		$consulta = "SELECT rif, contribuyente, direccion FROM vista_contribuyentes_direccion WHERE rif='".$_POST['ORIF']."';";
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
		}
		?>	  <table width="60%" border=1 align=center>
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
    <td bgcolor="#999999" colspan="2"  ><div align="center"><strong>Dirección</strong></div></td> 
		</tr>
	 <tr>
        <td bgcolor="#FFFFFF" colspan="2"  ><div align="center"><?php echo $direccion;?></div></td>
	</tr>
	
	</table>
	      <p></p>
	      <table width=70% align=center border=1>
  <tbody>
	<tr>	
<td bgcolor="#999999" ><div align="center"><strong>N°</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Sector</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Origen</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Año</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Expediente</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Resolución</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Per&iacute;odo</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Liquidacion</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Monto</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Estatus</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Memo Liq.</strong></div></td>
<td bgcolor="#999999" ><div align="center"><strong>Memo Not.</strong></div></td>
	</tr>
<?php 		
if ($_POST['CMDBUSCAR']=="Buscar")
	{
	$i=1;
	$consulta = "SELECT vista_sanciones_aplicadas.dependencia, vista_sanciones_aplicadas.periodoinicio, vista_sanciones_aplicadas.periodofinal, vista_sanciones_aplicadas.liquidacion, vista_sanciones_aplicadas.area, vista_sanciones_aplicadas.num_expediente, vista_sanciones_aplicadas.anno_expediente, vista_sanciones_aplicadas.memo, vista_sanciones_aplicadas.memo2, vista_sanciones_aplicadas.status_descripcion, ((monto_bs / concurrencia )* especial) as monto, vista_sanciones_aplicadas.sector, vista_sanciones_aplicadas.origen_liquidacion, resoluciones.fecha FROM vista_sanciones_aplicadas INNER JOIN resoluciones ON vista_sanciones_aplicadas.anno_expediente = resoluciones.anno_expediente AND resoluciones.num_expediente = vista_sanciones_aplicadas.num_expediente AND vista_sanciones_aplicadas.sector = resoluciones.id_sector AND resoluciones.id_origen = vista_sanciones_aplicadas.origen_liquidacion WHERE rif='".$_POST['ORIF']."' ORDER BY sector ASC, origen_liquidacion ASC, anno_expediente ASC, num_expediente ASC;";
	$tabla = mysql_query($consulta); //echo $consulta;
	while ($registro = mysql_fetch_object($tabla))
		{
		?>
<?php list ($sigla_resolucion, $fecha_resolucion, $num_res) = funcion_resolucion($registro->sector, $registro->origen_liquidacion, $registro->anno_expediente, $registro->num_expediente);?>
		<tr>	
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $i ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->dependencia; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->area; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->anno_expediente; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->num_expediente; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo palabras($sigla_resolucion).' de fecha '.voltea_fecha($registro->fecha); ?></div></td>
<td width="100" bgcolor="#FFFFFF" ><div align="center"><?php echo voltea_fecha($registro->periodoinicio) .' al ' . voltea_fecha($registro->periodofinal); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->liquidacion; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo formato_moneda($registro->monto); ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->status_descripcion ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->memo; ?></div></td>
<td bgcolor="#FFFFFF" ><div align="center"><?php echo $registro->memo2; ?></div></td>
		</tr>
		<?php
		$i++;
		}
	}
?>
      </tbody>
          </table>
		  <a name="vista"></a>
		  <p>&nbsp;</p>
		</form>

<?php include "../pie.php";?>


  <p>&nbsp;</p>
</body>
</html>
