<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
 
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
{
	//---------- ENCABEZADO
	function Header()
	{  
	// -------------------------------------------------------------------------------
	$linea_muestra = 0;
	$linea_fija = 1;
	//-------------
	// LOGO
	$this->Image('../../imagenes/logo.jpeg',17,8,55);
	
	////////// REGION DE EMISION
	$consulta_x = "SELECT Nombre FROM z_region;";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$Region=$registro_x->Nombre;
	// ---------------------
	$meses=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);
	list($mes,$anno)=explode('/',$_SESSION['var1']);
	//---------------
	$this->SetXY(0,10);
	$this->SetFont('Times','B',10);
	$this->Cell(0,6,'Fecha: '.date("d") . " de " . ($meses[(date("m")-1)]) . " de " . date("Y"),0,$linea_muestra,'R');
	//---------------
	
	//---------------
	$this->ln(0);
	$this->SetFont('Times','B',12);
	$this->MultiCell(0,6,'7.12 Movimientos de Especies Fiscales',$linea_muestra,'C',0);
	//---------------
	
	//---------------
	$this->SetFont('Times','B',10);
	$this->MultiCell(0,6,'Div. de Recaudación – Area de Timbre Fiscal',$linea_muestra,'C');
	//---------------

	//---------------
	$this->SetFont('Times','B',9);
	$this->MultiCell(0,6,ucfirst($Region),$linea_muestra,'C');
	$this->ln(2);
	//---------------

	//---------------
	$this->SetFont('Times','B',10);
	$this->MultiCell(0,6,'Mes: '. strtoupper($meses[(abs($mes))-1]).' '.$anno ,$linea_muestra,'L');
	$this->ln(2);
	//---------------
	
	//--------------
	$a=30;
	$b=15;
	$c=42;
	$d=42;
	$e=42;
	$f=42;
	$g=42;
	$h=21;
	$j=21;
	//--------------

	//---------------
	$this->SetFont('Times','B',9);
	$this->Cell($a,12,'Codigo',$linea_fija,0,'C',1);
	$this->Cell($b,12,'Valor',$linea_fija,0,'C',1);
	$this->Cell($c,6,'Existencia Inicial',$linea_fija,0,'C',1);
	$this->Cell($d,6,'Ingresos del Mes',$linea_fija,0,'C',1);
	$this->Cell($e,6,'Total',$linea_fija,0,'C',1);
	$this->Cell($f,6,'Ventas del Mes',$linea_fija,0,'C',1);
	$this->Cell($g,6,'Existencia Final',$linea_fija,0,'C',1);
	$this->ln(6);
	$this->Cell(($a+$b),6,'',$linea_muestra,0,'C',0);
	$this->Cell($h,6,'Cantidad',$linea_fija,0,'C',1);
	$this->Cell($j,6,'Monto',$linea_fija,0,'C',1);
	$this->Cell($h,6,'Cantidad',$linea_fija,0,'C',1);
	$this->Cell($j,6,'Monto',$linea_fija,0,'C',1);
	$this->Cell($h,6,'Cantidad',$linea_fija,0,'C',1);
	$this->Cell($j,6,'Monto',$linea_fija,0,'C',1);
	$this->Cell($h,6,'Cantidad',$linea_fija,0,'C',1);
	$this->Cell($j,6,'Monto',$linea_fija,0,'C',1);
	$this->Cell($h,6,'Cantidad',$linea_fija,0,'C',1);
	$this->Cell($j,6,'Monto',$linea_fija,0,'C',1);
	$this->ln(6);
	//---------------
	$this->Cell(($a+$b),6,'TOTALES',$linea_fija,0,'C',1);
	
	//------------ TOTAL EN EL INVENTARIO
	$consultax = "SELECT Sum(timbre_inv_detallado_mensual.cantidad) AS cantidad, Sum(timbre_inv.precio * timbre_inv_detallado_mensual.cantidad) AS monto FROM timbre_inv_detallado_mensual INNER JOIN timbre_inv ON timbre_inv_detallado_mensual.codigo = timbre_inv.codigo GROUP BY timbre_inv_detallado_mensual.fecha, timbre_inv.grupo HAVING (((timbre_inv_detallado_mensual.fecha)='".$_SESSION['var1']."') AND ((timbre_inv.grupo)='TIMBRES'));";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax))
		{
		//------------------------
		$cantidad0 = $registrox->cantidad;
		$monto0 = $registrox->monto;
		//------------------------
		}
	else
		{
		//------------------------
		$cantidad0 = 0;
		$monto0 = 0;
		//------------------------
		}
	
	//------------ TOTAL EN INGRESOS
	$consultax = "SELECT Sum(timbre_ingresos_detalle.cantidad) AS cantidad, Sum(timbre_inv.precio*timbre_ingresos_detalle.cantidad) AS monto FROM (timbre_ingresos INNER JOIN timbre_ingresos_detalle ON timbre_ingresos.numero = timbre_ingresos_detalle.numero_ingreso) INNER JOIN timbre_inv ON timbre_ingresos_detalle.codigo = timbre_inv.codigo WHERE (((Month(fecha))=".$mes.") AND ((Year(fecha))=20".$anno.") AND ((timbre_inv.grupo)='TIMBRES'));";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax))
		{
		//------------------------
		$cantidad1 = $registrox->cantidad;
		$monto1 = $registrox->monto;
		//------------------------
		}
	else
		{
		//------------------------
		$cantidad1 = 0;
		$monto1 = 0;
		//------------------------
		}
			
		//---VENTAS DEL MES--------
	$consultax = "SELECT Sum(timbre_ventas_detalle.cantidad) AS cantidad, Sum(timbre_inv.precio*timbre_ventas_detalle.cantidad) AS monto FROM (timbre_ventas INNER JOIN timbre_ventas_detalle ON timbre_ventas.numero = timbre_ventas_detalle.numero_venta) INNER JOIN timbre_inv ON timbre_ventas_detalle.codigo = timbre_inv.codigo WHERE (((Month(fecha))=".$mes.") AND ((Year(fecha))=20".$anno.") AND ((timbre_inv.grupo)='TIMBRES'));";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax))
		{
		//------------------------
		$cantidad2 = $registrox->cantidad;
		$monto2 = $registrox->monto;
		//------------------------
		}
	else
		{
		//------------------------
		$cantidad2 = 0;
		$monto2 = 0;
		//------------------------
		}


	$this->Cell($h,6,number_format(doubleval($cantidad0),0,',','.'),$linea_fija,0,'C',1);
	$this->Cell($j,6,number_format(doubleval($monto0),2,',','.'),$linea_fija,0,'C',1);
	
	$this->Cell($h,6,number_format(doubleval($cantidad1),0,',','.'),$linea_fija,0,'C',1);
	$this->Cell($j,6,number_format(doubleval($monto1),2,',','.'),$linea_fija,0,'C',1);
	
	$this->Cell($h,6,number_format(doubleval($cantidad0+$cantidad1),0,',','.'),$linea_fija,0,'C',1);
	$this->Cell($j,6,number_format(doubleval($monto0+$monto1),2,',','.'),$linea_fija,0,'C',1);
	
	$this->Cell($h,6,number_format(doubleval($cantidad2),0,',','.'),$linea_fija,0,'C',1);
	$this->Cell($j,6,number_format(doubleval($monto2),2,',','.'),$linea_fija,0,'C',1);
	
	$this->Cell($h,6,number_format(doubleval($cantidad0+$cantidad1-$cantidad2),0,',','.'),$linea_fija,0,'C',1);
	$this->Cell($j,6,number_format(doubleval($monto0+$monto1-$monto2),2,',','.'),$linea_fija,0,'C',1);
	$this->ln(6);
	//---------------
	}	
	
	//---------- PIE
	function Footer()
	{    
	$this->SetY(-12);
	$s=$this->PageNo();
	//Arial itálica 8
	$this->SetFont('Times','I',10);
	$this->SetTextColor(120);
	$this->Cell(0,0,sistema().' '.$s.'/{nb}',0,0,'R');
	}
}

