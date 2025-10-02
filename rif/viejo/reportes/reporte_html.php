<?php
session_start();

include "../../conexion.php";

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
	$Titulos = array('','Auxiliar Contable tributos Internos');
	
	list($anno1,$mes1,$dia1)=explode('/',$_SESSION['INICIO']);
	list($anno2,$mes2,$dia2)=explode('/',$_SESSION['FIN']);
	
	$Titulo = '	DESDE : ' . $dia1.'/'. $mes1 .'/'. $anno1 . ' HASTA: ' . $dia2.'/'. $mes2 .'/'. $anno2 ;
	
	////////// REGION DE EMISION
	$consulta_x = "SELECT nombre FROM z_region;";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$Region=$registro_x->nombre;
	// ---------------------

	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.mb_convert_case($Region, MB_CASE_UPPER, "ISO-8859-1");

	////////// BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
	$consulta_x = "SELECT nombre, tipo_division FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
	$tabla_x = mysql_query($consulta_x);
	$regstro_x = mysql_fetch_object($tabla_x);
	$area = $regstro_x->tipo_division;
	$dependencia = $regstro_x->nombre;

	$Sede ='DEPENDENCIA: ' . mb_convert_case($dependencia, MB_CASE_UPPER, "ISO-8859-1");
	
?>
<style type="text/css">
<!--
.Estilo15 {font-size: 18px; font-weight: bold; color: #FFFFFF; }
.Estilo16 {font-size: 18px; font-weight: bold; color: #FF0000; }
.Estilo17 {font-size: 14px; }
-->
</style>

<table width="100%" border="0">
   	<tr>
	    <td><div align="center"><strong>DIVISI&Oacute;N DE CONTRIBUYENTES PASIVOS ESPECIALES </strong></div></td>
	</tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Titulo; ?></strong></div></td>
	</tr>
	    <strong>
        </tr>
	    </strong>
	
		<tr>
	    <td><div align="center"><strong><?php echo $Gerencia; ?></strong></div></td>
	</tr>
	    <strong>
        </tr>
	    </strong>
	
		<tr>
	    <td><div align="center"><strong><?php echo $Sede; ?></strong></div></td>
	</tr>	
</table>
	
	<table width="75%" border="1" cellpadding="0" cellspacing="0" align="center">
   <tr>
    <td bgcolor="#FF0000" ><div align="center" class="Estilo15">N&deg;</div></td> 
	<td bgcolor="#FF0000" ><div align="center" class="Estilo15"><strong>Rif</strong></div></td>
    <td bgcolor="#FF0000" ><div align="center" class="Estilo15">Contribuyente</div></td>
    <td bgcolor="#FF0000" ><div align="center" class="Estilo15">Impuesto</div></td>
    <td bgcolor="#FF0000" ><div align="center" class="Estilo15">Fecha Pres. </div></td>
    <td bgcolor="#FF0000" ><div align="center" class="Estilo15">Fecha Pago </div></td>
	<td bgcolor="#FF0000" ><div align="center" class="Estilo15">Monto</div></td>
  </tr>

<?php

	// CONDICIONES
	if ($_SESSION['OSEDE']==0) {$Sede = '';}
	else {$Sede = " AND Sector=".$_SESSION['OSEDE']."";}
	if ($_SESSION['RIF']==0) {$Contribuyente = '';}
		else {$Contribuyente = " AND ce_pagos.Rif='".$_SESSION['RIF']."'";}
	if ($_SESSION['TERMINAL']==100) {$terminal = '';}
		else {$terminal = " AND RIGHT(ce_pagos.Rif,1)='".$_SESSION['TERMINAL']."'";}
	if ($_SESSION['IMPUESTO']==0) {$Impuesto = '';}
		else {$Impuesto = " AND ce_pagos.Tipo_Impuesto=".$_SESSION['IMPUESTO'];}
	// FIN DE LAS CONDICIONES

	$consulta_x = "SELECT ce_pagos.Rif, vista_contribuyentes_direccion.contribuyente AS NombreRazon, ce_cal_tip_obligaciones.Tipo, date_format(ce_pagos.Fecha_Presentacion,'%d/%m/%Y') AS presentacion, date_format(ce_pagos.Fecha_Pago,'%d/%m/%Y') AS pago, ce_pagos.Monto FROM ce_pagos INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = ce_pagos.Rif INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto WHERE ce_pagos.Fecha_Pago >= '".$_SESSION['INICIO']."' AND ce_pagos.Fecha_Pago <= '".$_SESSION['FIN']."'".$Contribuyente.$Impuesto.$Sede.$terminal." ORDER BY ce_pagos.Fecha_Presentacion ASC, ce_pagos.Fecha_Pago ASC, ce_pagos.Rif ASC";
$tabla_x = mysql_query($consulta_x);

$i=1;
$total =0;

	while ($registro_x = mysql_fetch_object($tabla_x))
		{
		// ---- IMPRESION DE LOS DATOS
		printf ('<tr>
			<td ><div align="center" class="Estilo17">%s</div></td> 
			<td ><div align="center" class="Estilo17">%s</div></td> 
			<td  ><div align="center" class="Estilo17">%s</div></td>
			<td  ><div align="center" class="Estilo17">%s</div></td>
			<td  ><div align="center" class="Estilo17">%s</div></td>
			<td  ><div align="center" class="Estilo17">%s</div></td>
			<td  ><div align="right" class="Estilo17">%s</div></td>
		  </tr>	',$i,$registro_x->Rif,$registro_x->NombreRazon,$registro_x->Tipo,$registro_x->presentacion,$registro_x->pago,number_format(doubleval($registro_x->Monto),2,',','.'));	
		 $total += $registro_x->Monto;
		$i++;
		}
		
// FIN

?>
<tr>
<td align="center" colspan="6"><span class="Estilo16">>>> TOTAL <<<</span></td>
<td align="right" ><span class="Estilo16"><?php echo number_format(doubleval($total),2,',','.'); ?></span></td>
</tr>
</table>
<?php
//----------
include "../../desconexion.php";
//----------

?>