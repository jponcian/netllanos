<?php
// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR
if ($_SESSION['DIVISION_USUARIO']==7)
	{
	$consulta_x = "SELECT * FROM vista_jefe_esp WHERE id_sector=".$_SESSION['SEDE'].";";
	}
else
	{
	$consulta_x = "SELECT * FROM vista_jefe_rec WHERE id_sector=".$_SESSION['SEDE'].";";
	}
//echo $consulta_x;
$tabla_x = mysql_query ($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$jefe = utf8_decode($registro_x->jefe);
$cedula = "C.I. N° V-" .$registro_x->cedula;
$cargo = $registro_x->cargo;
$providencia = utf8_decode($registro_x->providencia);
$fecha_prov = $registro_x->fecha_prov;
$gaceta = utf8_decode($registro_x->gaceta);
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = $registro_x->descripcion;

//---------------------------------
$pdf->Ln(8);
$pdf->SetFont('Times','B',$tamañoletra);
$pdf->Cell(0,5,$jefe,0,0,'C'); 
$pdf->Ln(5);
$pdf->SetFont('Times','',9);
$pdf->Cell(0,5,trim($cargo),0,0,'C'); 
$pdf->Ln(5);
$pdf->SetFont('Times','',8);
$pdf->MultiCell(0,4,$providencia,0,'C');
$pdf->MultiCell(0,4,'de fecha '.voltea_fecha($fecha_prov),0,'C');
// FIN
//----------------
?>