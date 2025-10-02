<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: ../index.php?errorusuario=val");
	exit();
}

require('../../funciones/fpdf.php');
include('../../conexion.php');
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
{
	function Footer()
	{
		//Posici�n a 1,5 cm del final
		$this->SetY(-15);
		// Arial itálica 8
		$this->SetFont('Times', 'I', 9);
		// Color del texto en gris
		$this->SetTextColor(120);
		// Número de página
		$this->Cell(0, 0, sistema() . ' ', 0, 0, 'R');
	}
}

// ENCABEZADO
$pdf = new PDF('P', 'mm', 'LETTER');
$pdf->AliasNbPages();
$pdf->SetMargins(22, 25, 17);
$pdf->SetDisplayMode($zoom == 'real');
$pdf->SetAutoPageBreak(1, $margin = 10);

//--- COMIENZO DEL MEMO
$pdf->AddPage();
setlocale(LC_TIME, 'sp_ES', 'sp', 'es');

$tamano = 0;
$id_reasignacion = $_GET['id'];

$i = 0;
$consulta = "SELECT id_sector_actual FROM vista_bienes_reasignaciones_solicitadas WHERE id_reasignacion=" . $id_reasignacion;
$tabla = mysql_query($consulta);
while ($registro = mysql_fetch_object($tabla)) {
	$i++;
}

