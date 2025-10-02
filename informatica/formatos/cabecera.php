<?php

$fuente_cabecera = 8;
$alto_cabecera = 3.5;

global $color;

$a=20 ; //codigo 	
$b=80 ; //denominacion
$c=20 ; //cedula	
$d=80 ; //nombres

//------------------------
//$this->SetFillColor(170,166,166);
$this->Image('../../imagenes/logo.jpeg',20,18,40);
$this->Image('../../imagenes/cuadro_lleno.jpg',92,25,3);
$this->Image('../../imagenes/cuadro_vacio.jpg',152,25,3);
//----------------------------
$this->SetY(20);

//---------------------------
$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(45,$alto_cabecera,'',0,0,'C');
$this->Cell(160,$alto_cabecera,'COMPROBANTE DE '.$comprobante,0,0,'C');
$this->Cell(0,$alto_cabecera,'N° '.$this->PageNo().' de {nb}',0,0,'C');	
$this->Ln(4);

//---------------------------
$this->SetFont('Arial','',$fuente_cabecera);
$this->Cell(65,$alto_cabecera,'',0,0,'C');
$this->Cell(60,$alto_cabecera,'BIENES MUEBLES',1,0,'C');
$this->Cell(60,$alto_cabecera,'MATERIALES',1,0,'C');
$this->Cell(20,$alto_cabecera,'',0,0,'C');

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(0,$alto_cabecera,'Fecha: '.date('d/m/Y'),0,0,'C');	
$this->Ln(3);	
//---------------------------


$this->Ln(5);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(0,$alto_cabecera,'ORGANISMO',1,0,'L');
$this->Ln($alto_cabecera);

$x=$this->GetX();
$y=$this->GetY();

$this->SetFont('Arial','',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'Código',0,0,'L');
$this->Cell(0,$alto_cabecera,'Denominación',0,0,'L');	
$this->Ln($alto_cabecera);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'07',0,0,'R');
$this->Cell(0,$alto_cabecera,'MINISTERIO DEL PODER POPULAR PARA LA BANCA Y FINANZAS',0,0,'C');		

$this->SetY($y);
$this->SetX($x);

$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
$this->Ln($alto_cabecera*2);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(0,$alto_cabecera,'UNIDAD ADMINISTRADORA',1,0,'L');
$this->Ln($alto_cabecera);

$x=$this->GetX();
$y=$this->GetY();

$this->SetFont('Arial','',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'Código',0,0,'L');
$this->Cell(0,$alto_cabecera,'Denominación',0,0,'L');	
$this->Ln($alto_cabecera);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'98067',0,0,'R');
$this->Cell(0,$alto_cabecera,'SERVICIO NACIONAL INTEGRADO DE ADMINISTRACIÓN ADUANERA Y TRIBUTARIA (SENIAT)',0,0,'C');		

$this->SetY($y);
$this->SetX($x);

$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
$this->Ln($alto_cabecera*2);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(0,$alto_cabecera,'DEPENDENCIA USUARIA',1,0,'L');
$this->Ln($alto_cabecera);

$x=$this->GetX();
$y=$this->GetY();

$this->SetFont('Arial','',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'Código',0,0,'L');
$this->Cell(0,$alto_cabecera,'Denominación',0,0,'L');	
$this->Ln($alto_cabecera);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'98067',0,0,'R');

//----- POR SI SON REASIGNACIONES
if ($_GET['comprobante']==21 or $_GET['comprobante']==31)
	{
	$this->Cell(0,$alto_cabecera,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS '.strtoupper(buscar_region()).' - '.strtoupper($division).' - '.strtoupper($area),0,0,'C');		
	}
else
	{
	//---------- FILTROS
	if ($_SESSION['AREA']==0) 	//$_SESSION['AREAS'] == 'TODAS' and 
		{
		//////// ---- 
		$consulta_xxx = "SELECT id_area FROM vista_bienes_informatica WHERE id_division=".$_SESSION['DIVISION']." AND borrado=0 ORDER BY id_area";
		$tabla_xxx = mysql_query($consulta_xxx);
		$registro_xxx = mysql_fetch_object($tabla_xxx);
		$_SESSION['AREA_ACTUAL'] = $registro_xxx->id_area;
		}	 
	else	
		{		$_SESSION['AREA_ACTUAL'] = $_SESSION['AREA'];		}
	//-----------------------------------------------
	if ($_SESSION['AREAS'] <> 'ULTIMA')
		{
		$this->Cell(0,$alto_cabecera,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS '.strtoupper(buscar_region()).' - '.strtoupper(division($_SESSION['DIVISION'])).' - '.strtoupper(area($_SESSION['AREA_ACTUAL'])),0,0,'C');
		}
	else
		{
		$this->Cell(0,$alto_cabecera,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS '.strtoupper(buscar_region()).' - '.strtoupper(division($_SESSION['DIVISION'])).' - '.'RESUMEN',0,0,'C');
		}	
	//-----------------
	$_SESSION['DIVISION2'] = $_SESSION['DIVISION'];
	}

$this->SetY($y);
$this->SetX($x);

$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
$this->Ln($alto_cabecera*2);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(0,$alto_cabecera,'RESPONSABLE DEL ALMACEN',1,0,'L');
$this->Ln($alto_cabecera);

$x=$this->GetX();
$y=$this->GetY();

$this->SetFont('Arial','',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'Código',0,0,'L');
$this->Cell($b,$alto_cabecera,'Denominación',0,0,'L');
$this->Cell($c,$alto_cabecera,'C.I.',0,0,'L');
$this->Cell($d,$alto_cabecera,'Apellidos y Nombres',0,0,'L');
$this->Cell(0,$alto_cabecera,'Cargo',0,0,'L');	
$this->Ln($alto_cabecera);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell($a,$alto_cabecera,'5032',0,0,'R');
$this->Cell($b,$alto_cabecera,'',0,0,'L');
$this->Cell($c,$alto_cabecera,'',0,0,'L');
$this->Cell($d,$alto_cabecera,'',0,0,'L');
$this->Cell(0,$alto_cabecera,'',0,0,'L');	

$this->SetY($y);
$this->SetX($x);

$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
$this->Cell($b,$alto_cabecera*2,'',1,0,'L');
$this->Cell($c,$alto_cabecera*2,'',1,0,'L');
$this->Cell($d,$alto_cabecera*2,'',1,0,'L');
$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
$this->Ln($alto_cabecera*2);

?>