<?php
ob_end_clean();
session_start();

include('../../conexion.php');
include "../../funciones/auxiliar_php.php";

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');

class PDF extends FPDF
{
	function Header()
	{
		// Select Arial bold 15
		$this->SetFont('Arial','B',15);
		// Move to the right
		$this->Cell(80);
		// Framed title
		$this->Cell(30,10,'',0,0,'C');
		// Line break
		$this->Ln(10);
	}
}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(17,10,17);
$pdf->SetAutoPageBreak(1,10);

// CAPTURA DE VALORES
$NUMERO = $_GET['num'];
$ANNO = $_GET['anno'];
$SEDE = $_GET['sede'];

////////// DATOS DE LA PROVIDENCIA
$consulta = "SELECT expedientes_rif.Numero, expedientes_rif.Anno, expedientes_rif.Rif, expedientes_rif.FechaRegistro, expedientes_rif.Coordinador, expedientes_rif.Funcionario, expedientes_rif.Fecha_proceso, expedientes_rif.Usuario, expedientes_rif.Sector, expedientes_rif.Status, expedientes_rif.FechaAnulacion, expedientes_rif.MotivoAnulacion, concat(z_empleados.Nombres,' ',z_empleados.Apellidos) AS Nombre_Coordinador, concat(Empleados.Nombres,' ',Empleados.Apellidos) AS Nombre_Funcionario, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.direccion, contribuyentes.Telefonos, contribuyentes.fechaespecial FROM expedientes_rif INNER JOIN z_empleados ON z_empleados.cedula = expedientes_rif.Coordinador INNER JOIN z_empleados AS Empleados ON Empleados.cedula = expedientes_rif.Funcionario INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_rif.Rif INNER JOIN contribuyentes ON contribuyentes.rif = vista_contribuyentes_direccion.rif WHERE expedientes_rif.Sector=".$SEDE." and expedientes_rif.Anno = ".$ANNO." AND expedientes_rif.Numero = ".$NUMERO."";
$tabla = mysql_query($consulta);

