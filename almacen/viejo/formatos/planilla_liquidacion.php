<?php
ob_end_clean();
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
mysql_query("SET NAMES 'latin1'");
setlocale(LC_TIME, 'sp_ES','sp', 'es');

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');

class PDF extends FPDF
{
	//---------- ENCABEZADO
	function Header()
	{  
	// LOGO
	//$this->Image('../../imagenes/logo.jpeg',17,8,55);
	}	
	
	//---------- PIE
	function Footer()
	{    
	$this->SetY(-12);
	//Arial itálica 8
	$this->SetFont('Times','I',10);
	$this->SetTextColor(120);
	$this->Cell(360,10,sistema().' ',0,0,'C');
	}
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,10,20);
$pdf->SetAutoPageBreak(1,5);
$pdf->SetFillColor(210);
//---------------
$pdf->AddPage();
// -------------------------------------------------------------------------------

$linea_fija = 1;
//-------------

$_SESSION['VARIABLE'] = $_GET['num'];
$_SESSION['FECHA1'] = $_GET['anno'];

// CONSULTA CON LOS DATOS
$consulta = "SELECT timbre_expendedores.tipo, vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.direccion AS Direccion, timbre_ventas.monto, timbre_ventas.comision, timbre_ventas.total, timbre_ventas.liquidacion, Year(timbre_ventas.fecha) AS anno, date_format(timbre_ventas.fecha, '%d/%m/%Y') AS fecha1, timbre_expendedores.licencia FROM timbre_expendedores INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = timbre_expendedores.rif INNER JOIN timbre_ventas ON timbre_ventas.licencia = timbre_expendedores.licencia WHERE (((timbre_ventas.numero)=".$_SESSION['VARIABLE'].")) AND Year(timbre_ventas.fecha)=".$_SESSION['FECHA1'].";";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);

// DATOS DEL EXPENDEDOR
$rif = $registro->rif;
$contribuyente = $registro->contribuyente;
$direccion = $registro->Direccion;
$liquidacion = $registro->anno .'02010001243'. sprintf("%005s", $registro->liquidacion);
$fecha = $registro->fecha1;
$monto = $registro->monto;
$comision = $registro->comision;
$total = $registro->total;
$licencia = $registro->licencia;
$tipo = $registro->tipo;

// ----------------

////////// REGION DE EMISION
$consulta_x = "SELECT Nombre FROM z_region;";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$Region=$registro_x->Nombre;
// ---------------------

//---------------
$pdf->SetFont('Times','B',11);
$pdf->Cell(40,6,'',$linea_muestra,'L');
$pdf->Cell(0,6,$Region,$linea_muestra,'L');
//---------------

//---------------
$pdf->SetFont('Times','B',12);
$pdf->ln(16);
$pdf->Cell(72,6,$liquidacion,$linea_muestra,'L');
$pdf->Cell(25,6,$fecha,$linea_muestra,'L');
$pdf->Cell(0,6,$rif,$linea_muestra,0,'C');
//---------------

//---------------
$pdf->SetFont('Times','B',11);
$pdf->ln(10);
$pdf->Cell(140,6,$contribuyente,$linea_muestra,'L');
$pdf->Cell(0,6,'GRTI-RLL-DR-ATF-'.$licencia,$linea_muestra,'L');
//---------------

//---------------
$pdf->ln(9);
$pdf->SetFont('Times','',10);
$pdf->MultiCell(159,3,$direccion,$linea_muestra,'L');
//---------------

//---------------
$pdf->SetFont('Times','B',11);
//---------------
if ($tipo == 'Libre') 	{	$pdf->SetXY(165,46);	} else	{	$pdf->SetXY(189,46);	}
//---------------
$pdf->Cell(0,6,'X',$linea_muestra,'L');
//---------------

//---------------
if ($tipo == 'Libre')	{	$pdf->SetY(185);	} else	{	$pdf->SetY(72);	}
//---------------
$pdf->Cell(40,6,'',$linea_muestra,0,'R');
$pdf->Cell(45,6,'BsS. '.number_format(doubleval($monto),2,',','.'),$linea_muestra,0,'R');
$pdf->Cell(45,6,'BsS. '.number_format(doubleval($comision),2,',','.'),$linea_muestra,0,'R');
$pdf->Cell(0,6,'BsS. '.number_format(doubleval($total),2,',','.'),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->SetY(195);
$pdf->Cell(0,6,'BsS. '.number_format(doubleval($total),2,',','.'),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(23);
//---------------
if ($tipo == 'Libre')	{	$codigo='301100132';	$descripcion='ESTAMPILLAS';} else	{	$codigo='301180103'; $descripcion='FORMULARIOS';	}
//---------------
$pdf->Cell(35,6,$codigo,$linea_muestra,0,'L');
$pdf->Cell(110,6,$descripcion,$linea_muestra,0,'L');
$pdf->Cell(0,6,'BsS. '.number_format(doubleval($total),2,',','.'),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(17);
$pdf->Cell(0,6,'BsS. '.number_format(doubleval($total),2,',','.'),$linea_muestra,0,'R');
//---------------

$pdf->Output();
?>