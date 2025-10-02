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
		// NUMERO DE LA RESOLUCION
		list ($resolucion, $fecha_res, $num_res, $anno_res) = funcion_resolucion( $_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
		// FIN		
		//Posición a 1,1 cm del final
		$this->SetY(-11);
		//Arial itálica 8
		$this->SetFont('Times','I',9);
		//Color del texto en gris
		$this->SetTextColor(120);
		//Número de página
		$this->Cell(120,0,$resolucion,0,0,'L');
		$this->Cell(0,0,sistema().' '.$this->PageNo().' de {nb}',0,0,'R');
		}	
	}

// ENCABEZADO
$pdf=new PDF('P','mm','oficio');
$pdf->AliasNbPages();
$pdf->SetMargins(22,25,17);
$pdf->SetDisplayMode($zoom=='real');
$pdf->SetAutoPageBreak(1, $margin=15);

//--- COMIENZO DEL REPORTE
$pdf->AddPage();
$pdf->SetFont('Times','B',12);
setlocale(LC_TIME, 'sp_ES','sp', 'es');

// ACTUALIZACION DEL NUMERO DE LA RESOLUCION
generar_resolucion( $_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
// FIN

// NUMERO DE LA RESOLUCION
list ($resolucion, $fecha_res, $num_res, $anno_res) = funcion_resolucion( $_SESSION['SEDE'], $_SESSION['ORIGEN'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
// FIN

////////// INFORMACION DEL EXPEDIENTE
$consulta_datos = "SELECT * FROM vista_exp_especiales WHERE anno=0".$_SESSION['ANNO_PRO']." AND numero=0".$_SESSION['NUM_PRO']." AND sector =0".$_SESSION['SEDE'].";";
$tabla_datos = mysql_query($consulta_datos);
$registro_datos = mysql_fetch_object($tabla_datos);
// ---------------------
//if ($_SESSION['SEDE'] == 1) { $num_res = $num_res-41;}
//if ($_SESSION['SEDE'] == 2) { $num_res = $num_res-109;}
//if ($_SESSION['SEDE'] == 3) { $num_res = $num_res-74;}
//if ($_SESSION['SEDE'] == 4) { $num_res = $num_res-9;}
//if ($_SESSION['SEDE'] == 5) { $num_res = $num_res-17;}

////////// SIGLAS DE LA resolucion
$SIGLAS = $registro_datos->Siglas_resol_especiales;
// ---------------------

// ---------------------
$pdf->SetFont('Arial','B',15);
$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
$pdf->SetFont('Times','B',11); 
$pdf->Ln(8);
$pdf->Cell(0,5,'N°    '.$resolucion);
//$pdf->Ln(10);

////////// GERENCIA, SECTOR O UNIDAD DE EMISION
$Sede = $registro_datos->adscripcion_gerencia;
// -----------

////////// CIUDAD DE EMISION
$Ciudad = $registro_datos->nombre;
// -----------

list($anno,$mes,$dia)=explode('-',$fecha_res);
$FECHA=mktime(0,0,0,$mes,$dia,$anno);
$_SESSION['VARIABLE']=$FECHA;

$t=(140-(strlen($Ciudad)));

$mes=array(Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre);

$pdf->Text($t,24,$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$mes[(strftime('%m', strtotime(date('m/d/Y',$FECHA)))-1)].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))));
$pdf->Ln(10);
//----------------------- FIN
	
