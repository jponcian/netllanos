<?php
session_start();
//ob_end_clean();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');

//setlocale(LC_TIME, 'sp_ES','sp', 'es');
//$_SESSION['conexionsql']->query("SET NAMES 'latin1'");

//if ($_SESSION['VERIFICADO'] != "SI") { 
//    header ("Location: ../index.php?errorusuario=val"); 
//    exit(); 
//	}
	
class PDF extends FPDF
{
	function Footer()
	{    
		$this->SetFont('Times','I',8);
		$this->SetY(-15);
		$this->SetTextColor(120);
		//$this->Cell(0,5,'Resolución '.($_GET['id']));
		//--------------
		$s=$this->PageNo();
		$this->Cell(0,0,'NetlosLlanos '.$this->PageNo().' de {nb}',0,0,'R');
		$this->SetY(-15);
		$this->Cell(0,0,$_SESSION['CEDULA_USUARIO'],0,0,'L');
	}	
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(12,15,12);
$pdf->SetAutoPageBreak(1,17);
$pdf->SetTitle('Relacion de Bienes');

// ----------
$pdf->AddPage();
$pdf->SetFillColor(2, 117, 216);
$pdf->Image('../../imagenes/logo.jpeg',20,10,45);
//$pdf->Image('../../images/escudo.jpg',165,12,26);
//$pdf->Image('../../images/logo_web.png',100,80,100);
$pdf->SetFont('Times','',11);

// ---------------------
//$pdf->SetY(12);
//$instituto = instituto();
$pdf->SetFont('Times','I',11.5);
$pdf->Cell(0,5,'República Bolivariana de Venezuela',0,0,'C'); $pdf->Ln(5);
$pdf->Cell(0,5,'Servicio Nacional Integrado de Administracion',0,0,'C'); $pdf->Ln(5);
$pdf->Cell(0,5,'Aduanera y Tributaria',0,0,'C'); $pdf->Ln(5);
$pdf->Cell(0,5,'Division de Administración - Area de Bienes Nacionales',0,0,'C'); 
$pdf->Ln(10);

$pdf->SetFont('Times','B',11);
$pdf->Cell(0,5,'RELACIÓN DE BIENES',0,1,'C'); 
$pdf->Cell(0,5,$_SESSION[titulo],0,0,'C'); 
$pdf->Ln(7);

$pdf->SetTextColor(255);
$pdf->SetFont('Times','B',10.5);
$pdf->Cell($aa=9,7,'Item',1,0,'C',1);
$pdf->Cell($b=15,7,'Bien',1,0,'C',1);
$pdf->Cell($c=98+48,7,'Descripcion',1,0,'L',1);
$pdf->Cell($d=0,7,'Estatus',1,1,'C',1);
$pdf->SetTextColor(0);
$pdf->SetFillColor(255);
$i=0;
//-----------------
$_SESSION['estatus'] = array('Por Verificar','Verificado');
$tabla = $_SESSION['conexionsql2']->query($_SESSION['consulta']); //echo $_SESSION['consulta'];
//-----------------
$i=0; $monto=0;
while ($registro = $tabla->fetch_object())
	{
	$pdf->SetFont('Times','',8.5);
	if ($i%2==0)	{$pdf->SetFillColor(255);} else {$pdf->SetFillColor(235);}
	//----------
	$pdf->Cell($aa,5.5,$i+1,1,0,'C',1);
	$pdf->SetFont('Times','',8.5);
	$pdf->Cell($b,5.5,$registro->numero_bien,1,0,'C',1);
	$pdf->SetFont('Times','',7);
	$pdf->Cell($c,5.5,substr($registro->descripcion_bien,0,70),1,0,'L',1);
	$pdf->SetFont('Times','',8.5);
	$pdf->Cell($d,5.5,$_SESSION['estatus'][($registro->revisado)],1,0,'C',1);

	$pdf->Ln(5.5);
	//-----------
	$i++;
	}

//$pdf->SetFont('Times','B',12);
//$pdf->SetFillColor(230);
//$pdf->Cell(0,6,'TOTAL => '.formato_moneda($monto),1,0,'R',1);
//
$pdf->Output();
?>