<?php
$pdf->AddPage();
//---------------
$pdf->SetFont('Times','B',12);
//---------------
$pdf->SetXY(0,12);
$pdf->MultiCell(216,4,'PLANILLA DE LIQUIDACION',$linea_muestra,'C');
//---------------
$pdf->SetFont('Times','B',9);
//---------------
$pdf->SetXY(175,9);
$pdf->MultiCell(30,4,'N-'.substr($registro->liquidacion,2,2).sprintf("%002s",region_liq()).'9'.sprintf("%005s",$registro->secuencial),$linea_muestra,'C');

//---------------
$pdf->SetFont('Times','',9);

//---------------
$pdf->SetXY(140,14.5);
$pdf->MultiCell(40,4,'N° DE LIQUIDACIÓN',$linea_muestra,'C');
//---------------
$pdf->SetFont('Times','B',10);
//---------------
$pdf->SetXY(140,18);
$pdf->MultiCell(40,4,$registro->numeroliquidacion,$linea_muestra,'C');

//---------------
$pdf->SetFont('Times','',9);
//---------------
$pdf->SetXY(180,14.5);
$pdf->MultiCell(20,4,'FECHA',$linea_muestra,'C');
//---------------
$pdf->SetFont('Times','B',10);
//---------------
$pdf->SetXY(180,18);
$pdf->MultiCell(20,4,voltea_fecha($registro->fecha_liquidacion),$linea_muestra,'C');

$pdf->SetFont('Times','B',10);
//---------------
$pdf->SetXY(0,20);
$pdf->MultiCell(216,4,$Region,$linea_muestra,'C');

//---------------
$pdf->SetFont('Times','B',10);
//---------------
$pdf->SetXY(0,20);
$pdf->MultiCell(216,4,buscar_region(),$linea_muestra,'C');

//---------------
$pdf->SetFont('Times','B',10);

//---------------
$pdf->SetXY(17,32);
$pdf->MultiCell(0,4,'DATOS DEL CONTRIBUYENTE, CAUSANTE O AGENTE DE RETENCIÓN',$linea_fija,'L',1);

//---------------
$pdf->SetFont('Times','',8);
//---------------
$pdf->SetXY(17,38);
$pdf->Cell(30,0,'RIF',$linea_muestra,0,'C');
$pdf->MultiCell(80,0,'APELLIDOS Y NOMBRES - RAZON SOCIAL',$linea_muestra,'C');

$pdf->SetFont('Times','B',10);
//---------------
$pdf->SetXY(17,41);
$pdf->Cell(30,0,strtoupper(formato_rif($registro->rif)),$linea_muestra,0,'C');
$pdf->MultiCell(132,0,$registro->contribuyente,$linea_muestra,'L');

$pdf->SetFont('Times','',8);
//---------------
$pdf->SetXY(17,45);
$pdf->Cell(30,0,'DIRECCIÓN',$linea_muestra,0,'C');

$pdf->SetFont('Times','B',10);
//---------------
$pdf->SetXY(17,46);
$pdf->MultiCell(0,3,$registro->direccion,$linea_muestra,'J');

//---------------
$pdf->ln(3);
//---------------
$pdf->SetFont('Times','B',10);
$pdf->MultiCell(0,4,'IDENTIFICACIÓN DE LA DECLARACIÓN',$linea_fija,'L',1);

//---------------
$pdf->ln(2);
//---------------
$pdf->SetFont('Times','',6); 
//---------------
$pdf->Cell(20,0,'COD. TRIBUTO',$linea_muestra,0,'C');
$pdf->Cell(40,0,'TIPO DE TRIBUTO',$linea_muestra,0,'C');
$pdf->Cell(60,0,'',$linea_muestra,'L');
$pdf->Cell(60,0,'PERIODO O EJERCICIO FISCAL',$linea_muestra,0,'C');

$pdf->ln(3);
//---------------
$pdf->SetFont('Times','B',10);
//---------------
$pdf->Cell(20,0,'3',$linea_muestra,0,'C');
$pdf->Cell(40,0,'INTERES '.$registro->Abreviatura,0,'C');
$pdf->Cell(60,0,'',$linea_muestra,'L');
$pdf->Cell(60,0,voltea_fecha($registro->periodoinicio).' al '.voltea_fecha($registro->periodofinal),$linea_muestra,0,'C');

$pdf->SetFont('Times','B',10);
//---------------
$pdf->ln(4);
$pdf->Cell(0,4,'DEMOSTRACION DE LA LIQUIDACION',$linea_fija,0,'L',1);

