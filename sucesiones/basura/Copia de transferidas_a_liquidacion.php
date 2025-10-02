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
			$this->Cell(0,5,mb_convert_case($area.' de Recaudación: Sucesiones', MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->SetXY(90,21);
			$this->Cell(0,5,mb_convert_case('Dependencia: '.$dependencia, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->Ln(5);
			
			//TITULO DEL REPORTE
			$this->SetFont('Times','B',14);
			$this->Cell(0,5,'Relación de Expedientes Exportados a Liquidación Desde: '.date("d-m-Y",strtotime($_SESSION['INICIO'])).' Hasta: '.date("d-m-Y",strtotime($_SESSION['FIN'])),0,1,'C');
			$this->Ln(2);
			$this->SetFillColor(170,166,166);
			/*$color=true;
			$this->SetWidths(array(12,25,25,60,12,80,30));
			$this->Row(array('Prov', 'Fecha', 'Rif', 'Contribuyente','Serie','Descripcion','Monto'));*/
			$this->SetFont('Arial','B',10);
			$this->Cell(10,5,'Exp',1,0,'C',true);
			$this->Cell(25,5,'Fecha',1,0,'C',true);
			$this->Cell(25,5,'Rif',1,0,'C',true);
			$this->Cell(60,5,'Contribuyente',1,0,'C',true);
			$this->Cell(10,5,'Serie',1,0,'C',true);
			$this->Cell(80,5,'Descripción',1,0,'C',true);
			$this->Cell(30,5,'Monto',1,1,'C',true);
		}
		function Footer()
		{
		//OBTENEMOS LA CANTIDAD DE EXPEDIENTES    
		$consulta = "SELECT  COUNT(DISTINCTROW numero) AS cantidad FROM vista_transferidas_suc_liquidacion WHERE fecha_transferencia_a_liq BETWEEN '".$_SESSION['INICIO']."' AND '".$_SESSION['FIN']."' AND sector=".$_SESSION['SEDE_USUARIO'].";";
		$tabla = mysql_query($consulta);
		if ($providencia = mysql_fetch_object($tabla)) { $cant_prov = $providencia->cantidad; };

		//OBTENEMOS LA CANTIDAD DE PLANILLAS
		$consulta = "SELECT  COUNT(numero) AS cantidad FROM vista_transferidas_suc_liquidacion WHERE fecha_transferencia_a_liq BETWEEN '".$_SESSION['INICIO']."' AND '".$_SESSION['FIN']."' AND sector=".$_SESSION['SEDE_USUARIO'].";";
		$tabla = mysql_query($consulta);
		if ($planillas = mysql_fetch_object($tabla)) { $cant_plan = $planillas->cantidad; };

		$this->SetFont('Arial','B',10);
		$this->SetFillColor(170,166,166);
		$this->SetXY(182,-30);
		$this->Cell(40,5,'Total Expedientes',1,0,'C',true);
		$this->Cell(40,5,'Total Planillas',1,1,'C',true);
		$this->SetXY(182,-35);
		$this->Cell(40,5,$cant_prov,1,0,'C');
		$this->Cell(40,5,$cant_plan,1,1,'C');

		//IDENTICACION DEL USUARIO
		$consulta = "SELECT CONCAT_WS(' ',z_empleados.Nombres,z_empleados.Apellidos) AS nombre FROM z_empleados WHERE z_empleados.cedula = ".$_SESSION['CEDULA_USUARIO'];
		$tabla = mysql_query($consulta);
		if ($usuario = mysql_fetch_object($tabla)) { $nombre_usuario = $usuario->nombre; };		
		$this->Cell(0,5,'TRANSFERIDO POR: '.$nombre_usuario,0,1,'L');
		$this->Cell(0,15,'RECIBIDO POR: ____________________________',0,1,'L');

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
$a = 10;
$b = 25;
$c = 25;
$d = 60;
$e = 10;
$f = 80;
$g = 30;

$linea = 0;
$i=1;
$consulta = "SELECT anno, numero, fecha_emision, rif, contribuyente, serie, descripcion, monto, fecha_transferencia_a_liq, sector FROM vista_transferidas_suc_liquidacion WHERE fecha_transferencia_a_liq BETWEEN '".$_SESSION['INICIO']."' AND '".$_SESSION['FIN']."' AND sector=".$_SESSION['SEDE_USUARIO']." ORDER BY anno, numero ASC;";
$tabla = mysql_query($consulta);

while ($registro = mysql_fetch_object($tabla))
{
	//--- PARA CALCULAR LA ALTURA MAXIMA DEL MULTICELL - SANCION
	$pdf->SetTextColor(255,255,255);
	$y1=$pdf->GetY();
	$pdf->MultiCell($f,5, (($registro->descripcion)),0,'J');
	$y2=$pdf->GetY();
	$alto2 = ($y2-$y1);
	$pdf->SetY($y1);
	//----------------------------------
	//--- PARA CALCULAR LA ALTURA MAXIMA DEL MULTICELL - CONTRIBUYENTE
	$y1=$pdf->GetY();
	$pdf->MultiCell($d,5, (($registro->contribuyente)),0,'J');
	$y2=$pdf->GetY();
	$alto3 = ($y2-$y1);	
	// POR SI EL NOMBRE ES MAS ALTO QUE LA DESCRIPCION
	//if ($alto3>5) 	{	$alto4 = 5;	}	else  {	$alto4 = $alto2;	}	
	if ($alto3 > $alto2) 	{	$alto2 = $alto3;	}	else  {	$alto3 = 5;	$alto4 = $alto2;	}	
	//------------------
	$pdf->SetY($y1);
	$pdf->SetTextColor(0,0,0);
	//----------------------------------
	
	// ----- PARA EL TEXTO
	$y1=$pdf->GetY();
	//---------
	if ($y1 > 163) { $pdf->AddPage(); 	$y1=$pdf->GetY();}
	//---------
	$pdf->Cell($a,($alto2),$registro->numero,$linea,0,'C'); 
	$pdf->Cell($b,($alto2),voltea_fecha($registro->fecha_emision),$linea,0,'C'); 
	$pdf->Cell($c,($alto2),$registro->rif,$linea,0,'C'); 
	//--- MULTICELL
	$x=$pdf->GetX();
	$pdf->MultiCell($d,5,$registro->contribuyente,$linea,'J');
	//- PARA PONER LAS COORDENADAS DESPUES DEL MULTICELL
	$pdf->SetY($y1);
	$pdf->SetX($x+$d);
	//--------------------------------------------------
	$pdf->Cell($e,($alto2),$registro->serie,$linea,0,'C',0); 
	//--- MULTICELL
	$x=$pdf->GetX();
	$pdf->MultiCell($f,$alto3,$registro->descripcion,$linea,'J');
	$y2=$pdf->GetY();
	//- PARA PONER LAS COORDENADAS DESPUES DEL MULTICELL
	$pdf->SetY($y1);
	$pdf->SetX($x+$f);
	//--------------------------------------------------
	$pdf->Cell($g,($alto2),formato_moneda($registro->monto),$linea,0,'R'); 
	//--------------------
	
	// ----- PARA EL CUADRO
	$pdf->SetY($y1);
	$pdf->Cell($a,($alto2),"",1); 
	$pdf->Cell($b,($alto2),"",1); 
	$pdf->Cell($c,($alto2),"",1); 
	$pdf->Cell($d,($alto2),"",1); 
	$pdf->Cell($e,($alto2),"",1); 
	$pdf->Cell($f,($alto2),"",1); 
	$pdf->Cell($g,($alto2),"",1); 
	//---------------------
	$pdf->Ln($y2-$y1);
}

$pdf->Output();
?>