$pdf->SetFont('Times','B',13-$tamaño);


	$pdf->Cell(0,5,'RESOLUCIÓN DE IMPOSICIÓN DE SANCIÓN',0,0,'C'); 
	$pdf->Ln(10);

	$pdf->SetFont('Times','',11);
	$pdf->Cell(0,5,'Contribuyente:');
	
	$pdf->SetFont('Times','B',11);
	$pdf->SetX(60);
	$pdf->MultiCell(0,5,strtoupper($registro_datos->contribuyente));
	$pdf->Ln(3); 
	
	$pdf->SetFont('Times','',11);
	$pdf->Cell(0,5,'RIF N°:'); 
	
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
	
	list ($estado, $sede, $conector1, $conector2, $adscripcion) = buscar_sector($_SESSION['SEDE']);
	
	if ($_SESSION['ANNO_PRO'] == 2017 and (($_SESSION['SEDE'] == 1 and $_SESSION['NUM_PRO']>68) or ($_SESSION['SEDE'] == 5 and $_SESSION['NUM_PRO']>39) or ($_SESSION['SEDE'] == 3 and $_SESSION['NUM_PRO']>83) or ($_SESSION['SEDE'] == 4 and $_SESSION['NUM_PRO']>21)))
		{
	$txt='En '.$registro_datos->nombre.', Estado '.$estado.', sede '.$conector1.' '.$sede.' '.$conector2.' Gerencia Regional de Tributos Internos '.utf8_decode(buscar_region()).', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT); de conformidad con lo establecido en los artículos 89, 131 numeral 2, 137 numeral 2 y 182 del Código Orgánico Tributario publicado en Gaceta Oficial N° 6.152 Extraordinario del 18 de noviembre de 2014 , en lo sucesivo COT, la División de Sujetos Pasivos Especiales adscrita a '.$adscripcion.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.utf8_decode(buscar_region()).', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), en uso de las facultades legales conferidas en el artículo 102, numeral 7, en concordancia con el numeral 18 de la Resolución Nº 32 sobre la Organización, Atribuciones y Funciones del SENIAT, publicada en Gaceta Oficial Nº 4.881 Extraordinario, de fecha 29/03/1995 y el artículo 4 numeral 9 de la Ley del SENIAT, publicada en Gaceta Oficial Nº 37.329 de fecha 08/11/2001, el articulo 2 de la resolución SNAT/2002/Nº 913, publicada en Gaceta Oficial 37.398, de fecha 06/02/2002 y el artículo 2 de la Providencia Administrativa SNAT/2009/0077 publicada en Gaceta Oficial Nº 39.246 de fecha 20/08/2009; se procede a emitir la presente Resolución de Imposición de Sanción, por el (la) Funcionario(a) actuante '.$registro_datos->nombrefuncionario.' titular de la cédula de Identidad N° V-'.formato_cedula($registro_datos->funcionario).' debidamente facultado(a) según Autorizacion N° '.$registro_datos->Siglas_resol_aut_spe.'/'.$anno_res.'/'.($num_res).' de fecha '.voltea_fecha($fecha_res).'.';
		}
	else
		{
	$txt='En '.$registro_datos->nombre.', Estado '.$estado.', sede '.$conector1.' '.$sede.' '.$conector2.' Gerencia Regional de Tributos Internos '.utf8_decode(buscar_region()).', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT); de conformidad con lo establecido en los artículos 89, 131 numeral 2, 137 numeral 2 y 182 del Código Orgánico Tributario publicado en Gaceta Oficial N° 6.152 Extraordinario del 18 de noviembre de 2014 , en lo sucesivo COT, la División de Sujetos Pasivos Especiales adscrita a '.$adscripcion.' '.$conector2.' Gerencia Regional de Tributos Internos de la '.utf8_decode(buscar_region()).', del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), en uso de las facultades legales conferidas en el artículo 102, numeral 7, en concordancia con el numeral 18 de la Resolución Nº 32 sobre la Organización, Atribuciones y Funciones del SENIAT, publicada en Gaceta Oficial Nº 4.881 Extraordinario, de fecha 29/03/1995 y el artículo 4 numeral 9 de la Ley del SENIAT, publicada en Gaceta Oficial Nº 37.329 de fecha 08/11/2001, el articulo 2 de la resolución SNAT/2002/Nº 913, publicada en Gaceta Oficial 37.398, de fecha 06/02/2002 y el artículo 2 de la Providencia Administrativa SNAT/2009/0077 publicada en Gaceta Oficial Nº 39.246 de fecha 20/08/2009; se procede a emitir la presente Resolución de Imposición de Sanción en virtud de los hechos que se exponen a continuación:';
		}
		
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	

////////// REVISION SI EXISTEN SANCIONES PARA CONTRIBUYENTES ESPECIALES

