<?php
mysql_query("SET NAMES 'latin1'");
// INICIO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
//$pdf->SetAutoPageBreak(1,50);
$pdf->AddPage();
//-------------------
		
$_SESSION['monto'] = 0;	$linea = 1; $alto = 4; $_SESSION['i']=0;
$id_reasignacion = $_GET['id'];

//////// ---- DETALLE
$consulta_x = "SELECT * FROM vista_bienes_reasignaciones_solicitadas WHERE borrado=0 AND id_reasignacion=".$id_reasignacion;
$tabla_x = mysql_query($consulta_x);

while ($registro_x = mysql_fetch_object($tabla_x))
{
	//++++++++++++++++++++++++++
	if ($y1 > 170 or $y2 > 170) 
		{ 
		$pdf->SetFont('Arial','B',$fuente_cabecera);
		$pdf->Cell(20,$alto,$_SESSION['i'],1,0,'C');
		$pdf->Cell(25,$alto,'',1,0,'L');
		$pdf->Cell(25,$alto,'',1,0,'L');
		$pdf->Cell(130,$alto,'VAN',1,0,'C');
		$pdf->Cell(23,$alto,'SUBTOTAL',1,0,'C');	
		$pdf->Cell(0,$alto,formato_moneda($_SESSION['monto']),1,0,'R');	
		//----------------------------------
		$pdf->AddPage();  $y1=$pdf->GetY();	
		}
	//----------------------------------
	
	//-------------------
	$pdf->SetFont('Times','',9);

	//--- PARA CALCULAR LA ALTURA MAXIMA DEL MULTICELL
	$pdf->SetTextColor(255,255,255);
	$y1=$pdf->GetY();
	$pdf->MultiCell($d,$alto, ucfirst(strtolower($registro_x->descripcion_bien)),0,'J');
	$y2=$pdf->GetY();
	$alto2 = ($y2-$y1);
	$pdf->SetTextColor(0,0,0);
	//----------------------------------
	
	//----- PARA ARRANCAR CON LA LINEA
	$pdf->SetY($y1);
	//----------------------------------------
	$pdf->Cell($a,($alto2),'01',$linea,0,'C');
	$pdf->Cell($b,($alto2), $registro_x->codigo_categoria,$linea,0,'C'); 
	$pdf->Cell($c,($alto2), $registro_x->numero_bien,$linea,0,'C'); 
	//--- --------------------------------------MULTICELL
	$x=$pdf->GetX();
	$pdf->MultiCell($d,$alto, ucfirst(strtolower($registro_x->descripcion_bien)),$linea,'J');
	$y2=$pdf->GetY();
	//- PARA PONER LAS COORDENADAS DESPUES DEL MULTICELL
	$pdf->SetY($y1);
	$pdf->SetX($x+$d);
	//---------------------------------------------------
	$pdf->Cell($e,($alto2), $comprobante,$linea,0,'C'); 
	$pdf->Cell($f,($alto2), $concepto,$linea,0,'C'); 
	$pdf->Cell($g,($alto2), formato_moneda_bienes($registro_x->valor),$linea,0,'R'); 
	$pdf->Cell($h,($alto2), formato_moneda_bienes($registro_x->valor),$linea,0,'R'); 
	//--------------------
	$_SESSION['monto'] = $_SESSION['monto']+($registro_x->valor);
	
	//---------------------
	$pdf->Ln($alto2);
	$_SESSION['i']++;
}

while ($pdf->GetY()<=170)
	{
	//----------- LINEA EN BLANCO
	$pdf->Cell($a,4,'',1,0,'C');
	$pdf->Cell($b,4,'',1,0,'L');
	$pdf->Cell($c,4,'',1,0,'L');
	$pdf->Cell($d,4,'',1,0,'C');
	$pdf->Cell($e,4,'',1,0,'L');	
	$pdf->Cell($f,4,'',1,0,'R');
	$pdf->Cell($g,4,'',1,0,'L');	
	$pdf->Cell($h,4,'',1,0,'R');	
	$pdf->Ln(4);
	//----------------------
	$i++;
	}
				
// TOTAL GENERAL
$pdf->SetY(-41.8);
$pdf->SetFont('Arial','B',$fuente_cabecera);
$pdf->Cell($a,$alto,$_SESSION['i'],1,0,'C');
$pdf->Cell($b+$c,$alto,'TOTAL CANTIDAD',1,0,'L');
$pdf->Cell($d+$e+$f,$alto,'TOTAL',1,0,'R');
$pdf->Cell($g,$alto,formato_moneda_bienes($_SESSION['monto']),1,0,'R');	
$pdf->Cell($h,$alto,formato_moneda_bienes($_SESSION['monto']),1,0,'R');	
//----------------------------------
		
?>