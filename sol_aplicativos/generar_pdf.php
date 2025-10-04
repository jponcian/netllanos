<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
require('../lib/fpdf/fpdf.php');

class PDF extends FPDF
{
    public $headers;
    public $widths;

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if($nb > 0 && $s[$nb-1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i < $nb)
        {
            $c = $s[$i];
            if($c == "\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c == ' ')
                $sep = $i;
            $l += $cw[ord($c)];
            if($l > $wmax)
            {
                if($sep == -1)
                {
                    if($i == $j)
                        $i++;
                }
                else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function CheckPageBreak($h)
    {
        if($this->GetY() + $h > $this->PageBreakTrigger)
        {
            $this->AddPage($this->CurOrientation);
            $this->SetFont('Arial', 'B', 10);
            foreach ($this->headers as $i => $h_text) {
                $this->Cell($this->widths[$i], 10, $h_text, 1, 0, 'C');
            }
            $this->Ln();
            $this->SetFont('Arial', '', 10);
        }
    }
}

// Conectar a la BD illanos para consultas de funcionarios
try {
    $pdo_func = new PDO('mysql:host=localhost;port=3306;dbname=illanos;charset=utf8', 'root', '');
    $pdo_func->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error conectando a illanos');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    die('ID no proporcionado');
}

// Usar la conexión de la sesión
$mysqli = $_SESSION['conexionsqli'];
if (!$mysqli) {
    die('Conexión no disponible');
}

// Obtener solicitud
$sql = "SELECT * FROM sol_solicitudes WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$sol = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sol) {
    die('Solicitud no encontrada');
}

// Obtener jefe de la división
$sql = "SELECT d.id_jefediv FROM designacion d WHERE d.id_func = (SELECT id FROM funcionarios WHERE cedula = ?) AND d.estatus = 'activo' LIMIT 1";
$stmt = $pdo_func->prepare($sql);
$stmt->execute([$_SESSION['CEDULA_USUARIO']]);
$design = $stmt->fetch(PDO::FETCH_ASSOC);

$jefe_nombre = 'HECTOR LANDAETA'; // default
if ($design) {
    $sql = "SELECT CONCAT(nombre, ' ', apellido) as nombre FROM funcionarios WHERE id = ?";
    $stmt = $pdo_func->prepare($sql);
    $stmt->execute([$design['id_jefediv']]);
    $jefe = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($jefe) {
        $jefe_nombre = $jefe['nombre'];
    }
}

// Obtener funcionarios y aplicativos
$sql = "SELECT f.*, s.id_aplicativo, a.aplicativo, s.accion
    FROM sol_funcionarios f
    LEFT JOIN sol_aplicativos_solicitados s ON f.id = s.id_funcionario
    LEFT JOIN a_matriz_aplicativos a ON s.id_aplicativo = a.id
    WHERE f.id_solicitud = ?
    ORDER BY f.id, s.id";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Agrupar por funcionario
$funcionarios = [];
while ($row = $result->fetch_assoc()) {
    $fid = $row['id'];
    if (!isset($funcionarios[$fid])) {
        $funcionarios[$fid] = [
            'id' => $row['id'],
            'cedula' => $row['cedula'],
            'nombre' => $row['nombre'],
            'cargo' => $row['cargo'],
            'division' => $row['division'],
            'usuario' => $row['usuario'],
            'aplicativos' => []
        ];
    }
    if ($row['id_aplicativo']) {
        $funcionarios[$fid]['aplicativos'][] = [
            'nombre' => $row['aplicativo'],
            'accion' => $row['accion']
        ];
    }
}

// Generar PDF
if (ob_get_length()) {
    ob_end_clean();
}

$pdf = new PDF('L');

// Table headers and widths
$widths = [55, 25, 35, 35, 20, 25, 20, 60];
$headers = ['Nombres y Apellidos', 'C.I.', 'Cargo', 'Div./Área', 'Sistema', 'Usuario', 'Operación', 'Aplicaciones Requeridas'];
$pdf->headers = $headers;
$pdf->widths = $widths;

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'FORMATO DE SOLICITUDES', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'ISENIAT/ GERENCIA REGIONAL DE TRIBUTOS INTERNOS REGION LOS LLANOS', 0, 1, 'C');
$pdf->Cell(0, 10, 'DIVISIÓN DE: RECAUDACION', 0, 1);
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'FECHA DE SOLICITUD: ' . date('d/m/Y', strtotime($sol['fecha'])), 0, 1);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
foreach ($headers as $i => $h) {
    $pdf->Cell($widths[$i], 10, $h, 1, 0, 'C');
}
$pdf->Ln();

// Data
$pdf->SetFont('Arial', '', 10);
$lineHeight = 5; // Height of one line

foreach ($funcionarios as $func) {
    // Calculate row height
    $nb_nombre = $pdf->NbLines($widths[0], utf8_decode($func['nombre']));
    $nb_cargo = $pdf->NbLines($widths[2], utf8_decode($func['cargo']));
    $nb_division = $pdf->NbLines($widths[3], utf8_decode($func['division']));
    $nb_left = max($nb_nombre, $nb_cargo, $nb_division);
    if ($nb_left == 0) $nb_left = 1;
    $h_left = $lineHeight * $nb_left;

    $num_apps = count($func['aplicativos']);
    if ($num_apps == 0) $num_apps = 1;
    $h_right = $lineHeight * $num_apps;

    $h = max($h_left, $h_right);

    $pdf->CheckPageBreak($h);

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Draw spanned cells
    $pdf->Rect($x, $y, $widths[0], $h);
    $pdf->MultiCell($widths[0], $lineHeight, utf8_decode($func['nombre']), 0, 'L');
    $pdf->SetXY($x + $widths[0], $y);

    $pdf->Rect($x + $widths[0], $y, $widths[1], $h);
    $pdf->Cell($widths[1], $h, $func['cedula'], 0, 0, 'C');
    $pdf->SetXY($x + $widths[0] + $widths[1], $y);

    $pdf->Rect($x + $widths[0] + $widths[1], $y, $widths[2], $h);
    $pdf->MultiCell($widths[2], $lineHeight, utf8_decode($func['cargo']), 0, 'L');
    $pdf->SetXY($x + $widths[0] + $widths[1] + $widths[2], $y);

    $pdf->Rect($x + $widths[0] + $widths[1] + $widths[2], $y, $widths[3], $h);
    $pdf->MultiCell($widths[3], $lineHeight, utf8_decode($func['division']), 0, 'L');
    $pdf->SetXY($x + $widths[0] + $widths[1] + $widths[2] + $widths[3], $y);

    $pdf->Rect($x + $widths[0] + $widths[1] + $widths[2] + $widths[3], $y, $widths[4], $h);
    $pdf->Cell($widths[4], $h, 'ISENIAT', 0, 0, 'C');
    $pdf->SetXY($x + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4], $y);

