<?php

include "../auxiliar.php";
// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR
$consulta_x = "SELECT cedula,nombre,cargo,region,providencia,fecha_prov,fecha_gaceta,gaceta,sector 
from (
SELECT
z_region.ci_gerente as cedula,
z_region.gerente as nombre,
z_region.cargo as cargo,
z_region.nombre as region,
z_region.providencia as providencia,
z_region.fecha_prov as fecha_prov,
z_region.gaceta as gaceta,
z_region.fecha_gaceta as fecha_gaceta,
1 as sector
FROM
z_region ".
 // UNION ALL 
//SELECT 
//z_jefes_detalle.cedula AS cedula,
//z_jefes_detalle.jefe as nombre,
//z_jefes_detalle.cargo as cargo,
//z_jefes_detalle.descripcion AS region,
//z_jefes_detalle.providencia as providencia,
//z_jefes_detalle.fecha_prov as fecha_prov,
//z_jefes_detalle.gaceta as gaceta,
//z_jefes_detalle.id_sector
//FROM
//z_jefes_detalle WHERE id_division=0
") as t WHERE sector=1;";

$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$jefe = "Jose de Jesus Urbaneja";
$cedula = "C.I. N° V-" .$registro_x->cedula;
$cargo = $registro_x->cargo;
$providencia = $registro_x->providencia;
$fecha_prov = $registro_x->fecha_prov;
$gaceta = $registro_x->gaceta;
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = $registro_x->region;

//---------------------------------
$pdf->Ln(29);

$pdf->SetFont('Times','B',$tamañoletra);

//if ($sector==2)
//{
//$pdf->Image('firma.jpg',64,$pdf->GetY()-9,90);
//$pdf->Ln(22);
//}
//else
//{
//--------------------
	$pdf->Cell(0,5,$jefe,0,0,'C'); $pdf->Ln(5);
	if ($registro_x->sector==1)
	{
		$pdf->SetFont('Times','',9);
		$pdf->Cell(0,5,utf8_decode(trim($cargo)),0,0,'C'); $pdf->Ln(5);
		$pdf->SetFont('Times','',8);
		$pdf->MultiCell(0,4,utf8_decode($providencia).', de fecha '.date("d/m/Y", strtotime($fecha_prov)),0,'C');
		//$pdf->MultiCell(0,4,'de fecha '.date("d/m/Y", strtotime($fecha_prov)),0,'C');
		$pdf->MultiCell(0,4,utf8_decode($gaceta).', de fecha '.(voltea_fecha($fecha_gac)),0,'C');
	}
	else
	{
		$pdf->SetFont('Times','',9);
		$pdf->Cell(0,5,trim($cargo),0,0,'C'); $pdf->Ln(5);
		$pdf->SetFont('Times','',8);
		$pdf->MultiCell(0,4,$providencia,0,'C');
		$pdf->MultiCell(0,4,'de fecha '.date("d/m/Y", strtotime($fecha_prov)),0,'C');
	}
//--------------------
//}
// FIN
//----------------
?>