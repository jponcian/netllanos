<?PHP //session_start();

include_once("../conexion.php");
include_once("../../funciones/auxiliar_php.php");

if ($_GET['anno']>0) 
{
	$numeromemo=$_GET['num'];
	$añomemo=$_GET['anno'];
	$sector = $_GET['sector'];
	//$noti = $_GET['noti'];
	
	$sqlregistro = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector;
	$rs_registro = $conexionsql->query($sqlregistro);
	$fila_registro = $rs_registro->fetch_object();

	$emisionmemo=$status=$fila_registro->FechaEmision;
	$memocierre=$status=$fila_registro->Status;
	$noti = $fila_registro->Notificacion;
	$VFP=$fila_registro->FP;
	
	if ($VFP==1)
	{
		$FiscalizacionPuntual = " (FISC. PUNTUALES)";
	}
	else
	{
		$FiscalizacionPuntual = "";
	}

} else {
	$emisionmemo=$_GET['fecha'];
	$numeromemo=$_GET['num'];
	$memocierre=$_GET['stu'];
	$sector = $_GET['sector'];
	$añomemo=date("Y",strtotime($emisionmemo));

	$sqlregistro = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector;
	$rs_registro = $conexionsql->query($sqlregistro);
	$fila_registro = $rs_registro->fetch_object();

	$emisionmemo=$status=$fila_registro->FechaEmision;
	$memocierre=$status=$fila_registro->Status;
	$noti = $fila_registro->Notificacion;
	$VFP=$fila_registro->FP;
	$ESP=$fila_registro->ESPECIAL;
	
	$chek_cierre=$fila_registro->Clausurado;
	if ($memocierre==99) {
		$status=99;
	} else {
		$status=$fila_registro->Status;
	}

	if ($VFP==1)
	{
		$FiscalizacionPuntual = " (FISC. PUNTUALES)";
	}
	else
	{
		$FiscalizacionPuntual = "";
	}
}
	$sqlcant = "SELECT count(*) as Total FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector;

$rs_cant = $conexionsql->query($sqlcant);
$valor = $rs_cant->fetch_object(); 
$cantidad = $valor->Total;

//$chek_cierre=$chek_cierre;
//$añomemo=$_SESSION['sa_memo'];
//$numeromemo=$num;
//$emisionmemo=$emisionmemo;

//BUSCAMOS LAS SIGLAS
//BUSCAMOS LAS SIGLAS
$sql_siglas = "SELECT Siglas_resol_fis FROM z_siglas WHERE id_sector=".$sector;
$tabla = $conexionsql->query($sql_siglas);
$sigla = $tabla->fetch_object();
global $siglas;	
$siglas= $sigla->Siglas_resol_fis."/".$_GET['anno']."-".$_GET['num'];
//$status=6;
require('../fpdf/fpdf.php');



class PDF extends FPDF
{
	// pie de pagina
	function Footer()
	{    
		global $siglas;
		$this->Image('../../imagenes/logo.jpeg',20,15,60);
		$this->SetY(33);
		$this->SetX(22);
		//Arial itálica 8
		$this->SetFont('Times','',8);
		$this->SetY(-20);
		$texto = utf8_decode($siglas.' - Página: '.$this->PageNo().' / {nb}');		
		$this->Cell(0,10,$texto,0,1,'R');
	}	
}

//BUSCAMOS EL DESTINO
function buscar_destino($sector, $destino, $noti)
{
	global $conexionsql;
	switch ((string)$destino)
	{
		case 11:
		case 31:
			$emision = "destino = 1";
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 21:
			$emision = 'destino = 3';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 12:
		case 22:
		case 32:
			$emision = 'destino = 2';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 23:
		case 33:
			$emision = 'destino = 2';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 24:
		case 34:
			$emision = 'destino = 2 or destino = 4';
			if ($noti == 1)
			{
				$emision = 'destino = 1 OR destino = 4';
			}
			break;
		case 25:
		case 35: 
		//------------ LINEA AGREGADA PARA QUE APAREZCA SUMARIO ////// BORRARRRRRRRRRRRRRRRRRRRRRRRRRRRR
		$emision = 'destino = 4'; break;
		case 55:
		case 65:
			$emision = 'destino = 4';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 42:
			$emision = 'destino = 5';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 43:
			$emision = 'destino = 5';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 44:
			$emision = 'destino = 5 or destino = 4';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 91:
			$emision = 'destino = 1';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 92:
			$emision = 'destino = 2';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
		case 94:
			$emision = 'destino = 5';
			if ($noti == 1)
			{
				$emision = 'destino = 1';
			}
			break;
	}

	//echo $noti.'............';

	$sql_destino = "SELECT descripcion, jefe, area FROM ct_destinos WHERE sector = ".$sector." AND ".$emision." ORDER BY destino ASC"; //echo $sql_destino;
	$result = $conexionsql->query($sql_destino);
	
	if ($destino == 24 or $destino == 34 or $destino == 44 or $destino == 44)
	{
		$i = 0;
		while ($valor = $result->fetch_object())
		{
			if ($i == 0)
			{
				$division = $valor->area;
				$area = $valor->descripcion;
				$jefe = $valor->jefe;
			} else {
				$division1 = $valor->area;
				$area1 = $valor->descripcion;
				$jefe1 = $valor->jefe;
			}
			$i += 1;
		}
		return array($division1,$area1,$jefe1,$division,$area,$jefe);
	} else {
		if ($valor = $result->fetch_object())
		{
				$division = $valor->area;
				$area = $valor->descripcion;
				$jefe = $valor->jefe;
		}
		return array($division,$area,$jefe);
	}	
}

