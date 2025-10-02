<?php
ob_end_clean();
session_start();

include('../../conexion.php');
include "../../funciones/auxiliar_php.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: ../index.php?errorusuario=val");
	exit();
}

require('../../funciones/fpdf.php');

class PDF extends FPDF
{
	function Header()
	{
		// Select Arial bold 15
		$this->SetFont('Arial', 'B', 15);
		// Move to the right
		$this->Cell(80);
		// Framed title
		$this->Cell(30, 10, '', 0, 0, 'C');
		// Line break
		$this->Ln(10);
	}
}

// ENCABEZADO
$pdf = new PDF('P', 'mm', 'LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17, 10, 17);
$pdf->SetAutoPageBreak(1, 10);

// CAPTURA DE VALORES
$NUMERO = $_GET['num'];
$ANNO = $_GET['anno'];
$SEDE = $_GET['sede'];

////////// DATOS DE LA PROVIDENCIA
$consulta = "SELECT expedientes_especiales_siscontri.Numero, expedientes_especiales_siscontri.Anno, expedientes_especiales_siscontri.Rif, expedientes_especiales_siscontri.FechaRegistro, expedientes_especiales_siscontri.Coordinador, expedientes_especiales_siscontri.Funcionario, expedientes_especiales_siscontri.Fecha_proceso, expedientes_especiales_siscontri.Usuario, expedientes_especiales_siscontri.Sector, expedientes_especiales_siscontri.Status, expedientes_especiales_siscontri.FechaAnulacion, expedientes_especiales_siscontri.MotivoAnulacion, concat(z_empleados.Nombres,' ',z_empleados.Apellidos) AS Nombre_Coordinador, concat(Empleados.Nombres,' ',Empleados.Apellidos) AS Nombre_Funcionario, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.direccion, contribuyentes.Telefonos, contribuyentes.fechaespecial FROM expedientes_especiales_siscontri INNER JOIN z_empleados ON z_empleados.cedula = expedientes_especiales_siscontri.Coordinador INNER JOIN z_empleados AS Empleados ON Empleados.cedula = expedientes_especiales_siscontri.Funcionario INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_especiales_siscontri.Rif INNER JOIN contribuyentes ON contribuyentes.rif = vista_contribuyentes_direccion.rif WHERE expedientes_especiales_siscontri.Sector=" . $SEDE . " and expedientes_especiales_siscontri.Anno = " . $ANNO . " AND expedientes_especiales_siscontri.Numero = " . $NUMERO . "";
$tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);