$consulta_xxx = "SELECT especial FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND especial=3 and serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_xxx = mysql_query($consulta_xxx);
if ($registro_xxx = mysql_fetch_object($tabla_xxx))
	{
	////////// IMPRESION DEL TEXTO
	$txt='En virtud de lo establecido en el único aparte del artículo 108 del Codigo Orgánico Tributario y por cuanto el contribuyente identificado supra, está calificado por la administración tributaria como Sujeto Pasivo Especial, se procede a aumentar las sanciones pecuniarias impuestas tipificadas en el Capítulo II del citado Código en un doscientos por ciento (200%).';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4);
	}

////////// VALIDACION DE LAS SANCIONES
$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla = mysql_query($consulta);

/////////------------------------+
if ($registro_s = mysql_fetch_object($tabla))
{
	//if ($acta>=0)
//		{
//		////////////////// SANCIONES APLICADAS
//		$pdf->SetFont('Times','B',11-$tamaño);
//		$pdf->Cell(0,5,'SANCIONES POR INCUMPLIMIENTO DE DEBERES FORMALES',0,0,'C'); 
//		$pdf->SetFont('Times','',11-$tamaño);
//		$pdf->Ln(10);
//		}
	
//////// DATOS DE LAS SANCIONES
$consulta = "SELECT * FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
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
		$txt='Que '.utf8_decode($registro_s->sancion).', en contravención a lo establecido en el (los) artículo(s) '.utf8_decode($registro_s->art_ley_rgto).' de la (del) '.utf8_decode($registro_s->ley).' '.utf8_decode($registro_s->art_regla).', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, por cuanto se trata de la '.$Matriz_1[$registro_s->reiteracion].' infracción de esta índole cometida por el (la) Contribuyente'.$Incremento;
		$pdf->MultiCell(0,5,$txt);
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
	$txt='Que '.utf8_decode($registro_s->sancion).', en contravención a lo establecido en el (los) artículo(s) '.utf8_decode($registro_s->art_ley_rgto).' de la (del) '.utf8_decode($registro_s->ley).' '.utf8_decode($registro_s->art_regla).', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa consistente en una (1) Unidad Tributaria por cada factura, documento o comprobante, la cual asciende a la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias.';
	$pdf->MultiCell(0,5,$txt);
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
		$txt='Que '.utf8_decode($registro_s->sancion).', en contravención a lo establecido en el (los) artículo(s) '.utf8_decode($registro_s->art_ley_rgto).' de la (del) '.utf8_decode($registro_s->ley).' '.utf8_decode($registro_s->art_regla).', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, por cuanto se trata de la '.$Matriz_1[$registro_s->reiteracion].' infracción de esta índole cometida por el (la) Contribuyente'.$Incremento;
		$pdf->MultiCell(0,5,$txt);
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
		$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, calculada en su termino medio, conforme a lo establecido en la mencionada norma, en concordancia a lo previsto en el artículo 37 del Código Penal, en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';
		$pdf->MultiCell(0,5,$txt);
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
		$txt='Que '.$registro_s->sancion.', en contravención a lo establecido en el (los) artículo(s) '.$registro_s->art_ley_rgto.' de la (del) '.$registro_s->ley.' '.$registro_s->art_regla.', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, calculada en su termino medio, conforme a lo establecido en la mencionada norma, en concordancia a lo previsto en el artículo 37 del Código Penal, en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';
		$pdf->MultiCell(0,5,$txt);
		$pdf->Ln(4); 
		break;
		////////////////////////////////////		
		case 18 or 12 or 10 or 53: // AQUI VAN LAS DE RETENCIONES
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
	$txt='Que '.utf8_decode($registro_s->sancion).', en contravención a lo establecido en el (los) artículo(s) '.utf8_decode($registro_s->art_ley_rgto).' de la (del) '.utf8_decode($registro_s->ley).' '.utf8_decode($registro_s->art_regla).', correspondiente al (a los) ejercicio(s) o (los) periodo(s) comprendido(s) entre '.voltea_fecha($registro_s->periodoinicio).' y '.voltea_fecha($registro_s->periodofinal).'; en consecuencia, esta Administración Tributaria procede a aplicar la sanción prevista en el artículo '.$Aparte.' del Código Orgánico Tributario vigente por concepto de multa en la cantidad de '.abs(($registro_s->monto_ut/$registro_s->concurrencia)*$registro_s->especial).' Unidades Tributarias, calculada conforme a lo establecido en la mencionada norma en virtud de no existir circunstancias atenuantes y/o agravantes que considerar en el presente caso.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	// AQUI == >>>>>>
	// LLENADO DE LA TABLA DE FACTURAS
	
	$Total = 0;
//	$consulta_x = "SELECT Format([Net_Fis_Reten_Detalladas].[PeriodoiNICIO],'dd/mm/yyyy') AS Desde, Format([Net_Fis_Reten_Detalladas].[PeriodoFIN],'dd/mm/yyyy') AS Hasta, Net_Fis_Reten_Detalladas.Multa AS Total, Net_Fis_Reten_Detalladas.Factura, Format([Net_Fis_Reten_Detalladas].[FechaPago],'dd/mm/yyyy') AS FechaPago, [Net_Fis_Reten_Detalladas].[Multa] AS Monto, Net_Fis_Reten_Detalladas.AñoProvidencia, Net_Fis_Reten_Detalladas.NroProvidencia, Format([Net_Fis_Reten_Detalladas].[FechaVen],'dd/mm/yyyy') AS Vencimiento FROM Net_Fis_Reten_Detalladas WHERE ([Net_Fis_Reten_Detalladas].[PeriodoiNICIO]=#".$registro_s->FechaInicioDeclaracion10."# and [Net_Fis_Reten_Detalladas].[PeriodoFIN]=#".$registro_s->FechaFinDeclaracion10."#) and (((Net_Fis_Reten_Detalladas.AñoProvidencia)=".$_SESSION['ANNO_PRO'].") AND ((Net_Fis_Reten_Detalladas.NroProvidencia)=".$_SESSION['NUM_PRO']."));";
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
//		$txt = 'N° de Factura';
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

//////////////// FIN DE LAS SANCIONES

//////// REVISION SI EXISTEN SANCIONES CON CONCURRENCIA

//////// VALIDACION DE LAS SANCIONES CON CONCURRENCIA
$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND concurrencia=2 and serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_xxx = mysql_query($consulta); 
//////// FIN
if ($registro_xxx = mysql_fetch_object($tabla_xxx))
	{
	////////// IMPRESION DEL TEXTO
	$txt='Por cuanto en el presente caso existe concurrencia de ilícitos tributarios sancionados con penas pecuniarias, se aplica la sanción más grave, aumentada con la mitad de las otras sanciones, conforme a lo establecido en el Artículo 81 del Código Orgánico Tributario, en virtud de lo cual esta Administración Tributaria procedió a determinar la(s) multa(s) resultante(s), aplicando el concurso de acuerdo a la sumatoria de la totalidad de las sanciones aplicables por cada tipo de ilícito, a efectos de establecer el ilícito cuya sumatoria arroje la sanción más cuantiosa, tal como se demuestra a continuación:';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4);
	
	////////// DATOS DE LAS SANCIONES RESUMEN POR SANCION
	$consulta = "SELECT id_liquidacion, art_cot, aplicacion, id_sancion, sum(monto_ut*especial) SumaDeUTCifras, sum(monto_ut/concurrencia*especial) as SumaDeUTdividida FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND serie<>29 AND serie<>38 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." GROUP BY art_cot, aplicacion, id_sancion;";
	$tabla_r = mysql_query($consulta);
	////////// FIN
	
	$Num=0;
	while ($registro_r = mysql_fetch_object($tabla_r))
		{
		$Num++;
		////////// IMPRESION DEL CUADRO CON EL TITULO
		$pdf->Ln(3);
		
		//------- PARA QUE EL CUADRO ARRANQUE EN UNA HOJA NUEVA Y NO SE SEPARE EL TITULO
		if ($pdf->GetY()>170) {$pdf->AddPage();}
		
			////////// TITULOS
			$pdf->SetFont('Times','',9);
			$pdf->SetFillColor(192,192,192);
			$pdf->Cell(10,5,'',0,0,'C');
			$txt='Art. COT.';
			$pdf->Cell(108,5,$txt,1,0,'C','true');
			$txt='SubTotal U.T.';
			$pdf->Cell(2,5,'',0,0,'C');
			$pdf->Cell(50,5,$txt,1,0,'C','true');
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
			$pdf->Cell(50,5,abs($txt),1,0,'C');
			$pdf->Ln(5);	
		
		////////// DETALLE DE LAS SANCIONES		
		$consulta = "SELECT * FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND id_sancion=".$registro_r->id_sancion." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." ORDER BY monto_ut DESC, periodoinicio, periodofinal;";
		$tabla_l = mysql_query($consulta);
					
		////////// TITULOS
		$pdf->SetFont('Times','',9);
		$pdf->SetFillColor(192,192,192);
		
		$pdf->Cell(10,5,'',0,0,'C');
		$txt='Descripción del Hecho Punible';
		$pdf->Cell(78,5,$txt,1,0,'C','true');
		$txt='Período';
		$pdf->Cell(2,5,'',0,0,'C');
		$pdf->Cell(33,5,$txt,1,0,'C','true');
		$txt='Monto U.T.';			
		$pdf->Cell(2,5,'',0,0,'C');
		$pdf->Cell(23,5,$txt,1,0,'C','true');
		$txt='Concur. U.T.';
		$pdf->Cell(2,5,'',0,0,'C');
		$pdf->Cell(20,5,$txt,1,0,'C','true');
		$pdf->Ln(5);
			
		while ($registro_l = mysql_fetch_object($tabla_l))
		{
			$pdf->SetFont('Times','',6);
			$pdf->SetFillColor(0,0,0);

			$pdf->Cell(10,8,'',0,0,'C');
			$txt=$registro_l->Codigo.'- '.substr($registro_l->sancion,0,55).'_';
			$pdf->Text($pdf->GetX()+2, $pdf->GetY()+3, $txt);
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
			$pdf->Cell(20,8,abs($txt),1,0,'C');

			$pdf->Ln(8);
		}
		////////// 
	}
	}
