<?php
$interes = $interes + (((($registro->monto_pagado/100) * ($tasa * 1.20)) / 360) * $Dias) ;
//------------------------------------------------------------------------
$interes_acum += $interes ;
//------------------------------------------------------------------------
$pdf->SetXY($x,$y);
$pdf->Cell($a,$alto,$mes_letras[number_format(doubleval($MES),0,',','.')].' - '.$AÑO,$linea_muestra,0,'L');
$pdf->Cell($b,$alto,formato_moneda($tasa),$linea_muestra,0,'C');
$pdf->Cell($c,$alto,$Dias,$linea_muestra,0,'C');
$pdf->Cell($d,$alto,formato_moneda($interes),$linea_muestra,0,'C');
$pdf->MultiCell($e,$alto,formato_moneda($registro->monto_bs/$registro->concurrencia*$registro->especial),$linea_muestra,'C');
//------------------------------------------------------------------------
$Dias = 0;
// ---- PARA CONTROLAR LA LINEA
if ($x >= 92)	
	{
	$x = $x - 92;
	$y = $y + 3;	
	}
else
	{
	$x = $x + 92;
	}
// FIN
$MES = date('m',$FECHA_VENCIMIENTO);
$AÑO = date('Y',$FECHA_VENCIMIENTO);
$interes = 0;	
?>