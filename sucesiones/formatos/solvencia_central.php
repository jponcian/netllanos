<?php
session_start();
if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
 
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');

class PDF extends FPDF
	{
	function Footer()
		{    
		//Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Times','I',8);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,0,sistema(),0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','nuevo');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,22);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=20);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFillColor(192,192,192);
$tamaño=-1;

////////// SIGLAS DE LA RESOLUCION
$consulta_datos = "SELECT Siglas_resol_Suc FROM z_siglas WHERE id_sector=".$_SESSION['SEDE'].";";
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
////////// SIGLAS DE LA RESOLUCION
$resolucion = $registro_datos->Siglas_resol_Suc;
// ---------------------

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT coordinador, funcionario, sucesiones_solvencias.indice, sucesiones_solvencias.sector, sucesiones_solvencias.anno, sucesiones_solvencias.numero, sucesiones_solvencias.fecha_registro, sucesiones_solvencias.certificado, sucesiones_solvencias.fecha_emision, sucesiones_solvencias.num_planilla, sucesiones_solvencias.fecha_dec, sucesiones_solvencias.num_planilla_sus, sucesiones_solvencias.fecha_dec_sus, sucesiones_solvencias.usuario, sucesiones_solvencias.fecha_proceso, expedientes_sucesiones.rif, expedientes_sucesiones.cedula, expedientes_sucesiones.sucesion, vista_contribuyentes_direccion.contribuyente FROM sucesiones_solvencias INNER JOIN expedientes_sucesiones ON expedientes_sucesiones.sector = sucesiones_solvencias.sector AND expedientes_sucesiones.numero = sucesiones_solvencias.numero AND sucesiones_solvencias.anno = expedientes_sucesiones.anno INNER JOIN vista_contribuyentes_direccion ON expedientes_sucesiones.rif = vista_contribuyentes_direccion.rif WHERE sucesiones_solvencias.anno=0".$_SESSION['ANNO_PRO']." AND sucesiones_solvencias.numero=0".$_SESSION['NUM_PRO']." AND sucesiones_solvencias.sector =0".$_SESSION['SEDE'].";";
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------

// ---------------------
$pdf->SetFont('Times','B',9-$tamaño);

$pdf->Cell(0,5,'RIF: G-20000303-0');
$pdf->Ln(5);
$pdf->Cell(0,5,$resolucion);
$pdf->Ln(10);
	
$pdf->SetFont('Times','B',13-$tamaño);

$pdf->Cell(0,5,'CERTIFICADO DE SOLVENCIA',0,0,'C'); 
$pdf->Ln(5);

$pdf->Cell(0,5,'IMPUESTO SOBRE SUCESIONES, DONACIONES',0,0,'C'); 
$pdf->Ln(5);

$pdf->Cell(0,5,'Y DEMAS RAMOS CONEXOS',0,0,'C'); 
$pdf->Ln(20);

$pdf->SetFont('Times','B',11-$tamaño);

$pdf->Cell(20,5,'');
$pdf->Cell(130,5,'N° DE EXPEDIENTE:',1,0,'C',true);
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(130,6,($registro_datos->anno.' / '.$registro_datos->numero),1,0,'C');
$pdf->Ln(6); 

$pdf->Cell(20,5,'');
$pdf->Cell(130,5,'N° DE PLANILLA:',1,0,'C',true);
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(130,6,($registro_datos->num_planilla),1,0,'C');
$pdf->Ln(6); 

$pdf->Cell(20,5,'');
$pdf->Cell(130,5,'N° DE PLANILLA SUSTITUTIVA:',1,0,'C',true);
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(130,6,($registro_datos->num_planilla_sus),1,0,'C');
$pdf->Ln(6); 

$pdf->Cell(20,5,'');
$pdf->Cell(130,5,'R.I.F.',1,0,'C',true);
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(130,6,formato_rif($registro_datos->rif),1,0,'C');
$pdf->Ln(6); 

$pdf->Cell(20,5,'');
$pdf->Cell(130,5,'NOMBRES Y APELLIDOS DEL CAUSANTE',1,0,'C',true);
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(130,6,($registro_datos->sucesion),1,0,'C');
$pdf->Ln(6); 

$pdf->Cell(20,5,'');
$pdf->Cell(130,5,'LUGAR Y FECHA DE EXPEDICIÓN',1,0,'C',true);
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(130,6,sector($_SESSION['SEDE']).', '.voltea_fecha($registro_datos->fecha_emision),1,0,'C');
$pdf->Ln(15); 

$pdf->SetFont('Times','',11-$tamaño);

$txt='Se expide este documento conforme a disposiciones contenidas en el Artículo 45 de la Ley de Impuesto Sobre Sucesiones, Donaciones y Demás Ramos Conexos.';
$pdf->MultiCell(0,5,$txt);

// FIRMA DEL JEFE
$pdf->Ln(20);
include "firma.php";
$pdf->SetRightMargin(22);
$pdf->SetLeftMargin(22);
$pdf->Ln(8);
//-----------

$pdf->SetFont('Times','',11-$tamaño);

$txt='"Artículo 50 LISSDDRC: Cada uno de los bienes que integran la masa hereditaria quedarán afectos para garantizar los derechos que correspondan a la República conforme a esta Ley inclusive las multas a que hubiere lugar"';
$pdf->MultiCell(0,5,$txt);
$pdf->Ln(8);
//-----------

$pdf->SetFont('Times','B',9);
$pdf->Cell(20,6,'');  	$pdf->Cell(70,6,'Recibido Por:',1);  			$pdf->Cell(70,6,'Entregado Por:',1); 			$pdf->Ln(6);									

$x = $pdf->getX();	$y = $pdf->getY();
$pdf->Cell(20,12,'');  	$pdf->Cell(70,6,'Nombre y Apellido:',0);  		$pdf->Cell(70,6,'Nombre y Apellido:',0); 		$pdf->Ln(12);

$pdf->SetXY($x,$y);
$pdf->Cell(20,12,'');  	$pdf->Cell(70,12,'',1);  		$pdf->Cell(70,12,'',1); 		$pdf->Ln(12);
			
$pdf->Cell(20,6,'');  	$pdf->Cell(70,6,'Cédula de Identidad:',1);  	$pdf->Cell(70,6,'Cédula de Identidad:',1); 		$pdf->Ln(6);									

$x = $pdf->getX();	$y = $pdf->getY();
$pdf->Cell(20,12,'');  	$pdf->Cell(70,6,'Teléfono:',0);  				$pdf->Cell(70,6,'Teléfono:',0); 				$pdf->Ln(10);									

$pdf->SetXY($x,$y);
$pdf->Cell(20,12,'');  	$pdf->Cell(70,12,'',1);  		$pdf->Cell(70,12,'',1); 		$pdf->Ln(12);

list($coordinador) = funcion_funcionario($registro_datos->coordinador) ;
list($funcionario) = funcion_funcionario($registro_datos->funcionario) ;

$coordinador = extraer_inciales($coordinador);
$funcionario = extraer_inciales($funcionario);

$pdf->Cell(20,12,$coordinador.'/'.strtolower($funcionario));

$pdf->Output();
?>