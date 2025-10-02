<?php

session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}

class PDF extends FPDF
{
function Header()
	{
	$this->Image('../../imagenes/logo.jpeg',20,8,65);	
	
	//------ ORIGEN DEL FUNCIONARIO
	include "../../funciones/origen_funcionario.php";
	//--------------------
	
	$fecha = date('Y/m/d');	
	list($anno,$mes,$dia)=explode('/',$fecha);
	$fecha = mktime(0,0,0,$mes,$dia,$anno);

	////////// CIUDAD DE EMISION
	$consulta_x = "SELECT nombre FROM z_sectores WHERE id_sector=".$_SESSION['SEDE'];
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$ciudad=$registro_x->nombre;
	// ---------------------
	
	//$t=(190-(strlen($ciudad)));
	
	//$mes=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	
	//$this->Text($t,34,$ciudad.',  '.strftime('%d', strtotime(date('m/d/Y',$fecha))).' de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$fecha)))-1)].' del '.strftime('%Y', strtotime(date('m/d/Y',$fecha))));
	$this->Ln(10);
	
	$this->SetFont('Times','B',13);
	$this->Cell(0,5,'Fraccionamientos Transferidos a Liquidacion Periodo: '.date('d/m/Y',strtotime($_SESSION['INICIO'])).' - '.date('d/m/Y',strtotime($_SESSION['FIN'])),0,0,'C');
	$this->Ln(10);

	// FIN
	}	
	
function Footer()
	{    //Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(460,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'C');
	}		
}

//------ ORIGEN DEL FUNCIONARIO
include "../../funciones/origen_funcionario.php";
//--------------------

// ENCABEZADO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,20,17);
$pdf->SetAutoPageBreak(1,25);

$pdf->AddPage();

$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

$pdf->SetFont('Times','',11);

/////// TITULOS DEL CUADRO CON LAS LIQUIDACIONES

$pdf->SetFont('Times','B',10);
$pdf->SetFillColor(202,201,201);

$txt='Año';
$pdf->Cell(12,7,$txt,1,0,'C',1);

$txt='Numero';
$pdf->Cell(15,7,$txt,1,0,'C',1);

$txt='Rif';
$pdf->Cell(35,7,$txt,1,0,'C',1);

$txt='Contribuyente';
$pdf->Cell(100,7,$txt,1,0,'C',1);

$txt='Motivo';
$pdf->Cell(30,7,$txt,1,0,'C',1);


$txt='Sancion';
$pdf->Cell(25,7,$txt,1,0,'C',1);

$txt='Monto BsS.';
$pdf->Cell(30,7,$txt,1,1,'C',1);

//////// ---- DETALLE DE LAS PLANILLAS
$monto = 0;
$pdf->SetFont('Times','',9);

$consulta_x = "SELECT * FROM liquidacion, vista_contribuyentes_direccion WHERE liquidacion.rif=vista_contribuyentes_direccion.rif AND status=10 AND fecha_transferencia_a_liq BETWEEN '".$_SESSION['INICIO']."' AND '".$_SESSION['FIN']."' AND origen_liquidacion=".$origenF." ORDER BY anno_expediente DESC, num_expediente DESC, id_sancion DESC;";
$tabla_x = mysql_query($consulta_x);

while ($registro_x = mysql_fetch_object($tabla_x))
{
//
$txt=$registro_x->anno_expediente;
$pdf->Cell(12,6,$txt,1,0,'C');
//
$txt=$registro_x->num_expediente;
$pdf->Cell(15,6,$txt,1,0,'C');
//
$txt=formato_rif($registro_x->rif);
$pdf->Cell(35,6,$txt,1,0,'C');
//
$txt=$registro_x->contribuyente;
$pdf->Cell(100,6,$txt,1,0,'C');
//
$txt='Fraccionamiento'; 
$pdf->Cell(30,6,$txt,1,0,'C');
//
$txt=$registro_x->id_sancion; 
$pdf->Cell(25,6,$txt,1,0,'C');
//
$txt=number_format(doubleval($registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial),2,',','.'); 
$pdf->Cell(30,6,$txt,1,1,'C');
//
$monto += ($registro_x->monto_bs / $registro_x->concurrencia * $registro_x->especial);
//
}

//////// ---- TOTAL DE LAS PLANILLAS
$pdf->SetFillColor(181,175,175);
$pdf->SetFont('Times','B',10);
$txt='==> Monto Total BsS. ==>  ';
$pdf->Cell(217,6,$txt,1,0,'R');
$txt=$monto;
$pdf->Cell(30,6,number_format(doubleval($txt),2,',','.'),1,0,'C',1);
$pdf->Ln(10);

//////// ---------------------------

$pdf->Output();

?>