if ($registro = mysql_fetch_object($tabla))
{

$pdf->AddPage();
$pdf->SetFont('Times','',12);
setlocale(LC_ALL, 'sp_ES','sp','es');
$pdf->SetFillColor(190);

//DETERMINAOS LA DEPENDENCIA
list ($estado, $sede, $conector1, $conector2, $adscripcion, $tipo_division, $sector, $titulo) = buscar_sector($registro->Sector);

$ciudad = $sector.", ";
$dependencia = $titulo . ' ' . strtoupper($sector);

//switch ($registro->Sector) {
  //  case 1:
   //     $dependencia = "GERENCIA REGIONAL DE TRIBUTOS INTERNOS REGION LOS LLANOS";
//		$ciudad = "Calabozo, ";
 //       break;
  //  case 2:
   //     $dependencia = "SECTOR DE TRIBUTOS INTERNOS SAN JUAN DE LOS MORROS";
//		$ciudad = "San Juan de los Morros, ";
 //       break;
  //  case 3:
   //     $dependencia = "SECTOR DE TRIBUTOS INTERNOS SAN FERNANDO DE APURE";
//		$ciudad = "San Fernando de Apure, ";
 //       break;
  //  case 4:
   //     $dependencia = "UNIDAD DE TRIBUTOS INTERNOS ALTAGRACIA DE ORITUCO";
//		$ciudad = "Altagracia de Orituco, ";
 //       break;
  //  case 5:
   //     $dependencia = "SECTOR DE TRIBUTOS INTERNOS VALLE DE LA PASCUA";
//		$ciudad = "Valle de la Pascua, ";
 //       break;
//}
$fecha = dia($registro->FechaRegistro) . " de " . ($_SESSION['meses_anno'][abs(mes($registro->FechaRegistro))]) . " de " . anno($registro->FechaRegistro);;
// IMAGEN
$pdf->Image('../../imagenes/logo.jpeg',18,21,60);
$pdf->SetFont('Times','B',10);
$pdf->Cell(60,22,'',1,0,'C');
// Título
$pdf->Cell(120,10,$ciudad.$fecha,0,0,'R');
$pdf->Ln(12);
// Salto de línea
$pdf->Cell(60);
$pdf->Cell(40,10,'EXPEDIENTE',1,0,'C',1);
$pdf->Cell(20,10,$registro->Numero,1,0,'C');
$pdf->Cell(40,10,utf8_decode('AÑO'),1,0,'C',1);
$pdf->Cell(20,10,$registro->Anno,1,0,'C');

$pdf->SetFont('Times','B',9);

// TITULO
/*
$pdf->Cell(0,0,'GERENCIA REGIONAL DE TRIBUTOS INTERNOS',0,0,'C',0);
$pdf->Ln(4);
$pdf->Cell(0,0,'REGION LOS LLANOS',0,0,'C',0);
$pdf->Ln(4);
$pdf->Cell(0,0,'EXPEDIENTE SUJETO PASIVO ESPECIAL',0,0,'C',0);
$pdf->Ln(10);

$pdf->SetFont('Times','',7.5);

// CONTENIDO
$pdf->Cell(40,5,'NOMBRE DEL PROGRAMA:   '.$registro->TipoAutorizacion.'    '.$registro->Descripcion,0,0,'L',0);
$pdf->Cell(80,5,'',0,0,'C',0);
$pdf->Cell(30,5,'NUMERO DE RIF:  '.$registro->Rif,0,0,'C',0);
$pdf->Cell(30,5,'',0,0,'C',0);
$pdf->Ln(7);
*/



// CUADRO VDF
$pdf->Ln(10);

$pdf->SetFont('Times','B',7);
$pdf->Cell(35,9,'PROGRAMA:',1,0,'C',1);
$pdf->Cell(145,9,'',1,0,'C',0);
$pdf->Ln(9);
//--
$lineas = 9;
$pdf->Cell(75,$lineas,'GESTION DE COBRANZA:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'AGENTE DE RETENCION',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'CASINOS, SALAS DE BINGO Y MAQUINAS TRAGANIQUELES:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'BENEFICIOS FISCALES',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'REGISTRO DE INFORMACION FISCAL:',1,0,'C',1);
$pdf->Cell(20,$lineas,'X',1,0,'C',0);
$pdf->Cell(65,$lineas,'CONTROL DE INGRESOS',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'CONTRIBUYENTES ORDINARIOS:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'DEBERES FORMALES',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'PERSONA NATURALES:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'REGISTRO DE CONTRIB EXPORTADORES',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--
$pdf->Cell(75,$lineas,'AVALUOS Y SUCESIONES:',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Cell(65,$lineas,'RECURSOS JERARQUICOS',1,0,'C',1);
$pdf->Cell(20,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);
//--

// DATOS DEL SUJETO PASIVO
$pdf->Cell(180,$lineas,'DATOS DEL SUJETO PASIVO',1,0,'C',1);
$pdf->Ln($lineas);
$pdf->Cell(50,$lineas+1,'CONTRIBUYENTE O RESPONSABLE:',1,0,'C',1);
$pdf->MultiCell(130,10,$registro->contribuyente,1,'C');
//$pdf->Ln($lineas);
$pdf->Cell(50,$lineas,'R.I.F. Nro:',1,0,'C',1);
$pdf->Cell(130,$lineas,substr($registro->Rif,0,1).'-'.substr($registro->Rif,1,8).'-'.substr($registro->Rif,-1),1,0,'C',0);
$pdf->Ln($lineas);
$pdf->Cell(50,$lineas+1,'DOMICILIO FISCAL:',1,0,'C',1);
//$pdf->Cell(130,$lineas,$registro->direccion,1,0,'C',0);
$pdf->MultiCell(130,5,$registro->direccion,1,'C');
//$pdf->Ln($lineas);
$pdf->Cell(50,$lineas,'NRO. DE TELEFONO:',1,0,'C',1);
$pdf->Cell(130,$lineas,$registro->Telefonos,1,0,'C',0);
$pdf->Ln($lineas);
$pdf->Cell(50,$lineas,'CORREO ELECTRONICO:',1,0,'C',1);
$pdf->Cell(130,$lineas,'',1,0,'C',0);
$pdf->Ln($lineas);

// DATOS DIVISION DE SUJETOS PASIVOS ESPECIALES
$pdf->Cell(180,$lineas,'DATOS DIVISION DE SUJETOS PASIVOS ESPECIALES',1,0,'C',1);
$pdf->Ln($lineas);
$pdf->Cell(70,$lineas,'FECHA DE INICIO CONTRIBUYENTE ESPECIAL:',1,0,'C',1);
if ($registro->fechaespecial<>NULL)
{
	$fechainicio = date("d-m-Y", strtotime($registro->fechaespecial));
}
else
{
	$fechainicio ="";
}
$pdf->Cell(110,$lineas,$fechainicio,1,0,'C',0);
$pdf->Ln($lineas);

$pdf->Cell(70,$lineas,'DEPENDENCIA A LA CUAL ESTA ADSCRITO:',1,0,'C',1);
$pdf->Cell(110,$lineas,$dependencia,1,0,'C',0);
$pdf->Ln($lineas);

$pdf->Cell(70,$lineas,'FISCAL:',1,0,'C',1);
$pdf->Cell(110,$lineas,formato_cedula($registro->Funcionario).' - '.$registro->Nombre_Funcionario,1,0,'C',0);
$pdf->Ln($lineas);

$pdf->Cell(70,$lineas,'SUPERVISOR:',1,0,'C',1);
$pdf->Cell(110,$lineas,formato_cedula($registro->Coordinador).' - '.$registro->Nombre_Coordinador,1,0,'C',0);
$pdf->Ln($lineas);

// OBSERVACIONES
$pdf->Cell(180,$lineas,'OBSERVACIONES',1,0,'C',1);
$pdf->Ln($lineas);

$pdf->Ln(5);

$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Cell(160,$lineas,'',1,0,'L',0);
$pdf->Cell(10,$lineas,'',0,0,'L',0);
$pdf->Ln($lineas);

// CUADRO 
$pdf->SetY(20);
$pdf->SetX(17);
$pdf->SetFont('Times','',7);
$pdf->Cell(180,240,'',1,0,'C',0);
// ------	

}
// FIN DE LA VALIDACION DE LA CONSULTA

$pdf->Output();

?>