switch ($status)
{
	case 11:
	case 31:
		//VDF CONFORMES
		/*$division="TRAMITACION";
		$area="C/A AREA DE ARCHIVO";*/
		$vector = buscar_destino($sector, $status, $noti);
		if ($status == 11) { $tipoexpediente = 'Verificación de Deberes Formales'; } else { $tipoexpediente = 'Fiscalización';}
		$expedientes="EXPEDIENTES DE VDF CONFORMES".$FiscalizacionPuntual;
		if ($cantidad==1)
			{
			$txt = 'Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir un (01) expediente de '.$tipoexpediente.', cuyo contribuyente resultó conforme, el mismo se detalla a continuación: (ver relacion Anexa).';		
			}
		else
			{
			$txt = 'Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir '.valorEnLetrasNatural($cantidad).' ('.($cantidad).') expedientes de '.$tipoexpediente.', cuyos contribuyentes resultaron conformes, los mismos se detallan a continuación: (ver relación Anexa).';		
			}
		break;
		
	case 21:
		//SUCESIONES CONFORMES
		/*$division="RECAUDACION";
		$area="C/A AREA DE SUCESIONES";*/
		$vector = buscar_destino($sector, $status, $noti);
		$expedientes="EXPEDIENTES CONFORMES".$FiscalizacionPuntual;
		$txt = 'Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') expedientes de Sucesiones cuyos contribuyentes resultaron conformes, los mismo se detallan a continuación: (ver relación Anexa).';
		break;

	case 12:
	case 22:
	case 32:
		//EXPEDIENTES SANCIONADOS
		if ($chek_cierre==0)
		{
			/*$division="RECAUDACION";
			$area="C/A AREA DE LIQUIDACION";*/
			$vector = buscar_destino($sector, $status, $noti);
			if ($status == 12) { $tipo = 'Verificación de Deberes Formales'; }
			if ($status == 22) { $tipo = 'Avalúo de Bienes y Líquido Hereditario'; }
			if ($status == 32) { $tipo = 'Fiscalización'; }
			$expedientes="EXPEDIENTES SANCIONADOS PARA SU NOTIFICACION".$FiscalizacionPuntual;
			$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su correpondiente notificación, la cantidad de ('.$cantidad.') expedientes de '.$tipo.', cuyos contribuyentes resultaron sancionados, los mismo se detallan a continuación: (ver relación Anexa).';
		}
		else
		{
			//$division="RECAUDACION";
			$vector = buscar_destino($sector, $status, $noti);
			$expedientes="EXPEDIENTES CLAUSURADOS Y NOTIFICADOS".$FiscalizacionPuntual;
			$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') expedientes de Verificación de Deberes Formales, cuyos contribuyentes resultaron sancionados, clausurados y notificada la resolución de imposición de sanción y las planilas de multas liquidadas, los mismo se detallan a continuación: (ver relación Anexa).';
		}
		break;
		
	case 23:
	case 33:
		//EXPEDIENTES ALLANADOS
		/*$division="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		if ($status == 23) { $tipo = 'Avalúo de Bienes y Líquido Hereditario'; }
		if ($status == 33) { $tipo = 'Fiscalización'; }
		$expedientes="EXPEDIENTES ALLANADOS PARA SU NOTIFICACION".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su correpondiente notificación, la cantidad de ('.$cantidad.') expedientes de '.$tipo.' a cuyos contribuyentes se les notificó Acta de Reparo y los mismo se ajustaron totalmente dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en el artículo 195 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		break;
		
	case 25:
	case 35:
		//EXPEDIENTES NO ALLANADOS
		/*$division="SUMARIO";*/
		
		$vector = buscar_destino($sector, $status, $noti);
		if ($status == 25) { $tipo = 'Avalúo de Bienes y Líquido Hereditario'; }
		if ($status == 35) { $tipo = 'Fiscalización'; }
		$expedientes="EXPEDIENTES NO ALLANADOS".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') expedientes de '.$tipo.' a cuyos contribuyentes se les notificó Acta de Reparo y los mismo no se ajustaron dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en el artículo 198 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		break;
		
	case 24:
	case 34:
		//EXPEDIENTES ALLANADOS PARCIALMENTE
		/*$division="SUMARIO";
		$division1="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		if ($status == 24) { $tipo = 'Avalúo de Bienes y Líquido Hereditario'; }
		if ($status == 34) { $tipo = 'Investigación'; }
		$expedientes="EXPEDIENTES ALLANADOS PARCIALMENTE".$FiscalizacionPuntual;
		$expedientes1="RESOLUCION ALLANADOS PARCIALMENTE PARA SU NOTIFICACION".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') expedientes de '.$tipo.' a cuyos contribuyentes se les notificó Acta de Reparo y los mismo se ajustaron parcialmente dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en el artículo 195 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		$txt61='Tengo el agrado de dirigirme a usted, en la oportunidad de remitir para su correpondiente notificación, la cantidad de ('.$cantidad.') resoluciones de allanamiento, a cuyos contribuyentes se les notificó Acta de Reparo y los mismo se ajustaron parcialmente dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en el artículo 195 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		break;
		
	case 44:
		//CONTRIBUYENTES ESPECIALES ALLANADOS PARCIALMENTE
		/*$division="SUMARIO";
		$division1="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		$expedientes="EXPEDIENTES ALLANADOS PARCIALMENTE".$FiscalizacionPuntual;
		$expedientes1="RESOLUCION ALLANADOS PARCIALMENTE PARA SU NOTIFICACION".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') expedientes de fiscalizaciones a Sujetos Pasivos Especiales a los cuales se les notificó Acta de Reparo y los mismo se ajustaron parcialmente dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en el artículo 195 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		$txt61='Tengo el agrado de dirigirme a usted, en la oportunidad de remitir para su correpondiente notificación, la cantidad de ('.$cantidad.') resoluciones de allanamiento  a Sujetos Pasivos Especiales a los cuales se se les notificó Acta de Reparo y los mismo se ajustaron parcialmente dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en el artículo 195 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		break;

	case 55:
		//EXPEDIENTES ALLANADOS
		/*$division="SUMARIO";*/
		$vector = buscar_destino($sector, $status, $noti);
		if ($status == 55) { $tipo = 'Fiscalización'; }
		$expedientes="EXPEDIENTES ALLANADOS (FP - RETENCIONES)";
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') expedientes de '.$tipo.' a cuyos contribuyentes (Agentes de Retención) se les notificó Acta de Reparo y los mismo se ajustaron dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en los artículos 118 y 198 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		break;
		
	case 65:
		//EXPEDIENTES ALLANADOS PARCIALMENTE   ALEX-BUZZANO
		/*$division="SUMARIO";*/
		$vector = buscar_destino($sector, $status, $noti);
		if ($status == 65) { $tipo = 'Fiscalización'; }
		$expedientes="EXPEDIENTES ALLANADOS PARCIALMENTE (FP - RETENCIONES)";
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') expedientes de '.$tipo.' a cuyos contribuyentes (Agentes de Retención) se les notificó Acta de Reparo y los mismo se ajustaron parcialmente dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en los artículos 118 y 198 del Código Orgánico Tributario, los mismo se detallan a continuación: (ver relación Anexa).';
		break;

	case 99:
		$division="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";
		$expedientes="RESOLUCION DE IMPOSICION DE SANCION CLAUSURA".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir la cantidad de ('.$cantidad.') resoluciones de imposición de sanciones por incumplimientos de deberes formales con medida de clausura temporal del establecimiento, se agradece que una vez efectuada la liquidación se remita a ésta División para su correspondiente ejecución, los mismo se detallan a continuación: (ver relación Anexa).';
		break;
		
	case 42:
		//CONTRIBUYENTES ESPECIALES SANCIONADOS
		/*$division="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		$expedientes="EXPEDIENTES SANCIONADOS PARA SU NOTIFICACION".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su correpondiente notificación, la cantidad de ('.$cantidad.') expedientes de Fiscalización a Sujetos Pasivos Especiales los cuales resultaron sancionados por incumplimiento de Deberes Formales, los mismo se detallan a continuación: (ver relación Anexa).';
		break;
		
	case 43:
		//CONTRIBUYENTES ESPECIALES ALLANADOS
		/*$division="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		$expedientes="EXPEDIENTES ALLANADOS PARA SU NOTIFICACION".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de remitir para su correpondiente notificación, la cantidad de ('.$cantidad.') expedientes de Fiscalización a Sujetos Pasivos Especiales a los cuales se les notificó Acta de Reparo y los mismo se ajustaron totalmente dentro de los 15 días habiles siguientes a la notificación de dicha acta de reparo, todo de acuerdo con lo establecido en el artículo 195 del Código Orgánico Tributario, los mismo se detallan a continuación. (ver relación Anexa).';
		break;

	case 91:
		//CONTRIBUYENTES ESPECIALES ALLANADOS
		/*$division="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		$expedientes="EXPEDIENTES CON PLANILLAS PAGADAS".$FiscalizacionPuntual;
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su archivo, la cantidad de ('.$cantidad.') expedientes de Fiscalización a los cuales se les notificó Resolución y Planillas de Pago las cuales fueron pagadas totalmente dentro de los 25 días habiles siguientes a la notificación. (ver relación Anexa).';
		break;

	case 92:
		//CONTRIBUYENTES ESPECIALES ALLANADOS
		/*$division="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		$expedientes="EXPEDIENTES CON PLANILLAS NOTIFICADAS".$FiscalizacionPuntual;
		if ($cantidad==1)
			{
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su repectiva gestión de cobranza, un (01) expediente de Fiscalización al cual se le notificó Resolución y Planillas de Pago. (ver relación Anexa).';
			}
		else
			{
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su repectiva gestión de cobranza, la cantidad de '.valorEnLetrasNatural($cantidad).' ('.($cantidad).') expedientes de Fiscalización a los cuales se les notificó Resolución y Planillas de Pago. (ver relación Anexa).';
			}
		break;

	case 94:
		//CONTRIBUYENTES ESPECIALES ALLANADOS
		/*$division="RECAUDACION";
		$area="C/A AREA DE LIQUIDACION";*/
		$vector = buscar_destino($sector, $status, $noti);
		$expedientes="EXPEDIENTES CON PLANILLAS NOTIFICADAS".$FiscalizacionPuntual;
		if ($cantidad==1)
			{
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su repectiva gestión de cobranza, un (01) expediente de Fiscalización al cual se le notificó Resolución y Planillas de Pago. (ver relación Anexa).';
			}
			else
			{
		$txt='Tengo el agrado de dirigirme a usted, en la oportunidad de saludarle cordialmente con la finalidad de remitir para su repectiva gestión de cobranza, la cantidad de '.valorEnLetrasNatural($cantidad).' ('.($cantidad).') expedientes de Fiscalización a los cuales se les notificó Resolución y Planillas de Pago. (ver relación Anexa).';	
			}
		break;

}

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER'); // H
$pdf->AliasNbPages();

