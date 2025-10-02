<?php
ob_end_clean();
session_start();
/*
$_SESSION['SEDE']=1;
$_SESSION['ORIGEN']=4;
$_SESSION['ANNO']=2013;
$_SESSION['NUMERO']=155;
*/
if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');
include('../../conexion.php');
include('../../funciones/auxiliar_php.php');
mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
{
	function Footer()
	{    //Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(360,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'C');
	}
	function Header()
	{
		////////// SIGLAS DE LA RESOLUCION
		$consulta_x = "SELECT siglas_fis_bol FROM z_siglas WHERE id_sector=0".$_SESSION['SEDE'];
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		//---------------
		$SIGLAS=$registro_x->siglas_fis_bol;
		// -------
				
		////////// DATOS DEL REQUERIMIENTO
		$consulta_x = "SELECT * FROM fis_boletas WHERE sector=".$_SESSION['SEDE']." AND origen=".$_SESSION['ORIGEN']." AND anno=".$_SESSION['ANNO']." AND numero=".$_SESSION['NUMERO']."";
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_array($tabla_x);
		$RESOLUCION = $SIGLAS . "/". $registro_x['anno'] . '/' . sprintf("%005s", $registro_x['numero']);
		$FECHA=$registro_x['fecha'];
		// ---------------------
				
		//Select Arial bold 15
		$this->SetFont('Arial','B',15);
		//Move to the right
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		$this->SetFont('Times','B',11);
		$this->Cell(0,5,'N°    '.$RESOLUCION);
		$this->Ln(10);
		//Line break
	}		
}

include "../../funciones/numerosALetras.class.php";

////////// DATOS DE LA BOLETA
$consulta_x = "SELECT * FROM fis_boletas WHERE sector=".$_SESSION['SEDE']." AND origen=".$_SESSION['ORIGEN']." AND anno=".$_SESSION['ANNO']." AND numero=".$_SESSION['NUMERO']."";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);
// ---------
$rif = $registro_x['rif'];
$ced_funcionario = $registro_x['funcionario'];
$fecha = $registro_x['fecha'];
$fecha_com = $registro_x['fecha_com'];
$hora = $registro_x['hora'];
$representante = $registro_x['representante'];
$cedular = $registro_x['cedula'];
$cargor = $registro_x['cargo'];
$sector = $registro_x['sector'];
// ---------------

if ($sector==2){
$adscripcions=" Sector San Juan de los Morros";}
elseif($sector==3){
$adscripcions=" Sector San Fernando de Apure";}
elseif($sector==4){
$adscripcions=" Unidad Altagracia de Orituco";}
elseif($sector==5){
$adscripcions="  Sector Valle de la Pascua";}

////////// REGION DE EMISION
$consulta_x = "SELECT * FROM z_region;";
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$region=$registro_x->nombre;
// ---------------------

if ($ced_funcionario==$registro_x->ci_gerente)
	{
	//--- GERENTE
	$gerente = 'SI';
	//---------------------------------
	$jefe = $registro_x->gerente;
	$cedula = "C.I. N° V-" .$registro_x->ci_gerente;
	$cargo = $registro_x->cargo;
	$providencia = $registro_x->providencia;
	$fecha_prov = $registro_x->fecha_prov;
	$gaceta = $registro_x->gaceta;
	$fecha_gac = $registro_x->fecha_gaceta;
	$region=$registro_x->nombre;
	//---------------------------------
	}
else
	{
	// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR
	$consulta_x = "SELECT * FROM vista_jefe_fis WHERE id_sector=".$_SESSION['SEDE'].";";
	$tabla_x = mysql_query ( $consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//---------------------------------
	$jefe = $registro_x->jefe;
	$cedula = "C.I. N° V-" .$registro_x->cedula;
	$cargo = $registro_x->cargo;
	$providencia = $registro_x->providencia;
	$fecha_prov = $registro_x->fecha_prov;
	$gaceta = $registro_x->gaceta;
	$fecha_gac = $registro_x->fecha_gaceta;
	$division_sector = $registro_x->descripcion;
	//---------------------------------
	}

// BUSQUEDA DEL CONTRIBUYENTE
$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='".$rif."';";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
//////////
$rif = $registro_x->rif;
$contribuyente = $registro_x->contribuyente;
$direccion1= $registro_x->direccion;
// -------

////////// SIGLAS DE LA RESOLUCION
$consulta_x = "SELECT siglas_fis_bol FROM z_siglas WHERE id_sector=0".$_SESSION['SEDE'];
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
//---------------
$SIGLAS=$registro_x->siglas_fis_bol;
// -------
	
////////// FECHA DE LA BOLETA
list($anno,$mes,$dia)=explode('-',$fecha);
$FECHA=mktime(0,0,0,$mes,$dia,$anno);
$_SESSION['VARIABLE']=$FECHA;
// -------

////////// CIUDAD DE EMISION
$consulta_x = "SELECT nombre, direccion_fiscalizacion_sola FROM z_sectores WHERE id_sector=0".$_SESSION['SEDE'];
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
// ---------
$Ciudad = $registro_x->nombre;
$direccion = $registro_x->direccion_fiscalizacion_sola;
//////////

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,10);

$pdf->AddPage();

$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

$t=(140-(strlen($Ciudad)));

$mes=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);

