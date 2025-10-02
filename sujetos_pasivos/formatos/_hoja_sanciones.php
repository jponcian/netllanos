<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');
include('../../conexion.php');
mysql_query("SET NAMES 'utf8'");
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";

class PDF extends FPDF
{
	function Footer()
	{    
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',7);
		$this->Cell(245,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
	}	
}

// ENCABEZADO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->Ln(8);
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,10);

// CAPTURA DE VALORES
$NUMERO = $_GET['num'];
$ANNO = $_GET['anno'];
$SEDE = $_GET['sede'];
$ORIGEN = 2;

////////// DATOS DE LA PROVIDENCIA
$consulta = "SELECT DISTINCTROW FechaRegistro, NombreRazon, Rif, direccion, fecha_aprobacion, anno, numero, ci_fiscal, fiscal, ci_supervisor, supervisor, sector, nombre FROM vista_hoja_sanciones_esp WHERE anno=".$ANNO." AND numero=".$NUMERO." AND sector=".$SEDE.";";
$tabla = mysql_query($consulta);

$registro = mysql_fetch_object($tabla);

$pdf->AddPage();
$pdf->SetFont('Times','',12);
setlocale(LC_ALL, 'sp_ES','sp','es');

//////////
$pdf->Image('../../imagenes/logo.jpeg',20,15,55);
$pdf->SetFont('Times','B',15);
//////////
$pdf->SetFillColor(190);
$pdf->SetFont('Times','B',11);
$pdf->Cell(245,8,'TABLA DE CONFORMACIÓN DE SANCIONES',0,1,'C');
//--

$pdf->SetFont('Times','B',7);
$pdf->Cell(25,5,'RIF:',1,0,'C','true');
$pdf->SetFont('Times','',8);
$pdf->Cell(30,5,formato_rif($registro->Rif),1,0,'C');

$pdf->SetFont('Times','B',7);
$pdf->Cell(60,5,'CONTRIBUYENTE O RESPONSABLE:',1,0,'C','true');
$pdf->SetFont('Times','',8);
$pdf->Cell(130,5,utf8_decode($registro->NombreRazon),1,1,'C');
//--

$pdf->SetFont('Times','B',7);
$pdf->Cell(35,5,'DOMICILIO FISCAL:',1,0,'C','true');
$pdf->SetFont('Times','',8);
$pdf->Cell(210,5,utf8_decode($registro->direccion),1,1,'C');
//--

$pdf->SetFont('Times','B',7);
$pdf->Cell(50,5,'EXPEDIENTE N°:',1,0,'C','true');

////////// SIGLAS DE LA RESOLUCION
$consulta_x = "SELECT Siglas_resol_especiales FROM z_siglas WHERE id_sector=".$SEDE.";";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$SIGLAS=$registro_x->Siglas_resol_especiales;
// ---------------------
$NUM=$SIGLAS.$ANNO.'/'.sprintf("%005s",$NUMERO);
	
$pdf->SetFont('Times','',7);
$pdf->Cell(70,5,$NUM,1,0,'C');
//--

$pdf->SetFont('Times','B',7);
$pdf->Cell(30,5,'FECHA CREACION:',1,0,'C','true');

$pdf->SetFont('Times','',8);
$pdf->Cell(32,5,voltea_fecha($registro->FechaRegistro),1,0,'C');

$pdf->SetFont('Times','B',7);
$pdf->Cell(30,5,'FECHA IMPRESIÓN:',1,0,'C','true');

$pdf->SetFont('Times','',8);
$pdf->Cell(33,5,date("d-m-Y"),1,1,'C');
//--

//--------------------------------- CUADRO VDF

$consulta = "SELECT num_expediente, anno_expediente, origen_liquidacion FROM liquidacion WHERE id_resolucion=0 AND num_expediente=".$NUMERO." AND anno_expediente=".$ANNO." AND sector=".$SEDE." AND origen_liquidacion=".$ORIGEN.";";
$tabla_x = mysql_query($consulta);
$registro_x = mysql_fetch_object($tabla_x);

$pdf->Ln(3);

$pdf->SetFont('Times','B',6);
$pdf->Cell(15,6,'Tributo',1,0,'C',1);
$pdf->cell(10,6,'Sancion',1,0,'C',1);
$pdf->cell(80,6,'Descripcion del Hecho Punible',1,0,'C',1);
$pdf->cell(12,6,'C.O.T.',1,0,'C',1);
$pdf->cell(17,6,'Período',1,0,'C',1);
$pdf->cell(15,6,'Fecha Venc.',1,0,'C',1);
$pdf->cell(15,6,'Fecha Pago',1,0,'C',1);
$pdf->cell(15,6,'Cant. UT Apli.',1,0,'C',1);
$pdf->cell(15,6,'Cant. UT Conc.',1,0,'C',1);
$pdf->cell(15,6,'Valor UT Prim.',1,0,'C',1);
$pdf->cell(16,6,'Valor UT Apli.',1,0,'C',1);
$pdf->cell(20,6,'Total Bs.',1,1,'C',1);

