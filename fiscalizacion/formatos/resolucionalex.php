<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

require('../../funciones/fpdf.php');
include('../../conexion.php');
include('../../funciones/auxiliar_php.php');
include "../../funciones/numerosALetras.class.php";
//mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
	{
	function Footer()
		{    
		//Posición a 1,5 cm del final
		$this->SetY(-12);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//N�mero de página
		$this->Cell(0,0,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','nuevo');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=15);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

// ACTUALIZACION DEL NUMERO DE LA RESOLUCION
generar_resolucion ($_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);

////////// NUMERO DE LA RESOLUCION
list ($resolucion, $fecha_res, $num_res, $anno_res) = funcion_resolucion($_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);

////////// INFORMACION DE LA PROVIDENCIA
$consulta_datos = "SELECT * FROM vista_providencias WHERE anno=0".$_SESSION['ANNO_PRO']." AND numero=0".$_SESSION['NUM_PRO']." AND sector =0".$_SESSION['SEDE'].";"; //echo $consulta_datos; 
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
//alex
$programa = $registro_datos->TipoPrograma;
 $nombre = $registro_datos->nombre;
//fin alex
// ---------------------

////////// SIGLAS DE LA resolucion
$SIGLAS = $registro_datos->Siglas_resol_fis;
// ---------------------

////////// SIGLAS DE LA PROVIDENCIA
$SIGLAS1 = $registro_datos->Siglas1;
$SIGLAS2 = $registro_datos->Siglas2;
// ---------------------

////////// SIGLAS DE LA RESOLUCION Y PROVIDENCIA
$resolucion_prov = $SIGLAS."/".$_SESSION['ANNO_PRO']."/".$SIGLAS2."/".$SIGLAS1.sprintf("%004s", $_SESSION['NUM_PRO']);
// ---------------------

// ---------------------
$pdf->SetFont('Arial','B',15);
$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
//$pdf->SetFont('Times','B',11); $pdf->Ln(8);
$pdf->SetFont('Times','B',11); $pdf->Ln(6);
$pdf->Cell(0,5,utf8_decode('N°    ').$resolucion);
//$pdf->Ln(10);

////////// GERENCIA, SECTOR O UNIDAD DE EMISION
$Sede = $registro_datos->texto_sede;
// -----------

////////// CIUDAD DE EMISION
$Ciudad = $registro_datos->nombre;
// -----------

list($anno,$mes,$dia)=explode('-',$fecha_res);
$FECHA=mktime(0,0,0,$mes,$dia,$anno);
$_SESSION['VARIABLE']=$FECHA;

$t=(140-(strlen($Ciudad)));

$mes=array(0,Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);

$pdf->Text($t,24,$Ciudad.', '.dia($fecha_res).' de '.$mes[abs(mes($fecha_res))].' del '.anno($fecha_res));
//$pdf->Ln(10);
$pdf->Ln(6);
//----------------------- FIN
	
$pdf->SetFont('Times','B',13-$tamaño);

//----------------------------------------------- PARA VERIFICAR SI TIENE ACTA DE REPARO
include "../0_buscar_acta_y_prov.php";

//---------------
if ($monto_pagado > 0)
	{
	$pdf->Cell(0,5,'RESOLUCION',0,0,'C'); 
	$pdf->Ln(7);
	//--------- TITULO
	$consulta_xcot = "SELECT cot FROM vista_detalle_actas WHERE anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE']." group by cot;";
$tabla_xcot = mysql_query($consulta_xcot);
	//----------------
	$txta=''; $txtb=''; $txtc=''; $txtd=''; $txte='';
	//----------------	
	while ($registro_xcot = mysql_fetch_object($tabla_xcot))
		{
		switch ($registro_xcot->COT)
			{
				case "22":
					$txta = 'Artículo 22 Ley de Impuesto a las Actividades de Envite o Azar';
					break;		
				case "111":
				case "112#1":
				case "112#2":
				case "112#3":
				case "112#4":
					$txtb = 'Artículo 186 del Código Orgánico Tributario del 2001';
					break;
				case "113":
					$txte = 'Artículo 109 del Código Orgánico Tributario del 2001';
					break;
				case "114#1":
				case "114#2":
				case "115#1":
				case "115#2":
				case "115#3":
				case "115#4":
					$txtc = 'Artículo 109 del Código Orgánico Tributario del 2020';
					break;																				
				case "112":
					$txtd = 'Artículo 196 del Código Orgánico Tributario del 2020';
					break;
				}
		}
		//----------------
		$pdf->SetFont('Times','B',11-$tamaño);
		if ($txta<>'') {	$pdf->Cell(0,5,utf8_decode($txta),0,0,'C'); 	$pdf->Ln(5);	}
		//---------------------------------
		if ($txtb<>'' and $txte<>'') 
			{	
			$pdf->Cell(0,5,utf8_decode('Artículo 109 y 196 del Código Orgánico Tributario del 2001'),0,0,'C'); 	$pdf->Ln(5);	
			}
		else
			{
			if ($txtb<>'') {	$pdf->Cell(0,5,utf8_decode($txtb),0,0,'C'); 	$pdf->Ln(5);	}
			if ($txte<>'') {	$pdf->Cell(0,5,utf8_decode($txte),0,0,'C'); 	$pdf->Ln(5);	}
			}
		//---------------------------------
		if ($txtc<>'' and $txtd<>'') 
			{	
			$pdf->Cell(0,5,utf8_decode('Artículo 109 y 196 del Código Orgánico Tributario del 2020'),0,0,'C'); 	$pdf->Ln(5);	
			}
		else
			{
			if ($txtc<>'') {	$pdf->Cell(0,5,utf8_decode($txtc),0,0,'C'); 	$pdf->Ln(5);	}
			if ($txtd<>'') {	$pdf->Cell(0,5,utf8_decode($txtd),0,0,'C'); 	$pdf->Ln(5);	}
			}
	//----------------
	$pdf->Cell(0,5,'(Allanamiento)',0,0,'C'); 	$pdf->Ln(10);
	$pdf->SetFont('Times','B',11);

	////////// SIGLAS DEL ACTA
	$resolucion_acta = $resolucion_prov.'/'.$registro_acta->anno.'/'.sprintf("%004s", $registro_acta->numero) ;
	// ---------------------
	
	$pdf->SetFont('Times','',11-$tamaño);
	
	//---------------------------------------------------------------
	$txt1 = $Sede.' Gerencia Regional de Tributos Internos '.buscar_region().' del Servicio Nacional Integrado de Administración Aduanera y tributaria (SENIAT) del Ministerio del Poder Popular de Planificación y Finanzas, ';//de conformidad con lo establecido en el Artículo 
		
	$txt2 =  'procede a emitir la presente resolución con ocasion del Acta de Reparo N° '. $resolucion_acta .' de fecha '.voltea_fecha($registro_acta->fecha).', de conformidad con el Artículo '.$articulo2.' ejusdem, levantada al contribuyente: '.strtoupper($registro_datos->contribuyente).' RIF. N° '. formato_rif($registro_datos->rif.', domiciliado en '.ucwords(strtolower(trim($registro_datos->direccion))).'.');//$articulo . ' del Código Orgánico Tributario vigente para el periodo o ejercicio investigado,
	
	$txt = $txt1 . $txt2 ;
	
	//---------------------------------------------------------------
	
	$pdf->MultiCell(0,5,utf8_decode($txt));
	$pdf->Ln(2); 
	
	$txt='De la fiscalización realizada por el funcionario: '.$registro_datos->Nombres1.' '.$registro_datos->Apellidos1.' titular de la cédula de Identidad N° V - '.formato_cedula($registro_datos->ci_fiscal1).', adscrito(a) '.$registro_datos->adscripcion.' de la Gerencia Regional de Tributos Internos Región Los Llanos, debidamente facultado, según Providencia Administrativa (Investigación Fiscal) N° '.$resolucion_prov.' de fecha '.voltea_fecha($registro_datos->fecha_emision).', surgieron las siguientes objeciones:';
	$pdf->MultiCell(0,5,utf8_decode($txt));
	//$pdf->Ln(7); 
	
	//----------------XXXXXXXXXXXXXXXXXXX PARA REVISAR LA CANTIDAD DE TRIBUTOS QUE POSEE XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	$consulta = "SELECT tributo FROM vista_detalle_actas WHERE monto_pagado>0 AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE']." GROUP BY tributo;";
	$tabla = mysql_query($consulta);
	
	while ($registro_tributo = mysql_fetch_object($tabla))
		{
		//----------------
		$consulta_xx = "SELECT * FROM vista_detalle_actas WHERE monto_pagado>0 AND tributo=".$registro_tributo->tributo." AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE'].";";
		$tabla_xx = mysql_query($consulta_xx);
		//----------------
		$registro_acta = mysql_fetch_object($tabla_xx);
		//----------------

		$pdf->SetFont('Times','B',11-$tamaño);
		//-----------------------------------------------------------------------------------------XXXXXXXXXXXXXXXXXXXXXXXXXX
		$pdf->Ln(5);
		$txt = 'TRIBUTO:';
		$pdf->MultiCell(0,5, utf8_decode($txt));
		
		$txt = $registro_acta->nombre.' ('.$registro_acta->siglas.')';
		$pdf->MultiCell(0,5, utf8_decode($txt));
		
		$pdf->SetFont('Times','',11-$tamaño);
		
		///-------------------- PARA SEPARAR POR COT ---------------------------------------------------XXXXXXXXXXXXXXXXXXXXXXXXX
		$consulta_cot = "SELECT COT FROM vista_detalle_actas WHERE monto_pagado>0 AND tributo=".$registro_tributo->tributo." AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE']." GROUP BY COT;";
		$tabla_cot = mysql_query($consulta_cot);
		while ($registro_cot = mysql_fetch_object($tabla_cot))
			{		
			//-----------------------------
			$COT = $registro_cot->COT;
			//-----------------------------
			//-------------- PARA REVISAR SI FUE TOTAL O PARCIAL
			$consulta_x = "SELECT Sum(fis_actas_detalle.impuesto_omitido) - Sum(fis_actas_detalle.monto_pagado) as total FROM fis_actas_detalle, fis_actas WHERE fis_actas_detalle.id_acta = fis_actas.id_acta AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE']." AND fis_actas_detalle.COT='".$COT."';"; //echo $consulta_x;
			$tabla_x = mysql_query($consulta_x); 
			//----------------
			if ($registro_x = mysql_fetch_object($tabla_x))
				{
				if ($registro_x->total>0)
					{$reparo = 'parcialmente';	} 
				else {	$reparo = 'totalmente'; }
				}
	
			//---------------------------------------------------------------
			switch ($COT)
				{
				case "22":
					$articulo = '196';
					$articulo2 = '194';
					$articulo4 = '91';
					$txt1 = '   Artículo 22 Ley de Impuesto a las Actividades de Envite o Azar';
					break;		
				case "111":
					$articulo = '186';
					$articulo2 = '184';
					$articulo4 = '94';
					$txt='Se determinó reparo en la Declaracion y pago del '.ucwords(strtolower($registro_acta->nombre)).'. En tal sentido, el contribuyente aceptó '.$reparo.' el reparo formulado, mediante la presentación de la(s) Declaracion(es) Sustitutiva(s) del '.ucwords(strtolower($registro_acta->nombre)).' y el pago del impuesto resultante; todo de conformidad con lo dispuesto en el Artículo '.$articulo.' del Código Orgánico Tributario vigente para el periodo o ejercicio investigado';
					$txt1 = '   Artículo 186 del Código Orgánico Tributario del 2001';
					break;
				case "112#1":
				case "112#2":
				case "112#3":
				case "112#4":
					$numeral = substr($COT,4,1);
					if ($numeral == 1) { $texto = 'omisión'; } else { $texto = 'retraso'; }
					if ($numeral == 1) { $texto2 = 'Omisión en el Pago'; } else { $texto2 = 'Pago Extemporáneo'; }
					if (strtolower($registro_acta->nombre)=='ISLR') {	$texto4 =' Definitiva';	} else {$texto4 ='';}
					//--------------
					$articulo = '186';
					$articulo2 = '184';
					$articulo4 = '94';
					$txt='Se determinó '.$texto.' en el pago de los monto o porciones de la Declaración'.$texto4.' del '.ucwords(strtolower($registro_acta->nombre)).'. En tal sentido, el contribuyente aceptó '.$reparo.' el reparo formulado, mediante el pago del(los) monto(s) o porcion(es); todo de conformidad con lo dispuesto en el Artículo '.$articulo.' del Código Orgánico Tributario vigente para el periodo o ejercicio investigado';
					$txt1 = '   Artículo 186 del Código Orgánico Tributario del 2001';
					break;
				case "113":
					$articulo = '109';
					$articulo2 = '184';
					$articulo3 = '185';
					$articulo4 = '94';
					$txt='Se determinó reparo en la Declaracion y pago de las Retenciones del '.ucwords(strtolower($registro_acta->nombre)).'. En tal sentido, el contribuyente aceptó '.$reparo.' el reparo formulado, mediante la Declaración y Pago de las Retenciones efectuadas; todo de conformidad con lo dispuesto en el Artículo '.$articulo3.' del Código Orgánico Tributario vigente para el periodo o ejercicio investigado';
					$txt1 = '   Artículo 109 del Código Orgánico Tributario del 2001';
					break;																				
				case "114#1":
				case "114#2":
				case "115#1":
				case "115#2":
				case "115#3":
				case "115#4":
					$articulo = '109';
					$articulo2 = '194';
					$articulo3 = '195';
					$articulo4 = '91';		
					$txt='Se determinó reparo en la Declaracion y pago de las Retenciones del '.ucwords(strtolower($registro_acta->nombre)).'. En tal sentido, el contribuyente aceptó '.$reparo.' el reparo formulado, mediante la Declaración y Pago de las Retenciones efectuadas; todo de conformidad con lo dispuesto en el Artículo '.$articulo3.' del Código Orgánico Tributario vigente para el periodo o ejercicio investigado';
					$txt1 = '   Artículo 109 del Código Orgánico Tributario del 2020';
					break;																				
				case "112":
					$articulo = '196';
					$articulo2 = '194';
					$articulo4 = '91';
					$txt='Se determinó '.$texto.' en el pago de los monto o porciones de la Declaración'.$texto4.' del '.ucwords(strtolower($registro_acta->nombre)).'. En tal sentido, el contribuyente aceptó '.$reparo.' el reparo formulado, mediante el pago del(los) monto(s) o porcion(es); todo de conformidad con lo dispuesto en el Artículo '.$articulo.' del Código Orgánico Tributario vigente para el periodo o ejercicio investigado.';
					$txt1 = '   Artículo 196 del Código Orgánico Tributario del 2020';
					break;
				}
			//---------------------------------------------------------------
			$pdf->Ln(2);
			$pdf->SetFont('Times','B',11-$tamaño);
			$pdf->MultiCell(0,5,utf8_decode($txt1));
			$pdf->SetFont('Times','',11-$tamaño);
			$pdf->Ln(2);
			//---------------------------------------------------------------
			
			 $txt = $txt . ' y verificado dicho pago a través del ISENIAT.';
			//$txt = $txt . ' y verificado dicho pago a través del Sistema Venezolano de Información Tributaria (SIVIT).';
			$pdf->MultiCell(0,5, utf8_decode($txt));
			$pdf->Ln(1);
			
			$txt='Tal como se detalla en el cuadro siguiente:';
			$pdf->MultiCell(0,5, utf8_decode($txt));
			$pdf->Ln(4);
				
			// TITULOS TABLA PAGOS
				
			$pdf->SetFont('Times','',8);
			$pdf->SetFillColor(192,192,192);
			
			$pdf->Cell(10,5,'',0,0,'C');
			
			$txt='PERIODO';
			$pdf->Cell(40,5,$txt,1,0,'C','true');
			
			$txt='PLANILLA';
			$pdf->Cell(40,5,$txt,1,0,'C','true');
			
			$txt='FECHA PAGO';
			$pdf->Cell(35,5,$txt,1,0,'C','true');
			
			$txt='MONTO PAGADO';
			$pdf->Cell(40,5,$txt,1,0,'C','true');
			
			$pdf->Ln(5);
			
			// LLENADO DE LA TABLA DE PAGOS
			
			$Total = 0;
			
			$consulta_x = "SELECT * FROM vista_detalle_actas WHERE COT='".$COT."' AND monto_pagado>0 AND tributo=".$registro_tributo->tributo." AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE'].";";
			$tabla_x = mysql_query($consulta_x);
			
			while ($registro_x = mysql_fetch_object($tabla_x))
				{
				$pdf->Cell(10,4,'',0,0,'C');
				$txt = voltea_fecha($registro_x->periodo_desde).' al '.voltea_fecha($registro_x->periodo_hasta);
				$pdf->Cell(40,4,$txt,1,0,'C');
				$txt = $registro_x->planilla;
				$pdf->Cell(40,4,$txt,1,0,'C');
				$txt = voltea_fecha($registro_x->fecha_pago);
				$pdf->Cell(35,4,$txt,1,0,'C');
				$txt = formato_moneda($registro_x->monto_pagado);
				$pdf->Cell(40,4,$txt,1,0,'C');
				$pdf->Ln(4);
				$Total = $Total + $registro_x->monto_pagado ;
				//$Fecha_Pago = $registro_x->fecha_pago;
				$fecha_moneda = $registro_x->periodo_hasta;
				}
			
				$pdf->Cell(10,4,'',0,0,'C');
				$txt = 'Total =>';
				$pdf->Cell(115,4,$txt,1,0,'C','True');
				$txt = formato_moneda($Total);
				$pdf->Cell(40,4,$txt,1,0,'C');
				
				// ---------------------- FIN DEL LLENADO DE LA TABLA DE PAGOS
				
				$pdf->Ln(8);
				$pdf->SetFont('Times','',11-$tamaño);
				
				//------------------------------------------------------------------------------ FIN
					
		//---------------------------------------------------------------
		if (substr($COT,3,1)=='#')
			{
			$txt='En consecuencia, se procede a liquidar la(s) multa(s) correspondiente(s) conforme a lo dispuesto en el Artículo '.substr($COT,0,3).' numeral '.substr($COT,4,1).' del Código Orgánico Tributario y al cálculo de los Intereses Moratorios como lo indica el Artículo 66 del precitado Código. ';
			}
		else
			{
			switch ($COT)
				{
				case "22":
					$txt='En consecuencia, se procede a liquidar la(s) multa(s) correspondiente(s) conforme a lo dispuesto en el Artículo 22 de la Ley de Impuesto a las Actividades de Envite y Azar. ';
					break;
				default:
					$txt='En consecuencia, se procede a liquidar la(s) multa(s) correspondiente(s) conforme a lo dispuesto en el Artículo '.substr($COT,0,3).' del Código Orgánico Tributario vigente para el periodo o ejercicio investigado. ';
					break;																				
				}
			}
		//---------------------------------------------------------------
		
		$pdf->MultiCell(0,5, utf8_decode($txt));
		//$pdf->Ln(7); 
		
	//------------------------------------------------------------------------------------------------- PARRAFO DE MULTA
	
	$pdf->SetFont('Times','B',11-$tamaño);
	$pdf->Ln(2);
	
	switch (substr($COT,0,3))
		{
		case 22:
			$pdf->Cell(0,5,'Multa (Art. 22 Ley de Impuesto a las Actividades de Envite o Azar)',0,0,'C'); 
			break;
		default:
			$pdf->Cell(0,5,'Multa (Art. '.substr($COT,0,3).' C.O.T.)',0,0,'C'); 
			break;			
		}
	//---------------------------------------------------------------
	
	$pdf->Ln(7);
	
	$pdf->SetFont('Times','',11-$tamaño);
	
	//-------------------------
	$paragrafo = array(0,Primero,Segundo,Tercero,Cuarto);
	//-------------------------
		
	switch ($COT)
		{
		// ----------------------
		case "22":
			// VALIDACION DEL TRIBUTO SI ES POR SUCESIONES
			$texto = 'Impuesto a las Actividades de Juegos de Envite y Azar';
			// ----------------------
			$txt='Asimismo, se impone al contribuyente supra identificado, la pena prevista en el Artículo 22 de la Ley de Impuesto a las Actividades de Envite o Azar, y el Artículo 91, 92 y 94 del Código Orgánico Tributario vigente para el periodo o ejercicio investigado, reparado por concepto de '.$texto.', lo que originó una diferencia de impuesto a pagar, el cual fue cancelado dentro del término de los quince (15) días hábiles, contenidos en el Artículo 196 del Codigo Orgánico Tributario. Así se declara.';
			break;
		// ----------------------
		case "111":
			// VALIDACION DEL TRIBUTO SI ES POR SUCESIONES
			if ($registro_acta->tributo==9) {$texto='Patrimonio Neto Hereditario';} else {$texto = 'Débito Fiscal no declarado';}
			// ----------------------
			$txt='Asimismo, se impone al contribuyente supra identificado, la pena prevista en el Artículo 111 Parágrafo Segundo, y el Artículo 94 del Código Orgánico Tributario vigente para el periodo o ejercicio investigado, por concepto de reparos formulados, lo que originó una diferencia de impuesto a pagar, el cual fue cancelado dentro del término de los quince (15) días hábiles, contenidos en el citado Artículo. Así se declara.';
			break;
		// ----------------------
		case "112#1":
			case "112#2":
			case "112#3":
			case "112#4":
			$txt='Asimismo, se impone al contribuyente supra identificado, la pena prevista en el Artículo 112 Parágrafo '.$paragrafo[substr($COT,4,1)].', y el Artículo 94 del Código Orgánico Tributario vigente para el periodo o ejercicio investigado, por concepto de '.$texto2.' de la(s) porcion(es) de la Declaración'.$texto4.' de '.ucwords(strtolower($registro_acta->nombre)).', el cual fue cancelado dentro del término de los quince (15) días hábiles, contenidos en el citado Artículo. Así se declara.';
			break;
		// ----------------------
		case "112":
			$txt='Se impone al contribuyente supra identificado, la pena prevista en el Artículo 112, y el Artículo 91 del Código Orgánico Tributario vigente para el periodo o ejercicio investigado, por concepto de '.$texto2.' de la(s) porcion(es) de la Declaración'.$texto4.' de '.ucwords(strtolower($registro_acta->nombre)).', el cual fue cancelado dentro del término de los quince (15) días hábiles, contenidos en el citado Artículo. Así se declara.';
			break;			
		// ----------------------
		case "113":
			$txt='Asimismo, se impone al contribuyente supra identificado, la pena prevista en el Artículo '.substr($COT,0,3).', y el Artículo 94 del Código Orgánico Tributario vigente para el periodo o ejercicio investigado, reparado por enterar con retardo el Impuesto Retenido. Así se declara.';
			break;																				
		// ----------------------
		case "114#1":
			case "114#2":
			case "115#1":
			case "115#2":
			case "115#3":
			case "115#4":
			$txt='Asimismo, se impone al contribuyente supra identificado, la pena prevista en el Artículo '.substr($COT,0,3).' numeral '.substr($COT,4,1).', y el Artículo 91 del Código Orgánico Tributario vigente para el periodo o ejercicio investigado, reparado por enterar con retardo el Impuesto Retenido. Así se declara.';
			break;																				
		}
	//---------------------------------------------------------------
	
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(2); 
	//$pdf->Ln(3);
	// TITULOS TABLA MULTA
	
	$pdf->SetFont('Times','',8);
	$pdf->SetFillColor(192,192,192);
	
	$txt='Periodo';
	$pdf->Cell(30,6,$txt,1,0,'C','true');
	
	$txt='Monto Pagado (Bs.)';
	$pdf->Cell(25,6,$txt,1,0,'C','true');
	
	$txt='Fecha Venc';
	$pdf->Cell(15,6,$txt,1,0,'C','true');
	
	$txt='Fecha Pago';
	$pdf->Cell(15,6,$txt,1,0,'C','true');
	
	$txt='Monto (Bs.)';
	$pdf->Cell(20,6,$txt,1,0,'C','true');
	
	if (fecha_a_numero($fecha_moneda) >= fecha_a_numero('2020/03/01'))
		{	
		$pdf->SetFont('Times','',7);
		$txt='Moneda Prim.';
		$pdf->Cell(16,6,$txt,1,0,'C','true');
		
		$txt='Cant. Mon. Prim.';
		$pdf->Cell(20,6,$txt,1,0,'C','true');
		
		$txt='Moneda Act.';
		$pdf->Cell(15,6,$txt,1,0,'C','true');
		$pdf->SetFont('Times','',8);
		}
	else
		{	
		$txt='UT Primitiva';
		$pdf->Cell(16,6,$txt,1,0,'C','true');
		
		$txt='Cant. UT Prim.';
		$pdf->Cell(20,6,$fecha_moneda,1,0,'C','true');
		
		$txt='U.T. Actual';
		$pdf->Cell(15,6,$txt,1,0,'C','true');
		}
		
	$txt='Multa (Bs.)';
	$pdf->Cell(20,6,$txt,1,0,'C','true');
	
	$pdf->Ln(4);
	//$pdf->Ln(6);
	
	// LLENADO DE LA TABLA DE MULTA
	
	$Total = 0;
	$UT_Primitivas_Aplicadas = 0;
	$Multa = 0;
	$Multa_nueva = 0;
	
	$consulta_x = "SELECT * FROM vista_detalle_actas WHERE tributo=".$registro_tributo->tributo." AND COT='".$COT."' AND monto_pagado>0 AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE'].";";
	$tabla_x = mysql_query($consulta_x);
	
	while ($registro_x = mysql_fetch_object($tabla_x))
	{
		$tributo = $registro_x->tributo;
		
		$txt = voltea_fecha($registro_x->periodo_desde).'-'.voltea_fecha($registro_x->periodo_hasta);
		$pdf->Cell(30,4,$txt,1,0,'C');
		
		$txt = number_format(doubleval($registro_x->monto_pagado),2,',','.');
		$pdf->Cell(25,4,$txt,1,0,'C');
		
		$txt = voltea_fecha($registro_x->fecha_vencimiento);
		$pdf->Cell(15,4,$txt,1,0,'C');
	
		$txt = voltea_fecha($registro_x->fecha_pago);
		$pdf->Cell(15,4,$txt,1,0,'C');
		
		$txt = number_format(doubleval($registro_x->multa_primitiva),2,',','.');
		$pdf->Cell(20,4,$txt,1,0,'C');
		
		$txt = number_format(doubleval(($registro_x->multa_primitiva/$registro_x->UT_primitiva)),5,',','.');
		$pdf->Cell(16,4,$txt,1,0,'C');
		
		$txt = number_format(doubleval($registro_x->UT_primitiva),2,',','.'); 
		$pdf->Cell(20,4,$txt,1,0,'C');
		
		if (fecha_a_numero($fecha_moneda) >= fecha_a_numero('2020/03/01'))
			{	
			$txt =  number_format(doubleval(moneda_infraccion($registro_x->fecha_pago)),2,',','.');
			}
		else
			{	
			$txt =  number_format(doubleval($_SESSION['VALOR_UT_ACTUAL']),2,',','.');
			}
		$pdf->Cell(15,4,$txt,1,0,'C');
		
		$txt = number_format(doubleval($registro_x->multa_actual),2,',','.');
		$pdf->Cell(20,4,$txt,1,0,'C');
		
		$pdf->Ln(4);
		
		$Total = $Total + $registro_x->monto_pagado ;
		$Multa = $Multa + $registro_x->multa_primitiva;
		$UT_Primitivas_Aplicadas = $UT_Primitivas_Aplicadas + ($registro_x->multa_primitiva/$registro_x->UT_primitiva);
		$Multa_nueva = $Multa_nueva + $registro_x->multa_actual;
		
	}
	
	$txt = 'Totales =>';
	$pdf->Cell(30,4,$txt,1,0,'C','True');
	
	$txt = number_format(doubleval($Total),2,',','.');
	$pdf->Cell(25,4,$txt,1,0,'C');
	
	$txt = '';
	$pdf->Cell(15,4,$txt,1,0,'C','true');
	
	$txt = '';
	$pdf->Cell(15,4,$txt,1,0,'C','true');
	
	$txt = number_format(doubleval($Multa),2,',','.');
	$pdf->Cell(20,4,$txt,1,0,'C');		
	
	$txt = '';
	$pdf->Cell(16,4,$txt,1,0,'C','true');	
	
	$txt = '';//number_format(doubleval($UT_Primitivas_Aplicadas),2,',','.');
	$pdf->Cell(20,4,$txt,1,0,'C','true');		
	
	$txt = '';//number_format(doubleval($_SESSION['VALOR_UT_ACTUAL']),2,',','.');
	$pdf->Cell(15,4,$txt,1,0,'C','true');	
	
	$txt = number_format(doubleval($Multa_nueva),2,',','.');
	$pdf->Cell(20,4,$txt,1,0,'C');			
	
	$pdf->Ln(5);
	$txt='          Conversión del Monto con la Unidad Tributaria Actual (Artículo '.$articulo4.' del Código Orgánico Tributario).';
	$pdf->MultiCell(0,5, utf8_decode($txt)); 
	//$pdf->Ln(5);
	// ---------------------- FIN DEL LLENADO DE LA TABLA DE MULTAS
				}
		}
	
	// PARRAFO DE INTERESES MORATORIOS
	$pdf->Ln(3);
		
		//---------- POR SI EL TITULO ESTA MUY AL PIE DE PAGINA
		if ($pdf->GetY()>=274) {$pdf->AddPage();}
		
	$pdf->SetFont('Times','B',11);
	$pdf->Cell(0,5,'INTERESES MORATORIOS'.$y,0,0,'C'); 
	$pdf->Ln(10);
	
	$pdf->SetFont('Times','',11);
	
	$txt='Procede '.strtolower(utf8_decode($Sede)).utf8_decode(' Gerencia Regional de Tributos Internos, al cálculo de Intereses Moratorios de conformidad con el Artículo 66 del Código Orgánico tributario, de acuerdo a la tasa activa promedio publicada por el banco Central de venezuela, incrementada en los términos establecidos en dicho Artículo, cálculo éste que se efectua a partir del día siguiente al vencimiento del plazo establecido para la autoliquidación y pago del tributo, es decir desde el vencimiento de cada periodo hasta el momento de efectuado el pago, según consta en la(s) planilla(s) de pago, correspondiente a los periodos tributarios que originaron el impuesto a pagar. Así se declara.');
	$pdf->MultiCell(0,5, $txt);
	$pdf->Ln(5); 
	
	$txt='Se calculan utilizando la siguiente fórmula:
	Intereses Moratorios = C x R x T / t (año) x 100';
	$pdf->MultiCell(0,5, utf8_decode($txt)); 
	$pdf->Ln(5); 
	
	$txt='Donde:
	C = Capital
	R = Tasa de interés
	T = Tiempo de mora
	 t = 360 x 100';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(5); 
	
	$txt='A continuación se muestra el cuadro resumen de los intereses moratorios, los cuales se detallan en la aplicacion de la citada fórmula.';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(5); 
	
	// TITULOS DE LA PRIMERA TABLA DE INTERES
	
	$pdf->SetFont('Times','',8);
	$pdf->SetFillColor(192,192,192);
	
	$txt='Periodo';
	$pdf->Cell(30,6,$txt,1,0,'C','true');
	
	$txt='Impuesto Determinado (Bs.)';
	$pdf->Cell(35,6,$txt,1,0,'C','true');
	
	$txt='Fecha vencimiento';
	$pdf->Cell(25,6,$txt,1,0,'C','true');
	
	$txt='Fecha Pago';
	$pdf->Cell(25,6,$txt,1,0,'C','true');
	
	$txt='Dias de Mora';
	$pdf->Cell(25,6,utf8_decode($txt),1,0,'C','true');
	
	$txt='Intereses Moratorios (Bs.)';
	$pdf->Cell(35,6,$txt,1,0,'C','true');
	
	$pdf->Ln(6);
	
	// LLENADO DE LA PRIMERA TABLA DE INTERES
	
	$Total = 0;
	$Interes = 0;
	$Dias_Total = 0;
	
	$consulta_x = "SELECT * FROM vista_detalle_actas WHERE monto_pagado>0 AND anno_prov=".$_SESSION['ANNO_PRO']." AND num_prov=".$_SESSION['NUM_PRO']." AND id_sector=".$_SESSION['SEDE'].";";
	$tabla_x = mysql_query($consulta_x);
	
	while ($registro_x = mysql_fetch_object($tabla_x))
	{
		$txt = voltea_fecha($registro_x->periodo_desde).'-'.voltea_fecha($registro_x->periodo_hasta);
		$pdf->Cell(30,4,$txt,1,0,'C');
		
		$txt =number_format(doubleval($registro_x->monto_pagado),2,',','.');
		$pdf->Cell(35,4,$txt,1,0,'C');
		
		$txt = voltea_fecha($registro_x->fecha_vencimiento);
		$pdf->Cell(25,4,$txt,1,0,'C');
		
		$txt = voltea_fecha($registro_x->fecha_pago);
		$pdf->Cell(25,4,$txt,1,0,'C');
		
			list($anno,$mes,$dia)=explode('-',$registro_x->fecha_pago);
			$FECHA_PAGO = mktime(0,0,0,$mes,$dia,$anno);
			
			list($anno,$mes,$dia)=explode('-',$registro_x->fecha_vencimiento);
			$FECHA_VENCIMIENTO = mktime(0,0,0,$mes,$dia,$anno);
			
			$Dias = $FECHA_PAGO - $FECHA_VENCIMIENTO;
		
		$txt = number_format(redondea($Dias/86400),0);
		$pdf->Cell(25,4,$txt,1,0,'C');
		
		$txt = number_format(doubleval($registro_x->interes),2,',','.');
		$pdf->Cell(35,4,$txt,1,0,'C');
			
		$pdf->Ln(4);
		
		$Total = $Total + $registro_x->monto_pagado ;
		$Interes = $Interes + $registro_x->interes ;
		$Dias_Total = $Dias_Total + $Dias;
	} //////////////////
	
	$txt = 'Totales =>';
	$pdf->Cell(30,4,$txt,1,0,'C','True');
	
	$txt = number_format(doubleval($Total),2,',','.');
	$pdf->Cell(35,4,$txt,1,0,'C');
	
	$txt = '';
	$pdf->Cell(50,4,$txt,1,0,'C','true');
	
	$txt = number_format(redondea($Dias_Total/86400),2);
	$pdf->Cell(25,4,$txt,1,0,'C');
	
	$txt = number_format(doubleval($Interes),2,',','.');
	$pdf->Cell(35,4,$txt,1,0,'C');
	
	$pdf->Ln(5);
		
	$txt='Aplicación de la fórmula: Intereses Moratorios = C x R x T / (año) 360 x 100';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	
	$pdf->Ln(5); 
	// ---------------------- FIN DEL LLENADO DE LA PRIMERA TABLA DE INTERES
	}
//---------------------------------------------- FIN SI TIENE ACTA DE REPARO

$pdf->SetFont('Times','',11-$tamaño);

// --------------------------------------------- SI TIENE SANCIONES VDF
if ($monto_pagado<=0)
	{$pdf->SetFont('Times','B',11-$tamaño);
	
	$pdf->Cell(0,5,'RESOLUCION DE IMPOSICION DE SANCION',0,0,'C'); 
	$pdf->Ln(5);
	//$pdf->Ln(10);

	$pdf->SetFont('Times','',11);
	$pdf->Cell(0,5,'Contribuyente:');
	
	$pdf->SetFont('Times','B',11);
	$pdf->SetX(60);
	$pdf->MultiCell(0,5,utf8_decode(strtoupper($registro_datos->contribuyente)));
	$pdf->Ln(3); 
	
	$pdf->SetFont('Times','',11);
	$pdf->Cell(0,5,utf8_decode('RIF N°:')); 
	
	$pdf->SetFont('Times','B',11);
	$pdf->SetX(60);
	$pdf->Cell(0,5,formato_rif($registro_datos->rif));
	$pdf->Ln(8); 
	
	$pdf->SetFont('Times','',11);
	$pdf->Cell(0,5,'Domicilio Fiscal:'); 
	
	$pdf->SetFont('Times','B',11);
	$pdf->SetX(60);
	$pdf->MultiCell(0,5,strtoupper(trim($registro_datos->direccion)));

	$pdf->SetFont('Times','',10);
	$pdf->Ln(5);
	$txt= 'De conformidad con lo establecido en los Artículos 89, 131, 133, 182 y 183 del Codigo Orgánico Tributario Vigente, en concordancia con lo dispuesto en el numeral 14 del Artículo 98 de la Resolución N° 32 de fecha 24/03/1995, sobre la organización, atribuciones y funciones del Servicio Nacional Integrado de Administración Aduanera y Tributaria SENIAT, publicada en Gaceta Oficial N° 4.881 Extraordinario de fecha 29/03/1995, Artículo 1, 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, y en el Artículo 2 de la Resolución SNAT/2002/N° 913 de fecha 06/02/2002, publicada en Gaceta Oficial N° 37.398 de fecha 06/03/2002, se procede a emitir la presente Resolución de Imposición de Sanción, por cuanto se constató para el momento de la verificación practicada el incumplimiento del (los) Deber(es) Formal(es), que se indica(n) a continuación, tal como consta en Acta de Recepción y Verificación Fiscal o Constancia de Incumplimiento levantada por el (la) Funcionario(a) actuante '.$registro_datos->Nombres1.' '.$registro_datos->Apellidos1.' titular de la cédula de Identidad N° V-'.formato_cedula($registro_datos->ci_fiscal1).' debidamente facultado(a) según Providencia Administrativa N° '.$resolucion_prov.' de fecha '.voltea_fecha($registro_datos->fecha_emision).'.';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(4); 
	}

////////// REVISION SI EXISTEN SANCIONES PARA CONTRIBUYENTES ESPECIALES

$consulta_xxx = "SELECT especial FROM vista_sanciones_aplicadas WHERE especial>1 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_xxx = mysql_query($consulta_xxx);
if ($registro_xxx = mysql_fetch_object($tabla_xxx))
	{
	////////// IMPRESION DEL TEXTO
	$txt='En virtud de lo establecido en el único aparte del Artículo 108 del Codigo Orgánico Tributario y por cuanto el contribuyente identificado supra, está calificado por la Administración tributaria como Sujeto Pasivo Especial, se procede a aumentar las sanciones pecuniarias impuestas tipificadas en el Capítulo II del citado Código en un doscientos por ciento (200%).';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(4);
	}

////////// VALIDACION DE LAS SANCIONES
$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla = mysql_query($consulta);

/////////------------------------+
if ($registro_s = mysql_fetch_object($tabla))
	{
	if ($serie>=0)
		{
		////////////////// SANCIONES APLICADAS
		$pdf->SetFont('Times','B',11-$tamaño);
		$pdf->Cell(0,5,'SANCIONES POR INCUMPLIMIENTO DE DEBERES FORMALES',0,0,'C'); 
		$pdf->SetFont('Times','',11-$tamaño);
		$pdf->Ln(10);
		}
	
//////// DATOS DE LAS SANCIONES
$consulta = "SELECT * FROM vista_sanciones_aplicadas WHERE serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_s = mysql_query($consulta);
//////// FIN

$Cantidad=0;
while ($registro_s = mysql_fetch_object($tabla_s))
	{
	switch ($registro_s->aplicacion) 
		{
		////////////////////////////////////
		case 9 :
		$Matriz_1=array('','Primera','Segunda','Tercera','Cuarta','Quinta');
		$Matriz_2=array(' Unico Aparte',' Primer Aparte',' Segundo Aparte',' Tercer Aparte',' Cuarto Aparte',' Quinto Aparte', ' Sexto Aparte', ' Septimo Aparte');
		/////////////////////////////////
		//----- APARTE
		if (substr($registro_s->art_cot,3,1)=='#')
			{
			$Aparte = substr($registro_s->art_cot,0,3);
			//----------
			if (substr($registro_s->art_cot,3,1)=="#") 
				{$Aparte = $Aparte.' Numeral '.substr($registro_s->art_cot,4,2);}
			else
				{$Aparte = $Aparte."";}
			//----------
			if (substr($registro_s->art_cot,7,1)<>"") 
				{$Aparte = $Aparte.$Matriz_2[substr($registro_s->art_cot,7,1)];}
			else
				{$Aparte = $Aparte."";}
			}
		else {$Aparte = $registro_s->art_cot;}
		//----- CON INCREMENTO
		if ($registro_s->reiteracion>1) 
			{$Incremento=', tal como consta en Acta(s) Fiscal(es) levantadas como resultado de la(s) Providencias(s) Administritativa(s) N°: '.substr($registro_s->reiteracion_resolucion,0,strlen($registro_s->reiteracion_resolucion)-1).'.';}
		else
			{$Incremento='.';}
		$Monto_L = new numerosALetras(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial);
		/////////////////////////////////
		$txt='Que '.utf8_decode($registro_s->sancion).utf8_decode(', en contravención a lo establecido en el (los) Artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el Artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, por cuanto se trata de la '.$Matriz_1[$registro_s->reiteracion].' infracción de esta índole cometida por el (la) Contribuyente').$Incremento;
		//
		$pdf->MultiCell(0,5, $txt);
		$pdf->Ln(4); 
		break;
		////////////////////////////////////
		case 14 :
		if ($registro_s->Codigo<836 or $registro_s->Codigo>1550)
		{
			$Matriz_1=array('','Primera','Segunda','Tercera','Cuarta','Quinta');
	$Matriz_2=array(' Unico Aparte',' Primer Aparte',' Segundo Aparte',' Tercer Aparte',' Cuarto Aparte',' Quinto Aparte', ' Sexto Aparte', ' Septimo Aparte');
	/////////////////////////////////
	//----- APARTE
	if (substr($registro_s->art_cot,3,1)=='#')
		{
		$Aparte = substr($registro_s->art_cot,0,3);
		//----------
		if (substr($registro_s->art_cot,3,1)=="#") 
			{$Aparte = $Aparte.' Numeral '.substr($registro_s->art_cot,4,2);}
		else
			{$Aparte = $Aparte."";}
		//----------
		if (substr($registro_s->art_cot,7,1)<>"") 
			{$Aparte = $Aparte.$Matriz_2[substr($registro_s->art_cot,7,1)];}
		else
			{$Aparte = $Aparte."";}
		}
	else {$Aparte = $registro_s->art_cot;}
	//----- CONCURRENCIA
	$Monto_L = new numerosALetras(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial);
	/////////////////////////////////
	$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) Artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el Artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa consistente en una (1) Unidad Tributaria por cada factura, documento o comprobante, la cual asciende a la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias).';
	//equivalente a '.$Monto_L->resultado.'Bol�vares (BsS. '.formato_moneda(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial).'
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(4); 
		}
		else
		{
		$Matriz_1=array('','Primera','Segunda','Tercera','Cuarta','Quinta');
		$Matriz_2=array(' Unico Aparte',' Primer Aparte',' Segundo Aparte',' Tercer Aparte',' Cuarto Aparte',' Quinto Aparte', ' Sexto Aparte', ' Septimo Aparte');
		/////////////////////////////////
		//----- APARTE
		if (substr($registro_s->art_cot,3,1)=='#')
			{
			$Aparte = substr($registro_s->art_cot,0,3);
			//----------
			if (substr($registro_s->art_cot,3,1)=="#") 
				{$Aparte = $Aparte.' Numeral '.substr($registro_s->art_cot,4,2);}
			else
				{$Aparte = $Aparte."";}
			//----------
			if (substr($registro_s->art_cot,7,1)<>"") 
				{$Aparte = $Aparte.$Matriz_2[substr($registro_s->art_cot,7,1)];}
			else
				{$Aparte = $Aparte."";}
			}
		else {$Aparte = $registro_s->art_cot;}
		//----- CON INCREMENTO
		if ($registro_s->reiteracion>1) 
			{$Incremento=', tal como consta en Acta(s) Fiscal(es) levantadas como resultado de la(s) Providencia(s) Administritativa(s) N°: '.substr($registro_s->reiteracion_resolucion,0,strlen($registro_s->reiteracion_resolucion)-1).'.';}
		else
			{$Incremento='.';}
		$Monto_L = new numerosALetras(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial);
		/////////////////////////////////
		$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) Artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el Artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, por cuanto se trata de la '.$Matriz_1[$registro_s->reiteracion].' infracción de esta índole cometida por el (la) Contribuyente'.$Incremento;
		//
		$pdf->MultiCell(0,5, utf8_decode($txt));
		$pdf->Ln(4); 
		}
		break;
		////////////////////////////////////
		case 13 :
		$Matriz_1=array('','Primera','Segunda','Tercera','Cuarta','Quinta');
		$Matriz_2=array(' Unico Aparte',' Primer Aparte',' Segundo Aparte',' Tercer Aparte',' Cuarto Aparte',' Quinto Aparte', ' Sexto Aparte', ' Septimo Aparte');
		/////////////////////////////////
		//----- APARTE
		if (substr($registro_s->art_cot,3,1)=='#')
			{
			$Aparte = substr($registro_s->art_cot,0,3);
			//----------
			if (substr($registro_s->art_cot,3,1)=="#") 
				{$Aparte = $Aparte.' Numeral '.substr($registro_s->art_cot,4,2);}
			else
				{$Aparte = $Aparte."";}
			//----------
			if (substr($registro_s->art_cot,7,1)<>"") 
				{$Aparte = $Aparte.$Matriz_2[substr($registro_s->art_cot,7,1)];}
			else
				{$Aparte = $Aparte."";}
			}
		else {$Aparte = $registro_s->art_cot;}
		$Monto_L = new numerosALetras(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial);
		/////////////////////////////////
		$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) Artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el Artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, calculada en su termino medio, conforme a lo establecido en la mencionada norma, en concordancia a lo previsto en el Artículo 37 del Código Penal, en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';
		//
		$pdf->MultiCell(0,5, utf8_decode($txt));
		$pdf->Ln(4); 
		break;
		////////////////////////////////////		
		case 15 :
		$Matriz_1=array('','Primera','Segunda','Tercera','Cuarta','Quinta');
		$Matriz_2=array(' Unico Aparte',' Primer Aparte',' Segundo Aparte',' Tercer Aparte',' Cuarto Aparte',' Quinto Aparte', ' Sexto Aparte', ' Septimo Aparte');
		/////////////////////////////////
		//----- APARTE
		if (substr($registro_s->art_cot,3,1)=='#')
			{
			$Aparte = substr($registro_s->art_cot,0,3);
			//----------
			if (substr($registro_s->art_cot,3,1)=="#") 
				{$Aparte = $Aparte.' Numeral '.substr($registro_s->art_cot,4,2);}
			else
				{$Aparte = $Aparte."";}
			//----------
			if (substr($registro_s->art_cot,7,1)<>"") 
				{$Aparte = $Aparte.$Matriz_2[substr($registro_s->art_cot,7,1)];}
			else
				{$Aparte = $Aparte."";}
			}
		else {$Aparte = $registro_s->art_cot;}
		$Monto_L = new numerosALetras(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial);
		/////////////////////////////////
		$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) Artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el Artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, calculada en su termino medio, conforme a lo establecido en la mencionada norma, en concordancia a lo previsto en el Artículo 37 del Código Penal, en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';
		//
		$pdf->MultiCell(0,5, utf8_decode($txt));
		$pdf->Ln(4); 
		break;
		////////////////////////////////////		
		case 18 or 12 or 10 or 53 or 100: // AQUI VAN LAS DE RETENCIONES
		$Matriz_1=array('','Primera','Segunda','Tercera','Cuarta','Quinta');
	$Matriz_2=array(' Unico Aparte',' Primer Aparte',' Segundo Aparte',' Tercer Aparte',' Cuarto Aparte',' Quinto Aparte', ' Sexto Aparte', ' Septimo Aparte');
	/////////////////////////////////
	//----- APARTE
	if (substr($registro_s->art_cot,3,1)=='#')
		{
		$Aparte = substr($registro_s->art_cot,0,3);
		//----------
		if (substr($registro_s->art_cot,3,1)=="#") 
			{$Aparte = $Aparte.' Numeral '.substr($registro_s->art_cot,4,2);}
		else
			{$Aparte = $Aparte."";}
		//----------
		if (substr($registro_s->art_cot,7,1)<>"") 
			{$Aparte = $Aparte.$Matriz_2[substr($registro_s->art_cot,7,1)];}
		else
			{$Aparte = $Aparte."";}
		}
	else {$Aparte = $registro_s->art_cot;}
	//----- CON INCREMENTO
	if ($registro_s->reiteracion>1) 
		{$Incremento=', tal como consta en Acta(s) Fiscal(es) levantadas como resultado de la(s) Providencia(s) Administritativa(s) N°: '.substr($registro_s->reiteracion_resolucion,0,strlen($registro_s->reiteracion_resolucion)-1).'.';}
	else
		{$Incremento='.';}
	$Monto_L = new numerosALetras(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial);
	/////////////////////////////////
	if ($registro_s->id_sancion>=20000)
		{
		$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) artículo(s) '.trim($registro_s->art_ley_rgto).' de la (del) '.trim($registro_s->ley).' '.trim($registro_s->art_regla).', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.strtoupper(valorEnLetrasNatural($registro_s->monto_ut)). '('.abs($registro_s->monto_ut).') veces el tipo de cambio oficial de la moneda de mayor valor . ('.formato_moneda2($registro_s->monto_bs / $registro_s->monto_ut).'), publicado por el Banco Central de Venezuela, calculada conforme a lo establecido en la mencionada norma en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';
		//$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) artículo(s) '.trim($registro_s->art_ley_rgto).' de la (del) '.trim($registro_s->ley).' '.trim($registro_s->art_regla).', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.strtoupper(valorEnLetrasNatural($registro_s->monto_ut)). '('.abs($registro_s->monto_ut).') veces el tipo de cambio oficial de la moneda de mayor valor publicado por el Banco Central de Venezuela, calculada conforme a lo establecido en la mencionada norma en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';

		}
	else
		{
		$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, calculada conforme a lo establecido en la mencionada norma en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';
		}
	// equivalente a Bol�vares '.$Monto_L->resultado.' (BsS. '.formato_moneda(($registro_s->monto_bs/$registro_s->concurrencia)*$registro_s->especial).')
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(4); 
	// AQUI == >>>>>>
	// LLENADO DE LA TABLA DE FACTURAS
	
	$Total = 0;
