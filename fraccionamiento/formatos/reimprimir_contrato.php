<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');
require('../../funciones/numeros_a_letras.php');

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}

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
		$this->Cell(340,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'C');
	}
	function Header()
	{
		$id= $_GET['id'];
		
		// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE
		$consulta = "SELECT expedientes_fraccionamiento.anno, expedientes_fraccionamiento.numero, expedientes_fraccionamiento.sector FROM liquidacion INNER JOIN expedientes_fraccionamiento ON liquidacion.anno_expediente = expedientes_fraccionamiento.anno AND liquidacion.num_expediente = expedientes_fraccionamiento.numero AND liquidacion.sector = expedientes_fraccionamiento.sector WHERE liquidacion.fraccionada = ".$id." GROUP BY expedientes_fraccionamiento.anno, expedientes_fraccionamiento.numero, expedientes_fraccionamiento.sector";
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		// ------
		$numero = $registro->numero;
		$año = $registro->anno;
		$sector = $registro->sector;
		
		////////// SIGLAS DE LA RESOLUCION
		$consulta_x = "SELECT Siglas_resol_Frac FROM z_siglas WHERE id_sector=".$sector;
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		$SIGLAS=$registro_x->Siglas_resol_Frac;
		// ---------------------
		
		////////// DATOS LA RESOLUCION
		$RESOLUCION = $SIGLAS."CO/".$año."/".sprintf("%004s", $numero);
		// ---------------------
		
		//Move to the right
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		$this->Ln(2);
		$this->SetFont('Times','B',11);
		$this->Cell(0,5,'N°    '.$RESOLUCION);
		$this->Ln(10);
		//Line break
	}		
}

$id= $_GET['id'];

$consulta = "SELECT expedientes_fraccionamiento.rif FROM liquidacion INNER JOIN expedientes_fraccionamiento ON liquidacion.anno_expediente = expedientes_fraccionamiento.anno AND liquidacion.num_expediente = expedientes_fraccionamiento.numero AND liquidacion.sector = expedientes_fraccionamiento.sector WHERE liquidacion.fraccionada = ".$id." GROUP BY expedientes_fraccionamiento.anno, expedientes_fraccionamiento.numero, expedientes_fraccionamiento.sector";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);
// ------
$rif = $registro->rif;

// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE PARA VER SI TIENE NUMERO DE RESOLUCION
$consulta = "SELECT numero FROM expedientes_fraccionamiento WHERE indice=".$id;
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);
// ------ 

// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE
$consulta = "SELECT *, date_format(fecha,'%Y/%m/%d') as fechares FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE expedientes_fraccionamiento.rif= vista_contribuyentes_direccion.rif AND expedientes_fraccionamiento.indice=".$id;
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);
// ------
$numero = $registro->numero;
$año = $registro->anno;
$fecha = $registro->fechares;
$sector = $registro->sector;
$contribuyente = $registro->contribuyente;
$rif_r = $registro->representante;
$cuotas = $registro->cuotas;
$monto= $registro->monto;
$tasa= $registro->tasa;
// ------
list($anno,$mes,$dia)=explode('/',$fecha);
$fecha = mktime(0,0,0,$mes,$dia,$anno);

////////// DATOS DEL REPRESENTANTE
$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='".$rif_r."';";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
// ------
$representante = $registro_x->contribuyente;
// ------

// ---------- DATOS DEL JEFE
$consulta_x = "SELECT cedula, jefe, cargo, providencia FROM vista_jefe_rec WHERE id_sector=".$sector;
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
// ------
$jefe=$registro_x->jefe;
$cedula_numero= number_format(doubleval($registro_x->cedula),0,'','.');
$cedula= "C.I. N° V-" .$registro_x->cedula;
$cargo=$registro_x->cargo;
$texto1=$registro_x->providencia;
// ------

// ------ CALCULO DE LA MENSUALIDAD
// tasa de interes mensual
$tasa_2 =  ($tasa/100)/12;
// -------------
$mensualidad = 1/pow((1+$tasa_2),$cuotas);
$mensualidad = 1 - $mensualidad;
$mensualidad =  round(($monto*$tasa_2)/$mensualidad,2);
// -------------
// todo lo demas
$interes = ($monto * $tasa_2);
$total = round(($mensualidad*$cuotas),2);
// ------------
	
////////// GERENCIA, SECTOR O UNIDAD DE EMISION
$consulta_x = "SELECT adscripcion_gerencia, nombre FROM z_sectores WHERE id_sector=".$sector;
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$adscripcion=$registro_x->adscripcion_gerencia;
$ciudad=$registro_x->nombre;
//////////

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,25);

