<?php
// BUSQUEDA DEL JEFE
switch ($opcion) {
	case "division":
		$consulta_x = "SELECT * FROM vista_jefes WHERE division=" . $division . ";";
		break;
	default:
		$consulta_x = "SELECT * FROM vista_jefes WHERE id_sector=" . $sector . ";";
}

$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
echo " opcion => " . $opcion . " consulta => " . $consulta_x;
//---------------------------------
$jefe = $registro_x->jefe;
$cedula = "C.I. N° V-" . $registro_x->cedula;
$cargo = $registro_x->cargo;
$providencia = $registro_x->providencia;
$fecha_prov = $registro_x->fecha_prov;
$gaceta = $registro_x->gaceta;
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = $registro_x->descripcion;

//---------------------------------
$pdf->Ln(4);
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 5, $jefe, 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 8);
$pdf->Ln(10);

// FIN
//----------------
?>