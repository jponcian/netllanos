<?php
if ($_SESSION['SEDE']=='0')
	{
	$pdf->AddPage();
	//-----------
	$pdf->SetFont('Arial','B',8);
	//----------- LINEA EN BLANCO
	$pdf->Cell($a,4,'',1,0,'C');
	$pdf->Cell($b,4,'',1,0,'L');
	$pdf->Cell($c,4,'',1,0,'L');
	$pdf->Cell($d,4,'',1,0,'C');
	$pdf->Cell($e,4,'',1,0,'L');	
	$pdf->Cell($f,4,'',1,0,'R');	
	$pdf->Ln(4);
	//----------------------
	$i=0;
	//----------------------
	$consulta_xxx = 'SELECT sum(cant) as cant1, division, sum(total) as total1 FROM vista_bienes_resumen WHERE id_division<100 GROUP BY division ORDER BY orden';
	$tabla_xxx = mysql_query($consulta_xxx);
	while ($registro_xxx = mysql_fetch_object($tabla_xxx))
		{
		$_SESSION['i'] = $_SESSION['i'] + $registro_xxx->cant1;
		$_SESSION['monto'] = $_SESSION['monto'] + $registro_xxx->total1;		
		//-----------
		$pdf->Cell($a,4,$registro_xxx->cant1,1,0,'C');
		$pdf->Cell($b,4,'',1,0,'L');
		$pdf->Cell($c,4,'',1,0,'L');
		$pdf->Cell($d,4,$registro_xxx->division,1,0,'L');
		$pdf->Cell($e,4,'',1,0,'L');	
		$pdf->Cell($f,4,formato_moneda($registro_xxx->total1),1,0,'R');	
		$pdf->Ln(4);
		//----------------------
		$i++;
		}
	//----------------------
	while ($i<=19)
		{
		//----------- LINEA EN BLANCO
		$pdf->Cell($a,4,'',1,0,'C');
		$pdf->Cell($b,4,'',1,0,'L');
		$pdf->Cell($c,4,'',1,0,'L');
		$pdf->Cell($d,4,'',1,0,'C');
		$pdf->Cell($e,4,'',1,0,'L');	
		$pdf->Cell($f,4,'',1,0,'R');	
		$pdf->Ln(4);
		//----------------------
		$i++;
		}

	//----------------------
	$pdf->Cell($a,4,redondea($_SESSION['i']),1,0,'C');
	$pdf->Cell($b+$c,4,'TOTAL DE BIENES',1,0,'C');
	$pdf->Cell($d,4,'',1,0,'C');
	$pdf->Cell($e,4,'TOTAL',1,0,'C');	
	$pdf->Cell($f,4,formato_moneda($_SESSION['monto']),1,0,'R');	
	$pdf->Ln(4);
	//---------------
	}
//-------------------	
?>