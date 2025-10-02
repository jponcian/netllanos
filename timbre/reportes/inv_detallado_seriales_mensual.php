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
	$mes=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);
	//---------------
	$this->SetXY(0,15);
	//$this->ln(10);
	$this->SetFont('Times','BI',12);
	$this->Cell(120,6,'',$linea_muestra,'L');
	$this->Cell(40,6,'Fecha de Impresión:',$linea_muestra,'L');
	$this->SetFont('Times');
	$this->Cell(20,6,date("d") . " de " . ($mes[(date("m")-1)]) . " de " . date("Y"),$linea_muestra,'L');
	//---------------
	
	//---------------
	$this->ln(18);
	$this->SetFont('Times','B',14);
	$this->MultiCell(0,6,'INVENTARIO '.$_SESSION['var1'].' '.strtoupper($mes[(abs(substr($_SESSION['var2'],0,2)))-1]).' 20'.substr($_SESSION['var2'],3,2),$linea_muestra,'C',0);
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

//---------------
$pdf->SetFont('Times','B',12);
$pdf->Cell(40,6,'Codigo',$linea_fija,0,'C');
$pdf->Cell(90,6,'Descripcion',$linea_fija,0,'C');
$pdf->Cell(25,6,'Cantidad',$linea_fija,0,'C');
$pdf->Cell(25,6,'Valor',$linea_fija,0,'C');
$pdf->ln(8);
//---------------

//--------- PARA RELLENAR EL CUADRO

$x=$pdf->GetX();
$y=$pdf->GetY();
//--------------
list($mes,$anno)=explode('/',$_SESSION['var2']);
//--------------

$consulta = "SELECT timbre_inv_detallado_mensual.codigo, Sum(timbre_inv_detallado_mensual.cantidad) AS cantidad, timbre_inv.descripcion, timbre_inv.precio, timbre_inv.indice, timbre_inv_detallado_mensual.fecha FROM timbre_inv_detallado_mensual INNER JOIN timbre_inv ON timbre_inv_detallado_mensual.codigo = timbre_inv.codigo GROUP BY timbre_inv_detallado_mensual.codigo, timbre_inv.descripcion, timbre_inv.precio, timbre_inv.indice, timbre_inv_detallado_mensual.fecha HAVING (((timbre_inv_detallado_mensual.fecha)='".$mes.'/'.$anno."')) ORDER BY timbre_inv.indice;";
$tabla = mysql_query($consulta);
//-------------------------
while ($registro = mysql_fetch_object($tabla))
	{
	$pdf->SetFont('Times','B',10);
	
	$a = 40;
	$pdf->Cell($a,6,$registro->codigo,$linea_fija,0,'L',1);
	
	$b = 90;
	$pdf->Cell($b,6,$registro->descripcion,$linea_fija,0,'L',1);
	
	$c = 25 ;
	$pdf->Cell($c,6,number_format(doubleval($registro->cantidad),0,',','.'),$linea_fija,0,'C',1);
	
	$d = 25;
	$pdf->Cell($d,6,number_format(doubleval($registro->precio),5,',','.'),$linea_fija,0,'C',1);
		
	$pdf->ln(6.5);
	//------------------------------------------------------------------------------------------
	$pdf->SetFont('Times','',10);	
	$consultax = "SELECT codigo, serial_desde, serial_hasta, cantidad FROM timbre_inv_detallado_mensual WHERE codigo='".$registro->codigo."' AND (((timbre_inv_detallado_mensual.fecha)='".$mes.'/'.$anno."')) ORDER BY serial_desde;"; 
	$tablax = mysql_query($consultax);
	//----------------------
	while ($registrox = mysql_fetch_object($tablax))
		{
		$pdf->Cell($a,6,'------------ Serial ----------->',$linea_muestra,0,'L');
		
		$pdf->Cell($b,6,'Desde el '.number_format(doubleval($registrox->serial_desde),0,',','.').' al '.number_format(doubleval($registrox->serial_hasta),0,',','.'),$linea_muestra,'L');

		$pdf->Cell($c,6,number_format(doubleval($registrox->cantidad),0,',','.'),$linea_muestra,0,'C',1);
		
		$pdf->Cell($d,6,number_format(doubleval($registro->precio),5,',','.'),$linea_muestra,0,'C',1);
				
		// -------	
		$pdf->ln(6);	
		}
	$pdf->ln(2);
	//-------------------------------------------------------------------------------------------
	}
// ----------------

$pdf->Output();

?>