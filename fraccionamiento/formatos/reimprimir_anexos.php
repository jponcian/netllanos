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
		//Move to the right
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		$this->Ln(10);
		//Line break
	}	
	
var $B;
var $I;
var $U;
var $HREF;

function PDF($orientation='P', $unit='mm', $size='A4')
{
    // Llama al constructor de la clase padre
    $this->FPDF($orientation,$unit,$size);
    // Iniciación de variables
    $this->B = 0;
    $this->I = 0;
    $this->U = 0;
    $this->HREF = '';
}

function WriteHTML($html)
{
    // Intérprete de HTML
    $html = str_replace("\n",' ',$html);
    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            // Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            // Etiqueta
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                // Extraer atributos
                $a2 = explode(' ',$e);
                $tag = strtoupper(array_shift($a2));
                $attr = array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])] = $a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    // Etiqueta de apertura
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF = $attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    // Etiqueta de cierre
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF = '';
}

function SetStyle($tag, $enable)
{
    // Modificar estilo y escoger la fuente correspondiente
    $this->$tag += ($enable ? 1 : -1);
    $style = '';
    foreach(array('B', 'I', 'U') as $s)
    {
        if($this->$s>0)
            $style .= $s;
    }
    $this->SetFont('',$style);
}
	
}

$id= $_GET['id'];

// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE PARA VER SI TIENE NUMERO DE RESOLUCION
$consulta = "SELECT numero FROM expedientes_fraccionamiento WHERE indice=".$id;
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);
// ------ 

// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE
$consulta = "SELECT *, date_format(fecha,'%d/%m/%Y') as fechares FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE expedientes_fraccionamiento.rif= vista_contribuyentes_direccion.rif AND expedientes_fraccionamiento.indice=".$id;
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
$monto = $registro->monto;
$tasa = $registro->tasa;
$direccion = $registro->direccion;
$sector = $registro->sector;
$rif =  $registro->rif;
// ------
//list($dia,$mes,$anno)=explode('/',$fecha);
//$fecha = mktime(0,0,0,$mes,$dia,$anno);

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,10);
$pdf->SetFillColor(192,192,192);

// ---- PRIMERA PAGINA ANEXO 1
$pdf->AddPage();

$pdf->SetFont('Times','B',11);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

////////// CIUDAD DE EMISION Y RESOLUCION 
$consulta_x = "SELECT Siglas_resol_Frac FROM z_siglas WHERE id_sector=".$sector;
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$ciudad=$registro_x->nombre;
$SIGLAS=$registro_x->Siglas_resol_Frac;
// ---------------------
$consulta_x = "SELECT nombre FROM z_sectores WHERE id_sector=".$sector;
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$ciudad=$registro_x->nombre;
// ---------------------

$t=(140-(strlen($ciudad)));

$mes=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