//--
	
$consulta_x = "SELECT * FROM vista_hoja_sanciones_fis WHERE id_resolucion=0 AND num_prov=".$NUMERO." AND anno_prov=".$ANNO." AND sector=".$SEDE." AND origen_liquidacion=".$ORIGEN.";";
	
$tabla_x = mysql_query($consulta_x);
	
$Total =0;
	
while ($registro_xx = mysql_fetch_object($tabla_x))
{
	$pdf->SetFont('Times','',6);
	$pdf->Cell(15,6,$registro_xx->tributo,1,0,'C');
	$pdf->Cell(10,6,$registro_xx->sancion,1,0,'C');
	
	$pdf->SetFont('Times','',5);
	$txt = utf8_decode(substr($registro_xx->descripcion,0,75)).'_';
	$pdf->Text($pdf->GetX()+2, $pdf->GetY()+3, $txt);
	$txt = ' '.utf8_decode(substr($registro_xx->descripcion,75,150));
	$pdf->Text($pdf->GetX()+2, $pdf->GetY()+5, $txt);

	$pdf->Cell(80,6,'',1,0,'C');
	
	$pdf->SetFont('Times','',6);
	$pdf->Cell(12,6,$registro_xx->cot,1,0,'C');

	$txt = utf8_decode(substr($registro_xx->periodo,0,10)).' al ';
	$pdf->Text($pdf->GetX()+2, $pdf->GetY()+3, $txt);
	$txt = ' '.utf8_decode(substr($registro_xx->periodo,12,22));
	$pdf->Text($pdf->GetX()+2, $pdf->GetY()+5, $txt);

	$pdf->Cell(17,6,'',1,0,'C');
	//$pdf->Cell(23,6,$registro_xx->periodo,1,0,'C');

	$pdf->SetFont('Times','',6);
	$pdf->Cell(15,6,voltea_fecha($registro_xx->fecha_vencimiento),1,0,'C');
	$pdf->Cell(15,6,voltea_fecha($registro_xx->fecha_pago),1,0,'C');
	$pdf->Cell(15,6,number_format($registro_xx->ut_aplicada,2,',','.'),1,0,'C');
			
	if ($registro_xx->ut_concurrencia<>$registro_xx->ut_aplicada)
	{
		$pdf->Cell(15,6,number_format($registro_xx->ut_concurrencia,2,',','.'),1,0,'C');
	} else {
		$pdf->Cell(15,6,'0',1,0,'C');
	}
	
	$valorut = unidad_infraccion($registro_xx->fecha_vencimiento);
	$pdf->Cell(15,6,redondea($valorut),1,0,'C');
		
	if ($registro_xx->ut_aplicada==0) 
		{
		$pdf->Cell(16,6,'0',1,0,'C');
		}
	else 
		{ 
		$pdf->Cell(16,6,redondea($registro_xx->ut_actual,2,',','.'),1,0,'C');
		}

	$pdf->Cell(20,6,number_format($registro_xx->total,2,',','.'),1,1,'C');
	
	//--
	$Total = $Total + ($registro_xx->total);
}
	
$pdf->SetFont('Times','B',7);
$pdf->Cell(225,6,'================================ TOTAL ================================>',1,0,'C',1);
$pdf->Cell(20,6,number_format(doubleval($Total),2,',','.'),1,1,'C');	


