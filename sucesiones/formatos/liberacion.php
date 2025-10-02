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
$pdf=new PDF('P','mm','LETTER');
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
$consulta_datos = "SELECT expedientes_sucesiones.fecha_fall, expedientes_sucesiones.rif, coordinador, funcionario, sucesiones_liberacion.*, expedientes_sucesiones.cedula, expedientes_sucesiones.sucesion, vista_contribuyentes_direccion.contribuyente FROM sucesiones_liberacion INNER JOIN expedientes_sucesiones ON expedientes_sucesiones.sector = sucesiones_liberacion.sector AND expedientes_sucesiones.numero = sucesiones_liberacion.numero AND sucesiones_liberacion.anno = expedientes_sucesiones.anno INNER JOIN vista_contribuyentes_direccion ON expedientes_sucesiones.rif = vista_contribuyentes_direccion.rif WHERE sucesiones_liberacion.anno=0".$_SESSION['ANNO_PRO']." AND sucesiones_liberacion.numero=0".$_SESSION['NUM_PRO']." AND sucesiones_liberacion.sector =0".$_SESSION['SEDE'].";";
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------

////////// INFORMACION DE LA RECEPCION
$consulta_datos = "SELECT fecha_recepcion FROM sucesiones_recepcion WHERE rif='".$registro_datos->rif."';";
$tabla_x = mysql_query($consulta_datos);
$registro_x = mysql_fetch_object($tabla_x);
// ---------------------

// ---------------------
$pdf->Ln(5);
$pdf->SetFont('Times','B',13-$tamaño);
$pdf->Cell(0,5,'CERTIFICADO DE LIBERACION SUCESORAL',0,0,'C'); 
$pdf->Ln(20);

$pdf->SetFont('Times','',12-$tamaño);
$pdf->Cell(60,5,'');
$pdf->Cell(70,5,$resolucion,1,0,'C');
$pdf->Cell(0,5,'FECHA: '.voltea_fecha($registro_datos->fecha_emision),1,0,'C');
$pdf->Ln(20);
	
//$pdf->SetFont('Times','B',12-$tamaño);
$pdf->MultiCell(0,5,'CERTIFICADO DE LIBERACION SUCESORAL, que expide ésta Gerencia Regional de Tributos Internos '.buscar_region().', a favor de: '.($registro_datos->herederos).', únicos y universales herederos de: '.($registro_datos->contribuyente).', quien falleció AB-INTESTATO el día '.voltea_fecha($registro_datos->fecha_fall).', la declaración de herencia y solicitud de prescripción, ingresaron por ante la oficina fiscal competente el día '.voltea_fecha($registro_x->fecha_recepcion).' y contiene los siguientes rubros:',0,'J'); 
$pdf->Ln(10);

//$pdf->SetFont('Times','B',11-$tamaño);

$pdf->Cell(20,5,'');
$pdf->Cell(115,5,'ACTIVO......................................................................Bs.');
$pdf->Cell(20,5,formato_moneda($registro_datos->activo),0,0,'R');
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(115,5,'PASIVO.......................................................................Bs.');
$pdf->Cell(20,5,formato_moneda($registro_datos->pasivo),0,0,'R');
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(115,5,'DESGRAVAMEN.......................................................Bs.');
$pdf->Cell(20,5,formato_moneda($registro_datos->desgravamen),0,0,'R');
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(115,5,'LIQUIDO HEREDITARIO.........................................Bs.');
$pdf->Cell(20,5,formato_moneda($registro_datos->liquido),0,0,'R');
$pdf->Ln(5);

$pdf->Cell(20,5,'');
$pdf->Cell(115,5,'DISTRIBUCION FISCAL: CUOTA PARTE.............Bs.');
$pdf->Cell(20,5,formato_moneda($registro_datos->cuota),0,0,'R');
$pdf->Ln(5);


$pdf->SetFont('Times','',11-$tamaño);

$pdf->Ln(10);
$txt='No hubo derechos que liquidar a favor del Fisco Nacional, por estar consumada y alegada la prescripción, de conformidad con lo establecido en el Artículo 56 del Código Orgánico Tributario y el Artículo 98 de la Ley de Impuesto Sobre Sucesiones, Donaciones y Demás Ramos Conexos vigente, y en cumplimiento a lo ordenado en la Resolución Nº '.($registro_datos->resolucion).', de fecha '.voltea_fecha($registro_datos->fecha_res).', emitida por la División Jurídico Tributario.';
$pdf->MultiCell(0,5,$txt);

// FIRMA DEL JEFE
$pdf->Ln(15);
include "../../funciones/firma_gerente.php";
$pdf->SetRightMargin(22);
$pdf->SetLeftMargin(22);
$pdf->Ln(12);
//-----------

$pdf->Cell(0,5,'N° DE EXPEDIENTE:'.$registro_datos->anno.' / '.$registro_datos->numero);
$pdf->Ln(5); 
//-----------

// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR
$consulta_x = "SELECT * FROM vista_jefe_rec WHERE id_sector=".$_SESSION['SEDE'].";";
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);
$jefe_div = $registro_x->jefe;
//---------------------------------
list($coordinador) = funcion_funcionario($registro_datos->coordinador) ;
list($funcionario) = funcion_funcionario($registro_datos->funcionario) ;

$gerente = extraer_inciales($jefe);
$jefe = extraer_inciales($jefe_div);
$coordinador = extraer_inciales($coordinador);
$funcionario = extraer_inciales($funcionario);

$pdf->Cell(20,5,$gerente.'/'.$jefe.'/'.$coordinador.'/'.strtolower($funcionario));

$pdf->Output();
?>