$pdf->Ln(8); // PARA SALTAR LINEAS

$pdf->SetMargins(22,45,22);

$pdf->AddPage(); // OBLIGATORIO

$pdf->SetFont('Times','',12);

$pdf->SetFillColor(190); // COLOR DEL FONDO

//////////
$pdf->Image('../images/logocr.gif',20,15,60); // INSERTAR IMAGEN
$pdf->SetFont('Times','',10);
$pdf->Cell(0,1,$siglas,0,1);

$pdf->SetFont('Times','B',15);

$pdf->Ln(10);
//////////

$pdf->SetFont('Times','B',10);

$pdf->SetFont('Times','B',14);
$pdf->Cell(0,8,'MEMORANDO',0,1,'C',0);

$pdf->SetFont('Times','',12);
$fecha = date("d-m-Y", strtotime($emisionmemo));
$pdf->Cell(30,3,'',0,1,'J',0);
$pdf->Cell(30,6,'PARA:',0,0,'J',0);

$pdf->Cell(10,6,utf8_decode($vector[2]),0,1,'J',0);
$pdf->Cell(30,3,'',0,0,'J',0);
$pdf->Cell(10,6,utf8_decode($vector[0]),0,1,'J',0);

if ($status==11 or $status==31 or $status==91) 
{
	$pdf->Cell(30,3,'',0,0,'J',0);
	$pdf->Cell(10,6,"C/A AREA DE ARCHIVO",0,1,'J',0);
}

