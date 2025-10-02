<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

include "../../conexion.php";
include "../../funciones/auxiliar_php.php";

	$Titulos = array('','AUXILIAR CONTABLE TRIBUTOS INTERNOS','IMPUESTO SOBRE LA RENTA','LIQUIDACIONES NOTIFICADAS ','LOS LLANOS');
	
	list($anno1,$mes1,$dia1)=explode('/',$_SESSION['FECHA1']);
	list($anno2,$mes2,$dia2)=explode('/',$_SESSION['FECHA2']);
	
	
	$Titulo = $Titulos[3] . '	DESDE : ' . $dia1.'/'. $mes1 .'/'. $anno1 . ' HASTA: ' . $dia2.'/'. $mes2 .'/'. $anno2 ;

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
	$texto1	= 'Sujetos Pasivos Especiales';
	//---------

	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.strtoupper($Region);
	
	$Sede ='DEPENDENCIA: ' . strtoupper($dependencia);
	
?>
<style type="text/css">
<!--
.Estilo5 {font-size: 12px; }
.Estilo7 {font-size: 14px; font-weight: bold; }
-->
</style>

<table width="100%" border="0">
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
	    <td><div align="center"><strong><?php echo $Titulos[1]; ?></strong></div></td>
	</tr>	
		<tr>
	    <td><div align="center"><strong><?php echo $Titulos[2]; ?></strong></div></td>
	</tr>	
		<tr>
	    <td><div align="center"><strong><?php echo $Titulo; ?></strong></div></td>
	</tr>	
</table>
	<p></p>

	<table width="100%" border="1" cellpadding="1" cellspacing="0">
   <tr height="40px" bgcolor="#FF0000" style="color:#FFFFFF">
    <td><div align="center" class="Estilo7"><strong>N&deg;</strong></div></td>
    <td><div align="center" class="Estilo7">Rif</div></td>
    <td><div align="center" class="Estilo7">Contribuyente</div></td>
    <td><div align="center" class="Estilo7">N&deg; Resoluci&oacute;n</div></td>
    <td><div align="center" class="Estilo7">Forma</div></td>
    <td><div align="center" class="Estilo7">N&deg; Liquidacion</div></td>
    <td><div align="center" class="Estilo7">Fecha Liq.</div></td>
    <td><div align="center" class="Estilo7">N&deg; Documento</div></td>
    <td><div align="center" class="Estilo7">Fecha de Not.</div></td>
    <td><div align="center" class="Estilo7">Fecha para Pagar</div></td>
    <td><div align="center" class="Estilo7">Multas y Recargos</div></td>
    <td><div align="center" class="Estilo7">Intereses Moratorios</div></td>
    <td><div align="center" class="Estilo7">Total Planilla Liquidada</div></td>
    <td><div align="center" class="Estilo7">Fecha Pago</div></td>
    <td><div align="center" class="Estilo7">Cod. Banco</div></td>
    <td><div align="center" class="Estilo7">Cod. Agencia</div></td>
    <td><div align="center" class="Estilo7">N&deg; Documento</div></td>
    <td><div align="center" class="Estilo7">Monto Racaudado</div></td>
  </tr>
 <?php
 
	$i=0;
	
	// FILTROS DEL REPORTE
	$Filtro = " WHERE sector = ".$_SESSION['OSEDE']." AND fecha_not>='".$_SESSION['FECHA1']."' and fecha_not<='".$_SESSION['FECHA2']."'";
	$Orden = " AND origen_liquidacion=2 AND fecha_not IS NOT NULL ORDER BY fecha_not ASC";
					
	// --------- FIN
	
	$Sentencia = "SELECT ce_pagos.Rif AS Rif, contribuyentes.contribuyente AS contribuyente, ce_cal_tip_obligaciones.Tipo, liquidacion.liquidacion, liquidacion.sector, liquidacion.origen_liquidacion, liquidacion.anno_expediente, liquidacion.num_expediente, liquidacion.fecha_impresion, ce_pagos.Numero, liquidacion.fecha_not, liquidacion.monto_bs, liquidacion.concurrencia, liquidacion.especial, ce_pagos.Fecha_Pago, a_banco.banco, a_agencia.id_agencia_especial, ce_pagos.Monto AS monto_recaudado, ce_cal_tip_obligaciones.Numero AS forma, liquidacion.id_tributo2, liquidacion.id_tributo FROM ((((ce_pagos JOIN liquidacion ON (((liquidacion.rif = ce_pagos.Rif) AND (ce_pagos.Numero = liquidacion.planilla_notificacion)))) JOIN a_agencia ON ((a_agencia.id_agencia = ce_pagos.Agencia))) JOIN a_banco ON ((a_banco.id_banco = a_agencia.id_banco))) JOIN contribuyentes ON ((ce_pagos.Rif = contribuyentes.rif))) INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto WHERE ce_pagos.Fecha_Pago BETWEEN '2015-01-01' AND '2016-03-17' AND ce_pagos.Tipo_Impuesto BETWEEN 8 AND 9"; 

	$consulta_x = $Sentencia . $Filtro . $Orden . ";";
	
	$tabla_x = mysql_query ($Sentencia);

	while ($registro_x = mysql_fetch_object($tabla_x))
	{
	// CONTENIDO TABLA
	
	echo '<tr>';
	//COLUMNA 1
	$i= $i +1;
	$txt= $i;
	
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 2
	$txt= $registro_x->Rif;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 3
	$txt= $registro_x->contribuyente;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
		
	list ($resolucion, $fecha) = funcion_resolucion($registro_x->sector, $registro_x->origen_liquidacion, $registro_x->anno_expediente, $registro_x->num_expediente);
	
	//COLUMNA 4
	$txt= $resolucion;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	//COLUMNA 5
	$txt= substr($registro_x->Tipo,6,3);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 6
	$txt= substr($registro_x->liquidacion,4,15);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 7
	$txt= voltea_fecha($registro_x->fecha_impresion);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 8
	$txt= $registro_x->Numero;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 9
	$txt= voltea_fecha($registro_x->fecha_not);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 10
	$txt= voltea_fecha($registro_x->fecha_not); //+ 25 DIAS
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	if ($registro_x->forma == 8)
	{
		$multa = $registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial;
		$intereses = 0;
	} else {
		$multa = 0;
		$intereses = $registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial;
	}
	
	//COLUMNA 11
	$txt= $multa;
	echo '<td align="right"><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	//COLUMNA 12
	$txt= $intereses;
	echo '<td align="right"><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	//COLUMNA 13
	$txt= $multa + $intereses;
	echo '<td align="right"><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';

	//COLUMNA 14
	$txt= $registro_x->Fecha_Pago;
	echo '<td><div align="right" class="Estilo5">';
	echo voltea_fecha($txt);
	echo '</div></td>';

	//COLUMNA 15
	$txt= $registro_x->banco;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	//COLUMNA 16
	$txt= $registro_x->id_agencia_especial;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	//COLUMNA 17
	$txt= $registro_x->Numero;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
			
	//COLUMNA 18
	$txt= formato_moneda($registro_x->monto_recaudado);
	echo '<td><div align="right" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	echo ' </tr>';
	}
  ?>
</table>

<?php 


//FUNCION PARA BUSCAR FECHA DE PAGO DE LA PLANILLA EN ce_pagos

?>

