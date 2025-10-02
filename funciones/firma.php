<?php
// BUSQUEDA DEL JEFE
switch ($opcion) 
	{
	case "division":
       $consulta_x = "SELECT * FROM vista_jefes WHERE division=".$division.";";		
		break;	
	default:
       $consulta_x = "SELECT * FROM vista_jefes WHERE id_sector=".$sector.";";		
	}

$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$jefe = $registro_x->jefe;
$cedula = "C.I. N° V-" .$registro_x->cedula;
$cargo = $registro_x->cargo;
$providencia = $registro_x->providencia;
$fecha_prov = $registro_x->fecha_prov;
$gaceta = $registro_x->gaceta;
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = $registro_x->descripcion;

//---------------------------------
$pdf->Ln(4);
$pdf->SetFont('Times','B',12);
$pdf->Cell(0,5,$jefe,0,0,'C'); $pdf->Ln(5);
$pdf->SetFont('Times','B',8);
$pdf->Cell(0,5,$cargo,0,0,'C'); $pdf->Ln(5);
$pdf->Cell(0,5,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS '.strtoupper(buscar_region()),0,0,'C'); $pdf->Ln(5);
$pdf->SetRightMargin(70);
$pdf->SetLeftMargin(70);
$pdf->SetFont('Times','',8);
$pdf->MultiCell(0,4,$providencia,0,'C');
$pdf->Cell(0,5,'de fecha '.voltea_fecha($fecha_prov),0,0,'C'); $pdf->Ln(5);
// FIN
//----------------
?>