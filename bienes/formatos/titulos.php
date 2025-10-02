<?php
error_reporting(0);
mysql_query("SET NAMES 'latin1'");
//------------ CUANDO ES POR INVENTARIO
if ($comprobante == 'INVENTARIO')
{
global $a, $b, $c, $d, $e, $f;

$a=20 ; //cantidad 	
$b=25 ; //codigo
$c=25 ; //bien	
strtoupper($d=124) ; //descripcion
$e=20 ; //conservacion
//$e=23 ; //conservacion original
$f=0 ; //valor

$this->SetFont('Arial','B',$fuente_cabecera-0.5);

$this->cell($a,12,'Cantidad',1,0,'C');

$x=$this->GetX();
$y=$this->GetY();
$this->multicell($b,6,utf8_decode('Código del Catalago'),1,'C');
$this->SetY($y);
$this->SetX($x+$b);

$x=$this->GetX();
$y=$this->GetY();
$this->multicell($c,4,utf8_decode('Número de Inventario (solo para bienes)'),1,'C');
$this->SetY($y);
$this->SetX($x+$c);

$this->cell(strtoupper($d),12,utf8_decode('DESCRIPCIÓN'),1,0,'C');

$x=$this->GetX();
$y=$this->GetY();
$this->multicell($e,6,utf8_decode('Estado de Conservación'),1,'C');
$this->SetY($y);
$this->SetX($x+$e);

$x=$this->GetX();
$y=$this->GetY();
$this->multicell($f,12,'Valor Unitario',1,'C');

//----------- VIENEN
if ($this->PageNo()>1 and $_SESSION['AREAS']<>'ULTIMA' and $_SESSION['i']>0) //
	{
	$this->SetFont('Arial','B',$fuente_cabecera);
	$this->Cell($a,4,$_SESSION['i'],1,0,'C');
	$this->Cell($b,4,'',1,0,'L');
	$this->Cell($c,4,'',1,0,'L');
	$this->Cell(strtoupper($d),4,'VIENEN',1,0,'C');
	$this->Cell($e,4,'',1,0,'L');	
	$this->Cell($f,4,formato_moneda_bienes($_SESSION['monto']),1,0,'R');	
	$this->Ln(4);
	}
//---------------
}

//------------ CUANDO ES POR REASIGNACI�N
if ($comprobante == 'REASIGNACIÓN' or $comprobante == 'MOVIMIENTO INTERNO')
{
global $a, $b, $c, $d, $e, $f, $g, $h;

$a=12 ; //cantidad 	
$b=19 ; //codigo
$c=23 ; //bien	
strtoupper($d=100) ; //descripcion
$e=12 ; //codigo
$f=17 ; //concepto
$g=28 ; //valor
$h=0 ; //total

$this->SetFont('Arial','B',$fuente_cabecera-0.5);

$this->cell($a,12,'Cantidad',1,0,'C');

$x=$this->GetX();
$y=$this->GetY();
$this->multicell($b,6,'Codigo del Catalago',1,'C');
$this->SetY($y);
$this->SetX($x+$b);

$x=$this->GetX();
$y=$this->GetY();
$this->multicell($c,4,'Numero de Inventario (solo para bienes)',1,'C');
$this->SetY($y);
$this->SetX($x+$c);

$this->cell(strtoupper($d),12,'DESCRIPCION',1,0,'C');

$this->cell($e,12,'Codigo',1,0,'C');

$this->cell($f,12,'Concepto',1,0,'C');

$this->cell($g,12,'Valor Unitario',1,0,'C');

$this->cell($h,12,'Valor Total',1,0,'C');

$this->Ln(12);

//----------- VIENEN
if ($this->PageNo()>1)
	{
	$this->SetFont('Arial','B',$fuente_cabecera);
	$this->Cell($a,4,$_SESSION['i'],1,0,'C');
	$this->Cell($b,4,'',1,0,'L');
	$this->Cell($c,4,'',1,0,'L');
	$this->Cell(strtoupper($d),4,'VIENEN',1,0,'C');
	$this->Cell($e,4,'',1,0,'L');	
	$this->Cell($f,4,'',1,0,'L');	
	$this->Cell($g,4,'',1,0,'L');	
	$this->Cell($h,4,formato_moneda_bienes($_SESSION['monto']),1,0,'R');	
	$this->Ln(4);
	}
}
?>