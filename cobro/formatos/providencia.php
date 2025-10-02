<?php
ob_end_clean();
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
setlocale(LC_TIME, 'sp_ES','sp', 'es');
mysql_query("SET NAMES 'latin1'");

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
	
require('../../funciones/fpdf.php');

class PDF extends FPDF
{
	function Footer()
		{    
		//Posición a 1,5 cm del final
		$this->SetY(-15);
		//Arial itálica 8
		$this->SetFont('Arial','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(0,0,sistema(),0,0,'R');
		}	

	function cabeceraHorizontal($cabecera, $x, $y)
    {
        $this->SetXY($x+1, $y);
        $this->SetFont('Arial','B',8);
        $this->SetFillColor(160,160,160);//Fondo verde de celda
        $this->SetTextColor(0, 0, 0); //Letra color blanco
		/*
        foreach($cabecera as $fila)
        {
 
            $this->CellFitSpace(30,7, utf8_decode($fila),1, 0 , 'L', true);
 
        }
		*/
            $this->CellFitSpace(30,5, utf8_decode($cabecera[0]),1, 0 , 'L', true);
            $this->CellFitSpace(25,5, utf8_decode($cabecera[1]),1, 0 , 'L', true);
            $this->CellFitSpace(120,5, utf8_decode($cabecera[2]),1, 0 , 'L', true);
    }
 
    function datosHorizontal($datos, $x, $y)
    {
        $this->SetXY($x+1,$y+5);
        $this->SetFont('Arial','',7);
        $this->SetFillColor(229, 229, 229); //Gris tenue de cada fila
        $this->SetTextColor(3, 3, 3); //Color del texto: Negro
        $bandera = false; //Para alternar el relleno
        foreach($datos as $fila)
        {
            //Usaremos CellFitSpace en lugar de Cell
            $this->CellFitSpace(30,5, utf8_decode($fila['BANCO']),1, 0 , 'L', $bandera );
            $this->CellFitSpace(25,5, utf8_decode($fila['CIUDAD']),1, 0 , 'L', $bandera );
            $this->CellFitSpace(120,5, utf8_decode($fila['DIRECCION']),1, 0 , 'L', $bandera );
            $this->Ln();//Salto de línea para generar otra fila
            $bandera = !$bandera;//Alterna el valor de la bandera
        }
    }
 
    function tablaHorizontal($cabeceraHorizontal, $datosHorizontal, $x, $y)
    {
        $this->cabeceraHorizontal($cabeceraHorizontal, $x, $y);
        $this->datosHorizontal($datosHorizontal, $x, $y);
    }
 
    //***** Aquí comienza código para ajustar texto *************
    //***********************************************************
    function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);
 
        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;
 
        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }
 
        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
 
        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }
 
    function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }
 
    //Patch to also work with CJK double-byte text
    function MBGetStringLength($s)
    {
        if($this->CurrentFont['type']=='Type0')
        {
            $len = 0;
            $nbbytes = strlen($s);
            for ($i = 0; $i < $nbbytes; $i++)
            {
                if (ord($s[$i])<128)
                    $len++;
                else
                {
                    $len++;
                    $i++;
                }
            }
            return $len;
        }
        else
            return strlen($s);
    }
//************** Fin del código para ajustar texto *****************
//******************************************************************

} // FIN Class PDF

// ENCABEZADO
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();

//$pdf->SetAutoPageBreak(1,10);

$NUMERO_ORIGINAL=$_SESSION['NUMERO'];

$sector = $_GET['sector'];
$anno = $_GET['anno'];
$num1 = $_GET['num1'];
$num2 = $_GET['num2'];

