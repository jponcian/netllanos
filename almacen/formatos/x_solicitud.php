<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

//alex
class PDF extends FPDF
{
	// Caja con sombra simple (offset) para simular profundidad
	function DrawShadowBox($x, $y, $w, $h, $offset = 1.2, $shadow = array(230, 232, 235), $bg = array(255, 255, 255), $border = array(200, 200, 200))
	{
		// Sombra
		$this->SetDrawColor($shadow[0], $shadow[1], $shadow[2]);
		$this->SetFillColor($shadow[0], $shadow[1], $shadow[2]);
		$this->Rect($x + $offset, $y + $offset, $w, $h, 'F');
		// Caja principal
		$this->SetDrawColor($border[0], $border[1], $border[2]);
		$this->SetFillColor($bg[0], $bg[1], $bg[2]);
		$this->Rect($x, $y, $w, $h, 'F');
		// Restaurar color de trazo suave
		$this->SetDrawColor($border[0], $border[1], $border[2]);
	}
	function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle = 0)
	{
		$font_angle += 90 + $txt_angle;
		$txt_angle *= M_PI / 180;
		$font_angle *= M_PI / 180;

		$txt_dx = cos($txt_angle);
		$txt_dy = sin($txt_angle);
		$font_dx = cos($font_angle);
		$font_dy = sin($font_angle);

		$s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', $txt_dx, $txt_dy, $font_dx, $font_dy, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
		if ($this->ColorFlag)
			$s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
		$this->_out($s);
	}
	//fin

	function Header()
	{
		//------ DATOS DE LA SOLICITUD
		$consulta = "SELECT * FROM vista_alm_solicitud WHERE id_solicitud=" . $_GET['solicitud'] . " ORDER BY descripcion;";
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		//-----------------------
		// Panel superior con sombra y bordes suaves (alineado al alto del encabezado)
		$this->SetLineWidth(0.2);
		$panelX = $this->lMargin;
		$panelY = $this->GetY();
		$panelW = $this->w - $this->lMargin - $this->rMargin;
		$panelH = 21; // igual al alto de las celdas del encabezado
		$shadowOffset = 0.6; // sombra sutil para evitar líneas visibles
		if (method_exists($this, 'DrawShadowBox')) {
			$this->DrawShadowBox($panelX, $panelY, $panelW, $panelH, $shadowOffset);
		}
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Image('../../imagenes/logo.jpeg', 20, 22, 50);
		//----------------------------

		$this->SetFont('Arial', 'B', 14);
		//----------------------------

		$this->SetDrawColor(200, 200, 200);
		$this->Cell(55, 21, '', 1, 'C');
		//----------------------------

		$this->SetY($y);
		$this->SetX($x + 55);
		$this->SetFillColor(245, 246, 248);
		$this->MultiCell(60, 7, 'SOLICITUD DE MATERIALES AL ALMACEN', 1, 'C', true);
		//----------------------------

		$this->SetFont('Arial', 'B', 12);
		//----------------------------
		$this->SetY($y);
		$this->SetX($x + 115);
		// Consecutivo 80% más grande (de 12pt a ~22pt) y celda más ancha para evitar cortes
		$this->SetFont('Arial', 'B', 22);
		$this->Cell(40, 21, ($registro->solicitud), 1, 0, 'C');
		// Valor de FECHA EMISIÓN
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(0, 21, voltea_fecha($registro->fecha), 1, 0, 'C');
		// Restaurar para etiquetas
		$this->SetFont('Arial', 'B', 8);
		$this->SetY($y);
		$this->SetX($x + 115);
		$this->Cell(40, 7, utf8_decode('N° CONSECUTIVO'), 0, 0, 'C');
		$this->Cell(0, 7, utf8_decode('FECHA EMISIÓN'), 0, 0, 'C');
		$this->ln(22);
		//----------------------------

		$this->SetFont('Arial', '', 8);
		$y = $this->GetY();
		//----------------------------
		if ($registro->division == 9) {
			$area = $registro->area;
		} else {
			$area = $registro->descripcion;
		}
		$this->Cell(120, 14, $area, 1, 0, 'C');
		$this->Cell(0, 14, '', 1, 0, 'C');
		//----------------------------
		$this->SetY($y);
		//----------------------------
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(120, 7, 'Unidad Solicitante:');
		$this->Cell(0, 7, 'Centro de Costo:');
		$this->ln(14);
		//----------------------------

		$this->SetFont('Arial', '', 8);
		$y = $this->GetY();
		//----------------------------
		$this->Cell(110, 14, $registro->descripcion, 1, 0, 'C');
		$this->Cell(0, 14, $registro->jefe, 1, 0, 'C');
		//----------------------------
		$this->SetY($y);
		//----------------------------
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(110, 7, utf8_decode('Entregar En:'));
		if ($registro->division >= 2 and $registro->division <= 5) {
			$this->Cell(0, 7, utf8_decode('Jefe de Sector o Unidad:'));
		} else {
			$this->Cell(0, 7, utf8_decode('Jefe de División:'));
		}

		$this->ln(14);
		//----------------------------

		$this->SetFont('Arial', 'B', 8);
		$y = $this->GetY();
		//----------------------------
		// Encabezado de tabla con fondo tenue
		$this->SetFillColor(245, 246, 248);
		$this->Cell($a = 12, 14, 'ITEM', 1, 0, 'C', true);
		$this->Cell($b = 18, 14, 'CODIGO', 1, 0, 'C', true);
		$this->Cell($c = 73, 14, 'DESCRIPCION', 1, 0, 'C', true);
		$this->Cell($d = 10, 14, 'U.M.', 1, 0, 'C', true);
		$y = $this->GetY();
		$x = $this->GetX();
		$this->MultiCell($e = 20, 7, 'CANTIDAD SOLICITADA', 1, 'C', true);
		$this->SetY($y);
		$this->SetX($x + $e);
		$this->Cell($f = 0, 7, 'PARA USO DEL ALMACEN', 1, 0, 'C', true);
		$this->ln(7);
		$this->SetX($x + $e);
		$this->Cell($g = 25, 7, 'APROBADA', 1, 0, 'C', true);
		$texto_columna = ($registro->status == 10) ? 'ENTREGADO' : 'ENTREGADO';
		$this->Cell(0, 7, $texto_columna, 1, 0, 'C', true);
		//----------------------------
		$this->ln(7);
	}

	function Footer()
	{
		//Posición a 1,5 cm del final
		$this->SetY(-14);
		//Arial itálica 8
		$this->SetFont('Times', 'I', 9);
		//Color del texto en gris
		$this->SetTextColor(120);
		// Separador superior del pie
		$this->SetDrawColor(220, 220, 220);
		$this->Line($this->lMargin, $this->h - 16, $this->w - $this->rMargin, $this->h - 16);
		// Firma de agua al pie: "impreso por: <usuario> <NetlosLlanos>" (gris claro, centrado)
		$userName = isset($_SESSION['NOM_USUARIO']) ? $_SESSION['NOM_USUARIO'] : '';
		$userName = trim($userName);
		if ($userName !== '') {
			$userName = ucwords(strtolower($userName));
		}
		$printDateTime = date('d/m/Y h:i A');
		$wmText = '<NetlosLlanos 3.0> · impreso por: ' . $userName . ' · ' . $printDateTime;
		$this->SetFont('Arial', 'I', 10);
		$this->SetTextColor(180);
		$wmW = $this->GetStringWidth($wmText);
		$wmX = ($this->w - $wmW) / 2;
		$wmY = $this->h - 6; // cercano al borde inferior
		$this->Text($wmX, $wmY, utf8_decode($wmText));
		// Restaurar color para número de página
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(460, 10, utf8_decode(sistema() . ' ' . $this->PageNo() . ' de {nb}'), 0, 0, 'C');
	}
}

