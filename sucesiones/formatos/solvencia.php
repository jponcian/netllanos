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

class PDF extends FPDF
	{
	function Footer()
		{    
		//Posici�n a 1,5 cm del final
		$this->SetY(-15);
		//Arial it�lica 8
		$this->SetFont('Times','I',8);
		//Color del texto en gris
		$this->SetTextColor(120);
		//N�mero de p�gina
		$this->Cell(0,0,sistema(),0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,22);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=20);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFillColor(192,192,192);
$tama�o=-1;

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT * FROM vista_re_sucesiones_solvencias WHERE anno=0".$_SESSION['ANNO_PRO']." AND numero=0".$_SESSION['NUM_PRO']." AND sector =0".$_SESSION['SEDE'].";"; 
//echo $consulta_datos;
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------

// ---------------------
$pdf->SetFont('Times','B',8-$tama�o);
$pdf->Cell(0,5,'GERENCIA');
$pdf->Ln(5);
$pdf->Cell(0,5,'REGIONAL DE TRIBUTOS INTERNOS'); 
$pdf->Ln(5);
$pdf->Cell(0,5,mayuscula(buscar_region())); 
$pdf->Ln(15);

$pdf->SetFont('Times','B',17-$tama�o);
$pdf->Cell(0,5,'SOLVENCIA SUCESORAL',0,0,'C'); 
$pdf->Ln(15);

$pdf->SetFont('Times','B',9-$tama�o);
$pdf->Cell(50,5,'N�MERO:',1,0,'C',true); 
$pdf->Cell(50,5,'FECHA EXPEDICI�N:',1,0,'C',true); 
$pdf->Cell(0,5,'N�MERO DE EXPEDIENTE:',1,0,'C',true); 
$pdf->Ln(5);
$pdf->Cell(50,5,$registro_datos->num_solvencia,1,0,'C'); 
$pdf->Cell(50,5,voltea_fecha($registro_datos->fecha_emision),1,0,'C'); 
$pdf->Cell(0,5,$registro_datos->anno.' / '.$registro_datos->numero,1,0,'C'); 
$pdf->Ln(5);

$pdf->Cell(0,5,'NOMBRES Y APELLIDOS DEL CAUSANTE:',1,0,'L',true);
$pdf->Ln(5);
$pdf->Cell(0,5,$registro_datos->contribuyente,1,0,'L'); 
$pdf->Ln(5);

$pdf->Cell(85,5,'FECHA DE FALLECIMIENTO:',1,0,'C',true); 
$pdf->Cell(0,5,'N�MERO DE R. I. F. :',1,0,'C',true); 
$pdf->Ln(5);
$pdf->Cell(85,5,voltea_fecha($registro_datos->fecha_fall),1,0,'C'); 
$pdf->Cell(0,5,formato_rif($registro_datos->rif),1,0,'C'); 
$pdf->Ln(5);

$pdf->Cell(0,5,'REPRESENTANTE LEGAL O RESPONSABLE:',1,0,'L',true);
$pdf->Ln(5);
$pdf->Cell(0,5,mayuscula($registro_datos->representante),1,0,'L'); 
$pdf->Ln(5);

$y = $pdf->GetY();
$pdf->MultiCell(50,5,'FORMULARIO PARA AUTOLIQUIDACI�N:',1,'C',true); 
$pdf->SetY($y);
$pdf->Cell(50,10,''); 
$pdf->Cell(60,10,'FECHA DE LA DECLARACI�N:',1,0,'C',true); 
$pdf->Cell(0,10,'TIPO DE DECLARACI�N:',1,0,'C',true); 
$pdf->Ln(10);

if ($registro_datos->sustitutiva<>'' and $registro_datos->fecha_sustitutiva<>'0000/00/00')
	 {$txt1 = $registro_datos->sustitutiva; $txt2 = $registro_datos->fecha_sustitutiva;} 
else 	 {$txt1 = $registro_datos->declaracion; $txt2 = $registro_datos->fecha_declaracion;} 
	 
$pdf->Cell(50,5,$txt1,1,0,'C'); 
$pdf->Cell(60,5,voltea_fecha($txt2),1,0,'C'); 
$pdf->Cell(0,5,'AB-INTESTATO',1,0,'C'); 
$pdf->Ln(5);

$pdf->Cell(70,5,'FORMULARIOS:',1,0,'C',true); 
$pdf->Cell(0,5,'N�MERO(S) DE FORMULARIO(S):',1,0,'C',true); 
$pdf->Ln(5);

$pdf->Cell(70,5,'BIENES INMUEBLES:',1,0,'L'); 
$pdf->Cell(0,5,($registro_datos->inmuebles),1,0,'L'); 
$pdf->Ln(5);

if ($registro_datos->muebles<>'') {$txt = $registro_datos->muebles;} else {$txt = 'NO FUE DECLARADO';}
$pdf->Cell(70,5,'BIENES MUEBLES:',1,0,'L'); 
$pdf->Cell(0,5,$txt,1,0,'L'); 
$pdf->Ln(5);

if ($registro_datos->pasivo<>'') {$txt = $registro_datos->pasivo;} else {$txt = 'NO FUE DECLARADO';}
$pdf->Cell(70,5,'PASIVO:',1,0,'L'); 
$pdf->Cell(0,5,$txt,1,0,'L'); 
$pdf->Ln(5);

if ($registro_datos->desgravamenes<>'') {$txt = $registro_datos->desgravamenes;} else {$txt = 'NO FUE DECLARADO';}
$pdf->Cell(70,5,'DESGR�VAMENES:',1,0,'L'); 
$pdf->Cell(0,5,$txt,1,0,'L'); 
$pdf->Ln(5);

if ($registro_datos->exenciones<>'') {$txt = $registro_datos->muebles;} else {$txt = 'NO FUE DECLARADO';}
$pdf->Cell(70,5,'EXENCIONES:',1,0,'L'); 
$pdf->Cell(0,5,$txt,1,0,'L'); 
$pdf->Ln(5);

if ($registro_datos->exoneraciones<>'') {$txt = $registro_datos->muebles;} else {$txt = 'NO FUE DECLARADO';}
$pdf->Cell(70,5,'EXONERACIONES:',1,0,'L'); 
$pdf->Cell(0,5,$txt,1,0,'L'); 
$pdf->Ln(5);

if ($registro_datos->litigiosos<>'') {$txt = $registro_datos->litigiosos;} else {$txt = 'NO FUE DECLARADO';}
$pdf->Cell(70,5,'BIENES LITIGIOSOS:',1,0,'L'); 
$pdf->Cell(0,5,$txt,1,0,'L'); 
$pdf->Ln(5);

$pdf->Cell(85,5,'COORDINADORA AREA DE SUCESIONES',1,0,'C',true); 
$pdf->Cell(0,5,'JEFE DE DIVISION DE RECAUDACION',1,0,'C',true); 
$pdf->Ln(5);

$y = $pdf->GetY();
$pdf->Cell(85,40,'',1,0); 
$pdf->Cell(0,40,'',1,0); 
$pdf->Ln(40);
$pdf->SetY($y);

$pdf->SetFont('Times','',9-$tama�o);
$pdf->Cell(85,20,'Firma:',0,0); 
$pdf->Cell(0,20,'Firma:',0,0); 
$pdf->Ln(25);

$pdf->SetFont('Times','B',9-$tama�o);

//--- JEFE Y COORDINADOR
$consulta_x = "SELECT * FROM vista_jefe_rec WHERE id_sector=".$_SESSION['SEDE'].";";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
list($funcionario) = funcion_funcionario($registro_datos->coordinador);
//----------------

$pdf->Cell(85,5,mayuscula($funcionario),0,0,'C'); 
$pdf->Cell(0,5,$registro_x->jefe,0,0,'C'); 
$pdf->Ln(5);

$pdf->Cell(85,5,'C.I. N� '.formato_cedula($registro_datos->coordinador),0,0,'C'); 
$pdf->Cell(85,5,'C.I. N� '.formato_cedula($registro_x->cedula),0,0,'C'); 
$pdf->Ln(10);

$pdf->SetFont('Times','',8-$tama�o);

$txt='Se expide el presente documento de conformidad con lo establecido en el Art�culo 42 de la Ley de Impuesto Sobre Sucesiones, Donaciones y Dem�s Ramos Conexos. La emisi�n de la presente solvencia acredita �nicamente los activos y pasivos en ella declarados, ha sido expedida de acuerdo a los datos suministrados por los herederos y est� sujeta a las modificaciones que resulten de las investigaciones que practiquen los funcionarios fiscales en el ejercicio de las facultades de fiscalizaci�n y determinaci�n establecidas en el art�culo 137 y siguientes del C�digo Org�nico Tributario Vigente. Se exhorta a todos los Registros y Notar�as a no dar curso a documentos que enajenen o graven bienes que no se identifiquen en los formularios que se detallan en esta solvencia, solamente pueden ser gravados y enajenados los bienes que se encuentran en los formularios identificados a excepci�n de los bienes declarados como litigiosos en Anexo 7. Se emite en uso de las atribuciones conferidas en el numeral 3 del art�culo N� 93 de la Resoluci�n N� 32 publicada en Gaceta Oficial N� 881 Extraordinario del 29-03-95.';
$pdf->MultiCell(0,5,$txt,1);

//----------------------
$pdf->Output();
?>