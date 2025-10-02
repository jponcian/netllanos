<?php
//CONECTAR A LA BD
	include "../../conexion.php";
	mysql_query("SET NAMES 'utf8'");
	include "../../funciones/auxiliar_php.php";
	$rif=$_GET['rif'];
	
// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf.php');
require_once('tcpdf/tcpdf_barcodes_1d.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
    	$html = '
		<div>
			<table witch="100%" border="0"><tr>
				<td><img src="../../imagenes/logo.jpeg" width="204" height="69" /></td>
			</tr>
			<tr>
				<td></td>
			</tr></table>
		</div>
    	';
		$this->SetFont('times', '', 10);
    	$this->writeHTML($html, true, false, true, false, '');

    }

    // Page footer
    public function Footer() {
    	$html = '
		<div>
			<table witch="100%" border="0"><tr>
				<td align="left">Sistema de Información Regional - NETLOSLLANOS</td>
				<td align="right">1 / 1</td>
			</tr></table>
		</div>
    	';
		$this->SetFont('times', '', 10);
		$this->Cell(0, 0, 'Sistema de Información Regional - NETLOSLLANOS', 1, 0, 'C', 0, '', 0);
		$this->Cell(0, 0, $this->getAliasRightShift().$this->PageNo().'/'.$this->getAliasNbPages(), 0, 0, 'R');
    	//$this->writeHTML($html, true, false, true, false, '');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Gustavo Garcia');
$pdf->SetTitle('ACTOS ADMINISTRATIVO NOTIFICADOS AL SUJETO PASIVO');
$pdf->SetSubject('TCPDF');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(20);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
$sql_acta = "SELECT vista_contribuyentes_direccion.contribuyente FROM vista_contribuyentes_direccion WHERE vista_contribuyentes_direccion.rif = '".$rif."'";
//echo $sql_acta;

$tabla_acta = mysql_query($sql_acta);
$temp = mysql_fetch_object($tabla_acta);

setlocale(LC_ALL,"es_ES");
$dia = date("d");
$mes = buscar_mes(date("n"));
$año = date("Y");

$sede = 'Calabozo, '.$dia.' de '.$mes.' de '.$año;

$html = 'La Gerencia Regional de Tributos Internos Región Los Llanos a través de la División de Fiscalización y de las Áreas de Fiscalización de los Sectores y Unidades de Tributos Internos adscritos a ésta Gerencia Regional, hace constar mediante la presente los siguientes Actos Administrativos notificados al Sujeto Pasivo: <b>'.$temp->contribuyente.'</b> registrado en el Registro de Información Fiscal (RIF) bajo el numero <b>'.formato_rif($rif).'</b>.';

$html1 = 'Certificación emitida en repuesta a lo solicitado en memorando Nro. SNAT/INTI/GRTI/RLL/DJT/AS/______-_________ de fecha ____/____/_______, a los fines consiguientes, por el Sistema Regional de Información NETLOSLLANOS en la ciudad de Calabozo a los '.$dia.' días del mes de '.$mes.' del año '.$año.'.';


$html2 = "Firma y Sello: Jefe División de Fiscalización";
// set font
$pdf->SetFont('times', 'B', 12);

// add a page
$pdf->AddPage();
$pag = $pdf->getAliasNumPage();

// Para colocar el HTML
ob_start();
?>
<style>
	.contenedor {
		border: black 1px solid;
	}
</style>
<?php
$pdf->SetFont('times', '', 12);
?>
	<table width="100%" align="right" border="0">
		<tr>
			<td><?php echo $sede ?></td>
		</tr>
		<tr>
			<td></td>
		</tr>
	</table>
	
<?php
				
$pdf->SetFont('times', 'B', 18);
?>

	<p align="center" style="font-size:20px">ACTOS ADMINISTRATIVO NOTIFICADOS</p>

<?php
$pdf->SetFont('times', '', 12);
?>

	<p align="justify"><?php echo $html ?></p>
<table width="100%" border="1" bordercolor="#000000" cellspacing="0" cellpadding="1" style="font-size:10px" align="center">
  <tr>
	<td width="50%" align="center"><strong>N° PROVIDENCIA ADMINISTRATIVA</strong></td>
	<td width="15%" align="center"><strong>EMISION</strong></td>
	<td width="15%" align="center"><strong>NOTIFICACIÓN</strong></td>
	<td width="19%" align="center"><strong>PROGRAMA</strong></td>
  </tr>
  
  <?php
  $sqlacta = "SELECT CONCAT(z_siglas.Siglas_resol_fis,'/', expedientes_fiscalizacion.anno,'/', a_tipo_providencia.Siglas2,'/', a_tipo_providencia.Siglas1, LPAD(expedientes_fiscalizacion.numero,4,'0')) as providencia, date_format(expedientes_fiscalizacion.fecha_emision,'%d/%m/%Y') as emision, date_format(expedientes_fiscalizacion.fecha_notificacion,'%d/%m/%Y') as notificacion, a_tipo_providencia.TipoPrograma, concat_ws(' ',expedientes_fiscalizacion.texto1,expedientes_fiscalizacion.texto2,expedientes_fiscalizacion.texto3) as texto FROM expedientes_fiscalizacion INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.fecha_notificacion IS NOT NULL AND expedientes_fiscalizacion.fecha_notificacion<>'0000/00/00' AND expedientes_fiscalizacion.rif = '".$rif."' ORDER BY expedientes_fiscalizacion.fecha_notificacion DESC";
$tabla_a = mysql_query($sqlacta);
$cantidad = mysql_num_rows($tabla_a);
while ($detalle = mysql_fetch_object($tabla_a))
{
  ?>
  <tr style="font-size:12px">
    <td align="left"><?php echo $detalle->providencia ?></td>
    <td align="center"><?php echo $detalle->emision ?></td>
    <td align="center"><?php echo $detalle->notificacion ?></td>
    <td align="center"><?php echo $detalle->TipoPrograma ?></td>
  </tr>
<?php
}
mysql_select_db('losllanos_viejo', $_SESSION['conexionsql']);
$sqlviejas = "SELECT CONCAT('SNAT/INTI/GRTI/RLL/DF/',ec_providencia.Anno_Providencia,'/',ec_tipo_autorizacion.Siglas2,'/',ec_tipo_autorizacion.Siglas1,LPAD(ec_providencia.NroAutorizacion,4,'0')) AS providencia, ec_tipo_autorizacion.Siglas1, ec_tipo_autorizacion.Siglas2, date_format(ec_providencia.FechaRegistro,'%d/%m/%Y') as emision, date_format(ec_providencia.FechaNotificacion,'%d/%m/%Y') as notificacion, ec_tipo_autorizacion.TipoPrograma, concat_ws(' ',ec_providencia.ObjetoAutorizacion,ec_providencia.Periodos) as texto FROM ec_providencia INNER JOIN ec_tipo_autorizacion ON ec_tipo_autorizacion.Tipo = ec_providencia.TipoAutorizacion WHERE ec_providencia.Anno_Providencia > 2005 AND ec_providencia.FechaNotificacion IS NOT NULL AND ec_providencia.Rif = '".$rif."' ORDER BY ec_providencia.FechaNotificacion DESC";
$tabla_v = mysql_query($sqlviejas);
$cantidad1 = mysql_num_rows($tabla_v);

while ($detallev = mysql_fetch_object($tabla_v))
{
  ?>
  <tr style="font-size:12px">
    <td align="left"><?php echo $detallev->providencia ?></td>
    <td align="center"><?php echo $detallev->emision ?></td>
    <td align="center"><?php echo $detallev->notificacion ?></td>
    <td align="center"><?php echo $detallev->TipoPrograma ?></td>
  </tr>
<?php
}

if ($cantidad == 0 and $cantidad1 == 0)
{
  ?>
  <tr style="font-size:12px">
    <td align="center" colspan="4">No Existen Actos Administrativos Notificados al Sujeto Pasivo supra indicado</td>
  </tr>
  <?php
}
		
$barcode = $pdf->serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 72, 25, 0.5, array('position'=>'S','border'=>false, 'padding'=>2, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>7, 'stretchtext'=>6), 'N'));