$pdf->AddPage();

$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

////////// CIUDAD DE EMISION Y RESOLUCION
$consulta_x = "SELECT Siglas_resol_Frac FROM z_siglas WHERE id_sector=".$sector;
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$SIGLAS=$registro_x->Siglas_resol_Frac;
// ---------------------

$t=(140-(strlen($ciudad)));

$mes=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

$pdf->Text($t,34,$ciudad.',  '.strftime('%d', strtotime(date('m/d/Y',$fecha))).' de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$fecha)))-1)].' del '.strftime('%Y', strtotime(date('m/d/Y',$fecha))));
$pdf->Ln(3);
	
$pdf->SetFont('Times','B',12);
$pdf->Cell(0,5,'CONVENIMIENTO PARA EL FRACCIONAMIENTO DE PAGOS',0,0,'C');
$pdf->Ln(7);
$pdf->Cell(0,5,'DE DEUDAS TRIBUTARIAS NACIONALES (Artículo 47 COT)',0,0,'C');
$pdf->Ln(15);

// FIN
$t = 6;
$pdf->SetFont('Times','',11.5);

$txt = 'Entre la REPUBLICA BOLIVARIANA DE VENEZUELA por órgano del SERVICIO NACIONAL INTEGRADO DE ADMINISTRACION ADUANERA Y TRIBUTARIA - SENIAT, representado en este acto por el ciudadano(a) '.$jefe.', titular de la cédula de identidad Nro. V-'.$cedula_numero.', actuando en su carácter de '.$cargo.', '.$adscripcion.' Gerencia Regional de Tributos Internos '.buscar_region().', según '.$texto1.', en ejercicio de las atribuciones previstas en el artículo 47 del Código Orgánico Tributario vigente y lo previsto en el Capitulo III de la Providencia Administrativa N° 0116 de fecha 14 de febrero de 2005, publicada en la Gaceta Oficial 38.213 de fecha 21 de junio de 2005, sobre el Procedimiento de Otorgamiento de Prórrogas, Fraccionamientos y Plazos para la Declaración y/o Pago de Obligaciones Tributarias y en el artículo 97, numerales 13 y 33 de la Resolución 32 de fecha 24/03/94 que establece la Organización, Atribuciones y Funciones del SENIAT, publicada en la Gaceta Oficial N° 4.881 Extraordinario del 29 de marzo de 1995, en lo adelante EL SENIAT, por una parte, y por la otra la sociedad mercantil '.$contribuyente.', inscrita en el Registro de Información Fiscal (R.I.F.) bajo el Nro. '.formato_rif($rif).', y quien a los efectos del presente documento se denominará EL CONTRIBUYENTE, representado en este acto por el ciudadano '.$representante.', mayor de edad, de este domicilio, inscrito en el Registro de Información Fiscal (R.I.F.) bajo el Nro. '.formato_rif($rif_r).', actuando en su carácter de Representante Legal, debidamente facultado para este otorgamiento por los estatutos sociales, han concertado la celebración del presente convenimiento de pago, el cual se regirá por las disposiciones contenidas en las cláusulas que a continuación se especifican:';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

////////////////--------------
$cuotas_L = num_a_letras_sin_dec($cuotas);

$txt='PRIMERA: EL SENIAT, de conformidad con lo establecido en la Providencia supra identificada que establece el Procedimiento de Otorgamiento de Prórrogas, Fraccionamientos y Plazos para la Declaración y/o Pago de Obligaciones Tributarias, concede a EL CONTRIBUYENTE un plan de fraccionamiento de pago por un plazo de '.$cuotas_L.' ('.$cuotas.') MESES, contados a partir de la firma del presente convenio, para el pago de las deudas tributarias primitivas identificadas en la relación como Anexo A.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$total_L = num_a_letras($monto);

$txt='SEGUNDA: El monto total de lo adeudado por EL CONTRIBUYENTE objeto del presente fraccionamiento, asciende a la suma de '.$total_L.' BOLIVARES FUERTES (BsS. '.number_format(doubleval($monto),2,',','.').' ) por concepto de (impuesto, multas e intereses). El monto adeudado a fraccionar será otorgado según tabla de amortización identificada como anexo “B”.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t);  

$mensualidad_L = num_a_letras($mensualidad);

$txt='TERCERA: EL CONTRIBUYENTE se compromete a pagar el monto total adeudado, señalado en la cláusula segunda del presente convenimiento, en '.$cuotas_L.' ('.$cuotas.') cuotas iguales, mensuales y consecutivas por un monto de '.$mensualidad_L.' BOLIVARES FUERTES (Bs. '.number_format(doubleval($mensualidad),2,',','.').' ) cada una, cuyo primer vencimiento será a los treinta días (30) días continuos, contados a partir de la fecha de la firma del presente documento. Todas las cuotas contienen un abono a la deuda tributaria de conformidad a lo dispuesto en el artículo 44 del Código Orgánico Tributario vigente, y a los intereses de financiamiento, tal y como se demuestra en el cuadro de amortización marcado como anexo “B” y que forma parte integrante y principal del presente convenio.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t);  

$tasa_L = num_a_letras($tasa);

$txt='CUARTA: Los intereses de financiamiento son calculados sobre los montos fraccionados, desde el día siguiente a la notificación de la aprobación del presente convenio, hasta el pago total de la deuda y son equivalentes a la tasa de interés activa mensual promedio vigente publicada por EL SENIAT para el momento de la aprobación del convenio. Se entenderá por tasa de interés activa mensual promedio vigente a la tasa de interés activa mensual promedio de los bancos comerciales y universales del país con mayor volumen de depósitos, excluidas las carteras preferenciales, calculada por el Banco Central de Venezuela para el mes calendario inmediato anterior. A los efectos del presente fraccionamiento, se aplicó la tasa activa bancaria de '.number_format(doubleval($tasa),2,',','.').'%.'; 
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='QUINTA: En caso de presentarse una variación mensual positiva o negativa del diez por ciento (10%) o más entre la tasa bancaria aplicada al fraccionamiento, y la tasa bancaria vigente, se calcularán las diferencias y serán reflejadas en las cuotas restantes. Los ajustes respectivos se realizarán trimestralmente, o antes, si son dos o menos las referidas cuotas.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t);  

$txt='SEXTA: Al efecto de realizar la cancelación de las cuotas, EL SENIAT emitirá la totalidad de las planillas de pago por los conceptos, montos y las respectivas fechas de vencimiento; igualmente, queda expresamente convenido que EL CONTRIBUYENTE podrá pagar las cuotas adeudadas antes de su vencimiento.'; 
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t);  

