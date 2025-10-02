<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
//include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");


//if ($_SESSION['VERIFICADO'] != "SI") { 
  //  header ("Location: index.php?errorusuario=val"); 
    //exit(); 
	//}

class FPDF_CellFit extends FPDF
{
	//Cell with horizontal scaling if text is too wide
    function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);

        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;

        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max(strlen($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }

        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);

        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }

    //Cell with horizontal scaling only if necessary
    function CellFitScale($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,false);
    }

    //Cell with horizontal scaling always
    function CellFitScaleForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,true);
    }

    //Cell with character spacing only if necessary
    function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }

    //Cell with character spacing always
    function CellFitSpaceForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        //Same as calling CellFit directly
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,true);
    }

function Header()
	{
	//--------
	$consulta = "SELECT * FROM bn_areas WHERE id_area=".$_GET['area'];
	$tabla = mysql_query ($consulta);
	$registro = mysql_fetch_object($tabla);
	
		$area = $registro->descripcion;
		$division = $registro->division;
		
		$consulta_xj = "SELECT * FROM vista_jefes WHERE division=$division ";
		$tabla_xj = mysql_query($consulta_xj);
		$registro_xj = mysql_fetch_object($tabla_xj);
//estilo cabecera 
		$fuente_cabecera = 8;
		$alto_cabecera = 3.5;
		
		global $color;
		
		$a=18 ; //codigo 	
		$b=70 ; //denominacion
		$c=20 ; //cedula	
		$d=65 ; //nombres
		
		//------------------------
		//$this->SetFillColor(170,166,166);
		$this->Image('../../imagenes/logo.jpeg',20,18,40);
		$this->Image('../../imagenes/cuadro_lleno.jpg',92,25,3);
		$this->Image('../../imagenes/cuadro_vacio.jpg',152,25,3);
		//----------------------------
		$this->SetY(20);
		
		//---------------------------
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell(45,$alto_cabecera,'',0,0,'C');
		$this->Cell(160,$alto_cabecera,utf8_decode('COMPROBANTE DE ASIGNACIÓN DE BIENES NACIONALES AL RESPONSABLE PATRIMONIAL POR USO'),0,0,'C');
		$this->Cell(0,$alto_cabecera,'No '.$this->PageNo().' de {nb}',0,0,'C');	
		$this->Ln(4);
		
		//---------------------------
		$this->SetFont('Arial','',$fuente_cabecera);
		$this->Cell(65,$alto_cabecera,'',0,0,'C');
		$this->Cell(60,$alto_cabecera,'BIENES MUEBLES',1,0,'C');
		$this->Cell(60,$alto_cabecera,'MATERIALES',1,0,'C');
		$this->Cell(20,$alto_cabecera,'',0,0,'C');
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell(0,$alto_cabecera,'Fecha: '.date('d/m/Y'),0,0,'C');
		//$this->Cell(0,$alto_cabecera,('Fecha: 02/09/2025'),0,0,'C');
		
		$this->Ln(8);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell(0,$alto_cabecera,'ORGANISMO',1,0,'L');
		$this->Ln($alto_cabecera);
		
		$x=$this->GetX();
		$y=$this->GetY();
		
		$this->SetFont('Arial','',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'Codigo',0,0,'L');
		$this->Cell(0,$alto_cabecera,'Denominacion',0,0,'L');	
		$this->Ln($alto_cabecera);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'07',0,0,'R');
		$this->Cell(0,$alto_cabecera,'MINISTERIO DEL PODER POPULAR DE ECONOMIA Y FINANZAS',0,0,'C');		
		
		$this->SetY($y);
		$this->SetX($x);
		
		$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
		$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
		$this->Ln($alto_cabecera*2);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell(0,$alto_cabecera,'UNIDAD ADMINISTRADORA',1,0,'L');
		$this->Ln($alto_cabecera);
		
		$x=$this->GetX();
		$y=$this->GetY();
		
		$this->SetFont('Arial','',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'Codigo',0,0,'L');
		$this->Cell(0,$alto_cabecera,'Denominacion',0,0,'L');	
		$this->Ln($alto_cabecera);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'98067',0,0,'R');
		$this->Cell(0,$alto_cabecera,utf8_decode('SERVICIO NACIONAL INTEGRADO DE ADMINISTRACIÓN ADUANERA Y TRIBUTARIA (SENIAT)'),0,0,'C');		
		
		$this->SetY($y);
		$this->SetX($x);
		
		$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
		$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
		$this->Ln($alto_cabecera*2);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell(0,$alto_cabecera,'DEPENDENCIA USUARIA',1,0,'L');
		$this->Ln($alto_cabecera);
		
		$x=$this->GetX();
		$y=$this->GetY();
		
		$this->SetFont('Arial','',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'Codigo',0,0,'L');
		$this->Cell(0,$alto_cabecera,'Denominacion',0,0,'L');	
		$this->Ln($alto_cabecera);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'32000000',0,0,'R');
		
		$this->Cell(0,$alto_cabecera, $area,0,0,'C');
				
		$this->SetY($y);
		$this->SetX($x);
		
		$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
		$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
		$this->Ln($alto_cabecera*2);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell(0,$alto_cabecera,'RESPONSABLE DEL ALMACEN',1,0,'L');
		$this->Ln($alto_cabecera);
		
		$x=$this->GetX();
		$y=$this->GetY();
		
		$this->SetFont('Arial','',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'Codigo',0,0,'L');
		$this->Cell($b,$alto_cabecera,'Denominacion',0,0,'L');
		$this->Cell($c,$alto_cabecera,'C.I.',0,0,'L');
		$this->Cell($d,$alto_cabecera,'Apellidos y Nombres',0,0,'L');
		$this->Cell(0,$alto_cabecera,'Cargo',0,0,'L');	
		$this->Ln($alto_cabecera);
		
		$this->SetFont('Arial','B',$fuente_cabecera);
		$this->Cell($a,$alto_cabecera,'',0,0,'R');
		$this->Cell($b,$alto_cabecera,$registro_xj->descripcion,0,0,'L');
		$this->Cell($c,$alto_cabecera,$registro_xj->cedula,0,0,'L');
		$this->Cell($d,$alto_cabecera,$registro_xj->jefe,0,0,'L');
		$this->Cell(0,$alto_cabecera,$registro_xj->cargo,0,0,'L');	
		
		$this->SetY($y);
		$this->SetX($x);
		
		$this->Cell($a,$alto_cabecera*2,'',1,0,'L');
		$this->Cell($b,$alto_cabecera*2,'',1,0,'L');
		$this->Cell($c,$alto_cabecera*2,'',1,0,'L');
		$this->Cell($d,$alto_cabecera*2,'',1,0,'L');
		$this->Cell(0,$alto_cabecera*2,'',1,0,'L');	
		$this->Ln($alto_cabecera*2);
		
		//formato titulo
		
		$a=18 ; //cantidad 	
		$b=20 ; //codigo
		$c=25 ; //bien	
		$d=130 ; //descripcion
		$e=20 ; //conservacion
		//$e=23 ; //conservacion original
		$f=0 ; //valor
		
		$this->SetFont('Arial','B',$fuente_cabecera-0.5);
		
		$this->cell($a,12,'Cantidad',1,0,'C');
		
		$x=$this->GetX();
		$y=$this->GetY();
		$this->multicell($b,6,utf8_decode('Código del Catalago'),1,'C');
		$this->SetY($y);
		$this->SetX($x+$b);
		
		$x=$this->GetX();
		$y=$this->GetY();
		$this->multicell($c,4,utf8_decode('Número de Inventario (solo para bienes)'),1,'C');
		$this->SetY($y);
		$this->SetX($x+$c);
		
		$this->cell(strtoupper($d),12,utf8_decode('DESCRIPCIÓN'),1,0,'C');
		
		$x=$this->GetX();
		$y=$this->GetY();
		$this->multicell($e,6,utf8_decode('Estado de Conservación'),1,'C');
		$this->SetY($y);
		$this->SetX($x+$e);
		
		$x=$this->GetX();
		$y=$this->GetY();
		$this->multicell($f,12,'Valor Unitario',1,'C');
		