//	$consulta_x = "SELECT Format([Net_Fis_Reten_Detalladas].[PeriodoiNICIO],'dd/mm/yyyy') AS Desde, Format([Net_Fis_Reten_Detalladas].[PeriodoFIN],'dd/mm/yyyy') AS Hasta, Net_Fis_Reten_Detalladas.Multa AS Total, Net_Fis_Reten_Detalladas.Factura, Format([Net_Fis_Reten_Detalladas].[FechaPago],'dd/mm/yyyy') AS FechaPago, [Net_Fis_Reten_Detalladas].[Multa] AS Monto, Net_Fis_Reten_Detalladas.añoProvidencia, Net_Fis_Reten_Detalladas.NroProvidencia, Format([Net_Fis_Reten_Detalladas].[FechaVen],'dd/mm/yyyy') AS Vencimiento FROM Net_Fis_Reten_Detalladas WHERE ([Net_Fis_Reten_Detalladas].[PeriodoiNICIO]=#".$registro_s->FechaInicioDeclaracion10."# and [Net_Fis_Reten_Detalladas].[PeriodoFIN]=#".$registro_s->FechaFinDeclaracion10."#) and (((Net_Fis_Reten_Detalladas.añoProvidencia)=".$_SESSION['ANNO_PRO'].") AND ((Net_Fis_Reten_Detalladas.NroProvidencia)=".$_SESSION['NUM_PRO']."));";
//	$tabla_x = mysql_query($consulta_x);
//	$i=0;
//	while ($registro_x = mysql_fetch_object($tabla_x))
//	{
//	if ($i==0)
//		{
//		$pdf->SetFont('Times','',8);
//		$pdf->SetFillColor(192,192,192);
//		$pdf->Cell(10,4,'',0,0,'C');
//		$txt = 'Periodo';
//		$pdf->Cell(40,4,$txt,1,0,'C','True');
//		$txt = 'N� de Factura';
//		$pdf->Cell(30,4,$txt,1,0,'C','True');
//		$txt ='Fecha de Venc.';
//		$pdf->Cell(30,4,$txt,1,0,'C','True');
//		$txt ='Fecha de Pago';
//		$pdf->Cell(30,4,$txt,1,0,'C','True');
//		$txt = 'Multa';
//		$pdf->Cell(30,4,$txt,1,0,'C','True');
//		$pdf->Ln(4);
//		}
//	$pdf->Cell(10,4,'',0,0,'C');
//	$txt = $registro_x->Desde.'-'.$registro_x->Hasta;
//	$pdf->Cell(40,4,$txt,1,0,'C');
//	$txt = $registro_x->Factura;
//	$pdf->Cell(30,4,$txt,1,0,'C');
//	$txt = $registro_x->Vencimiento;
//	$pdf->Cell(30,4,$txt,1,0,'C');
//	$txt = $registro_x->FechaPago;
//	$pdf->Cell(30,4,$txt,1,0,'C');
//	$txt = $registro_x->Monto;
//	$pdf->Cell(30,4,number_format(doubleval($txt),2,',','.'),1,0,'C');
//	$pdf->Ln(4);
//	$Total = $Total + $registro_x->Total ;
//	$Fecha_Pago = $registro_x->FechaPago;
//	$i++;
//	}
//	
	if ($i>0)
		{
		$pdf->Cell(10,4,'',0,0,'C');
		$txt = 'Total =>';
		$pdf->Cell(130,4,$txt,1,0,'C','True');
		$txt = number_format(doubleval($Total),2,',','.');
		$pdf->Cell(30,4,$txt,1,0,'C');
		$pdf->Ln(10);
		}
	// ---------------------- FIN DEL LLENADO DE LA TABLA DE PAGOS
	// FIN
		break;
		////////////////////////////////////		
		}
	$Cantidad++;
	}