// --------------------------------------------- SI TIENE SANCIONES VDF
}
// --------------------------------------------- FIN SI TIENE SANCIONES

////------- POR SI TIENE INTERES Y NO LLEVA ACTA DE REPARO
$consulta = "SELECT id_liquidacion FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND serie=38 and origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
$tabla_i = mysql_query($consulta);
	
if ($registro_i = mysql_fetch_object($tabla_i))
	{
	//---------------------
	$pdf->Ln(6);
	$pdf->SetFont('Times','B',11);
	////////// VALIDAR SI ES UN INTERES COMPLEMENTARIO
	if ($registro_datos->resolucion_prim<>'')	
		{
		$pdf->Cell(0,5,'INTERESES MORATORIOS',0,0,'C'); 
		$pdf->Ln(10);
		$pdf->SetFont('Times','',11);
		$txt='Procede '.strtolower($Sede).' Gerencia Regional de Tributos Internos, al cálculo de los Intereses Moratorios, derivados de la Omisión del pago del Impuesto determinado en la Resolución Culminatoria de Sumario Administrativo N° '.strtoupper($registro_datos->resolucion_prim).', de fecha '.voltea_fecha($registro_datos->resolucion_prim_fecha).', donde se refleja el cálculo de los intereses del Impuesto No Enterado, a la fecha de la referida Resolución, de conformidad con el artículo 66 del Código Orgánico Tributario, de acuerdo a la tasa activa promedio publicada por el banco Central de Venezuela, incrementada en los términos establecidos en el mencionado artículo, cálculo éste que se efectúa a partir del día siguiente a la fecha de la Resolución Culminatoria de Sumario Administrativo ut supra hasta la fecha del pago del Impuesto determinado en la descrita Resolución, según consta en la (s) planilla (s) de liquidación, correspondientes a los periodos tributarios que originaron el impuesto a pagar:';
		$pdf->MultiCell(0,5,$txt);
		}
	else
		{
		$pdf->Cell(0,5,'INTERESES MORATORIOS',0,0,'C'); 
		$pdf->Ln(10);
		$pdf->SetFont('Times','',11);
		$txt='Procede '.strtolower($Sede).' Gerencia Regional de Tributos Internos, al cálculo de Intereses Moratorios de conformidad con el artículo 66 del Código Orgánico tributario, de acuerdo a la tasa activa promedio publicada por el banco Central de venezuela, incrementada en los términos establecidos en dicho artículo, cálculo éste que se efectua a partir del día siguiente al vencimiento del plazo establecido para la autoliquidación y pago del tributo, es decir desde el vencimiento de cada período hasta el momento de efectuado el pago, según consta en la(s) planilla(s) de pago, correspondiente a los períodos tributarios que originaron el impuesto a pagar. Así se declara.';
		$pdf->MultiCell(0,5,$txt);
		}
		
	$pdf->Ln(5); 
	//---------------------
	$txt='Se calculan utilizando la siguiente fórmula:
	Intereses Moratorios = C x R x T / t (año) x 100';
	$pdf->MultiCell(0,5,$txt); 
	$pdf->Ln(5); 
	
	$txt='Donde:
	C = Capital
	R = Tasa de interés
	T = Tiempo de mora
	 t = 360 x 100';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(5); 
	
	$txt='A continuación se muestra el cuadro resumen de los intereses moratorios, los cuales se detallan en la aplicacion de la citada fórmula.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(5); 
	
	// TITULOS DE LA PRIMERA TABLA DE INTERES
	
	$pdf->SetFont('Times','',8);
	$pdf->SetFillColor(192,192,192);
	
	$txt='Tributo';
	$pdf->Cell(18,6,$txt,1,0,'C','true');
	
	$txt='Período';
	$pdf->Cell(30,6,$txt,1,0,'C','true');
	
	$txt='Impuesto Determinado (Bs.)';
	$pdf->Cell(35,6,$txt,1,0,'C','true');
	
	// POR SI ES UN INTERES COMPLEMENTARIO
	if ($registro_datos->resolucion_prim<>'')	
		{	$txt='Fecha Resolucion';	}
	else
		{	$txt='Fecha Vencimiento';	}
				
	$pdf->Cell(25,6,$txt,1,0,'C','true');
	
	$txt='Fecha Pago';
	$pdf->Cell(15,6,$txt,1,0,'C','true');
	
	$txt='Días de Mora';
	$pdf->Cell(18,6,$txt,1,0,'C','true');
	
	$txt='Intereses Moratorios (Bs.)';
	$pdf->Cell(33,6,$txt,1,0,'C','true');
	
	$pdf->Ln(6);
	
	// LLENADO DE LA PRIMERA TABLA DE INTERES
	
	$Total = 0;
	$Interes = 0;
	$Dias_Total = 0;
	
	$consulta = "SELECT * FROM vista_sanciones_aplicadas WHERE id_resolucion=0 AND serie=38 and origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." ORDER BY id_tributo;";
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
		$pdf->Cell(18,4,$txt,1,0,'C');
		
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
	$pdf->Cell(18,4,$txt,1,0,'C');
	
	$txt = number_format(doubleval($Interes),2,',','.');
	$pdf->Cell(33,4,$txt,1,0,'C');
	
	$pdf->Ln(5);
		
	$txt='Aplicación de la fórmula: Intereses Moratorios = C x R x T / (año) 360 x 100';
	$pdf->MultiCell(0,5,$txt);
	
	//$pdf->Ln();
	}
