<?php
ob_end_clean();
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
//<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}
require('../../funciones/fpdf.php');
//mysql_query("SET NAMES 'latin1'");

class PDF extends FPDF
{
	

function Footer()
	{    
		$this->SetY(-20);
		//Arial itlica 8
		$this->SetFont('Times','',7);
		////////// REGION DE EMISION
		$consulta_x = "SELECT direccion_fiscalizacion, iniciales_prov FROM z_sectores WHERE id_sector=".$_SESSION['SEDE'].";";
		$tabla_x = mysql_query ( $consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		$Direccion=$registro_x->direccion_fiscalizacion;
		$Iniciales=$registro_x->iniciales_prov; 

		
		//alexander	
		  $consulta_xx = "SELECT * FROM vista_providencias WHERE anno=".$_SESSION['ANNO']." AND numero>=".$_SESSION['NUMERO']." AND numero<=".$_SESSION['FIN']." AND sector=".$_SESSION['SEDE'].";";
	      $tabla_xx = mysql_query ($consulta_xx);
	     $registro_xx = mysql_fetch_object($tabla_xx);
	      $programa = $registro_xx->TipoPrograma;
		  $nombre = $registro_xx->nombre;
		//if($programa == 'Verificacion' and  $nombre == 'Calabozo' or $nombre == 'San Juan de los Morros'  or $nombre == 'San Fernando de Apure' or $nombre == 'Altagracia de Orituco' or $nombre == 'Valle de la Pascua'){
		if($programa == 'Investigacion' and  $nombre == 'Calabozo' or $nombre == 'San Juan de los Morros'  or $nombre == 'San Fernando de Apure' or $nombre == 'Altagracia de Orituco' or $nombre == 'Valle de la Pascua'){
			   $Iniciales=$registro_x->iniciales_prov;
			    //$Iniciales="CEAA/jac";
			  }
               else
			  { 
		         $Iniciales="JAAB/jac";
				 
		      }

		//fin alex
		
		
		$this->SetFont('Times','I',8);
		$this->SetTextColor(120);
		$this->SetRightMargin(17);
		$this->SetLeftMargin(17);
		$this->MultiCell(0,4,utf8_decode($Direccion));
		// ---------------------
			$this->SetY(-12);
			//Arial itlica 8
			$this->Cell(0,4,$Iniciales);
			// ---------------------
			$this->SetY(-12);
			//Arial itlica 8
			$s=$this->PageNo();
			while ($s>4)
			{	$s=$s-4;	}
			$this->Cell(340,10,sistema().' '.$s.' de 4',0,0,'C');
	}
}	
// ENCABEZADO
$pdf=new PDF('P','mm','PROVIDENCIA');
$pdf->AliasNbPages();
$pdf->SetMargins(17,30,17);
$pdf->SetAutoPageBreak(1,10);

$NUMERO_ORIGINAL=$_SESSION['NUMERO'];

while ($_SESSION['NUMERO']<=$_SESSION['FIN'])
	{
	////////// ACTUALIZAR EL STATUS DE LA PROVIDENCIA
	$consulta = "UPDATE expedientes_fiscalizacion SET status=1, fecha_impresion=date(now()) WHERE status=0 AND anno=".$_SESSION['ANNO']." AND numero>=".$_SESSION['NUMERO']." AND numero<=".$_SESSION['FIN']." AND sector=".$_SESSION['SEDE'].";";
	$tabla = mysql_query ($consulta);
	////////// DATOS DE LA PROVIDENCIA
	$consulta = "SELECT * FROM vista_providencias WHERE anno=".$_SESSION['ANNO']." AND numero>=".$_SESSION['NUMERO']." AND numero<=".$_SESSION['FIN']." AND sector=".$_SESSION['SEDE'].";";
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
		
		////////// PRIMERA PAGINA
		
		$pdf->AddPage();
		$pdf->SetFont('Times','',12);
		setlocale(LC_ALL, 'sp_ES','sp','es');
		
		//////////
		$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
		$pdf->SetFont('Times','B',15);
		$pdf->Ln(5);
		$pdf->SetFillColor(190);
		$pdf->Cell(0,6,'Hoja de Control de Providencias',1,0,'C','true');
		$pdf->Ln(11);
		//////////
				
		////////// FECHA DE LA PROVIDENCIA
		list($anno,$mes,$dia)=explode('-',$registro->fecha_emision);
		$FECHA=mktime(0,0,0,$mes,$dia,$anno);
		////////// FIN
		
		////////// DATOS DEL JEFE DE LA DIVISION
		$jefe_division = $registro->ci_jefe;
		$fecha_jefe = $registro->fecha_emision;
		// ---------------------

		////////// GERENCIA, SECTOR O UNIDAD DE EMISION		
		$Direccion = $registro->direccion_fiscalizacion;
		$Iniciales = $registro->iniciales_prov;

		//$jefe = $registro->jefe_fis;
		//$jefe_division = $jefe;
		$texto_sede = $registro->texto_sede;
		$adscripcion = $registro->adscripcion;
		$tipo = $registro->tipo;	
        $programa = $registro->TipoPrograma;
        $nombre = $registro->nombre;		
	
		
		////////// CIUDAD DE EMISION
		$Ciudad=$registro->nombre;
		//////////

		$pdf->SetFont('Times','B',11);
		$pdf->Cell(15,5,'Numero',1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(8,5,$_SESSION['NUMERO'],1,0,'C');
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(10,5,'Tipo',1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(117,5,$registro->tipo.'    '.utf8_decode($registro->descripcion),1,0,'C');
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(12,5,'Fecha',1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(20,5,date('d/m/Y',$FECHA),1,0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(30,5,'Fiscal',1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(105,5,utf8_decode($registro->Nombres1).' '.utf8_decode($registro->Apellidos1),1,0,'L');
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(20,5,utf8_decode('Cédula'),1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(27,5,formato_cedula($registro->ci_fiscal1),1,0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(30,5,'Supervisor',1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(105,5,utf8_decode($registro->Nombres).' '.utf8_decode($registro->Apellidos),1,0,'L');
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(20,5,utf8_decode('Cédula'),1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(27,5,formato_cedula($registro->ci_supervisor),1,0,'C');
		$pdf->Ln(5);
			
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(30,5,'Contribuyente',1,0,'C');
		$pdf->SetFont('Times','',11);
		//$pdf->Cell(105,5,palabras($contribuyente),1,0,'L');
		$pdf->Cell(105,5,ucwords(strtolower($contribuyente)),1,0,'L');
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(20,5,'RIF',1,0,'C');
		$pdf->SetFont('Times','',11);
		$pdf->Cell(27,5,$rif,1,0,'C');
		$pdf->Ln(10);
		
		//alex --- verificar el cambio de firma
		 if($programa == 'Investigacion' or $programa == 'Verificacion')	{
			  $jefe = $registro->jefe_fis;
			  $jefe_division = $jefe;
			  }
               else
				  { $jefe = "Gerente"; }
		
		//-------------------
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(91,6,'Impresa',1,0,'C');
        $pdf->Cell(91,6,'Firmada por el '.ucwords($jefe).'',1,0,'C');
		$pdf->Ln(6);
		$pdf->SetFont('Times','',11);
		
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Ln(5);
		
		////////// USUARIO DE EMISION
		$consulta_x = "SELECT * FROM vista_emisor_providencia WHERE id_sector=".$_SESSION['SEDE'].";";
		$tabla_x = mysql_query ( $consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		$Nombre = strtolower($registro_x->Nombres.' '.primera_cadena($registro_x->Apellidos));
				
		$pdf->Cell(46,25,ucwords($Nombre),1,0,'C');
		
		$pdf->Cell(45,25, date('d',$FECHA) .' de '.$_SESSION['meses_anno'][abs(strftime('%m', strtotime(date('m/d/Y',$FECHA))))]. ' del ' . date('Y',$FECHA),1,0,'C');
		
		$pdf->Cell(46,25,'',1,0,'C');
		$pdf->Cell(45,25,'',1,0,'C');
		$pdf->Ln(28);
		
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(91,6,utf8_decode('Firmada por el Coordinador de Fiscalización'),1,0,'C');
		$pdf->Cell(91,6,'Recibida por el Fiscal',1,0,'C');
		$pdf->Ln(6);
		$pdf->SetFont('Times','',11);
		//-------------------
		
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(46,12,'',1,0,'C');
		$pdf->Cell(45,12,'',1,0,'C');
		$pdf->Cell(46,12,'',1,0,'C');
		$pdf->Cell(45,12,'',1,0,'C');
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(91,6,utf8_decode('Notificación de Providencia'),1,0,'C');
		$pdf->Cell(91,6,utf8_decode('Concluida Fiscalización'),1,0,'C');
		$pdf->Ln(6);
		$pdf->SetFont('Times','',11);
		
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(46,12,'',1,0,'C');
		$pdf->Cell(45,12,'',1,0,'C');
		$pdf->Cell(46,12,'',1,0,'C');
		$pdf->Cell(45,12,'',1,0,'C');
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(91,6,utf8_decode('Resolución y Liquidación'),1,0,'C');
		$pdf->Cell(91,6,utf8_decode('Notificación'),1,0,'C');
		$pdf->Ln(6);
		$pdf->SetFont('Times','',11);
		
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Cell(46,5,'Firma',1,0,'C');
		$pdf->Cell(45,5,'Fecha',1,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(46,12,'',1,0,'C');
		$pdf->Cell(45,12,'',1,0,'C');
		$pdf->Cell(46,12,'',1,0,'C');
		$pdf->Cell(45,12,'',1,0,'C');
		
		$pdf->Ln(17);
		
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(182,5,'Anulada',1,0,'C');
		$pdf->Ln(5);
		$pdf->SetFont('Times','',11);
		
		$pdf->Cell(42,15,'Firma y Fecha',1,0,'C');
		$pdf->Cell(140,15,'Motivo',1,0,'L');
		$pdf->Ln(20);
		
		$pdf->SetFont('Times','B',10);
		$pdf->Cell(72,6,utf8_decode('CÓDIGO DE ACTIVIDAD ECONOMICA:'),1,0,'C');
		$pdf->Cell(110,6,'',1,0,'L');
		$pdf->Ln(10);
		
		$pdf->Cell(182,5,'REPRESENTANTE LEGAL:',1,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(41,5,utf8_decode('C.I. N°:'),1,0,'L');
		$pdf->Cell(100,5,'NOMBRE:',1,0,'L');
		$pdf->Cell(41,5,'TELEFONO:',1,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(41,6,'',1,0,'L');
		$pdf->Cell(100,6,'',1,0,'L');
		$pdf->Cell(41,6,'',1,0,'L');
		$pdf->Ln(10);
		
		///////////////////
		$pdf->SetY(15);
		$pdf->SetX(-60);
		$pdf->Cell(40,7,'Control Despacho',1,0,'C');
		///////////////////
		
		////////// FIN DE LA PRIMERA PAGINA ----------
				
		$x=0;
		while ($x<3)
		{
			// PAGINA DE LA PROVIDENCIA
			$pdf->AddPage();
			$pdf->SetFont('Times','',10);
			setlocale(LC_TIME, 'sp_ES','sp', 'es');
			
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS=$registro->Siglas_resol_fis;
			

			// ---------------------
			////////// DATOS DE LA RESOLUCION
			//$RESOLUCION = $SIGLAS."/".$_SESSION['ANNO']."/".$registro->Siglas2."/".$registro->Siglas1.sprintf("%004s", $_SESSION['NUMERO']); Original
			  $RESOLUCION = $SIGLAS."/".$_SESSION['ANNO']."/".$registro->Siglas1.sprintf("%004s", $_SESSION['NUMERO']);
			  
			////////// FIN
			
			////////// A
			
			//////////
			$pdf->Image('../../imagenes/logo.jpeg',20,8,65);
			$pdf->SetFont('Times','',11);
			$pdf->Cell(0,5,utf8_decode('N°    ').$RESOLUCION);
			$pdf->Ln(5);
			//////////
			//alex variable
			if($programa == 'Investigacion' or $programa == 'Verificacion')	{
				$nota=utf8_decode("Verificación");
			}
				else
				{
				$nota=utf8_decode("Ficalización y Determinación");
				}
					
			//fin alex
			// ---------------------
			$t=(140-(strlen($Ciudad)));
			
			//$pdf->Text($t,26,$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$_SESSION['meses_anno'][abs(strftime('%m', strtotime(date('m/d/Y',$FECHA))))].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))));

			$pdf->Ln(2);
				
			$pdf->SetFont('Times','B',11);
			//$pdf->Cell(0,5,'PROVIDENCIA ADMINISTRATIVA',0,0,'C'); $pdf->Ln(8); original
			//$pdf->Cell($t,26,$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$_SESSION['meses_anno'][abs(strftime('%m', strtotime(date('m/d/Y',$FECHA))))].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))));
			$pdf->Cell(0,5,'                                                                                                                  '.$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$_SESSION['meses_anno'][abs(strftime('%m', strtotime(date('m/d/Y',$FECHA))))].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))),0,0,'C'); $pdf->Ln(6);
			$pdf->Cell(0,5,'PROVIDENCIA ADMINISTRATIVA',0,0,'C'); $pdf->Ln(6);
			//$pdf->Cell(0,5,'('.$Ciudad.', '.strftime('%d', strtotime(date('m/d/Y',$FECHA))).' de '.$_SESSION['meses_anno'][abs(strftime('%m', strtotime(date('m/d/Y',$FECHA))))].' del '.strftime('%Y', strtotime(date('m/d/Y',$FECHA))),0,0,'C'); $pdf->Ln(6);
			$pdf->SetFont('Times','',10);
			$pdf->SetFont('Times','B',12);
			$pdf->Cell(0,5,'('.$nota.')',0,0,'C'); $pdf->Ln(6);
			$pdf->SetFont('Times','',10);
			
						
			$pdf->SetFont('Times','',10);
		
			$pdf->Cell(0,5,'CONTRIBUYENTE:');
		
			$pdf->SetFont('Times','B',10);
			$pdf->SetX(60);
		
			$pdf->MultiCell(0,5,strtoupper(utf8_decode($contribuyente)));
			$pdf->Ln(2); 
			
			$pdf->SetFont('Times','',10);
			$pdf->Cell(0,5,utf8_decode('RIF N°:')); 
			
			$pdf->SetFont('Times','B',10);
			$pdf->SetX(60);
			$pdf->Cell(0,5,$rif);
			$pdf->Ln(5); 
			
			$pdf->SetFont('Times','',10);
			$pdf->Cell(0,5,'DOMICILIO FISCAL:'); 
			
			$pdf->SetFont('Times','B',10);
			$pdf->SetX(60);
			$pdf->MultiCell(0,5,utf8_decode($direccion));
			
			// FIN
			
			$pdf->SetFont('Times','',10);
			$pdf->Ln(4);
		
			$tamañoletra = 10;
			//$tipo = 10;
			$datosley = "Impuesto al Valor Agregado"; //utf8_decode
			
			$txt0 = ($texto_sede);
			
			if ($_SESSION['SEDE']>1)
			{
				$texto_sector = ", Providencia Administrativa N° SNAT/2011/0015 del 05/04/2011 mediante la cual se unifican las competencias de verificación, fiscalización y determinación de los sectores y unidades adscritas a las Gerencias Regionales de Tributos Internos del Servicio Nacional Integrado de Administración Tributaria (SENIAT) publicada en Gaceta Oficial Nro. 39.649 de fecha 05/04/2011";
			} else {
				$texto_sector = "";
			}
			
	//alex 06.2023		aplica sede	
		if ($_SESSION['SEDE']==1 and $registro->tipo < 2000){
			
		//$txt1 = utf8_decode(" Gerencia Regional de Tributos Internos de la " .buscar_region(). " del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), haciendo uso de las facultades que le otorga el artículo 4, numerales 8, 9,10 y 47 del Decreto N° 2.177, mediante el cual se dicta el Decreto con Rango, Valor y Fuerza de Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30/12/2015, el artículo 98 (105, 106, 113 y 114 en los casos que aplique) de la Resolución N° 32 de fecha 24/03/1995 publicada en Gaceta Oficial N° 4.881 Extraordinario de fecha 29/03/1995, sobre la Organización, Atribuciones y Funciones del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), así como el artículo 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, mediante la cual se reorganiza las Gerencias Regionales de Tributos Internos y las Gerencias de Aduanas Principales y se crea la División de Cobro Ejecutivo y Medidas Cautelares adscritas a las Gerencias Regionales de Tributos Internos".$texto_sector.", en concordancia con los artículos 131 numeral 2 y 5, 134, 137 al 146, 155, 166, 187 al 204 del Código Orgánico Tributario publicado en la Gaceta Oficial de la República Bolivariana de Venezuela N° 6.507 de fecha 29-01-2020, autoriza al (a los) funcionario(s) actuante(s) ");
           $txt1 = utf8_decode(" Gerencia Regional de Tributos Internos de la " .buscar_region(). " del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), haciendo uso de las facultades que le otorga el artículo 4, numerales 8, 9,10 y 47 del Decreto N° 2.177, mediante el cual se dicta el Decreto con Rango, Valor y Fuerza de Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30/12/2015, el artículo 98 de la Resolución N° 32 de fecha 24/03/1995 publicada en Gaceta Oficial N° 4.881 Extraordinario de fecha 29/03/1995, sobre la Organización, Atribuciones y Funciones del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), así como el artículo 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, mediante la cual se reorganiza las Gerencias Regionales de Tributos Internos y las Gerencias de Aduanas Principales y se crea la División de Cobro Ejecutivo y Medidas Cautelares adscritas a las Gerencias Regionales de Tributos Internos, en concordancia con los artículos 131 numeral 2 y 5, 134, 137 al 146, 155, 166, 187 al 204 del Código Orgánico Tributario publicado en la Gaceta Oficial de la República Bolivariana de Venezuela N° 6.507 de fecha 29-01-2020, autoriza al funcionario actuante ");
		}
		elseif($registro->tipo >= 2000 and $_SESSION['SEDE']==1 ){
			   $txt1 = utf8_decode(" Gerencia Regional de Tributos Internos de la " .buscar_region(). " del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), haciendo uso de las facultades que le otorga el artículo 4, numerales 8, 9, 10 y 44 del decreto N° 2.177, mediante el cual se dicta el Decreto con Rango, Valor y Fuerza de la Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30-12-2015, el artículo 98 de la Resolución 32 de fecha 24-03-1995 publicada en la Gaceta Oficial N° 4.881 Extraordinario de fecha 29-03-1995 sobre la Organización, Atribuciones y Funciones del Servicio Nacional Integrado de Administración Tributaria (SENIAT), así como el artículo 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, mediante la cual se reorganizan las Gerencias Regionales de Tributos Internos y las Gerencias de Aduanas Principales y se crea la División de Cobro Ejecutivo y Medidas Cautelares Adscritas a las Gerencias Regionales de Tributos Internos, en concordancia con los Artículos 131 numeral 2, 134, 155, 162, 182 al 186 del Código Orgánico Tributario publicado en la Gaceta Oficial Extraordinaria N° 6.507 de fecha 29-01-2020, autoriza al funcionario actuante ");
		}
		if (($_SESSION['SEDE']==2 or $_SESSION['SEDE']==3 or $_SESSION['SEDE']==5)  and $registro->tipo < 2000){
			    $txt1 = utf8_decode(" Gerencia Regional de Tributos Internos de la " .buscar_region(). " del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), haciendo uso de las facultades que le otorga el artículo 4, numerales 8, 9,10 y 47 del Decreto N° 2.177, mediante el cual se dicta el Decreto con Rango, Valor y Fuerza de Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30/12/2015, y los artículo 105 Y 106 de la Resolución N° 32 de fecha 24/03/1995 publicada en Gaceta Oficial N° 4.881 Extraordinario de fecha 29/03/1995, sobre la Organización, Atribuciones y Funciones del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), así como el artículo 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, mediante la cual se reorganiza las Gerencias Regionales de Tributos Internos y las Gerencias de Aduanas Principales y se crea la División de Cobro Ejecutivo y Medidas Cautelares adscritas a las Gerencias Regionales de Tributos Internos, en concordancia con los artículos 131 numeral 2 y 5, 134, 137 al 146, 155, 166, 187 al 204 del Código Orgánico Tributario publicado en la Gaceta Oficial de la República Bolivariana de Venezuela N° 6.507 de fecha 29-01-2020, autoriza al funcionario actuante ");
		}
		elseif ($registro->tipo >= 2000 and ($_SESSION['SEDE']==2 or $_SESSION['SEDE']==3 or $_SESSION['SEDE']==5)){
		         $txt1 = utf8_decode(" Gerencia Regional de Tributos Internos de la " .buscar_region(). " del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), haciendo uso de las facultades que le otorga el artículo 4, numerales 8, 9, 10 y 44 del decreto N° 2.177, mediante el cual se dicta el Decreto con Rango, Valor y Fuerza de la Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30-12-2015, y los artículo 105 Y 106 de la Resolución 32 de fecha 24-03-1995 publicada en la Gaceta Oficial N° 4.881 Extraordinario de fecha 29-03-1995 sobre la Organización, Atribuciones y Funciones del Servicio Nacional Integrado de Administración Tributaria (SENIAT), así como el artículo 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, mediante la cual se reorganizan las Gerencias Regionales de Tributos Internos y las Gerencias de Aduanas Principales y se crea la División de Cobro Ejecutivo y Medidas Cautelares Adscritas a las Gerencias Regionales de Tributos Internos, en concordancia con los Artículos 131 numeral 2, 134, 155, 162, 182 al 186 del Código Orgánico Tributario publicado en la Gaceta Oficial Extraordinaria N° 6.507 de fecha 29-01-2020, autoriza al funcionario actuante ");
		}
		if (($_SESSION['SEDE']==4)  and $registro->tipo < 2000){
			       $txt1 = utf8_decode(" Gerencia Regional de Tributos Internos de la " .buscar_region(). " del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), haciendo uso de las facultades que le otorga el artículo 4, numerales 8, 9,10 y 47 del Decreto N° 2.177, mediante el cual se dicta el Decreto con Rango, Valor y Fuerza de Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30/12/2015, y los artículo 113 y 114 de la Resolución N° 32 de fecha 24/03/1995 publicada en Gaceta Oficial N° 4.881 Extraordinario de fecha 29/03/1995, sobre la Organización, Atribuciones y Funciones del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), así como el artículo 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, mediante la cual se reorganiza las Gerencias Regionales de Tributos Internos y las Gerencias de Aduanas Principales y se crea la División de Cobro Ejecutivo y Medidas Cautelares adscritas a las Gerencias Regionales de Tributos Internos, en concordancia con los artículos 131 numeral 2 y 5, 134, 137 al 146, 155, 166, 187 al 204 del Código Orgánico Tributario publicado en la Gaceta Oficial de la República Bolivariana de Venezuela N° 6.507 de fecha 29-01-2020, autoriza al funcionario actuante ");
		}
		elseif ($registro->tipo >= 2000 and $_SESSION['SEDE']==4){
		        $txt1 = utf8_decode(" Gerencia Regional de Tributos Internos de la " .buscar_region(). " del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), haciendo uso de las facultades que le otorga el artículo 4, numerales 8, 9, 10 y 44 del decreto N° 2.177, mediante el cual se dicta el Decreto con Rango, Valor y Fuerza de la Ley del Servicio Nacional Integrado de Administración Aduanera y Tributaria, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 6.211 Extraordinario de fecha 30-12-2015, y los artículo 113 y 114 de la Resolución 32 de fecha 24-03-1995 publicada en la Gaceta Oficial N° 4.881 Extraordinario de fecha 29-03-1995 sobre la Organización, Atribuciones y Funciones del Servicio Nacional Integrado de Administración Tributaria (SENIAT), así como el artículo 2 numerales 8 y 13 de la Providencia Administrativa SNAT/2015-0009 de fecha 03-02-2015, publicada en Gaceta Oficial de la República Bolivariana de Venezuela N° 40.598 de fecha 09-02-2015, mediante la cual se reorganizan las Gerencias Regionales de Tributos Internos y las Gerencias de Aduanas Principales y se crea la División de Cobro Ejecutivo y Medidas Cautelares Adscritas a las Gerencias Regionales de Tributos Internos, en concordancia con los Artículos 131 numeral 2, 134, 155, 162, 182 al 186 del Código Orgánico Tributario publicado en la Gaceta Oficial Extraordinaria N° 6.507 de fecha 29-01-2020, autoriza al funcionario actuante ");
		}
		
	  //fin alex*/
	   
			$txt2 = utf8_decode(", portador de la cédula de Identidad N° V-");
			//$txt2 = (", titulares de las cédulas de Identidad N°s V-");
			
			$txt3 = utf8_decode(" y supervisado por ");
			
			$txt4 = utf8_decode(", portador de la cédula de Identidad N° V-");
			
			$txt5 = (", adscritos ");
			
			$txt6 = ($adscripcion);
			
			$txt7 = (" de la citada Gerencia Regional, a los fines de ");
			
			$txt8 = utf8_decode("El Área de Fiscalización ");
			$txt9 = utf8_decode("al Área de Fiscalización ");
		
			// ------- TIPO DE PROVIDENCIA $_SESSION['VARIABLE1']
			include "texto_base.php";
			
			if ($_SESSION['SEDE']==1)
			{
			 $txt =$txt0 . $txt1 . utf8_decode($registro->Nombres1).' '.utf8_decode($registro->Apellidos1) . $txt2 . formato_cedula($registro->ci_fiscal1) . $txt3 . utf8_decode($registro->Nombres).' '.utf8_decode($registro->Apellidos) . $txt4 .formato_cedula($registro->ci_supervisor) . $txt5 . $txt9 . $txt6 . $txt7 . $texto_obj . $txt_after_ejercicio . utf8_decode($registro->texto2) . $txt_after_periodos . $txt_final;
			} else {
			$txt =$txt8 . $txt0 . $txt1 . utf8_decode($registro->Nombres1).' '.utf8_decode($registro->Apellidos1) . $txt2 . formato_cedula($registro->ci_fiscal1) . $txt3 . utf8_decode($registro->Nombres).' '.utf8_decode($registro->Apellidos) . $txt4 .formato_cedula($registro->ci_supervisor) . $txt5 .  $txt9 .$txt6 . $txt7 . $texto_obj . $txt_after_ejercicio . utf8_decode($registro->texto2) . $txt_after_periodos . $txt_final;
			}
			
			
			//if($registro->tipo <> 2000){
			 //$txt =$txt0 . $txt1 . $registro->Nombres1.' '.$registro->Apellidos1 . $txt2 . formato_cedula($registro->ci_fiscal1) . $txt3 . $registro->Nombres.' '.$registro->Apellidos . $txt4 .formato_cedula($registro->ci_supervisor) . $txt5 . $txt6 . $txt7 . $texto_obj . $txt_after_ejercicio . $registro->texto2 . $txt_after_periodos . $txt_final;
			
			//$txt =$txt8 . $txt0 . $txt1 . $registro->Nombres1.' '.$registro->Apellidos1 . $txt2 . formato_cedula($registro->ci_fiscal1) . $txt3 . $registro->Nombres.' '.$registro->Apellidos . $txt4 .formato_cedula($registro->ci_supervisor) . $txt5 . $txt6 . $txt7 . $texto_obj . $txt_after_ejercicio . $registro->texto2 . $txt_after_periodos . $txt_final;
			
			//}else{
			//$txt = $txt0 . $txt1 . $registro->Nombres1.' '.$registro->Apellidos1 . $txt2 . formato_cedula($registro->ci_fiscal1) . $txt3 . $registro->Nombres.' '.$registro->Apellidos . $txt4 .formato_cedula($registro->ci_supervisor) . $txt5 . $txt6 . $txt7 . $texto_obj . $txt_after_ejercicio . $registro->texto2 . $txt_after_periodos . $txt_final;
			//}
			//$txt = $txt0 . $txt1 . $registro->Nombres1.' '.$registro->Apellidos1 . ' y ANDREA JOSE MENDOZA FLORES'. $txt2 . formato_cedula($registro->ci_fiscal1) . ' y V-23.569.251 respectivamente' . $txt3 . $registro->Nombres.' '.$registro->Apellidos . $txt4 .formato_cedula($registro->ci_supervisor) . $txt5 . $txt6 . $txt7 . $texto_obj . $txt_after_ejercicio . $registro->texto2 . $txt_after_periodos . $txt_final;
			$pdf->MultiCell(0,4,$txt);

			$pdf->Ln(6); 
					
			
			//---------------FIRMA DEL JEFE
			//$pdf->SetY(-80);
			//if($programa == 'Verificacion' and  $nombre == 'Calabozo' or $nombre == 'San Juan de los Morros'  or $nombre == 'San Fernando de Apure' or $nombre == 'Altagracia de Orituco' or $nombre == 'Valle de la Pascua'){
			If($programa == 'Investigacion' or $programa == 'Verificacion' and $nombre == 'Calabozo' or $nombre == 'San Juan de los Morros'  or $nombre == 'San Fernando de Apure' or $nombre == 'Altagracia de Orituco' or $nombre == 'Valle de la Pascua'){
				$pdf->SetY(-76);
				include "firma.php";
			//$pdf->SetY(-102);
			//$pdf->SetY(-102);
			//include "firma_gerente.php";
			}
			else{
				
				$pdf->SetY(-110);
				include "firma_gerente.php";
			}
			//----------------
			
			$pdf->SetRightMargin(17);
			$pdf->SetLeftMargin(17);
			$pdf->SetFont('Times','',9);
			$pdf->Ln(1);
			
			$pdf->SetFont('Times','B',9);
			$pdf->Cell(90,6,'NOTIFICACION AL SUJETO PASIVO',0,0,'L');
			$pdf->Cell(70,6,'FUNCIONARIO ACTUANTE',0,1,'L');
			$pdf->SetFont('Times','',8);
			$pdf->Cell(25,4,'Nombre y Apellido:',0,0,'L');
			$pdf->Cell(65,4,'__________________________________',0,0,'L');
			$pdf->Cell(25,4,'Nombre y Apellido:',0,0,'L');
			$pdf->Cell(40,4,'__________________________________',0,1,'L');
			$pdf->Cell(25,4,'C.I.N:',0,0,'L');
			$pdf->Cell(65,4,'__________________________________',0,0,'L');
			$pdf->Cell(25,4,'C.I.N:',0,0,'L');
			$pdf->Cell(40,4,'__________________________________',0,1,'L');
			$pdf->Cell(25,4,'Cargo:',0,0,'L');
			$pdf->Cell(65,4,'__________________________________',0,0,'L');
			$pdf->Cell(25,4,'Cargo:',0,0,'L');
			$pdf->Cell(40,4,'__________________________________',0,1,'L');
			$pdf->Cell(25,4,'Fecha:',0,0,'L');
			$pdf->Cell(65,4,'__________________________________',0,0,'L');
			$pdf->Cell(25,4,'Telefono:',0,0,'L');
			$pdf->Cell(40,4,'__________________________________',0,1,'L');
			$pdf->Cell(25,4,'Telefono:',0,0,'L');
			$pdf->Cell(65,4,'__________________________________',0,0,'L');
			$pdf->Cell(25,4,'Firma:',0,1,'L');
			$pdf->Cell(25,4,utf8_decode('Correo Electrónico:'),0,0,'L');
			$pdf->Cell(40,4,'__________________________________',0,1,'L');
			$pdf->Cell(25,6,'Firma y Sello:',0,1,'L');
			$pdf->SetXY(-20,-15);
			switch($x)
				{
				case 0: 
				$pdf->Cell(0,5,'CONTRIBUYENTE',0,0,'R'); $pdf->Ln(5);
				break;
				case 1: 
				$pdf->Cell(0,5,'EXPEDIENTE',0,0,'R'); $pdf->Ln(5);
				break;
				case 2: 
				$pdf->Cell(0,5,utf8_decode('FISCALIZACIÓN'),0,0,'R'); $pdf->Ln(5);
				break;
				case 3: 
				$pdf->Cell(0,5,'GERENCIA',0,0,'R'); $pdf->Ln(5);
				break;
				}						
			
			// FIN DE LA PAGINA
		$x++;
		}
		
		}// FIN DE LA VALIDACION DE LA CONSULTA
	$_SESSION['NUMERO']++;
}// FIN DEL CICLO COMPLETO
$_SESSION['NUMERO']=$NUMERO_ORIGINAL;

$pdf->Output();

?>