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
	function Header()
		{		
		//----------------------------------------------------------------------
		$this->Image('../../imagenes/logo.jpeg',20,8,65);
		//---- DATOS DEL EXPEDENTE
		$consulta_x = "SELECT anno, numero FROM expedientes_sucesiones WHERE rif ='".$_SESSION['RIF']."';";
		$tabla_x = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla_x);
		if ($numero_filas>0)
			{
			$registro = mysql_fetch_object($tabla_x);
			$expediente = $registro->anno.' / '.$registro->numero;
			}
		else
			{			
			// CONSULTA DEL EXPEDIENTE SIGUIENTE
			$consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM expedientes_sucesiones WHERE sector='.$_SESSION['SEDE'].' AND anno='.date('Y').';';
			$tabla_x = mysql_query ($consulta_x);
			$registro_x = mysql_fetch_array($tabla_x);
			//-------------
				if ($registro_x['Maximo']>0)
					{$Maximo = $registro_x['Maximo'];}
				else
					{$Maximo = 1;}
			// FIN
			//----------------------
			$consulta_x = "INSERT INTO expedientes_sucesiones (anno, numero, usuario, rif, sector, status ) VALUES (".date('Y').", ".$Maximo.", ".$_SESSION['CEDULA_USUARIO'].", '".$_SESSION['RIF']."', ".$_SESSION['SEDE'].", 0);";
			//$tabla_x = mysql_query($consulta_x);
			//------------
			$expediente = date('Y').' / '.$Maximo;
			}

		//-----------
		$this->SetY(15);
		//Arial itálica 8
		$this->SetFont('Times','B',10);
		//Número de página
		$this->Cell(0,5,'Expediente: '.$expediente ,0,0,'R'); 
		$this->SetFont('Times','B',11);
		$this->SetY(30);
		//----------------
		$this->SetFont('Times','B',14);
		$this->Cell(0,5,'AUTO DE RECEPCIÓN',0,0,'C'); 
		$this->Ln(20); 
		}
	
	function Footer()
		{    
		//Posición a 1,5 cm del final
		$this->SetY(-12);
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
$pdf->SetTitle('Solvencia Sucesoral');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=15);

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
	// GUARDADO DE LAS OBSERVACIONES
	$consulta = "UPDATE sucesiones_recepcion SET observaciones='".$_POST['OOBSERVACIONES']."' WHERE rif='".$_SESSION['RIF']."';";
	$tabla = mysql_query ($consulta);
	//--------------------------
	$registro_datos = mysql_fetch_object($tabla_datos);
	//--------------------------
	$funcionario = $registro_datos->Nombres.' '.$registro_datos->Apellidos;
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
			// COORDINADOR DEL AREA
			$consulta_x = "SELECT cedula FROM z_empleados WHERE (id_origen = 3 and rol='C') or (id_origen2 = 3 and rol='C') or (id_origen3 = 3 and rol='C');";
			$tabla_x1 = mysql_query ( $consulta_x);	
			$registro_x1 = mysql_fetch_object($tabla_x1);	
			// NOMBRE DEL CAUSANTE
			$consulta_x = 'SELECT sucesion FROM vista_contribuyentes_direccion WHERE rif="'.$_SESSION['RIF'].'";';
			$tabla_x = mysql_query ( $consulta_x);	
			$registro_x = mysql_fetch_object($tabla_x);	
			//----------------------
			$consulta_x = "INSERT INTO expedientes_sucesiones (coordinador, cedula, fecha_fall, declaracion, fecha_declaracion, anno, numero, funcionario, rif, fecha_registro, usuario, sector, status, sucesion ) VALUES ('".$registro_x1->cedula."', '".$_SESSION['OCEDULAC']."', '".$_SESSION['OFECHAF']."', '".$declaracion."', '".($fecha_dec)."', ".date('Y').", ".$Maximo.", ".$_SESSION['CEDULA_USUARIO'].", '".$_SESSION['RIF']."', date(now()), ".$_SESSION['CEDULA_USUARIO'].", ".$sector.", 0, '".$registro_x->sucesion."');";
			}
		$tabla_x = mysql_query($consulta_x);
		//echo $consulta_x;
	// ---------------------
	$pdf->SetFont('Times','',11);
	
	$txt='En el día de hoy, '.voltea_fecha($registro_datos->fecha_recepcion).', el (la) Ciudadano (a) '.($registro_datos->representante).', Titular de la Cédula de Identidad Nº '.formato_cedula($registro_datos->cedula).', en su carácter de '.($registro_datos->caracter).',  domiciliado en '.mayuscula($registro_datos->direccion).', Consignó  por ante esta  '.$registro_datos->tipo_division.' de Recaudación ('.$registro_datos->area.' de Sucesiones), adscrita a '.($registro_datos->adscripcion_gerencia).' Gerencia Regional de Tributos Internos de la '.buscar_region().',  Forma DS-99032 Nº '.mayuscula(trim($registro_datos->declaracion)).', correspondiente a la Declaración de Herencia del (la) causante ' .mayuscula($registro_datos->sucesion). ' conforme a lo establecido en el Artículo 01 de la Resolución Nº 12 de fecha 26-03-92.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	
	$pdf->SetFont('Times','B',11);
	$pdf->MultiCell(0,5,'DOCUMENTOS CONSIGNADOS PARA SU VERIFICACIÓN PREVIA CONFRONTACIÓN CON LOS ORIGINALES:');	
	//$pdf->Ln(3);
	$pdf->SetFont('Times','',10);
	$i=1;
	
	////////// RECAUDOS PRESENTADOS
	$consulta_datos = "SELECT * FROM vista_re_sucesiones_recepcion WHERE rif ='".$_SESSION['RIF']."' and tipo_req=1 ORDER BY descripcion;";
	$tabla_datos = mysql_query($consulta_datos);
	while ($registro_datos = mysql_fetch_object($tabla_datos))
		{
		$pdf->Ln(5);
		$pdf->Cell(0,5,$i.' - '.ucfirst($registro_datos->descripcion));	
		$i++;
		}

	////////// RECAUDOS FALTANTES
	$consulta_datos = "SELECT * FROM vista_re_sucesiones_recepcion WHERE rif ='".$_SESSION['RIF']."' and tipo_req=2 ORDER BY descripcion;";
	$tabla_datos = mysql_query($consulta_datos);
	$numero_filas = mysql_num_rows($tabla_datos);
	//----------------
	if ($numero_filas>0)
		{
		$pdf->Ln(9);
		$pdf->SetFont('Times','B',11);
		$pdf->MultiCell(0,5,'Se deja constancia de que en este Acto le fue notificado al presentante, que en el plazo de Quince (15) días contados a partir de la presente fecha, deberá consignar por ante esta Oficina los documentos siguientes:');	
		//$pdf->Ln(3);
		$pdf->SetFont('Times','',10);
		$i=1;
		
		while ($registro_datos = mysql_fetch_object($tabla_datos))
			{
			$pdf->Ln(5);
			$pdf->Cell(0,5,$i.' - '.ucfirst($registro_datos->descripcion));	
			$i++;
			}
		}
	
	//-- OBSERVACIONES
	if ($_POST['OOBSERVACIONES']<>'')
		{
		$pdf->SetFont('Times','B',11);
		$pdf->Ln(9);
		$pdf->Cell(30,5,'Observaciones:');
		$pdf->SetFont('Times','',11);
		$pdf->MultiCell(0,5,$_POST['OOBSERVACIONES']);	
		//$pdf->Ln(3);
		}
	
	if ($pdf->GetY()>215)	
		{	$pdf->AddPage();	}
	else
		{	$pdf->SetY(-65);	}
		
	$pdf->Ln(8);
	$pdf->SetFont('Times','',11);
	$pdf->MultiCell(0,5,'De conformidad con lo dispuesto  en el Artículo  50, de la ley Orgánica de Procedimientos Administrativos, en caso contrario se tramitará la declaración presentada basándose en la documentación aportada.');	
	$pdf->Cell(5,5,'');
	$pdf->MultiCell(0,5,'Se hace constar que en este mismo acto, fue entregada la Forma DS-99032, la cual pertenece a la Sucesión.');	
	$pdf->Ln(7);

	//$pdf->SetY(-40);
	$pdf->SetFont('Times','B',9);
	$pdf->Cell(110,5,'Por el Contribuyente:');  										
	$pdf->Cell(0,5,'Funcionario:'); 
	$pdf->Ln(7);
	$pdf->SetFont('Times','',8);
	$pdf->Cell(110,5,'Firma:          __________________________________');
	$pdf->Cell(110,5,'Firma:          __________________________________');
	$pdf->Ln(7);
	$pdf->Cell(110,5,'Fecha:          __________________________________');
	$pdf->Cell(0,5,'Nombre:      '.palabras($funcionario));
		
	// FIN DE LA RESOLUCION
	
	$pdf->Output();
	
	}
else
	{
	echo "<script type=\"text/javascript\">alert('No se han consignado recaudos!');</script>";	
	}
?>