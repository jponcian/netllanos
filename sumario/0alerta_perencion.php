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
.Estilo8 {color: #000000}
-->
  </style>

<?php

//$fecha = fecha_perencion($dia);
$hoy = date('Y-m-d');
$imprimir = 0;

$sqlperencion1 = "SELECT expedientes_sumario.anno, expedientes_sumario.numero, expedientes_sumario.anno_expediente_fisc, expedientes_sumario.num_expediente_fisc, expedientes_sumario.rif, expedientes_sumario.fecha_recepcion, expedientes_sumario.fecha_notificacion_acta, z_sectores.nombre, vista_contribuyentes_direccion.contribuyente, CONCAT_WS(' ',z_empleados.Nombres,z_empleados.Apellidos) AS ponente, expedientes_sumario.fecha_asignacion_ponente FROM expedientes_sumario INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_sumario.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_sumario.sector INNER JOIN z_empleados ON z_empleados.cedula = expedientes_sumario.cedula_ponente WHERE status<2";
$tabla1 = mysql_query($sqlperencion1);
$n=1;
while ($valor1 = mysql_fetch_object($tabla1))
{

	$fecha1 = fecha_perencion($valor1->fecha_notificacion_acta);
	if ($fecha1 <= $hoy)
	{
		$imprimir = 1;
		break;
	}
}

if ($imprimir == 1)
{
	?>
		<table width="65%" border=1 align=center>
		<tbody>
		  <tr>
			<td bgcolor="#FF0000" height="45" colspan="12" align="center"><p class="Estilo7"><u>Expedientes por Perimir Sumario Administrativo al <?php echo date("d-m-Y") ?></u></p></td>
			</tr>
		</tbody>
	<?php
	//echo '<table width="75%" border=1 align=center>';
	echo '<tr>';
	echo '<td bgcolor="#CCCCCC"  height=27><div align="center"><strong>Num</strong></div></td>';
	echo utf8_encode('<td bgcolor="#CCCCCC"  height=27><div align="center"><strong>N° Recepción</strong></div></td>');
	echo utf8_encode('<td bgcolor="#CCCCCC"  height=27><div align="center"><strong>Fecha Recepción</strong></div></td>');
	echo '<td bgcolor="#CCCCCC"  height=27><div align="center"><strong>Rif</strong></div></td>';
	echo '<td bgcolor="#CCCCCC"  ><div align="center"><strong>Contribuyente</strong></div></td>';
	echo utf8_encode('<td bgcolor="#CCCCCC"  ><div align="center"><strong>Año Prov</strong></div></td>');
	echo '<td bgcolor="#CCCCCC"  ><div align="center"><strong>N&uacute;mero Prov</strong></div></td>';
	echo '<td bgcolor="#CCCCCC"  ><div align="center"><strong>Notificacion Acta</strong></div></td>';
	echo '<td bgcolor="#CCCCCC"  ><div align="center"><strong>Origen</strong></div></td>';
	echo '<td bgcolor="#CCCCCC"  ><div align="center"><strong>Dependencia</strong></div></td>';
	echo '<td bgcolor="#CCCCCC"  ><div align="center"><strong>Ponente</strong></div></td>';
	echo '<td bgcolor="#CCCCCC"  ><div align="center"><strong>Fecha Asignacion</strong></div></td>';
	echo '</tr>';
	$sqlperencion = "SELECT expedientes_sumario.anno, expedientes_sumario.numero, expedientes_sumario.anno_expediente_fisc, expedientes_sumario.num_expediente_fisc, expedientes_sumario.rif, expedientes_sumario.fecha_recepcion, expedientes_sumario.fecha_notificacion_acta, z_sectores.nombre, vista_contribuyentes_direccion.contribuyente, CONCAT_WS(' ',z_empleados.Nombres,z_empleados.Apellidos) AS ponente, expedientes_sumario.fecha_asignacion_ponente FROM expedientes_sumario INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_sumario.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_sumario.sector INNER JOIN z_empleados ON z_empleados.cedula = expedientes_sumario.cedula_ponente WHERE status<2";
	$tabla = mysql_query($sqlperencion);
	$i=1;
	while ($valor = mysql_fetch_object($tabla))
	{
	
		$fecha = fecha_perencion($valor->fecha_notificacion_acta);
		if ($fecha <= $hoy)
		{
			echo '<tr>';
			echo '<td align="center">'.$i.'</td>';
			echo '<td align="center">'.$valor->anno.' - '.$valor->numero.'</td>';
			echo '<td align="center">'.$valor->fecha_recepcion.'</td>';
			echo '<td align="center">'.$valor->rif.'</td>';
			echo '<td align="center">'.$valor->contribuyente.'</td>';
			echo '<td align="center">'.$valor->anno_expediente_fisc.'</td>';
			echo '<td align="center">'.$valor->num_expediente_fisc.'</td>';
			echo '<td align="center">'.$valor->fecha_notificacion_acta.'</td>';
			echo utf8_encode('<td align="center">División de Fiscalización</td>');
			echo '<td align="center">'.$valor->nombre.'</td>';
			echo '<td align="center">'.$valor->ponente.'</td>';
			echo '<td align="center">'.$valor->fecha_asignacion_ponente.'</td>';
			echo '</tr>';
			$i++;
		}
	} 
	if ($i > 1)
	{ ?>
		<table width="65%" border=1 align=center>  
		<tr>
			<td align="center"><div id="blink" class="luzon">Estos Expedientes PERIMEN en un lapso menor o igual a DOS(2) meses</div>></td>
		</tr>
		</table>
	<?php
	}
}
else{
?> <?php
	include "../logo_central.php";
?><?php
}
?>
