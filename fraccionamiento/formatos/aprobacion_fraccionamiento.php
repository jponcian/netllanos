<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");
//--------------------

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
		$this->Cell(330,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'C');
	}
	function Header()
	{
		$rif = $_GET['rif'];
		
		// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE
		$consulta = "SELECT numero, anno, sector FROM expedientes_fraccionamiento WHERE rif='".$rif."' AND status=1;";
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		// ------
		$numero = $registro->numero;
		$año = $registro->anno;
		$sector = $registro->sector;
		
		//------ ORIGEN DEL FUNCIONARIO
		include "../../funciones/origen_funcionario.php";
		//--------------------
		
		// ACTUALIZACION DEL NUMERO DE LA RESOLUCION
		generar_resolucion( $sector, $origenF, $año, $numero);
		
		////////// RESOLUCION
		list ($resolucion, $fecha, $numero, $año) = funcion_resolucion($sector, $origenF, $año, $numero);
		// ---------------------
		
		//Move to the right
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		$this->Ln(2);
		$this->SetFont('Times','B',11);
		$this->Cell(0,5,'N°    '.$resolucion);
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

$rif = $_GET['rif'];

// ------ PARA GENERAR EL EXPEDIENTE Y RESOLUCION
include "numero_expediente.php";

// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE
$consulta = "SELECT *, date_format(fecha,'%Y/%m/%d') as fechares FROM expedientes_fraccionamiento, vista_contribuyentes_direccion WHERE expedientes_fraccionamiento.rif= vista_contribuyentes_direccion.rif AND expedientes_fraccionamiento.rif='".$rif."' AND status=1;";
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
// ------
list($anno,$mes,$dia)=explode('/',$fecha);
$fecha = mktime(0,0,0,$mes,$dia,$anno);

//------ ORIGEN DEL FUNCIONARIO
include "../../funciones/origen_funcionario.php";
//--------------------

////////// DATOS DEL REPRESENTANTE
$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='".$rif_r."';";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
// ------
$representante = $registro_x->contribuyente;
// ------

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
$pdf->SetMargins(20,30,20);
$pdf->SetAutoPageBreak(1,10);

$pdf->AddPage();

$pdf->SetFont('Times','B',11);
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
	
$pdf->SetFont('Times','B',11);
$pdf->Cell(0,5,'APROBACION PLAN DE FRACCIONAMIENTO',0,0,'C');
$pdf->Ln(10);

// FIN

$pdf->SetFont('Times','',11);
$t = 7;

$txt = '<b>Señores:</b> '.$contribuyente;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>R.I.F.:</b> '.strtoupper(substr($rif,0,1)).'-'.substr($rif,1,8).'-'.substr($rif,9,1);
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$txt = '<b>Domicilio Fiscal:</b> '.$direccion;
$pdf->WriteHTML($txt);
$pdf->Ln($t); 

$pdf->SetFont('Times','',11);
$t = 3;
$pdf->Ln($t);

$txt = 'Vista la solicitud de Fraccionamiento de Pago Nro. '.$numero.', recibida en fecha '.date('d/m/Y',$fecha).', interpuesta por el (la) ciudadano(a) '.$representante.', inscrito en el Registro de Información Fiscal (R.I.F.) bajo el Nro. '.formato_rif($rif_r).', actuando en su carácter de Representante Legal del contribuyente '.$contribuyente.', inscrito en el en el Registro de Información Fiscal (R.I.F.) bajo el Nro. '.formato_rif($rif).', '.$adscripcion.' Gerencia Regional de Tributos Internos '.buscar_region().', del SENIAT, le notifica la aprobación del fraccionamiento solicitado, de conformidad a lo previsto en la Providencia Nro. SNAT/2005/0116, de fecha 14 de febrero de 2005, publicada en Gaceta Oficial 38.213, de fecha 21 de junio de 2005, sobre el Procedimiento de Otorgamiento de Prórrogas, Fraccionamientos y Plazos para la Declaración o Pago de Obligaciones Tributarias, para las deudas que se identifican en la relación identificada como anexo “A”.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='Por lo cual el fraccionamiento solicitado será otorgado según tabla de amortización identificada como anexo “B”.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

$txt='Asimismo le informa que esta Administración Tributaria ajustó, a la unidad tributaria vigente, de conformidad a lo dispuesto en el articulo 91 del Código Orgánico Tributario vigente, en concordancia con el artículo 23 de la providencia antes citada, las multas objeto de fraccionamiento. Del mismo modo serán calculados los intereses moratorios causados hasta la emisión del presente documento de haber lugar a ello.';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t);  

$txt='Esta notificación se formula de conformidad con lo previsto en el artículo 171 del Código Orgánico Tributario vigente, en tres (03) ejemplares de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del contribuyente'; 
$pdf->MultiCell(0,5,$txt);
$pdf->Ln($t); 

// FIRMA DEL JEFE
$cedula_gerente = $reg_gerente->ci_gerente;
$_SESSION['SEDE']=$_SESSION['SEDE_USUARIO'];
include "firma.php";
//---------------------------------

$pdf->SetRightMargin(20);
$pdf->SetLeftMargin(20);
$pdf->Ln(1);

$t = 7;

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