if ($status==21) 
{
	$pdf->Cell(30,3,'',0,0,'J',0);
	$pdf->Cell(10,6,"C/A AREA DE SUCESIONES",0,1,'J',0);
}

if ($status==12 or $status==22 or $status==32 or $status==23 or $status==33 or $status==42 or $status==43 or $status==44) 
{
	$pdf->Cell(30,3,'',0,0,'J',0);
	$pdf->Cell(10,6,"C/A AREA DE NOTIFICACION",0,1,'J',0);
}

if ($status==92 or $status==94) 
{
	$pdf->Cell(30,3,'',0,0,'J',0);
	$pdf->Cell(10,6,"C/A AREA DE COBRANZA",0,1,'J',0);
}

//BUSCAMOS EL RESPONSABLE QUE EMITE EL MEMORANDO EN LA SEDE ES EL JEFE DE FISCALIZACION - EN LOS SECTORES EL JEFE DE AREA DE FISCALIZACION O COORDINADOR
if ($sector < 2 and ($status==25 or $status==35 or $status==24 or $status==34 or $status==44 or $status==94 or $status==55 or $status==65))
{
	//EN CASO DE SUMARIO ES EL JEFE DEL SECTOR EN LOS SECTORES
	$remitente = "SELECT descripcion, jefe, cargo FROM z_jefes_detalle WHERE division BETWEEN 2 AND 6 AND id_sector = ".$sector;
} else {
	if ($sector < 2 or $status==55)
	{
		$remitente = "SELECT descripcion, jefe, cargo FROM z_jefes_detalle WHERE division BETWEEN 2 AND 6 AND id_sector = ".$sector;
	} else {
		$remitente = "SELECT id_sector, cedula, coordinador as jefe, cargo FROM ct_coordinador WHERE id_sector = ".$sector;
	}
}
//echo $remitente;
$rs_coord = $conexionsql->query($remitente);
$fila_jefe = $rs_coord->fetch_object();

$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(30,6,'DE:',0,0,'J',0);
$pdf->Cell(10,6,strtoupper(utf8_decode($fila_jefe->cargo)),0,1,'J',0);
$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(30,6,'FECHA:',0,0,'J',0);
$pdf->Cell(10,6,$fecha,0,1,'J',0);
$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(30,6,'ASUNTO:',0,0,'J',0);
$pdf->Cell(10,6,mb_strtoupper($expedientes, 'UTF-8'),0,1,'J',0);
 
$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(10,3,'',0,1,'J',0);

//$pdf->Cell(100,8,'SSSSSSSSSSSSSSSSSSSSSSSSSSSSS:',1,1,'L',1); // AGREGA TEXTO - LAS 2 PRIMEROS VALORES ES TAMAÑO

