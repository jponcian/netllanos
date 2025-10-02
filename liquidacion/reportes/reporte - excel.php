<?php
ob_end_clean();
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";


if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

	$Titulos = array('','RELACIÓN DE PLANILLAS APROBADAS EN LIQUIDACION','RELACIÓN DE PLANILLAS LIQUIDADAS','RELACIÓN DE PLANILLAS TRANSFERIDAS','','');
	
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
		
	//--------- PARA BUSCAR LA DIVISION DEPENDE DEL ORIGEN USUARIO
	$filtro2='';
	// --- VALIDACION DEL ORIGEN DEL USUARIO
	if ($_SESSION['ORIGEN_USUARIO']==2)
		{$filtro2=' and (origen_liquidacion=7 or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'].')';}
	if ($_SESSION['ORIGEN_USUARIO']==4)
		{$filtro2=' and origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'];}
		//-------------------
	if ($_SESSION['ORIGEN_USUARIO']>0 and $_SESSION['ORIGEN_USUARIO2']>0) 
		{
		if ($_SESSION['ORIGEN_USUARIO']==2 or $_SESSION['ORIGEN_USUARIO2']==2)
			{	$filtro2=' and (origen_liquidacion=7 or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'].' or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO2'].')';	}
		else
			{	$filtro2=' and (origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'].' or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO2'].')';	}
		}		
		//-------------------
	if ($_SESSION['ORIGEN_USUARIO']>0 and $_SESSION['ORIGEN_USUARIO2']>0 and $_SESSION['ORIGEN_USUARIO3']>0) 
		{
		if ($_SESSION['ORIGEN_USUARIO']==2 or $_SESSION['ORIGEN_USUARIO2']==2  or $_SESSION['ORIGEN_USUARIO3']==2)
			{	$filtro2=' and (origen_liquidacion=7 or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'].' or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO2'].' or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO3'].')';	}
		else
			{	$filtro2=' and (origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'].' or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO2'].' or origen_liquidacion='.$_SESSION['ORIGEN_USUARIO3'].')';
			}
		}
		//-------------------
	
	if ($_SESSION['ORIGEN_USUARIO']<=0)	{	$texto1	= 'Recaudación'; 					}
	if ($_SESSION['ORIGEN_USUARIO']==2)	{	$texto1	= 'Sujetos Pasivos Especiales';		}
	if ($_SESSION['ORIGEN_USUARIO']==4)	{	$texto1	= 'Fiscalización';					}						
	//---------

	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.strtoupper($Region);
	
	$Sede = 'DEPENDENCIA: ' . strtoupper($dependencia);
	