////////// DATOS DE LA PROVIDENCIA
$consulta = "SELECT * FROM vista_ce_providencias WHERE sector=".$sector." AND anno=".$anno." AND numero>=".$num1." AND numero<=".$num2.";";
$tabla = mysql_query ($consulta);
	while ($registro = mysql_fetch_object($tabla))
	{
	$pdf->SetMargins(20,25,20);
	//////////
	// -------------- CONTRIBUYENTE
	$contribuyente = $registro->contribuyente;
	$rif = $registro->rif;
	$rif = strtoupper(substr($rif,0,1)).'-'.substr($rif,1,8).'-'.substr($rif,9,1);
	$direccion = strtoupper(trim($registro->direccion));
		if (strtoupper($registro->rif)=='J000000000') 
		{ 
		$contribuyente = '_______________________________';
		$rif = '______________';
		$direccion = '____________________________________________________________';
		}
	// --------------
	
	// PAGINA DE LA PROVIDENCIA
	$pdf->AddPage();
	$pdf->SetFont('Arial','',9);
	setlocale(LC_TIME, 'sp_ES','sp', 'es');
	
	////////// SIGLAS DE LA RESOLUCION
	$consulta_x = "SELECT Siglas_resol_especiales FROM z_siglas WHERE id_sector=".$sector.";";
	$tabla_x = mysql_query ( $consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$SIGLAS=$registro_x->Siglas_resol_especiales;
	// ---------------------
	////////// DATOS DE LA RESOLUCION
	$RESOLUCION = $SIGLAS.$registro->anno."/".sprintf("%004s", $registro->numero);
	////////// FIN
	
	//////////
	$pdf->Image('../../imagenes/logo.jpeg',20,5,65);
	$pdf->SetFont('Arial','',9);
	//////////
	
	////////// FECHA DE LA RESOLUCION
	list($dia,$mes,$anno)=explode('/',$registro->fecha_registro);
	$FECHA=mktime(0,0,0,$mes,$dia,$anno);
	////////// FIN

	// ---------------------
	$Ciudad = $registro->nombre;	//"Calabozo"; 
	$t=(140-(strlen($Ciudad)));
	
	$pdf->Text(20,32,'N° '.$RESOLUCION);
	$pdf->Text($t,32,$Ciudad.', '.date('d',strtotime($registro->fecha_registro)).' de '.$_SESSION['meses_anno'][abs(date('m',strtotime($registro->fecha_registro)))].' de '.date('Y',strtotime($registro->fecha_registro)));
	$pdf->Ln(12);
		
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(0,6,'PROVIDENCIA ADMINISTRATIVA',0,0,'C'); $pdf->Ln(8);
	//$pdf->Cell(0,1,'Calificación como Sujeto Pasivo Especial',0,0,'C'); $pdf->Ln(10);
	$pdf->SetFont('Arial','',9);
	
	$pdf->SetFont('Arial','',9);

	$pdf->Cell(0,5,'SUJETO PASIVO:');

	$pdf->SetFont('Arial','B',9);
	$pdf->SetX(55);

	$pdf->MultiCell(0,5,strtoupper($registro->contribuyente));
	$pdf->Ln(2); 
	
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(0,5,'RIF N°:'); 
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetX(55);
	$pdf->Cell(0,5,formato_rif($registro->rif));
	$pdf->Ln(7); 
	
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(0,5,'DOMICILIO FISCAL:'); 
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetX(55);
	$pdf->MultiCell(0,5,$registro->direccion);
	
	// FIN
	
	$pdf->SetFont('Arial','',9);
	$pdf->Ln(2);

	$txt0 = "";
	$suscriptor = "Gerente Regional de Tributos Internos de la Región Los Llanos";
	$tramitacion = "de la Gerencia Regional de Tributos Internos de la Región Los Llanos";
	
	//switch (1) {
//			case 1:
//				$suscriptor = "Gerente Regional de Tributos Internos de la Región Los Llanos";
//				$tramitacion = "de la Gerencia Regional de Tributos Internos de la Región Los Llanos";
//				$txt18 = ("4. Los trámites relacionados con los tributos administrados por este Servicio, tales como: consultas, comunicaciones, solicitudes, recursos, entre otros, deberán ser consignados en el Área de Correspondencia ubicada en  la Sede SENIAT Sector Pinto Salinas, Calle la Pedrera Edf. Don Pepino, P.B., Calabozo, Estado Guárico. Sólo se exceptúan los trámites en materia de Aduana, los cuales deberán ser efectuados ante la Gerencia de Aduana correspondiente.");
//				break;
//			case 2:
//				$suscriptor = "Jefe del Sector de Tributos Internos San Juan de Los Morros";
//				$tramitacion = "del Sector de Tributos Internos San Juan de Los Morros";
//				$txt18 = ("4. Los trámites relacionados con los tributos administrados por este Servicio, tales como: consultas, comunicaciones, solicitudes, recursos, entre otros, deberán ser consignados en el Área de Correspondencia ubicada en  la Sede SENIAT Sector Pinto Salinas, Calle la Pedrera Edf. Don Pepino, P.B., Calabozo, Estado Guárico. Sólo se exceptúan los trámites en materia de Aduana, los cuales deberán ser efectuados ante la Gerencia de Aduana correspondiente.");
//				break;
//			case 3:
//				$suscriptor = "Jefe del Sector de Tributos Internos San Fernando de Apure";
//				$tramitacion = "del Sector de Tributos Internos San Fernando de Apure";
//				$txt18 = ("4. Los trámites relacionados con los tributos administrados por este Servicio, tales como: consultas, comunicaciones, solicitudes, recursos, entre otros, deberán ser consignados en el Área de Correspondencia ubicada en  el Sector San Fernando de Apure SENIAT, ubicado AV paseo Libertador Edificio Don Antonio piso 01, San Fernando, Estado Apure. Sólo se exceptúan los trámites en materia de Aduana, los cuales deberán ser efectuados ante la Gerencia de Aduana correspondiente.");
//				break;
//			case 4:
//				$suscriptor = "Jefe de la Unidad de Tributos Internos Altagracia de Orituco";
//				$tramitacion = "de la Unidad de Tributos Internos Altagracia de Orituco";
//				$txt18 = ("4. Los trámites relacionados con los tributos administrados por este Servicio, tales como: consultas, comunicaciones, solicitudes, recursos, entre otros, deberán ser consignados en el Área de Correspondencia ubicada en  la Sede SENIAT Sector Pinto Salinas, Calle la Pedrera Edf. Don Pepino, P.B., Calabozo, Estado Guárico. Sólo se exceptúan los trámites en materia de Aduana, los cuales deberán ser efectuados ante la Gerencia de Aduana correspondiente.");
//				break;
//			case 5:
//				$suscriptor = "Jefe del Sector de Tributos Internos Valle de la pascua";
//				$tramitacion = "del Sector de Tributos Internos Valle de la pascua";
//				$txt18 = ("4. Los trámites relacionados con los tributos administrados por este Servicio, tales como: consultas, comunicaciones, solicitudes, recursos, entre otros, deberán ser consignados en el Área de Correspondencia ubicada en  la Sede SENIAT, Calle Real Nº 12, Edif. Seniat, a 50 metros de la Plaza Bolívar Sector Centro, Valle de la Pascua, Estado Guárico. Sólo se exceptúan los trámites en materia de Aduana, los cuales deberán ser efectuados ante la Gerencia de Aduana correspondiente.");
//				break;
//		}
	
	//DETERMINAR SI ES NATURAL O JURIDICA
	$digito = substr($registro->rif, 0, 1);
	if ($digito=="V" or $digito=="E")
	{
		$texto_literal = "literal a";
		$literal_a = "a) Las personas naturales que hubieren obtenido ingresos brutos iguales o superiores al equivalente de siete mil quinientas unidades tributarias (7.500 U.T.) conforme a lo señalado en su última declaración jurada anual presentada, para el caso de tributos que se liquiden por períodos anuales, o que hubieren efectuado ventas o prestaciones de servicios por montos superiores al equivalente de seiscientas veinticinco unidades tributarias (625 U.T.) mensuales, conforme a lo señalado en cualquiera de las seis (6) últimas declaraciones, para el caso de tributos que se liquiden por períodos mensuales. Igualmente, podrán ser calificados como especiales las personas naturales que laboren exclusivamente bajo relación de dependencia y hayan obtenido enriquecimientos netos iguales o superiores a siete mil quinientas unidades tributarias (7.500 U.T.), conforme a lo señalado en su última declaración del impuesto sobre la renta presentada.";
		$literal_b = "b) (Omissis)";
		$literal_c = "c) (Omissis)";
	}
	else
	{
		$texto_literal = "literal b";
		$literal_a = "a) (Omissis)";
		$literal_b = "b) Las personas jurídicas, con exclusión de las señaladas en el artículo 4 de esta Providencia, que hubieren obtenido ingresos brutos iguales o superiores al equivalente de treinta mil unidades tributarias (30.000 U.T.), conforme a lo señalado en su última declaración jurada anual presentada, para el caso de tributos que se liquiden por períodos anuales, o que hubieren efectuado ventas o prestaciones de servicios por montos iguales o superiores al equivalente de dos mil quinientas unidades tributarias (2.500 U.T) mensuales, conforme a lo señalado en una cualquiera de las seis (6) últimas declaraciones presentadas, para el caso de tributos que se liquiden por períodos mensuales.";
		$literal_c = "c) (Omissis)";
	} 
	
	if (substr($registro->rif, 0, 1) == "V" or substr($registro->rif, 0, 1) == "E")
		{
		$juridico = 0;
		}
	else
		{
		$juridico = 1;
		}

	if ($registro->id_tributo == 3 and $juridico == 0) { $forma = 99025;}
	if ($registro->id_tributo == 3 and $juridico == 1) { $forma = 99026;}
	if ($registro->id_tributo == 20) { $forma = 99026;}
	if ($registro->id_tributo == 1) { $forma = 99030;}
	
	//CALCULAR EL VALOR DE LA UNIDAD TRIBUTARIA PARA EL MOMENTO DEL CIERRE DEL PERIODO
	if ($registro->id_tributo==1)
		{
		$impuesto = "Impuesto al Valor Agregado";
		$item = 46;
		$periodo_iva = ObtenerPeiodo($registro->planilla_periodo_ini, 0);
		$periodo = " periodo ".$periodo_iva[1]." de ".$periodo_iva[0];
		$ingresos = 'ventas';
		}
	else
		{
		$impuesto = "Impuesto Sobre la Renta";
		$item = 711;
		$periodo = " ejercicio ".voltea_fecha($registro->planilla_periodo_ini)." al ".voltea_fecha($registro->planilla_periodo_fin);
		$ingresos = 'ingresos brutos';
		}
	$ut = unidad_infraccion($registro->planilla_periodo_fin);
	$monto_ut = round($registro->monto_planilla / $ut,2);
	$monto_ut = formato_moneda($monto_ut);

	//DETERMINAR EL TEXTO0 DE ACUERDO AL TIPO DE CONTRIBUYENTE
	if ($juridico == 1 and substr($registro->rif, 0, 1) == "G")
	{
		$texto = "en virtud de lo establecido en el artículo 3 literal c) de la Providencia ut supra, según la cual podran ser calificados como sujetos pasivos especiales, sometidos al control y administración de esta Gerencia Regional, los sujetos pasivos con domicilio fiscal en la jurisdicción de la Región Capital, que sean entes públicos nacionales, estadales y municipales, los institutos autónomos y demás entes descentralizados de la República, de los Estados y de los Municipios que actúan exclusivamente en calidad de agentes de retención o percepción de tributos.";
	}
	else
	{
		$texto = "por cuanto en la declaración N° ".$registro->planilla.", de fecha ".date("d/m/Y", strtotime($registro->fecha_planilla)).", forma ".$forma.", correspondiente al".$periodo.", refleja ".$ingresos." por la cantidad de Bs. " .formato_moneda($registro->monto_planilla)."; equivalente a ".$monto_ut." Unidades Tributarias, de la cual se desprende los ingresos establecidos para su calificación.";
	}
	
	$txt1 = ("Quien suscribe, ".$suscriptor." del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), en ejercicio de las competencias otorgadas en el artículo 4 numerales 1 y 7 del Decreto con Rango, Valor y Fuerza de Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria publicada en Gaceta Oficial de la Republica Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30/12/2015, y conforme a lo dispuesto en el artículo 2 numeral 47 de la Providencia Administrativa N° SNAT/2015/0009 publicado en Gaceta Oficial N° 40.589 de fecha 09/02/2016 en consonancia al artículo 102 de la Resolución N° 32 Sobre la Organización Atribuciones y Funciones del Servicio Nacional Integrado de Administración Tributaria, publicada en la Gaceta Oficial de la República de Venezuela N° 4.881 Extraordinario, de fecha 29/03/1995, y con lo dispuesto en los artículo 1 y 2 ".$texto_literal." de la Providencia Administrativa N° 0685 “Sobre Sujetos Pasivos Especiales” de fecha 06/11/2006, emanada de este Servicio, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 38.622 de fecha 08/02/2007, usted ha sido calificado como SUJETO PASIVO ESPECIAL, ".$texto);

	$txt2 = ("Artículo 2: Podrán ser calificados como sujetos pasivos especieles, sometidos al control y Administración de la respectiva Gerencia Regional de Tributos Internos de su domicilio fiscal, los siguientes sujetos pasivos:");
	
	$txt3 = $literal_a;
	
	$txt4 = $literal_b;
	
	$txt5 = $literal_c;
					
	if ($digito<>"V")
		{
		$txt5 = ("Asimismo, se le comunica que al ser notificado como Sujeto Pasivo Especial, es designado como Agente de Retención del Impuesto al Valor Agregado (IVA), de acuerdo a lo establecido en el artículo 1 de la Providencia SNAT-2015-0049 de fecha 14/07/2015, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela Nº 40.746 de fecha 15/09/2015.");
		}	
	else
		{	$txt5='';	}
	
	//DESCOMPONER FECHA DE INICIO COMO SUJETO PASIVO ESPECIAL
	$fecha_inicio = date('d',strtotime($registro->inicio_sujeto_especial)).' de '.$_SESSION['meses_anno'][abs(date('m',strtotime($registro->inicio_sujeto_especial)))].' de '.date('Y',strtotime($registro->inicio_sujeto_especial));
	
	$txt6 = ("En tal sentido, a partir del ".$fecha_inicio." deberá cumplir con sus obligaciones tributarias como Sujeto Pasivo Especial y Agente de Retención de los tributos en las formas y plazos que a continuación se establecen:");

	$txt7 = ("1. Deberá cumplir con sus obligaciones de declarar y pagar los tributos administrados por este Servicio, exclusivamente en las siguientes Oficinas Receptoras de Fondos Nacionales, únicos autorizados para recibir todos los pagos de sus obligaciones tributarias, en la fecha que corresponda de acuerdo al último dígito del número de Registro Único de Información Fiscal (RIF), de conformidad con lo previsto en el calendario para sujetos pasivos especiales dictado a tales efectos.");
	
	$txt8 = ("Las declaraciones del Impuesto al Valor Agregado, deberán ser procesadas electrónicamente a través del portal electrónico www.seniat.gob.ve, conforme lo dicta la Providencia Administrativa Nº 0082 del 9 de febrero de 2006, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 38.423 del 25 de abril de 2006.");

	$txt9 = ("Las declaraciones estimadas y definitivas del Impuesto Sobre la Renta, deberán ser presentadas en forma electrónica a través del portal electrónico  www.seniat.gob.ve, conforme a lo señalado en la Providencia SNAT/2009-0034 de fecha 5 de mayo de 2009, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela Nº 39.171 en la misma fecha.");

	$txt10 = ("Las declaraciones de las Retenciones del Impuesto Sobre la Renta deberán ser efectuadas de conformidad con lo establecido en la norma contenida en el Decreto Nº 1.808 publicado en la Gaceta Oficial de la República de Venezuela Nº 36.203 de fecha 12 de mayo de 1997 y ser presentadas conforme a lo dispuesto en la Providencia Administrativa SNAT/2009/0095 de fecha 22 de septiembre de 2009, publicada en  Gaceta Oficial de la República Bolivariana de Venezuela Nº 39.269 en la misma fecha.");

	$txt11 = ("2.  Las modalidades de pagos de las obligaciones a las que podrá optar serán:");

	$txt12 = ("a. En Efectivo.
b. Cheque a nombre del Tesoro Nacional del Banco Industrial de Venezuela, Banco de Venezuela y Banco Bicentenario.
c. Cheque de Gerencia de otros Bancos a nombre del Tesoro Nacional.
d. Pago electrónico a través de las Instituciones Financieras autorizadas Banco del Tesoro (www.bt.gob.ve), Banco de Venezuela (www.bancodevenezuela.com) y Banco Bicentenario (www.bicentenariobu.com).
e. Titulos valores, los cuales deberán ser transferidos previo a la fecha en que se realizará el pago, según la normativa vigente.");

	$txt17 = ("3.  Los pagos deben ser realizados de lunes a viernes en horario corrido, comprendido de 8:30 a.m. a 3:30 p.m., exceptuando los días feriados nacionales, regionales o bancarios, y para consultas y demás trámites, en horario comprendido de 8:00 a.m. a 4:30 p.m.");

	$txt18 = ("4. Los trámites relacionados con los tributos administrados por este Servicio, tales como: consultas, comunicaciones, solicitudes, recursos, entre otros, deberán ser consignados en el Área de Correspondencia ubicada en ".$registro->direccion_sede.". Sólo se exceptúan los trámites en materia de Aduana, los cuales deberán ser efectuados ante la Gerencia de Aduana correspondiente.");
	
	$txt19 = ("5. Por ser sujeto pasivo especial, debe regirse por el calendario especial para el cumplimiento de sus obligaciones tributarias, cuya fecha de vencimiento se ha diseñado en función del último dígito de su Registro Único de Información Fiscal (RIF), establecido en la Providencia Administrativa SNAT/2018/0189  de fecha 04/12/2018, Publicado en Gaceta Oficial de la República Bolivariana de Venezuela Nº 444.766 del 14/12/2018, así como en los ejercicios  sucesivos, los  cuales  serán publicados  en  la Gaceta Oficial de la República Bolivariana de Venezuela durante los últimos días de cada año calendario, por lo  cual  deberá estar atento en los medios impresos y a nuestro Portal  Fiscal www.seniat.gob.ve.");
	
	$txt20 = ("6. Presentar la Declaración Informativa de Patrimonio según lo establecido en la Providencia Administrativa Nº SNAT/2017/0002 de fecha 16/01/2017, publicada en la Gaceta Oficial Nº 41.075 de fecha 16/01/2017. ");
	
	$txt21 = ("Cualquier consulta o información requerida vía telefónica, puede efectuarse a través de los siguientes números   telefónicos: ".$registro->telefonos.", o la dirección electrónica ".$registro->email);

	$txt22 = ("El incumplimiento de estas obligaciones así como de cualquiera establecida en la normativa vigente, acarrea la aplicación de sanciones conforme a los previstos en el Decreto con Rango, Valor y Fuerza de Ley del  Código Orgánico Tributario.");

	$txt23 = ("Se le notifica al Sujeto Pasivo Especial, que en caso de disconformidad de la presente Providencia Administrativa, podrá interponer el Recurso Jerárquico ante la oficina de donde emanó el acto y/o subsidiariamente el  Recurso Contencioso Tributario por ante el  tribunal competente de  esta jurisdicción, previsto en los artículos 252 y 269 del Decreto con Rango, Valor y Fuerza de Ley del  Código Orgánico Tributario dentro del plazo de veinticinco (25) días hábiles, contados a partir de la presente notificación.");

	$txt24 = ("De conformidad con lo dispuesto en los artículos 171 al 178 Decreto con Rango, Valor y Fuerza de Ley del Código Orgánico Tributario vigente, en concordancia con el artículo 73 de la Ley Orgánica de Procedimientos Administrativos, y a los fines legales consiguientes se emite la presente Providencia Administrativa en dos (2) ejemplares de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del Sujeto Pasivo Especial.");

	// ------- TIPO DE PROVIDENCIA $_SESSION['VARIABLE1']
	$linea = 3.5;		
	$pdf->MultiCell(0,$linea,$txt1);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt5);
	if ($txt5<>'')	{$pdf->Ln(3); }
	$pdf->MultiCell(0,$linea,$txt6);
	$pdf->Ln(3);
	$pdf->MultiCell(0,$linea,$txt7);
	$pdf->Ln(3); 
	//********************************************************************************************************
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	//$pdf->MultiCell(0,$linea,"X = ".$x." - Y = ".$y);
	//$pdf->Ln(3); 
	$pdf->SetXY($x,$y);
	$miCabecera = array('BANCO', 'CIUDAD', 'DIRECCION');

	$sqlbancos = "SELECT a_banco.Descripcion, ce_banco_ubicacion.ciudad, ce_banco_ubicacion.direccion FROM a_banco, ce_banco_ubicacion WHERE ce_banco_ubicacion.id_banco = a_banco.id_banco AND ce_banco_ubicacion.sector = ".$sector;
	$pdf->cabeceraHorizontal($miCabecera, $x, $y);
	$tabla_bancos = mysql_query ($sqlbancos);
	while ($registro_bancos = mysql_fetch_object($tabla_bancos))
		{
		$misDatos = array(
						array('BANCO' => $registro_bancos->Descripcion, 'CIUDAD' => $registro_bancos->ciudad, 'DIRECCION' => utf8_encode($registro_bancos->direccion)),
						);
					$pdf->datosHorizontal($misDatos, $x, $y);
					$y += 5;
		}

	$pdf->SetFont('Arial','',9);
	$pdf->Ln(3);
	//********************************************************************************************************

	$pdf->MultiCell(0,$linea,$txt8);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt9);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt10);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt11);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt12);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt17);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt18);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt19);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt20);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt21);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt22);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt23);
	$pdf->Ln(3); 
	$pdf->MultiCell(0,$linea,$txt24);
	$pdf->Ln(3); 
	
	//---------------FIRMA DEL JEFE
	$cedula_gerente = $registro->cedula_autorizado;
	
	//if ($sector==1)
	//{
	//include "firma.php";
	//}
	//else
	//{
	include "firma_gerente.php";
	//}
	//----------------
	
	$pdf->SetRightMargin(30);
	$pdf->SetLeftMargin(30);
	$pdf->SetFont('Arial','',9);
	$pdf->Ln(3);
	
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(80,10,'NOTIFICACION AL SUJETO PASIVO',0,0,'L');
	$pdf->Cell(70,10,'FUNCIONARIO NOTIFICADOR',0,1,'L');
	$pdf->SetFont('Times','',8);
	$pdf->Cell(25,4,'Nombre y Apellido:',0,0,'L');
	$pdf->Cell(55,4,'__________________________________',0,0,'L');
	$pdf->Cell(25,4,'Nombre y Apellido:',0,0,'L');
	$pdf->Cell(40,4,'__________________________________',0,1,'L');
	$pdf->Cell(25,4,'C.I.N°:',0,0,'L');
	$pdf->Cell(55,4,'__________________________________',0,0,'L');
	$pdf->Cell(25,4,'C.I.N°:',0,0,'L');
	$pdf->Cell(40,4,'__________________________________',0,1,'L');
	$pdf->Cell(25,4,'Cargo:',0,0,'L');
	$pdf->Cell(55,4,'__________________________________',0,0,'L');
	$pdf->Cell(25,4,'Cargo:',0,0,'L');
	$pdf->Cell(40,4,'__________________________________',0,1,'L');
	$pdf->Cell(25,4,'Fecha:',0,0,'L');
	$pdf->Cell(55,4,'__________________________________',0,0,'L');
	$pdf->Cell(25,4,'Telefono:',0,0,'L');
	$pdf->Cell(40,4,'__________________________________',0,1,'L');
	$pdf->Cell(25,4,'Telefono:',0,0,'L');
	$pdf->Cell(55,4,'__________________________________',0,0,'L');
	$pdf->Cell(25,4,'Firma:',0,1,'L');
	$pdf->Cell(25,4,'Correo Electrónico:',0,0,'L');
	$pdf->Cell(40,4,'__________________________________',0,1,'L');
	$pdf->Cell(25,5,'Firma y Sello:',0,1,'L');
	// FIN DE LA PAGINA
	}
// FIN DE LA VALIDACION DE LA CONSULTA

$pdf->Output();

function ObtenerPeiodo($fecha, $Restar)
{
	//$fecha = date('Y-m-j');
	$fechaMesPasado = strtotime ('-'.$Restar.' month', strtotime($fecha));
	$fechaYear = date('Y', $fechaMesPasado);
	$fechaMes = date('m', $fechaMesPasado);

	setlocale(LC_TIME, 'spanish');  
	$nombre=strftime("%B",mktime(0, 0, 0, $fechaMes, 1, 2000)); 

	$matriz=array(); 
    $matriz[0]= $fechaYear; 
    $matriz[1]= ucwords($nombre); 
    return $matriz;
}

?>