// --------------------------------------------- FIN DE LAS SANCIONES

//////// VALIDACION DE LAS SANCIONES CON CONCURRENCIA
$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE concurrencia=2 and serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_xxx = mysql_query($consulta); 
//////// FIN
if ($registro_xxx = mysql_fetch_object($tabla_xxx))
	{
	////////// IMPRESION DEL TEXTO
	$txt='Por cuanto en el presente caso existe concurrencia de ilícitos tributarios sancionados con penas pecuniarias, se aplica la sanción más grave, aumentada con la mitad de las otras sanciones, conforme a lo establecido en el Artículo 82 del Código Orgánico Tributario vigente, en virtud de lo cual esta Administración Tributaria procedió a determinar la(s) multa(s) resultante(s), aplicando el concurso de acuerdo a la sumatoria de la totalidad de las sanciones aplicables por cada tipo de ilícito, a efectos de establecer el ilícito cuya sumatoria arroje la sanción más cuantiosa, tal como se demuestra a continuación:';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(2);
	
	////////// DATOS DE LAS SANCIONES RESUMEN POR SANCION
	$consulta = "SELECT id_liquidacion, art_cot, aplicacion, id_sancion, sum(monto_ut*especial) SumaDeUTCifras, sum(monto_ut/concurrencia*especial) as SumaDeUTdividida FROM vista_sanciones_aplicadas WHERE serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." GROUP BY art_cot, aplicacion, id_sancion;";
	$tabla_r = mysql_query($consulta);
	////////// FIN
	
	$Num=0;
	while ($registro_r = mysql_fetch_object($tabla_r))
		{
		$Num++;
		////////// IMPRESION DEL CUADRO CON EL TITULO
		$pdf->Ln(3);
			////////// TITULOS
			$pdf->SetFont('Times','',9);
			$pdf->SetFillColor(192,192,192);
			$pdf->Cell(10,5,'',0,0,'C');
			$txt='Art. COT.';
			$pdf->Cell(108,5,utf8_decode($txt),1,0,'C','true');
			if ($registro_r->id_sancion>60000)
				{	$txt='SubTotal TCOMMV';	}
			else
				{	$txt='SubTotal U.T.';	}
			$pdf->Cell(2,5,'',0,0,'C');
			$pdf->Cell(54,5,utf8_decode($txt),1,0,'C','true');
			$pdf->Ln(5);	
			//////////  CONTENIDO
			$pdf->SetFont('Times','',9);
			$pdf->SetFillColor(0,0,0);
			$pdf->Cell(10,5,'',0,0,'C');
			
			//----- APARTE
			$Matriz_2=array(' Unico Aparte',' Primer Aparte',' Segundo Aparte',' Tercer Aparte',' Cuarto Aparte',' Quinto Aparte', ' Sexto Aparte', ' Septimo Aparte');
			$Matriz_3=array(' Unico',' Primero',' Segundo',' Tercero',' Cuarto',' Quinto', ' Sexto', ' Septimo');
			if (substr($registro_r->art_cot,3,1)=='#')
				{
				$Aparte = substr($registro_r->art_cot,0,3);
				//----------
				if (substr($registro_r->art_cot,3,1)=="#") 
					{$Aparte = $Aparte.' Numeral '.substr($registro_r->art_cot,4,2);}
				else
					{$Aparte = $Aparte."";}
				//----------
				if (substr($registro_r->art_cot,7,1)<>"") 
					{$Aparte = $Aparte.$Matriz_2[substr($registro_r->art_cot,7,1)];}
				else
					{$Aparte = $Aparte."";}
				}
			else
				{
				if (substr($registro_r->art_cot,3,1)=='P')
					{
					$Aparte = abs(substr($registro_r->art_cot,0,3));
					//----------
					if (substr($registro_r->art_cot,3,1)=="P") 
						{$Aparte = $Aparte.' Parágrafo'.$Matriz_3[substr($registro_r->art_cot,5,1)];}
					else
						{$Aparte = $Aparte."";}
					//----------
					if (substr($registro_r->art_cot,7,1)<>"") 
						{$Aparte = $Aparte.$Matriz_2[substr($registro_r->art_cot,7,1)];}
					else
						{$Aparte = $Aparte."";}
					}
				else
					{$Aparte = $registro_r->art_cot;}
				}

			$pdf->Cell(108,5,$Aparte,1,0,'L');
			$pdf->Cell(2,5,'',0,0,'C');
			if ($Num<>1) 
				{$txt=$registro_r->SumaDeUTdividida;} 
			else 
				{$txt=$registro_r->SumaDeUTCifras;}
			$pdf->Cell(54,5,abs($txt),1,0,'C');
			$pdf->Ln(5);	
		
		////////// DETALLE DE LAS SANCIONES		
		$consulta = "SELECT * FROM vista_sanciones_aplicadas WHERE id_sancion=".$registro_r->id_sancion." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." ORDER BY monto_ut DESC, periodoinicio, periodofinal;";
		$tabla_l = mysql_query($consulta);
					
		////////// TITULOS
		$pdf->SetFont('Times','',8.5);
		$pdf->SetFillColor(192,192,192);
		
		$pdf->Cell(10,5,'',0,0,'C');
		$txt='Descripción del Hecho Punible';
		$pdf->Cell(78,5,utf8_decode($txt),1,0,'C','true');
		$txt='periodo';
		$pdf->Cell(2,5,'',0,0,'C');
		$pdf->Cell(33,5,$txt,1,0,'C','true');
		if ($registro_r->id_sancion>60000)
			{	$txt='Monto TCOMMV';		}
		else
			{	$txt='Monto U.T.';			}	
		$pdf->Cell(2,5,'',0,0,'C');
		$pdf->Cell(23,5,$txt,1,0,'C','true');
		if ($registro_r->id_sancion>60000)
			{	$txt='Concur TCOMMV';		}
		else
			{	$txt='Concur. U.T.';		}
		$pdf->Cell(2,5,'',0,0,'C');
		$pdf->Cell(24,5,$txt,1,0,'C','true');
		$pdf->Ln(5);
			
		while ($registro_l = mysql_fetch_object($tabla_l))
		{
			$pdf->SetFont('Times','',6);
			$pdf->SetFillColor(0,0,0);

			$pdf->Cell(10,8,'',0,0,'C');
			$txt=$registro_l->Codigo.'- '.substr($registro_l->sancion,0,55).'_';
			$pdf->Text($pdf->GetX()+2, $pdf->GetY()+3, utf8_decode($txt));
			$txt='  '.substr($registro_l->sancion,55,55);
			$pdf->Text($pdf->GetX()+2, $pdf->GetY()+6, $txt);
			$pdf->Cell(78,8,'',1,0,'L');
			
			$pdf->SetFont('Times','',8);
			
			$txt=voltea_fecha($registro_l->periodoinicio).' - '.voltea_fecha($registro_l->periodofinal);
			$pdf->Cell(2,8,'',0,0,'C');
			$pdf->Cell(33,8,$txt,1,0,'C');
				
			$txt = $registro_l->monto_ut * $registro_l->especial;
			$pdf->Cell(2,8,'',0,0,'C');
			$pdf->Cell(23,8,abs($txt),1,0,'C');
			
			$txt = $registro_l->monto_ut / $registro_l->concurrencia * $registro_l->especial;

			$pdf->Cell(2,8,'',0,0,'C');
			$pdf->Cell(24,8,abs($txt),1,0,'C');

			$pdf->Ln(6);
		}
		////////// 
	}
	}
