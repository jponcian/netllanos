<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');
include('../../conexion.php');
include('../../funciones/auxiliar_php.php');

class PDF extends FPDF
{
	function Footer()
	{    //Posici�n a 1,5 cm del final
		$this->SetY(-15);
		//Arial it�lica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//N�mero de p�gina
		$this->Cell(360,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'C');
	}
	function Header()
	{
		////////// SIGLAS DE LA RESOLUCION
		$consulta_x = "SELECT siglas_fis_req FROM z_siglas WHERE id_sector=0".$_SESSION['SEDE'];
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		//---------------
		$SIGLAS=$registro_x->siglas_fis_req;
		// -------
		
		////////// DATOS DEL REQUERIMIENTO
		$RESOLUCION = $SIGLAS . "/". $_SESSION['ANNO'] . '/' . sprintf("%005s", $_SESSION['NUMERO']);
		// ---------------------
				
		//Select Arial bold 15
		$this->SetFont('Arial','B',15);
		//Move to the right
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		$this->SetFont('Times','B',11);
		$this->Cell(0,5,'N�    '.$RESOLUCION);
		$this->Ln(10);
		//Line break
	}		
}

include "../../funciones/numerosALetras.class.php";

////////// DATOS DEL REQUERIMIENTO
$consulta_x = "SELECT * FROM fis_requerimientos WHERE sector=".$_SESSION['SEDE']." AND origen=".$_SESSION['ORIGEN']." AND anno=".$_SESSION['ANNO']." AND numero=".$_SESSION['NUMERO']."";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);
// ---------
$rif = $registro_x['rif'];
$ced_supervisor = $registro_x['supervisor'];
$ced_fiscal = $registro_x['fiscal'];
$fecha = $registro_x['fecha'];
$texto = $registro_x['texto'];
$id = $registro_x['id_req'];
$sector = $registro_x['sector'];
// ---------------

// ------- CONTRIBUYENTE
// BUSQUEDA DEL CONTRIBUYENTE
$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='".$rif."';";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
//////////
$rif = $registro_x->rif;
$contribuyente = $registro_x->contribuyente;
$direccion= $registro_x->direccion;
// -------

// ------- SUPERVISOR
$consulta_x = "SELECT * FROM z_empleados WHERE cedula='".$ced_supervisor."';";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
// FIN
$supervisor = $registro_x->Nombres .' '. $registro_x->Apellidos;
// -------

// ------- FISCAL
$consulta_x = "SELECT * FROM z_empleados WHERE cedula='".$ced_fiscal."';";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
// FIN
$fiscal = $registro_x->Nombres .' '. $registro_x->Apellidos;
// -------

////////// CIUDAD DE EMISION
$consulta_x = "SELECT * FROM z_sectores WHERE id_sector=0".$sector;
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);
// ---------
$Ciudad = $registro_x['nombre'];
$adscripcion = $registro_x['adscripcion'];
// ---------------
	
////////// FECHA DEL REQUERIMIENTO
list($anno,$mes,$dia)=explode('-',$fecha);
$FECHA=mktime(0,0,0,$mes,$dia,$anno);
$_SESSION['VARIABLE']=$FECHA;
// -------

// ENCABEZADO
$pdf=new PDF('P','mm','LEGAL');
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
$pdf->Cell(0,5,'REQUERIMIENTO DE INFORMACION',0,0,'C'); $pdf->Ln(12);
$pdf->SetFont('Times','',12);

$pdf->Cell(0,5,'Contribuyente o Responsable:');

$pdf->SetFont('Times','B',12);
$pdf->SetX(75);
$pdf->MultiCell(0,5,strtoupper($contribuyente));
$pdf->Ln(3); 

$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,'RIF N�:'); 

$pdf->SetFont('Times','B',12);
$pdf->SetX(75);
$pdf->Cell(0,5,strtoupper(substr($rif,0,1)).'-'.substr($rif,1,8).'-'.substr($rif,9,1));
$pdf->Ln(8); 

$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,'Domicilio Fiscal:'); 

$pdf->SetFont('Times','B',11);
$pdf->SetX(75);
$pdf->MultiCell(0,5,strtoupper(trim($direccion)));

// FIN

$pdf->SetFont('Times','',11);
$pdf->Ln(6);

$txt='El Servicio Nacional Integrado de Administraci�n Aduanera y Tributaria (SENIAT), de conformidad con lo dispuesto en el Articulo 131, numerales 1, 2 y 3, Art�culos 182 y 184 del C�digo Org�nico Tributario y Articulo 4 numerales 7, 8, 9 y 44 de la Ley del Servicio Nacional Integrado de Administraci�n Aduanera y Tributaria, en concordancia con el articulo 97, numerales 2, 3, 12 y 16 y articulo 98 de la resoluci�n N� 32 de fecha 24/03/95, publicada en Gaceta Oficial N� 4881 Extraordinario de fecha 29/03/95, Autoriza a los funcionarios actuantes '.$fiscal.' y '.$supervisor.' titulares de la c�dula de identidad N� '.$ced_fiscal.' y '.$ced_supervisor.', con los cargos de fiscal actuante y supervisor, adscritos '.utf8_decode($adscripcion).' de la Gerencia  de Tributos Internos de la ' .utf8_decode(buscar_region()). ' del Servicio Nacional integrado de Administraci�n Aduanera y Tributaria (SENIAT), para que por medio del presente proceda a requerir los documentos que se especifican a continuaci�n:';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