// INICIO
$pdf = new PDF('P', 'mm', 'LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17, 20, 17);
$pdf->AddPage();
//----------------------------

$i = 0;
$pdf->SetFont('Arial', '', 8);
//----------------------------
$consultax = "SELECT * FROM vista_alm_detalle_solicitud WHERE id_solicitud=" . $_GET['solicitud'] . " ORDER BY descripcion;";
$tablax = mysql_query($consultax);
while ($registrox = mysql_fetch_object($tablax)) {
	$i++;
	$alto = 5.5;
	$estatus = $registrox->status;
	//----------------------------
	// Zebra striping y bordes suaves
	if ($i % 2 == 1) {
		$pdf->SetFillColor(252, 252, 252);
	} else {
		$pdf->SetFillColor(255, 255, 255);
	}
	$pdf->SetDrawColor(200, 200, 200);
	$pdf->Cell($a = 12, $alto, $i, 1, 0, 'C', true);
	$pdf->Cell($b = 18, $alto, $registrox->codigo, 1, 0, 'C', true);
	$pdf->Cell($c = 73, $alto, oracion($registrox->descripcion), 1, 0, 'L', true);
	$pdf->Cell($d = 10, $alto, $registrox->unidad, 1, 0, 'C', true);
	$pdf->Cell($e = 20, $alto, $registrox->cant_solicitada, 1, 0, 'C', true);
	$valAprobada = (isset($registrox->cant_aprobada) && $registrox->cant_aprobada != 0) ? $registrox->cant_aprobada : '';
	$pdf->Cell($f = 25, $alto, $valAprobada, 1, 0, 'C', true);
	$pdf->Cell($g = 0, $alto, $registrox->cant_despachada, 1, 0, 'C', true);
	//----------------------------
	$pdf->ln($alto);
}

//CONDICION 



// (Eliminado) Separador sutil para evitar línea flotante visual bajo Unidad Solicitante
while ($i < 21) {
	$i++;
	//----------------------------
	if ($i % 2 == 1) {
		$pdf->SetFillColor(252, 252, 252);
	} else {
		$pdf->SetFillColor(255, 255, 255);
	}
	$pdf->SetDrawColor(200, 200, 200);
	$pdf->Cell($a, 7, $i, 1, 0, 'C', true);
	$pdf->Cell($b, 7, '', 1, 0, 'C', true);
	$pdf->Cell($c, 7, '', 1, 0, 'L', true);
	$pdf->Cell($d, 7, '', 1, 0, 'C', true);
	$pdf->Cell($e, 7, '', 1, 0, 'C', true);
	$pdf->Cell($f, 7, '', 1, 0, 'C', true);
	$pdf->Cell($g, 7, '', 1, 0, 'C', true);
	//----------------------------
	$pdf->ln(7);
}

$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 8);
//----------------------------
$pdf->Cell($a = 55, 7, utf8_decode('Autorizado Por:'), 0, 0, 'L');
$pdf->Cell($a = 55, 7, utf8_decode('Tramitado Por:'), 0, 0, 'L');
$x = $pdf->GetX();
$pdf->Cell(45, 4, utf8_decode('Por el Almacén:'), 1, 0, 'C');
$pdf->Cell(0, 4, utf8_decode('Solicitante:'), 1, 0, 'L');
//----------------------------

