<?php //session_start();

	//CONECTAR A LA BD
	include "conexion.php";
	include('../funciones/auxiliar_php.php');
	$id=$_GET['id'];
	$cargo = $_GET['cargo'];
	$sector = $_GET['sector'];

	$sqlacta = "SELECT * FROM ct_destruccion_facturas WHERE id=$id";
	$tablaacta = $conexionsql->query($sqlacta);
	
	$temp = $tablaacta->fetch_object();
	global $fechaACTA;
	global $sigla;
	global $adscrito;

	//BUSCAMOS LAS SIGLAS
	$sql_siglas = "SELECT Siglas_resol_fis FROM z_siglas WHERE id_sector=".$sector;
	$tabla = $conexionsql->query($sql_siglas);
	$sigla = $tabla->fetch_object();
	
	//BUSCAMOS LA ADSCRIPCION DEL FUNCIONARIO
	$adscrito_sql = "SELECT adscripcion FROM z_sectores WHERE id_sector = ".$sector;
	$tabla = $conexionsql->query($adscrito_sql);
	$ubicacion = $tabla->fetch_object();
	$adscrito = $ubicacion->adscripcion;


	$fechaACTA = $temp->fecha_emision;
	$numeroACTA = $temp->numero_acta;
	if ($temp->numero_acta!=Null)
	{
	
		require('fpdf/fpdf.php');
		
		class PDF extends FPDF
		{
			// pie de pagina
			function Footer()
			{   
				global $fechaACTA;
				global $numeroACTA;
				global $sigla;
				$this->Image('images/logocr.gif',20,15,60);
				$this->SetY(33);
				$this->SetX(22);
				//Arial itálica 8
				$this->SetFont('Times','B',10);
				$this->Cell(0,10,$sigla->Siglas_resol_fis.'/'.date("Y",strtotime($fechaACTA)).'/'.sprintf("%004s", $numeroACTA),0,1);
			}	
		}
	
	
		// ENCABEZADO
		$pdf=new PDF('P','mm','Legal'); // H
		$pdf->AliasNbPages();
		
		$pdf->Ln(8); // PARA SALTAR LINEAS
		
		$pdf->SetMargins(22,45,22);
		
		$pdf->AddPage(); // OBLIGATORIO
		
		$pdf->SetFont('Times','',12);
		
		$pdf->SetFillColor(190); // COLOR DEL FONDO
		
		//////////
		$pdf->Image('images/logocr.gif',20,15,60); // INSERTAR IMAGEN
		
		//****CONSULTAMOS EL ACTA****
		$sql_acta = "SELECT * FROM ct_destruccion_facturas WHERE id=$id";
		$tabla_acta = $conexionsql->query($sql_acta);
		$temp = $tabla_acta->fetch_object();

		$numacta=$temp->numero_acta;
		$fechaacta=$temp->fecha_emision;
		$dia = date("d",strtotime($fechaacta));
		$mes = date("m",strtotime($fechaacta));
		$cedula=$temp->ced_funcionario;
		$horasistema = $temp->hora;	
		$vaño = date("Y",strtotime($fechaacta));
		$siglas= $sigla->Siglas_resol_fis."/'".$vaño."'/'".$numacta."'";

		//***************************	
		
		//****CONSULTAMOS EL ACTA****
		/*$sqlcargo = "SELECT telefonos FROM empleados WHERE cedula=".$temp->ced_funcionario."";
		$cargo = $conexionsql->query($sqlcargo);
		$cargoF = $cargo->fetch_object();*/
		//***************************	
	
		$pdf->SetFont('Times','B',15);
		
		$pdf->Ln(10);
		//////////
		
		$pdf->SetFont('Times','B',10);
		
		$pdf->SetFont('Times','B',14);
		$pdf->Cell(0,8,'ACTA DE '.$temp->tipo_solicitud,0,1,'C',0);
		
		if ($temp->tipo_solicitud=="DESTRUCCION DE FACTURAS Y OTROS DOCUMENTOS")
		{
			$tipo = "DESTRUIR";
		}
		else
		{
			$tipo = "INUTILIZAR";
		}
		
		//$conexion = odbc_connect ("LLANOS","Administrador","escucha3205");
		
		
		//$pdf->Cell(100,8,'SSSSSSSSSSSSSSSSSSSSSSSSSSSSS:',1,1,'L',1); // AGREGA TEXTO - LAS 2 PRIMEROS VALORES ES TAMAÑO
		
		switch ($sector) {
			case 1:
				$ciudad = "Calabozo";
				$estado = "Guárico";
				$sede = "de la Gerencia Regional de Tributos Internos";
				break;
			case 2:
				$ciudad = "San Juan de los Morros";
				$estado = "Guárico";
				$sede = "del Sector San Juan de los Morros";
				break;
			case 3:
				$ciudad = "San Fernando de Apure";
				$estado = "Apure";
				$sede = "del Sector San Fernando de Apure";
				break;
			case 4:
				$ciudad = "Altagracia de Orituco";
				$estado = "Guárico";
				$sede = "de la Unidad Altagracia de Orituco";
				break;
			case 5:
				$ciudad = "Valle de la Pascua";
				$estado = "Guárico";
				$sede = "del Sector Valle de la Pascua";
				break;
		}

		// //BUSCAMOS EL CARGO
		// $cadena_de_texto = $cargo;
		// //echo $cadena_de_texto;
		// $cadena_buscada   = 'ADMINISTRATIVO';
		// $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
		 
		// //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
		// if ($posicion_coincidencia === false) 
		// {
		// 	//echo "1...".$cadena_de_texto;
		// 	$cargos = $cargo." TRIBUTARIO";
		// } 
		
		$parrafo1 = 'En '.$ciudad.' Estado '.$estado.', a los '.numtoletras($dia).' ('.$dia.') días del mes de '.mesletra($mes).' del '.numtoletras($vaño).', a las '.$horasistema.', el (los) funcionario(s) '.$temp->nom_funcionario.', titular(es) de la(s) cédula de identidad Nº '.number_format($temp->ced_funcionario, 0, '', '.').', con el cargo de '.$cargo.', adscrito(s) '.$adscrito.' de la Gerencia Regional de Tributos Internos de la Región Los Llanos del Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT), constituido(s) en la sede '.$sede.' y presente el(la) ciudadano(a): '.$temp->rep_contribuyente.', titular de la cédula de identidad Nº '.number_format($temp->ced_rep_contribuyente, 0, '', '.').', en su carácter de '.tipopersona($temp->persona_responsable).' la contribuyente: '.$temp->nombre.', inscrito(a) en el Registro Único de Información Fiscal, bajo el Nº '.formato_rif($temp->rif).', con el fin de proceder a la '.$temp->tipo_solicitud.' que se identifican a continuación, en virtud de la solicitud identificada con el Nº '.$temp->num_solicitud.'  de fecha '.date("d-m-Y",strtotime($temp->fecha_solicitud)).'.';
		
		$parrafo2 = 'De conformidad con lo establecido en la Providencia Nº SNAT/2011/00071 de fecha 08/11/2011 Publicada en Gaceta Oficial 39.795 de fecha 08/11/2011, mediante la cual se dictan las Normas Generales de Emisión de Facturas y Otros Documentos, se deja constancia de la destrucción efectuada.';
		
		$parrafo3 = 'A los fines legales consiguientes, se emite la presente Acta en dos (2) ejemplares de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del contribuyente o responsable, quien firma en señal de notificación.';
	
		$pdf->Ln(8);
		
		$pdf->SetFont('Arial','',12);
		$pdf->MultiCell(0,5,utf8_decode($parrafo1),0,'J',0);
		
		//DOCUMENTOS A DESTRUIR O INUTILIZAR
		$pdf->SetFont('Arial','B',10);
		$pdf->Ln(5);
		$pdf->Cell(171,5,'DOCUMENTOS A '.$tipo,1,0,'C',1);
		$pdf->Ln(5);
		$pdf->Cell(71,5,'TIPO DE DOCUMENTO',1,0,'C',1);
		$pdf->Cell(50,5,utf8_decode('N° CONTROL INICIAL'),1,0,'C',1);
		$pdf->Cell(50,5,utf8_decode('N° CONTROL FINAL'),1,0,'C',1);
		$pdf->SetFont('Arial','',12);
	
		//****DOCUMENTOS DEL ACTA****
		$sqldoc = "SELECT * FROM ct_doc_destFacturas WHERE numero_acta=".$numacta." and fecha_emision='".$fechaacta."' and sector=".$sector;
		$documentos = $conexionsql->query($sqldoc);
		//***************************	
		
		while ($doc = $documentos->fetch_object())
		{
			$pdf->Ln(5);
			$pdf->Cell(71,5,$doc->tipo_documento,1,0,'L',0);
			$pdf->Cell(50,5,sprintf("%008s", number_format($doc->control_inicial,0,'','')),1,0,'C',0);
			$pdf->Cell(50,5,sprintf("%008s", number_format($doc->control_final,0,'','')),1,0,'C',0);
		}
	
		$pdf->Ln(5);
		$pdf->Ln(8);
		$pdf->MultiCell(0,5,utf8_decode($parrafo2),0,'J',0);
		
		$pdf->Ln(8);
		$pdf->MultiCell(0,5,utf8_decode($parrafo3),0,'J',0);
		$pdf->Ln(8);
		
		$pdf->Cell(70,5,'Por el Sujeto Pasivo:',1,0,'L',0);
		$pdf->Cell(31,5,'',0,0,'C',0);
		$pdf->Cell(70,5,'Funcionario Actuante:',1,0,'L',0);
		$pdf->Ln(8);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(70,5,'Nombres y Apellidos:',1,0,'L',0);
		$pdf->Cell(31,5,'',0,0,'C',0);
		$pdf->Cell(70,5,'Nombres y Apellidos: '.$temp->nom_funcionario,1,0,'L',0);
		$pdf->Ln(5);
		$pdf->Cell(70,5,utf8_decode('C. I. Nº:'),1,0,'L',0);
		$pdf->Cell(31,5,'',0,0,'C',0);
		$pdf->Cell(70,5,utf8_decode('C. I. Nº: '.number_format($temp->ced_funcionario, 0, '', '.')),1,0,'L',0);
		$pdf->Ln(5);
		$pdf->Cell(70,5,'Cargo:',1,0,'L',0);
		$pdf->Cell(31,5,'',0,0,'C',0);
		$pdf->Cell(70,5,'Cargo: '.$cargo,1,0,'L',0);
		$pdf->Ln(5);
		$pdf->Cell(70,5,'Fecha:',1,0,'L',0);
		$pdf->Cell(31,5,'',0,0,'C',0);
		$pdf->Cell(70,5,'Firma:',1,0,'L',0);
		$pdf->Ln(5);
		$pdf->Cell(70,5,'Hora:',1,0,'L',0);
		$pdf->Ln(5);
		$pdf->Cell(70,5,utf8_decode('Teléfono:'),1,0,'L',0);
		$pdf->Ln(5);
		$pdf->Cell(70,5,'Firma:',1,0,'L',0);
		$pdf->Ln(5);
		$pdf->Cell(70,5,'Sello:',1,0,'L',0);
		
		// CUADRO ACTAS DE REPARO
		$pdf->Ln(8);
		
		// FIN DE LA VALIDACION DE LA CONSULTA
		
		$pdf->Output(); // OBLIGATORIO PARA IMPRIMIR
	}
	else
	{
		print '<script language="javascript">alert("El Acta No ha Sido Generada, por favor generela y vuelva a intentarlo.");</script>';
		print '<script language="javascript">window.close();</script>';
	}
	