?>
</table>
<p align="justify"><?php echo $html1 ?></p>

<p>&nbsp;</p>
<?php
// CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
$html = ob_get_clean();
$style = array(
	'position' => '',
	'align' => 'C',
	'stretch' => false,
	'fitwidth' => true,
	'cellfitalign' => '',
	'border' => false,
	'hpadding' => 'auto',
	'vpadding' => 'auto',
	'fgcolor' => array(0,0,0),
	'bgcolor' => false, //array(255,255,255),
	'text' => true,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4
);
$style['text'] = false; // disable stretch
$pdf->write1DBarcode('NETLOSLLANOS', 'C128A', '', '', '', 18, 0.4, $style, 'N');

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->writeHTML($html2, true, false, true, false, '');
$pdf->Ln();
$pdf->SetFont('times', '', 10);

$style['text'] = true; // disable stretch
$style['fitwidth'] = false; // disable fitwidth
$style['align'] = 'R';
$pdf->write1DBarcode($rif, 'C128A', '', '', '', 15, 0.4, $style, 'N');
	
//---------CODIGO DE BARRA ----------------------------------
/*
$code_number = '125689365472365458';
#new barCodeGenrator($code_number,0,'hello.gif');
new barCodeGenrator($code_number,0,'hello.gif', 190, 130, true);	
*/
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('finiquito.pdf', 'I');

