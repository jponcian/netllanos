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
function Header()
	{
	$id_reasignacion = $_GET['id'];
	$consulta_x = "SELECT * FROM vista_bienes_reasignaciones_solicitadas WHERE id_reasignacion=".$id_reasignacion; //echo $consulta_x;
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//-----------------------
	if ($_GET['comprobante']==21 )
		{	
		$comprobante = 'REASIGNACIÓN';	
		$area = $registro_x->area_destino;
		$division = $registro_x->division_destino;
		}
	//-----------------------
	if ($_GET['comprobante']==31)
		{	
		$comprobante = 'REASIGNACIÓN';	
		$area = $registro_x->area_actual;
		$division = $registro_x->division_actual;
		}
	//---------------
	include "../formatos/cabecera.php";
	include "../formatos/titulos.php";
	}	
	
function Footer()
	{   
	$id_reasignacion = $_GET['id'];
	$consulta_x = "SELECT usuario, id_division_destino, id_division_actual FROM vista_bienes_reasignaciones_solicitadas WHERE id_reasignacion=".$id_reasignacion;
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//-----------------------
	$funcionario = $registro_x->usuario;
	//-----------------------
	if ($_GET['comprobante']==21) {	$comprobante = '21';	$_SESSION['DIVISION'] = $registro_x->id_division_destino;	}
	if ($_GET['comprobante']==31) {	$comprobante = '31';	$_SESSION['DIVISION'] = $registro_x->id_division_actual; $_SESSION['DIVISION2'] = $registro_x->id_division_destino;	}
	//-----------------------
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

//-- CUERPO
if ($_GET['comprobante']==21) {	$comprobante = '21';	$concepto = 'Recepcion'; 		}
if ($_GET['comprobante']==31) {	$comprobante = '31';	$concepto = 'Entrega';			}

include "x_reasignacion_cuerpo.php";

$pdf->Output();

?>