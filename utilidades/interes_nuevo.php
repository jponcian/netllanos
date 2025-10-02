<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

//if ($_SESSION['VERIFICADO'] != "SI") { 
//    header ("Location: index.php?errorusuario=val"); 
//    exit(); 
//	}
//----------
?>
<html>
<head>
  <title>Interes</title>
  <style type="text/css">
<!--
.Estilomenun {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
body {
	background-image: url();
}
.Estilo1 {
	font-size: 18px;
	font-weight: bold;
	color:#FF0000;
}
.Estilo2 {color: #FFFFFF}
-->
  </style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</head>

<p>
  <?php include "../titulo.php";?>
</p>
<div align="center"><p align="center">
<?php
	include "menu.php";
?>
</div>
<form id="form1" name="form1" method="post" action="">
  <p>
    <label></label></p>
  <table width="296" border="1" align="center">
    <tr>
      <td width="161"><div align="center">Fecha de Vencimiento</div></td>
      <td width="119"><div align="center">
        <input onClick='javascript:scwShow(this,event);' type="text" name="OFECHA_VEN" size="8" readonly="true" value="<?php if(isset($_POST['OFECHA_VEN'])) {echo $_POST['OFECHA_VEN'];} ?>">
      </div></td>
    </tr>
	 <tr>
      <td width="161"><div align="center">Fecha de Pago</div></td>
      <td width="119"><div align="center">
        <input type="text" name="OFECHA_PAGO" size="8" readonly="true" onclick='javascript:scwShow(this,event);' value="<?php if(isset($_POST['OFECHA_PAGO'])) {echo $_POST['OFECHA_PAGO'];} ?>">
      </div></td>
    </tr>
	 <tr>
      <td width="161"><div align="center">Monto Pagado</div></td>
      <td width="119"><div align="center">
        <input type="text" size="12" name="OMONTO" value="<?php echo $_POST['OMONTO']; ?>"/>
      </div></td>
    </tr>
  </table>
  
   <p align="center">
     <input type="submit" class="boton" name="CMDCALCULAR" value="Calcular" />
   </p>
  <table width="769" border="1" align="center">
    <tr>
      <td width="150"><div align="center">Periodo</div></td>
      <td width="94"><div align="center">Fecha de Vencimiento</div></td>
	  <td width="83"><div align="center">Fecha de Pago</div></td>
	  <td width="60"><div align="center">D&iacute;as de Mora </div></td>
      <td width="96"><div align="center">Tributo </div></td>
	  <td width="41"><div align="center">Tasa</div></td>
	  <td width="86"><div align="center">Inter&eacute;s Moratorio Mesual </div></td>
	  <td width="95"><div align="center">Inter&eacute;s Acumulado </div></td>
    </tr>
  
 
  
<?php

if ($_POST['CMDCALCULAR']=='Calcular')
{
$Monto = $_POST['OMONTO'];

// CALCULO DE LOS DIAS DE MORA
list($dia,$mes,$anno)=explode('/',$_POST['OFECHA_PAGO']);
$fecha_pago = mktime(0,0,0,$mes,$dia,$anno);

list($dia,$mes,$anno)=explode('/',$_POST['OFECHA_VEN']);
$fecha_vencimiento = mktime(0,0,0,$mes,$dia,$anno);

$Dias = $fecha_pago - $fecha_vencimiento;
			
// CALCULO DEL INTERES-------------------------------------------------------------------
$fecha_vencimiento = $fecha_vencimiento + 86400;
$interes = 0;
$Dias = 0;
$MES = date('m',$fecha_vencimiento);
$AÑO = date('Y',$fecha_vencimiento);

$interes_acum = 0;

// CONSULTA DE LAS TAZAS
$consulta_y = "SELECT * FROM a_tasa_interes ORDER BY anno";
$tabla_y = mysql_query ($consulta_y);
$registro_y = mysql_fetch_object($tabla_y);
// FIN CONSULTA DE LAS TAZAS

$mes_letras=array(0,Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);

while ($fecha_vencimiento <= $fecha_pago)
	{
	while ($registro_y->anno < date('Y',$fecha_vencimiento)) // BUSQUEDA DEL AÑO
		{
		$registro_y = mysql_fetch_object($tabla_y);
		$tazas = array('0',$registro_y->enero,$registro_y->febrero,$registro_y->marzo,$registro_y->abril,$registro_y->mayo,$registro_y->junio,$registro_y->julio,$registro_y->agosto,$registro_y->septiembre,$registro_y->octubre,$registro_y->noviembre,$registro_y->diciembre);	
		} 													// FIN DE LA BUSQUEDA DEL AÑO
	
	$tasa = $tazas[number_format(doubleval(date('m',$fecha_vencimiento)),0)];
	$interes = $interes + (($Monto * ($tasa * 1.20)) / 36000) ;
	
	// IMPRIMIR SI CAMBIA EL MES
	if ($MES <> date('m',$fecha_vencimiento))
		{
		$interes_acum = $interes_acum + $interes;
		//------------------------------------------------------------------------
		echo '<tr>
		  <td><div align="left">'.$mes_letras[number_format(doubleval($MES),0,',','.')].' - '.$AÑO.'</div></td>
		  <td><div align="center">'.$_POST['OFECHA_VEN'].'</div></td>
		  <td><div align="center">'.$_POST['OFECHA_PAGO'].'</div></td>
		  <td><div align="center">'.$Dias.'</div></td>
		  <td><div align="right">'.formato_moneda($Monto).'</div></td>
		  <td><div align="center">'.formato_moneda($tasa * 1.20).'</div></td>
		  <td><div align="right">'.formato_moneda($interes).'</div></td>
		  <td><div align="right">'.formato_moneda($interes_acum).'</div></td>
    	</tr>';
		//------------------------------------------------------------------------
		$Dias = 0;
		$MES = date('m',$fecha_vencimiento);
		$AÑO = date('Y',$fecha_vencimiento);
		$interes = 0;
		}
	
	$fecha_vencimiento = $fecha_vencimiento + 86400;
	$Dias =  $Dias +1;
	}
	
	//-------------- ULTIMA LINEA
	$interes_acum = $interes_acum + $interes ;
	//------------------------------------------------------------------------
	echo '<tr>
		  <td><div align="left">'.$mes_letras[number_format(doubleval($MES),0,',','.')].' - '.$AÑO.'</div></td>
		  <td><div align="center">'.$_POST['OFECHA_VEN'].'</div></td>
		  <td><div align="center">'.$_POST['OFECHA_PAGO'].'</div></td>
		  <td><div align="center">'.$Dias.'</div></td>
		  <td><div align="right">'.formato_moneda($Monto).'</div></td>
		  <td><div align="center">'.formato_moneda($tasa * 1.20).'</div></td>
		  <td><div align="right">'.formato_moneda($interes).'</div></td>
		  <td><div align="right">'.formato_moneda($interes_acum).'</div></td>
    	</tr>';
	//------------------------------------------------------------------------
	$Dias = 0;
	$MES = date('m',$fecha_vencimiento);
	$AÑO = date('Y',$fecha_vencimiento);
	$interes = 0;
	//-----------------
// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------

}
?>

 </table>
 <p align="center">
   <label></label>
 </p>
 
</form>
<p>
  <?php include "../pie.php";?>
</p>
<p>&nbsp;</p>