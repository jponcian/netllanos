<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

	include "../../conexion.php";
	include "../../funciones/auxiliar_php.php";


	$Titulos = array('','RELACIÓN DE CERTIFICADOS DE SOLVENCIA EMITIDOS','RELACIÓN DE CERTIFICADOS DE LIBERACIÓN EMITIDOS');
		
	$Titulo = $Titulos[$_SESSION['VARIABLE']] . '	DESDE : ' . voltea_fecha($_SESSION['INICIO']) . ' HASTA: ' . voltea_fecha($_SESSION['FIN']) ;
	
	//BUSCAMOS LA REGION
	$consulta_x = "SELECT nombre FROM z_region";
	$tabla_x = mysql_query($consulta_x);
	$regstro_x = mysql_fetch_object($tabla_x);
	$Region = $regstro_x->nombre;
	
	//BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
	$consulta_x = "SELECT * FROM z_sectores WHERE id_sector=".$_SESSION['SEDE'];
	$tabla_x = mysql_query($consulta_x);
	$regstro_x = mysql_fetch_object($tabla_x);
	$area = $regstro_x->tipo_division;
	$dependencia = $regstro_x->nombre;
		
	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.mayuscula(buscar_region());
	
	$Sede ='DEPENDENCIA: ' . strtoupper($dependencia);
	
?>
<style type="text/css">
<!--
.Estilo5 {font-size: 10px; }
.Estilo7 {font-size: 12px; font-weight: bold; }
-->
</style>

<table width="90%" border="1">
   	<tr>
	    <td><div align="center"><strong><?php echo $Gerencia; ?></strong></div></td>
	</tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Sede; ?></strong></div></td>
	</tr>
        </tr>
		<tr>
	    <td><div align="center"><strong><?php echo strtoupper($area.' de Sucesiones'); ?></strong></div></td>
	</tr>
        </tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Titulo; ?></strong></div></td>
	</tr>	
</table>
	
<table width="90%" border="1">
   <tr>
    <td><div align="center" class="Estilo7"><strong>N&deg;</strong></div></td>
    <td><div align="center" class="Estilo7">Rif</div></td>
    <td><div align="center" class="Estilo7">Contribuyente</div></td>
    <td><div align="center" class="Estilo7">Número de Declaracion Sucesoral</div></td>
    <td><div align="center" class="Estilo7">Fecha de la Declaracion</div></td>
     <td><div align="center" class="Estilo7">Impuesto</div></td>
   <td><div align="center" class="Estilo7">Multa</div></td>
    <td><div align="center" class="Estilo7">Intereses</div></td>
    <td><div align="center" class="Estilo7">Numero de Certificado</div></td>
    <td><div align="center" class="Estilo7">Fecha de Emisión</div></td>
  </tr>
 <?php

		
	if ($_SESSION['VARIABLE']==1)
		{
		$Sentencia = "SELECT * FROM vista_re_sucesiones_solvencias ";
		}
	if ($_SESSION['VARIABLE']==2)
		{
		$Sentencia = "SELECT * FROM vista_re_sucesiones_liberacion ";
		}
	
	$Filtro= "WHERE fecha_emision BETWEEN '".($_SESSION['INICIO'])."' AND '".($_SESSION['FIN'])."' AND sector= ".$_SESSION['SEDE']."; ";	
	$consulta_x = $Sentencia . $Filtro . $origen . $Orden . ";";
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
	
	$txt= $registro_x->rif;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';

	$txt= $registro_x->contribuyente;
	echo '<td><div align="left" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
			
	$txt= $registro_x->declaracion;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= voltea_fecha($registro_x->fecha_declaracion);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= 0;
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	$txt = resumen_multa($registro_x->rif,0);
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	$txt= resumen_interes($registro_x->rif,0);
	echo '<td><div align="right" class="Estilo5">';
	echo formato_moneda($txt);
	echo '</div></td>';
	
	$txt= $registro_x->certificado;
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
	
	$txt= voltea_fecha($registro_x->fecha_emision);
	echo '<td><div align="center" class="Estilo5">';
	echo $txt;
	echo '</div></td>';
			
	echo ' </tr>';
	}
  ?>
</table>