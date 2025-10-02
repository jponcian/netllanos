<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}

class PDF extends FPDF
	{
	function Footer()
	{    //Posición a 1,5 cm del final
		$this->SetY(-15);
		$this->SetFont('Times','I',9);
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,5,'NetLosLlanos '.$this->PageNo().' de {nb}',0,0,'R');
		$this->SetY(-15);
		//-------------
		$id = $_GET['id'];
		// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE
		$consulta = "SELECT anno_expediente, num_expediente FROM vista_ajustes_ut WHERE id_expediente='".$id."';";
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		// ------
		$anno = $registro->anno_expediente;
		$num = $registro->num_expediente;
		//Número de página
		$this->Cell(0,10,'Exp '.$anno.'/'.$num);
	}
	function Header()
	{
		$id = $_GET['id'];
		// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE
		$consulta = "SELECT * FROM vista_ajustes_ut WHERE id_expediente='".$id."';";
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		////////// CIUDAD DE EMISION
		$Ciudad = $registro->nombre;
		// -----------
		//------ ORIGEN DEL FUNCIONARIO
		include "../../funciones/origen_funcionario.php";
		//--------------------
		
		////////// RESOLUCION
		list ($resolucion, $fecha, $numero, $año) = funcion_resolucion($registro->sector, $registro->origen_exp, $registro->anno_expediente, $registro->num_expediente);
		// ---------------------

		//Select Arial bold 15
		$this->SetFont('Arial','B',15);
		//Move to the right
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		$this->SetFont('Times','B',11);
		$this->Cell(0,5,'N°    '.$resolucion);
		//-------------
		$this->SetY(22);
		$this->Cell(0,5,ucwords($Ciudad).', '. date('d',strtotime($fecha)) .' de '.$_SESSION['meses_anno'][abs(date('m',strtotime($fecha)))]. ' del ' . date('Y',strtotime($fecha)),0,0,'R');
		$this->Ln(15);
		//Line break
	}		
	}

$id = $_GET['id'];
$rif = $_GET['rif'];
$linea = $_GET['linea'];

// ------ OBTENER LA INFORMACION DEL EXPEDIENTE
$consulta = "SELECT * FROM expedientes_ajustes_ut WHERE id_expediente='".$id."';";
$tabla = mysql_query($consulta); 
$registro = mysql_fetch_object($tabla);
// ------
//------ ORIGEN DEL FUNCIONARIO
include "../../funciones/origen_funcionario.php";
//--------------------

// ACTUALIZACION DEL NUMERO DE LA RESOLUCION
generar_resolucion( $registro->sector, $registro->origen_exp, $registro->anno_expediente, $registro->num_expediente);

////////// NUMERO DE LA RESOLUCION
list ($resolucion, $fecha, $numero, $año) = funcion_resolucion( $registro->sector, $registro->origen_exp, $registro->anno_expediente, $registro->num_expediente);
		
// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,25);

$pdf->AddPage();
$pdf->SetFillColor(190);

$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT * FROM vista_ajustes_ut WHERE id_expediente='".$id."';";
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------

$consulta_x = "SELECT id_sancion FROM liquidacion WHERE anno_expediente=".$registro_datos->anno_expediente." AND num_expediente=".$registro_datos->num_expediente." AND sector=".$registro_datos->sector." AND origen_liquidacion=".$registro->origen_exp.";";
$tabla_x = mysql_query($consulta_x);
$registro_xx = mysql_fetch_object($tabla_x);
if ($registro_xx->id_sancion>60000 )// or $registro->id_sancion<3649
	{	$titulo = 'AJUSTE UNIDAD TRIBUTARIA';	$siglas = 'Moneda';	}
else
	{	$titulo = 'AJUSTE UNIDAD TRIBUTARIA';	$siglas = 'UT';	}$siglas = 'UT';
//$pdf->Ln();
	
$pdf->SetFont('Times','B',13);
$pdf->Cell(0,5,'RESOLUCIÓN',0,0,'C');
$pdf->Ln(6);
$pdf->SetFont('Times','B',13);
$pdf->Cell(0,5,$titulo,0,0,'C');
$pdf->Ln(8);

$pdf->SetFont('Times','',11);
$pdf->Cell(0,5,'Contribuyente:');

$pdf->SetFont('Times','B',11);
$pdf->SetX(50);
$pdf->MultiCell(0,5,strtoupper($registro_datos->contribuyente));
$pdf->Ln(3); 

