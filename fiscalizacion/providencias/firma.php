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
$id_sector = $registro_x->id_sector;
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
$pdf->SetFont('Times','B',$tamañoletra);
$pdf->Cell(0,5,$jefe,0,0,'C'); $pdf->Ln(5);
$pdf->SetFont('Times','',8);
$pdf->Cell(0,5,utf8_decode($cargo),0,0,'C'); $pdf->Ln(4);

if ($id_sector>=1){
$pdf->Cell(0,5,utf8_decode('Región Los Llanos'),0,0,'C'); $pdf->Ln(4);
}
	else
	{
	}
$pdf->SetRightMargin(50);
$pdf->SetLeftMargin(50);
$pdf->SetFont('Times','',8);
//$pdf->MultiCell(0,4,utf8_decode($providencia),0,'C');
	$pdf->multiCell(0,4,utf8_decode($providencia).', de fecha '.date("d/m/Y", strtotime($fecha_prov)),0,'C');

	//if ($id_sector==1){
	//$pdf->MultiCell(0,4,utf8_decode($gaceta).', de fecha '.(voltea_fecha($fecha_gac)),0,'C');
	//}
	//else
	//{
//}
//$pdf->Cell(0,5,'de fecha '.voltea_fecha($fecha_prov),0,0,'C'); $pdf->Ln(5);
// FIN
//----------------
?>