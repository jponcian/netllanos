<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

include "../../conexion.php";
include('../../funciones/auxiliar_php.php');

	$Titulos = array('','Control Bancario');

	$mes=array(0,Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);
	
	$Titulo = '	MES : ' . mb_convert_case($_SESSION['meses_anno'][$_SESSION['VARIABLE']], MB_CASE_UPPER, "ISO-8859-1") . ' AÑO: ' . $_SESSION[VARIABLE2] ;
	
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
.Estilo5 {font-size: 10px; }
.Estilo7 {font-size: 12px; font-weight: bold; }
-->
</style>

<table width="90%" border="0" align="center">
   	<tr>
	    <td><div align="center"><strong>DIVISI&Oacute;N DE CONTRIBUYENTES PASIVOS ESPECIALES </strong></div></td>
	</tr>
        </tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Gerencia; ?></strong></div></td>
	</tr>
        </tr>
		<tr>
	    <td><div align="center"><strong><?php echo $Sede; ?></strong></div></td>
	</tr>	
	<tr>
	    <td><div align="center"><strong><?php echo $Titulo; ?></strong></div></td>
	</tr>
</table>
	<p></p>
	
    <p>
      <?php
	
// ------------------------------------------------------------------------
$consulta_b = "SELECT a_banco.banco, a_banco.Descripcion FROM ce_pagos, a_banco, a_agencia WHERE a_agencia.id_agencia = ce_pagos.Agencia AND ce_pagos.Sector = ".$_SESSION['SEDE']." AND Month(ce_pagos.Fecha_Pago)=".$_SESSION['VARIABLE']." AND Year(ce_pagos.Fecha_Pago)=".$_SESSION['VARIABLE2']." AND a_banco.id_banco = a_agencia.id_banco GROUP BY a_agencia.id_banco ORDER BY a_agencia.id_banco ASC";
$tabla_b = mysql_query($consulta_b);
while ($registro_b = mysql_fetch_object($tabla_b)) // ------------ INICIO DE LA CONSULTA DE LOS BANCOS UTILIZADOS
{
$banco = $registro_b->banco;
// ------------------------------------------------------------------------

// ------------------------------------------------------------------------
echo '<table width="70%" border="0" align="center" >
<tr>
<td><div align="center">'.$registro_b->Descripcion.'</div>
<table width="100%" border="1">
<tr>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7"><strong>Dia</strong></div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Efectivo y Cheques</div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Bonos</div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Total</div></td>
</tr>';
// ------------------------------------------------------------------------

	// ---------- COMIENZO DE LAS TABLAS
	
	$consulta_x = "SELECT date_format(ce_pagos.Fecha_Pago,'%d') AS Fecha, sum(ce_pagos.Monto) AS SumaDeMonto, a_banco.banco, a_banco.Descripcion, ce_pagos.Sector FROM ce_pagos INNER JOIN a_agencia ON a_agencia.id_agencia = ce_pagos.Agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco WHERE ce_pagos.Sector = ".$_SESSION['SEDE']." AND Month(ce_pagos.Fecha_Pago)=".$_SESSION['VARIABLE']." AND Year(ce_pagos.Fecha_Pago)=".$_SESSION['VARIABLE2']." AND a_banco.banco=".$banco." GROUP BY ce_pagos.Fecha_Pago, a_banco.banco, a_banco.Descripcion, ce_pagos.Sector ORDER BY a_banco.banco ASC, ce_pagos.Fecha_Pago ASC";
	$tabla_x = mysql_query($consulta_x);

	$i=1;
	
while ($i <=16)	
{
	while ($registro_x = mysql_fetch_object($tabla_x))
	{
	if ($registro_x->Fecha<=16)
		{
		while ($registro_x->Fecha>$i)
			{
				echo '<tr>
				<td ><div align="center" class="Estilo7"><strong>'.sprintf("%002s",$i).'</strong></div></td>
				<td ><div align="right" class="Estilo7">0</div></td>
				<td ><div align="center" class="Estilo7">0</div></td>
				<td ><div align="right" class="Estilo7">0</div></td>
			  	</tr>';
			  	$i++;
			}
			echo '<tr>
				<td ><div align="center" class="Estilo7"><strong>'.$registro_x->Fecha.'</strong></div></td>
				<td ><div align="right" class="Estilo7">'.number_format(doubleval($registro_x->SumaDeMonto),2,',','.').'</div></td>
				<td ><div align="center" class="Estilo7">0</div></td>
				<td ><div align="right" class="Estilo7">'.number_format(doubleval($registro_x->SumaDeMonto),2,',','.').'</div></td>
			  </tr>';
		$i++;
		}
	}
if ($i<17)
	{
	echo '<tr>
	<td ><div align="center" class="Estilo7"><strong>'.sprintf("%002s",$i).'</strong></div></td>
	<td ><div align="right" class="Estilo7">0</div></td>
	<td ><div align="center" class="Estilo7">0</div></td>
	<td ><div align="right" class="Estilo7">0</div></td>
	</tr>';
	$i++;
	}	
}
  
  	//-------- FIN DE LAS TABLAS

// ------------------------------------------------------------------------
echo '</table>
</td>
<td>
<table width="100%" border="1">
<tr>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7"><strong>Dia</strong></div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Efectivo y Cheques </div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Bonos</div></td>
<td bgcolor="#CCCCCC"><div align="center" class="Estilo7">Total</div></td>
</tr>';
// ------------------------------------------------------------------------

	// ---------- COMIENZO DE LAS TABLAS
	
	$consulta_x = "SELECT date_format(ce_pagos.Fecha_Pago,'%d') AS Fecha, sum(ce_pagos.Monto) AS SumaDeMonto, a_banco.banco, a_banco.Descripcion, ce_pagos.Sector FROM ce_pagos INNER JOIN a_agencia ON a_agencia.id_agencia = ce_pagos.Agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco WHERE ce_pagos.Sector = ".$_SESSION['SEDE']." AND Month(ce_pagos.Fecha_Pago)=".$_SESSION['VARIABLE']." AND Year(ce_pagos.Fecha_Pago)=".$_SESSION[VARIABLE2]." AND a_banco.banco=".$banco." GROUP BY ce_pagos.Fecha_Pago, a_banco.banco, a_banco.Descripcion, ce_pagos.Sector ORDER BY a_banco.banco ASC, ce_pagos.Fecha_Pago ASC";
	$tabla_x = mysql_query($consulta_x);

	$i=17;

	while ($i <=31)
	{
		while ($registro_x = mysql_fetch_object($tabla_x))
		{
		if ($registro_x->Fecha>16)
			{
			while ($i < $registro_x->Fecha)
				{
					echo '<tr>
					<td ><div align="center" class="Estilo7"><strong>'.sprintf("%002s",$i).'</strong></div></td>
					<td ><div align="right" class="Estilo7">0</div></td>
					<td ><div align="center" class="Estilo7">0</div></td>
					<td ><div align="right" class="Estilo7">0</div></td>
					</tr>';
					$i++;
				}
				echo '<tr>
					<td ><div align="center" class="Estilo7"><strong>'.$registro_x->Fecha.'</strong></div></td>
					<td ><div align="right" class="Estilo7">'.number_format(doubleval($registro_x->SumaDeMonto),2,',','.').'</div></td>
					<td ><div align="center" class="Estilo7">0</div></td>
					<td ><div align="right" class="Estilo7">'.number_format(doubleval($registro_x->SumaDeMonto),2,',','.').'</div></td>
				  </tr>';
			$i++;
			}
		}
		if ($i<32)
			{
			echo '<tr>
				<td ><div align="center" class="Estilo7"><strong>'.sprintf("%002s",$i).'</strong></div></td>
				<td ><div align="right" class="Estilo7">0</div></td>
				<td ><div align="center" class="Estilo7">0</div></td>
				<td ><div align="right" class="Estilo7">0</div></td>
				</tr>';
			}
		$i++;
	}
  	//-------- FIN DE LAS TABLAS

// ------------------------------------------------------------------------
echo '</table></td></tr></table>';
// ------------------------------------------------------------------------
} // --------------- FIN DE LOS BANCOS
//----------
include "../../desconexion.php";
//----------

?>
    </p>
    <p>&nbsp; </p>