    $usuario = (isset($func['usuario']) && $func['usuario']) ? utf8_decode($func['usuario']) : 'V' . $func['cedula'];
    $pdf->Rect($x + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4], $y, $widths[5], $h);
    $pdf->Cell($widths[5], $h, $usuario, 0, 0, 'C');

    // Draw line-by-line cells
    $line_by_line_x = $x + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4] + $widths[5];
    $pdf->SetXY($line_by_line_x, $y);

    if (!empty($func['aplicativos'])) {
        $current_y = $y;
        foreach ($func['aplicativos'] as $app) {
            $pdf->Rect($line_by_line_x, $current_y, $widths[6], $lineHeight);
            $pdf->Cell($widths[6], $lineHeight, utf8_decode($app['accion']), 0, 0, 'C');
            
            $pdf->Rect($line_by_line_x + $widths[6], $current_y, $widths[7], $lineHeight);
            $pdf->Cell($widths[7], $lineHeight, utf8_decode($app['nombre']), 0, 0, 'L');
            
            $current_y += $lineHeight;
            $pdf->SetXY($line_by_line_x, $current_y);
        }
    } else {
        $pdf->Rect($line_by_line_x, $y, $widths[6], $h);
        $pdf->Cell($widths[6], $h, '', 0, 0, 'C');
        $pdf->Rect($line_by_line_x + $widths[6], $y, $widths[7], $h);
        $pdf->Cell($widths[7], $h, '', 0, 0, 'L');
    }

    $pdf->SetY($y + $h);
}

$pdf->Ln(20);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'JEFE DE LA DIVISIÓN: Nombre y Apellido: ' . utf8_decode($jefe_nombre) . ' Firma: ___________________', 0, 1);
$pdf->Cell(0, 10, 'COORDINADOR (A): Nombre y Apellido: ______________________ Firma: _____________', 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 10, utf8_decode('Nota: El presente formato debe ser escaneado luego de su firma y sello y deberá ser remitido. No se procesaran solicitudes que no estén debidamente llenadas según el presente Ejemplo.'));

$pdf->Output('I', 'solicitud_' . $id . '.pdf');
