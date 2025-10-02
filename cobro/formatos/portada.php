<?php
ob_end_clean();
session_start();

include('../../conexion.php');
include "../../funciones/auxiliar_php.php";

mysql_query("SET NAMES 'utf8'");
setlocale(LC_ALL, 'sp_ES','sp','es');

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');

class PDF extends FPDF
{
	function Header()
	{
		// Select Arial bold 15
		$this->SetFont('Arial','B',15);
		// Move to the right
		$this->Cell(80);
		// Framed title
		$this->Cell(30,10,'',0,0,'C');
		// Line break
		$this->Ln(10);
	}
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,10,17);
$pdf->SetAutoPageBreak(1,10);

// CAPTURA DE VALORES
$NUMERO = $_GET['num'];
$ANNO = $_GET['anno'];
$SEDE = $_GET['sede'];

////////// DATOS DE LA PROVIDENCIA
$consulta = "SELECT * FROM vista_exp_cobro WHERE sector=".$SEDE." and anno = ".$ANNO." AND numero = ".$NUMERO."";
$tabla = mysql_query($consulta);

if ($registro = mysql_fetch_object($tabla))
{

$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->SetFillColor(190);

//DETERMINAMOS LA DEPENDENCIA
list ($estado, $sede, $conector1, $conector2, $adscripcion, $tipo_division, $sector, $titulo) = buscar_sector($registro->sector);

$ciudad = $sector.", ";
if ($sector>1) {$dependencia = strtoupper($titulo) . ' ' . strtoupper($sector);}
	else	{$dependencia = strtoupper($titulo).' '.strtoupper(buscar_region());}

$fecha = dia($registro->fecha_registro) . " de " . ($_SESSION['meses_anno'][abs(mes($registro->fecha_registro))]) . " de " . anno($registro->fecha_registro);

// POR SI ES CONTRIBUYENTE ESPECIAL
if ($registro->fechaespecial<>NULL)
	{
	$fechainicio = voltea_fecha($registro->fechaespecial);
	$contribuyenteS = 'X';
	}
else
	{
	$fechainicio ="";
	$contribuyenteO = 'X';
	}

// IMAGEN
$pdf->Image('../../imagenes/logo.jpeg',18,21,60);
$pdf->SetFont('Times','B',10);
$pdf->Cell(60,22,'',1,0,'C');

// Título
$pdf->Cell(120,10,$ciudad.$fecha,0,0,'R');
$pdf->Ln(12);

// Salto de línea
$pdf->Cell(60);
$pdf->Cell(40,10,'EXPEDIENTE',1,0,'C',1);
$pdf->Cell(20,10,$registro->numero,1,0,'C');
$pdf->Cell(40,10,utf8_decode('AÑO'),1,0,'C',1);
$pdf->Cell(20,10,$registro->anno,1,0,'C');

$pdf->SetFont('Times','B',9);

// CUADRO VDF
$pdf->Ln(10);

$pdf->SetFont('Times','B',7);
$pdf->Cell(35,9,'PROGRAMA:',1,0,'C',1);
$pdf->Cell(145,9,'',1,0,'C',0);
$pdf->Ln(9);
//--
$lineas = 9;
$pdf->Cell(75,$lineas,'GESTION DE COBRANZA:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'AGENTE DE RETENCION',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'CASINOS, SALAS DE BINGO Y MAQUINAS TRAGANIQUELES:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'BENEFICIOS FISCALES',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'CONTRIBUYENTES ESPECIALES:',1,0,'C',1);
$pdf->Cell(20,$lineas,$contribuyenteS,1,0,'C',0);
$pdf->Cell(65,$lineas,'CONTROL DE INGRESOS',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'CONTRIBUYENTES ORDINARIOS:',1,0,'C',1);
$pdf->Cell(20,$lineas,$contribuyenteO,1,0,'C',0);
$pdf->Cell(65,$lineas,'DEBERES FORMALES',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'PERSONA NATURALES:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'REGISTRO DE CONTRIB EXPORTADORES',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'AVALUOS Y SUCESIONES:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'RECURSOS JERARQUICOS',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--

// DATOS DEL SUJETO PASIVO
$pdf->Cell(180,$lineas,'DATOS DEL SUJETO PASIVO',1,0,'C',1);
$pdf->Ln($lineas);
$pdf->Cell(50,$lineas+1,'CONTRIBUYENTE O RESPONSABLE:',1,0,'C',1);
$pdf->MultiCell(130,10,$registro->contribuyente,1,'C');
//$pdf->Ln($lineas);
$pdf->Cell(50,$lineas,'R.I.F. Nro:',1,0,'C',1);
$pdf->Cell(130,$lineas,substr($registro->rif,0,1).'-'.substr($registro->rif,1,8).'-'.substr($registro->rif,-1),1,0,'C',0);
$pdf->Ln($lineas);
$pdf->Cell(50,$lineas+1,'DOMICILIO FISCAL:',1,0,'C',1);
//$pdf->Cell(130,$lineas,$registro->direccion,1,0,'C',0);
$pdf->MultiCell(130,5,$registro->direccion,1,'C');
//$pdf->Ln($lineas);
$pdf->Cell(50,$lineas,'NRO. DE TELEFONO:',1,0,'C',1);
$pdf->Cell(130,$lineas,$registro->Telefonos,1,0,'C',0);
$pdf->Ln($lineas);
$pdf->Cell(50,$lineas,'CORREO ELECTRONICO:',1,0,'C',1);
$pdf->Cell(130,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);

// DATOS DIVISION DE SUJETOS PASIVOS ESPECIALES
$pdf->Cell(180,$lineas,'DATOS AREA DE COBRO',1,0,'C',1);
$pdf->Ln($lineas);

$pdf->Cell(70,$lineas,'DEPENDENCIA A LA CUAL ESTA ADSCRITO:',1,0,'C',1);
$pdf->Cell(110,$lineas,$dependencia,1,0,'C',0);
$pdf->Ln($lineas);

$pdf->Cell(70,$lineas,'FISCAL:',1,0,'C',1);
$pdf->Cell(110,$lineas,formato_cedula($registro->funcionario).' - '.$registro->nombrefuncionario,1,0,'C',0);
$pdf->Ln($lineas);

$pdf->Cell(70,$lineas,'SUPERVISOR:',1,0,'C',1);
$pdf->Cell(110,$lineas,formato_cedula($registro->coordinador).' - '.$registro->nombrecoordinador,1,0,'C',0);
$pdf->Ln($lineas);

// OBSERVACIONES
$pdf->Cell(180,$lineas,'OBSERVACIONES',1,0,'C',1);
$pdf->Ln($lineas);

$pdf->Ln(5);

$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);

// CUADRO 
$pdf->SetY(20);
$pdf->SetX(17);
$pdf->SetFont('Times','',7);
$pdf->Cell(180,240,'',1,0,'C',0);
// ------	

}
// FIN DE LA VALIDACION DE LA CONSULTA

$pdf->Output();

?>