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
	// LOGO
	$this->Image('../../imagenes/logo.jpeg',17,8,55);
	
	////////// REGION DE EMISION
	$consulta_x = "SELECT Nombre FROM z_region;";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$Region=$registro_x->Nombre;
	// ---------------------
	$meses=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);
	//---------------
	$this->SetXY(0,15);
	//$this->ln(10);
	$this->SetFont('Times','BIU',12);
	$this->Cell(130,6,'',$linea_muestra,'L');
	//$this->Cell(20,6,'Timbre Fiscal',$linea_muestra,'L');
	$this->Cell(20,6,'Fecha:',$linea_muestra,'L');
	$this->SetFont('Times');
	$this->Cell(20,6,date("d") . " de " . ($meses[(date("m")-1)]) . " de " . date("Y"),$linea_muestra,'L');
	//---------------
	
	list($mes,$anno)=explode('/',$_SESSION['var1']);

	//---------------
	$this->ln(18);
	$this->SetFont('Times','B',14);
	$this->MultiCell(0,6,'RESUMEN DE SALIDAS '.strtoupper($meses[($mes-1)]).' '.$anno,$linea_muestra,'C',0);
	//---------------
	
	//---------------
	$this->SetFont('Times','B',12);
	$this->MultiCell(0,6,'DIV. DE RECAUDACIÓN - AREA DE TIMBRE FISCAL',$linea_muestra,'C');
	//---------------

	//---------------
	$this->SetFont('Times','B',12);
	$this->MultiCell(0,6,$Region,$linea_muestra,'C');
	$this->ln(8);
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
	$this->Cell(360,10,sistema().' '.$s.'/{nb}',0,0,'C');
	}
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,35,17);
$pdf->SetAutoPageBreak(1,20);
$pdf->SetFillColor(210);
//---------------
$pdf->AddPage();
// -------------------------------------------------------------------------------
$linea_muestra = 0;
$linea_fija = 1;
//-------------
list($mes,$anno)=explode('/',$_SESSION['var1']);
//---------------
$a = 35;
$b = 70;
$c = 25;
$d = 20;
$e = 0;
//---------------
$pdf->SetFont('Times','B',12);
$pdf->Cell($a,6,'Codigo',$linea_fija,0,'C',1);
$pdf->Cell($b,6,'Descripcion',$linea_fija,0,'C',1);
$pdf->Cell($c,6,'Cantidad',$linea_fija,0,'C',1);
$pdf->Cell($d,6,'Valor',$linea_fija,0,'C',1);
$pdf->Cell($e,6,'SubTotal',$linea_fija,0,'C',1);
$pdf->ln(7);
//---------------

//--------- PARA RELLENAR EL CUADRO

$x=$pdf->GetX();
$y=$pdf->GetY();
$i=1;

$consulta = "SELECT timbre_inv.codigo, timbre_inv.descripcion, timbre_inv.precio, SUM(timbre_ventas_detalle.cantidad) AS cantidad FROM timbre_inv INNER JOIN timbre_ventas_detalle ON timbre_ventas_detalle.codigo = timbre_inv.codigo INNER JOIN timbre_ventas ON timbre_ventas.numero = timbre_ventas_detalle.numero_venta WHERE (((Month(fecha))=".$mes.") AND ((Year(fecha))=".$anno.")) GROUP BY timbre_inv.codigo, timbre_inv.descripcion, timbre_inv.precio, timbre_inv.indice ORDER BY timbre_inv.indice;";
$tabla = mysql_query($consulta);
//-------------------------
while ($registro = mysql_fetch_object($tabla))
	{
	$pdf->SetFont('Times','B',10);
	if ($i%2==0){    $color='1';	}else{    $color='0';}
	//-------------------------------------------------------------------------------------------
	$pdf->Cell($a,6,$registro->codigo,$linea_muestra,0,'L',$color);
	$pdf->Cell($b,6,$registro->descripcion,$linea_muestra,0,'L',$color);
	$pdf->Cell($c,6,number_format(doubleval($registro->cantidad),0,',','.'),$linea_muestra,0,'C',$color);
	$pdf->Cell($d,6,number_format(doubleval($registro->precio),2,',','.'),$linea_muestra,0,'C',$color);
	$pdf->Cell($e,6,number_format(doubleval($registro->cantidad*$registro->precio),2,',','.'),$linea_muestra,0,'C',$color);
	//------------------------------------------------------------------------------------------
	$pdf->ln(6.5); $i ++;
	//-------------------------------------------------------------------------------------------
	}
// ----------------

$pdf->Output();

?>