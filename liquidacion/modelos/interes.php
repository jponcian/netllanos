<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
 
include "../../conexion.php";
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
require('../../funciones/fpdf.php');

setlocale(LC_TIME, 'sp_ES','sp', 'es');
mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
{
	//---------- ENCABEZADO
	function Header()
	{  
	// LOGO
	$this->Image('../../imagenes/logo.jpeg',17,8,55);
	}	
	
	//---------- PIE
	function Footer()
	{    
	$this->SetY(-12);
	//Arial itálica 8
	$this->SetFont('Times','I',10);
	$this->SetTextColor(120);
	$this->Cell(360,10,sistema().' ',0,0,'C');
	}
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,10,14);
$pdf->SetAutoPageBreak(1,5);
$pdf->SetFillColor(210);
// -------------------------------------------------------------------------------
	$linea_muestra = 0;
	$linea_fija = 1;
	//-------------
	// INFORMACION DE LA LIQUIDACION
	$consulta = "SELECT * FROM vista_liquidacion_planillas WHERE id_resolucion=0 AND serie=38 and anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." AND origen_liquidacion=".$_SESSION['ORIGEN'];
	$tabla = mysql_query( $consulta);
	
	// --------- RESOLUCION Y FECHA DEPENDE DEL ORIGEN
	list ($sigla_resolucion, $fecha_resolucion) = funcion_resolucion( $_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
	// --------
	
	while ($registro = mysql_fetch_object($tabla))
		{
		// ---------- 
		include('0-encabezado.php');
		
		// --------- DEMOSTRATIVA DE LOS INTERESES
		include('0-cuerpo_intereses.php');
	
		// --------- PIE DE PAGINA
		
		/*if ($registro->tipo >= 2000 and $registro->tipo <= 2406 and $registro->sector==1 or  $registro->sector==2 or $registro->sector==3 or  $registro->sector==4 or $registro->sector==5) {
	include "0-firma.php"; 
	}
	else { include "0-firma_gerente.php";}
		
		
		//include('0-firma.php');*/
		
		
			if ($_SESSION['ORIGEN'] == 4)
		{
		if ($_SESSION['SEDE']==1)
			{
			$consulta1 = "SELECT tipo FROM expedientes_fiscalizacion WHERE tipo<=2406 and tipo>=2000  and anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=1";
			$tabla1 = mysql_query($consulta1); //AND id_resolucion=0 
			if (mysql_num_rows($tabla1)>0)
				{	include "0-firma.php"; }
			else { 
				//include "0-firma_gerente.php"; 
				include "0-firma.php";
				}
			}
		else { 
			include "0-firma.php"; 
			}
		}
	else { 
		include "0-firma.php"; 
		}
		
		}
		
			//if ($registro->tipo >= 2000 and $registro->tipo <= 2406 and $registro->sector==1 or  $registro->sector==2 or $registro->sector==3 or  $registro->sector==4 or $registro->sector==5) {

	
	//fin alex

$pdf->Output();
?>