// --------------------------------------------- SI TIENE SANCIONES VDF

////---------------------------------------------------------------------------------------------------------------------------------------------------
////------- POR SI TIENE INTERES Y NO LLEVA ACTA DE REPARO
$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE serie=38 and origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_i = mysql_query($consulta);
	
if ($registro_i = mysql_fetch_object($tabla_i) and $monto_pagado <=0)
	{
	$pdf->Ln(2);
	$pdf->SetFont('Times','B',11);
	$pdf->Cell(0,5,'INTERESES MORATORIOS',0,0,'C'); 
	$pdf->Ln(10);
	
	$pdf->SetFont('Times','',11);
	
	$txt='Procede '.strtolower($Sede).' Gerencia Regional de Tributos Internos, al cálculo de Intereses Moratorios de conformidad con el Artículo 66 del Código Orgánico tributario, de acuerdo a la tasa activa promedio publicada por el banco Central de venezuela, incrementada en los términos establecidos en dicho Artículo, cálculo éste que se efectua a partir del día siguiente al vencimiento del plazo establecido para la autoliquidación y pago del tributo, es decir desde el vencimiento de cada periodo hasta el momento de efectuado el pago, según consta en la(s) planilla(s) de pago, correspondiente a los periodos tributarios que originaron el impuesto a pagar. Así se declara.';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(5); 
	
	$txt='Se calculan utilizando la siguiente fórmula:
	Intereses Moratorios = C x R x T / t (año) x 100';
	$pdf->MultiCell(0,5, utf8_decode($txt)); 
	$pdf->Ln(5); 
	
	$txt='Donde:
	C = Capital
	R = Tasa de interés
	T = Tiempo de mora
	 t = 360 x 100';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(4); 
	
	$txt='A continuación se muestra el cuadro resumen de los intereses moratorios, los cuales se detallan en la aplicacion de la citada fórmula.';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(4); 
	
	// TITULOS DE LA PRIMERA TABLA DE INTERES
	
	$pdf->SetFont('Times','',8);
	$pdf->SetFillColor(192,192,192);
	
	$txt='Tributo';
	$pdf->Cell(18,6,$txt,1,0,'C','true');
	
	$txt='Periodo';
	$pdf->Cell(30,6,$txt,1,0,'C','true');
	
	$txt='Impuesto Determinado (Bs.)';
	$pdf->Cell(35,6,$txt,1,0,'C','true');
	
	$txt='Fecha Vencimiento';
	$pdf->Cell(25,6,$txt,1,0,'C','true');
	
	$txt='Fecha Pago';
	$pdf->Cell(15,6,$txt,1,0,'C','true');
	
	$txt='Dias de Mora';
	$pdf->Cell(18,6,$txt,1,0,'C','true');
	
	$txt='Intereses Moratorios (Bs.)';
	$pdf->Cell(33,6,$txt,1,0,'C','true');
	
	$pdf->Ln(4);
	
	// LLENADO DE LA PRIMERA TABLA DE INTERES
	
	$Total = 0;
	$Interes = 0;
	$Dias_Total = 0;
	
	$consulta = "SELECT * FROM vista_sanciones_aplicadas WHERE serie=38 and origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." ORDER BY id_tributo;";
	$tabla_x = mysql_query($consulta);
	
	while ($registro_x = mysql_fetch_object($tabla_x))
	{
		$txt = $registro_x->siglas;
		$pdf->Cell(18,4,$txt,1,0,'C');

		$txt = voltea_fecha($registro_x->periodoinicio).'-'.voltea_fecha($registro_x->periodofinal);
		$pdf->Cell(30,4,$txt,1,0,'C');
		
		$txt =number_format(doubleval($registro_x->monto_pagado),2,',','.');
		$pdf->Cell(35,4,$txt,1,0,'C');
		
		$txt = voltea_fecha($registro_x->fecha_vencimiento);
		$pdf->Cell(25,4,$txt,1,0,'C');
		
		$txt = voltea_fecha($registro_x->fecha_pago);
		$pdf->Cell(15,4,$txt,1,0,'C');
		
			list($anno,$mes,$dia)=explode('-',$registro_x->fecha_pago);
			$FECHA_PAGO = mktime(0,0,0,$mes,$dia,$anno);
			
			list($anno,$mes,$dia)=explode('-',$registro_x->fecha_vencimiento);
			$FECHA_VENCIMIENTO = mktime(0,0,0,$mes,$dia,$anno);
			
			$Dias = $FECHA_PAGO - $FECHA_VENCIMIENTO;
		
		$txt = $Dias/86400;
		$pdf->Cell(18,4,redondea($txt),1,0,'C');
		
		$txt = number_format(doubleval($registro_x->monto_bs),2,',','.');
		$pdf->Cell(33,4,$txt,1,0,'C');
			
		$pdf->Ln(4);
		
		$Total = $Total + $registro_x->monto_pagado ;
		$Interes = $Interes + $registro_x->monto_bs ;
		$Dias_Total = $Dias_Total + $Dias;
	} //////////////////
	
	$txt = 'Totales =>';
	$pdf->Cell(48,4,$txt,1,0,'C','True');
	
	$txt = number_format(doubleval($Total),2,',','.');
	$pdf->Cell(35,4,$txt,1,0,'C');
	
	$txt = '';
	$pdf->Cell(40,4,$txt,1,0,'C','true');
	
	$txt = $Dias_Total/86400;
	$pdf->Cell(18,4,redondea($txt),1,0,'C');
	
	$txt = number_format(doubleval($Interes),2,',','.');
	$pdf->Cell(33,4,$txt,1,0,'C');
	
	$pdf->Ln(4);
		
	$txt='Aplicación de la fórmula: Intereses Moratorios = C x R x T / (año) 360 x 100';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	
	$pdf->Ln(4);
	}
