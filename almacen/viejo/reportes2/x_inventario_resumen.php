<?php
if ($_SESSION['AREAS']=='TODAS')
	{
	$_SESSION['AREAS'] = 'ULTIMA';
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
	$pdf->Cell($a,4,'',1,0,'C');
	$pdf->Cell($b,4,'',1,0,'L');
	$pdf->Cell($c,4,'',1,0,'L');
	$pdf->Cell($d,4,'RESUMEN DE BIENES NACIONALES',1,0,'C');
	$pdf->Cell($e,4,'',1,0,'L');	
	$pdf->Cell($f,4,'',1,0,'R');	
	$pdf->Ln(4);
	//----------------------
	$pdf->Cell($a,4,'',1,0,'C');
	$pdf->Cell($b,4,'',1,0,'L');
	$pdf->Cell($c,4,'',1,0,'L');
	$pdf->Cell($d,4,'DE LA '.strtoupper(division($_SESSION['DIVISION'])),1,0,'C');
	$pdf->Cell($e,4,'',1,0,'L');	
	$pdf->Cell($f,4,'',1,0,'R');	
	$pdf->Ln(4);
	//----------------------
	$pdf->Cell($a,4,'',1,0,'C');
	$pdf->Cell($b,4,'',1,0,'L');
	$pdf->Cell($c,4,'',1,0,'L');
	$pdf->Cell($d,4,'Fecha: '.date('d/m/Y'),1,0,'C');
	$pdf->Cell($e,4,'',1,0,'L');	
	$pdf->Cell($f,4,'',1,0,'R');	
	$pdf->Ln(4);
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
	$consulta_xxx = 'SELECT * FROM vista_bienes_resumen WHERE id_division='.$_SESSION['DIVISION'];
	$tabla_xxx = mysql_query ($consulta_xxx);
	while ($registro_xxx = mysql_fetch_object($tabla_xxx))
		{
		//-----------
		$pdf->Cell($a,4,$registro_xxx->cant,1,0,'C');
		$pdf->Cell($b,4,'',1,0,'L');
		$pdf->Cell($c,4,'',1,0,'L');
		$pdf->Cell($d,4,$registro_xxx->area,1,0,'L');
		$pdf->Cell($e,4,'',1,0,'L');	
		$pdf->Cell($f,4,formato_moneda($registro_xxx->total),1,0,'R');	
		$pdf->Ln(4);
		//----------------------
		$i++;
		}
	//----------------------
	while ($i<=16)
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
	$pdf->Cell($a,4,$_SESSION['i'],1,0,'C');
	$pdf->Cell($b,4,'',1,0,'L');
	$pdf->Cell($c,4,'',1,0,'L');
	$pdf->Cell($d,4,'',1,0,'C');
	$pdf->Cell($e,4,'TOTAL',1,0,'C');	
	$pdf->Cell($f,4,formato_moneda($_SESSION['monto']),1,0,'R');	
	$pdf->Ln(4);
	//---------------
	}
//-------------------	
?>