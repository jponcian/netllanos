<?php
session_start();
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');
mysql_query("SET NAMES 'latin1'");

$_SESSION['AREA'] = $_GET['area'];
$_SESSION['DIVISION'] = $_GET['division'];
$_SESSION['SEDE'] = $_GET['sede'];

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}

class PDF extends FPDF
{
function Header()
	{
	$comprobante = 'INVENTARIO';
	include "../formatos/cabecera.php";
	include "../formatos/titulos.php";
	}	
	
function Footer()
	{   
	include "../formatos/pie.php";
	//Posici�n a 1,5 cm del final
	$this->SetY(-14);
	//Arial it�lica 8
	$this->SetFont('Times','I',9);
	//Color del texto en gris
	$this->SetTextColor(120);
	//N�mero de p�gina
	$this->Cell(460,10,sistema().' '.$this->PageNo().' de {nb}',0,0,'C');
	}		
}

// INICIO
$pdf=new PDF('L','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
//-------------------

//---------- FILTRO POR DEPENDENCIA
if ($_SESSION['SEDE']=='0')
	{
	$_SESSION['i'] = 0;
	$_SESSION['monto'] = 0;	
	//-- CUERPO
	include "x_inventario_resumen_region.php";
	//------------------------
	$consulta_div = "SELECT bn_areas.division FROM bn_areas, z_jefes_detalle WHERE bn_areas.division = z_jefes_detalle.division GROUP BY division ORDER BY id_sector, z_jefes_detalle.division"; 
	//echo '<br><br> Principal => '.$consulta_div;
	$tabla_div = mysql_query($consulta_div);
	while ($registro_div = mysql_fetch_object($tabla_div))
		{
		$_SESSION['DIVISION'] = $registro_div->division;
		//-- CUERPO
		include "x_inventario_cuerpo.php";
		//-- RESUMEN
		include "x_inventario_resumen.php";
		//----------
		$_SESSION['AREAS']='TODAS';
		$_SESSION['monto'] = 0;		$_SESSION['i']=0;
		$_SESSION['AREA'] = 0;
		}
	}
else
	{
	//---------- FILTRO POR DIVISION
	if ($_SESSION['DIVISION']=='0')
		{
		$consulta_div = "SELECT bn_areas.division FROM bn_areas, z_jefes_detalle WHERE bn_areas.division = z_jefes_detalle.division AND z_jefes_detalle.id_sector = ".$_SESSION['SEDE'].' GROUP BY division ORDER BY id_sector, z_jefes_detalle.division'; 
		//echo '<br><br> Principal => '.$consulta_div;
		$tabla_div = mysql_query($consulta_div);
		while ($registro_div = mysql_fetch_object($tabla_div))
			{
			$_SESSION['DIVISION'] = $registro_div->division;
			//-- CUERPO
			include "x_inventario_cuerpo.php";
			//-- RESUMEN
			include "x_inventario_resumen.php";
			//----------
			$_SESSION['AREAS']='TODAS';
			$_SESSION['monto'] = 0;		$_SESSION['i']=0;
			$_SESSION['AREA'] = 0;
			}
		}
	else
		{
		//-- CUERPO
		include "x_inventario_cuerpo.php";
		//-- RESUMEN
		include "x_inventario_resumen.php";
		}
	}

$pdf->Output();
?>