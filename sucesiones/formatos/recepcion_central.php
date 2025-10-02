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
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,0,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=20);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFont('Times','B',12);

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT * FROM vista_re_sucesiones_recepcion WHERE rif ='".$_SESSION['RIF']."';";
$tabla_datos = mysql_query($consulta_datos);
$numero_filas = mysql_num_rows($tabla_datos);
//----------------
if ($numero_filas>0)
	{
	//--------------------------
	$registro_datos = mysql_fetch_object($tabla_datos);
	//--------------------------
	$sector = $registro_datos->sector;
	$declaracion = $registro_datos->declaracion;
	$fecha_dec = $registro_datos->fecha_declaracion;
	// ---------------------
		//----- SE BUSCA PARA VER SI EXISTE EL EXPEDIENTE SE CREA O SE MODIFICA
		$consulta_x = "SELECT indice FROM expedientes_sucesiones WHERE rif ='".$_SESSION['RIF']."';";
		$tabla_x = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla_x);
		//----------------
		if ($numero_filas>0)
			{
			//--------------------------
			$registro = mysql_fetch_object($tabla_x);
			//--------------------------
			$consulta_x = "UPDATE expedientes_sucesiones SET cedula=".$_SESSION['OCEDULAC'].", fecha_fall='".$_SESSION['OFECHAF']."', sector='".$sector."', declaracion='".$declaracion."', fecha_declaracion='".($fecha_dec)."', funcionario=".$_SESSION['CEDULA_USUARIO']." WHERE indice=".$registro->indice.";";
			$tabla_x = mysql_query($consulta_x);
			//--------------------------
			}
		else
			{
			// CONSULTA DEL EXPEDIENTE SIGUIENTE
			// AQUI CAMBIAMOS LA VARIABLE POR SI QUEREMOS EL CORRELATIVO SEGUN EL AÑO DE FALLECIMIENTO		anno($_SESSION['OFECHAF'])	date('Y')
			$consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM expedientes_sucesiones WHERE sector='.$sector.' AND anno='.date('Y').';';
			//echo $consulta_x;
			$tabla_x = mysql_query ($consulta_x);
			$registro_x = mysql_fetch_array($tabla_x);
			//-------------
				if ($registro_x['Maximo']>0)
					{$Maximo = $registro_x['Maximo'];}
				else
					{$Maximo = 1;}
			// FIN
			// NOMBRE DEL CAUSANTE
			$consulta_x = 'SELECT sucesion FROM vista_contribuyentes_direccion WHERE rif="'.$_SESSION['RIF'].'";';
			$tabla_x = mysql_query ( $consulta_x);	
			$registro_x = mysql_fetch_object($tabla_x);	
			//----------------------
			$consulta_x = "INSERT INTO expedientes_sucesiones (cedula, fecha_fall, declaracion, fecha_declaracion, anno, numero, funcionario, rif, fecha_registro, usuario, sector, status, sucesion ) VALUES ('".$_SESSION['OCEDULAC']."', '".$_SESSION['OFECHAF']."', '".$declaracion."', '".($fecha_dec)."', ".date('Y').", ".$Maximo.", ".$_SESSION['CEDULA_USUARIO'].", '".$_SESSION['RIF']."', date(now()), ".$_SESSION['CEDULA_USUARIO'].", ".$sector.", 0, '".$registro_x->sucesion."');";
			}
		$tabla_x = mysql_query($consulta_x);
		//echo $consulta_x;
	// ---------------------
	$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
	$pdf->SetFont('Times','B',11);
	$pdf->SetY(20);
	
	//---- DATOS DEL EXPEDENTE
	$consulta_x = "SELECT anno, numero FROM expedientes_sucesiones WHERE rif ='".$_SESSION['RIF']."';";
	$tabla_x = mysql_query($consulta_x);
	$registro = mysql_fetch_object($tabla_x);
	$pdf->Cell(0,5,'Expediente: '.$registro->anno.' / '.$registro->numero,0,0,'R'); 
	$pdf->Ln(6);
	//-----------
		
	//----- DATOS DE LA DECLARACION
	$pdf->Cell(0,5,'N° de Declaración: '.$registro_datos->declaracion.' de Fecha: '.voltea_fecha($registro_datos->fecha_declaracion),0,0,'R'); 
	$pdf->Ln(12);
	
	////////// GERENCIA, SECTOR O UNIDAD DE EMISION
	if ($registro_datos->sector>1)
		{$pdf->Cell(0,5,$registro_datos->titulo.' '.mayuscula($registro_datos->nombre),0,0,'L'); }
			else
				{$pdf->Cell(0,5,$registro_datos->titulo.' '.mayuscula(buscar_region()),0,0,'L'); }
	$pdf->Ln(5);
	$pdf->Cell(0,5,mayuscula($registro_datos->tipo_division).' DE RECAUDACIÓN',0,0,'L'); 
	$pdf->Ln(10);
	
	$pdf->SetFont('Times','B',14);
	$pdf->Cell(0,5,'ACTA DE RECEPCIÓN',0,0,'C'); 
	$pdf->Ln(12);
	
	$pdf->SetFont('Times','',11);
		
	$txt='Hoy, '.voltea_fecha($registro_datos->fecha_recepcion).', el (la) funcionario(a) '.$registro_datos->Apellidos.' '.$registro_datos->Nombres.', titular de la Cédula de Identidad N° '.formato_cedula($registro_datos->funcionario).', adscrito(a) '.$registro_datos->adscripcion.' '.$registro_datos->conector2_es.' Gerencia Regional de Tributos Internos de la '.buscar_region().', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), hace constar que ha recibido por parte del sujeto pasivo '.mayuscula($registro_datos->contribuyente).', con RIF: '.formato_rif($registro_datos->rif). ', domiciliado(a) en '.mayuscula(trim($registro_datos->direccion)).', la siguiente documentación para la tramitación de Presentación de Declaración Sucesoral:';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	
	$pdf->SetFont('Times','B',11);
	$pdf->Cell(0,5,'Recaudos Presentados');	
	$pdf->Ln(3);
	$pdf->SetFont('Times','',10);
	$i=1;
	
	////////// INFORMACION DEL EXPEDIENTE
	$consulta_datos = "SELECT * FROM vista_re_sucesiones_recepcion WHERE rif ='".$_SESSION['RIF']."' and tipo_req=1;";
	$tabla_datos = mysql_query($consulta_datos);
	while ($registro_datos = mysql_fetch_object($tabla_datos))
		{
		$pdf->Ln(5);
		$pdf->Cell(0,5,$i.' - '.ucfirst($registro_datos->descripcion));	
		$i++;
		}
	
	$pdf->SetY(-58);
	$pdf->SetFont('Times','B',9);
	$pdf->Cell(110,5,'Por el Contribuyente:');  										
	$pdf->Cell(0,5,'Firma Funcionario:'); 
	$pdf->Ln(7);
	$pdf->SetFont('Times','',8);
	$pdf->Cell(110,5,'Firma:          __________________________________');
	$pdf->Cell(0,5,'Sello:');	
	$pdf->Ln(5);
	$pdf->Cell(110,5,'Nombre:      __________________________________');				
	$pdf->Ln(5);
	$pdf->Cell(110,5,'C.I. N°:        __________________________________'); 		
	$pdf->Ln(5);
	$pdf->Cell(110,5,'Cargo:          __________________________________'); 	
	$pdf->Ln(5);
	$pdf->Cell(110,5,'Teléfono:     __________________________________'); 	
	$pdf->Ln(5);
	$pdf->Cell(110,5,'Fecha:          __________________________________');
		
	// FIN DE LA RESOLUCION
	
	$pdf->Output();
	
	}
else
	{
	echo "<script type=\"text/javascript\">alert('No se han consignado recaudos!');</script>";	
	}
?>