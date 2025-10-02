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
	$this->Image('../../imagenes/logo.jpeg',17,8,55);
	}	
	
	//---------- PIE
	function Footer()
	{    
	$this->SetY(-12);
	//Arial italica 8
	$this->SetFont('Times','I',10);
	$this->SetTextColor(120);
	$this->Cell(360,10,sistema().' ',0,0,'C');
	}
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,10,17);
$pdf->SetAutoPageBreak(1,5);
$pdf->SetFillColor(210);
//---------------
$pdf->AddPage();
// -------------------------------------------------------------------------------
$linea_muestra = 0;
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
//$pdf->SetXY(160,40);
$pdf->ln(10);
$pdf->SetFont('Times','BIU',12);
$pdf->Cell(130,6,'',$linea_muestra,'L');
$pdf->Cell(20,6,'Fecha:',$linea_muestra,'L');
$pdf->SetFont('Times');
$pdf->Cell(20,6,$fecha,$linea_muestra,'L');
//---------------

//---------------
$pdf->ln(18);
$pdf->SetFont('Times','B',14);
$pdf->MultiCell(0,6,'CONSTANCIA DE DESPACHO',$linea_muestra,'C',0);
//---------------

//---------------
$pdf->SetFont('Times','B',12);
$pdf->MultiCell(0,6,$Region,$linea_muestra,'C');
//---------------

//---------------
$pdf->ln(6);
$pdf->SetFont('Times','BIU');
$pdf->Cell(30,6,'N° Liquidacion:',$linea_muestra,'L');
$pdf->SetFont('Times');
$pdf->Cell(75,6,$liquidacion,$linea_muestra,'L');
$pdf->SetFont('Times','BIU');
$pdf->Cell(30,6,'Licencia:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->MultiCell(0,6,'GRTI-RLL-DR-ATF-'.$licencia,$linea_muestra,'L');
//---------------

//---------------
$pdf->ln(3);
$pdf->SetFont('Times','BIU');
$pdf->Cell(22,6,'Rif:',$linea_muestra,'L');

//---------------
$pdf->SetFont('Times');
$pdf->Cell(75,6,substr($rif,0,1).'-'.substr($rif,1,8).'-'.substr($rif,9,1),$linea_muestra,'L');
//---------------

//---------------
$pdf->SetFont('Times','BIU');
$pdf->Cell(40,6,'Clase de Expendio:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->MultiCell(0,6,$tipo,$linea_muestra,'L');
//---------------

//---------------
$pdf->ln(3);
$pdf->SetFont('Times','BIU');
$pdf->Cell(22,6,'Nombre:',$linea_muestra,'L');
//---------------

//---------------
$pdf->SetFont('Times');
$pdf->MultiCell(110,6,$contribuyente,$linea_muestra,'L');
//---------------

//---------------
$pdf->ln(3);
$pdf->SetFont('Times','BIU');
$pdf->Cell(22,6,'Direccion:',$linea_muestra,'L');
//---------------

$y=$pdf->GetY();	

//---------------
$pdf->SetFont('Times');
//$pdf->Cell(10,6,'',$linea_muestra,'L');
$pdf->MultiCell(0,6,$direccion,$linea_muestra,'L');
//---------------

$pdf->SetY($y+18);

//---------------
$pdf->SetFont('Times','B',12);
$pdf->Cell(40,6,'Codigo',$linea_fija,0,'C');
$pdf->Cell(80,6,'Descripcion',$linea_fija,0,'C');
$pdf->Cell(20,6,'Cantidad',$linea_fija,0,'C');
$pdf->Cell(20,6,'Valor',$linea_fija,0,'C');
$pdf->Cell(20,6,'Total',$linea_fija,0,'C');
$pdf->ln(6);
//---------------
$pdf->Cell(40,90,'',$linea_fija,'L');
$pdf->Cell(80,90,'',$linea_fija,'L');
$pdf->Cell(20,90,'',$linea_fija,'L');
$pdf->Cell(20,90,'',$linea_fija,'L');
$pdf->Cell(20,90,'',$linea_fija,'L');
//---------------

//---------------
$pdf->ln(100);
$pdf->SetFont('Times','BIU');
$pdf->Cell(115,6,'',$linea_muestra);
$pdf->Cell(30,6,'Venta:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->Cell(35,6,'BsS. '.(number_format(doubleval(minimo_soberano($monto,0)),2,',','.')),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(6);
$pdf->SetFont('Times','BIU');
$pdf->Cell(115,6,'',$linea_muestra);
$pdf->Cell(30,6,'Comision:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->Cell(35,6,'BsS. '.number_format(doubleval($comision),2,',','.'),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(6);
$pdf->SetFont('Times','BIU');
$pdf->Cell(115,6,'',$linea_muestra);
$pdf->Cell(30,6,'Total a Pagar:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->Cell(35,6,'BsS. '.(number_format(doubleval(minimo_soberano($total,0)),2,',','.')),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(28);
$pdf->SetFont('Times','B',12);
$pdf->Cell(90,6,'______________________',$linea_muestra,0,'C');
$pdf->Cell(90,6,'______________________',$linea_muestra,0,'C');
$pdf->ln(6);
$pdf->Cell(90,6,'Area de Timbre',$linea_muestra,0,'C');
$pdf->Cell(90,6,'Firma del Solicitante',$linea_muestra,0,'C');
//---------------


//--------- PARA RELLENAR EL CUADRO

$x = 17;
$y = 115;
$pdf->SetFont('Times','',10);

$consulta = "SELECT timbre_inv.codigo, timbre_inv.descripcion, timbre_ventas_detalle.cantidad, timbre_inv.precio
FROM (timbre_ventas_detalle INNER JOIN timbre_ventas ON timbre_ventas_detalle.numero_venta = timbre_ventas.numero) INNER JOIN timbre_inv ON timbre_ventas_detalle.codigo = timbre_inv.codigo WHERE (((timbre_ventas.numero)=".$_SESSION['VARIABLE'].")) AND Year(timbre_ventas.fecha)=".$_SESSION['FECHA1']." ORDER BY timbre_inv.indice;"; 
$tabla = mysql_query($consulta); //echo $consulta;

while ($registro = mysql_fetch_object($tabla))
	{
	$x = $x;
	$pdf->SetXY($x,$y);
	$a = 40;
	$pdf->Cell($a,6,$registro->codigo,$linea_muestra,0,'L');
	
	$x = $x + $a;
	$pdf->SetXY($x,$y);
	$b = 80;
	$pdf->MultiCell($b,6,$registro->descripcion,$linea_muestra,'L');
	
	$x = $x + $b;
	$pdf->SetXY($x,$y);
	$c = 20 ;
	$pdf->MultiCell($c,6,number_format(doubleval($registro->cantidad),0,',','.'),$linea_muestra,'C');
	
	$x = $x + $c;
	$pdf->SetXY($x,$y);
	$d = 20;
	$pdf->MultiCell($d,6,(number_format(doubleval(minimo_soberano($registro->precio,0)),2,',','.')),$linea_muestra,'C');
	
	$x = $x + $d;
	$pdf->SetXY($x,$y);
	$e = 20;
	$pdf->MultiCell($e,6,(number_format(doubleval(minimo_soberano($registro->cantidad * $registro->precio,0)),2,',','.')),$linea_muestra,'C');
	
	// -------
	$x = 17;
	$y = $y + 6;
	}
// ----------------

$pdf->Output();
?>