function numtoletras($xcifra)
{ 
$xarray = array(0 => "Cero",
1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", 
"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE", 
"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA", 
100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
);
//
$xcifra = trim($xcifra);
$xlength = strlen($xcifra);
$xpos_punto = strpos($xcifra, ".");
$xaux_int = $xcifra;
$xdecimales = "00";
if (!($xpos_punto === false))
   {
   if ($xpos_punto == 0)
      {
      $xcifra = "0".$xcifra;
      $xpos_punto = strpos($xcifra, ".");
      }
   $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
   $xdecimales = substr($xcifra."00", $xpos_punto + 1, 2); // obtengo los valores decimales
   }

$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
$xcadena = "";
for($xz = 0; $xz < 3; $xz++)
   {
   $xaux = substr($XAUX, $xz * 6, 6);
   $xi = 0; $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
   $xexit = true; // bandera para controlar el ciclo del While 
   while ($xexit)
      {
      if ($xi == $xlimite) // si ya llegó al límite máximo de enteros
         {
         break; // termina el ciclo
         }
   
      $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
      $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
      for ($xy = 1; $xy < 4; $xy++) // ciclo para revisar centenas, decenas y unidades, en ese orden
         {
         switch ($xy) 
            {
            case 1: // checa las centenas
               if (substr($xaux, 0, 3) < 100) // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 0, 3)]; // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                  if ($xseek)
                     {
                     $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                     if (substr($xaux, 0, 3) == 100) 
                        $xcadena = " ".$xcadena." CIEN ".$xsub;
                     else
                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                     $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                     }
                  else // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                     {
                     $xseek = $xarray[substr($xaux, 0, 1) * 100]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                     $xcadena = " ".$xcadena." ".$xseek;
                     } // ENDIF ($xseek)
                  } // ENDIF (substr($xaux, 0, 3) < 100)
               break;
            case 2: // checa las decenas (con la misma lógica que las centenas)
               if (substr($xaux, 1, 2) < 10)
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 1, 2)];
                  if ($xseek)
                     {
                     $xsub = subfijo($xaux);
                     if (substr($xaux, 1, 2) == 20)
                        $xcadena = " ".$xcadena." VEINTE ".$xsub;
                     else
                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                     $xy = 3;
                     }
                  else
                     {
                     $xseek = $xarray[substr($xaux, 1, 1) * 10];
                     if (substr($xaux, 1, 1) * 10 == 20)
                        $xcadena = " ".$xcadena." ".$xseek;
                     else  
                        $xcadena = " ".$xcadena." ".$xseek." Y ";
                     } // ENDIF ($xseek)
                  } // ENDIF (substr($xaux, 1, 2) < 10)
               break;
            case 3: // checa las unidades
               if (substr($xaux, 2, 1) < 1) // si la unidad es cero, ya no hace nada
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 2, 1)]; // obtengo directamente el valor de la unidad (del uno al nueve)
                  $xsub = subfijo($xaux);
                  $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                  } // ENDIF (substr($xaux, 2, 1) < 1)
               break;
            } // END SWITCH
         } // END FOR
         $xi = $xi + 3;
      } // ENDDO

      if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
         $xcadena.= " DE";
         
      if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
         $xcadena.= " DE";
      
      // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
      if (trim($xaux) != "")
         {
         switch ($xz)
            {
            case 0:
               if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                  $xcadena.= "UN BILLON ";
               else
                  $xcadena.= " BILLONES ";
               break;
            case 1:
               if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                  $xcadena.= "UN MILLON ";
               else
                  $xcadena.= " MILLONES ";
               break;
            case 2:
               if ($xcifra < 1 )
                  {
                  $xcadena = "CERO PESOS $xdecimales/100 CENTIMOS";
                  }
               if ($xcifra >= 1 && $xcifra < 2)
                  {
                  $xcadena = "UN PESO $xdecimales/100 CENTIMOS ";
                  }
               if ($xcifra >= 2)
                  {
                  $xcadena.= " CON $xdecimales/100 CENTIMOS "; // 
                  }
               break;
            } // endswitch ($xz)
         } // ENDIF (trim($xaux) != "")
      // ------------------      en este caso, para México se usa esta leyenda     ----------------
      $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
      $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
      $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
      $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
      $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
      $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
      $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
   } // ENDFOR ($xz)
   return trim($xcadena);
} // END FUNCTION


