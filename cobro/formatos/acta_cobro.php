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
mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
	{
	function Footer()
		{    
		////////// INFORMACION DEL EXPEDIENTE
		$consulta_datos = "SELECT * FROM vista_actas_cobro WHERE anno=0".$_GET['anno']." AND numero=0".$_GET['num']." AND sector =0".$_GET['sector'].";";
		$tabla_datos = mysql_query($consulta_datos);
		$registro_datos = mysql_fetch_object($tabla_datos);
		// FIN		
		$resolucion = $registro_datos->Siglas_resol_actas_spe.$registro_datos->AnnoResolucion.'-'.$registro_datos->NroResolucion;
		//Posici�n a 1,1 cm del final
		$this->SetY(-11);
		//Arial it�lica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//N�mero de p�gina
		$this->Cell(120,0,$resolucion,0,0,'L');
		$this->Cell(0,0,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=15);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT * FROM vista_actas_cobro WHERE anno=0".$_GET['anno']." AND numero=0".$_GET['num']." AND sector =0".$_GET['sector'].";";
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------

////////// SIGLAS DE LA RESOLUCION
$resolucion = $registro_datos->Siglas_resol_actas_spe.$registro_datos->AnnoResolucion.'-'.$registro_datos->NroResolucion;
// ---------------------

// ---------------------
$pdf->SetFont('Arial','B',15);
$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
$pdf->SetFont('Times','B',11); 
$pdf->Ln(8);
$pdf->Cell(0,5,'N�    '.$resolucion);
//$pdf->Ln(10);

////////// GERENCIA, SECTOR O UNIDAD DE EMISION
$Sede = $registro_datos->adscripcion_gerencia;
$Ciudad = $registro_datos->nombre;
$direccion = $registro_datos->direccion_especiales;
// -----------

list($anno,$mes,$dia)=explode('-',$registro_datos->Fecha_Resolucion);
$FECHA=mktime(0,0,0,$mes,$dia,$anno);
$_SESSION['VARIABLE']=$FECHA;

$t=(140-(strlen($Ciudad)));

$mes=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);

$pdf->Text($t,24,$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$FECHA)))-1)].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))));
$pdf->Ln(10);
//----------------------- FIN
	
$pdf->SetFont('Times','B',13-$tama�o);

$pdf->Cell(0,5,'ACTA DE COBRO',0,0,'C'); 
$pdf->Ln(10);

$pdf->SetFont('Times','',11);
$pdf->Cell(0,5,'Contribuyente:');

$pdf->SetFont('Times','B',11);
$pdf->SetX(60);
$pdf->MultiCell(0,5,strtoupper($registro_datos->contribuyente));
$pdf->Ln(3); 

$pdf->SetFont('Times','',11);
$pdf->Cell(0,5,'RIF N�:'); 

$pdf->SetFont('Times','B',11);
$pdf->SetX(60);
$pdf->Cell(0,5,formato_rif($registro_datos->rif));
$pdf->Ln(8); 

$pdf->SetFont('Times','',11);
$pdf->Cell(0,5,'Domicilio Fiscal:'); 

$pdf->SetFont('Times','B',11);
$pdf->SetX(60);
$pdf->MultiCell(0,5,strtoupper(trim($registro_datos->direccion)));

$pdf->SetFont('Times','',10);
$pdf->Ln(5);

list ($estado, $sede, $conector1, $conector2, $adscripcion) = buscar_sector($_GET['sector']);

$txt='En '.$registro_datos->nombre.', Estado '.$estado.', la Divisi�n de Sujetos Pasivos Especiales adscrita a '.$adscripcion.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.buscar_region().' del Servicio Nacional Integrado de Administraci�n Aduanera y Tributaria (S.E.N.I.A.T), de conformidad con lo dispuesto en los art�culos 131 numeral 1 del C�digo Org�nico Tributario, Articulo 4, numerales 7 y 14, de la Ley  del Servicio Nacional Integrado de Administraci�n Aduanera y Tributaria y 102 numeral 8 y 10 de la Resoluci�n N�32 sobre la Organizaci�n Atribuciones y Funciones del Servicio Nacional Integrado de Administraci�n Tributaria (S.E.N.I.A.T) publicada en la Gaceta Oficial numero 4.881 Extraordinario de fecha 29 de marzo de 1995, procede formalmente a emplazar al contribuyente al pago de la siguiente obligaci�n tributaria vencida a favor de la Rep�blica Bolivariana de Venezuela por concepto de imposici�n de Multa contenidas en las planillas de liquidaci�n que a continuaci�n se detallan:';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

$pdf->SetFont('Times','B',11);
$pdf->SetFillColor(190);
//-------- TITULOS
$pdf->Cell($a=60,6,'Liquidacion',1,0,'C',1);
$pdf->Cell($b=30,6,'Concepto',1,0,'C',1); 
$pdf->Cell($c=50,6,'Periodo',1,0,'C',1); 
$pdf->Cell(0,6,'Monto',1,0,'C',1); 
$pdf->Ln(6);
	
$pdf->SetFont('Times','',11);
// PLANILLAS
$consulta_x = "SELECT * FROM vista_liquidaciones WHERE liquidacion IN (".$registro_datos->Planillas.");";
$tabla_x = mysql_query($consulta_x); //echo $consulta_x;
while ($registro_x = mysql_fetch_object($tabla_x))
	{
	$pdf->Cell($a,6,$registro_x->liquidacion,1,0,'C');
	$pdf->Cell($b,6,($registro_x->concepto),1,0,'C'); 
	$pdf->Cell($c,6,voltea_fecha($registro_x->periodoinicio).' al '.voltea_fecha($registro_x->periodofinal),1,0,'C'); 
	$pdf->Cell(0,6,formato_moneda($registro_x->monto_bs),1,0,'R'); 
	$pdf->Ln(6);
	}
//---------

$pdf->Ln(4);
$txt='Esta obligaci�n deber� ser pagada de manera INMEDIATA en una Oficina Receptora de Fondos Nacionales y posteriormente acreditar el pago o demostrar haber pagado ante el �rea de Cobranzas de la Divisi�n de Sujetos Pasivos Especiales adscrita a '.$adscripcion.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.buscar_region().' ubicada '.$direccion.'. La presente notificaci�n hace aplicable la disposici�n contenida en el art�culo 60 del C�digo Org�nico Tributario vigente. Se hacen dos (02) ejemplares de un mismo tenor y a un solo efecto, los cuales deber�n ser firmados por cada una de las partes intervinientes como constancia de haber sido efectuada la notificaci�n, quedando en poder del contribuyente uno (01) de los mismos y uno (01) para control de la Administraci�n Tributaria.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4);
$pdf->SetFont('Times','B',11);
	
$txt='Comun�quese,';
$pdf->Cell(0,5,$txt,0,0,'C');

// FIRMA DEL JEFE
$pdf->Ln(7);
include "firma.php";
// FIN
	
// FIN DE LA RESOLUCION
$pdf->Output();
?>