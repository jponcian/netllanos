<?php	

//---------------
$pdf->SetFont('Times','',7);
//---------------
//$linea_muestra = 1;

$pdf->SetXY(17,221);
$pdf->MultiCell(0,3,'    EN CASO DE INCONFORMIDAD CON EL PRESENTE ACTO ADMINISTRATIVO PODRA EJERCER LOS RECURSOS QUE CONSAGRA EL CODIGO ORGANICO TRIBUTARIO EN SUS ARTICULOS 242 Y 259, DENTRO DE LOS PLAZOS PREVISTOS EN LOS ARTICULOS 244 Y 261 EJUSDEM, PARA LO CUAL DEBERA DARSE CUMPLIMIENTO A LO PREVISTO EN LOS ARTICULOS 243 Y 262 DEL CITADO CODIGO.',$linea_muestra,'J');

//---------------
$pdf->SetFont('Times','',6.4);
//---------------
$alto = 3;
$x = 17; 
$y = 231; 

$pdf->SetXY($x,$y);
$pdf->Cell(70,$alto,'RESOLUCION(ES) ANEXA(S) A ESTA PLANILLA DE LIQUIDACION',$linea_fija,0,'L',1);
$pdf->Cell(17,$alto,'FECHA',$linea_fija,0,'C',1);

$x = $pdf->GetX()+23; 
$pdf->MultiCell(23,$alto,'IDENTIFICACIÓN DEL PAGO:',$linea_fija,0,'C',1);

$pdf->SetXY($x,$y);
$pdf->MultiCell(0,$alto,'PAGUE EL (LOS) SIGUIENTES MONTO(S) MEDIANTE LA(S) PLANILLA(S) PARA PAGAR ANEXA(S)',$linea_fija,'J');
$x = $pdf->GetX(); 
$y = $pdf->GetY()-$alto; 

$pdf->SetXY($x,$y);
$pdf->Cell(70,$alto,$sigla_resolucion,$linea_fija,'L');
$pdf->Cell(17,$alto,voltea_fecha($fecha_resolucion),$linea_fija,0,'C');

$y = $pdf->GetY()+$alto; 

$pdf->SetXY($x,$y);
$pdf->Cell(87,$alto,'',$linea_muestra,0,'L');
$pdf->Cell(12,$alto,'PORCION',$linea_fija,0,'C',1);
$pdf->Cell(25,$alto,'FECHA VENCIMIENTO',$linea_fija,0,'C',1);
$pdf->Cell(20,$alto,'MONTO Bs.',$linea_fija,0,'C',1);
$pdf->Cell(0,$alto,'PLAN UNICO DE CUENTA',$linea_fija,0,'C',1);

$pdf->ln();

$pdf->Cell(87,$alto,'',$linea_muestra,'L');
$pdf->Cell(12,$alto,'01',$linea_fija,0,'C');
$pdf->Cell(25,$alto,'INMEDIATO',$linea_fija,0,'C');
$pdf->Cell(20,$alto,formato_moneda($registro->monto_bs/$registro->concurrencia*$registro->especial),$linea_fija,0,'C');
$pdf->Cell(0,$alto,$registro->cuenta,$linea_fija,0,'C');


//---------------
$pdf->SetFont('Times','',6);
//---------------

$pdf->SetXY(17,262);
$pdf->MultiCell(85,2.5,'    EN MI CARACTER DE CONTRIBUYENTE O REPRESENTANTE CERTIFICO QUE HE RECIBIDO LOS DOCUMENTOS MENCIONADOS EN ESTE ACTO ADMINISTRATIVO.',$linea_muestra,'J');

//---------------

$cuadro = 30;
$alto = 3;
$x = 30; 
$y = 250; 

$pdf->SetFont('Times','B',8);
//---------------------------------

$pdf->SetXY($x,$y);
$pdf->Cell($cuadro,$alto,'_________________',$linea_muestra,0,'C');

$pdf->Cell($cuadro,$alto,'_________________',$linea_muestra,0,'C');

$pdf->SetXY($x,$y+4);
$pdf->Cell($cuadro,$alto,'FIRMA',$linea_muestra,0,'C');

$pdf->Cell($cuadro,$alto,'C.I. N°',$linea_muestra,0,'C');

$pdf->SetFont('Times','',5);
//---------------------------------

$pdf->SetXY($x,$y+8);
$pdf->Cell($cuadro*2,$alto,'ORIGINAL: CONTRIBUYENTE',$linea_muestra,0,'C');

// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR DEPENDE DEL ORIGEN

if ($_SESSION['SEDE']==1)
	{
	 list ($funcionario, $cargo1, $cargo2, $division) = funcion_funcionario(0+$_SESSION['CEDULA_USUARIO']);
	
	 if ($division==6)
			{
			$consulta_x = "SELECT * FROM vista_jefe_fis_gerente WHERE id_sector=1;";
			} 
	}		
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$JEFE = $registro_x->jefe;
$CEDULA =  "C.I. N° V-" .formato_cedula($registro_x->cedula);
$CARGO = utf8_decode($registro_x->cargo);
$PROVIDENCIA = utf8_decode($registro_x->providencia);
$fecha_prov = $registro_x->fecha_prov;
$gaceta = utf8_decode($registro_x->gaceta);
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = $registro_x->descripcion;
//---------------------------------

$cuadro = 90;
$alto = 2.5;
$x = 120; 
$y = 245; 

$pdf->SetFont('Times','B',8);
//---------------------------------
$pdf->SetXY($x,$y);
$pdf->MultiCell($cuadro,$alto,'Firma Autorizada',$linea_muestra,'C');

$pdf->SetFont('Times','B',8);
//---------------------------------
$y = $y+$alto+6;
$pdf->SetXY($x,$y+$alto);
$pdf->Cell($cuadro,$alto,$JEFE,$linea_muestra,0,'C');

$pdf->SetFont('Times','',5);
//---------------------------------
$y = $y+$alto;
$pdf->SetXY($x,$y+$alto);
$pdf->Cell($cuadro,$alto,$CEDULA,$linea_muestra,0,'C');

$y = $y+$alto;
$pdf->SetXY($x,$y+$alto);
$pdf->Cell($cuadro,$alto,$CARGO,$linea_muestra,0,'C');

$y = $y+$alto;
$pdf->SetXY($x,$y+$alto);
$pdf->Cell($cuadro,$alto,"Región Los Llanos",$linea_muestra,0,'C');

$y = $y+$alto;
$pdf->SetXY($x,$y+$alto);
$pdf->Cell($cuadro,$alto,$PROVIDENCIA,$linea_muestra,0,'C');

$y = $y+$alto;
$pdf->SetXY($x,$y+$alto);
$pdf->Cell($cuadro,$alto,'de fecha '.$fecha_prov,$linea_muestra,0,'C');	

				
?>