if ($registro = mysqli_fetch_object($tabla)) {

	$pdf->AddPage();
	$pdf->SetFont('Times', '', 12);
	setlocale(LC_ALL, 'sp_ES', 'sp', 'es');
	$pdf->SetFillColor(190);

	//DETERMINAMOS LA DEPENDENCIA
	list($estado, $sede, $conector1, $conector2, $adscripcion, $tipo_division, $sector, $titulo) = buscar_sector($registro->Sector);

	$ciudad = $sector . ", ";
	$dependencia = $titulo . ' ' . strtoupper($sector);

	$fecha = dia($registro->FechaRegistro) . " de " . ($_SESSION['meses_anno'][abs(mes($registro->FechaRegistro))]) . " de " . anno($registro->FechaRegistro);
	;
	// IMAGEN
	$pdf->Image('../../imagenes/logo.jpeg', 18, 21, 60);
	$pdf->SetFont('Times', 'B', 10);
	$pdf->Cell(60, 22, '', 1, 0, 'C');
	// Título
	$pdf->Cell(120, 10, $ciudad . $fecha, 0, 0, 'R');
	$pdf->Ln(12);
	// Salto de línea
	$pdf->Cell(60);
	$pdf->Cell(55, 10, 'EXPEDIENTE SISCONTRI', 1, 0, 'C', 1);
	$pdf->Cell(20, 10, $registro->Numero, 1, 0, 'C');
	$pdf->Cell(25, 10, utf8_decode('AÑO'), 1, 0, 'C', 1);
	$pdf->Cell(20, 10, $registro->Anno, 1, 0, 'C');

	$pdf->SetFont('Times', 'B', 9);

	// TITULO
	/*
$pdf->Cell(0,0,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS',0,0,'C',0);
$pdf->Ln(4);
$pdf->Cell(0,0,'REGION LOS LLANOS',0,0,'C',0);
$pdf->Ln(4);
$pdf->Cell(0,0,'EXPEDIENTE SUJETO PASIVO ESPECIAL',0,0,'C',0);
$pdf->Ln(10);

$pdf->SetFont('Times','',7.5);

// CONTENIDO
$pdf->Cell(40,5,'NOMBRE DEL PROGRAMA:   '.$registro->TipoAutorizacion.'    '.$registro->Descripcion,0,0,'L',0);
$pdf->Cell(80,5,'',0,0,'C',0);
$pdf->Cell(30,5,'NUMERO DE RIF:  '.$registro->Rif,0,0,'C',0);
$pdf->Cell(30,5,'',0,0,'C',0);
$pdf->Ln(7);
*/



	// CUADRO VDF
	$pdf->Ln(10);

	$pdf->SetFont('Times', 'B', 7);
	$pdf->Cell(35, 9, 'PROGRAMA:', 1, 0, 'C', 1);
	$pdf->Cell(145, 9, '', 1, 0, 'C', 0);
	$pdf->Ln(9);
	//--
	$lineas = 9;
	$pdf->Cell(75, $lineas, 'GESTION DE COBRANZA:', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Cell(65, $lineas, 'AGENTE DE RETENCION', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	//--
	$pdf->Cell(75, $lineas, 'CASINOS, SALAS DE BINGO Y MAQUINAS TRAGANIQUELES:', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Cell(65, $lineas, 'BENEFICIOS FISCALES', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	//--
	$pdf->Cell(75, $lineas, 'CONTRIBUYENTES ESPECIALES:', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, 'X', 1, 0, 'C', 0);
	$pdf->Cell(65, $lineas, 'CONTROL DE INGRESOS', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	//--
	$pdf->Cell(75, $lineas, 'CONTRIBUYENTES ORDINARIOS:', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Cell(65, $lineas, 'DEBERES FORMALES', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	//--
	$pdf->Cell(75, $lineas, 'PERSONA NATURALES:', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Cell(65, $lineas, 'REGISTRO DE CONTRIB EXPORTADORES', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	//--
	$pdf->Cell(75, $lineas, 'AVALUOS Y SUCESIONES:', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Cell(65, $lineas, 'RECURSOS JERARQUICOS', 1, 0, 'C', 1);
	$pdf->Cell(20, $lineas, '', 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	//--

	// DATOS DEL SUJETO PASIVO
	$pdf->Cell(180, $lineas, 'DATOS DEL SUJETO PASIVO', 1, 0, 'C', 1);
	$pdf->Ln($lineas);
	$pdf->Cell(50, $lineas + 1, 'CONTRIBUYENTE O RESPONSABLE:', 1, 0, 'C', 1);
	$pdf->MultiCell(130, 10, $registro->contribuyente, 1, 'C');
	//$pdf->Ln($lineas);
	$pdf->Cell(50, $lineas, 'R.I.F. Nro:', 1, 0, 'C', 1);
	$pdf->Cell(130, $lineas, substr($registro->Rif, 0, 1) . '-' . substr($registro->Rif, 1, 8) . '-' . substr($registro->Rif, -1), 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	// --- Bloque para Domicilio Fiscal con altura dinámica ---
	// Guardar posición inicial
	$y_start = $pdf->GetY();
	$x_start = $pdf->GetX();

	// Dibujar el MultiCell del domicilio sin borde para calcular la altura
	$pdf->SetXY($x_start + 50, $y_start);
	$pdf->MultiCell(130, 5, $registro->direccion, 0, 'C');

	// Calcular la altura real de la fila
	$row_height = $pdf->GetY() - $y_start;

	// Volver a la posición inicial para dibujar la fila completa con bordes
	$pdf->SetXY($x_start, $y_start);

	// Dibujar la celda izquierda ('DOMICILIO FISCAL:') con la altura calculada
	$pdf->Cell(50, $row_height, 'DOMICILIO FISCAL:', 1, 0, 'C', 1);

	// Mover cursor para el MultiCell
	// $pdf->SetXY($x_start + 50, $y_start);

	$pdf->Ln($row_height);

	// Dibujar el MultiCell del domicilio otra vez, ahora con borde
	// $pdf->MultiCell(130, 5, $registro->direccion, 1, 'C');
	// --- Fin del bloque ---
	$pdf->Cell(50, $lineas, 'NRO. DE TELEFONO:', 1, 0, 'C', 1);
	$pdf->Cell(130, $lineas, $registro->Telefonos, 1, 0, 'C', 0);
	$pdf->Ln($lineas);
	$pdf->Cell(50, $lineas, 'CORREO ELECTRONICO:', 1, 0, 'C', 1);
	$pdf->Cell(130, $lineas, '', 1, 0, 'C', 0);
	$pdf->Ln($lineas);

	// DATOS DIVISION DE SUJETOS PASIVOS ESPECIALES
	$pdf->Cell(180, $lineas, 'DATOS DIVISION DE SUJETOS PASIVOS ESPECIALES', 1, 0, 'C', 1);
	$pdf->Ln($lineas);
	$pdf->Cell(70, $lineas, 'FECHA DE INICIO CONTRIBUYENTE ESPECIAL:', 1, 0, 'C', 1);
	if ($registro->fechaespecial <> NULL) {
		$fechainicio = date("d-m-Y", strtotime($registro->fechaespecial));
	} else {
		$fechainicio = "";
	}
	$pdf->Cell(110, $lineas, $fechainicio, 1, 0, 'C', 0);
	$pdf->Ln($lineas);

	$pdf->Cell(70, $lineas, 'DEPENDENCIA A LA CUAL ESTA ADSCRITO:', 1, 0, 'C', 1);
	$pdf->Cell(110, $lineas, $dependencia, 1, 0, 'C', 0);
	$pdf->Ln($lineas);

	$pdf->Cell(70, $lineas, 'FISCAL:', 1, 0, 'C', 1);
	$pdf->Cell(110, $lineas, formato_cedula($registro->Funcionario) . ' - ' . $registro->Nombre_Funcionario, 1, 0, 'C', 0);
	$pdf->Ln($lineas);

	$pdf->Cell(70, $lineas, 'SUPERVISOR:', 1, 0, 'C', 1);
	$pdf->Cell(110, $lineas, formato_cedula($registro->Coordinador) . ' - ' . $registro->Nombre_Coordinador, 1, 0, 'C', 0);
	$pdf->Ln($lineas);

	// OBSERVACIONES
	$pdf->Cell(180, $lineas, 'OBSERVACIONES', 1, 0, 'C', 1);
	$pdf->Ln($lineas);

	$pdf->Ln(5);

	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Cell(160, $lineas, '', 1, 0, 'L', 0);
	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Ln($lineas);
	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Cell(160, $lineas, '', 1, 0, 'L', 0);
	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Ln($lineas);
	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Cell(160, $lineas, '', 1, 0, 'L', 0);
	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Ln($lineas);
	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Cell(160, $lineas, '', 1, 0, 'L', 0);
	$pdf->Cell(10, $lineas, '', 0, 0, 'L', 0);
	$pdf->Ln($lineas);

	// CUADRO 
	$pdf->SetY(20);
	$pdf->SetX(17);
	$pdf->SetFont('Times', '', 7);
	$pdf->Cell(180, 240, '', 1, 0, 'C', 0);
	// ------	

}
// FIN DE LA VALIDACION DE LA CONSULTA

$pdf->Output();