// DATOS DEL MEMO
$consulta = "SELECT * FROM vista_bienes_reasignaciones_solicitadas WHERE id_reasignacion=" . $id_reasignacion;
$tabla = mysql_query($consulta);
if ($registro = mysql_fetch_object($tabla)) {

	//----------------------
	$sector = $registro->id_sector_actual;
	$jefe = $registro->jefe;
	$funcionario = $registro->usuario;
	$opcion = 'division';
	$division = $registro->id_division_actual;
	//---------------
	$jefe_destino = $registro->jefe_destino;
	$cargo_destino = strtoupper($registro->cargo_destino);
	$cargo = strtoupper($registro->cargo);
	// --- POR SI VA O VIENE DE UN SECTOR
	if ($registro->id_sector_actual <> $registro->id_sector_destino and ($registro->id_sector_actual == 1 or $registro->id_sector_destino == 1)) {
		$opcion = 'sector';
		if ($registro->id_sector_actual == 1) {
			//------------
			$consulta_x = "SELECT * FROM vista_jefes WHERE division=" . $registro->id_sector_actual . ";";
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_object($tabla_x);
			//------------
			$jefe_destino = $registro->jefe_destino;
			$cargo_destino = strtoupper($registro->cargo_destino);
			$cargo = strtoupper($registro_x->cargo);
			$division = $registro_x->division;
		}
		if ($registro->id_sector_destino == 1) {
			//------------
			$consulta_x = "SELECT * FROM vista_jefes WHERE division=" . $registro->id_sector_destino . ";";
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_object($tabla_x);
			//------------
			$division = $registro_x->division;
			$jefe_destino = $registro_x->jefe;
			$cargo_destino = strtoupper($registro_x->cargo);
		}
	} else {
		$jefe_destino = $registro->jefe_destino;
		$cargo_destino = strtoupper($registro->cargo_destino);
		$cargo = strtoupper($registro->cargo);
	}
	// Si los sectores son iguales, forzar destino a división 9
	if ($registro->id_sector_actual == $registro->id_sector_destino) {
		$id_div_dest = 9;
		// Consultar jefe de división 9
		$consulta_div9 = "SELECT * FROM vista_jefes WHERE division=9;";
		$tabla_div9 = mysql_query($consulta_div9);
		if ($registro_div9 = mysql_fetch_object($tabla_div9)) {
			$jefe_destino = $registro_div9->jefe;
			$cargo_destino = strtoupper($registro_div9->cargo);
		}
	}
	// ---------------------
	$pdf->Image('../../imagenes/logo.jpeg', 20, 8, 65);
	$pdf->SetFont('Times', 'B', 11);
	$pdf->Ln(8);

	$txt = $registro->siglas . $registro->anno . '/' . str_pad($registro->numero, 5, '0', STR_PAD_LEFT);
	$pdf->Cell(0, 5, $txt);
	$pdf->Ln(8);

	$pdf->SetFont('Times', 'B', 13);
	$pdf->Ln(8);

	$txt = 'M E M O R A N D O';
	$pdf->Cell(0, 5, $txt, 0, 0, 'C');
	$pdf->Ln(8);

	$pdf->SetFont('Times', 'B', 11);
	$pdf->Ln(8);

	$txt = 'PARA:';
	$pdf->Cell(25, 5, $txt);
	$pdf->Cell(0, 5, $jefe_destino);
	$pdf->Ln();

	$txt = '';
	$pdf->Cell(25, 5, $txt);
	$pdf->Cell(0, 5, $cargo_destino);
	$pdf->Ln(8);

	$txt = 'DE:';
	$pdf->Cell(25, 5, $txt);
	$pdf->Cell(0, 5, $cargo);
	$pdf->Ln(8);

	$txt = 'FECHA:';
	$pdf->Cell(25, 5, $txt);
	$txt = voltea_fecha($registro->fecha);
	$pdf->Cell(0, 5, $txt);
	$pdf->Ln(8);

	$txt = 'ASUNTO:';
	$pdf->Cell(25, 5, $txt);
	$txt = 'REASIGNACIÓN DE BIENES NACIONALES';
	$pdf->Cell(0, 5, $txt);
	$pdf->Ln(12);

	$pdf->SetFont('Times', '', 12 - $tamano);

	// POR SI ESTAN ASIGNANDO LOS BIENES
	if ($id_div_dest <> 9) {
		// POR SI HAY VARIOS BIENES
		if ($i == 1) {
			$txt = 'Tengo el agrado de dirigirme a usted, en la oportunidad de extenderle un cordial saludo Bolivariano, Revolucionario, Socialista y Profundamente Chavista, en la oportunidad de reasignarle, el bien nacional descrito en el comprobante anexo, el cual quedará adscrito al inventario bajo su responsabilidad por lo que se requiere tomar en cuenta las Normas Básicas Generales de Control Interno de los Bienes Nacionales:';
		} else {
			$txt = 'Tengo el agrado de dirigirme a usted, en la oportunidad de extenderle un cordial saludo Bolivariano, Revolucionario, Socialista y Profundamente Chavista, en la oportunidad de reasignarle, los bienes nacionales descritos en el comprobante anexo, los cuales quedarán adscritos al inventario bajo su responsabilidad por lo que se requiere tomar en cuenta las Normas Básicas Generales de Control Interno de los Bienes Nacionales:';
		}

		$pdf->MultiCell(0, 5, $txt);
		$pdf->Ln(4);

		$txt = '-Velar por la custodia, el buen uso y mantenimiento de los Bienes a su cargo.
			-El Responsable de los Bienes Muebles (primarios y de uso) en cada unidad de trabajo, responde penal, civil, administrativa y disciplinariamente por las fallas e irregularidades administrativas que cometieran en el manejo de los mismos, conforme a lo establecido en la Ley Orgánica de Bienes Públicos, Ley Orgánica de la Contraloría General de la República y del Sistema Nacional de Control Fiscal y su Reglamento.
			-Al momento de dejar un cargo administrativo u operativo, el funcionario saliente deberá presentar un acta de entrega de los bienes asignados al funcionario entrante o jefe inmediato superior.';
		$pdf->MultiCell(0, 5, $txt);
		$pdf->Ln(4);

		$txt = 'En este sentido se le solicita muy respetuosamente la máxima colaboración para dar cumplimiento a la Normativa legal vigente.';
		$pdf->MultiCell(0, 5, $txt);
		$pdf->Ln(4);
	}
	// POR SI ESTAN DEVOLVIENDO LOS BIENES
	else {
		$pdf->Ln(12);
		// POR SI HAY VARIOS BIENES
		if ($i == 1) {
			$txt = 'Tengo el agrado de dirigirme a usted, en la oportunidad de extenderle un cordial saludo Bolivariano, Revolucionario, Socialista y Profundamente Chavista, y a su vez reasignarle, el bien nacional descrito en el comprobante anexo, que se encuentra asignado en esta Divisi�n seg�n nuestro inventario.';
		} else {
			$txt = 'Tengo el agrado de dirigirme a usted, en la oportunidad de extenderle un cordial saludo Bolivariano, Revolucionario, Socialista y Profundamente Chavista, y a su vez reasignarle, los bienes nacionales descritos en el comprobante anexo, que se encuentran asignados en esta Divisi�n seg�n nuestro inventario.';
		}

		$pdf->MultiCell(0, 5, $txt);
		$pdf->Ln(6);

	}

	$txt = 'Sin otro particular al cual hacer referencia, se despide';
	$pdf->MultiCell(0, 5, $txt);

	$pdf->SetY(-75);

	$txt = 'Atentamente,';
	$pdf->MultiCell(0, 5, $txt, 0, 'C');

	// FIRMA DEL JEFE

	$pdf->Ln(10);



	include "../../funciones/firmabienes.php";
	//-----------

	$pdf->SetLeftMargin(22);
	$pdf->SetRightMargin(17);

	// USUARIO QUE HIZO LA REASIGNACION
	list($funcionario, $rol, $origen, $rol2, $origen2) = funcion_funcionario($funcionario);

	$pdf->Ln(5);
	$txt = extraer_iniciales($jefe) . '/' . strtolower(extraer_iniciales($funcionario));
	$pdf->MultiCell(0, 5, $txt, 0, 'L');
	// FIN

	$pdf->Output();
} else {
	echo 'Error: No existen datos.';
}
?>