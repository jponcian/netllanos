<?php
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');
include('../../conexion.php');
//mysql_query("SET NAMES 'utf8'");
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";

class PDF extends FPDF
{
	function Footer()
	{    //Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
	}
	function Header()
	{
		//--- CABEZERA DEL REPORTE
		$this->SetFont('Arial','B',15);
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		$this->SetFont('Times','B',11); $this->Ln(8);

		////////// REGION DE EMISION
		$consulta_x = "SELECT nombre FROM z_region;";
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		$Region=$registro_x->nombre;
		// ---------------------
	
		////////// BUSCAMOS LA DIVISION O AREA Y DEPENDENCIA
		$consulta_x = "SELECT nombre, tipo_division FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
		$tabla_x = mysql_query($consulta_x);
		$regstro_x = mysql_fetch_object($tabla_x);
		$area = $regstro_x->tipo_division;
		$dependencia = $regstro_x->nombre;

		$this->SetXY(90,11);
		$this->Cell(0,5,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.mb_convert_case($Region, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C','');
		$this->SetXY(90,16);
		$this->Cell(0,5,'DEPENDENCIA: ' . mb_convert_case($dependencia, MB_CASE_UPPER, "ISO-8859-1"),0,1,'C','');
		$this->SetXY(90,23);
		$this->Cell(0,5,'DIVISIÓN DE SUJETOS PASIVOS ESPECIALES',0,1,'C','');
		$this->SetXY(90,28);	
		list($anno1,$mes1,$dia1)=explode('/',$_SESSION['INICIO']);
		list($anno2,$mes2,$dia2)=explode('/',$_SESSION['FIN']);
		$this->Cell(0,5,'FECHA DESDE: '.$dia1.'/'.$mes1.'/'.$anno1.' HASTA: '.$dia2.'/'.$mes2.'/'.$anno2,0,1,'C','');

		$this->SetFont('Times','B',12);
		$this->SetFillColor(192,192,192);
		
		$this->Ln(8);
		
		$txt='N°';
		$this->Cell(10,7,$txt,1,0,'C','true');
	
		$txt='Rif';
		$this->Cell(20,7,$txt,1,0,'C','true');
	
		$txt='Contribuyente';
		$this->Cell(130,7,$txt,1,0,'C','true');
	
		$txt='Impuesto';
		$this->Cell(70,7,$txt,1,0,'C','true');
	
		$txt='Fecha Pres.';
		$this->Cell(30,7,$txt,1,0,'C','true');
	
		$txt='Fecha Pago';
		$this->Cell(30,7,$txt,1,0,'C','true');
	
		$txt='Monto';
		$this->Cell(30,7,$txt,1,0,'C','true');
		$this->Ln(7);
	}		
}

// ENCABEZADO
$pdf=new PDF('L','mm','LEGAL');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
//$pdf->SetAutoPageBreak(1,20);

$pdf->AddPage();

$pdf->SetFont('Times','B',14);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

	// CONDICIONES
	if ($_SESSION['OSEDE']==0) {$Sede = '';}
	else {$Sede = " AND Sector=".$_SESSION['OSEDE']."";}
	if ($_SESSION['RIF']==100) {$Contribuyente = '';}
		else {$Contribuyente = " AND ce_pagos.Rif='".$_SESSION['RIF']."'";}
	if ($_SESSION['TERMINAL']==100) {$terminal = '';}
		else {$terminal = " AND RIGHT(ce_pagos.Rif,1)='".$_SESSION[TERMINAL]."'";}
	if ($_SESSION['IMPUESTO']==0) {$Impuesto = '';}
		else {$Impuesto = " AND ce_pagos.Tipo_Impuesto=".$_SESSION['IMPUESTO'];}
	// FIN DE LAS CONDICIONES
	$consulta_x = "SELECT ce_pagos.Rif, vista_contribuyentes_direccion.contribuyente AS NombreRazon, ce_cal_tip_obligaciones.Tipo, date_format(ce_pagos.Fecha_Presentacion,'%d/%m/%Y') AS presentacion, date_format(ce_pagos.Fecha_Pago,'%d/%m/%Y') AS pago, ce_pagos.Monto FROM ce_pagos INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = ce_pagos.Rif INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto WHERE ce_pagos.Fecha_Pago >= '".$_SESSION['INICIO']."' AND ce_pagos.Fecha_Pago <= '".$_SESSION['FIN']."'".$Contribuyente.$Impuesto.$Sede.$terminal." ORDER BY ce_pagos.Fecha_Presentacion ASC, ce_pagos.Fecha_Pago ASC, ce_pagos.Rif ASC";
$tabla_x = mysql_query($consulta_x);

$i=1;
$Total =0;
$pdf->SetFont('Times','',10);
		
	while ($registro_x = mysql_fetch_object($tabla_x))
		{
	
		$txt = $i;
		$pdf->Cell(10,7,$txt,1,0,'C','');
	
		$txt = $registro_x->Rif;
		$pdf->Cell(20,7,$txt,1,0,'C','');
	
		$txt = $registro_x->NombreRazon;
		$pdf->Cell(130,7,$txt,1,0,'C','');
	
		$txt = $registro_x->Tipo;
		$pdf->Cell(70,7,$txt,1,0,'C','');
	
		$txt = $registro_x->presentacion;
		$pdf->Cell(30,7,$txt,1,0,'C','');
	
		$txt = $registro_x->pago;
		$pdf->Cell(30,7,$txt,1,0,'C','');
	
		$txt = number_format(doubleval($registro_x->Monto),2,',','.');
		$pdf->Cell(30,7,$txt,1,0,'C','');
		
		$Total = $Total + $registro_x->Monto ;
		$pdf->Ln(7);
		$i++;
		}

$pdf->Ln(7);

$pdf->SetFont('Times','B',13);
$pdf->SetFillColor(192,192,192);
	
$txt = '===== TOTAL =====>      ';
$pdf->Cell(260,7,$txt,1,0,'R','true');

$txt = number_format(doubleval($Total),2,',','.');
$pdf->Cell(60,7,$txt,1,0,'C','true');
		
// FIN

$pdf->Output();
//----------
include "../../desconexion.php";
//----------

?>