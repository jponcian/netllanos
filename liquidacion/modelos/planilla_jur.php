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

function extraer_caracteres($cadena)
	{
	$var=array();
	$longitud=strlen($cadena);
	for ($i=1;$i<$longitud+1;$i++) 
		{
		$var[$i]=substr($cadena, $i-1,1);
		}
	return $var;
	}

class PDF extends FPDF
	{
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
$pdf->SetMargins(35,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=20);

//CONSULTA A LA BASE DE DATOS
$consulta = "SELECT * FROM vista_liquidacion_planillas_jur WHERE status NOT IN (60,91,99,100) AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'];
//echo $consulta ;
$tabla = mysql_query($consulta);

while ($registro = mysql_fetch_object($tabla))
{
	//--- COMIENZO DEL REPORTE
	$pdf->AddPage();
	setlocale(LC_TIME, 'sp_ES','sp', 'es');
	$pdf->SetFont('Times','',12);
	//--- TIPO TRIBUTO
	//----- PRIMERO SE EVALUAN LOS FRACCIONAMIENTOS
	$codigo = $registro->forma;
	
//	if ($registro->serie==41)
//		{
//		$codigo = $registro->id_tributo;
//		}
//	else
//		{
//		if ($registro->concepto=="MULTA")
//			{
//			$codigo = 1;
//			}
//		else
//			{
//			$codigo = 32;
//			}
//		}
		
	$codigo = sprintf("%002s", $codigo);
	//LEEMOS EN ARRAY
	$codigo = str_replace("/", "", $codigo);
	$tributo = extraer_caracteres($codigo);
	//IMPRIMIMOS EL TRIBUTO
	$pdf->SetXY(124,18);
	$pdf->Cell(5,5,$tributo[1],0,0,'C');
	$pdf->Cell(5,5,$tributo[2],0,0,'C');
	
	//IMPRIMIMOS EL NUMERO DE NOTIFICACION
	$numero = $registro->numeronotificacion;
	$num_notificacion = extraer_caracteres($numero);
	$pdf->SetXY(38,32);
	$i=1;
	while ($i<=10)
		{
		$pdf->Cell(5.5,5,$num_notificacion[$i],0,0,'C');
		$i++;
		}
	
	//IMPRIMIMOS EL RIF
	$numerorif = $registro->rif;
	$rif = extraer_caracteres($numerorif);
	$pdf->SetXY(152,32);
	$i=1;
	while ($i<=10)
		{
		$pdf->Cell(5.5,5,$rif[$i],0,0,'C');
		$i++;
		}
	
	//IMPRIMIMOS EL PERIODO INICIAL
	$periodoinicial = str_replace("/", "", date('d/m/Y', strtotime($registro->periodoinicio)));
	$inicio = extraer_caracteres($periodoinicial);
	$pdf->SetXY(88,38);
	$i=1;
	while ($i<=8)
		{
		$pdf->Cell(5.5,5,$inicio[$i],0,0,'C');
		$i++;
		}
	
	//IMPRIMIMOS EL PERIODO FINAL
	$periodofinal = str_replace("/", "", date('d/m/Y', strtotime($registro->periodofinal)));
	$fin = extraer_caracteres($periodofinal);
	$pdf->SetXY(160,38);
	$i=1;
	while ($i<=8)
		{
		$pdf->Cell(5.5,5,$fin[$i],0,0,'C');
		$i++;
		}
	
	//IMPRIMIMOS EL NOMBRE DEL CONTRIBUYENTE
	$pdf->SetXY(90,43);
	$pdf->Cell(4,5,$registro->contribuyente,0,0,'C');
	
	//IMPRIMIMOS CODIGO DE LA REGION
	$pdf->SetXY(43,49);
	$pdf->Cell(5.5,5,'0',0,0,'C');
	$pdf->Cell(5.5,5,'2',0,0,'C');
	
	//IMPRIMIMOS EL NUMERO DE LIQUIDACION
	$numeroliq = $registro->numeroliquidacion;
	$num_liq = extraer_caracteres($numeroliq);
	$pdf->SetXY(85,49);
	$i=1;
	while ($i<=15)
		{
		$pdf->Cell(5.2,5,$num_liq[$i],0,0,'C');
		$i++;
		}
	
	//IMPRIMIMOS FECHA DE LIQUIDACION
	$fecha = str_replace("/", "", date('d/m/Y', strtotime($registro->fecha_liquidacion)));
	$fechaliq = extraer_caracteres($fecha);
	$pdf->SetXY(59,55);
	$i=1;
	while ($i<=15)
		{
		$pdf->Cell(5.5,5,$fechaliq[$i],0,0,'C');
		$i++;
		}
	
	//IMPRIMIMOS LA PORCION
	$porc = sprintf("%002s", $registro->porcion);
	$porcion = extraer_caracteres($porc);
	$pdf->SetXY(116,55);
	$pdf->Cell(5.5,5,$porcion[1],0,0,'C');
	$pdf->Cell(5.5,5,$porcion[2],0,0,'C');
	
	//IMPRIMIMOS PLAZO DE PAGO
	$pdf->SetXY(173,55);
	$pdf->Cell(4,5,'INMEDIATO',0,0,'C');
	
	//IMPRIMIMOS CODIGO PLAN DE CUENTA
	$codigocta = $registro->cuenta;
	$cod_plan = extraer_caracteres($codigocta);
	$pdf->SetXY(33,64);
	$i=1;
	while ($i<=9)
		{
		$pdf->Cell(5.2,5,$cod_plan[$i],0,0,'C');
		$i++;
		}
	
	//IMPRIMIMOS CONCEPTO (MULTA O INTERESES)
	$pdf->SetXY(92,64);
	$pdf->Cell(5.5,5,$registro->descripcion,0,0,'L');
	
	$monto_planilla = ($registro->monto_bs / $registro->concurrencia) * $registro->especial;
	//IMPRIMIMOS MONTO
	$pdf->SetXY(150,64);
	$pdf->Cell(40,5,number_format($monto_planilla, 2, ',', '.'),0,0,'R');
	
	//IMPRIMIMOS TOTAL
	$pdf->SetXY(150,85);
	$pdf->Cell(40,5,number_format($monto_planilla, 2, ',', '.'),0,0,'R');
	
	//FIRMAS
	include "firma.php";
	$pdf->SetFont('Times','B',8);
	$pdf->SetXY(135,98);
	$pdf->Cell(85,5,$jefe,0,0,'C');
	$pdf->SetFont('Times','',6);
	$pdf->SetXY(135,102);
	$pdf->Cell(85,5,$cargo,0,0,'C');
	$pdf->SetXY(135,105);
	$pdf->Cell(85,5,utf8_decode($cedula),0,0,'C');
	$pdf->SetXY(135,108);
	$pdf->Cell(85,5,$providencia,0,0,'C');
	$pdf->SetXY(135,111);
	$pdf->Cell(85,5,'de fecha '.$fecha_prov,0,0,'C');
}	

$pdf->Output();
?>