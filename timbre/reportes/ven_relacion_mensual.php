<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
 
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
{
	//---------- ENCABEZADO
	function Header()
	{  
	// -------------------------------------------------------------------------------
	$linea_muestra = 0;
	$linea_fija = 1;
	//-------------
	// LOGO
	$this->Image('../../imagenes/logo.jpeg',17,8,55);
	
	////////// REGION DE EMISION
	$consulta_x = "SELECT Nombre FROM z_region;";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$Region=$registro_x->Nombre;
	// ---------------------
	$meses=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);
	list($mes,$anno)=explode('/',$_SESSION['var1']);
	//---------------
	$this->SetXY(0,10);
	$this->SetFont('Times','B',10);
	$this->Cell(0,6,'Fecha: '.date("d") . " de " . ($meses[(date("m")-1)]) . " de " . date("Y"),0,$linea_muestra,'R');
	//---------------
	
	//---------------
	$this->ln(0);
	$this->SetFont('Times','B',12);
	$this->MultiCell(0,6,'Relacion Mensual sobre las Ventas de Especies Fiscales ',$linea_muestra,'C',0);
	//---------------
	
	//---------------
	$this->SetFont('Times','B',10);
	$this->MultiCell(0,6,'Div. de Recaudación – Area de Timbre Fiscal',$linea_muestra,'C');
	//---------------

	//---------------
	$this->SetFont('Times','B',9);
	$this->MultiCell(0,6,ucfirst($Region),$linea_muestra,'C');
	$this->ln(2);
	//---------------

	//---------------
	$this->SetFont('Times','B',10);
	$this->MultiCell(0,6,'Mes: '. strtoupper($meses[(abs($mes))-1]).' '.$anno ,$linea_muestra,'L');
	$this->ln(2);
	//---------------
	
	//---------------
	$this->SetFont('Times','B',9);
	$this->Cell(141,6,'',$linea_muestra,0,'C');
	$this->Cell(57,6,'TIMBRES',$linea_fija,0,'C',1);
	$this->Cell(57,6,'PUBLICACIONES OFICIALES',$linea_fija,0,'C',1);
	$this->ln(6);
	//---------------
	
	//--------------
	$a=7;
	$b=15;
	$c=72;
	$d=10;
	$e=22;
	$f=15;
	$g=19;
	//--------------

	//---------------
	$this->SetFont('Times','B',9);
	$this->Cell($a,6,'N°',$linea_fija,0,'C',1);
	$this->Cell($b,6,'Fecha',$linea_fija,0,'C',1);
	$this->Cell($c,6,'Expendedor',$linea_fija,0,'C',1);
	$this->Cell($d,6,'N° Lic',$linea_fija,0,'C',1);
	$this->Cell($e,6,'N° Planilla',$linea_fija,0,'C',1);
	$this->Cell($f,6,'N° Liq',$linea_fija,0,'C',1);
	$this->Cell($g,6,'Ventas',$linea_fija,0,'C',1);
	$this->Cell($g,6,'Comisión',$linea_fija,0,'C',1);
	$this->Cell($g,6,'Recaudación',$linea_fija,0,'C',1);
	$this->Cell($g,6,'Ventas',$linea_fija,0,'C',1);
	$this->Cell($g,6,'Comisión',$linea_fija,0,'C',1);
	$this->Cell($g,6,'Recaudación',$linea_fija,0,'C',1);
	$this->ln(6);
	//---------------
	}	
	
	//---------- PIE
	function Footer()
	{    
	$this->SetY(-12);
	$s=$this->PageNo();
	//Arial itálica 8
	$this->SetFont('Times','I',10);
	$this->SetTextColor(120);
	$this->Cell(0,0,sistema().' '.$s.'/{nb}',0,0,'R');
	}
}

// ENCABEZADO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(12,30,12);
$pdf->SetAutoPageBreak(1,20);
$pdf->SetFillColor(210);
//---------------
$pdf->AddPage();
// -------------------------------------------------------------------------------
$linea_muestra = 0;
$linea_fija = 1;
//-------------

//--------- PARA RELLENAR EL CUADRO
$x=$pdf->GetX();
$y=$pdf->GetY();
//--------------

//--------------
$a=7;
$b=15;
$c=72;
$d=10;
$e=22;
$f=15;
$g=19;
//--------------

//--------------
list($mes,$anno)=explode('/',$_SESSION['var1']);
//--------------