$pdf->SetFont('Times','',11);
$pdf->Cell(0,5,'RIF N°:'); 

$pdf->SetFont('Times','B',11);
$pdf->SetX(50);
$pdf->Cell(0,5,formato_rif($registro_datos->rif));
$pdf->Ln(8); 

$pdf->SetFont('Times','',11);
$pdf->Cell(0,5,'Domicilio Fiscal:'); 

$pdf->SetFont('Times','B',11);
$pdf->SetX(50);
$pdf->MultiCell(0,5,strtoupper(trim($registro_datos->direccion)));

$pdf->SetFont('Times','',12);
$pdf->Ln(5);

switch ($registro->origen_exp) 
	{
	case 7:
		$division = 'Sujetos Pasivos Especiales';
		$articulo = 102;
		break;
	case 16:
		$division = 'Recaudación';
		$articulo = 97;
		break;		
	}

list ($estado, $sede, $conector1, $conector2, $adscripcion) = buscar_sector($registro_datos->sector);

if ($siglas=='UT')
	{	
	$txt='En '.$registro_datos->nombre.', Estado '.$estado.', sede '.$conector1.' '.$sede.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.buscar_region().', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT); de conformidad con lo establecido en los artículos 89, 131 numeral 15 y 182 del Código Orgánico Tributario publicado en Gaceta Oficial N° 6.152 Extraordinario del 18 de noviembre de 2014, en lo sucesivo COT, la División de '.$division.' adscrita a '.$adscripcion.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.buscar_region().', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), en uso de las facultades legales conferidas en el artículo '.$articulo.' numeral 2 de la Resolución Nº 32 sobre la Organización, Atribuciones y Funciones del SENIAT, publicada en Gaceta Oficial Nº 4.881 Extraordinario, de fecha 29/03/1995, procede a emitir la presente Resolución, en virtud de los hechos que se exponen a continuación:';
	}
else
	{	
	$txt='En '.$registro_datos->nombre.', Estado '.$estado.', sede '.$conector1.' '.$sede.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.buscar_region().', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT); de conformidad con lo establecido en los artículos 89, 131 numeral 15 y 182 del Código Orgánico Tributario publicado en Gaceta Oficial N° 6.507 Extraordinario del 29 de Enero de 2020, en lo sucesivo COT, la División de '.$division.' adscrita a '.$adscripcion.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.buscar_region().', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), en uso de las facultades legales conferidas en el artículo '.$articulo.' numeral 2 de la Resolución Nº 32 sobre la Organización, Atribuciones y Funciones del SENIAT, publicada en Gaceta Oficial Nº 4.881 Extraordinario, de fecha 29/03/1995, procede a emitir la presente Resolución, en virtud de los hechos que se exponen a continuación:';
	}
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

// FIN

$pdf->SetFont('Times','',12);
//$pdf->Ln(5);

////////// DIRECCION DEL CONTRIBUYENTE
$direccion = $registro_datos->direccion;
$contribuyente = $registro_datos->contribuyente;
// ---------------------

$txt = 'En este sentido, verificado como ha sido, el estatus en nuestro Sistema de Información Tributaria, de la(s) planilla(s) por concepto de multa correspondiente al Contribuyente, '.$contribuyente.', con Rif '.formato_rif($registro->rif).', con domicilio en '.$direccion.', que se describe a continuación:';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(6); 

/////// TITULOS DEL CUADRO CON LAS LIQUIDACIONES

$pdf->SetFont('Times','B',10);

$x=$pdf->GetX();
$y=$pdf->GetY();

$txt='N° Liquidación Primitiva';
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+40;
$y=$pdf->GetY();	
$pdf->MultiCell($a=40,6,$txt,1,'C',1);

//$txt='Valor UT Primitiva';
//$pdf->SetY($y);
//$pdf->SetX($x);
//$x=$pdf->GetX()+18;
//$y=$pdf->GetY();	
//$pdf->MultiCell($b=18,6,$txt,1,'C',1);
//
//$txt='Cant. UT';
//$pdf->SetY($y);
//$pdf->SetX($x);
//$x=$pdf->GetX()+18;
//$y=$pdf->GetY();	
//$pdf->MultiCell($c=18,12,$txt,1,'C',1);