////---------------------------------------------------------------------------------------------------------------------------------------------------

}
// --------------------------------------------- FIN SI TIENE SANCIONES VDF
$pdf->SetFont('Times','',11-$tamaño);
//$pdf->SetFont('Times','',10);
//$pdf->Ln(5);

if ($monto_pagado<=0)
	{
	$pdf->Ln(4);
	//$txt='Por lo antes expuesto, expídase a cargo del (de la) Contribuyente o Responsable antes identificado(a) planilla(s) de pago por concepto de multa(s) por el (los) monto(s) indicado(s), la(s) cual(es) deberá cancelar en una Oficina Receptora de Fondos Nacionales de forma inmediata; asimismo, se le notifica que el monto de la sanción se encuentra sujeta a modificación en caso de cambio del valor de la Unidad Tributaria entre la presente fecha y la fecha efectiva de pago, conforme a lo previsto en el Artículo 91, 92 y 94 del Código Orgánico Tributario vigente.';
	  $txt='Por lo antes expuesto, expídase a cargo del (de la) Contribuyente o Responsable antes identificado(a) planilla(s) de pago por concepto de multa(s) por el (los) monto(s) indicado(s), la(s) cual(es) deberá cancelar en una Oficina Receptora de Fondos Nacionales de forma inmediata; asimismo, se le notifica que el monto de la sanción se encuentra sujeta a modificación en caso de cambio de la moneda de mayor valor públicado por el Banco Central de Venezuela entre la presente fecha y la fecha efectiva de pago, conforme a lo previsto en el Artículo 91, 92 y 94 del Código Orgánico Tributario vigente.';

	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(2); 
	
	if ($pdf->GetY()>160) {$pdf->AddPage();}
	//$txt='En caso de inconformidad con el presente Acto Administrativo el (la) Contribuyente o Responsable podrá ejercer los recursos que consagra el Código Orgánico Tributario en sus Artículos 252 y 266, dentro de los plazos previstos en los Artículos 257 y 268 Ejusdem, para lo cual deberá darse cumplimiento a lo previsto en los Artículos 253 y 259 del citado Código.';
	$txt='En caso de inconformidad con el presente Acto Administrativo el (la) Contribuyente o Responsable podrá ejercer los recursos que consagra el Código Orgánico Tributario en sus Artículos 272 y 286, dentro de los plazos previstos en los Artículos 277 y 288 Ejusdem, para lo cual deberá darse cumplimiento a lo previsto en los Artículos 273 y 279 del citado Código.';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	}