$pdf->Text($t,34,$ciudad.',  '.strftime('%d', strtotime(date('m/d/Y',$fecha))).' de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$fecha)))-1)].' del '.strftime('%Y', strtotime(date('m/d/Y',$fecha))));
	
$pdf->SetFont('Times','B',11);
$pdf->Cell(0,5,'ANEXO A',0,0,'C');
$pdf->Ln(5);

$pdf->SetFont('Times','B',11);
$pdf->Cell(0,5,'Planillas de Liquidación Fraccionadas',0,0,'C');
$pdf->Ln(12);

// FIN

$pdf->SetFont('Times','',11);
$t = 7;

$txt = '<b>Señores:</b> '.$contribuyente;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>R.I.F.:</b> '.formato_rif(strtoupper($rif));
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>Domicilio Fiscal:</b> '.$direccion;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>N° Aprobacion:</b> '.$numero;
$pdf->WriteHTML($txt);

$pdf->Cell(20,5,'',0,0,'C');

$txt = '<b>Fecha Registro:</b> '.$fecha;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$pdf->Ln(10); 
$t = 6;

// ------ CUADRO DE LIQUIDACIONES
$txt = 'N°';
$a=5;
$pdf->Cell($a,6,$txt,1,0,'C',1);

$txt = 'Numero Liquidacion';
$b=47;
$pdf->Cell($b,6,$txt,1,0,'C',1);

$txt = 'Fecha Liq.';
$c=20;
$pdf->Cell($c,6,$txt,1,0,'C',1);

$txt = 'Periodo';
$d=40;
$pdf->Cell($d,6,$txt,1,0,'C',1);

$txt = 'Cant. U.T.';
$e=20;
$pdf->Cell($e,6,$txt,1,0,'C',1);

$txt = 'Valor U.T.';
$g=20;
$pdf->Cell($g,6,$txt,1,0,'C',1);

$txt = 'Monto';
$f=30;
$pdf->Cell($f,6,$txt,1,0,'C',1);

$pdf->Ln($t); 

// --------- DETALLES
	$consulta = "SELECT liquidacion.liquidacion as Numero_Liquidacion, (liquidacion.monto_bs / liquidacion.concurrencia * liquidacion.especial) AS MontoCifras, (liquidacion.monto_ut / liquidacion.concurrencia * liquidacion.especial) AS UTCifras, date_format(liquidacion.fecha_impresion, '%d/%m/%Y') as FechaLiquidacion, date_format(liquidacion.periodoinicio, '%d/%m/%Y') as periodoinicio, date_format(liquidacion.periodofinal, '%d/%m/%Y') as periodofinal FROM liquidacion INNER JOIN a_sancion ON a_sancion.id_sancion = liquidacion.id_sancion WHERE liquidacion.fraccionada = ".$id." AND status=50  ORDER BY liquidacion.liquidacion ASC;";
	$tabla = mysql_query($consulta);
	
	$i = 1;
	$t = 5;
	$total = 0;
	
	while ($registro = mysql_fetch_object($tabla))
	{
	$txt = $i;
	$pdf->Cell($a,5,$txt,1,0,'C');
	
	$txt = $registro->Numero_Liquidacion;
	$pdf->Cell($b,5,$txt,1,0,'C');
	
	$txt = $registro->FechaLiquidacion;
	$pdf->Cell($c,5,$txt,1,0,'C');
	
	$txt = $registro->periodoinicio . ' - ' . $registro->periodofinal;
	$pdf->Cell($d,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval(($registro->UTCifras)),2,',','.');
	$pdf->Cell($e,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval($_SESSION['VALOR_UT_ACTUAL']),0,',','.');
	$pdf->Cell($g,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval($registro->UTCifras*$_SESSION['VALOR_UT_ACTUAL']),2,',','.');
	$pdf->Cell($f,5,$txt,1,0,'C');
	$pdf->Ln($t); 
	
	$total = $total + ($registro->UTCifras*$_SESSION['VALOR_UT_ACTUAL']);
	$i++;
	}


	$txt = 'Monto Total Fraccionado => ';
	$a=152;
	$pdf->Cell($a,6,$txt,1,0,'R');
	
	$txt = $total;
	$b=30;
	$pdf->Cell($b,6,number_format(doubleval($txt),2,',','.'),1,0,'C',1);
	
	$pdf->Ln($t); 
	
// ---- SEGUNDA PAGINA ANEXO 2
$pdf->AddPage();

$pdf->SetFont('Times','B',11);
$pdf->Cell(0,5,'ANEXO B',0,0,'C');
$pdf->Ln(5);

$pdf->SetFont('Times','B',11);
$pdf->Cell(0,5,'Tabla de Amortizacion',0,0,'C');
$pdf->Ln(12);

// FIN

$pdf->SetFont('Times','',11);
$t = 7;

$txt = '<b>Señores:</b> '.$contribuyente;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>R.I.F.:</b> '.formato_rif(strtoupper($rif));
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>Domicilio Fiscal:</b> '.$direccion;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>N° Aprobacion:</b> '.$numero;
$pdf->WriteHTML($txt);

$pdf->Cell(20,5,'',0,0,'C');

$txt = '<b>Fecha Registro:</b> '.$fecha;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$pdf->Ln(5); 
$t = 6;

// ------ CUADRO DE DATOS
$txt = '';
$o=36;
$pdf->Cell($o,6,$txt,0,0,'C',0);

$txt = 'Tasa: '.number_format(doubleval($tasa),2,',','.');
$a=36;
$pdf->Cell($a,6,$txt,1,0,'C',1);

$txt = 'Cuotas: '.$cuotas;
$b=36;
$pdf->Cell($b,6,$txt,1,0,'C',1);

$txt = 'Capital: '.number_format(doubleval($monto),2,',','.');
$c=36;
$pdf->Cell($c,6,$txt,1,0,'C',1);

$txt = '';
$pdf->Cell($o,6,$txt,0,0,'C',0);

$pdf->Ln(15); 

// ------ CUADRO DE LIQUIDACIONES
$txt = 'N° Cuota';
$a=20;
$pdf->Cell($a,6,$txt,1,0,'C',1);

$txt = 'Monto Inicial';
$b=32;
$pdf->Cell($b,6,$txt,1,0,'C',1);

$txt = 'Mensualidad';
$c=32;
$pdf->Cell($c,6,$txt,1,0,'C',1);

$txt = 'Amortizacion';
$d=32;
$pdf->Cell($d,6,$txt,1,0,'C',1);

$txt = 'Interes';
$e=32;
$pdf->Cell($e,6,$txt,1,0,'C',1);

$txt = 'Monto Final';
$f=32;
$pdf->Cell($f,6,$txt,1,0,'C',1);
$pdf->Ln($t); 

// --------- DETALLES
$i = 1;
$t = 5;
	
if ($cuotas>0)	
	{
	$z = 1;
	// ------ CALCULO DE LA MENSUALIDAD
	// tasa de interes mensual
	$tasa_2 =  ($tasa/100)/12;
	// -------------
	$mensualidad = 1/pow((1+$tasa_2),$cuotas);
	$mensualidad = 1 - $mensualidad;
	$mensualidad = ($total*$tasa_2)/$mensualidad;
	// -------------
	// todo lo demas
	$interes = ($total * $tasa_2);
	$amortizacion = $mensualidad - $interes;
	$monto_final = $total - $amortizacion;
	// ------------
	
	$txt = $z;
	$pdf->Cell($a,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval($total),2,',','.');
	$pdf->Cell($b,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval($mensualidad),2,',','.');
	$pdf->Cell($c,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval($amortizacion),2,',','.');
	$pdf->Cell($d,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval($interes),2,',','.');
	$pdf->Cell($e,5,$txt,1,0,'C');
	
	$txt = number_format(doubleval($monto_final),2,',','.');
	$pdf->Cell($f,5,$txt,1,0,'C');
	$pdf->Ln($t); 
	
	// ---------------
	$total = $total - $amortizacion;
	// -------------
	$z++;
	
	while ($z <= $cuotas)
		{
		// todo lo demas
		$interes = ($total * $tasa_2);
		$amortizacion = $mensualidad - $interes;
		$monto_final = $total - $amortizacion;
		// ------------
		$txt = $z;
		$pdf->Cell($a,5,$txt,1,0,'C');
		
		$txt = number_format(doubleval($total),2,',','.');
		$pdf->Cell($b,5,$txt,1,0,'C');
		
		$txt = number_format(doubleval($mensualidad),2,',','.');
		$pdf->Cell($c,5,$txt,1,0,'C');
		
		$txt = number_format(doubleval($amortizacion),2,',','.');
		$pdf->Cell($d,5,$txt,1,0,'C');
		
		$txt = number_format(doubleval($interes),2,',','.');
		$pdf->Cell($e,5,$txt,1,0,'C');
		
		$txt = number_format(doubleval($monto_final),2,',','.');
		$pdf->Cell($f,5,$txt,1,0,'C');
		$pdf->Ln($t); 
		
		// ---------------
		$total = $total - $amortizacion;
		// -------------
		$z++;
		}
}

$pdf->Output();

?>