?>
<style type="text/css">
<!--
.Estilo5 {font-size: 11px; }
.Estilo7 {font-size: 12px; color:#FFFFFF; font-weight: bold; }
.Estilo9 {font-size: 12px; font-weight: bold; }
.Estilo8 {font-size: 14}
.Estilo10 {font-size: 14; font-weight: bold; }
-->
</style>

<table bgcolor="#CCCCCC" width="100%" border="1">
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
	<tr bgcolor="#FF0000">
    <td rowspan="2" height="71"><div align="center" class="Estilo7"><strong>N°</strong></div></td>
	<td rowspan="2" ><div align="center" class="Estilo7">Expediente</div></td>
	<td rowspan="2" ><div align="center" class="Estilo7">Planilla</div></td>
    <td rowspan="2" ><div align="center" class="Estilo7">Contribuyente</div></td>
	<td rowspan="2" ><div align="center" class="Estilo7">Rif</div></td>
    <td rowspan="2" ><div align="center" class="Estilo7">Resolución</div></td>
    <td rowspan="2" ><div align="center" class="Estilo7">Documento</div></td>
    <td colspan="2"><div align="center" class="Estilo7">Periodo Fiscal</div></td>
    <td rowspan="2" ><div align="center" class="Estilo7">Numero Liquidacion</div></td>
    <td rowspan="2" ><div align="center" class="Estilo7">Fecha de Liquidación</div></td>
	<td rowspan="2" ><div align="center" class="Estilo7">Tipo de Impuesto</div></td>
	<td colspan="4" ><div align="center" class="Estilo7"><strong>Monto en BsS.</strong></div></td>
	<td rowspan="2" ><div align="center" class="Estilo7">Contribuyente Especial</div></td>
    <td rowspan="2" ><div align="center" class="Estilo7">Actividad Economica</div></td>
  </tr>
   <tr>
    <td ><div align="center" class="Estilo9"><strong>Desde</strong></div></td>
    <td ><div align="center" class="Estilo9"><strong>Hasta</strong></div></td>
	<td ><div align="center" class="Estilo9">Impuesto</div></td>
    <td  ><div align="center" class="Estilo9">Multa</div></td>
    <td  ><div align="center" class="Estilo9">Interes</div></td>
    <td  ><div align="center" class="Estilo9">Total</div></td>

	</tr>
 <?php
 
	$i=0;
	
	// FILTROS DEL REPORTE
	switch ($_SESSION['VARIABLE'])
		{					
		case 1:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_aprobacion_liq>='".$_SESSION['FECHA1']."' and fecha_aprobacion_liq<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY rif, liquidacion ASC';
		break;
		////////////////////////
		case 2:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_impresion>='".$_SESSION['FECHA1']."' and fecha_impresion<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY rif, fecha_impresion ASC';
		break;
		////////////////////////
		case 3:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_transferencia_not>='".$_SESSION['FECHA1']."' and fecha_transferencia_not<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY rif, fecha_transferencia_not ASC';
		break;	
		////////////////////////					
		}			
	// --------- FIN
	
	$rif = 0;
	$rif2 = '';
	
	$Sentencia = "SELECT cont_esp, sector, origen_liquidacion, anno_expediente, num_expediente , rif, contribuyente, periodoinicio, periodofinal, planilla_notificacion, numeroliquidacion, siglas, fecha_liquidacion, liquidacion, serie, monto_bs, concurrencia, especial, id_tributo FROM vista_liquidacion_planillas";

	$consulta_x = $Sentencia . $Filtro . $filtro2 . $Orden . ";"; 
	//echo $consulta_x;
	
	$tabla_x = mysql_query ($consulta_x);

	while ($registro_x = mysql_fetch_object($tabla_x))
	{
	// CONTENIDO TABLA
	
	echo '<tr>';
	
	if ($rif2<>$registro_x->rif) {	$rif++;	} 
	$rif2= $registro_x->rif;
	$txt= $rif;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= $registro_x->anno_expediente.' / '.$registro_x->num_expediente;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$i= $i +1;
	$txt= $i;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= $registro_x->contribuyente;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	$txt= $registro_x->rif;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	list ($resolucion, $fecha) = funcion_resolucion($registro_x->sector, $registro_x->origen_liquidacion, $registro_x->anno_expediente, $registro_x->num_expediente);
	
	$txt= $resolucion;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= $registro_x->planilla_notificacion;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	$txt= voltea_fecha($registro_x->periodoinicio);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= voltea_fecha($registro_x->periodofinal);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';	

	$txt= $registro_x->liquidacion;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	$txt= voltea_fecha($registro_x->fecha_liquidacion);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';	
		
	$txt= $registro_x->siglas;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= 0;
	echo '<td><div align="center" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->serie <> 38 and $registro_x->id_tributo <> 52) {	$txt= $registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial; }
	else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->serie == 38 or ($registro_x->serie == 41 and $registro_x->id_tributo == 52)) {	$txt= $registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial; }
	else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	$txt= ($registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial);
	echo '<td><div align="center" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	$txt= $registro_x->cont_esp;
	echo '<td><div align="center" class="Estilo5">';
	echo formato_si($txt);
	echo '</div></td>';

	$txt= $registro_x->rif;
	echo '<td><div align="center" class="Estilo5">';
	echo actividad_economica($txt);
	echo '</div></td>';

	echo ' </tr>';
	}
  ?>
</table>

