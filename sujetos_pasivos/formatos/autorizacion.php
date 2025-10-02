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
		//Posición a 1,2 cm del final
		$this->SetY(-12);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,0,sistema(),0,0,'R');
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

// ACTUALIZACION DEL NUMERO DE LA RESOLUCION
generar_resolucion( $_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
// FIN

// NUMERO DE LA RESOLUCION
list ($resolucion, $fecha_res, $num_res, $anno_res) = funcion_resolucion( $_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
// FIN

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT * FROM vista_exp_especiales WHERE anno=0".$_SESSION['ANNO_PRO']." AND numero=0".$_SESSION['NUM_PRO']." AND sector =0".$_SESSION['SEDE'].";";
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------

////////// SIGLAS DE LA RESOLUCION
$SIGLAS = $registro_datos->Siglas_resol_especiales;
// ---------------------
if ($_SESSION['ANNO_PRO']==2017)
	{
	if ($_SESSION['SEDE'] == 1) { $num_res = $num_res-41;}
	if ($_SESSION['SEDE'] == 2) { $num_res = $num_res-109;}
	if ($_SESSION['SEDE'] == 3) { $num_res = $num_res-74;}
	if ($_SESSION['SEDE'] == 4) { $num_res = $num_res-9;}
	if ($_SESSION['SEDE'] == 5) { $num_res = $num_res-17;}
	}
// ---------------------
$pdf->SetFont('Arial','B',15);
$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
$pdf->SetFont('Times','B',11); $pdf->Ln(8);
$pdf->Cell(0,5,'N°    '. $registro_datos->Siglas_resol_aut_spe.'/'.$anno_res.'/'.($num_res));
//$pdf->Ln(10);

////////// GERENCIA, SECTOR O UNIDAD DE EMISION
$Sede = $registro_datos->texto_sede;
// -----------

////////// CIUDAD DE EMISION
$Ciudad = $registro_datos->nombre;
// -----------

list($anno,$mes,$dia)=explode('-',$fecha_res);
$FECHA=mktime(0,0,0,$mes,$dia,$anno);
$_SESSION['VARIABLE']=$FECHA;

$t=(140-(strlen($Ciudad)));

$mes=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);

$pdf->Text($t,24,$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$FECHA)))-1)].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))));
$pdf->Ln(13);
//----------------------- FIN
	
$pdf->SetFont('Times','B',14-$tamaño);

$pdf->Cell(0,5,'AUTORIZACION',0,0,'C'); 
$pdf->Ln(10);

$pdf->SetFont('Times','',12-$tamaño);
$pdf->Ln(5);

list ($estado, $sede, $conector1, $conector2, $adscripcion) = buscar_sector($_SESSION['SEDE']);
	
$txt='En uso de las facultades previstas en los Artículos 131 numeral 2 y 4, Artículo 182, 183, 184, 185 y 186 del Código Orgánico Tributario vigente, Artículo 4 numeral 9 de la Ley del Servicio Nacional Integrado de Administración Tributaria, en concordancia con el Numeral 7,  del Artículo 102 de la Resolución 32, que define la Organización, Atribuciones y Funciones del  Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT),  se autoriza, por medio de la presente al (los) funcionario(s) : '.$registro_datos->nombrefuncionario.' y '.$registro_datos->nombrecoordinador.' titular(es) de la Cédula(s) de Identidad N° V-'.formato_cedula($registro_datos->funcionario).' y V-'.formato_cedula($registro_datos->coordinador).' respectivamente, adscrito(s) a la División de Sujetos Pasivos Especiales de la Gerencia de Tributos Internos Región Los Llanos, a los efectos de verificar las __________________________________, el cumplimiento de los deberes formales y los deberes de los agentes de retención y percepción en materia de '.strtoupper(tributo($registro_datos->tributo)).' e imponer las sanciones a que haya lugar a la contribuyente '.strtoupper($registro_datos->contribuyente).' Nº de  RIF '.formato_rif($registro_datos->rif).', ubicado en '.strtoupper(trim($registro_datos->direccion)).', en los periodos tributarios: '.strtoupper($registro_datos->texto1).'.';
$pdf->MultiCell(0,6,$txt);
	
// FIRMA DEL JEFE
$pdf->SetY(-85);
include "firma.php";

//-----------
$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
$pdf->Ln(1);
// FIN

$pdf->SetFont('Times','B',9);
$pdf->Cell(0,5,'Funcionario(s) Actuante(s)'); 
$pdf->Ln(7);
$pdf->SetFont('Times','',8);
$pdf->Cell(30,5,'Firma:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(6);
$pdf->Cell(30,5,'Nombre y Apellido:');	
$pdf->Cell(95,5,'__________________________________');	
$pdf->Ln(6);
$pdf->Cell(30,5,'C.I. N°:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(6);
$pdf->Cell(30,5,'Fecha:');	
$pdf->Cell(0,5,'__________________________________');	
	
// FIN DE LA RESOLUCION

$pdf->Output();
?>