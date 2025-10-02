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

include('../../funciones/origen_funcionario.php');

setlocale(LC_TIME, 'sp_ES','sp', 'es');
mysql_query("SET NAMES 'latin1'");

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
			
			//BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
			$consulta_x = "SELECT nombre, tipo_division FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
			$tabla_x = mysql_query($consulta_x);
			$regstro_x = mysql_fetch_object($tabla_x);
			$area = $regstro_x->tipo_division;
			$dependencia = $regstro_x->nombre;
			
			//IDENTIFICACION DE LA SEDE
			$this->SetXY(90,11);
			$this->Cell(0,5,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.mb_convert_case(buscar_region(), MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->SetXY(90,16);
			
			//--------- PARA BUSCAR LA DIVISION DEPENDE DEL ORIGEN USUARIO
			if ($_SESSION['ORIGEN_USUARIO']==0)	{	$texto1	= 'Tramitaciones';}
			if ($_SESSION['ORIGEN_USUARIO']==2)	{	$texto1	= 'Sujetos Pasivos Especiales';}
			if ($_SESSION['ORIGEN_USUARIO']==4)	{	$texto1	= 'Fiscalización';}						
			//---------
			$fecha1 = $_GET['fecha1'];
			$fecha2 = $_GET['fecha2'];
			
			$this->Cell(0,5,mb_convert_case('Dependencia: '.$dependencia, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->SetXY(90,21);
			$this->Cell(0,5,mb_convert_case($area.' de '.$texto1, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C');
			$this->Ln(7);
			
			//TITULO DEL REPORTE
			$this->SetFont('Times','B',14);
			$this->Cell(0,5,'Relación de Expedientes Asignados desde el día '.$fecha1.' al día '.$fecha2,0,1,'C');
			$this->Ln(4);
			$this->SetFillColor(170,166,166);
			$this->SetFont('Arial','B',10);

			//-------------------------			
			$a=20 ;
			$b=90 ;
			$c=35 ;
			$d=35 ;
			$e=20 ;
			$f=20 ;
			$g=20 ;
			//-------------------------			
			
			$this->Cell($a,5,'Rif',1,0,'C',true);
			$this->Cell($b,5,'Contribuyente',1,0,'C',true);
			$this->Cell($c,5,'Periodo',1,0,'C',true);
			$this->Cell($d,5,'Liquidacion',1,0,'C',true);
			$this->Cell($e,5,'Fecha Liq',1,0,'C',true);
			$this->Cell($f,5,'Tributo',1,0,'C',true);
			$this->Cell($g,5,'Monto',1,0,'C',true);						
		}
		function Footer()
		{
		//---------
		$fecha1 = $_GET['fecha1'];
		$fecha2 = $_GET['fecha2'];
			
		//OBTENEMOS LA CANTIDAD DE PROVIDENCIAS    
		$consulta = "SELECT COUNT(DISTINCTROW sector, origen_liquidacion, anno_expediente, num_expediente) AS cantidad FROM vista_liquidacion_planillas WHERE fecha_importacion_a_not<>'' AND (fecha_asignacion_notificador >= '".voltea_fecha($fecha1)."' AND fecha_asignacion_notificador <= '".voltea_fecha($fecha2)."') AND sector=".$_GET['sede']." ". $origen. ';';
		$tabla = mysql_query($consulta);
		if ($providencia = mysql_fetch_object($tabla)) { $cant_prov = $providencia->cantidad; };

		//OBTENEMOS LA CANTIDAD DE PLANILLAS
		$consulta = "SELECT COUNT(num_expediente) AS cantidad FROM vista_liquidacion_planillas WHERE fecha_importacion_a_not<>'' AND (fecha_asignacion_notificador >= '".voltea_fecha($fecha1)."' AND fecha_asignacion_notificador <= '".voltea_fecha($fecha2)."') AND sector=".$_GET['sede']." ". $origen. ';';
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
		$this->Cell(0,5,'IMPRESO POR: '.$nombre_usuario,0,1,'L');
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

//---------------
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];

//TITULO DEL CUADRO
$a=20 ;
$b=90 ;
$c=35 ;
$d=35 ;
$e=20 ;
$f=20 ;
$g=20 ;
			
$linea = 0;
$i=1;
$Monto_Total = 0;

$consulta = "SELECT rif, contribuyente, liquidacion, periodoinicio, periodofinal, fecha_liquidacion, siglas, (monto_bs/concurrencia*especial) as monto FROM vista_liquidacion_planillas WHERE fecha_importacion_a_not<>'' AND (fecha_asignacion_notificador >= '".voltea_fecha($fecha1)."' AND fecha_asignacion_notificador <= '".voltea_fecha($fecha2)."') AND sector=".$_GET['sede']." ". $origen. ';';
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
		$pdf->Cell($a+$b+$c+$d+$e,5," = = = = = = = = = >  Total  x  Expediente  = = = = = = = = = >",1,0,'R',true); 
		$pdf->Cell($f+$g,5,'Bs. '.formato_moneda($Monto_Total),1,0,'R',true); 
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
	if ($y1 > 165) { $pdf->AddPage(); 	$pdf->Ln(); 	$y1=$pdf->GetY();}
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
	$pdf->Cell($c,5,voltea_fecha($registro->periodoinicio).' - '.voltea_fecha($registro->periodofinal),$linea,0,'C'); 
	$pdf->Cell($d,5,$registro->liquidacion,$linea,0,'C'); 
	$pdf->Cell($e,5,voltea_fecha($registro->fecha_liquidacion),$linea,0,'C'); 
	$pdf->Cell($f,5,$registro->siglas,$linea,0,'C'); 
	$pdf->Cell($g,5,formato_moneda($registro->monto),$linea,0,'R'); 
	//--------------------
	$Monto_Total = $Monto_Total+$registro->monto;
	// ----- PARA EL CUADRO
	$pdf->SetY($y1);
	$pdf->Cell($a,$y2-$y1,"",1); 
	$pdf->Cell($b,$y2-$y1,"",1); 
	$pdf->Cell($c,$y2-$y1,"",1); 
	$pdf->Cell($d,$y2-$y1,"",1); 
	$pdf->Cell($e,$y2-$y1,"",1); 
	$pdf->Cell($f,$y2-$y1,"",1); 
	$pdf->Cell($g,$y2-$y1,"",1); 
	//---------------------
	$pdf->Ln($y2-$y1);

}
// CONDICION PARA EL SUBTOTAL
$pdf->SetFont('Times','B',9);
$pdf->SetFillColor(200,200,200);
$pdf->Cell($a+$b+$c+$d+$e,5," = = = = = = = = = >  Total  x  Expediente  = = = = = = = = = >",1,0,'R',true); 
$pdf->Cell($f+$g,5,'Bs. '.formato_moneda($Monto_Total),1,0,'R',true); 
$pdf->Ln(5);
//-------------------
$Rif_Proceso=$registro->rif;
$Monto_Total=0;
//++++++++++++++++++++++++++
$pdf->SetFont('Times','',9);
	
$pdf->Output();
?>