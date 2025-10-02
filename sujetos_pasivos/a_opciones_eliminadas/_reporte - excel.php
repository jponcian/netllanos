<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

include "../../conexion.php";
include "../../funciones/auxiliar_php.php";

	$Titulos = array('','RELACIÓN DE RESOLUCIONES EMITIDAS','RELACIÓN DE RESOLUCIONES TRANSFERIDAS','RELACIÓN DE PLANILLAS PAGADAS','','');
	
	list($anno1,$mes1,$dia1)=explode('/',$_SESSION['FECHA1']);
	list($anno2,$mes2,$dia2)=explode('/',$_SESSION['FECHA2']);
	
	$Titulo = $Titulos[$_SESSION['VARIABLE']] . '	DESDE : ' . $dia1.'/'. $mes1 .'/'. $anno1 . ' HASTA: ' . $dia2.'/'. $mes2 .'/'. $anno2 ;
	
	//BUSCAMOS LA REGION
	$consulta_x = "SELECT nombre FROM z_region";
	$tabla_x = mysql_query($consulta_x);
	$regstro_x = mysql_fetch_object($tabla_x);
	$Region = $regstro_x->nombre;
	
	//BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
	$consulta_x = "SELECT nombre, tipo_division FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
	$tabla_x = mysql_query($consulta_x);
	$regstro_x = mysql_fetch_object($tabla_x);
	$area = $regstro_x->tipo_division;
	$dependencia = $regstro_x->nombre;
		
	//--------- PARA BUSCAR LA DIVISION DEPENDE DEL ORIGEN USUARIO
	if ($_SESSION['ORIGEN_USUARIO']<=0)	{	$texto1	= 'Recaudación'; 		$filtro2='';}
	if ($_SESSION['ORIGEN_USUARIO']==2)	{	$texto1	= 'Sujetos Pasivos Especiales';		$filtro2=' AND origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'].' ';}
	if ($_SESSION['ORIGEN_USUARIO']==4)	{	$texto1	= 'Fiscalización';		$filtro2=' AND origen_liquidacion='.$_SESSION['ORIGEN_USUARIO'].' ';}						
	//---------

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
	   <tr bgcolor="#999999">
    <td height="50" colspan="3"><div align="center" class="Estilo10"><strong>Contribuyentes Notificados</strong></div></td>
	<td colspan="3"><div align="center" class="Estilo10">Notificación de Omisión</div></td>
	<td colspan="3"><div align="center" class="Estilo10"><strong>Resolución por Omisión o por Imposición de Sanción</strong></div></td>
	<td colspan="5"><div align="center" class="Estilo10"><strong>Producción Potencial</strong></div></td>
	<td colspan="5"><div align="center" class="Estilo10"><strong>Producción Efectiva</strong></div></td>		
	</tr>
   <tr bgcolor="#CCCCCC">
    <td height="71"><div align="center" class="Estilo7"><strong>Rif</strong></div></td>
    <td><div align="center" class="Estilo7">Contribuyente</div></td>
    <td><div align="center" class="Estilo7">Periodo</div></td>
    <td><div align="center" class="Estilo7">Numero</div></td>
    <td><div align="center" class="Estilo7">Fecha de Notificaci&oacute;n</div></td>
    <td><div align="center" class="Estilo7">Fecha de Declaraci&oacute;n y Pago</div></td>
    <td><div align="center" class="Estilo7">Numero</div></td>
    <td><div align="center" class="Estilo7">Fecha de Notificaci&oacute;n</div></td>
    <td><div align="center" class="Estilo7">Fecha de Pago</div></td>
    <td><div align="center" class="Estilo7">Impuesto omitido</div></td>
    <td><div align="center" class="Estilo7">Intereses Moratorios</div></td>
    <td><div align="center" class="Estilo7">Multa por incumplimiento de Deberes Formales</div></td>
    <td><div align="center" class="Estilo7">Otras sanciones</div></td>
    <td><div align="center" class="Estilo7">TOTAL  PRODUCCI&Oacute;N</div></td>
    <td><div align="center" class="Estilo7">Impuesto omitido</div></td>
    <td><div align="center" class="Estilo7">Intereses Moratorios</div></td>
    <td><div align="center" class="Estilo7">Multa por incumplimiento de Deberes Formales</div></td>
    <td><div align="center" class="Estilo7">Otras sanciones</div></td>
    <td><div align="center" class="Estilo7">TOTAL  PRODUCCI&Oacute;N</div></td>
  </tr>
 <?php
 
	$i=0;
	
	// FILTROS DEL REPORTE
	switch ($_SESSION['VARIABLE'])
		{					
		case 1:
			$Filtro = " WHERE  id_resolucion<=0 AND sector = ".$_SESSION['OSEDE']." AND fecha_res>='".$_SESSION['FECHA1']."' and fecha_res<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY rif ASC';
		break;
		////////////////////////
		case 2:
			$Filtro = " WHERE  id_resolucion<=0 AND sector = ".$_SESSION['OSEDE']." AND fecha_transferencia_a_liq>='".$_SESSION['FECHA1']."' and fecha_transferencia_a_liq<='".$_SESSION['FECHA2']."'";
			$Orden = ' ORDER BY fecha_transferencia_a_liq ASC';
		break;		
		////////////////////////		
		}			
	// --------- FIN
	
	$Sentencia = "SELECT sector, origen_liquidacion, anno_expediente, num_expediente , rif, contribuyente, periodoinicio, periodofinal, planilla_notificacion, numeroliquidacion, numeronotificacion, siglas, fecha_liquidacion, liquidacion, serie, monto_bs, concurrencia, especial, fecha_not, notificador, fecha_tranferencia_a_cob, fecha_importacion_a_cob, cobrador, id_tributo, fecha_pag FROM vista_liquidacion_planillas_resolucion";

	$consulta_x = $Sentencia . $Filtro . $filtro2 . $Orden . ";";
	
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
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	$txt= formato_periodo($registro_x->periodoinicio);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= '';
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	list ($resolucion, $fecha) = funcion_resolucion($registro_x->sector, $registro_x->origen_liquidacion, $registro_x->anno_expediente, $registro_x->num_expediente);
	
	$txt= $resolucion;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	if ($registro_x->fecha_not>'01/01/2000') {$txt= voltea_fecha($registro_x->fecha_not);}
	else {$txt= '';}
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	if ($registro_x->fecha_pag>'01/01/2000') {$txt= voltea_fecha($registro_x->fecha_pag);}
	else {$txt= '';}
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= 0;
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->serie == 38 or ($registro_x->serie == 41 and $registro_x->id_tributo == 52)) {	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs; }
	else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->serie <> 38 and $registro_x->id_tributo <> 52) {	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs; }
	else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	$txt= 0;
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs;
	echo '<td><div align="center" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	$txt= 0;
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->fecha_pag>'01/01/2000'){
	if ($registro_x->serie == 38 or ($registro_x->serie == 41 and $registro_x->id_tributo == 52)) {	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs; }
	else {	$txt= 0; }}else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->fecha_pag>'01/01/2000'){
	if ($registro_x->serie <> 38 and $registro_x->id_tributo <> 52) {	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs; }
	else {	$txt= 0; }}else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	$txt= 0;
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	if ($registro_x->fecha_pag>'01/01/2000'){
	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs; }else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';	

	echo ' </tr>';
	}
  ?>
</table>

