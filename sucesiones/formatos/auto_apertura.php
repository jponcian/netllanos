<?php
session_start();
if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
 
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');

class PDF extends FPDF
	{
	function Header()
		{		
		$this->Image('../../imagenes/logo.jpeg',20,20,65);
		}
		
	function Footer()
		{    
		//Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',8);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,0,sistema(),0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
//$pdf->AliasNbPages();
$pdf->SetMargins(22,25,22);
$pdf->SetDisplayMode($zoom=='real');
//$pdf->SetAutoPageBreak(1, $margin=20);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFillColor(192,192,192);
$tamaño=0;

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT * FROM vista_re_exp_sucesiones WHERE anno=0".$_SESSION['ANNO_PRO']." AND numero=0".$_SESSION['NUM_PRO']." AND sector =0".$_SESSION['SEDE'].";"; 
//echo $consulta_datos;
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------

// ---------------------
$pdf->SetFont('Times','B',17-$tamaño);
$pdf->Ln(40);
$pdf->Cell(0,6,'AUTO',0,0,'C'); 
$pdf->Ln(6);
$pdf->Cell(0,6,'(APERTURA)',0,0,'C'); 
$pdf->Ln(15);

$pdf->SetFont('Times','B',13-$tamaño);
$pdf->MultiCell(0,9,'     De conformidad con lo establecido en el Art. 31 de la Ley y Procedimientos Administrativos se apertura el Expediente identificado con el Nº '.$registro_datos->numero.', correspondiente a la División de Recaudación (Área de Sucesiones), perteneciente al Causante: '.$registro_datos->contribuyente.', Nº de RIF '.formato_rif($registro_datos->rif).'.'); 
$pdf->Ln(9);

$pdf->Cell(0,5,'     '.$registro_datos->nombre.', '.dia($registro_datos->fecha_registro).' de '.$_SESSION['meses_anno'][mes($registro_datos->fecha_registro)+0].' del Año '.anno($registro_datos->fecha_registro)); 
$pdf->Ln(35);

$pdf->SetFont('Times','B',12-$tamaño);

list ($funcionario, $rol, $origen, $rol2, $origen2, $rol3, $origen3, $cargo1, $cargo2) = funcion_funcionario(0+$registro_datos->funcionario);
$funcionario=$cargo1.' '.$cargo2;

list ($coordinador, $rol, $origen, $rol2, $origen2, $rol3, $origen3, $cargo1, $cargo2) = funcion_funcionario(0+$registro_datos->coordinador);
$coordinador=$cargo1.' '.$cargo2;

$pdf->Cell(95,5,'FUNCIONARIO'); 
$pdf->Cell(0,5,'COORDINADOR'); 
$pdf->Ln(8);
$pdf->SetFont('Times','',10-$tamaño);
$pdf->Cell(95,5,'Firma:                        __________________________');
$pdf->Cell(0,5,'Firma:                        __________________________');
$pdf->Ln(7);
$pdf->Cell(95,5,'Nombre y Apellido:  '.palabras($registro_datos->nombrefuncionario));
$pdf->Cell(0,5,'Nombre y Apellido:  '.palabras($registro_datos->nombrecoordinador));
$pdf->Ln(7);
$pdf->Cell(95,5,'C.I.:                           '.formato_cedula($registro_datos->funcionario));
$pdf->Cell(0,5,'C.I.:                           '.formato_cedula($registro_datos->coordinador));
$pdf->Ln(7);
$pdf->Cell(95,5,'Cargo:                       '.palabras($funcionario));
$pdf->Cell(0,5,'Cargo:                       '.palabras($coordinador));


//----------------------
$pdf->Output();
?>