$txt='Monto BsS. Primitivo';
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+30;
$y=$pdf->GetY();	
$pdf->MultiCell($d=30,6,$txt,1,'C',1);

$txt=$siglas.' Primitiva';
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+26;
$y=$pdf->GetY();	
$pdf->MultiCell($e=26,6,$txt,1,'C',1);

$txt='Cant. '.$siglas;
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+26;
$y=$pdf->GetY();	
$pdf->MultiCell($f=26,12,$txt,1,'C',1);

$txt='Monto BsS. Actual';
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+29;
$y=$pdf->GetY();	
$pdf->MultiCell($g=29,6,$txt,1,'C',1);

$txt='Monto Diferencia';
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+29;
$y=$pdf->GetY();	
$pdf->MultiCell($h=29,12,$txt,1,'C',1);

//////// ---- DETALLE DE LAS PLANILLAS

$monto = 0;
$pdf->SetFont('Times','',9);

$consulta_x = "SELECT *, ((monto_bs*especial)/concurrencia) as monto_bs1 FROM liquidacion WHERE anno_expediente=".$registro_datos->anno_expediente." AND num_expediente=".$registro_datos->num_expediente." AND sector=".$registro_datos->sector." AND origen_liquidacion=".$registro->origen_exp.";";
$tabla_x = mysql_query($consulta_x);
//echo $consulta_x;

while ($registro_xx = mysql_fetch_object($tabla_x))
{
// ----- DETALLE DE LA LIQUIDACION PRIMITIVA
$consulta_aux = "SELECT liquidacion, ((monto_bs*especial)/concurrencia) as MontoCifras1, ((monto_ut*especial)/concurrencia) as UTCifras1 FROM liquidacion WHERE id_liquidacion=".$registro_xx->id_liq_primitiva.";";
//echo $consulta_aux.'<br>';
$tabla_aux = mysql_query($consulta_aux);
$registro_old = mysql_fetch_object($tabla_aux);
// -----

$x=$pdf->GetX();
$y=$pdf->GetY();

if ($y>241) 
	{
	$pdf->AddPage();$pdf->Ln(10);
	$y=45;
	}

//
$txt=$registro_old->liquidacion;
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+$a;
$y=$pdf->GetY();	
$pdf->MultiCell($a,6,$txt,1,'C');
////
//$txt=formato_moneda(minimo_soberano($registro_old->MontoCifras1/$registro_old->UTCifras1,1));
//$pdf->SetY($y);
//$pdf->SetX($x);
//$x=$pdf->GetX()+18;
//$y=$pdf->GetY();	
//$pdf->MultiCell(18,6,$txt,1,'C');
////
//$txt=formato_moneda(minimo_soberano($registro_old->UTCifras1,1));
//$pdf->SetY($y);
//$pdf->SetX($x);
//$x=$pdf->GetX()+18;
//$y=$pdf->GetY();	
//$pdf->MultiCell(18,6,$txt,1,'C');
//
$txt=formato_moneda(minimo_soberano($registro_old->MontoCifras1,1));
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+$d;
$y=$pdf->GetY();	
$pdf->MultiCell($d,6,$txt,1,'C');
//
$txt=formato_moneda(minimo_soberano(($registro_xx->monto_bs1+$registro_old->MontoCifras1)/$registro_old->UTCifras1,1));
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+$e;
$y=$pdf->GetY();	
$pdf->MultiCell($e,6,$txt,1,'C');
//
$txt=formato_moneda($registro_old->UTCifras1);
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+$f;
$y=$pdf->GetY();	
$pdf->MultiCell($f,6,$txt,1,'C');
//
$txt=formato_moneda(minimo_soberano(($registro_xx->monto_bs1+$registro_old->MontoCifras1),1));
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+$g;
$y=$pdf->GetY();	
$pdf->MultiCell($g,6,$txt,1,'C');
//
$txt=formato_moneda($registro_xx->monto_bs1);
$pdf->SetY($y);
$pdf->SetX($x);
$x=$pdf->GetX()+$h;
$y=$pdf->GetY();	
$pdf->MultiCell($h,6,$txt,1,'C');
//
$monto += $registro_xx->monto_bs1;
//
}

//////// ---- TOTAL DE LAS PLANILLAS
$pdf->SetFont('Times','B',10);

$txt='==> Monto Total BsS. ==>  ';
$pdf->Cell(151,6,$txt,1,0,'R',1);
$txt=$monto;
$pdf->Cell($h,6,number_format($txt,2,',','.'),1,0,'C',1);
$pdf->Ln(10);