// REQUERIMIENTOS
$consulta_x = "SELECT * FROM fis_requerimientos_det WHERE id_req=".$id;
$tabla_x = mysql_query($consulta_x);
while ($registro_x = mysql_fetch_array($tabla_x))
{
	$pdf->SetRightMargin(25);
	$pdf->SetLeftMargin(25);
	// ---------
	$txt= utf8_decode($registro_x['texto']);
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	// ---------
}
// -------------
$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
$pdf->MultiCell(0,0,'');

//$pdf->SetFont('Times','B',11);
//$txt='NOTA: de poseer la presente informaci�n en digital entregar la misma al funcionario actuante en CD.';
//$pdf->MultiCell(0,5,$txt);
//$pdf->Ln(4); 

$pdf->SetFont('Times','',11);
//$txt='La presente documentaci�n debe ser entregada en un lapso de tres (03) d�as h�biles contados a partir de la notificaci�n de la presente. Todo a tenor de lo dispuesto en el art�culo 137  numeral 1 del C�digo Org�nico Tributario.';
$txt='La presente documentaci�n debe ser entregada en un lapso de                        (       ) d�as h�biles contados a partir de la notificaci�n de la presente. Todo a tenor de lo dispuesto en el art�culo 137  numeral 1 del C�digo Org�nico Tributario.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

$txt='Se hace del conocimiento del Sujeto Pasivo, la obligaci�n de dar cumplimiento a lo requerido de conformidad con los art�culos 23, 155 y 156 del C�digo Org�nico Tributario, cuya inobservancia se considera incumplimiento a los Deberes Formales, lo cual genera sanciones de acuerdo al  C�digo Org�nico Tributario.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

$txt='Igualmente se notifica al Sujeto Pasivo que de no cumplir con el presente requerimiento el fiscal actuante podr� intervenir los libros, documentos y bienes, tomando las medidas de seguridad para su conservaci�n, incautarlos y solicitar auxilio de la fuerza p�blica cuando la gravedad del caso lo requiera de conformidad con el numeral, 7, 13 y 14 del art�culo 137 y 138 del C�digo Org�nico Tributario Vigente. Por otra parte el suministro de la informaci�n objeto del presente requerimiento se reputar� como documento leg�timamente v�lido y el contenido de la misma  gozar� de veracidad, caso contrario se presumir� la intenci�n de defraudar al Fisco Nacional de conformidad con el Art�culo 120 del C�digo Org�nico Tributario.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

if ($_POST[LINEA]==1)
	{$pdf->AddPage();$pdf->Ln(10);}
	
$txt='Y para que as� conste a los fines legales consiguientes, se levanta la presente por duplicado (02) de un mismo tenor y a un solo efecto, UNO (01) de cuyos ejemplares queda en poder del sujeto pasivo, que firma en se�al de notificaci�n.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

if ($_POST[LINEA]==1)
	{$pdf->Ln(10);}
	
// FIRMA DEL JEFE DE DIVISION O GERENTE
if ($_POST['GERENTE']==1)
{
// BUSQUEDA DEL GERENTE
$consulta_x = "SELECT * FROM z_region;";
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$jefe = $registro_x->gerente;
$cedula = "C.I. N� V-" .$registro_x->ci_gerente;
$cargo = $registro_x->cargo;
$providencia = $registro_x->providencia;
$fecha_prov = $registro_x->fecha_prov;
$gaceta = $registro_x->gaceta;
$fecha_gac = $registro_x->fecha_gaceta;
$region=$registro_x->nombre;
//---------------------------------
$pdf->Ln(8);
$pdf->SetFont('Times','B',$tama�oletra);
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
//----------------
}
else
{
// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR
$consulta_x = "SELECT * FROM vista_jefe_fis WHERE id_sector=".$_SESSION['SEDE'].";";
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$jefe = $registro_x->jefe;
$cedula = "C.I. N� V-" .$registro_x->cedula;
$cargo = $registro_x->cargo;
$providencia = utf8_decode($registro_x->providencia);
$fecha_prov = $registro_x->fecha_prov;
$gaceta = $registro_x->gaceta;
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = $registro_x->descripcion;

//---------------------------------
$pdf->Ln(8);
$pdf->SetFont('Times','B',$tama�oletra);
$pdf->Cell(0,5,$jefe,0,0,'C'); $pdf->Ln(5);
$pdf->SetFont('Times','',9);
$pdf->Cell(0,5,$cargo,0,0,'C'); $pdf->Ln(5);
$pdf->SetRightMargin(70);
$pdf->SetLeftMargin(70);
$pdf->SetFont('Times','',8);
$pdf->MultiCell(0,4,$providencia,0,'C');
$pdf->MultiCell(0,4,'de fecha '.voltea_fecha($fecha_prov),0,'C');
// FIN
//----------------
}

$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
$pdf->SetFont('Times','',9);
$pdf->Ln(5);

if ($_POST[LINEA]==1)
	{$pdf->Ln(5);}
	
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,5,'Notificaci�n'); $pdf->Ln(5);
$pdf->SetFont('Times','',8);
$pdf->Cell(0,5,'Firma:       __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Nombre:   __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'C.I. N�:     __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Cargo:       __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Tel�fono:  __________________________________'); $pdf->Ln(6);
$pdf->Cell(0,5,'Sello:        __________________________________'); $pdf->Ln(6);

$pdf->Output();

?>