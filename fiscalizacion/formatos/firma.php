<?php

if ($jefe_division>0)
	{
	$consulta_x = "SELECT * FROM vista_jefe_fis WHERE cedula=".$jefe_division." AND fecha_prov<='".$fecha_jefe."' ORDER BY fecha_prov DESC;";
	}
else
	{
	$consulta_x = "SELECT * FROM vista_jefe_fis WHERE id_sector=".$_SESSION['SEDE'].";";
	}

// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$jefe = $registro_x->jefe;
$cedula = "C.I. N° V-" .$registro_x->cedula;
$cargo = utf8_decode($registro_x->cargo);
$providencia = utf8_decode($registro_x->providencia);
$fecha_prov = $registro_x->fecha_prov;
$gaceta = utf8_decode($registro_x->gaceta);
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = utf8_decode($registro_x->descripcion);

//---------------------------------
$pdf->Ln(4);
$pdf->SetFont('Times','B',$tamañoletra);
$pdf->Cell(0,5,$jefe,0,0,'C'); $pdf->Ln(5);
$pdf->SetFont('Times','',8);
$pdf->Cell(0,5,$cargo,0,0,'C'); $pdf->Ln(5);
$pdf->SetRightMargin(70);
$pdf->SetLeftMargin(70);
$pdf->SetFont('Times','',8);
$pdf->MultiCell(0,4,$providencia,0,'C');
$pdf->Cell(0,5,'de fecha '.voltea_fecha($fecha_prov),0,0,'C'); $pdf->Ln(5);
// FIN
//----------------
?>