//--------------------------------- ACTAS DE REPARO
/*
$consulta_xxx = "SELECT anno_prov, num_prov, anno, numero, tributo, reparo, impuesto_omitido, multa_actual, UT_actual, interes FROM vista_actas_hoja_sanciones_fis WHERE num_prov=".$NUMERO." AND anno_prov=".$ANNO." AND sector=".$SEDE.";";
$tabla_xxx = mysql_query($consulta_xxx);
if ($registro_xxx = mysql_fetch_object($tabla_xxx))
	{
	$pdf->Ln(3);
	$pdf->SetFont('Times','B',6.5);
	$pdf->Cell(15,6,'ACTA N°',1,0,'C',1);
	$pdf->Cell(25,6,'TRIBUTO',1,0,'C',1);
	$pdf->Cell(35,6,'REPARO',1,0,'C',1);
	$pdf->Cell(35,6,'IMP. OMITIDO',1,0,'C',1);
	$pdf->Cell(35,6,'MULTA Bs.',1,0,'C',1);
	$pdf->Cell(35,6,'INTERESES',1,0,'C',1);
	$pdf->Cell(25,6,'VALOR U.T.',1,0,'C',1);
	$pdf->Cell(40,6,'TOTAL Bs.',1,1,'C',1);
	
	//--

	$Total =0;
	$Multa =0;
	$Interes =0;

	while ($registro_xxx = mysql_fetch_object($tabla_xxx))
	{
		$pdf->SetFont('Times','',6);
		$pdf->Cell(15,6,$registro_xxx->numero,1,0,'C');
		$pdf->Cell(25,6,$registro_xxx->tributo,1,0,'C');
		$pdf->Cell(35,6,number_format(doubleval($registro_xxx->reparo),2,',','.'),1,0,'C');
		$pdf->Cell(35,6,number_format(doubleval($registro_xxx->impuesto_omitido),2,',','.'),1,0,'C');
		$pdf->Cell(35,6,number_format(doubleval($registro_xxx->multa_actual),2,',','.'),1,0,'C');
		$pdf->Cell(35,6,number_format(doubleval($registro_xxx->interes),2,',','.'),1,0,'C');
		$pdf->Cell(25,6,number_format(doubleval($_SESSION['VALOR_UT_ACTUAL']),2,',','.'),1,0,'C');
		$pdf->Cell(40,6,number_format(doubleval(($registro_xxx->multa_actual+$registro_xxx->interes)),2,',','.'),1,1,'C');
		//--
		$Multa = $Multa + $registro_x->multa_actual;
		$Interes = $Interes + $registro_x->interes;
		$Total = $Total + ($registro_x->multa_actual+$registro_x->interes);
	}
	

	$pdf->SetFont('Times','B',7);
	$pdf->Cell(110,6,'============== TOTAL ==============>',1,0,'C',1);
	$pdf->Cell(35,6,number_format(doubleval($Multa),2,',','.'),1,0,'C');
	$pdf->Cell(35,6,number_format(doubleval($Interes),2,',','.'),1,0,'C');
	$pdf->Cell(25,6,number_format(doubleval($_SESSION['VALOR_UT_ACTUAL']),2,',','.'),1,0,'C');
	$pdf->Cell(40,6,number_format(doubleval($Total),2,',','.'),1,1,'C');
	
}
*/
//---------------------------------
	
$pdf->Ln(3);

$pdf->SetFont('Times','B',7);
$pdf->Cell(30,10,'FISCAL ACTUANTE:',1,0,'C','true');

$pdf->SetFont('Times','',7);
$pdf->Cell(65,10,utf8_decode($registro->fiscal),1,0,'C');

$pdf->SetFont('Times','B',7);
$pdf->Cell(15,10,'C.I. N°:',1,0,'C','true');

$pdf->SetFont('Times','',7);
$pdf->Cell(15,10,number_format(doubleval($registro->ci_fiscal),0,',','.'),1,0,'C');

$pdf->SetFont('Times','B',7);
$pdf->Cell(15,10,'CÓDIGO:',1,0,'C','true');

$pdf->SetFont('Times','',6);
$pdf->Cell(15,10,'',1,0,'C');

$pdf->SetFont('Times','B',6);
$pdf->Cell(15,10,'FIRMA:',1,0,'C','true');

$pdf->SetFont('Times','',6);
$pdf->Cell(30,10,'',1,0,'C');

$pdf->SetFont('Times','B',6);
$pdf->Cell(15,10,'FECHA:',1,0,'C','true');

$pdf->SetFont('Times','',6);
$pdf->Cell(30,10,'',1,1,'C');

//-------------------

$pdf->SetFont('Times','B',7);
$pdf->Cell(30,10,'SUPERVISOR:',1,0,'C','true');

$pdf->SetFont('Times','',7);
$pdf->Cell(65,10,utf8_decode($registro->supervisor),1,0,'C');

$pdf->SetFont('Times','B',7);
$pdf->Cell(15,10,'C.I. N°:',1,0,'C','true');

$pdf->SetFont('Times','',7);
$pdf->Cell(15,10,number_format(doubleval($registro->ci_supervisor),0,',','.'),1,0,'C');

$pdf->SetFont('Times','B',7);
$pdf->Cell(15,10,'CÓDIGO:',1,0,'C','true');

$pdf->SetFont('Times','',6);
$pdf->Cell(15,10,'',1,0,'C');

$pdf->SetFont('Times','B',6);
$pdf->Cell(15,10,'FIRMA:',1,0,'C','true');

$pdf->SetFont('Times','',6);
$pdf->Cell(30,10,'',1,0,'C');

$pdf->SetFont('Times','B',6);
$pdf->Cell(15,10,'FECHA:',1,0,'C','true');

$pdf->SetFont('Times','',6);
$pdf->Cell(30,10,'',1,1,'C');

//-------------------

$pdf->SetFont('Times','B',7);
$pdf->Cell(45,6,'OBSERVACIONES:',1,0);
$pdf->Cell(200,10,'',1,1);

$pdf->Ln(3);
//--

// FINAL
$pdf->SetFont('Times','',5);
$pdf->Cell(245,4,'< LOS DATOS SEÑALADOS COMPROMETEN LA RESPONSABILIDAD DE QUIENES LA SUSCRIBEN >',1,0,'C');


// FIN DE LA VALIDACION DE LA CONSULTA

$pdf->Output();

?>