else
	{
	$pdf->Ln(2);
	//$txt='Se hace del conocimiento del sujeto pasivo, que el Código Orgánico tributario vigente en su Artículo 252 señala que podrá, en caso de no estar de acuerdo con el presente Acto Administrativo, ejercer el recurso Jerárquico, para lo cual deberá llenar los extremos señalados en el Artículo 253 ejusdem. Dispone además esta norma, que deberá contar con la presencia de un abogado o de cualquier otro profesional afín al área tributaria. Su omisión constituye causal de inadmisibilidad, a tenor de lo dispuesto en el Artículo 259 numeral 4 ejusdem. El lapso para su interposición será de veinticinco (25) días hábiles, contados a partir de la notificación de la presente Resolución. Igualmente podrá interponer el recurso Contencioso Tributario contenido en el Artículo 266 ejusdem, dentro del plazo de veinticinco (25) días hábiles contados a partir de la notificación del Acto que se impugna o del vencimiento del lapso previsto para decidir el recurso jerarquico, en caso de denegacion tácita de este, todo a tenor de lo dispuesto en el Artículo 261 ejusdem.';
	$txt='Se hace del conocimiento del sujeto pasivo, que el Código Orgánico tributario vigente en su Artículo 272 señala que podrá, en caso de no estar de acuerdo con el presente Acto Administrativo, ejercer el recurso Jerárquico, para lo cual deberá llenar los extremos señalados en el Artículo 273 ejusdem. Dispone además esta norma, que deberá contar con la presencia de un abogado o de cualquier otro profesional afín al área tributaria. Su omisión constituye causal de inadmisibilidad, a tenor de lo dispuesto en el Artículo 279 numeral 4 ejusdem. El lapso para su interposición será de veinticinco (25) días hábiles, contados a partir de la notificación de la presente Resolución. Igualmente podrá interponer el recurso Contencioso Tributario contenido en el Artículo 286 ejusdem, dentro del plazo de veinticinco (25) días hábiles contados a partir de la notificación del Acto que se impugna o del vencimiento del lapso previsto para decidir el recurso jerarquico, en caso de denegacion tácita de este, todo a tenor de lo dispuesto en el Artículo 281 ejusdem.';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(2);
	
	//$txt='Definitivamente firme el presente Acto Administrativo, y no habiendo el sujeto pasivo dado cumplimiento al contenido del mismo; '.$Sede.' Gerencia Regional de Tributos Internos, procederá al juicio Ejecutivo previsto en el Artículo 290 y siguientes del Código Orgánico Tributario vigente.';
	$txt='Definitivamente firme el presente Acto Administrativo, y no habiendo el sujeto pasivo dado cumplimiento al contenido del mismo; '.$Sede.' Gerencia Regional de Tributos Internos, procederá al juicio Ejecutivo previsto en el Artículo 226 y siguientes del Código Orgánico Tributario vigente.';
	$pdf->MultiCell(0,5, utf8_decode($txt));
	$pdf->Ln(2); 
	
	if ($pdf->GetY()>150) {$pdf->AddPage();}
	$txt='Y para que así conste a los fines legales consiguientes se levanta la presente resolución por triplicado de un mismo tenor y a un solo efecto, uno (1) de cuyos ejemplares queda en poder del Sujeto pasivo, que firma en señal de notificación.';
	$pdf->MultiCell(0,5, utf8_decode($txt));

	}
	