$txt='SÉPTIMA: El incumplimiento por parte de EL CONTRIBUYENTE de pagar dos (2) de las cuotas, consecutivas , en los terminos y condiciones establecidas en el presente fraccionamiento, originará la pérdida inmediata del fraccionamiento convenido, sobre el saldo no pagado a la fecha del incumplimiento mas los intereses moratorios. Iniciando EL SENIAT inmediatamente las acciones de cobro pertinentes.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t);  

$txt='OCTAVA: Los pagos extemporáneos, así como el saldo no pagado generarán los intereses moratorios de conformidad con lo previsto en el artículo 66 del Código Orgánico Tributario vigente, para la fecha de incumplimiento hasta la extinción total de la deuda.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='NOVENA: La(s) planilla(s) de Liquidacion objeto de este convenio estipulada(s) en la Clausula Primera, quedara(n) sin efecto una vez que el CONTRIBUYENTE le sea notificado el presente Contrato de Fraccionamiento de pago.'; 
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='DECIMA: El presente convenio reviste carácter de título ejecutivo para el cobro de las deudas en él señaladas.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='DECIMA PRIMERA: Para todos los efectos legales de este convenimiento, se elige como domicilio especial y excluyente de cualquier otro, a la ciudad de Calabozo, Estado Dtto. capital, y en consecuencia, cualquier conflicto que surja entre las partes originado en su aplicación e interpretación, será resuelto por los Tribunales con competencia en lo Contencioso-Tributario de esta circunscripción judicial.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='Se hacen tres (3) ejemplares de un mismo tenor y a un solo efecto.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='En '.$ciudad.', a los '.strftime('%d', strtotime(date('m/d/Y',$fecha))).' día(s) del mes de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$fecha)))-1)].' de '.strftime('%Y', strtotime(date('m/d/Y',$fecha))).'.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

// FIRMA DEL JEFE
$cedula_gerente = $reg_gerente->ci_gerente;
$_SESSION['SEDE']=$_SESSION['SEDE_USUARIO'];
include "firma.php";
//---------------------------------

$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
$pdf->SetFont('Times','',9);
$pdf->Ln(7);
	
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

$pdf->Output();

?>