//fin diseno cabecera desde la linea 94
//*******************************************************************
  
	$fuente_cabecera = 8;
$alto_cabecera = 4;
$a=35 ; 	
$b=100 ; 
$c=80 ;

//----------------------------
	$consulta_x = "SELECT * FROM z_empleados WHERE cedula=".$_SESSION['funcionario']."";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
//----------------------------
$this->SetY(-37.8);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(0,$alto_cabecera,'Responsable Patrimonial Primario',1,0,'L');
$this->Ln($alto_cabecera);

$this->Cell($a,$alto_cabecera,'Cedula de Identidad',1,0,'L');
$this->Cell($b,$alto_cabecera,'Apellidos y Nombres',1,0,'L');
$this->Cell($c,$alto_cabecera,'Cargo',1,0,'L');	

$y=$this->GetY();
$this->Cell(0,$alto_cabecera*2+2,'Firma',1,0,'L');	
$this->SetY($y);

$this->Ln($alto_cabecera);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell($a,$alto_cabecera+2,'V-'.formato_cedula($registro_x->cedula),1,0,'L');
$this->Cell($b,$alto_cabecera+2,$registro_x->Nombres.' '.$registro_x->Apellidos,1,0,'L');
$this->CellFitScale($c,$alto_cabecera+2,$registro_x->Cargo,1,0,'L');	
$this->Ln($alto_cabecera+2);

	//----------------------------
	$consulta_x = "SELECT * FROM z_empleados WHERE (division=9 and cedula=12991310);";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//----------------------------
	
	$this->SetFont('Arial','B',$fuente_cabecera); 
	$this->Cell($a+$b+$c,$alto_cabecera+3,'Preparado por:         '.$registro_x->Nombres.' '.$registro_x->Apellidos.'         C.I. V-'.formato_cedula($registro_x->cedula).'         COORDINADOR BIENES NACIONALES '.strtoupper((buscar_region())),1,0,'L');
	$this->Cell(0,$alto_cabecera+3,'Firma y Sello',1,0,'L');	
	
