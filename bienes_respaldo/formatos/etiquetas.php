<?php
//ob_end_clean();
//session_start();

session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

$_SESSION['AREA'] = $_GET['area'];

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}

class PDF extends FPDF
{
function Header()
	{
	//$comprobante = 'INVENTARIO';
	//include "../formatos/cabecera.php";
	//include "../formatos/titulos.php";
	}	
	
function Footer()
	{   
	//include "../formatos/pie.php";
	//Posición a 1,5 cm del final
	$this->SetY(-14);
	//Arial itálica 8
	$this->SetFont('Times','I',9);
	//Color del texto en gris
	$this->SetTextColor(120);
	//Número de página
	$this->Cell(0,5,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
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

$_SESSION['DIVISION'] = $_GET['area'];

//--- COMIENZO
$pdf->AddPage();
//setlocale(LC_TIME, 'sp_ES','sp', 'es');
$y = 10;
$x = 10;

////if ($_SESSION['DIVISION']=='0')
////		{
////		$consulta_div = "SELECT a_divisiones.* FROM bn_bienes,	a_divisiones WHERE bn_bienes.id_division = a_divisiones.id GROUP BY a_divisiones.id ORDER BY a_divisiones.id"; 
////		$tabla_div = $_SESSION['conexionsql']->query($consulta_div);
////		while ($registro_div = $tabla_div->fetch_object())
////			{
////			$_SESSION['DIVISION'] = $registro_div->id;
////			$_SESSION['DIVISION_L'] = $registro_div->division;
////			//$_SESSION['AREAS'] = '0';
////			$_SESSION['monto'] = 0;
////			$_SESSION['i'] = 0;
////			//------------
////			//$consulta = "SELECT a_areas.* FROM bn_bienes, a_areas,	a_divisiones WHERE bn_bienes.id_area = a_areas.id AND a_areas.id_division = a_divisiones.id AND a_divisiones.id=".$_SESSION['DIVISION']." GROUP BY a_areas.id ORDER BY a_areas.id"; 
////			//$tabla = $_SESSION['conexionsql']->query($consulta);
////			//while ($registro = $tabla->fetch_object())
////				//{
////				//$_SESSION['AREA'] = $registro->id;
////				//$_SESSION['AREA_L'] = $registro->area;
////				include "x_inventario_cuerpo.php";
////				//}
////				//----------
////				$_SESSION['DIVISION'] = 'ULTIMA';
////				//-- RESUMEN
////				include "x_inventario_resumen.php";
////			}
////		//-- RESUMEN
////		$_SESSION['DIVISION'] = 'FINAL';
////		$_SESSION['monto'] = 0;
////		$_SESSION['i'] = 0;
////		include "x_inventario_resumen_region.php";
////		}
////else
////	{
	$consulta_div = "SELECT bn_bienes.* FROM bn_bienes WHERE bn_bienes.id_area = ".$_SESSION['AREA'].";"; 
	$tabla_div = mysql_query($consulta_div);	//echo $consulta_div;
	//$tabla_div = $_SESSION['conexionsql']->query($consulta_div); 
	while ($registro_div = mysql_fetch_object($tabla_div))
		{
		if ($x>180)	{$x=10;$y += 37; }
		if ($y>240)	{$x=10;$y = 10; $pdf->AddPage(); }
		$pdf->Image("http://localhost/qr_generador.php?code=".$registro_div->numero_bien,$x,$y,32,32,"png");
		//-----------
		$pdf->SetXY($x+5,$y-1);
		//$pdf->Cell(22,5,extraer_iniciales($registro_div->division),0,0,'C');
		$pdf->SetXY($x+5,$y+28);
		$pdf->Cell(22,5,$registro_div->numero_bien,0,0,'C');
		$pdf->SetXY($x+1,$y-2);
		$pdf->Cell(30,35,'',1,0,'C');
		//-----------
		$x += 32;
		}
//		
////	}
$pdf->Output();
?>