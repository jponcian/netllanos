<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}

class PDF extends FPDF
{
function Header()
	{
	//------ ORIGEN DEL FUNCIONARIO
	include "../../funciones/origen_funcionario.php";
	//--------------------
	
	//$this->Image('../../imagenes/logo.jpeg',20,8,65);
	
	global $color;
	
	//--- CABEZERA DEL REPORTE
	$this->SetFont('Arial','B',15);
	$this->Image('../../imagenes/logo.jpeg',20,8,65);
	$this->SetFont('Times','B',11); $this->Ln(8);
	
	//BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
	list ($a, $b, $c, $d, $e, $area, $dependencia) = buscar_sector($_SESSION['SEDE']);
	
	// ORIGEN DEL AJUSTE
	switch ($origenUT) 
		{
		case 7:
			$division = "Sujetos Pasivos Especiales";
			break;
		case 16:
			$division = "Recaudación";
			break;		
		}

	//IDENTIFICACION DE LA SEDE
	$this->SetXY(90,11);
	$this->Cell(0,5,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.mb_convert_case(buscar_region(), MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
	$this->SetXY(90,16);
	$this->Cell(0,5,mb_convert_case('Dependencia: '.$dependencia, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');			
	$this->SetXY(90,21);
	$this->Cell(0,5,mb_convert_case($area.' de '.$division, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
	
	$this->Ln(10);
	
	//TITULO DEL REPORTE
	$this->SetFont('Times','B',14);
	$this->Cell(0,5,'Ajustes de Unidad Tributaria transferidos a Liquidacion Desde: '.date("d-m-Y",strtotime($_SESSION['INICIO'])).' Hasta: '.date("d-m-Y",strtotime($_SESSION['FIN'])),0,1,'C');
	$this->Ln(6);
	$this->SetFillColor(170,166,166);
	$this->SetFont('Arial','B',10);
	
	global $contador ;
	global $a ;
	global $b ;
	global $c ;
	global $d ;
	global $e ;
	global $f ;
	
	$contador=6 ; // contador
	$a=12 ;
	$b=15 ;
	$c=25 ;
	$d=104 ; // contribuyente
	$e=55 ;
	$f=30 ;
	
	$this->Cell($contador,5,'N°',1,0,'C',true);
	$this->Cell($a,5,'Año',1,0,'C',true);
	$this->Cell($b,5,'Numero',1,0,'C',true);
	$this->Cell($c,5,'Rif',1,0,'C',true);
	$this->Cell($d,5,'Contribuyente',1,0,'C',true);
	$this->Cell($e,5,'Periodo',1,0,'C',true);
	$this->Cell($f,5,'Monto BsS',1,1,'C',true);
	
	}	
function Footer()
	{    //Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(460,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'C');
	}		
}

$fecha = date('Y/m/d');

list($dia,$mes,$anno)=explode('/',$fecha);
$fecha = mktime(0,0,0,$mes,$dia,$anno);
		
////////// FIN
//------ ORIGEN DEL FUNCIONARIO
include "../../funciones/origen_funcionario.php";
//--------------------

// ENCABEZADO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,25);

$pdf->AddPage();

$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

//-------------------
$pdf->SetFont('Times','B',10);
$pdf->SetFillColor(202,201,201);
$x=$pdf->GetX();
$y=$pdf->GetY();
//-------------------
			
$linea = 0;
$i=1; $ii=1;
$Monto_Total = 0;

//////// ---- DETALLE DE LAS PLANILLAS

$monto = 0;
$pdf->SetFont('Times','',9);

$consulta_x = "SELECT * FROM liquidacion, vista_contribuyentes_direccion WHERE liquidacion.sector='".$_SESSION['SEDE']."' AND liquidacion.rif=vista_contribuyentes_direccion.rif AND fecha_transferencia_a_liq BETWEEN '".$_SESSION['INICIO']."' AND '".$_SESSION['FIN']."' AND (origen_liquidacion=".$origenUT.") ORDER BY anno_expediente DESC, num_expediente DESC, id_sancion DESC;"; 
//echo $consulta_x;
$tabla_x = mysql_query($consulta_x);

while ($registro_x = mysql_fetch_object($tabla_x))
{
	// CONDICION PARA EL SUBTOTAL
	if ($i==1) {$Rif_Proceso=$registro_x->rif; $i++;}
	//++++++++++++++++++++++++++
	// CONDICION PARA EL SUBTOTAL
	if ($Rif_Proceso<>$registro_x->rif)	
		{
		$pdf->SetFont('Times','B',9);
		$pdf->SetFillColor(200,200,200);
		$pdf->Cell($contador+$a+$b+$c+$d+$e,5," = = = = = = = = = >  Total  x  Expediente  = = = = = = = = = >",1,0,'R',true); 
		$pdf->Cell($f+$g,5,'Bs. '.formato_moneda($Monto_Total),1,0,'R',true); 
		$pdf->Ln(5);
		//-------------------
		$Rif_Proceso=$registro_x->rif;
		$Monto_Total=0;
		$ii=1;
		}
	//++++++++++++++++++++++++++
	$pdf->SetFont('Times','',9);
	// ----- PARA EL TEXTO
	$y1=$pdf->GetY();
	//---------
	if ($y1 > 185) { $pdf->AddPage();  $y1=$pdf->GetY(); }//$pdf->Ln();
	//----------------------------------
	$pdf->Cell($contador,5,$ii,$linea,0,'C');
	$pdf->Cell($a,5,$registro_x->anno_expediente,$linea,0,'C'); 
	$pdf->Cell($b,5,$registro_x->num_expediente,$linea,0,'C'); 
	$pdf->Cell($c,5,formato_rif($registro_x->rif),$linea,0,'C'); 
	//--- MULTICELL
	$x=$pdf->GetX();
	$pdf->MultiCell($d,5,$registro_x->contribuyente,$linea,'J');
	$y2=$pdf->GetY();
	//- PARA PONER LAS COORDENADAS DESPUES DEL MULTICELL
	$pdf->SetY($y1);
	$pdf->SetX($x+$d);
	//--------------------------------------------------
	$pdf->Cell($e,5,voltea_fecha($registro_x->periodoinicio).' al '.voltea_fecha($registro_x->periodofinal),$linea,0,'C'); 
	$pdf->Cell($f,5,formato_moneda(minimo_soberano($registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial,1)),$linea,0,'R'); 
	//--------------------
	$Monto_Total = $Monto_Total+($registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial);
	// ----- PARA EL CUADRO
	$pdf->SetY($y1);
	$pdf->Cell($contador,$y2-$y1,"",1); 
	$pdf->Cell($a,$y2-$y1,"",1); 
	$pdf->Cell($b,$y2-$y1,"",1); 
	$pdf->Cell($c,$y2-$y1,"",1); 
	$pdf->Cell($d,$y2-$y1,"",1); 
	$pdf->Cell($e,$y2-$y1,"",1); 
	$pdf->Cell($f,$y2-$y1,"",1); 
	//---------------------
	$pdf->Ln($y2-$y1);
	$ii++;
	$monto = $monto + $registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial;
}

$pdf->SetFont('Times','B',9);
$pdf->SetFillColor(200,200,200);
$pdf->Cell($contador+$a+$b+$c+$d+$e,5," = = = = = = = = = >  Total  x  Expediente  = = = = = = = = = >",1,0,'R',true); 
$pdf->Cell($f+$g,5,'Bs. '.formato_moneda(minimo_soberano($Monto_Total,1)),1,0,'R',true); 
$pdf->Ln(5);
//-------------------
$Rif_Proceso=$registro_x->rif;
$Monto_Total=0;
$ii=1;
		
//////// ---- TOTAL DE LAS PLANILLAS
$pdf->Cell(5,6,'');
$pdf->SetFillColor(181,175,175);
$pdf->SetFont('Times','B',10);
$txt='==> Monto Total BsS. ==>  ';
$pdf->Cell(207,6,$txt,1,0,'R');
$txt=$monto;
$pdf->Cell(30,6,number_format(doubleval($txt),2,',','.'),1,0,'C',1);
$pdf->Ln(10);

//////// ---------------------------

$pdf->Output();

?>