function subfijo($xx)
   { // esta función regresa un subfijo para la cifra
   $xx = trim($xx);
   $xstrlen = strlen($xx);
   if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
      $xsub = "";
   // 
   if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
      $xsub = "MIL";
   //
   return $xsub;
   } // END FUNCTION
   
	function calculaaño($fecha1, $fecha2, $años)
	{
		$fecha1 = strtotime($fecha1);
		$fecha1 = date("Y/m/d", $fecha1);
		$fecha2 = date("Y/m/d");
		$date2 = strtotime($fecha2);
		$date1 = strtotime($fecha1);
		$diff = abs($date2 - $date1);
		$years = floor($diff / (365*24*60*60)) + $años;
		return $years;
	}

function buscar_mes($mes)
{
	switch ($mes) {
		case 1:
			$nombre = 'enero';
			break;
		case 2:
			$nombre = 'febrero';
			break;
		case 3:
			$nombre = 'marzo';
			break;
		case 4:
			$nombre = 'abril';
			break;
		case 5:
			$nombre = 'mayo';
			break;
		case 6:
			$nombre = 'junio';
			break;
		case 7:
			$nombre = 'julio';
			break;
		case 8:
			$nombre = 'agosto';
			break;
		case 9:
			$nombre = 'septiembre';
			break;
		case 10:
			$nombre = 'octubre';
			break;
		case 11:
			$nombre = 'noviembre';
			break;
		case 12:
			$nombre = 'diciembre';
			break;
	}
	return $nombre;
}
	
//============================================================+
// END OF FILE
//============================================================+