////------------------------------------------------------

$pdf->SetFont('Times','',10-$tamaño);
$pdf->Ln(5);

if ($acta<0)
	{
	$txt='Por lo antes expuesto, expídase a cargo del (de la) Contribuyente o Responsable antes identificado(a) planilla(s) de pago por concepto de multa(s) por el (los) monto(s) indicado(s), la(s) cual(es) deberá cancelar en una Oficina Receptora de Fondos Nacionales de forma inmediata; asímismo, se le notifica que el monto de la sanción se encuentra sujeta a modificación en caso de cambio del valor de la Unidad Tributaria entre la presente fecha y la fecha efectiva de pago, conforme a lo previsto en el Artículo 91 del Código Orgánico Tributario.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(4); 
	
	$txt='En caso de inconformidad con el presente Acto Administrativo el (la) Contribuyente o Responsable podrá ejercer los recursos que consagra el Código Orgánico Tributario en sus Artículos 252 y 266, dentro de los plazos previstos en los Artículos 257 y 268 Ejusdem, para lo cual deberá darse cumplimiento a lo previsto en los Artículos 253 y 259 del citado código.';
	$pdf->MultiCell(0,5,$txt);
	}
else
	{
	$txt='Se hace del conocimiento del sujeto pasivo, que el código Orgánico tributario en su artículo 242 señala que podrá, en caso de no estar de acuerdo con el presente Acto Administrativo, ejercer el recurso Jerárquico, para lo cual deberá llenar los extremos señalados en el artículo 243 ejusdem. Dispone además esta norma, que deberá contar con la presencia de un abogado o de cualquier otro profesional afín al área tributaria. Su omisión constituye causal de inadmisibilidad, a tenor de lo dispuesto en el artículo 250 numeral 4 ejusdem. El lapso para su interposición será de veinticinco (25) días hábiles, contados a partir de la notificación de la presente Resolución. Igualmente podrá interponer el recurso Contencioso Tributario contenido en el artículo 259 ejusdem, dentro del plazo de veinticinco (25) días hábiles contados a partir de la notificación del Acto que se impugna o del vencimiento del lapso previsto para decidir el recurso jerarquico, en caso de denegacion tácita de este, todo a tenor de lo dispuesto en el artículo 261 ejusdem.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(5);
	
	$txt='Definitivamente firme el presente Acto Administrativo, y no habiendo el sujeto pasivo dado cumplimiento al contenido del mismo; '.strtolower($Sede).' Gerencia Regional de Tributos Internos, procederá al juicio Ejecutivo previsto en el artículo 289 y siguientes del Código Orgánico Tributario vigente.';
	$pdf->MultiCell(0,5,$txt);
	$pdf->Ln(5); 

	//--- PARA QUE NO QUEDE INCOMPLETA
	if ($pdf->GetY()>216) {$pdf->AddPage();}
	
	$txt='Y para que así conste a los fines legales consiguientes se levanta la presente resolución por triplicado de un mismo tenor y a un solo efecto, uno (1) de cuyos ejemplares queda en poder del Sujeto pasivo, que firma en señal de notificación.';
	$pdf->MultiCell(0,5,$txt);
	}
	