//$pdf->SetY($y);
$pdf->ln();
$pdf->SetX($x);
//----------------------------
$pdf->Cell(45, 7, 'Firma:', 0, 0, 'L');
$pdf->Cell(0, 7, 'Firma y Fecha:', 0, 0, 'L');

//-------- FIRMAS
//------ DATOS DE LA SOLICITUD
$consulta = "SELECT * FROM z_jefes_detalle WHERE division=9;";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);
//--------------------
$consulta_x = "SELECT Apellidos, Nombres FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_accesos_roles.rol='Coordinador Compras' AND modulo='ALMACEN';";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
//--------------------
$consulta_xx = "SELECT Apellidos, Nombres FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_accesos_roles.rol='Jefe' AND modulo='ALMACEN';";
$tabla_xx = mysql_query($consulta_xx);
$registro_xx = mysql_fetch_object($tabla_xx);
//--------------------

$pdf->SetFont('Arial', '', 8);
$pdf->SetY($y);
//---- PARA LOS BORDES
$pdf->Cell($a, 28, '', 1, 0, 'C');
$pdf->Cell($a, 28, '', 1, 0, 'C');
$pdf->SetY($y + 4);
$pdf->SetX($x);
$pdf->Cell(45, 24, '', 1, 0, 'C');
$pdf->Cell(0, 24, '', 1, 0, 'C');
//---- PARA LOS BORDES
$pdf->SetY($y + 18);
// Nombres en negrita
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell($a, 7, primera_cadena($registro->jefe) . ' ' . segunda_cadena($registro->jefe) . ' ' . tercera_cadena($registro->jefe), 0, 0, 'C');
$pdf->Cell($a, 7, primera_cadena($registro_x->Nombres) . ' ' . primera_cadena($registro_x->Apellidos), 0, 0, 'C');
$pdf->Cell(45, 7, primera_cadena($registro_xx->Nombres) . ' ' . primera_cadena($registro_xx->Apellidos), 0, 0, 'C');
// Restaurar a normal para cargos
$pdf->SetFont('Arial', '', 8);
//---- PARA LOS CARGOS
$pdf->SetY($y + 22);
$pdf->Cell($a, 6, 'JEFE DIV. ADMINISTRACION', 0, 0, 'C');
$pdf->Cell($a, 6, 'COOR. COMPRAS', 0, 0, 'C');
$pdf->Cell(45, 6, 'JEFE DE ALMACEN', 0, 1, 'C');
//----------------------------

$pdf->SetXY(17, 135);
if ($estatus == 99) {
	$pdf->SetFont('Arial', 'B', 30);
	$pdf->SetTextColor(255, 0, 0);
	//$pdf->Cell(0,7,'ANULADO',0,0,'C');
	$pdf->TextWithRotation(87, 175, 'ANULADO', 45, 0);
}

$pdf->Output();
?>