$pdf->ln(5);
//---------------
$pdf->SetFont('Times','',8);
//---------------
$pdf->MultiCell(0,3,'    DE CONFORMIDAD CON LO ESTABLECIDO EN EL ARTICULO 66 DEL CODIGO ORGANICO TRIBUTARIO VIGENTE, SE PROCEDE  A EMITIR LA PRESENTE PLANILLA DE LIQUIDACION  POR  CONCEPTO  DE  INTERESES MORATORIOS,  POR CUANTO EL (LA) CONTRIBUYENTE EFECTUO  EL  PAGO  DE  LA OBLIGACIÓN TRIBUTARIA FUERA DEL LAPSO ESTABLECIDO, DE ACUERDO A LA SIGUIENTE DETERMINACION:',$linea_muestra,'J');

$pdf->ln(2);
//---------------
$pdf->SetFont('Times','B',8);
//---------------
$pdf->Cell(12,3,'',$linea_muestra,0,'C');
$pdf->Cell(35,3,'MONTO TRIBUTO Bs.',$linea_fija,0,'C',1);
$pdf->Cell(35,3,'FECHA EXIGIBILIDAD',$linea_fija,0,'C',1);
$pdf->Cell(30,3,'FECHA DE PAGO',$linea_fija,0,'C',1);
$pdf->Cell(25,3,'DIAS DE MORA',$linea_fija,0,'C',1);
$pdf->Cell(35,3,'TOTAL INTERESES Bs.',$linea_fija,0,'C',1);
$pdf->ln(3);
//---------------
$pdf->SetFont('Times','',9);
//---------------
$pdf->Cell(12,4,'',$linea_muestra,0,'C');
$pdf->Cell(35,4,formato_moneda($registro->monto_pagado),$linea_fija,0,'C');
$pdf->Cell(35,4,voltea_fecha($registro->fecha_vencimiento),$linea_fija,0,'C');
$pdf->Cell(30,4,voltea_fecha($registro->fecha_pago),$linea_fija,0,'C');
$pdf->Cell(25,4,number_format(doubleval($txt),0,',','.'),$linea_fija,0,'C');
$pdf->Cell(35,4,formato_moneda($registro->monto_bs/$registro->concurrencia*$registro->especial),$linea_fija,0,'C');

// ------------
$pdf->ln(6);
//---------------
$pdf->SetFont('Times','B',10);
//---------------
$pdf->Cell(0,4,'DEMOSTRACIÓN DEL CALCULO DE INTERESES MORATORIOS',$linea_muestra,0,'C');
$pdf->ln(7);

//---------------
$pdf->SetFont('Times','B',7);
//---------------

$x = $pdf->GetX();
$y = $pdf->GetY();

//  COLUMNA 1
$x = $x + 2;
$pdf->SetXY($x,$y);
$a = 20;
$pdf->Cell($a,6,'MES',$linea_fija,0,'C');

$x = $x + $a;
$pdf->SetXY($x,$y);
$b = 14;
$pdf->MultiCell($b,3,'TASA DEL MES',$linea_fija,'C');

$x = $x + $b;
$pdf->SetXY($x,$y);
$c = 13 ;
$pdf->MultiCell($c,3,'DIAS DE MORA',$linea_fija,'C');

$x = $x + $c;
$pdf->SetXY($x,$y);
$d = 19;
$pdf->MultiCell($d,3,'INTERES DEL MES Bs.',$linea_fija,'C');

$x = $x + $d;
$pdf->SetXY($x,$y);
$e = 25;
$pdf->MultiCell($e,3,'INTERES ACUMULADO Bs.',$linea_fija,'C');

//  COLUMNA 2
$x = $x + $e + 1;
$pdf->SetXY($x,$y);
$pdf->Cell($a,6,'MES',$linea_fija,0,'C');

$x = $x + $a;
$pdf->SetXY($x,$y);
$pdf->MultiCell($b,3,'TASA DEL MES',$linea_fija,'C');

$x = $x + $b;
$pdf->SetXY($x,$y);
$pdf->MultiCell($c,3,'DIAS DE MORA',$linea_fija,'C');

$x = $x + $c;
$pdf->SetXY($x,$y);
$pdf->MultiCell($d,3,'INTERES DEL MES Bs.',$linea_fija,'C');

$x = $x + $d;
$pdf->SetXY($x,$y);
$pdf->MultiCell($e,3,'INTERES ACUMULADO Bs.',$linea_fija,'C');

$pdf->ln(3);
?>