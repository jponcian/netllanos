<?php
//CONECTAR A LA BD
	include "../../conexion.php";
	mysql_query("SET NAMES 'utf8'");
	include "../../funciones/auxiliar_php.php";
	$anno=$_GET['anno'];
	$num = $_GET['num'];
	$sector = $_GET['sec'];
	
// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
		$anno=$_GET['anno'];
		$num = $_GET['num'];
		$sector = $_GET['sec'];
		$consulta = "SELECT Siglas_resol_fis, numero, anno, Siglas2, Siglas1 FROM vista_providencias WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector;
		$tabla_x = mysql_query($consulta);
		$sigla = mysql_fetch_object($tabla_x);
		$SIGLAS=$sigla->Siglas_resol_fis.'/'.$sigla->anno.'/'.$sigla->Siglas2."/".$sigla->Siglas1.sprintf("%004s", $sigla->numero).'/_____';
		// ---------------------
		////////// DATOS DE LA RESOLUCION
		$Providencia = $SIGLAS;
    	$html = '
		<div>
			<table witch="100%" border="0"><tr>
				<td>
				<img src="logo_png.png" width="204" height="69" />
				</td>
			</tr>
			<tr>
				<td></td>
			</tr></table>
		</div>
    	';
    	$this->writeHTML($html, true, false, true, false, '');
		$this->SetFont('times', '', 10);

    }

    // Page footer
    public function Footer() {
		$anno=$_GET['anno'];
		$num = $_GET['num'];
		$sector = $_GET['sec'];
		$consulta = "SELECT Siglas_resol_fis, numero, anno, Siglas2, Siglas1 FROM vista_providencias WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector;
		$tabla_x = mysql_query($consulta);
		$sigla = mysql_fetch_object($tabla_x);
		$SIGLAS=$sigla->Siglas_resol_fis.'/'.$sigla->anno.'/'.$sigla->Siglas2."/".$sigla->Siglas1.sprintf("%004s", $sigla->numero).'/_____';
		// ---------------------
		////////// DATOS DE LA RESOLUCION
		$Providencia = $SIGLAS;
    	$html = '
		<div>
			<table witch="100%" border="0"><tr>
				<td align="left">'.$Providencia.'</td>
				<td align="right">1 / 1</td>
			</tr></table>
		</div>
    	';
		$this->SetFont('times', '', 10);
    	$this->writeHTML($html, true, false, true, false, '');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Gustavo Garcia');
$pdf->SetTitle('FINIQUITO');
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
if ($sector == 1)
{
	$filtro = "z_jefes_detalle.id_sector = ".$sector." AND z_jefes_detalle.division = 6";
}
else
{
	$filtro = "z_jefes_detalle.id_sector = ".$sector;
}

$sqljefe = "SELECT z_jefes_detalle.id_sector, z_jefes_detalle.jefe, z_jefes_detalle.cargo, z_jefes_detalle.providencia, date_format(z_jefes_detalle.fecha_prov, '%d-%m-%Y') as fecha_prov, z_jefes_detalle.fecha_not, z_sectores.nombre FROM z_jefes_detalle INNER JOIN z_sectores ON z_sectores.id_sector = z_jefes_detalle.id_sector WHERE ".$filtro;
//echo $sqljefe;
$tabla_jefe = mysql_query($sqljefe);
$jefe = mysql_fetch_object($tabla_jefe);

$sede = $jefe->nombre;
$nombre_jefe = $jefe->jefe;
$cargo_jefe = $jefe->cargo;
$providencia_jefe = str_replace('Providencia ', '', $jefe->providencia);
$fecha_prov_jefe = "de fecha ".$jefe->fecha_prov;

// ---------------------------------------------------------
$consulta = "SELECT Siglas_resol_fis, numero, anno, Siglas2, Siglas1 FROM vista_providencias WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector;
$tabla_x = mysql_query($consulta);
$sigla = mysql_fetch_object($tabla_x);
$SIGLAS=$sigla->Siglas_resol_fis.'/'.$sigla->anno.'/'.$sigla->Siglas2."/".$sigla->Siglas1.sprintf("%004s", $sigla->numero);

$sql_acta = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector, a_tipo_providencia.Siglas1,  expedientes_fiscalizacion.fecha_conclusion, a_tipo_providencia.Siglas2, vista_contribuyentes_direccion.contribuyente, vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.direccion, fis_actas.id_acta, fis_actas.anno as anno_acta, fis_actas.numero as num_acta, fis_actas_detalle.COT, fis_actas.fecha as emision_acta, fis_actas.fecha_notificacion, sum(fis_actas_detalle.impuesto_omitido) as impuesto FROM expedientes_fiscalizacion INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_fiscalizacion.rif INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo WHERE expedientes_fiscalizacion.sector = ".$sector." AND expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$num." GROUP BY expedientes_fiscalizacion.sector, expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, fis_actas.id_acta";
//echo $sql_acta;

$tabla_acta = mysql_query($sql_acta);
$temp = mysql_fetch_object($tabla_acta);

$idacta = $temp->id_acta;
$sede = $sede.', '.voltea_fecha($temp->fecha_conclusion);
$año_indepencia= calculaaño('05/07/1970', $temp->fecha_conclusion, 159).'°';
$año_federacion= calculaaño('05/07/1970', $temp->fecha_conclusion, 110).'°';
$monto = $temp->impuesto;
$monto_letras = numtoletras($monto);
$acta_reparo = $SIGLAS."/".$temp->anno_acta."/".sprintf("%004s", $temp->num_acta);

if ($temp->COT == '112' or $temp->COT == '114#1' or $temp->COT == '114#2' or $temp->COT == '115#1' or $temp->COT == '115#2' or $temp->COT == '115#3' or $temp->COT == '115#4')
{
	 $art1 = '196';
	 $art2 = '198';
	 $añoCOT = 2020;
}
else
{
	 $art1 = '186';
	 $art2 = '188';
	 $añoCOT = 2001;
}


$html = 'Se procede a realizar el presente finiquito a el(la) Contribuyente: <b>'.$temp->contribuyente.'</b>, registrado en el Registro de Información Fiscal (RIF) N°: <b>'.formato_rif($temp->rif).'</b>, Domiciliado en: <b>'.$temp->direccion.'</b>, de acuerdo al reparo formulado por las causas y motivos expuestas en el Acta de Reparo N° <b>'.$acta_reparo.'</b>, de fecha <b>'.voltea_fecha($temp->emision_acta).'</b> y notificada en fecha <b>'.voltea_fecha($temp->fecha_notificacion).'</b>; por el monto de Bolívares: <b>'.$monto_letras.' (Bs. '.number_format($monto, 2, ',', '.').')</b>, donde el (la) contribuyente se allanó parcialmente aceptando una parte del mismo, por lo cual la administración tributaria procede a elaborar el presente finiquito, procediendo al cálculo de la(s) multa(s) y de los intereses moratorios de acuerdo a lo estipulado en el artículo '.$art1.' Primer Aparte Parágrafo Único del Código Orgánico Tributario del '.$añoCOT.', procediendo su remisíón a Sumario Administrativo por el monto no allanado de acuerdo con lo establecido en al Artículo '.$art2.' del Código Orgánico Tributario del '.$añoCOT.'.';

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
$pdf->SetFont('times', '', 10);
?>
	<table width="100%" align="right" border="0">
		<tr>
			<td><?php echo $sede ?></td>
		</tr>
		<tr>
			<td><?php echo $año_indepencia ?></td>
		</tr>
		<tr>
			<td><?php echo $año_federacion ?></td>
		</tr>
	</table>
	
<?php
$pdf->SetFont('times', 'B', 14);
?>
	<p align="center" style="font-size:24px">FINIQUITO</p>
<?php
$pdf->SetFont('times', '', 10.5);
?>

	<p align="justify"><?php echo $html ?></p>
	<p align="justify">Seguidamente se muestra el detalle del finiquito otorgado a la contribuyente antes mencionado.</p>
<table width="100%" border="1" bordercolor="#000000" cellspacing="0" cellpadding="1" style="font-size:8px">
  <tr>
	<td align="center"><strong>N° DE DECLARACION</strong></td>
	<td align="center"><strong>FECHA DE CANCELACIÓN</strong></td>
	<td align="center"><strong>EJERCICIO O PERÍODO</strong></td>
	<td align="center"><strong>TIPO DE TRIBUTO</strong></td>
	<td align="center"><strong>MONTO NO CANCELADO Bs</strong></td>
	<td align="center"><strong>MONTO CANCELADO Bs</strong></td>
	<td align="center"><strong>MULTA Bs</strong></td>
	<td align="center"><strong>INTERESES MORATORIOS Bs</strong></td>
  </tr>
  
  <?php
  $sqlacta = "SELECT fis_actas_detalle.planilla, fis_actas_detalle.fecha_pago, CONCAT_WS(' AL ',date_format(fis_actas_detalle.periodo_desde, '%d/%m/%Y'),date_format(fis_actas_detalle.periodo_hasta, '%d/%m/%Y')) AS periodo, a_tributos.siglas as tributo, (fis_actas_detalle.impuesto_omitido - fis_actas_detalle.monto_pagado) AS no_pagado, fis_actas_detalle.monto_pagado, fis_actas_detalle.multa_actual, fis_actas_detalle.interes FROM fis_actas_detalle INNER JOIN a_tributos ON a_tributos.id_tributo = fis_actas_detalle.tributo WHERE fis_actas_detalle.id_acta = ".$idacta;
$tabla_a = mysql_query($sqlacta);
while ($detalle = mysql_fetch_object($tabla_a))
{
  ?>
  <tr>
    <td align="center"><?php echo $detalle->planilla ?></td>
    <td align="center"><?php echo voltea_fecha($detalle->fecha_pago) ?></td>
    <td align="center"><?php echo $detalle->periodo ?></td>
    <td align="center"><?php echo $detalle->tributo ?></td>
    <td align="right"><?php echo formato_moneda($detalle->no_pagado) ?></td>
    <td align="right"><?php echo formato_moneda($detalle->monto_pagado) ?></td>
    <td align="right"><?php echo formato_moneda($detalle->multa_actual) ?></td>
    <td align="right"><?php echo formato_moneda($detalle->interes) ?></td>
  </tr>
<?php
}
?>
</table>
<p>Se otorga el presente finiquito, a los fines legales consiguientes.</p>
<p>&nbsp;</p>
<div align="center">
	<table width="100%" align="center" border="0">
		<tr>
			<td><b><?php echo $nombre_jefe ?></b></td>
		</tr>
		<tr style="font-size:13px">
			<td><?php echo $cargo_jefe ?></td>
		</tr>
		<tr style="font-size:13px">
			<td>Providencia Administrativa</td>
		</tr>
		<tr style="font-size:13px">
			<td><?php echo $providencia_jefe ?></td>
		</tr>
		<tr style="font-size:13px">
			<td><?php echo $fecha_prov_jefe ?></td>
		</tr>
</div>
<?php

$html = ob_get_clean();

$pdf->writeHTML($html, true, false, true, false, '');

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
//============================================================+
// END OF FILE
//============================================================+