//$pdf->Ln(10); 
$pdf->Ln(5);

$pdf->SetFont('Times','B',11);
	
$txt=utf8_decode('Comuníquese,');
$pdf->Cell(0,5,$txt,0,0,'C');

// FIRMA DEL JEFE
//$pdf->Ln(10); original
/*$pdf->Ln(5);
$accion_resolucion = 1;
include "firma.php";*/

//-----------

//alex
if($programa == 'Verificacion' and  $nombre == 'Calabozo' or $nombre == 'San Juan de los Morros'  or $nombre == 'San Fernando de Apure' or $nombre == 'Altagracia de Orituco' or $nombre == 'Valle de la Pascua'){
				$pdf->Ln(5);
             $accion_resolucion = 1;
			include "firma.php";
			}
			else {
				$pdf->Ln(-10);
               $accion_resolucion = 1;
			include "firma_gerente.php";
			} 
//fin alex
$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
$pdf->Ln(2);
$pdf->Ln(3);
// FIN

$pdf->SetFont('Times','B',9);
$pdf->Cell(110,5,utf8_decode('Notificación'));  										
$pdf->Cell(0,5,'Funcionario Notificador'); 
$pdf->Ln(7);
$pdf->SetFont('Times','',8);
$pdf->Cell(110,5,'Firma:       __________________________________');	
$pdf->Cell(0,5,'Nombre:   __________________________________');
$pdf->Ln(5);
$pdf->Cell(110,5,'Nombre:   __________________________________');				
$pdf->Cell(0,5,utf8_decode('C.I. N°:     __________________________________'));
$pdf->Ln(5);
$pdf->Cell(110,5,utf8_decode('C.I. N°:     __________________________________')); 		
$pdf->Cell(0,5,'Cargo:       __________________________________');
$pdf->Ln(5);
$pdf->Cell(110,5,'Cargo:       __________________________________'); 	
$pdf->Cell(0,5,'Firma:       __________________________________');
$pdf->Ln(5);
$pdf->Cell(110,5,'Fecha:       __________________________________');
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,5,utf8_decode('Funcionario Supervisor')); 
$pdf->Ln(5);
$pdf->SetFont('Times','',8);
$pdf->Cell(12,5,'Telefono:');	
$pdf->Cell(98,5,'__________________________________');	
$pdf->Cell(0,5,'Nombre:   __________________________________');
$pdf->Ln(5);
$pdf->Cell(110,5,'');									
$pdf->Cell(0,5,utf8_decode('C.I. N°:     __________________________________'));
$pdf->Ln(5);
$pdf->Cell(110,5,''); 													
$pdf->Cell(0,5,'Cargo:       __________________________________');
$pdf->Ln(5);
$pdf->Cell(110,5,$A); 													
$pdf->Cell(0,5,'Firma:       __________________________________');


$pdf->Ln(6);
$pdf->Cell(110,5,''); 
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,5,utf8_decode('Fiscal Actuante')); 
$pdf->Ln(6);
$pdf->SetFont('Times','',8);
$pdf->Cell(110,5,''); 													
$pdf->Cell(0,5,'Nombre:   __________________________________');
$pdf->Ln(5);
$pdf->Cell(110,5,'');									
$pdf->Cell(0,5,utf8_decode('C.I. N°:     __________________________________'));
$pdf->Ln(5);
$pdf->Cell(110,5,''); 													
$pdf->Cell(0,5,'Cargo:       __________________________________');
$pdf->Ln(5);
$pdf->Cell(110,5,$A); 													
$pdf->Cell(0,5,'Firma:       __________________________________');


// FIN DE LA RESOLUCION

$pdf->Output();
?>