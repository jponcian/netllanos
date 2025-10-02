<?php
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
 
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');

//------- PARA ACTUALIZAR LAS PLANILLAS
//$consulta = "UPDATE liquidacion SET fecha_impresion=date(now()), status=19 WHERE id_resolucion=0 AND status=11 AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." AND origen_liquidacion=".$_SESSION['ORIGEN'];
//$tabla = mysql_query($consulta);
//--------------------

class PDF extends FPDF
	{
	// Cabecera de página
	function Header()
		{
		$this->Image('../../imagenes/logo.jpeg',17,8,55);
		}

	function Footer()
		{    
		//Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,0,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','nuevo');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=20);

//CONSULTA A LA BASE DE DATOS
$consulta = "SELECT * FROM vista_liquidacion_planillas_jur WHERE status NOT IN (60,91,99,100) AND serie<> 38 AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'];
$tabla = mysql_query($consulta);
while ($registro = mysql_fetch_object($tabla))
{ 
	//--- COMIENZO DEL REPORTE
	$pdf->AddPage();
	setlocale(LC_TIME, 'sp_ES','sp', 'es');

	$pdf->SetFont('Arial','B',12);
	// Movernos a la derecha
	$pdf->Cell(75);
	// Título
	$pdf->Cell(30,0,'PLANILLA DE LIQUIDACIÓN',0,0,'C');
	$pdf->Cell(60,0,'N-'.$registro->numeronotificacion,0,0,'R');
	// Salto de línea
	$pdf->Ln(8);

	$pdf->SetFont('Times','',8);
	$ubicacion = "REGIÓN LOS LLANOS";
	//--- ENCABEZADO IDENTIFICACION DE LA GERENCIA Y PLANILLA DE LIQUIDACION
	$pdf->Cell(65,5,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS: ',0,0,'L');
	$pdf->SetFont('Times','B',8);
	$pdf->Cell(45,5,$ubicacion,0,0,'L');
	$pdf->SetFont('Times','',8);
	$pdf->Cell(45,5,'N° DE LIQUIDACIÓN',0,0,'C');
	$pdf->Cell(25,5,'FECHA',0,0,'C');
	$pdf->Ln(3);
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(110);
	$pdf->Cell(45,5,$registro->numeroliquidacion,0,0,'C');
	$pdf->Cell(25,5,date("d/m/Y", strtotime($registro->fecha_liquidacion)),0,0,'C');
	$pdf->Ln(5);
	//LOS DATOS DEL CONTRIBUYENTE
	$pdf->SetFont('Times','',10);
	$pdf->Cell(65,5,'DATOS DEL CONTRIBUYENTE, CAUSANTE O AGENTE DE RETENCIÓN',0,0,'L');
	$Y = $pdf->GetY()+5;
	$pdf->Line(20, $Y, 205, $Y);
	$pdf->Ln(5);
	$pdf->SetFont('Times','',6);
	$pdf->Cell(22,5,'RIF',0,0,'C');
	$pdf->Cell(143,5,'APELLIDOS Y NOMBRES - RAZÓN SOCIAL',0,0,'L');
	$pdf->Cell(20,5,'ZONA POSTAL',0,0,'C');
	$pdf->Ln(5);
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(22,5,$registro->rif,0,0,'C');
	$pdf->Cell(143,5,$registro->contribuyente,0,0,'L');
	$pdf->Cell(20,5,'2312',0,0,'C');
	$pdf->Ln(6);
	$pdf->SetFont('Times','',6);
	$pdf->Cell(20,5,'DIRECCIÓN',0,0,'L');
	$pdf->Ln(5);
	$pdf->SetFont('Times','B',10);
	$pdf->MultiCell(185,5,$registro->direccion,0,'L');
	$pdf->Ln(7);
	
	//IDENTIFICACION DE LA DECLARACION
	$pdf->SetFont('Times','',10);
	$pdf->Cell(65,5,'IDENTIFICACIÓN DE LA DECLARACIÓN',0,0,'L');
	$Y = $pdf->GetY()+5;
	$pdf->Line(20, $Y, 205, $Y);
	$pdf->Ln(5);
	$pdf->SetFont('Times','',6);
	$pdf->Cell(22,5,'CÓDIGO TRIBUTO',0,0,'C');
	$pdf->Cell(123,5,'TIPO DE TRIBUTO',0,0,'L');
	$pdf->Cell(40,5,'PERIODO O EJERCICIO FISCAL',0,0,'C');
	$pdf->Ln(3);
	$pdf->Cell(145);
	$pdf->Cell(20,5,'DESDE',0,0,'C');
	$pdf->Cell(20,5,'HASTA',0,0,'C');
	$pdf->Ln(3);
	$pdf->SetFont('Times','B',10);
	$codigo = $registro->forma;
	$pdf->Cell(22,5,$codigo,0,0,'C');
	
	//----- PRIMERO SE EVALUAN LOS FRACCIONAMIENTOS
	if ($registro->serie==41)
		{
		$pdf->Cell(123,5,$registro->descripcion,0,0,'L');
		}
	else
		{
		$pdf->Cell(123,5,$registro->descripcion.' '.$registro->siglas,0,0,'L');
		}

	$pdf->Cell(20,5,date("d/m/Y", strtotime($registro->periodoinicio)),0,0,'C');
	$pdf->Cell(20,5,date("d/m/Y", strtotime($registro->periodofinal)),0,0,'C');
	
	$pdf->Ln(10);
	
	//DEMOSTRACION DE LA LIQUIDACION
	//SIGLAS DE LA RESOLUCION	
	if ($registro->id_resolucion>0)
		{
		list ($sigla_resolucion, $fecha_resolucion, $num_res) = funcion_resolucion_id( $registro->sector, $registro->origen_liquidacion, $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO'], $registro->id_resolucion);
		}
	else
		{
		list ($sigla_resolucion, $fecha_resolucion, $num_res) = funcion_resolucion( $registro->sector, $registro->origen_liquidacion, $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
		}

	//------------------------------
	$pdf->SetFont('Times','',10);
	$pdf->Cell(65,5,'DEMOSTRACIÓN DE LA LIQUIDACION',0,0,'L');
	$Y = $pdf->GetY()+5;
	$pdf->Line(20, $Y, 205, $Y);
	$pdf->Ln(20);
	//----- PRIMERO SE EVALUAN LOS FRACCIONAMIENTOS
	if ($registro->serie==41)
		{
		$pdf->Cell(15);
		$pdf->MultiCell(145,5,'Fraccionamiento de Pago acordado segun Convenimiento N°: '.$num_res.' , de fecha '.voltea_fecha($fecha_resolucion).' de acuerdo con lo previsto en el articulo 45 del Código Orgánico Tributario Vigente y de conformidad con lo pautado en Capitulo III de la Providencia Administrativa N°: 0116 de fecha 14 de Febrero de 2005, Publicada en Gaceta Oficial N°: 38.213 de fecha 21/06/2005, para las obligaciones Tributarias Primitivas.');
		}
	else
		{
		if ($registro->monto_ut>0)
			{ $ut = 'EN LA CANTIDAD DE '.formato_moneda($registro->monto_ut / $registro->concurrencia * $registro->especial).' UNIDADES TRIBUTARIAS, EQUIVALENTE A ';	}
		else
			{ $ut ='POR ';	}
		//---------------------------
		$pdf->Cell(25);
		$pdf->MultiCell(115,5,'SE EMITE PLANILLA DE LIQUIDACIÓN POR CONCEPTO DE '.$registro->concepto.', '.$ut.'BOLIVARES '.formato_moneda($registro->monto_bs / $registro->concurrencia * $registro->especial).'; SEGUN RESOLUCION N° '.$sigla_resolucion.' DE FECHA '.voltea_fecha($fecha_resolucion));
		}
	//UBICACION DEL PIE DE PAGINA
	$pdf->SetXY(20, 230);
	$pdf->Line(20, 230, 205, 230);
	$pdf->Ln(1);
	$pdf->SetFont('Times','',6);
	$pdf->Cell(70,5,'N° RESOLUCION(ES) ANEXA(S) A ESTA PLANILLA DE LIQUIDACIÓN',0,0,'L');
	$pdf->Cell(25,5,'FECHA',0,0,'C');
	$pdf->Cell(30,5,'IDENTIFICACIÓN DEL PAGO',0,0,'L');
	$pdf->SetFont('Times','',5);
	$pdf->MultiCell(58,3,'PAGUE EL(LOS) SIGUIENTE(S) MONTO(S) MEDIANTE LA(S) PLANILLA(S) PARA PAGAR (FORMA(S)-9) ANEXA(S)');
	$pdf->Line(20, 235, 115, 235);
	$pdf->SetFont('Times','B',6.5);
	$pdf->Cell(70,5,$sigla_resolucion,0,0,'C');
	$pdf->Cell(25,5,voltea_fecha($fecha_resolucion),0,0,'C');
	$pdf->Line(116, 238, 205, 238);
	$pdf->Line(20, 242, 115, 242);
	$pdf->Ln(1);
	$pdf->Cell(95);
	$pdf->SetFont('Times','',6);
	$pdf->Cell(10,5,'PORCIÓN',0,0,'C');
	$pdf->MultiCell(28,3,'FECHA DE VENCIMIENTO O PLAZO EN DIAS',0,'C');
	
	$pdf->SetXY(155, 238);
	$pdf->Cell(23,5,'MONTO Bs.',0,0,'C');
	$pdf->Cell(20,5,'PLAN ÚNICO DE CUENTA',0,0,'L');
	$pdf->Ln(5);
	$pdf->Cell(95);
	$pdf->SetFont('Times','B',6.5);
	$pdf->Cell(10,5,sprintf("%002s",$registro->porcion),0,0,'C');
	$pdf->Cell(28,5,'INMEDIATO',0,0,'C');
	$pdf->Cell(23,5,formato_moneda($registro->monto_bs / $registro->concurrencia * $registro->especial),0,0,'C');
	$pdf->Cell(20,5,sprintf("%010s",$registro->cuenta),0,0,'C');
	$pdf->Line(116, 250, 205, 250);
	$pdf->Ln(6);
	
	//FIRMAS
	include "firma.php";
	$pdf->Cell(95);
	$pdf->SetFont('Times','B',8);
	$pdf->Cell(85,5,'FIRMA AUTORIZADA',0,0,'C');
	$pdf->Ln(14);
	$pdf->Cell(47,5,'___________________',0,0,'C');
	$pdf->Cell(46,5,'___________________',0,0,'C');
	$pdf->Cell(85,5,$jefe,0,0,'C');
	$pdf->Ln(4);
	$pdf->Cell(47,5,'FIRMA',0,0,'C');
	$pdf->Cell(46,5,'C.I.N°',0,0,'C');
	$pdf->SetFont('Times','',6);
	$pdf->Cell(85,5,$cargo,0,0,'C');
	$pdf->Ln(3);
	$pdf->Cell(93,5,'',0,0,'C');
	$pdf->Cell(85,5,utf8_decode($cedula),0,0,'C');
	$pdf->Ln(3);
	$pdf->Cell(93,5,'',0,0,'C');
	$pdf->Cell(85,5,$providencia,0,0,'C');
	$pdf->Ln(3);
	$pdf->Cell(93,5,'ORIGINAL: CONTRIBUYENTE',0,0,'C');
	$pdf->Cell(85,5,'de fecha '.$fecha_prov,0,0,'C');
	$pdf->Ln(4);
}

$pdf->Output();
?>