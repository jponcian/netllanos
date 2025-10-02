<?php

////////// REGION DE EMISION
$consulta_x = "SELECT * FROM z_region;";
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
// ---------------------

//---------------------------------
$jefe = $registro_x->gerente;
$cedula = "C.I. N° V-" .$registro_x->ci_gerente;
$cargo = $registro_x->cargo;
$providencia = $registro_x->providencia;
$fecha_prov = $registro_x->fecha_prov;
$gaceta = $registro_x->gaceta;
$fecha_gac = $registro_x->fecha_gaceta;
$region=$registro_x->nombre;
//---------------------------------

//---------------------------------
$pdf->Ln(8);
$pdf->SetFont('Times','B',$tamañoletra);
$pdf->Cell(0,5,mayuscula($jefe),0,0,'C'); 
$pdf->Ln(5);
$pdf->SetFont('Times','',9);
$pdf->Cell(0,5,trim($cargo),0,0,'C'); 
$pdf->Ln(5);
$pdf->SetFont('Times','',8);
$pdf->MultiCell(0,4,$providencia,0,'C');
//$pdf->MultiCell(0,4,'de fecha '.$fecha_prov,0,'C');
if ($_SESSION['SEDE']==1)
	{
	$pdf->MultiCell(0,4,$gaceta,0,'C');
	}
// FIN
//----------------
?>