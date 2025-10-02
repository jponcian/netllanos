<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

	include "../../conexion.php";
	include "../../funciones/auxiliar_php.php";
	
	//---------- ORIGEN DEL FUNCIONARIO 
 	include "../../funciones/origen_funcionario.php";

	$Titulos = array('','RELACI&Oacute;N DE PLANILLAS RECIBIDAS EN COBRO','RELACI&Oacute;N DE PLANILLAS ASIGNADAS','RELACI&Oacute;N DE PLANILLAS PAGADAS','','');
	
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
	if ($_SESSION['ORIGEN_USUARIO']==0 or $_SESSION['ORIGEN_USUARIO']==13)	{	$texto1	= 'RECAUDACI&Oacute;N';}
	if ($_SESSION['ORIGEN_USUARIO']==2)	{	$texto1	= 'SUJETOS PASIVOS ESPECIALES';}
	if ($_SESSION['ORIGEN_USUARIO']==4)	{	$texto1	= 'FISCALIZACI&Oacute;N';}						
	//---------
	
	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.strtoupper($Region);
	
	$Sede ='DEPENDENCIA: ' . strtoupper($dependencia);
	
?>
<style type="text/css">
<!--
.Estilo5 {font-size: 10px; }
.Estilo7 {font-size: 12px; font-weight: bold; }
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
	    <td><div align="center"><strong><?php echo strtoupper($area).' DE '.$texto1; ?></strong></div></td>
	</tr>
        </tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Titulo; ?></strong></div></td>
	</tr>	
</table>
	
	<table width="100%" border="1">
   <tr>
    <td><div align="center" class="Estilo7"><strong>N&deg;</strong></div></td>
    <td><div align="center" class="Estilo7">Contribuyente</div></td>
    <td><div align="center" class="Estilo7">Rif</div></td>
    <td><div align="center" class="Estilo7">N&deg; Resoluci&oacute;n</div></td>
    <td><div align="center" class="Estilo7">Fecha Resoluci&oacute;n</div></td>
    <td><div align="center" class="Estilo7">Desde</div></td>
    <td><div align="center" class="Estilo7">Hasta</div></td>
    <td><div align="center" class="Estilo7">N&deg; Documento</div></td>
    <td><div align="center" class="Estilo7">N&deg; Liquidacion</div></td>
    <td><div align="center" class="Estilo7">N&deg; Notificacion</div></td>
    <td><div align="center" class="Estilo7">Fecha Liq.</div></td>
    <td><div align="center" class="Estilo7">Tipo de Impuesto</div></td>
    <td><div align="center" class="Estilo7">Impuesto</div></td>
    <td><div align="center" class="Estilo7">Multa</div></td>
    <td><div align="center" class="Estilo7">Inter&eacute;s</div></td>
    <td><div align="center" class="Estilo7">Total</div></td>
    <td><div align="center" class="Estilo7">Fecha de Not.</div></td>
    <td><div align="center" class="Estilo7">Fecha Trans. a Cobro</div></td>
	<td><div align="center" class="Estilo7">Notificador</div></td>
    <td><div align="center" class="Estilo7">Fecha Recibido en Cobro</div></td>
	<td><div align="center" class="Estilo7">Cobrador</div></td>
	<td><div align="center" class="Estilo7">Fecha Pago</div></td>
  </tr>
 <?php
 
	$i=0;
	
	// FILTROS DEL REPORTE
	switch ($_SESSION['VARIABLE'])
		{					
		case 1:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_importacion_a_cob>='".$_SESSION['FECHA1']."' and fecha_importacion_a_cob<='".$_SESSION['FECHA2']."'".$origen."";
			$Orden = ' ORDER BY fecha_importacion_a_cob ASC';
		break;
		////////////////////////
		case 2:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_asignacion_cobrador>='".$_SESSION['FECHA1']."' and fecha_asignacion_cobrador<='".$_SESSION['FECHA2']."'".$origen."";
			$Orden = ' ORDER BY fecha_asignacion_cobrador ASC';
		break;
		////////////////////////
		case 3:
			$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_pag>='".$_SESSION['FECHA1']."' and fecha_pag<='".$_SESSION['FECHA2']."'".$origen."";
			$Orden = ' ORDER BY fecha_pag ASC';
		break;	
		////////////////////////					
		}			
	// --------- FIN
	
	$Sentencia = "SELECT sector, origen_liquidacion, anno_expediente, num_expediente , rif, contribuyente, periodoinicio, periodofinal, planilla_notificacion, numeroliquidacion, numeronotificacion, siglas, fecha_liquidacion, liquidacion, serie, monto_bs, concurrencia, especial, fecha_not, notificador, fecha_tranferencia_a_cob, fecha_importacion_a_cob, cobrador, id_tributo, fecha_pag FROM vista_liquidacion_planillas";

	$consulta_x = $Sentencia . $Filtro . $Orden . ";";
	//echo $consulta_x ;
	$tabla_x = mysql_query ($consulta_x);

	while ($registro_x = mysql_fetch_object($tabla_x))
	{
	// CONTENIDO TABLA
	
	echo '<tr>';
	
	$i= $i +1;
	$txt= $i;
	
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= $registro_x->contribuyente;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= $registro_x->rif;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	list ($resolucion, $fecha) = funcion_resolucion($registro_x->sector, $registro_x->origen_liquidacion, $registro_x->anno_expediente, $registro_x->num_expediente);
	
	$txt= $resolucion;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= voltea_fecha($fecha);
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
	
	$txt= $registro_x->planilla_notificacion;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= $registro_x->numeroliquidacion;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= $registro_x->numeronotificacion ;
	echo '<td><div align="center" class="Estilo5">';
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
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->serie <> 38 and $registro_x->id_tributo <> 52) {	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs; }
	else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->serie == 38 or ($registro_x->serie == 41 and $registro_x->id_tributo == 52)) {	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs; }
	else {	$txt= 0; }
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	$txt= $registro_x->monto_bs / $registro_x->monto_bs * $registro_x->monto_bs;
	echo '<td><div align="center" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	if ($registro_x->fecha_not>'01/01/2000') {$txt= voltea_fecha($registro_x->fecha_not);}
		else {$txt= '';}
	
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	if ($registro_x->fecha_tranferencia_a_cob>'01/01/2000') {$txt= voltea_fecha($registro_x->fecha_tranferencia_a_cob);}
		else {$txt= '';}
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	if ($registro_x->notificador>0) {list ($txt, $txt2) = funcion_funcionario($registro_x->notificador);}
		else {$txt= '';}	
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	if ($registro_x->fecha_importacion_a_cob>'01/01/2000') {$txt= voltea_fecha($registro_x->fecha_importacion_a_cob);}
		else {$txt= '';}
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
			
	if ($registro_x->cobrador>0) {list ($txt, $txt2) =  funcion_funcionario($registro_x->cobrador);}
		else {$txt= '';}	
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	if ($registro_x->fecha_pag>'01/01/2000') {$txt= voltea_fecha($registro_x->fecha_pag);}
		else {$txt= '';}
	
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	echo ' </tr>';
	}
  ?>
</table>