// ENCABEZADO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(12,30,12);
$pdf->SetAutoPageBreak(1,20);
$pdf->SetFillColor(210);
//---------------
$pdf->AddPage();
// -------------------------------------------------------------------------------
$linea_muestra = 0;
$linea_fija = 1;
//-------------

//--------- PARA RELLENAR EL CUADRO
$x=$pdf->GetX();
$y=$pdf->GetY();
//--------------

//--------------
$a=30;
$b=15;
$c=42;
$d=42;
$e=42;
$f=42;
$g=42;
$h=21;
$j=21;
//--------------
$pdf->Cell($a+$b+$c+$d+$e+$f+$g,3,'',$linea_fija,0,'C',0);
$pdf->ln(3);
//--------------
list($mes,$anno)=explode('/',$_SESSION['var1']);
//--------------
$consulta = "SELECT codigo, precio FROM timbre_inv WHERE grupo='TIMBRES' GROUP BY codigo ORDER BY indice;";
$tabla = mysql_query($consulta);
//-------------------------
$i=1;
//-----------------
while ($registro = mysql_fetch_object($tabla))
	{
	$pdf->SetFont('Times','',8);
	//-------------- INVENTARIO INICIAL
	$consultax = "SELECT Sum(cantidad) AS cantidad FROM timbre_inv_detallado_mensual WHERE codigo='".$registro->codigo."' AND (((fecha)='".$_SESSION['var1']."'));";
	$tablax = mysql_query($consultax);
	$registrox = mysql_fetch_object($tablax);
	//-------------------------
	$cantidad0 = $registrox->cantidad;
	//------------------------

	//---INGRESOS DEL MES--------
	$consultax = "SELECT Sum(timbre_ingresos_detalle.cantidad) AS cantidad  FROM timbre_ingresos , timbre_ingresos_detalle  WHERE timbre_ingresos.numero = timbre_ingresos_detalle.numero_ingreso and (((Month(fecha))=".$mes.") AND ((Year(fecha))=20".$anno.") AND ((timbre_ingresos_detalle.codigo)='".$registro->codigo."'))"; 
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax))
		{		$cantidad1 = $registrox->cantidad;		}
	else
		{		$cantidad1 = 0;		}	
	//-------------------------
	
	//---VENTAS DEL MES--------
	$consultax = "SELECT Sum(timbre_ventas_detalle.cantidad) AS cantidad FROM timbre_ventas INNER JOIN timbre_ventas_detalle ON timbre_ventas.numero = timbre_ventas_detalle.numero_venta WHERE (((Month(fecha))=".$mes.") AND ((Year(fecha))=20".$anno.") AND ((timbre_ventas_detalle.codigo)='".$registro->codigo."')) GROUP BY timbre_ventas_detalle.codigo;";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax))
		{		$cantidad2 = $registrox->cantidad;		}
	else
		{		$cantidad2 = 0;		}	
	
	//-------------------------
	$pdf->Cell($a,6,$registro->codigo,$linea_fija,0,'L',0);
	$pdf->Cell($b,6,number_format(doubleval(($registro->precio*100000)),2,',','.'),$linea_fija,0,'C',0);
	//------------
	$pdf->Cell($h,6,number_format(doubleval($cantidad0),2,',','.'),$linea_fija,0,'R',0);
	$pdf->Cell($j,6,number_format(doubleval(minimo_soberano(($cantidad0*$registro->precio),$cantidad0)),2,',','.'),$linea_fija,0,'R',0);
	//-------------------------
	$pdf->Cell($h,6,number_format(doubleval($cantidad1),2,',','.'),$linea_fija,0,'R',0);
	$pdf->Cell($j,6,number_format(doubleval(minimo_soberano(($cantidad1*$registro->precio),$cantidad1)),2,',','.'),$linea_fija,0,'R',0);
	//-------------------------
	$pdf->Cell($h,6,number_format(doubleval($cantidad0 + $cantidad1),2,',','.'),$linea_fija,0,'R',0);
	$pdf->Cell($j,6,number_format(doubleval(minimo_soberano((($cantidad0 + $cantidad1) * $registro->precio),($cantidad0 + $cantidad1))),2,',','.'),$linea_fija,0,'R',0);
	//-------------------------
	$pdf->Cell($h,6,number_format(doubleval($cantidad2),2,',','.'),$linea_fija,0,'R',0);
	$pdf->Cell($j,6,number_format(doubleval(minimo_soberano(($cantidad2 * $registro->precio),$cantidad2)),2,',','.'),$linea_fija,0,'R',0);
	//-------------------------
	$pdf->Cell($h,6,number_format(doubleval($cantidad0 + $cantidad1 - $cantidad2),2,',','.'),$linea_fija,0,'R',0);
	$pdf->Cell($j,6,number_format(doubleval(minimo_soberano(($cantidad0 + $cantidad1 - $cantidad2) * $registro->precio,($cantidad0 + $cantidad1 - $cantidad2))),2,',','.'),$linea_fija,0,'R',0);
	//----------------------
	$i++;
	$pdf->ln(6);
	//----------------------
	}

$pdf->Output();

?>