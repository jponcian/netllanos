<?php
//ob_end_clean();
//session_start();

session_start();
include "conexion.php";
include('funciones/auxiliar_php.php');
include "funciones/numerosALetras.class.php";
require('funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
{
function Header()
	{

	}	
	
function Footer()
	{   

	}		
}

// INICIO
// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=10);
$pdf->SetTitle('Etiquetas QR');
$pdf->SetFont('Times','',9);

//--- COMIENZO
$pdf->AddPage();
//setlocale(LC_TIME, 'sp_ES','sp', 'es');
$y = 10;
$x = 10;

	$consulta_div = "SELECT * FROM z_empleados WHERE division = '9' AND area = '2' LIMIT 0, 1000;"; 
	$tabla_div = mysql_query($consulta_div);	//echo $consulta_div;
	//$tabla_div = $_SESSION['conexionsql']->query($consulta_div); 
	while ($registro_div = mysql_fetch_object($tabla_div))
		{
		if ($x>180)	{$x=10;$y += 55; }
		if ($y>240)	{$x=10;$y = 10; $pdf->AddPage(); }
		$pdf->Cell(50,5,'i-LLANOS',0,0,'C');
		$pdf->Image("http://localhost/qr_generador.php?code=".$registro_div->cedula,$x,$y,50,50,"png");
		$pdf->Image("imagenes/logo.jpeg",$x+16,$y+21,18,8,"jpeg");
		
		//-----------
		$pdf->SetXY($x+5,$y-1);
		//$pdf->Cell(22,5,extraer_iniciales($registro_div->division),0,0,'C');
		$pdf->SetXY($x+15,$y+45);
		$pdf->Cell(22,5,$registro_div->cedula,0,0,'C');
		$pdf->SetXY($x+1,$y-2);
		//$pdf->Cell(30,35,'',1,0,'C');
		//-----------
		$x += 50;
		}

$pdf->Output();
?>