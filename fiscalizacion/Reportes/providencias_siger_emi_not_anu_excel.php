<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

include "../../conexion.php";

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

	////////// BUSCAMOS EL AREA
	$consulta_i = "SELECT tipo_division, nombre FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'].";";
	$tabla_i = mysql_query($consulta_i);
	$registro_i = mysql_fetch_object($tabla_i);
	$Fiscalizacion=$registro_i->tipo_division;
	$Dependencia=$registro_i->dependencia;
	// ---------------------

	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.mb_convert_case($Region, MB_CASE_UPPER, "ISO-8859-1");
	
	$DEPENDENCIAS= mb_convert_case('Dependencia: '.$Dependencia, MB_CASE_UPPER, "ISO-8859-1");

	$Sede =mb_convert_case($Fiscalizacion . ' de Fiscalización', MB_CASE_UPPER, "ISO-8859-1");
	
?>
<style type="text/css">
<!--
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
</table>
	
	<table width="100%" border="1">
   <tr>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7"><strong>Año Prov</strong></div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7"><strong>N&deg; Prov</strong></div></td>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Fecha Emision</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Fecha Notificacion</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Fecha Culminacion</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Rif</div></td>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Razon Social </div></td>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Ciudad </div></td>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Domicilio</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Actividad</div></td>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Supervisor </div></td>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Fiscal </div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Impuesto</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Multa</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Interes</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Clausurado</div></td>
    <td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Tipo</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Tributo</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Anulada</div></td>
	<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Especial</div></td>
  </tr>
 <?php
 
	// CONSULTA DE LAS SIGLAS
	$consulta_a = "SELECT * FROM z_sectores WHERE id_sector<=5;";
	$tabla_a = mysql_query($consulta_a);
	$registro_a = mysql_fetch_object($tabla_a);
	// ---------------------

	// CONSULTA POR SEDE
	if ($_SESSION['SEDE']==0) 
		{$Sede="sector";} else {$Sede="sector=".$_SESSION['SEDE'];}
	// CONSULTA POR RIF
	if ($_SESSION['RIF']==0) 
		{$Rif="rif<>''";} else {$Rif="rif='".$_SESSION['RIF']."'";}
	// CONSULTA POR FISCAL
	if ($_SESSION['FISCAL']==0) 
		{$Fiscal="cedulafiscal<>0";} else {$Fiscal="cedulafiscal=".$_SESSION['FISCAL']."";}
	// CONSULTA POR SUPERVISOR
	if ($_SESSION['SUPERVISOR']==0) 
		{$Supervisor="cedulasupervisor<>0";} else {$Supervisor="cedulasupervisor=".$_SESSION['SUPERVISOR']."";}
	// AÑO DE EMISION
	if ($_SESSION['ANNO']==0) 
		{$Emision="anno";} 
	else 
		{$Emision="anno=".$_SESSION['ANNO'];}
	
	// CONSULTA POR STATUS DE PROVIDENCIA
	switch ($_SESSION['VARIABLE'])
	{
	case 'EMITIDAS':
		if ($_SESSION['INICIO']<>"" and $_SESSION['FIN']<>"")
		{
			$Fecha="AND fecha_emision>='".$_SESSION['INICIO']."' AND fecha_emision<='".$_SESSION['FIN']."'";
		} else {
			$Fecha='';
		}
		$SIN_MONTO='SI';
	break;
	case 'POR NOTIFICAR':
		if ($_SESSION['INICIO']<>"" and $_SESSION['FIN']<>"")
		{
			$Fecha="AND fecha_emision>='".$_SESSION['INICIO']."' AND fecha_emision<='".$_SESSION['FIN']."'";
		} else {
			$Fecha='';
		}
		$Status =' AND estatus=2 ';
		$SIN_MONTO='SI';		
	break;
	case 'NOTIFICADAS':
		if ($_SESSION['INICIO']<>"" and $_SESSION['FIN']<>"")
		{
			$Fecha="AND fecha_notificacion>='".$_SESSION['INICIO']."' AND fecha_notificacion<='".$_SESSION['FIN']."'";
		} else {
			$Fecha='AND fecha_notificacion';		
		}
		$SIN_MONTO='SI';		
	break;
	case 'ANULADAS':
		if ($_SESSION['INICIO']<>"" and $_SESSION['FIN']<>"")
		{
			$Fecha="AND fecha_anulacion>='".$_SESSION['INICIO']."' AND fecha_anulacion<='".$_SESSION['FIN']."'";
		} else {
			$Fecha='AND fecha_anulacion';
		}
		$SIN_MONTO='SI';
	break;
	case 'CONCLUIDAS PRODUCTIVAS':
		if ($_SESSION['INICIO']<>"" and $_SESSION['FIN']<>"")
		{
			$Fecha="AND fecha_conclusion>='".$_SESSION['INICIO']."' AND fecha_conclusion<='".$_SESSION['FIN']."'";
		} else {
			$Fecha='AND fecha_conclusion';
		}
		$SIN_MONTO='NO';
		$CONFORMES='NO';
	break;
	case 'CONCLUIDAS CONFORMES':
		if ($_SESSION['INICIO']<>"" and $_SESSION['FIN']<>"")
		{
			$Fecha="AND fecha_conclusion>='".$_SESSION['INICIO']."' AND fecha_conclusion<='".$_SESSION['FIN']."'";
		} else {
			$Fecha='AND fecha_conclusion';
		}
		$SIN_MONTO='NO';
		$CONFORMES='SI';
	break;		
	}
	
	$consulta_x  = "SELECT anno, numero, fecha_emision, fecha_notificacion, fecha_conclusion, rif, contribuyente, ciudad, direccion, actividad, cedulasupervisor, supervisor, cedulafiscal, fiscal, programa, tributos, fecha_anulacion, sector, estatus, Especial FROM vista_providencias_reporte WHERE ".$Rif. $Fecha . " AND ".$Fiscal." AND ".$Supervisor." AND ".$Sede." AND ".$Emision." ".$Status." ORDER BY anno, numero;";
	
	$tabla_x = mysql_query($consulta_x);

	while ($registro_x = mysql_fetch_object($tabla_x))
	{
		$especial=array('No','Si');
		// ANULADAS
		$anulada = '';
		if ($registro_x->estatus==9) {$anulada = '1';}
		// --------
		// ACTIVIDADES ECONOMICAS
		$actividad = $registro_x->actividad;

		// DIRECCION
		$direccion = $registro_x->direccion;

		// IMPUESTO
		$impuesto = '';
		$consultax  = "SELECT impuesto_omitido FROM vista_resumen_actas WHERE num_prov=".$registro_x->numero." AND anno_prov=".$registro_x->anno." AND sector=".$_SESSION['SEDE_USUARIO'].";";
			$tablax = mysql_query($consultax);
			if ($registrox = mysql_fetch_object($tablax)) 
			{
				$impuesto = $registrox->impuesto_omitido;
				$multareparo = $registrox->multa_actual;
				$intereses = $registrox->interes;
			}
		// --------
		// SANCION
		$sancion = '';
		$consultax  = "SELECT multa FROM vista_resumen_multa_vdf_reporte WHERE num_prov=".$registro_x->numero." AND anno_prov=".$registro_x->anno." AND sector=".$_SESSION['SEDE_USUARIO'].";";
		$tablax = mysql_query($consultax);
			if ($registrox = mysql_fetch_object($tablax)) {$sancion = $registrox->multa;}
		// --------
		// INTERES
		$interes = '';
		$consultax  = "SELECT intereses FROM vista_resumen_interes_vdf_reporte WHERE num_prov=".$registro_x->numero." AND anno_prov=".$registro_x->anno." AND sector=".$_SESSION['SEDE_USUARIO'].";";
		$tablax = mysql_query($consultax);
			if ($registrox = mysql_fetch_object($tablax)) {$interes = $registrox->intereses + $intereses;}
		// --------
		// CLAUSURA
		$clausura = '';
		$consultax  = "SELECT dias FROM vista_clausura_reporte WHERE num_prov=".$registro_x->numero." AND anno_prov=".$registro_x->anno." AND sector=".$_SESSION['SEDE_USUARIO'].";";
		$tablax = mysql_query($consultax);
			if ($registrox = mysql_fetch_object($tablax)) {$clausura = '1';}
		// --------
	 printf ('<tr>
    <td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
    <td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
	<td ><div align="center" class="Estilo7">%s</div></td>
  </tr>',$registro_x->anno,$registro_x->numero,$registro_x->fecha_emision,$registro_x->fecha_notificacion,$registro_x->fecha_culminacion,$registro_x->rif,$registro_x->contribuyente,$registro_x->ciudad,$direccion,$actividad,$registro_x->supervisor,$registro_x->fiscal,number_format(doubleval($impuesto),2,',','.'),number_format(doubleval($sancion),2,',','.'),number_format(doubleval($interes),2,',','.'),$clausura,$registro_x->programa,$registro_x->tributos,$anulada,$especial[$registro_x->Especial]);
  	}
  
  ?>
</table>
    <p>&nbsp;</p>
