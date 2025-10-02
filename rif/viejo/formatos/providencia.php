<?php
ob_end_clean();
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
setlocale(LC_TIME, 'sp_ES','sp', 'es');

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
$pdf->SetMargins(30,40,30);
//$pdf->SetAutoPageBreak(1,10);

$NUMERO_ORIGINAL=$_SESSION['NUMERO'];


	////////// DATOS DE LA PROVIDENCIA
	$consulta = "SELECT * FROM vista_ce_providencias WHERE anno=".$_SESSION['ANNO']." AND numero>=".$_SESSION['NUMERO']." AND sector=".$_SESSION['SEDE_USUARIO'].";";
	$tabla = mysql_query ($consulta);
		if ($registro = mysql_fetch_object($tabla))
		{
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
		
				
		$x=0;
		while ($x<1)
		{
			// PAGINA DE LA PROVIDENCIA
			$pdf->AddPage();
			$pdf->SetFont('Times','',12);
			setlocale(LC_TIME, 'sp_ES','sp', 'es');
			
			////////// SIGLAS DE LA RESOLUCION
			$consulta_x = "SELECT Siglas_resol_especiales FROM z_siglas WHERE id_sector=".$_SESSION['SEDE_USUARIO'].";";
			$tabla_x = mysql_query ( $consulta_x);
			$registro_x = mysql_fetch_object($tabla_x);
			$SIGLAS=$registro_x->Siglas_resol_especiales;
			// ---------------------
			////////// DATOS DE LA RESOLUCION
			$RESOLUCION = $SIGLAS.$_SESSION['ANNO']."/".sprintf("%004s", $_SESSION['NUMERO']);
			////////// FIN
			
			//////////
			$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
			$pdf->SetFont('Times','',11);
			$pdf->Cell(0,5,'N°    '.$RESOLUCION);
			$pdf->Ln(7);
			//////////
			
			////////// FECHA DE LA RESOLUCION
			list($dia,$mes,$anno)=explode('/',$registro->fecha_registro);
			$FECHA=mktime(0,0,0,$mes,$dia,$anno);
			////////// FIN
	
			// ---------------------
			$Ciudad = $registro->nombre;
			$t=(140-(strlen($Ciudad)));
			
			$pdf->Text($t,26,$Ciudad.', '.date('d',strtotime($registro->fecha_registro)).' de '.$_SESSION['meses_anno'][abs(date('m',strtotime($registro->fecha_registro)))].' de '.date('Y',strtotime($registro->fecha_registro)));
			$pdf->Ln(2);
				
			$pdf->SetFont('Times','B',13);
			$pdf->Cell(0,6,'PROVIDENCIA ADMINISTRATIVA',0,0,'C'); $pdf->Ln(8);
			$pdf->Cell(0,1,'Calificación como Sujeto Pasivo Especial',0,0,'C'); $pdf->Ln(10);
			$pdf->SetFont('Times','',12);
			
			$pdf->SetFont('Times','',12);
		
			$pdf->Cell(0,5,'CONTRIBUYENTE:');
		
			$pdf->SetFont('Times','B',12);
			$pdf->SetX(70);
		
			$pdf->MultiCell(0,5,strtoupper($registro->contribuyente));
			$pdf->Ln(2); 
			
			$pdf->SetFont('Times','',12);
			$pdf->Cell(0,5,'RIF N°:'); 
			
			$pdf->SetFont('Times','B',12);
			$pdf->SetX(70);
			$pdf->Cell(0,5,$registro->rif);
			$pdf->Ln(7); 
			
			$pdf->SetFont('Times','',12);
			$pdf->Cell(0,5,'DOMICILIO FISCAL:'); 
			
			$pdf->SetFont('Times','B',11);
			$pdf->SetX(70);
			$pdf->MultiCell(0,5,$registro->direccion);
			
			// FIN
			
			$pdf->SetFont('Times','',12);
			$pdf->Ln(6);
		
			$txt0 = "";
			
			switch ($registro->sector) {
				case 1:
					$suscriptor = "Gerente Regional de Tributos Internos de la Región Los Llanos";
					$tramitacion = "de la Gerencia Regional de Tributos Internos de la Región Los Llanos";
					break;
				case 2:
					$suscriptor = "Jefe del Sector de Tributos Internos San Juan de Los Morros";
					$tramitacion = "del Sector de Tributos Internos San Juan de Los Morros";
					break;
				case 3:
					$suscriptor = "Jefe del Sector de Tributos Internos San Fernando de Apure";
					$tramitacion = "del Sector de Tributos Internos San Fernando de Apure";
					break;
				case 4:
					$suscriptor = "Jefe de la Unidad de Tributos Internos Altagracia de Orituco";
					$tramitacion = "de la Unidad de Tributos Internos Altagracia de Orituco";
					break;
				case 5:
					$suscriptor = "Jefe del Sector de Tributos Internos Valle de la pascua";
					$tramitacion = "del Sector de Tributos Internos Valle de la pascua";
					break;
			}
			
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
			
			$txt1 = ("Quien suscribe, ".$suscriptor." adscrito a la Gerencia Regional de Tributos Internos de la Región Los Llanos, del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), en ejercicio de las atribuciones conferidas en el artículo 94 numeral 34 de la Resolución N° 32 Sobre la Organización Atribuciones y Funciones del Servicio Nacional Integrado de Administración Tributaria, publicada en la Gaceta Oficial N° 4.881 Extraordinario, de fecha 29 de marzo de 1.995 y de acuerdo a lo dispuesto en el artículo 1 de la Providencia Administrativa N° 0685 “Sobre Sujetos Pasivos Especiales” de fecha 6 de noviembre de 2006, emanada de este Servicio, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 38.622 de fecha 8 de febrero de 2007, le informo que ha sido calificado como SUJETO PASIVO ESPECIAL, en virtud de encuadrar en el supuesto establecido en el ".$texto_literal." del Artículo 2 de la providencia in comento. El cual señala:");
		
			$txt2 = ("Artículo 2: Podrán ser calificados como sujetos pasivos especieles, sometidos al control y Administración de la respectiva Gerencia Regional de Tributos Internos de su domicilio fiscal, los siguientes sujetos pasivos:");
			
			$txt3 = $literal_a;
			
			$txt4 = $literal_b;
			
			$txt5 = $literal_c;
			
			if ($registro->id_tributo==1)
			{
				$impuesto = "Impuesto al Valor Agregado";
				$item = 46;
			}
			else
			{
				$impuesto = "Impuesto Sobre la Renta";
				$item = 711;
			}
			
			$txt6 = ("Así lo evidencia la Declaración correspondiente al ".$impuesto." en su item ".$item." (N° ".$registro->planilla.", Fecha: ".date("d/m/Y", strtotime($registro->fecha_planilla)).", Bs. ".formato_moneda($registro->monto_planilla).")");
			
			$txt7 = ("En este mismo sentido, se le comunica que al ser notificado como Sujeto Pasivo Especial es designado como Agente de Retención del Impuesto al Valor Agregado (IVA), de acuerdo a lo establecido en el artículo N° 1 de la Providencia SNAT-2015-0049 de fecha 14 de julio de 2015, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 40.720 de fecha 10 de agosto de 2015.");
			
			//DESCOMPONER FECHA DE INICIO COMO SUJETO PASIVO ESPECIAL
			$fecha_inicio = date('d',strtotime($registro->inicio_sujeto_especial)).' de '.$_SESSION['meses_anno'][abs(date('m',strtotime($registro->inicio_sujeto_especial)))].' de '.date('Y',strtotime($registro->inicio_sujeto_especial));
			
			$txt8 = ("En tal sentido, a partir del ".$fecha_inicio." usted deberá cumplir con sus obligaciones tributarias como contribuyente Pasivo Especial y Agente de Retención de los tributos en las formas y plazos que a continuación se establecen:");

			$txt9 = ("1. Deberá cumplir con sus obligaciones de declarar y pagar los tributos administrados por este Servicio, exclusivamente en las siguientes Oficinas Receptoras de Fondos Nacionales, ".$registro->banco_especiales.", únicos autorizados para recibir todos los pagos de sus obligaciones tributarias, en la fecha que corresponda de acuerdo al último dígito del número de Registro Único de Información Fiscal (RIF), de conformidad con lo previsto en el calendario para sujetos pasivos especiales dictado a tales efectos.");

			$txt10 = ("Las declaraciones del Impuesto al Valor Agregado, deberán ser procesadas electrónicamente a través del portal electrónico www.seniat.gob.ve, conforme lo dicta la Providencia Administrativa Nº 0082 del 9 de febrero de 2006, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 38.423 del 25 de abril de 2006.");

			$txt11 = ("Las declaraciones estimadas y definitivas del Impuesto Sobre la Renta, deberán ser presentadas en forma electrónica a través del portal www.seniat.gob.ve, conforme a lo señalado en la Providencia SNAT/2009-0034 de fecha 5 de mayo de 2009, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela Nº 39.171 de la misma fecha.");

			$txt12 = ("Las declaraciones de las Retenciones del Impuesto Sobre la Renta deberán ser efectuadas según lo dispone el Decreto Nº 1.808 publicado en la  Gaceta Oficial Nº 36.203 de fecha 12 de mayo de 1997 y ser presentadas conforme lo dispuesto en la Providencia Administrativa SNAT/2009/0095 de fecha 22 de septiembre de 2009, publicada en  Gaceta Oficial de la República Bolivariana de Venezuela Nº 39.269 de la misma fecha.");

			$txt13 = ("2. Las modalidades de pagos de las obligaciones a las que podrá optar serán:");

			$txt14 = ("a. En Efectivo.");

			$txt15 = ("b. Cheque a nombre del Tesoro Nacional del Banco Industrial de Venezuela, Banco de Venezuela y Banco Bicentenario.");

			$txt16 = ("c. Cheque de Gerencia de otros Bancos a nombre del Tesoro Nacional.");

			$txt17 = ("d. Pago electrónico a través de las Instituciones Financieras autorizadas Banco del Tesoro (www.bt.gob.ve), Banco de Venezuela (www.bancodevenezuela.com) y Banco Bicentenario (www.bicentenariobu.com).");

			$txt18 = ("e. Titulos valores, los cuales deberán ser transferidos previo a la fecha en que se realizará el pago, según la normativa vigente.");

			$txt19 = ("3. Los pagos deben ser realizados de lunes a viernes en horario corrido, comprendido de 8:30 a.m. a 3:30 p.m., exceptuando los días feriados nacionales, regionales o bancarios, y para consultas y demás trámites, en horario comprendido de 8:00 a.m. a 4:30 p.m.");

			$txt20 = ("4. Los trámites relacionados con los tributos administrados por este Servicio, tales como: consultas, comunicaciones, solicitudes, recursos, entre otros, deberán consignarlos en la Taquilla de Tramitaciones ".$tramitacion." ubicada en ".$registro->direccion_especiales." Sólo se exceptúan los trámites en materia de Aduana, los cuales deberán ser efectuados ante la Gerencia de Aduana correspondiente.");

			$txt21 = ("5. Por ser sujeto pasivo especial, debe regirse por el calendario especial para el cumplimiento de sus obligaciones tributarias, cuya fecha de vencimiento se ha diseñado en función del último dígito de su Registro Único de Información Fiscal (RIF), establecido en la Providencia Administrativa SNAT/0047, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.542 de fecha 17/11/2014, así como en los ejercicios sucesivos, los cuales serán publicados en la Gaceta Oficial de la República Bolivariana de Venezuela durante los últimos días de cada año calendario, por la cual deberá estar atento en los medios impresos y a nuestro Portal Fiscal www.seniat.gob.ve.");
			
			$txt22 = ("Cualquier consulta o información requerida vía telefónica puede efectuarse a través de los siguientes números telefónicos: ".$registro->telefonos_especiales."");

			$txt23 = ("El incumplimiento de estas obligaciones así como de cualquiera establecida en la normativa vigente, acarrea la aplicación de sanciones conforme a lo previsto en el Código Orgánico Tributario.");

			$txt24 = ("De acuerdo a lo dispuesto en el artículo 73 de la Ley Orgánica de Procedimiento Administrativo se le notifica al Sujeto Pasivo Especial, que en caso de disconformidad con lo señalado en la presente Providencia Administrativa, podrá interponer el Recurso Jerárquico ante la oficina de donde emanó el acto y/o subsidiariamente el Recurso Contencioso Tributario por ante el Tribunal competente de esta jurisdicción, previsto en los artículos 253 y 267 del Código Orgánico Tributario dentro del plazo de veinticinco (25) días hábiles, contados a partir de la presente notificación.");

			$txt25 = ("De conformidad con lo dispuesto en los artículos 171 al 178 del Código Orgánico Tributario vigente, en concordancia con el artículo 73 de la Ley Orgánica de Procedimientos Administrativos, y a los fines legales consiguientes se emite la presente Providencia Administrativa en dos(2) ejemplares de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del Sujeto Pasivo Especial.");
		
			// ------- TIPO DE PROVIDENCIA $_SESSION['VARIABLE1']
			//include "texto_base.php";
			$linea = 5;		
			$pdf->MultiCell(0,$linea,$txt1);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt2);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt3);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt4);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt5);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt6);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt7);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt8);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt9);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt10);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt11);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt12);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt13);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt14);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt15);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt16);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt17);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt18);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt19);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt20);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt21);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt22);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt23);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt24);
			$pdf->Ln(4); 
			$pdf->MultiCell(0,$linea,$txt25);
			$pdf->Ln(4); 
			
			//---------------FIRMA DEL JEFE
			$cedula_gerente = $registro->cedula_autorizado;
			include "firma_gerente.php";
			//----------------
			
			$pdf->SetRightMargin(30);
			$pdf->SetLeftMargin(30);
			$pdf->SetFont('Times','',9);
			$pdf->Ln(8);
			
			$pdf->SetFont('Times','B',9);
			$pdf->Cell(80,10,'NOTIFICACION AL SUJETO PASIVO',0,0,'L');
			$pdf->Cell(70,10,'FUNCIONARIO NOTIFICADOR',0,1,'L');
			$pdf->SetFont('Times','',8);
			$pdf->Cell(25,5,'Nombre y Apellido:',0,0,'L');
			$pdf->Cell(55,5,'__________________________________',0,0,'L');
			$pdf->Cell(25,5,'Nombre y Apellido:',0,0,'L');
			$pdf->Cell(40,5,'__________________________________',0,1,'L');
			$pdf->Cell(25,5,'C.I.N°:',0,0,'L');
			$pdf->Cell(55,5,'__________________________________',0,0,'L');
			$pdf->Cell(25,5,'C.I.N°:',0,0,'L');
			$pdf->Cell(40,5,'__________________________________',0,1,'L');
			$pdf->Cell(25,5,'Cargo:',0,0,'L');
			$pdf->Cell(55,5,'__________________________________',0,0,'L');
			$pdf->Cell(25,5,'Cargo:',0,0,'L');
			$pdf->Cell(40,5,'__________________________________',0,1,'L');
			$pdf->Cell(25,5,'Fecha:',0,0,'L');
			$pdf->Cell(55,5,'__________________________________',0,0,'L');
			$pdf->Cell(25,5,'Telefono:',0,0,'L');
			$pdf->Cell(40,5,'__________________________________',0,1,'L');
			$pdf->Cell(25,5,'Telefono:',0,0,'L');
			$pdf->Cell(55,5,'__________________________________',0,0,'L');
			$pdf->Cell(25,5,'Firma:',0,1,'L');
			$pdf->Cell(25,5,'Correo Electrónico:',0,0,'L');
			$pdf->Cell(40,5,'__________________________________',0,1,'L');
			$pdf->Cell(25,6,'Firma y Sello:',0,1,'L');
			
			/*
			switch($x)
				{
				case 0: 
				$pdf->Cell(0,5,'CONTRIBUYENTE',0,0,'R'); $pdf->Ln(5);
				break;
				case 1: 
				$pdf->Cell(0,5,'EXPEDIENTE',0,0,'R'); $pdf->Ln(5);
				break;
				case 2: 
				$pdf->Cell(0,5,'FISCALIZACION',0,0,'R'); $pdf->Ln(5);
				break;
				case 3: 
				$pdf->Cell(0,5,'GERENCIA',0,0,'R'); $pdf->Ln(5);
				break;
				}						
			*/
			// FIN DE LA PAGINA
		$x++;
		}
		
		}// FIN DE LA VALIDACION DE LA CONSULTA
	$_SESSION['NUMERO']++;
$_SESSION['NUMERO']=$NUMERO_ORIGINAL;

$pdf->Output();

?>