<?php
//---------------
$pdf->SetFont('Times','',7);
//---------------

$x = $pdf->GetX()+2;
$y = $pdf->GetY()-2;
$alto = 3;

// CALCULO DEL INTERES-------------------------------------------------------------------
$FECHA_VENCIMIENTO = $FECHA_VENCIMIENTO + 86400;
$interes = 0;
$Dias = 0;
$MES = date('m',$FECHA_VENCIMIENTO);
$AÑO = date('Y',$FECHA_VENCIMIENTO);
$interes_acum = 0;

// CONSULTA DE LAS TAZAS
$consulta_y = "SELECT * FROM a_tasa_interes ORDER BY anno";
$tabla_y = mysql_query( $consulta_y);
$registro_y = mysql_fetch_object($tabla_y);
// FIN CONSULTA DE LAS TAZAS

$mes_letras=array(0,Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);

while ($FECHA_VENCIMIENTO <= $FECHA_PAGO)
	{
	$Dias =  $Dias+1;
	//----------- BUSQUEDA DEL AÑO
	while ($registro_y->anno < date('Y',$FECHA_VENCIMIENTO)) 
		{
		$registro_y = mysql_fetch_object($tabla_y);
		$tazas = array('0',$registro_y->enero,$registro_y->febrero,$registro_y->marzo,$registro_y->abril,$registro_y->mayo,$registro_y->junio,$registro_y->julio,$registro_y->agosto,$registro_y->septiembre,$registro_y->octubre,$registro_y->noviembre,$registro_y->diciembre);	
		} 	
	// FIN DE LA BUSQUEDA DEL AÑO
	$tasa = $tazas[number_format(doubleval(date('m',$FECHA_VENCIMIENTO)),0,'','')];
	$FECHA_VENCIMIENTO = $FECHA_VENCIMIENTO + 86400;
	//---------- IMPRIMIR SI CAMBIA EL MES
	if ($MES <> date('m',$FECHA_VENCIMIENTO))
		{
		$interes = $interes + (($registro->monto_pagado * ($tasa*1.20)) / 36000) * $Dias;
		//------------------------------------------------------------------------
		$interes_acum += $interes ;
		//------------------------------------------------------------------------
		$pdf->SetXY($x,$y);
		$pdf->Cell($a,$alto,$mes_letras[number_format(doubleval($MES),0,',','.')].' - '.$AÑO,$linea_muestra,0,'L');
		$pdf->Cell($b,$alto,formato_moneda($tasa* 1.20),$linea_muestra,0,'C');
		$pdf->Cell($c,$alto,$Dias,$linea_muestra,0,'C');
		$pdf->Cell($d,$alto,formato_moneda($interes),$linea_muestra,0,'C');
		$pdf->MultiCell($e,$alto,number_format(doubleval($interes_acum),2,',','.'),$linea_muestra,'C');
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
		//------------------------------------------------ POR SI HAY MUCHOS INTERESES
		if ($y>215) 
			{
			include('0-firma.php');
			//-----------------
			include('0-encabezado_2pag.php');
			// --------------- NUEVAS COORDENADAS
			$pdf->SetFont('Times','',7);
			$x = 19;
			$y = 107;	
			}
		//-------------------
		// FIN
		$MES = date('m',$FECHA_VENCIMIENTO);
		$AÑO = date('Y',$FECHA_VENCIMIENTO);
		$interes = 0;	
		}
	}
// ----- PARA IMPRIMIR LA ULTIMA LINEA
include('0-cuerpo_intereses_1.php');
// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------
	
?>