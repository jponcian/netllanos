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
		$this->SetY(-25);
		//Arial itálica 8
		$this->SetFont('Times','',7);
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
$pdf->MultiCell(210,5,utf8_decode($registro->direccion),1,'L');
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

$pdf->Ln(3);

$pdf->SetFont('Times','B',5.5);
$pdf->Cell(15,6,'TRIBUTO',1,0,'C',1);
$pdf->Cell(10,6,'SANCION',1,0,'C',1);
$pdf->Cell(120,6,'DESCRIPCION DEL HECHO PUNIBLE',1,0,'C',1);
$pdf->Cell(12,6,'C.O.T.',1,0,'C',1);
$pdf->Cell(23,6,'PERÍODO',1,0,'C',1);
$pdf->Cell(15,6,'U.T. APLI.',1,0,'C',1);
$pdf->Cell(15,6,'U.T. CONC.',1,0,'C',1);
$pdf->Cell(15,6,'VALOR U.T.',1,0,'C',1);
$pdf->Cell(20,6,'TOTAL Bs.',1,1,'C',1);

//--
	
$consulta_x = "SELECT anno_prov, num_prov, sector, tributo, sancion, descripcion, cot, periodo, ut_aplicada, ut_concurrencia, ut_actual, total FROM vista_hoja_sanciones_fis WHERE id_resolucion=0 AND num_prov=".$NUMERO." AND anno_prov=".$ANNO." AND sector=".$SEDE." AND origen_liquidacion=".$ORIGEN.";";
	
$tabla_x = mysql_query($consulta_x);
	
$Total =0;
	
while ($registro_xx = mysql_fetch_object($tabla_x))
{
	$pdf->SetFont('Times','',6);
	$pdf->Cell(15,6,$registro_xx->tributo,1,0,'C');
	$pdf->Cell(10,6,$registro_xx->sancion,1,0,'C');
	
	$pdf->SetFont('Times','',5);
	$txt = utf8_decode(substr($registro_xx->descripcion,0,115)).'_';
	$pdf->Text($pdf->GetX()+2, $pdf->GetY()+3, $txt);
	$txt = '  '.utf8_decode(substr($registro_xx->descripcion,115,120));
	$pdf->Text($pdf->GetX()+2, $pdf->GetY()+5, $txt);

	$pdf->Cell(120,6,'',1,0,'C');
	
	$pdf->SetFont('Times','',6);
	$pdf->Cell(12,6,$registro_xx->cot,1,0,'C');
	$pdf->Cell(23,6,$registro_xx->periodo,1,0,'C');

	$pdf->SetFont('Times','',6);
	$pdf->Cell(15,6,number_format($registro_xx->ut_aplicada,2,',','.'),1,0,'C');
	if ($registro_xx->ut_concurrencia<>$registro_xx->ut_aplicada)
	{
		$pdf->Cell(15,6,number_format($registro_xx->ut_concurrencia,2,',','.'),1,0,'C');
	} else {
		$pdf->Cell(15,6,'0,00',1,0,'C');
	}
	
	if ($registro_xx->ut_aplicada==0) 
		{
		$pdf->Cell(15,6,'0,00',1,0,'C');
		}
	else 
		{ 
		$pdf->Cell(15,6,number_format($registro_xx->ut_actual,2,',','.'),1,0,'C');
		}

	$pdf->Cell(20,6,number_format($registro_xx->total,2,',','.'),1,1,'C');
	//--
	$Total = $Total + ($registro_xx->total);
}
	
$pdf->SetFont('Times','B',7);
$pdf->Cell(225,6,'================================ TOTAL ================================>',1,0,'C',1);
$pdf->Cell(20,6,number_format(doubleval($Total),2,',','.'),1,1,'C');	

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