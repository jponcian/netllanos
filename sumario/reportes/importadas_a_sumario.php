<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');
include('../../conexion.php');
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";

class PDF extends FPDF
	{
		function Header()
		{
			global $color;
			
			//--- CABEZERA DEL REPORTE
			$this->SetFont('Arial','B',15);
			$this->Image('../../imagenes/logo.jpeg',20,8,65);
			$this->SetFont('Times','B',11); $this->Ln(8);
			//$pdf->Cell(90);
			
			//BUSCAMOS LA REGION
			$consulta_x = "SELECT nombre FROM z_region";
			$tabla_x = mysql_query($consulta_x);
			$regstro_x = mysql_fetch_object($tabla_x);
			$region = $regstro_x->nombre;
			
			//BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
			$consulta_x = "SELECT nombre, tipo_division FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
			$tabla_x = mysql_query($consulta_x);
			$regstro_x = mysql_fetch_object($tabla_x);
			$area = $regstro_x->tipo_division;
			$dependencia = $regstro_x->nombre;
			
			//IDENTIFICACION DE LA SEDE
			$this->SetXY(90,11);
			$this->Cell(0,5,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.mb_convert_case($region, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->SetXY(90,16);
			
			//--------- PARA BUSCAR LA DIVISION DEPENDE DEL ORIGEN USUARIO
			$texto1	= 'Sumario Administrativo';						
			//---------
			
			$this->Cell(0,5,mb_convert_case('Dependencia: '.$dependencia, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->SetXY(90,21);
			$this->Cell(0,5,mb_convert_case($area.' de '.$texto1, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->Ln(7);
			
			//---------- FECHA DE LA TRANSFERENCIA
			$consulta_x = "SELECT fecha_recepcion_sumario FROM vista_sumario_exp_transferido_suma WHERE fecha_recepcion_sumario BETWEEN  '".voltea_fecha($_SESSION['INICIO'])."' AND '".voltea_fecha($_SESSION['FIN'])."' AND sector = ".$_SESSION['SEDE_USUARIO'];
			$tabla_x = mysql_query($consulta_x); 
			$regstro_x = mysql_fetch_object($tabla_x);
			$fecha = $regstro_x->fecha;

			//TITULO DEL REPORTE
			$this->SetFont('Times','B',14);
			$this->Cell(0,5,'Relación de Expedientes Recibidos desde el '.$_SESSION['INICIO'].' al '.$_SESSION['FIN'],0,1,'C');
			$this->Ln(4);
			$this->SetFillColor(170,166,166);
			$this->SetFont('Arial','B',10);

			//-------------------------			
			$a=20 ;
			$b=60 ;
			$c=25 ;
			$d=20 ;
			$e=25 ;
			$f=25 ;
			$g=25 ;
			$h=28 ;
			$i=20 ;
			//-------------------------			
			
			$this->Cell($a,5,'Rif',1,0,'C',true);
			$this->Cell($b,5,'Contribuyente',1,0,'C',true);
			$this->Cell($c,5,'Providencia',1,0,'C',true);
			$this->Cell($d,5,'Recepcion',1,0,'C',true);
			$this->Cell($e,5,'Reparo',1,0,'C',true);
			$this->Cell($f,5,'Impto Omitido',1,0,'C',true);
			$this->Cell($g,5,'Monto Pagado',1,0,'C',true);						
			$this->Cell($h,5,'Monto Sumario',1,0,'C',true);						
			$this->Cell($i,5,'Sector',1,0,'C',true);						
		}
		function Footer()
		{
		//Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,0,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
		}	

	}

// ENCABEZADO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=20);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFont('Times','',9);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

//TITULO DEL CUADRO
$a=20 ;
$b=60 ;
$c=25 ;
$d=20 ;
$e=25 ;
$f=25 ;
$g=25 ;
$h=28 ;
$z=20 ;
			
$linea = 0;
$i=1;
$Monto_Total = 0;

$consulta = "SELECT rif, contribuyente, CONCAT_WS('-',anno,numero) as providencia, fecha_recepcion_sumario as recepcion, reparo, impuesto_omitido, monto_pagado, (impuesto_omitido - monto_pagado) as monto_sumario, nombre as nombre_sector FROM vista_sumario_exp_transferido_suma WHERE fecha_recepcion_sumario BETWEEN '".voltea_fecha($_SESSION['INICIO'])."' AND '".voltea_fecha($_SESSION['FIN'])."' AND sector = ".$_SESSION['SEDE_USUARIO'];
$tabla = mysql_query($consulta);

$pdf->Ln(5);

while ($registro = mysql_fetch_object($tabla))
{
	// CONDICION PARA EL SUBTOTAL
	if ($i==1) {$Rif_Proceso=$registro->rif; $i++;}
	//++++++++++++++++++++++++++
	// CONDICION PARA EL SUBTOTAL
	if ($Rif_Proceso<>$registro->rif)	
		{
		$pdf->SetFont('Times','B',9);
		$pdf->SetFillColor(200,200,200);
		$pdf->Cell($a+$b+$c+$d+$e+$f+$g,5," = = = = = = = = = >  Total  x  Expediente  = = = = = = = = = >",1,0,'R',true); 
		$pdf->Cell($h+$z,5,'Bs. '.formato_moneda($Monto_Total),1,0,'R',true); 
		$pdf->Ln(5);
		//-------------------
		$Rif_Proceso=$registro->rif;
		$Monto_Total=0;
		}
	//++++++++++++++++++++++++++
	$pdf->SetFont('Times','',9);
	// ----- PARA EL TEXTO
	$y1=$pdf->GetY();
	//---------
	if ($y1 > 185) { $pdf->AddPage(); 	$y1=$pdf->GetY();}
	//----------------------------------
	$pdf->Cell($a,5,$registro->rif,$linea,0,'C'); 
	//--- MULTICELL
	$x=$pdf->GetX();
	$pdf->MultiCell($b,5,$registro->contribuyente,$linea,'J');
	$y2=$pdf->GetY();
	//- PARA PONER LAS COORDENADAS DESPUES DEL MULTICELL
	$pdf->SetY($y1);
	$pdf->SetX($x+$b);
	//--------------------------------------------------
	$pdf->Cell($c,5,$registro->providencia,$linea,0,'C'); 
	$pdf->Cell($d,5,voltea_fecha($registro->recepcion),$linea,0,'C'); 
	$pdf->Cell($e,5,formato_moneda($registro->reparo),$linea,0,'R'); 
	$pdf->Cell($f,5,formato_moneda($registro->impuesto_omitido),$linea,0,'R'); 
	$pdf->Cell($g,5,formato_moneda($registro->monto_pagado),$linea,0,'R'); 
	$pdf->Cell($h,5,formato_moneda($registro->monto_sumario),$linea,0,'R'); 
	$pdf->Cell($z,5,$registro->nombre_sector,$linea,0,'C'); 
	//--------------------
	$Monto_Total = $Monto_Total+$registro->monto_sumario;
	// ----- PARA EL CUADRO
	$pdf->SetY($y1);
	$pdf->Cell($a,$y2-$y1,"",1); 
	$pdf->Cell($b,$y2-$y1,"",1); 
	$pdf->Cell($c,$y2-$y1,"",1); 
	$pdf->Cell($d,$y2-$y1,"",1); 
	$pdf->Cell($e,$y2-$y1,"",1); 
	$pdf->Cell($f,$y2-$y1,"",1); 
	$pdf->Cell($g,$y2-$y1,"",1); 
	$pdf->Cell($h,$y2-$y1,"",1); 
	$pdf->Cell($z,$y2-$y1,"",1); 
	//---------------------
	$pdf->Ln($y2-$y1);

}
// CONDICION PARA EL SUBTOTAL
$pdf->SetFont('Times','B',9);
$pdf->SetFillColor(200,200,200);
$pdf->Cell($a+$b+$c+$d+$e+$f+$g,5," = = = = = = = = = >  Total  x  Expediente  = = = = = = = = = >",1,0,'R',true); 
$pdf->Cell($h+$z,5,'Bs. '.formato_moneda($Monto_Total),1,0,'R',true); 
$pdf->Ln(5);
//-------------------
$Rif_Proceso=$registro->rif;
$Monto_Total=0;
//++++++++++++++++++++++++++
$pdf->SetFont('Times','',9);
	
$pdf->Output();
?>