$pdf->Ln(7); 

$pdf->SetFont('Times','B',11);
	
$txt='Comuníquese,';
$pdf->Cell(0,5,$txt,0,0,'C');

// FIRMA DEL JEFE
$pdf->Ln(7);

////BUSCAMOS AL GERENTE
//$sql_gerente = "SELECT ci_gerente FROM z_region ORDER BY ci_gerente DESC";
//$tabla_gerente = mysql_query($sql_gerente);
//$reg_gerente = mysql_fetch_object($tabla_gerente);
//
//$cedula_gerente = $reg_gerente->ci_gerente;

include "firma.php";

//-----------
$pdf->SetRightMargin(17);
$pdf->SetLeftMargin(17);
$pdf->Ln(1);
// FIN

$pdf->SetFont('Times','B',9);
$pdf->Cell(110,5,'Notificado');  										
$pdf->Cell(0,5,'Funcionario(s) Actuante(s)'); 
$pdf->Ln(7);
$pdf->SetFont('Times','',8);
$pdf->Cell(15,5,'Nombre:');	
$pdf->Cell(90,5,'__________________________________');	
$pdf->Cell(20,5,'Nombre:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'C.I. N°:');	
$pdf->Cell(90,5,'__________________________________');	
$pdf->Cell(20,5,'C.I. N°:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Cargo:');	
$pdf->Cell(90,5,'__________________________________');	
$pdf->Cell(20,5,'Dependencia:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Fecha:');	
$pdf->Cell(90,5,'__________________________________');	
$pdf->Cell(20,5,'Cargo:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Firma:');	
$pdf->Cell(90,5,'__________________________________');	
$pdf->Cell(20,5,'Fecha:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Telefono:');	
$pdf->Cell(90,5,'__________________________________');	
$pdf->Cell(20,5,'Firma:');	
$pdf->Cell(0,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Sello:');	
$pdf->Cell(90,5,'__________________________________');	
$pdf->Ln(5);
$pdf->Cell(15,5,'Correo:');	
$pdf->Cell(0,5,'__________________________________');	
	
// FIN DE LA RESOLUCION

$pdf->Output();
?>