$pdf->Ln(8);
$pdf->MultiCell(0,5,utf8_decode($txt),0,'J',0);


// CUADRO ACTAS DE REPARO
$pdf->Ln(8);

$pdf->SetFont('Times','',10);
//$pdf->Cell(10,6,utf8_decode('VER RELACIÓN DE EXPEDIENTES ANEXA'),0,1,'J',0);

$pdf->SetFont('Times','',12);

$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(10,3,'',0,1,'J',0);

$txt1 = utf8_decode('Sin más a que hacer referencia, se despide');
$pdf->MultiCell(0,5,$txt1,0,'J',0);

$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Ln(6);
$pdf->Cell(0,8,'Atentamente,',0,1,'C',0);
$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Cell(10,3,'',0,1,'J',0);
$pdf->Ln(6);

$pdf->SetFont('Times','B',12);

$pdf->Cell(0,8,utf8_decode(mb_strtoupper($fila_jefe->jefe, 'UTF-8')),0,1,'C',0);

$pdf->Ln(30);

//DETERMINAMOS LAS INICIALES
$sqliniciales = "SELECT iniciales_prov AS inciales FROM z_sectores WHERE id_sector = ".$sector;
$rs_ini = $conexionsql->query($sqliniciales);
$iniciales = $rs_ini->fetch_object();

$pdf->SetFont('Times','',10);
$pdf->Cell(10,6,$iniciales->inciales,0,1,'L',0);

