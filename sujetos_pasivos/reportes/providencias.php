<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

include "../../conexion.php";
include "../../funciones/auxiliar_php.php";

	$Titulos = array('','RELACION DE PROVIDENCIAS EMITIDAS','RELACION DE PROVIDENCIAS','RELACION DE PROVIDENCIAS NOTIFICADAS','RELACION DE PROVIDENCIAS INCORPORADAS EN EL i-SENIAT');
	
	list($anno1,$mes1,$dia1)=explode('/',$_SESSION['FECHA1']);
	list($anno2,$mes2,$dia2)=explode('/',$_SESSION['FECHA2']);
	
	$Titulo = $Titulos[$_SESSION['VARIABLE']] . '	DESDE : ' . $dia1.'/'. $mes1 .'/'. $anno1 . ' HASTA: ' . $dia2.'/'. $mes2 .'/'. $anno2 ;
	
	//BUSCAMOS LA REGION
	$consulta_x = "SELECT nombre FROM z_region";
	$tabla_x = mysql_query($consulta_x);
	$regstro_x = mysql_fetch_object($tabla_x);
	$Region = $regstro_x->nombre;
	
	//BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
	$consulta_x = "SELECT nombre, tipo_division FROM z_sectores WHERE id_sector=".$_SESSION['OSEDE'];
	$tabla_x = mysql_query($consulta_x);
	$regstro_x = mysql_fetch_object($tabla_x);
	$area = $regstro_x->tipo_division;
	$dependencia = $regstro_x->nombre;
		
	$texto1	= 'Sujetos Pasivos Especiales';

	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.strtoupper($Region);
	
	$Sede ='DEPENDENCIA: ' . strtoupper($dependencia);
	
?>
<style type="text/css">
<!--
.Estilo5 {font-size: 11px; }
.Estilo7 {font-size: 12px; font-weight: bold; }
.Estilo8 {font-size: 14}
.Estilo10 {font-size: 14; font-weight: bold; }
-->
</style>

<table width="100%" border="1">
   	<tr>
	    <td><div align="center"><strong><?php echo $Gerencia; ?></strong></div></td>
	</tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Sede; ?></strong></div></td>
	</tr>
        </tr>
		<tr>
	    <td><div align="center"><strong><?php echo strtoupper($area.' de '.$texto1); ?></strong></div></td>
	</tr>
        </tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Titulo; ?></strong></div></td>
	</tr>	
</table>
	
	<table width="100%" border="1">
	  <!-- <tr bgcolor="#999999">
    <td height="50" colspan="6"><div align="center" class="Estilo10"><strong>Contribuyentes Notificados</strong></div></td>
	</tr>-->
   <tr bgcolor="#CCCCCC">
    <td height="71"><div align="center" class="Estilo7"><strong>Rif</strong></div></td>
    <td><div align="center" class="Estilo7">Contribuyente</div></td>
    <td><div align="center" class="Estilo7">Providencia</div></td>
	<td><div align="center" class="Estilo7">Dependencia</div></td>
	<td><div align="center" class="Estilo7">Fecha Emisi&oacute;n en Sistema </div></td>
    <td><div align="center" class="Estilo7">Fecha de Incorporaci&oacute;n</div></td>
	<td><div align="center" class="Estilo7">Fecha Providencia</div></td>
    <td><div align="center" class="Estilo7">Fecha Notificaci&oacute;n</div></td>
    <td><div align="center" class="Estilo7">Fecha de ISeniat</div></td>
  </tr>
<?php

	$i=0;
	
	// FILTROS DEL REPORTE
	switch ($_SESSION['VARIABLE'])
		{					
		case 0:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_registro>='".$_SESSION['FECHA1']."' and fecha_registro<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY fecha_registro DESC';
		break;
		////////////////////////
		case 1:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_prov>='".$_SESSION['FECHA1']."' and fecha_prov<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY fecha_prov DESC';
		break;
		////////////////////////
		case 2:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND inicio_sujeto_especial>='".$_SESSION['FECHA1']."' and inicio_sujeto_especial<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY inicio_sujeto_especial DESC';
		break;
		////////////////////////
		case 3:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_not>='".$_SESSION['FECHA1']."' and fecha_not<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY fecha_not DESC';
		break;
		////////////////////////
		case 4:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_iseniat>='".$_SESSION['FECHA1']."' and fecha_iseniat<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY fecha_iseniat DESC';
		break;
		////////////////////////
		}	
	// --------- FIN
	
	$Sentencia = "SELECT * FROM vista_ce_providencias";

	$consulta_x = $Sentencia . $Filtro . $Orden . ";";
	
	$tabla_x = mysql_query ($consulta_x);

	while ($registro_x = mysql_fetch_object($tabla_x))
	{
	// CONTENIDO TABLA
	
	echo '<tr>';
	
//	$i= $i +1;
//	$txt= $i;
//	
//	echo '<td><div align="center" class="Estilo5">';
//	echo $txt;
//	echo '</div></td>';
	
	$txt= $registro_x->rif;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= $registro_x->contribuyente;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	$txt= $registro_x->Siglas_resol_especiales.$registro_x->anno.'/'.$registro_x->numero;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= $registro_x->nombre;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= voltea_fecha($registro_x->fecha_registro);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	$txt= voltea_fecha($registro_x->inicio_sujeto_especial);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= voltea_fecha($registro_x->fecha_prov);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= voltea_fecha($registro_x->fecha_not);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= voltea_fecha($registro_x->fecha_iseniat);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	echo ' </tr>';
	}
  ?>
</table>