function tipopersona($tipopp)
{
	switch ($tipopp)
	{
		case 0:
			$persona="Representante Legal de";
			break;
		case 1:
			$persona="Persona Autorizada por";
			break;
	}
	return $persona;
}
	
function mesletra($nummes)
{
	switch ($nummes)
	{
		case 1:
			$expmes="ENERO";
			break;
		case 2:
			$expmes="FEBRERO";
			break;
		case 3:
			$expmes="MARZO";
			break;
		case 4:
			$expmes="ABRIL";
			break;
		case 5:
			$expmes="MAYO";
			break;
		case 6:
			$expmes="JUNIO";
			break;
		case 7:
			$expmes="JULIO";
			break;
		case 8:
			$expmes="AGOSTO";
			break;
		case 9:
			$expmes="SEPTIEMBRE";
			break;
		case 10:
			$expmes="OCTUBRE";
			break;
		case 11:
			$expmes="NOVIEMBRE";
			break;
		case 12:
			$expmes="DICIEMBRE";
			break;
	}
   return $expmes;
}
	
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
            /*case 2:
               if ($xcifra < 1 )
                  {
                  $xcadena = "CERO PESOS $xdecimales/100 M.N.";
                  }
               if ($xcifra >= 1 && $xcifra < 2)
                  {
                  $xcadena = "UN PESO $xdecimales/100 M.N. ";
                  }
               if ($xcifra >= 2)
                  {
                  $xcadena.= " PESOS $xdecimales/100 M.N. "; // 
                  }
               break;*/
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

function BuscarCargo($cargo)
{
	$cadena_de_texto = $cargo;
	//echo $cadena_de_texto;
	$cadena_buscada   = 'ADMINISTRATIVO';
	$posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
	 
	//se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
	if ($posicion_coincidencia === false) 
	{
		//echo "1...".$cadena_de_texto;
		echo $cargo." TRIBUTARIO";
	} 
	else 
	{
		//echo "2...".$cadena_de_texto;
	    return $cargo;
	}	 
}

?>