if($status==24 or $status==34 or $status==44)
{
	
	$pdf->AliasNbPages();
	
	$pdf->Ln(8); // PARA SALTAR LINEAS
	
	$pdf->SetMargins(22,45,22);
	
	$pdf->AddPage('P'); // OBLIGATORIO
	$pdf->Cell(0,1,$siglas.'-A',0,1);

	$pdf->Ln(10);
	//////////

	$pdf->SetFont('Times','B',10);
	
	$pdf->SetFont('Times','B',14);
	$pdf->SetFont('Times','B',14);
	$pdf->Cell(0,8,'MEMORANDO',0,1,'C',0);
	
	$pdf->SetFont('Times','',12);
	$fecha = date("d-m-Y", strtotime($emisionmemo));
	$pdf->Cell(30,3,'',0,1,'J',0);
	$pdf->Cell(30,6,'PARA:',0,0,'J',0);
	$pdf->Cell(10,6,$vector[5],0,1,'J',0);
	$pdf->Cell(30,3,'',0,0,'J',0);
	$pdf->Cell(10,6,$vector[3],0,1,'J',0);

	if ($status==24 or $status==34 or $status==43 or $status==44) 
	{
		$pdf->Cell(30,3,'',0,0,'J',0);
		$pdf->Cell(10,6,"C/A AREA DE NOTIFICACION",0,1,'J',0);
	}

	if ($status==92 OR $status==94) 
	{
		$pdf->Cell(30,3,'',0,0,'J',0);
		$pdf->Cell(10,6,"C/A AREA DE COBRANZA",0,1,'J',0);
	}

	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(30,6,'DE:',0,0,'J',0);
	$pdf->Cell(10,6,mb_strtoupper($fila_jefe->cargo, 'UTF-8'),0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(30,6,'FECHA:',0,0,'J',0);
	$pdf->Cell(10,6,$fecha,0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(30,6,'ASUNTO:',0,0,'J',0);
	$pdf->Cell(10,6,mb_strtoupper($expedientes1, 'UTF-8'),0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	
	//$pdf->Cell(100,8,'SSSSSSSSSSSSSSSSSSSSSSSSSSSSS:',1,1,'L',1); // AGREGA TEXTO - LAS 2 PRIMEROS VALORES ES TAMAÑO
	
	$pdf->Ln(8);
	$pdf->MultiCell(0,5,utf8_decode($txt61),0,'J',0);
	
	
	// CUADRO ACTAS DE REPARO
	$pdf->Ln(8);
	
	$pdf->SetFont('Times','',10);
	$pdf->Cell(10,6,utf8_decode('VER RELACIÓN DE EXPEDIENTES ANEXA'),0,1,'J',0);
	
	$pdf->SetFont('Times','',12);
	
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	
	$txt1 = utf8_decode('Sin más particular a que hacer referencia.');
	$pdf->MultiCell(0,5,$txt1,0,'J',0);
	
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Ln(6);
	$pdf->Cell(0,8,'Atentamente,',0,1,'C',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Cell(10,3,'',0,1,'J',0);
	$pdf->Ln(6);
	$pdf->Cell(0,8,mb_strtoupper($fila_jefe->jefe, 'UTF-8'),0,1,'C',0);
	
	$pdf->Ln(6);
	
	$pdf->SetFont('Times','',10);
	$pdf->Cell(10,6,$iniciales->inciales,0,1,'L',0);
	
}

$pdf->AliasNbPages();

$pdf->Ln(8); // PARA SALTAR LINEAS

$pdf->SetMargins(22,45,22);

if ($status==11 or $status==21 or $status==31 or $status==91)
{
	$pdf->AddPage('L'); // OBLIGATORIO
	
	$pdf->SetFont('Times','',14);
	$pdf->Cell(0,8,utf8_decode('RELACIÓN DE EXPEDIENTES'),0,1,'C',0);
	
	$pdf->Ln(8);
	$pdf->SetFont('Times','B',8);
	$pdf->Cell(10,6,utf8_decode('N°:'),1,0,'C',1);
	$pdf->Cell(20,6,'RIF',1,0,'C',1);
	$pdf->Cell(70,6,'NOMBRE',1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('TIPO'),1,0,'C',1);
	$pdf->Cell(15,6,utf8_decode('N° PROV.'),1,0,'C',1);
	$pdf->Cell(40,6,utf8_decode('CONTENIDO'),1,0,'C',1);
	$pdf->Cell(15,6,utf8_decode('FOLIOS'),1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('NOTIFICACIÓN'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('UBICACION'),1,0,'C',1);
	$pdf->Ln(6);
	$item=1;
	$pdf->SetFont('Times','',9);
	///LISTAR REGISTROS
	//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
	
	$IDRegistro = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector." and Status=".$status."";
	
	$rs_result = $conexionsql->query($IDRegistro);
	
	//$fila_IDpara = odbc_fetch_object($rs_result);
	
	//if ($fila = odbc_fetch_object($rs_result))
	while ($fila = $rs_result->fetch_array())
	{
		$pdf->Cell(10,6,$item,1,0,'C',0);
		$pdf->Cell(20,6,substr($fila['Rif'],0,25),1,0,'J',0);
		$pdf->Cell(70,6,substr($fila['Nombre'],0,25),1,0,'J',0);
		$pdf->Cell(30,6,substr($fila['Tipo'],0,25),1,0,'J',0);
		$pdf->Cell(15,6,$fila['Anno_Providencia']."-".$fila['NroAutorizacion'],1,0,'C',0);
		$pdf->Cell(40,6,substr($fila['Contenido'],0,25),1,0,'J',0);
		$pdf->Cell(15,6,substr($fila['Folio'],0,25),1,0,'C',0);
		$pdf->Cell(25,6,date("d-m-Y",strtotime($fila['FechaNotificacion'])),1,0,'C',0);
		$pdf->Cell(20,6,substr('ARCHIVO',0,25),1,0,'C',0);
		$pdf->Ln(6);
		$item=$item+1;
	}
}

/*if ($status==12 or $status==22 or $status==32 or $status==42)
{
	$pdf->AddPage('L'); // OBLIGATORIO
	
	$pdf->SetFont('Times','',14);
	$pdf->Cell(0,8,'RELACION DE EXPEDIENTES',0,1,'C',0);
	
	$pdf->Ln(8);
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(10,6,utf8_decode('N°:'),1,0,'C',1);
	$pdf->Cell(20,6,'RIF',1,0,'C',1);
	$pdf->Cell(80,6,'NOMBRE',1,0,'C',1);
	$pdf->Cell(40,6,utf8_decode('TIPO'),1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('N° PROV.'),1,0,'C',1);
	$pdf->Cell(45,6,utf8_decode('CONTENIDO'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('FOLIO No.'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('NOTIFICACION'),1,0,'C',1);
	$pdf->Cell(40,6,utf8_decode('UBICACION FISICA'),1,0,'C',1);
	$pdf->Ln(6);
	$item=1;
	$pdf->SetFont('Times','',10);
	///LISTAR REGISTROS
	//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
	
	$IDRegistro = "SELECT * FROM Salida_Expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector." and Status=".$status."";
	
	$rs_result = $conexionsql->query($IDRegistro);
	
	//$fila_IDpara = odbc_fetch_object($rs_result);
	
	//if ($fila = odbc_fetch_object($rs_result))
	while ($fila = $rs_result->fetch_array())
	{
		$pdf->Cell(10,6,$item,1,0,'C',0);
		$pdf->Cell(20,6,substr($fila['Rif'],0,25),1,0,'J',0);
		$pdf->Cell(80,6,substr($fila['Nombre'],0,25),1,0,'J',0);
		$pdf->Cell(40,6,substr($fila['Tipo'],0,25),1,0,'J',0);
		$pdf->Cell(25,6,$fila['Anno_Providencia']."-".$fila['NroAutorizacion'],1,0,'C',0);
		$pdf->Cell(45,6,substr($fila['Contenido'],0,25),1,0,'J',0);
		$pdf->Cell(20,6,substr($fila['Folio'],0,25),1,0,'C',0);
		$pdf->Cell(30,6,date("d-m-Y",strtotime($fila['FechaNotificacion'])),1,0,'C',0);
		$pdf->Cell(40,6,substr('ARCHIVO',0,25),1,0,'C',0);
		$pdf->Ln(6);
		$item=$item+1;
	}
}*/

if ($status==12 or $status==22 or $status==32 or $status==42)
{
	$pdf->AddPage('P'); // OBLIGATORIO
	
	$pdf->SetFont('Times','',14);
	$pdf->Cell(0,8,utf8_decode('RELACIÓN DE EXPEDIENTES'),0,1,'C',0);
	
	$pdf->Ln(8);
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(10,6,utf8_decode('N°:'),1,0,'C',1);
	$pdf->Cell(20,6,'RIF',1,0,'C',1);
	$pdf->Cell(60,6,'NOMBRE',1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('N° RESOL.'),1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('N° PROV.'),1,0,'C',1);
	$pdf->Cell(25,6,'MULTA DF',1,0,'C',1);
	$pdf->Ln(6);
	$item=1;
	$pdf->SetFont('Times','',10);
	///LISTAR REGISTROS
	//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
	
	$IDRegistro = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector." and Status=".$status."";
	
	$rs_result = $conexionsql->query($IDRegistro);
	
	//$fila_IDpara = odbc_fetch_object($rs_result);
	
	//if ($fila = odbc_fetch_object($rs_result))
	while ($fila = $rs_result->fetch_array())
	{
		$pdf->Cell(10,6,$item,1,0,'C',0);
		$pdf->Cell(20,6,substr($fila['Rif'],0,25),1,0,'J',0);
		$pdf->Cell(60,6,substr($fila['Nombre'],0,25),1,0,'J',0);
		$pdf->Cell(25,6,$fila['Anno_Resolucion']."-".$fila['NroResolucion']."-".$fila['Notificacion'],1,0,'C',0);
		$pdf->Cell(25,6,$fila['Anno_Providencia']."-".$fila['NroAutorizacion'],1,0,'C',0);
		$pdf->Cell(25,6,number_format($fila['Multa_DF'],2,',','.'),1,0,'R',0);
		$pdf->Ln(6);
		$item=$item+1;
	}
}

if ($status==92 or $status==94)
{
	$pdf->AddPage('P'); // OBLIGATORIO
	
	$pdf->SetFont('Times','',14);
	$pdf->Cell(0,8,utf8_decode('RELACIÓN DE EXPEDIENTES'),0,1,'C',0);
	
	$pdf->Ln(8);
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(10,6,utf8_decode('N°:'),1,0,'C',1);
	$pdf->Cell(20,6,'RIF',1,0,'C',1);
	$pdf->Cell(85,6,'NOMBRE',1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('N° RESOL.'),1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('N° PROV.'),1,0,'C',1);
	$pdf->Ln(6);
	$item=1;
	$pdf->SetFont('Times','',10);
	///LISTAR REGISTROS
	//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
	
	$IDRegistro = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector." and Status=".$status."";
	
	$rs_result = $conexionsql->query($IDRegistro);
	
	//$fila_IDpara = odbc_fetch_object($rs_result);
	
	//if ($fila = odbc_fetch_object($rs_result))
	while ($fila = $rs_result->fetch_array())
	{
		$pdf->Cell(10,6,$item,1,0,'C',0);
		$pdf->Cell(20,6,substr($fila['Rif'],0,25),1,0,'J',0);
		$pdf->Cell(85,6,substr($fila['Nombre'],0,25),1,0,'J',0);
		$pdf->Cell(25,6,$fila['Anno_Resolucion']."-".$fila['NroResolucion'],1,0,'C',0);
		$pdf->Cell(25,6,$fila['Anno_Providencia']."-".$fila['NroAutorizacion'],1,0,'C',0);
		$pdf->Ln(6);
		$item=$item+1;
	}
}

if ($status==23 or $status==33 or $status==43 or $status==24 or $status==34 or $status==44 or $status==55 or $status==65)
{
	$pdf->AddPage('L'); // OBLIGATORIO
	
	$pdf->SetFont('Times','',14);
	$pdf->Cell(0,8,'RELACION DE EXPEDIENTES',0,1,'C',0);
	
	$pdf->Ln(8);
	$pdf->SetFont('Times','B',8);
	$pdf->Cell(10,6,utf8_decode('N°:'),1,0,'C',1);
	$pdf->Cell(20,6,'RIF',1,0,'C',1);
	$pdf->Cell(60,6,'NOMBRE',1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('N° RESOL.'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('N° PROV.'),1,0,'C',1);
	$pdf->Cell(20,6,'REPARO.',1,0,'C',1);
	$pdf->Cell(21,6,'IMP OMITIDO',1,0,'C',1);
	$pdf->Cell(20,6,'MULTA.',1,0,'C',1);
	$pdf->Cell(20,6,'INTERESES',1,0,'C',1);
	$pdf->Cell(21,6,'MTO. PAGADO',1,0,'C',1);
	$pdf->Ln(6);
	$item=1;
	$pdf->SetFont('Times','',9);
	///LISTAR REGISTROS
	//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
	
	$IDRegistro = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector." and Status=".$status."";
	
	$rs_result = $conexionsql->query($IDRegistro);
	
	//$fila_IDpara = odbc_fetch_object($rs_result);
	
	//if ($fila = odbc_fetch_object($rs_result))
	while ($fila = $rs_result->fetch_array())
	{
		$pdf->Cell(10,6,$item,1,0,'C',0);
		$pdf->Cell(20,6,substr($fila['Rif'],0,25),1,0,'J',0);
		$pdf->Cell(60,6,substr($fila['Nombre'],0,25),1,0,'J',0);
		$pdf->Cell(20,6,$fila['Anno_Resolucion']."-".$fila['NroResolucion'],1,0,'C',0);
		$pdf->Cell(20,6,$fila['Anno_Providencia']."-".$fila['NroAutorizacion'],1,0,'C',0);
		$pdf->Cell(20,6,number_format($fila['Monto_Reparo'],2,',','.'),1,0,'R',0);
		$pdf->Cell(21,6,number_format($fila['Impto_Omitido'],2,',','.'),1,0,'R',0);
		$pdf->Cell(20,6,number_format($fila['Multa_Reparo'],2,',','.'),1,0,'R',0);
		$pdf->Cell(20,6,number_format($fila['Intereses'],2,',','.'),1,0,'R',0);
		$pdf->Cell(21,6,number_format($fila['Monto_Pagado'],2,',','.'),1,1,'R',0);
		$item=$item+1;
	}
}

if ($status==25 or $status==35)
{
	$pdf->AddPage('P'); // OBLIGATORIO
	
	$pdf->SetFont('Times','',14);
	$pdf->Cell(0,8,utf8_decode('RELACIÓN DE EXPEDIENTES'),0,1,'C',0);
	
	$pdf->Ln(8);
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(10,6,utf8_decode('N°:'),1,0,'C',1);
	$pdf->Cell(20,6,'RIF',1,0,'C',1);
	$pdf->Cell(80,6,'NOMBRE',1,0,'C',1);
	//$pdf->Cell(25,6,utf8_decode('N° RESOL.'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('N° PROV.'),1,0,'C',1);
	$pdf->Cell(25,6,'REPARO.',1,0,'C',1);
	$pdf->Cell(27,6,'IMP OMITIDO',1,0,'C',1);
	$pdf->Ln(6);
	$item=1;
	$pdf->SetFont('Times','',10);
	///LISTAR REGISTROS
	//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
	
	$IDRegistro = "SELECT * FROM ct_salida_expediente WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and sector=".$sector." and Status=".$status."";
	
	$rs_result = $conexionsql->query($IDRegistro);
	
	//$fila_IDpara = odbc_fetch_object($rs_result);
	
	//if ($fila = odbc_fetch_object($rs_result))
	while ($fila = $rs_result->fetch_array())
	{
		$pdf->Cell(10,6,$item,1,0,'C',0);
		$pdf->Cell(20,6,substr($fila['Rif'],0,25),1,0,'J',0);
		$pdf->Cell(80,6,substr($fila['Nombre'],0,40),1,0,'J',0);
		//$pdf->Cell(25,6,$fila['Anno_Resolucion']."-".$fila['NroResolucion'],1,0,'C',0);
		$pdf->Cell(20,6,$fila['Anno_Providencia']."-".$fila['NroAutorizacion'],1,0,'C',0);
		$pdf->Cell(25,6,number_format($fila['Monto_Reparo'],2,',','.'),1,0,'R',0);
		$pdf->Cell(27,6,number_format($fila['Impto_Omitido'],2,',','.'),1,1,'R',0);
		$item=$item+1;
	}
}

/*if ($status==99)
{
	$pdf->AddPage('P'); // OBLIGATORIO
	
	$pdf->SetFont('Times','',14);
	$pdf->Cell(0,8,'RELACION DE RESOLUCIONES',0,1,'C',0);
	
	$pdf->Ln(8);
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(10,6,utf8_decode('N°:'),1,0,'C',1);
	$pdf->Cell(20,6,'RIF',1,0,'C',1);
	$pdf->Cell(60,6,'NOMBRE',1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('N° RESOL.'),1,0,'C',1);
	$pdf->Cell(25,6,utf8_decode('N° PROV.'),1,0,'C',1);
	$pdf->Cell(30,6,'MULTA DF',1,0,'C',1);
	$pdf->Ln(6);
	$item=1;
	$pdf->SetFont('Times','',10);
	///LISTAR REGISTROS
	//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
	
	$IDRegistro = "SELECT * FROM Res_Sancion_Cierre WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and Status=".$status."";
	
	$rs_result = $conexionsql->query($IDRegistro);
	
	//$fila_IDpara = odbc_fetch_object($rs_result);
	
	//if ($fila = odbc_fetch_object($rs_result))
	while ($fila = $rs_result->fetch_array())
	{
		$pdf->Cell(10,6,$item,1,0,'C',0);
		$pdf->Cell(20,6,substr($fila['Rif'],0,25),1,0,'J',0);
		$pdf->Cell(60,6,substr($fila['Nombre'],0,25),1,0,'J',0);
		$pdf->Cell(25,6,$fila['Anno_Resolucion']."-".$fila['NroResolucion'],1,0,'C',0);
		$pdf->Cell(25,6,$fila['Anno_Providencia']."-".$fila['NroAutorizacion'],1,0,'C',0);
		$pdf->Cell(30,6,number_format($fila['Multa_DF'],2),1,1,'R',0);
		$item=$item+1;
	}
}*/

/*$IDActualizar = "UPDATE Salida_Expediente SET Status=2 WHERE Anno_memo=".$añomemo." and NroMemo=".$numeromemo." and Status=1";

$result = odbc_exec ($conexion, $IDActualizar);*/

//FIN LISTAR REGISTROS

//--

// FIN DE LA VALIDACION DE LA CONSULTA

$pdf->Output(); // OBLIGATORIO PARA IMPRIMIR

?>
