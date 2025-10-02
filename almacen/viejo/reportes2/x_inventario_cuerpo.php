<?php
$pdf->AddPage();
		
$_SESSION['monto'] = 0;	$linea = 1; $alto = 4; $_SESSION['i']=0;

//---------- FILTRO POR AREA
if ($_SESSION['AREA']==0 or $_SESSION['DIVISIONES']=='TODAS' ) //$_SESSION['AREAS'] == 'TODAS' and 
	{
	$filtro1 = "id_area";
	$orden1 = "area,";
	}
else
	{
	$filtro1 = "id_area=".$_SESSION['AREA'];
	$orden1 = "";
	} 

$_SESSION['AREA_ACTUAL'] = 0; 	$_SESSION['AREA_ANTERIOR'] = 0;	$i = 0;	$siguiente_area = 0;

//////// ---- DETALLE
$consulta_x = "SELECT * FROM vista_bienes_nacionales WHERE id_division=".$_SESSION['DIVISION']." AND ".$filtro1." AND borrado=0 ORDER BY ".$orden1." descripcion_bien, numero_bien";
$tabla_x = mysql_query($consulta_x); 
//echo '<br> Cuerpo => '.$consulta_x;

while ($registro_x = mysql_fetch_object($tabla_x))
	{	
	$_SESSION['AREA'] = $registro_x->id_area;
	//---- PARA COMENZAR EL CICLO CON LA PRIMERA AREA
	$i++;	if ($i==1) {	$_SESSION['AREA_ACTUAL'] = $registro_x->id_area;	$_SESSION['AREA_ANTERIOR'] = $_SESSION['AREA_ACTUAL'];	}
	//----------------
	
	$_SESSION['AREA_ACTUAL'] = $registro_x->id_area;
	//---- PARA EFECTUAR SALTO DE HOJA CUANDO CAMBIA DE AREA
	if ($_SESSION['AREA_ACTUAL']<>$_SESSION['AREA_ANTERIOR'])
		{	
		$_SESSION['AREA_ANTERIOR'] = $_SESSION['AREA_ACTUAL'];	
		$siguiente_area = 1;
		if ($y1 > 170 or $y2 > 170 or $siguiente_area == 1) 
			{
			while ($pdf->GetY()<=170)
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
			}
		}
	
	//++++++++++++++++++++++++++
	if ($y1 > 170 or $y2 > 170 or $siguiente_area == 1) 
		{ 
		if ($siguiente_area == 1)	{	$pdf->SetY(-42);	}
		$siguiente_area = 0;
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
	$pdf->Cell($b,($alto2), $registro_x->codigo,$linea,0,'C'); 
	$pdf->Cell($c,($alto2), $registro_x->numero_bien,$linea,0,'C'); 
	//--- --------------------------------------MULTICELL
	$x=$pdf->GetX();
	$pdf->MultiCell($d,$alto, ucfirst(strtolower($registro_x->descripcion_bien)),$linea,'J');
	$y2=$pdf->GetY();
	//- PARA PONER LAS COORDENADAS DESPUES DEL MULTICELL
	$pdf->SetY($y1);
	$pdf->SetX($x+$d);
	//---------------------------------------------------
	$pdf->Cell($e,($alto2), $registro_x->conservacion,$linea,0,'C'); 
	$pdf->Cell($f,($alto2), formato_moneda($registro_x->valor),$linea,0,'R'); 
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
	$pdf->Ln(4);
	//----------------------
	$i++;
	}
	
// TOTAL GENERAL
$pdf->SetY(-41.8);
$pdf->SetFont('Arial','B',$fuente_cabecera);
$pdf->Cell(20,$alto,$_SESSION['i'],1,0,'C');
$pdf->Cell(25,$alto,'',1,0,'L');
$pdf->Cell(25,$alto,'',1,0,'L');
$pdf->Cell(130,$alto,'',1,0,'C');
$pdf->Cell(23,$alto,'TOTAL',1,0,'C');	
$pdf->Cell(0,$alto,formato_moneda($_SESSION['monto']),1,0,'R');	
//----------------------------------
		
?>