$pdf->Text($t,24,$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$FECHA)))-1)].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))));
$pdf->Ln(2);
	
$pdf->SetFont('Times','B',13);
$pdf->Cell(0,5,'BOLETA  DE  COMPARECENCIA',0,0,'C'); $pdf->Ln(12);
$pdf->SetFont('Times','',12);

$pdf->Cell(0,5,'Contribuyente o Responsable:');

$pdf->SetFont('Times','B',12);
$pdf->SetX(75);
$pdf->MultiCell(0,5,strtoupper($contribuyente));
$pdf->Ln(3); 

$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,'RIF N°:'); 

$pdf->SetFont('Times','B',12);
$pdf->SetX(75);
$pdf->Cell(0,5,strtoupper(substr($rif,0,1)).'-'.substr($rif,1,8).'-'.substr($rif,9,1));
$pdf->Ln(8); 

$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,'Domicilio Fiscal:'); 

$pdf->SetFont('Times','B',11);
$pdf->SetX(75);
$pdf->MultiCell(0,5,strtoupper(trim($direccion1)));

// FIN

$pdf->SetFont('Times','',11);
$pdf->Ln(8);
		
if (strtoupper(substr($rif,0,1)) == 'V')
	{
	$txt='Sírvase comparecer el día '.'____/____/________'.', hora '.'____:____ ____'.', '.$Adscripcion.' sede la Gerencia Regional de Tributos Internos de la  ' .buscar_region . '  del  Servicio  Nacional  Integrado  de  Administración  Aduanera y Tributaria (SENIAT), adscrito al Ministerio del Poder Popular Para la Banca y Finanzas, Gerencia, ubicada en: '.$direccion.', donde será atendido por el funcionario (a): '.strtoupper($jefe).' ('.$cargo.'), de acuerdo a lo señalado en el numeral 4 del artículo 127 del Código Orgánico Tributario en concordancia con el artículo 29 de la Ley de Procedimientos Administrativos, a fin de tratar asunto de su interés.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	}
else
	{
	if (substr($fecha_com,0,1)<>2)
		{	$fechac = '____/____/________';		$hora = '____:____ ____';	}
	else
		{	$fechac = substr($fecha_com,8,2).'/'.substr($fecha_com,5,2).'/'.substr($fecha_com,0,4);		}
	//------------
	$txt= 'Al ciudadano (a) '.$representante.', titular de la Cédula de Identidad Nº '.$cedular.', en su carácter de '.$cargor.', del sujeto pasivo ya citado, sírvase comparecer el día '.$fechac.', hora '.$hora.' en (el/la)'.$adscripcions.' de la Gerencia Regional de Tributos Internos de la ' .buscar_region(). '  del  Servicio  Nacional  Integrado  de  Administración  Aduanera y Tributaria (SENIAT), adscrito al Ministerio del Poder Popular Para la Banca y Finanzas, Gerencia, ubicada en: '.$direccion.', donde será atendido por el funcionario (a): '.strtoupper($jefe).' ('.$cargo.'), de acuerdo a lo señalado en el artículo 131, 155 numeral 7 y 159 del Código Orgánico Tributario, 182, publicado en la Gaceta Oficial N° 6.507 de fecha 29 de enero del 2.020 en concordancia con los artículos 25 y 26 de la Ley Orgánica de Procedimientos Administrativos, a fin de tratar asunto de su interés.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	}

$txt='Se hace del conocimiento del contribuyente la obligación de dar cumplimiento a la presente notificación, la no comparecencia generara la sanción establecida en el artículo 105 del Código Orgánico Tributario vigente.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

if ($_POST[LINEA]==1)
	{$pdf->AddPage();$pdf->Ln(10);}
	
$txt='Y para que así conste a los fines legales consiguientes, se levanta la presente por duplicado (02) de un mismo tenor y a un solo efecto, UNO (01) de cuyos ejemplares queda en poder del sujeto pasivo, que firma en señal de notificación.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

if ($_POST[LINEA]==1)
	{$pdf->Ln(10);}

// FIRMA DEL JEFE DE DIVISION O GERENTE
$pdf->Ln(8);
$pdf->SetFont('Times','B',$tamañoletra);
$pdf->Cell(0,5,$jefe,0,0,'C'); $pdf->Ln(5);
$pdf->SetFont('Times','',9);
$pdf->Cell(0,4,$cargo,0,0,'C'); $pdf->Ln(4);
$pdf->Cell(0,4,$region,0,0,'C'); $pdf->Ln(4);
$pdf->SetRightMargin(65);
$pdf->SetLeftMargin(65);
$pdf->SetFont('Times','',8);
$pdf->MultiCell(0,4,$providencia,0,'C');
$pdf->MultiCell(0,4,$gaceta,0,'C');
// FIN

$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
$pdf->SetFont('Times','',9);
$pdf->Ln(5);

if ($_POST[LINEA]==1)
	{$pdf->Ln(10);}
	
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,5,'Notificación'); $pdf->Ln(5);
$pdf->SetFont('Times','',8);
$pdf->Cell(0,5,'Firma:       __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Nombre:   __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'C.I. N°:     __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Cargo:       __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Teléfono:  __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Sello:        __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Fecha:        __________________________________'); $pdf->Ln(6);

$pdf->Output();

?>