//////// ---------------------------
		
$pdf->SetFont('Times','',12);

if ($siglas=='UT')
	{	
	$txt='Por cuanto nos encontramos en el supuesto de hecho establecido el el artículo 91 del COT tributario vigente para el momento del pago de la(s) planilla(s) antes señalada(s), se ordena expedir a cargo del (de la ) Contribuyente o Responsable antes identificado(a) planilla(s) de pago por concepto de multa(s), como consecuencia de la diferencia existente entre el valor de la moneda mas alta publicada por el Banco Central vigente para el momento del pago y el valor que estaba para el momento de la comisión del ílicito, por el (los) monto(s) indicado(s), la(s) cual(es) deberá cancelar de forma inmediata en la Oficina Receptora de Fondos Nacionales; asímismo, se le notifica que el monto de la sanción se encuentra sujeta a modificación en caso de cambio del valor de la Unidad Tributaria entre la presente fecha y la fecha efectiva de pago, conforme a lo previsto en el Artículo 91 del Código Orgánico Tributario.';
	}
else
	{	
	$txt='Por cuanto nos encontramos en el supuesto de hecho establecido el el artículo 91 del COT tributario vigente para el momento del pago de la(s) planilla(s) antes señalada(s), se ordena expedir a cargo del (de la ) Contribuyente o Responsable antes identificado(a) planilla(s) de pago por concepto de multa(s), como consecuencia de la diferencia existente entre el valor de la Unidad Tributaria vigente para el momento del pago y el valor que estaba para el momento de la comisión del ílicito, por el (los) monto(s) indicado(s), la(s) cual(es) deberá cancelar de forma inmediata en la Oficina Receptora de Fondos Nacionales; asímismo, se le notifica que el monto de la sanción se encuentra sujeta a modificación en caso de cambio del valor de la Unidad Tributaria entre la presente fecha y la fecha efectiva de pago, conforme a lo previsto en el Artículo 91 del Código Orgánico Tributario.';	
	}
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(4); 

//------ SI LA FIRMA ESTÁ MUY ABAJO
if ($pdf->GetY()>160) {$pdf->AddPage();}

if ($siglas=='UT')
	{	
	$txt='En caso de inconformidad con el presente Acto Administrativo el (la) Contribuyente o Responsable podrá ejercer los recursos que consagra el Código Orgánico Tributario en sus Artículos 242 y 259, dentro de los plazos previstos en los Artículos 247 y 261 Ejusdem, para lo cual deberá darse cumplimiento a lo previsto en los Artículos 243 y 250 del citado código.';
	}
else
	{	
	$txt='En caso de inconformidad con el presente Acto Administrativo el (la) Contribuyente o Responsable podrá ejercer los recursos que consagra el Código Orgánico Tributario en sus Artículos 262 y 279, dentro de los plazos previstos en los Artículos 267 y 281 Ejusdem, para lo cual deberá darse cumplimiento a lo previsto en los Artículos 263 y 270 del citado código.';
	}
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(10); 

// FIRMA DEL JEFE
include "firma.php";
// FIN

$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
//
	
$pdf->SetFont('Times','B',9);
$pdf->Cell(110,5,'Notificado');  										
$pdf->Cell(0,5,'Funcionario(s) Actuante(s)'); 
$pdf->Ln(7);
$pdf->SetFont('Times','',8);
$pdf->Cell(15,5,'Nombre:');	
$pdf->Cell(95,5,'__________________________________');	
$pdf->Cell(15,5,'Nombre:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'C.I. N°:');	
$pdf->Cell(95,5,'__________________________________');	
$pdf->Cell(15,5,'C.I. N°:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Cargo:');	
$pdf->Cell(95,5,'__________________________________');	
$pdf->Cell(15,5,'Dependencia:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Fecha:');	
$pdf->Cell(95,5,'__________________________________');	
$pdf->Cell(15,5,'Cargo:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Firma:');	
$pdf->Cell(95,5,'__________________________________');	
$pdf->Cell(15,5,'Fecha:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Sello:');	
$pdf->Cell(95,5,'__________________________________');	
$pdf->Cell(15,5,'Firma:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Telefono:');	
$pdf->Cell(95,5,'__________________________________');	

$pdf->Output();

?>