$consulta = "SELECT date_format(timbre_ventas.fecha,'%d/%m/%Y') as fecha1, contribuyentes.contribuyente, timbre_ventas.licencia, timbre_ventas.planilla, timbre_ventas.liquidacion, timbre_ventas.monto, timbre_ventas.comision, timbre_ventas.total, timbre_expendedores.tipo FROM timbre_ventas INNER JOIN (timbre_expendedores INNER JOIN contribuyentes ON timbre_expendedores.rif = contribuyentes.rif) ON timbre_ventas.licencia = timbre_expendedores.licencia WHERE (((Month(fecha))=".$mes.") AND ((Year(fecha))=".$anno.")) ORDER BY timbre_ventas.liquidacion;";
$tabla = mysql_query($consulta);
//-------------------------
$i=1;
//-------------------------
$monto1 = 0;	
$comision1 = 0;	
$total1 = 0;	
//-------------------------
$monto2 = 0;	
$comision2 = 0;	
$total2 = 0;						
//-----------------
while ($registro = mysql_fetch_object($tabla))
	{
	$pdf->SetFont('Times','',8);
	//----------------------
	$pdf->Cell($a,6,$i,$linea_fija,0,'C');
	$pdf->Cell($b,6,$registro->fecha1,$linea_fija,0,'C');
	//----------------------------
	$pdf->SetFont('Times','',7);
	$pdf->Cell($c,6,$registro->contribuyente,$linea_fija,0,'L');
	//----------------------------
	$pdf->SetFont('Times','',8);
	$pdf->Cell($d,6,$registro->licencia,$linea_fija,0,'C');
	$pdf->Cell($e,6,$registro->planilla,$linea_fija,0,'C');
	$pdf->Cell($f,6,sprintf('%06s',$registro->liquidacion),$linea_fija,0,'C');
	if ($registro->tipo=='Libre')
		{
		$pdf->Cell($g,6,number_format(doubleval($registro->monto),2,',','.'),$linea_fija,0,'R');
		$pdf->Cell($g,6,number_format(doubleval($registro->comision),2,',','.'),$linea_fija,0,'R');
		$pdf->Cell($g,6,number_format(doubleval($registro->total),2,',','.'),$linea_fija,0,'R');
		$pdf->Cell($g,6,'0,00',$linea_fija,0,'R');
		$pdf->Cell($g,6,'0,00',$linea_fija,0,'R');
		$pdf->Cell($g,6,'0,00',$linea_fija,0,'R');	
		//-------------------------
		$monto1 = $monto1 + $registro->monto;	
		$comision1 = $comision1 + $registro->comision;	
		$total1 = $total1 + $registro->total;					
		}
	else
		{
		$pdf->Cell($g,6,'0,00',$linea_fija,0,'R');
		$pdf->Cell($g,6,'0,00',$linea_fija,0,'R');
		$pdf->Cell($g,6,'0,00',$linea_fija,0,'R');
		$pdf->Cell($g,6,number_format(doubleval($registro->monto),2,',','.'),$linea_fija,0,'R');
		$pdf->Cell($g,6,number_format(doubleval($registro->comision),2,',','.'),$linea_fija,0,'R');
		$pdf->Cell($g,6,number_format(doubleval($registro->total),2,',','.'),$linea_fija,0,'R');	
		//-------------------------
		$monto2 = $monto2 + $registro->monto;	
		$comision2 = $comision2 + $registro->comision;	
		$total2 = $total2 + $registro->total;						
		}
	
	//----------------------
	$i++;
	$pdf->ln(6);
	//----------------------
	}
//---------------
$pdf->SetFont('Times','B',9);
$pdf->Cell(141,6,'',$linea_fija,0,'C',1);
$pdf->Cell($g,6,number_format(doubleval($monto1),2,',','.'),$linea_fija,0,'R',1);
$pdf->Cell($g,6,number_format(doubleval($comision1),2,',','.'),$linea_fija,0,'R',1);
$pdf->Cell($g,6,number_format(doubleval($total1),2,',','.'),$linea_fija,0,'R',1);
$pdf->Cell($g,6,number_format(doubleval($monto2),2,',','.'),$linea_fija,0,'R',1);
$pdf->Cell($g,6,number_format(doubleval($comision2),2,',','.'),$linea_fija,0,'R',1);
$pdf->Cell($g,6,number_format(doubleval($total2),2,',','.'),$linea_fija,0,'R',1);
$pdf->ln(6);
// ----------------

$pdf->Output();

?>