//**************************************************** */ */


	}	
	
function Footer()
	{}	

}

$pdf=new FPDF_CellFit('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,10);


//////// ---- DETALLE
$consulta_xD = "SELECT inf_ci_asignado FROM bn_bienes WHERE borrado=0 AND inf_ci_asignado>0 AND id_area=".$_GET['area'] ." GROUP BY inf_ci_asignado ORDER BY inf_ci_asignado";
$tabla_xD = mysql_query($consulta_xD);

while ($registro_xD = mysql_fetch_object($tabla_xD))
{
$funcionario = $registro_xD->inf_ci_asignado;
$_SESSION['funcionario'] = $registro_xD->inf_ci_asignado;

$pdf->AddPage();
$pdf->SetY(86);

$_SESSION['monto'] = 0;	$linea = 1; $alto = 4; $_SESSION['i']=0;

$a=18 ; //cantidad 	
$b=20 ; //codigo
$c=25 ; //bien	
$d=130 ; //descripcion
$e=20 ; //conservacion
//$e=23 ; //conservacion original
$f=0 ; //valor

//////// ---- DETALLE
$consulta_x = "SELECT * FROM vista_bienes_nacionales_rpu WHERE id_area=".$_GET['area'] ." AND inf_ci_asignado=".$funcionario;
$tabla_x = mysql_query($consulta_x);

while ($registro_x = mysql_fetch_object($tabla_x))
{

	//++++++++++++++++++++++++++
	if ($y1 > 170 or $y2 > 170) 
		{ 
			$pdf->SetFont('Arial','B',$fuente_cabecera);
			$pdf->Cell($a,$alto,$_SESSION['i'],1,0,'C');
			$pdf->Cell($b,$alto,'',1,0,'L');
			$pdf->Cell($c,$alto,'',1,0,'L');
			$pdf->Cell(120,$alto,'VAN',1,0,'C');
			$pdf->Cell(28,$alto,'SUBTOTAL',1,0,'C');	
			$pdf->Cell(0,$alto,formato_moneda_bienes($_SESSION['monto']),1,0,'R');	
			
			///----------------------------------
			$pdf->AddPage();  
			$y1=86;	
			$pdf->SetY($y1);
		}
	//----------------------------------
	
	//-------------------
	$pdf->SetFont('Times','',9);

	//--- PARA CALCULAR LA ALTURA MAXIMA DEL MULTICELL
	$pdf->SetTextColor(255,255,255);
	$y1=$pdf->GetY();
	$pdf->MultiCell($d,$alto, ucfirst(strtolower($registro_x->descripcion_bien)),0,'J');
	$y2=$pdf->GetY();
	$alto2 = ($y2-$y1);
	$pdf->SetTextColor(0,0,0);
	//----------------------------------
	
	//----- PARA ARRANCAR CON LA LINEA
	$pdf->SetY($y1);
	//----------------------------------------
	$pdf->Cell($a,($alto2),'01',$linea,0,'C');
	$pdf->Cell($b,($alto2), $registro_x->codigo,$linea,0,'C'); 
	$pdf->Cell($c,($alto2), $registro_x->numero_bien,$linea,0,'C'); 
	//--- --------------------------------------MULTICELL
	$x=$pdf->GetX();
	$pdf->MultiCell($d,$alto, ucfirst(strtolower($registro_x->descripcion_bien)),$linea,'J');
	$y2=$pdf->GetY();
	//- PARA PONER LAS COORDENADAS DESPUES DEL MULTICELL
	$pdf->SetY($y1);
	$pdf->SetX($x+$d);
	//---------------------------------------------------
	$pdf->Cell($e,($alto2), $comprobante,$linea,0,'C'); 
	$pdf->Cell($f,($alto2), $concepto,$linea,0,'C'); 
	//$pdf->Cell($g,($alto2), formato_moneda($registro_x->valor),$linea,0,'R'); 
	$pdf->Cell($h,($alto2), formato_moneda_bienes($registro_x->valor,''),$linea,0,'R'); 
	//--------------------
	$_SESSION['monto'] = $_SESSION['monto']+($registro_x->valor);
	//---------------------
	$pdf->Ln($alto2);
	$_SESSION['i']++;
	$y1=$pdf->GetY();
}

while ($pdf->GetY()<=170)
	{
	//----------- LINEA EN BLANCO
	$pdf->Cell($a,4,'',1,0,'C');
	$pdf->Cell($b,4,'',1,0,'L');
	$pdf->Cell($c,4,'',1,0,'L');
	$pdf->Cell($d,4,'',1,0,'C');
	$pdf->Cell($e,4,'',1,0,'L');	
	$pdf->Cell($f,4,'',1,0,'R');
	$pdf->Cell($g,4,'',1,0,'L');	
	$pdf->Cell($h,4,'',1,0,'R');	
	$pdf->Ln(4);
	//----------------------
	$i++;
	}
				
// TOTAL GENERAL
$pdf->SetY(-41.8);
$pdf->SetFont('Arial','B',$fuente_cabecera);
$pdf->Cell($a,$alto,$_SESSION['i'],1,0,'C');
$pdf->Cell($b+$c,$alto,'TOTAL CANTIDAD',1,0,'L');
$pdf->Cell($d+$e+$f,$alto,'TOTAL',1,0,'R');
//$pdf->Cell($g,$alto,formato_moneda_bienes($_SESSION['monto']),1,0,'R');	
$pdf->Cell($h,$alto,formato_moneda_bienes($_SESSION['monto']),1,0,'R');	
//----------------------------------
}

$pdf->Output();

?>