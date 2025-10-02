<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');
include('../../conexion.php');
mysql_query("SET NAMES 'latin1'");
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";

class PDF extends FPDF
{
	function Footer()
	{    
		$this->SetY(-25);
		//Arial itálica 8
		$this->SetFont('Times','',7);
		$this->Cell(245,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
	}	
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->Ln(8);
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,10);

// CAPTURA DE VALORES
$NUMERO = $_GET['num'];
$ANNO = $_GET['anno'];
$SEDE = $_GET['sede'];
$ORIGEN = 4;

////////// DATOS DE LA PROVIDENCIA
$consulta = "SELECT DISTINCTROW FechaNotificacion, TipoAutorizacion, Descripcion, Siglas1, Siglas2, NombreRazon, Rif, direccion, FechaEmision, FechaConclusion, anno, numero, ci_fiscal, fiscal, ci_supervisor, supervisor, Periodos, sector FROM vista_portada_fiscalizacion WHERE anno=".$ANNO." AND numero=".$NUMERO." AND sector=".$SEDE.";";
$tabla = mysql_query($consulta);

if ($registro = mysql_fetch_object($tabla))
{

$pdf->AddPage();
$pdf->SetFont('Times','',12);
setlocale(LC_ALL, 'sp_ES','sp','es');
$pdf->SetFillColor(190);

// IMAGEN
$pdf->Image('../../imagenes/logo.jpeg',18,41,60);
//////////

$pdf->SetFont('Times','B',9);

$pdf->Ln(3);
$pdf->MultiCell(180,7,'N° '.$ANNO.'-'.$NUMERO.' - '.formato_rif($registro->Rif).' - '.utf8_decode($registro->NombreRazon),1,'R');
$pdf->Ln(35);

// TITULO
$pdf->Cell(0,0,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS',0,0,'C',0);
$pdf->Ln(4);
$pdf->Cell(0,0,strtoupper(utf8_decode(buscar_region())),0,0,'C',0);
$pdf->Ln(4);
$pdf->Cell(0,0,'EXPEDIENTE DE FISCALIZACIÓN',0,0,'C',0);
$pdf->Ln(10);

$pdf->SetFont('Times','',7.5);

// CONTENIDO
$pdf->Cell(40,5,'NOMBRE DEL PROGRAMA:   '.$registro->TipoAutorizacion.'    '.utf8_decode($registro->Descripcion),0,0,'L',0);
$pdf->Cell(80,5,'',0,0,'C',0);
$pdf->Cell(30,5,'NÚMERO DE RIF:  '.formato_rif($registro->Rif),0,0,'C',0);
$pdf->Cell(30,5,'',0,0,'C',0);
$pdf->Ln(7);

$pdf->Cell(40,5,'ZONA ASIGNADA:  _______________________________________',0,0,'L',0);
$pdf->Cell(60,5,'',0,0,'C',0);
$pdf->Cell(55,5,'PROVIDENCIA N°:  '.$NUMERO.'  EMISIÓN: '.date("d-m-Y",strtotime($registro->FechaEmision)).'  NOTIFICACIÓN: '.date("d-m-Y",strtotime($registro->FechaNotificacion)),0,0,'C',0);
$pdf->Cell(25,5,'',0,0,'C',0);
$pdf->Ln(7);

$pdf->Cell(50,5,'FECHA DE FISCALIZACIÓN O VERIFICACION: DESDE EL '.date("d-m-Y",strtotime($registro->FechaNotificacion)).' HASTA EL '.date("d-m-Y",strtotime($registro->FechaConclusion)),0,0,'L',0);
$pdf->Cell(130,5,'',0,0,'L',0);
$pdf->Ln(7);

$pdf->Cell(40,5,'REPARO:',0,0,'L',0);
$consulta_xxx ="SELECT numero FROM fis_actas WHERE anno_prov=".$ANNO." AND num_prov=".$NUMERO." AND id_sector=".$SEDE.";";
$tabla_xxx = mysql_query($consulta_xxx);

if ($registro_prov = mysql_fetch_object($tabla_xxx))
	{$pdf->Cell(80,5,'SI [X]   NO [ ]',0,0,'L',0);}
else
	{$pdf->Cell(80,5,'SI [ ]   NO [X]',0,0,'L',0);}

$pdf->Cell(30,5,'MULTA:',0,0,'C',0);
$consulta_xxx = "SELECT multa FROM vista_resumen_multa_vdf_reporte WHERE anno_prov=".$ANNO." AND num_prov=".$NUMERO." AND sector=".$SEDE.";";
$tabla_xxx = mysql_query($consulta_xxx);

if ($registro_prov = mysql_fetch_object($tabla_xxx))
	{$pdf->Cell(30,5,'SI [X]   NO [ ]',0,0,'L',0);}
else
	{$pdf->Cell(30,5,'SI [ ]   NO [X]',0,0,'L',0);}

$pdf->Ln(7);

//*******BUSCAR TIPO DE NORMAS
$cadena = " ".str_replace("/"," ",$registro->Siglas1);
$striva = "IVA";
$resultadoiva = strpos($cadena, $striva);

if($resultadoiva == true){
	$cadenaiva = "NORMAS DEL IMPUESTO AL VALOR AGREGADO";
} else {
	$cadenaiva = "";
}

$cadena1 = " ".str_replace("/"," ",$registro->Siglas1);
$strislr = "ISLR";
$resultadoislr = strpos($cadena1, $strislr);

if($resultadoislr == true){
	$cadenaislr = "NORMAS DEL IMPUESTO SOBRE LA RENTA";
} else {
	$cadenaislr = "";
}

$cadena2 = " ".str_replace("/"," ",$registro->Siglas1);
$strsuc = "ISDRC";
$resultadosuc = strpos($cadena2, $strsuc);

if($resultadosuc == true){
	$cadenasuc = "NORMAS DEL IMPUESTO SOBRE SUCESIONES, DONACIONES Y DEMAS RAMOS CONEXOS";
} else {
	$cadenasuc = "";
}

if ($cadenaiva<>"" and $cadenaislr=="" and $cadenasuc=="")
{
	$cadenabaselegal = $cadenaiva;
} 
else if ($cadenaiva<>"" and $cadenaislr<>"" and $cadenasuc=="")
{
	$cadenabaselegal = $cadenaiva." Y ".$cadenaislr;
} 
else if ($cadenaiva=="" and $cadenaislr<>"" and $cadenasuc=="")
{
	$cadenabaselegal = $cadenaislr;
} else if ($cadenaiva=="" and $cadenaislr=="" and $cadenasuc<>"")
{
	$cadenabaselegal = $cadenasuc;
}
//****************************

$pdf->Cell(30,5,'BASE LEGAL:',0,0,'L',0);
$pdf->Cell(150,5,$cadenabaselegal,0,0,'L',0);
$pdf->Ln(7);

$pdf->Cell(40,5,'TIPO DE TRIBUTO:',0,0,'L',0);
$pdf->Cell(140,5,str_replace("/"," ",$registro->Siglas1),0,0,'L',0);
$pdf->Ln(7);

$pdf->Cell(60,5,'EJERCICIOS Y/O PERIODOS INVESTIGADOS:',0,0,'L',0);
$pdf->Cell(120,5,$registro->Periodos,0,0,'L',0);
$pdf->Ln(7);

$pdf->Cell(30,5,'PRESCRIPCION: _____/_____/_______   NOMBRE DEL FISCAL:  '.utf8_decode($registro->fiscal).'     FIRMA DEL FISCAL: ___________________',0,0,'L',0);
$pdf->Cell(70,5,'',0,0,'C',0);
$pdf->Ln(7);

$pdf->Cell(60,5,'NOMBRE DEL SUPERVISOR DEL FISCAL:  '.utf8_decode($registro->supervisor),0,0,'L',0);
$pdf->Cell(80,5,'',0,0,'C',0);
$pdf->Cell(20,5,'N° DE GRUPO:',0,0,'C',0);
$pdf->Cell(15,5,'_________',0,0,'C',0);
$pdf->Ln(7);

$pdf->Cell(45,5,'N° DE FOLIO DESDE:',0,0,'L',0);
$pdf->Cell(55,5,'________________________________',0,0,'C',0);
$pdf->Cell(25,5,'HASTA:',0,0,'C',0);
$pdf->Cell(55,5,'________________________________',0,0,'C',0);
$pdf->Ln(7);

$pdf->Cell(180,5,'FECHA DE RECEPCION DEL EXPEDIENTE:',0,0,'L',0);
$pdf->Ln(7);

$pdf->Cell(40,5,'SUPERVISOR DEL FISCAL:',0,0,'L',0);
$pdf->Cell(30,5,'_____/_____/_________',0,0,'C',0);
$pdf->Cell(40,5,'FIRMA DEL SUPERVISOR:',0,0,'C',0);
$pdf->Cell(70,5,'___________________________________________',0,0,'C',0);
$pdf->Ln(7);

$pdf->Cell(40,5,'DIVISIÓN DE FISCALIZACIÓN:',0,0,'C',0);
$pdf->Cell(30,5,'_____/_____/_________',0,0,'C',0);
$pdf->Cell(40,5,'FIRMA COORDINADOR:',0,0,'C',0);
$pdf->Cell(70,5,'___________________________________________',0,0,'C',0);
$pdf->Ln(7);

$pdf->Cell(40,5,'SUMARIO ADMINISTRATIVO:',0,0,'C',0);
$pdf->Cell(30,5,'_____/_____/_________',0,0,'C',0);
$pdf->Cell(40,5,'FIRMA JEFE DE DIVISIÓN:',0,0,'C',0);
$pdf->Cell(70,5,'___________________________________________',0,0,'C',0);
$pdf->Ln(7);

// CUADRO VDF
$pdf->Ln(3);

$pdf->SetFont('Times','B',7);
$pdf->Cell(45,6,'TIPO DE IMPUESTO:',1,0,'C',1);
$pdf->Cell(50,6,'MULTAS BS.',1,0,'C',1);
$pdf->Cell(35,6,'CANT. U.T. APLICADAS:',1,0,'C',1);
$pdf->Cell(50,6,'INTERÉS:',1,0,'C',1);
$pdf->Ln(6);
//--

$consulta = "SELECT multa, ut, siglas FROM vista_resumen_multa_vdf_reporte WHERE anno_prov=".$ANNO." AND num_prov=".$NUMERO." AND sector=".$SEDE.";";
$tabla_x = mysql_query($consulta);

$consulta2 = "SELECT intereses FROM vista_resumen_interes_vdf_reporte WHERE anno_prov=".$ANNO." AND num_prov=".$NUMERO." AND sector=".$SEDE.";";
$tabla_x2 = mysql_query($consulta2);

$Monto =0 ;
$Ut_Aplicadas =0 ;
$Valor_Ut =0 ;
$i=0;

while ($registro_x = mysql_fetch_object($tabla_x))
	{
	//REGISTRO PARA EL INTERÉS
	$registro_x2 = mysql_fetch_object($tabla_x2);
	//--------------------
	$pdf->SetFont('Times','',7);
	$pdf->Cell(45,5.5,$registro_x->siglas,1,0,'C');
	$pdf->Cell(50,5.5,number_format(doubleval($registro_x->multa),2,',','.'),1,0,'C');
	$pdf->Cell(35,5.5,number_format(doubleval($registro_x->ut),2,',','.'),1,0,'C');
	$pdf->Cell(50,5.5,number_format(doubleval($registro_x2->intereses),2,',','.'),1,0,'C');
	$pdf->Ln(5.5);
	//--
	$Monto = $Monto + ($registro_x->multa);
	$Monto2 = $Monto2 + $registro_x2->intereses;
	$Ut_Aplicadas = $Ut_Aplicadas + ($registro_x->ut);
	$Valor_Ut = $Valor_Ut + (($registro_x->ut));
	$i=1;
	}
	
	if ($i==0) // PARA AGREGAR UNA LINEA EN BLANCO SI NO EXISTEN REGISTROS
	{
		$pdf->Cell(45,5.5,'',1,0,'C');
		$pdf->Cell(50,5.5,'',1,0,'C');
		$pdf->Cell(35,5.5,'',1,0,'C');
		$pdf->Cell(50,5.5,'',1,0,'C');
		$pdf->Ln(5.5);
	}	

$pdf->SetFont('Times','B',7);
$pdf->Cell(45,6,'TOTAL:',1,0,'C',1);
$pdf->Cell(50,6,number_format(doubleval($Monto),2,',','.'),1,0,'C');
$pdf->Cell(35,6,number_format(doubleval($Ut_Aplicadas),2,',','.'),1,0,'C');
$pdf->Cell(50,6,number_format(doubleval($Monto2),2,',','.'),1,0,'C');
$pdf->Ln(6);
//--

// CUADRO ACTAS DE REPARO
$pdf->Ln(3);

$pdf->SetFont('Times','B',7);
$pdf->Cell(30,6,'TIPO DE IMPUESTO:',1,0,'C',1);
$pdf->Cell(30,6,'ACTA DE REPARO N°',1,0,'C',1);
$pdf->Cell(30,6,'TRIBUTO OMITIDO Bs.',1,0,'C',1);
$pdf->Cell(30,6,'MULTA Bs.',1,0,'C',1);
$pdf->Cell(30,6,'INT. MORATORIOS Bs.',1,0,'C',1);
$pdf->Cell(30,6,'PAGOS Bs.',1,0,'C',1);
$pdf->Ln(6);
//--

// -------- PARA VALIDAR SI HAY PAGO EN EL ACTA DE REPARO
$consulta ="SELECT impuesto_omitido, multa_actual, interes, tributo, siglas, impuesto_pagado, monto_pagado, anno, numero FROM vista_resumen_actas WHERE anno_prov=".$ANNO." AND num_prov=".$NUMERO." AND sector=".$SEDE.";"; 
//echo $consulta;
$tabla_x = mysql_query($consulta);

$Tri_Omi =0 ;
$Mul =0 ;
$Int_Mor =0 ;
$Pag_Vol =0 ;
$i=0;
	
while ($registro_x = mysql_fetch_object($tabla_x))
{
	$pdf->SetFont('Times','',7);
	$pdf->Cell(30,5.5,$registro_x->siglas,1,0,'C');
	$pdf->Cell(30,5.5,$registro_x->numero,1,0,'C');
	$pdf->Cell(30,5.5,number_format(doubleval($registro_x->impuesto_omitido),2,',','.'),1,0,'C');
	$pdf->Cell(30,5.5,number_format(doubleval($registro_x->multa_actual),2,',','.'),1,0,'C');
	$pdf->Cell(30,5.5,number_format(doubleval($registro_x->interes),2,',','.'),1,0,'C');
	$pdf->Cell(30,5.5,number_format(doubleval($registro_x->monto_pagado),2,',','.'),1,0,'C');
	$pdf->Ln(5.5);
	//--
	$Tri_Omi = $Tri_Omi + $registro_x->impuesto_omitido;
	$Mul = $Mul + $registro_x->multa_actual; 
	$Int_Mor = $Int_Mor + $registro_x->interes;
	$Pag_Vol = $Pag_Vol + $registro_x->monto_pagado;
	$i=1;
}
if ($i==0) // PARA AGREGAR UNA LINEA EN BLANCO SI NO EXISTEN REGISTROS
	{
	$pdf->SetFont('Times','',7);
	$pdf->Cell(30,5.5,'',1,0,'C');
	$pdf->Cell(30,5.5,'',1,0,'C');
	$pdf->Cell(30,5.5,'',1,0,'C');
	$pdf->Cell(30,5.5,'',1,0,'C');
	$pdf->Cell(30,5.5,'',1,0,'C');
	$pdf->Cell(30,5.5,'',1,0,'C');
	$pdf->Ln(5.5);
	}
	
$pdf->SetFont('Times','B',7);
$pdf->Cell(60,6,'TOTAL:',1,0,'C',1);
$pdf->Cell(30,6,number_format(doubleval($Tri_Omi),2,',','.'),1,0,'C');
$pdf->Cell(30,6,number_format(doubleval($Mul),2,',','.'),1,0,'C');
$pdf->Cell(30,6,number_format(doubleval($Int_Mor),2,',','.'),1,0,'C');
$pdf->Cell(30,6,number_format(doubleval($Pag_Vol),2,',','.'),1,0,'C');
$pdf->Ln(12);
//--

$pdf->Cell(180,5,'OBSERVACIONES:',0,0,'L',0);
$pdf->Ln(5);

$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Cell(160,6,'',1,0,'L',0);
$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Ln(6);
$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Cell(160,6,'',1,0,'L',0);
$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Ln(6);
/*$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Cell(160,6,'',1,0,'L',0);
$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Ln(6);
$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Cell(160,6,'',1,0,'L',0);
$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Ln(6);
$pdf->Cell(10,6,'',0,0,'L',0);
$pdf->Cell(160,6,'',1,0,'L',0);
$pdf->Cell(10,6,'',0,0,'L',0);
*/

// CUADRO 
$pdf->SetY(20);
$pdf->SetX(17);
$pdf->SetFont('Times','',7);
$pdf->Cell(180,230,'',1,0,'C',0);
// ------	

}
// FIN DE LA VALIDACION DE LA CONSULTA

$pdf->Output();

?>