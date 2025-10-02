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
	//Arial itálica 8
	$this->SetFont('Times','I',10);
	$this->SetTextColor(120);
	$this->Cell(360,10,sistema().' ',0,0,'C');
	}
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,10,17);
$pdf->SetAutoPageBreak(1,15);
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
$pdf->MultiCell(0,6,'SERIALES DE ESPECIES FISCALES',$linea_muestra,'C',0);
//---------------

//---------------
$pdf->SetFont('Times','B',12);
$pdf->MultiCell(0,6,$Region,$linea_muestra,'C');
//---------------

//---------------
$pdf->ln(6);
$pdf->SetFont('Times','BIU');
$pdf->Cell(30,6,'N° Liquidación:',$linea_muestra,'L');
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
$pdf->Cell(30,6,'Expendedor:',$linea_muestra,'L');
//---------------

//---------------
$pdf->SetFont('Times');
$pdf->MultiCell(0,6,$contribuyente,$linea_muestra,'L');
$pdf->ln(12);
//---------------

//---------------
$pdf->SetFont('Times','B',12);
$pdf->Cell(40,6,'Codigo',$linea_fija,0,'C');
$pdf->Cell(80,6,'Descripcion',$linea_fija,0,'C');
$pdf->Cell(20,6,'Cantidad',$linea_fija,0,'C');
$pdf->Cell(20,6,'Valor',$linea_fija,0,'C');
$pdf->Cell(20,6,'Total',$linea_fija,0,'C');
$pdf->ln(8);
//---------------

//--------- PARA RELLENAR EL CUADRO

$x=$pdf->GetX();
$y=$pdf->GetY();

$consulta = "SELECT timbre_inv.codigo, timbre_inv.descripcion, timbre_ventas_detalle.cantidad, timbre_inv.precio
FROM (timbre_ventas_detalle INNER JOIN timbre_ventas ON timbre_ventas_detalle.numero_venta = timbre_ventas.numero) INNER JOIN timbre_inv ON timbre_ventas_detalle.codigo = timbre_inv.codigo WHERE (((timbre_ventas.numero)=".$_SESSION['VARIABLE'].")) AND Year(timbre_ventas.fecha)=".$_SESSION['FECHA1']." ORDER BY timbre_inv.codigo;"; 
$tabla = mysql_query($consulta);
//-------------------------
while ($registro = mysql_fetch_object($tabla))
	{
	$pdf->SetFont('Times','B',10);
	
	$x = $x;
	$pdf->SetXY($x,$y);
	$a = 40;
	$pdf->Cell($a,6,$registro->codigo,$linea_fija,0,'L',1);
	
	$x = $x + $a;
	$pdf->SetXY($x,$y);
	$b = 80;
	$pdf->MultiCell($b,6,$registro->descripcion,$linea_fija,'L',1);
	
	$x = $x + $b;
	$pdf->SetXY($x,$y);
	$c = 20 ;
	$pdf->MultiCell($c,6,number_format(doubleval($registro->cantidad),0,',','.'),$linea_fija,'C',1);
	
	$x = $x + $c;
	$pdf->SetXY($x,$y);
	$d = 20;
	$pdf->MultiCell($d,6,number_format(doubleval($registro->precio),5,',','.'),$linea_fija,'C',1);
	
	$x = $x + $d;
	$pdf->SetXY($x,$y);
	$e = 20;
	$pdf->MultiCell($e,6,number_format(doubleval($registro->cantidad * $registro->precio),5,',','.'),$linea_fija,'C',1);
	
	// -------
	$x = 17;
	$y = $y + 6.5;
	//------------------------------------------------------------------------------------------
	$pdf->SetFont('Times','',10);	
	$consultax = "SELECT codigo, serial_desde, serial_hasta, cantidad FROM timbre_ventas_seriales WHERE (((numero_venta)=".$_SESSION['VARIABLE'].") AND ((anno_venta)=".$_SESSION['FECHA1'].") AND ((codigo)='".$registro->codigo."')) ORDER BY serial_desde;"; 
	$tablax = mysql_query($consultax);
	//----------------------
	while ($registrox = mysql_fetch_object($tablax))
		{
		$x = $x;
		$pdf->SetXY($x,$y);
		$a = 40;
		$pdf->Cell($a,6,'------------ Serial ----------->',$linea_muestra,0,'L');//
		
		$x = $x + $a;
		$pdf->SetXY($x,$y);
		$b = 80;
		$pdf->MultiCell($b,6,'Desde el '.number_format(doubleval($registrox->serial_desde),0,',','.').' al '.number_format(doubleval($registrox->serial_hasta),0,',','.'),$linea_muestra,'L');
		
		$x = $x + $b;
		$pdf->SetXY($x,$y);
		$c = 20 ;
		$pdf->MultiCell($c,6,number_format(doubleval($registrox->cantidad),0,',','.'),$linea_muestra,'C',1);
		
		$x = $x + $c;
		$pdf->SetXY($x,$y);
		$d = 20;
		$pdf->MultiCell($d,6,number_format(doubleval($registro->precio),5,',','.'),$linea_muestra,'C',1);
		
		$x = $x + $d;
		$pdf->SetXY($x,$y);
		$e = 20;
		$pdf->MultiCell($e,6,number_format(doubleval($registrox->cantidad * $registro->precio),5,',','.'),$linea_muestra,'C',1);
		
		// -------
		$x = 17;
		$y = $y + 6;
		
		// POR SI SE LLENO LA PAGINA
		if ($y>250)
			{	$pdf->AddPage();	$y =35;		}

		}
	$y = $y + 2;
	//-------------------------------------------------------------------------------------------
	}
// ----------------

//---------------
if ($y<200)
	{$pdf->SetY(220);}
$pdf->SetFont('Times','BIU');
$pdf->Cell(115,6,'',$linea_muestra);
$pdf->Cell(30,6,'Venta:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->Cell(35,6,'BsS. '.number_format(doubleval($monto),5,',','.'),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(6);
$pdf->SetFont('Times','BIU');
$pdf->Cell(115,6,'',$linea_muestra);
$pdf->Cell(30,6,'Comisión:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->Cell(35,6,'BsS. '.number_format(doubleval($comision),5,',','.'),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(6);
$pdf->SetFont('Times','BIU');
$pdf->Cell(115,6,'',$linea_muestra);
$pdf->Cell(30,6,'Total a Pagar:',$linea_muestra,0,'R');
$pdf->SetFont('Times');
$pdf->Cell(35,6,'BsS. '.number_format(doubleval($total),5,',','.'),$linea_muestra,0,'R');
//---------------

//---------------
$pdf->ln(18);
$pdf->SetFont('Times','B',12);
$pdf->Cell(90,6,'______________________',$linea_muestra,0,'C');
$pdf->Cell(90,6,'______________________',$linea_muestra,0,'C');
$pdf->ln(6);
$pdf->Cell(90,6,'Area de Timbre',$linea_muestra,0,'C');
$pdf->Cell(90,6,'Firma del Solicitante',$linea_muestra,0,'C');
//---------------

$pdf->Output();
?>