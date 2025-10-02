<?php
header('Content-Type: text/html; charset=UTF-8');
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: ../index.php?errorusuario=val");
	exit();
}

include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
require('../../funciones/fpdf.php');
mysqli_query($_SESSION['conexionsqli'], "SET NAMES 'utf8'");

class PDF extends FPDF
{
	//---------- ENCABEZADO
	function Header()
	{
		// LOGO
		$this->Image('../../imagenes/logo.jpeg', 17, 8, 55);

		////////// REGION DE EMISION
		$consulta_x = "SELECT Nombre FROM z_region;";
		$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
		$registro_x = mysqli_fetch_object($tabla_x);
		$Region = $registro_x->Nombre;
		// ---------------------
		$mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
		$linea_muestra = 0;
		//---------------
		$this->SetY(10);
		//$this->ln(10);
		// $this->SetFont('Times', 'BIU', 12);
		// $this->Cell(130, 6, '', $linea_muestra, 'L');
		//$this->Cell(20,6,'Timbre Fiscal',$linea_muestra,'L');
		// $this->Cell(20, 6, 'Fecha:', $linea_muestra, 'L');
		$this->SetFont('Times');
		$this->Cell(0, 6, date("d") . " de " . ($mes[(date("m") - 1)]) . " de " . date("Y"), $linea_muestra, 0, 'R');
		//$this->Cell(20,6,'08'. " de " . ($mes[(date("m")-1)]) . " de " . date("Y"),$linea_muestra,'L');
		//---------------

		//---------------
		$this->ln(18);
		$this->SetFont('Times', 'B', 15);
		$this->SetTextColor(40, 40, 40);
		$this->MultiCell(0, 8, 'INVENTARIO ACTUAL ' . strtoupper($mes[(date("m") - 1)]) . ' ' . date("Y"), $linea_muestra, 'C', 0);
		//---------------

		//---------------
		$this->SetFont('Times', 'B', 10);
		$this->SetTextColor(60, 60, 60);
		$this->MultiCell(0, 6, utf8_decode('DIV. DE ADMINISTRACIÓN - ÁREA DE ALMACÉN'), $linea_muestra, 'C');
		//---------------

		//---------------
		$this->SetFont('Times', 'B', 12);
		$this->SetTextColor(80, 80, 80);
		$this->MultiCell(0, 6, utf8_decode($Region), $linea_muestra, 'C');
		$this->ln(6);
		//---------------
	}

	//---------- PIE
	function Footer()
	{
		$this->SetY(-12);
		$s = $this->PageNo();
		//Arial it�lica 8
		$this->SetFont('Times', 'I', 10);
		$this->SetTextColor(120);
		$this->Cell(360, 10, sistema() . ' ' . $s . '/{nb}', 0, 0, 'C');
	}
}

// ENCABEZADO
$pdf = new PDF('P', 'mm', 'LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17, 35, 17);
$pdf->SetAutoPageBreak(1, 20);
$pdf->SetFillColor(210);
//---------------
$pdf->AddPage();
// -------------------------------------------------------------------------------
$linea_muestra = 0;
$linea_fija = 1;
//-------------

//---------------
// Encabezado de la tabla con fondo gris claro y texto oscuro
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetTextColor(30, 30, 30);
// Encabezado de la tabla reordenado: Código, Descripción, Cantidad, Item
$pdf->Cell($d = 25, 8, utf8_decode('Código'), $linea_fija, 0, 'C', 1);
$pdf->Cell($b = 123, 8, utf8_decode('Descripción'), $linea_fija, 0, 'C', 1);
$pdf->Cell($c = 20, 8, utf8_decode('Cantidad'), $linea_fija, 0, 'C', 1);
$pdf->Cell($a = 12, 8, 'Item', $linea_fija, 0, 'C', 1);
$pdf->ln(8);
//---------------

//--------- PARA RELLENAR EL CUADRO

$x = $pdf->GetX();
$y = $pdf->GetY();
$i = 0;

// ...existing code...
$consulta = "SELECT * FROM vista_alm_inventario WHERE cantidad>0 ORDER BY descripcion;";
$tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);
//-------------------------
while ($registro = mysqli_fetch_object($tabla)) {
	$pdf->SetFont('Times', '', 10);
	$pdf->SetTextColor(50, 50, 50);
	$i++;
	$pdf->Cell($d, 7, utf8_decode($registro->codigo), 0, 0, 'C', 0);
	$pdf->Cell($b, 7, utf8_decode($registro->descripcion), 0, 0, 'L', 0);
	$pdf->Cell($c, 7, number_format(doubleval($registro->cantidad), 0, ',', '.'), 0, 0, 'R', 0);
	$pdf->Cell($a, 7, $i, 0, 0, 'C', 0);
	$pdf->ln(7);
}
// ----------------

$pdf->Output();

?>