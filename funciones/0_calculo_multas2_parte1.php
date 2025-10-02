<?php	
$_SESSION['VALOR_UT_PRIMITIVA'] = 0;
//--------------
if ($_POST['OESPECIAL']<3) {$_POST['OESPECIAL']=1;}
//--------------
 
$ut_aplicadas = 0;
$monto = 0;
$multa_diaria = 0;
$ut_aplicada_diaria = 0;
$reiteracion = 1;
$id_resolucion = '';
$Dias = 0;
	
//xxxxxxxxxx	PORCENTAJE MENSUAL
if ($aplicacion==3 or $aplicacion==11 or $aplicacion==18)
	{
	if ($aplicacion==3)		{	$porcentaje = 1.5;	}
	if ($aplicacion==11)	{	$porcentaje = 50;	}	
	if ($aplicacion==18)	{	$porcentaje = 50;	}	

	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	$_SESSION['VALOR_UT_PRIMITIVA'] = unidad_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx

	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($_POST['OVENCIMIENTO']));
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($_POST['OPAGO']));
	//--------------
	$totaldias = ($FechaPago-$FechaVen)/86400;
	
	//
	while (($Dias<$totaldias) and ($Dias<=300))
		{
		$multa_diaria = (($porcentaje*$_POST['OMONTO']/100)/30);
		$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;	
		//------------------
		$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
		//------------------
		$Dias++;
		}
	//---------------
	}
	
//xxxxxxxxxx	POR FACTURA O REITERACION
if ($aplicacion==2 or $aplicacion==9 or $aplicacion==14)
	{
	if ((($sancion<836) or ($sancion>=1550)) and $aplicacion==14)
		{
		$ut_aplicadas=($ut_min * $_POST['OFACTURAS']);
		if ($ut_aplicadas>$ut_max) 
			{$ut_aplicadas=$ut_max; }
		}
	else
		{
		// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
		$_SESSION['VALOR_UT_PRIMITIVA'] = unidad_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
		// xxxxxxxxxx
		// REVISION DE LAS MULTAS ANTERIORES
		$consulta3 = "SELECT id_resolucion FROM vista_resoluciones_x_liquidacion WHERE (origen_liquidacion<>0".$_SESSION['ORIGEN']." AND anno_expediente<>".$_SESSION['ANNO_PRO']." AND num_expediente<>".$_SESSION['NUM_PRO']." AND sector<>".$_SESSION['SEDE_USUARIO'].") AND rif='". $rif ."' AND id_sancion=".$sancion." GROUP BY sector, origen_liquidacion, anno_expediente, num_expediente;";
		$tabla3 = mysql_query ($consulta3);
		if ($registro3 = mysql_fetch_object($tabla3))
			{
			$id_resolucion = $registro3->id_resolucion . '/' . $id_resolucion;
			//REVISA LA CANTIDAD DE INFRACCIONES
				while ($registro3 = mysql_fetch_object($tabla3))
				{
				$reiteracion++;
				}
			$reiteracion++;
			}
		//------------------
		$ut_aplicadas = ($ut_min * $reiteracion);
		if ($ut_aplicadas > $ut_max) { $ut_aplicadas = $ut_max; }
		//------------------
		}	
	}


//xxxxxxxxxx	PORCENTUAL
if ($aplicacion==10 or $aplicacion==12 or $aplicacion==53)
	{
	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	$_SESSION['VALOR_UT_PRIMITIVA'] = unidad_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx
	$monto = (($_POST['OPORCENTAJE']*$_POST['OMONTO'])/100);
	$ut_aplicadas = $monto / $_SESSION['VALOR_UT_PRIMITIVA']  ;
	//---------------
	}
	
//xxxxxxxxxx	TERMINO MEDIO
if (($aplicacion==13) or ($aplicacion==15) or ($aplicacion==52) or ($aplicacion==152))
	{
	//---------------
	$ut_aplicadas=($ut_min + $ut_max)/2;
	//---------------
	}				

//xxxxxxxxxx	UT MAXIMA	
//if ($aplicacion==50 or ($aplicacion==150))	alex sustituir				
if ($aplicacion==50 or ($aplicacion==150) or ($aplicacion==200))
	{
	// UNIDADES A APLICAR
	$ut_aplicadas = $ut_max;
	}

//xxxxxxxxxx	PORCENTUAL 1 AÑO 2 AÑOS O 3 AÑOS
if ($aplicacion==51)
	{
	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	$_SESSION['VALOR_UT_PRIMITIVA'] = unidad_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx
	
	// FECHA DE VENCIMIENTO
	list($dia,$mes,$anno)=explode('/',$_POST['OVENCIMIENTO']);
	$FechaVen = mktime(0,0,0,$mes,$dia,$anno); 
	
	// 1 AÑO MAS
	list($dia,$mes,$anno)=explode('/',$_POST['OVENCIMIENTO']);
	$FechaVen1 = mktime(0,0,0,$mes,$dia,$anno+1); 
	
	// 2 AÑOS MAS
	list($dia,$mes,$anno)=explode('/',$_POST['OVENCIMIENTO']);
	$FechaVen2 = mktime(0,0,0,$mes,$dia,$anno+2); 
	
	// FECHA DE PAGO
	list($dia,$mes,$anno)=explode('/',$_POST['OPAGO']);
	$FechaPago = mktime(0,0,0,$mes,$dia,$anno);
	
	//--------------
	$Dias = ($FechaPago - $FechaVen) / 86400 ;
	$FechaVen = $FechaVen + 86400;
	
	// SI PAGO 2 AÑOS DESPUES DE LA FECHA DE VENCIMIENTO
	if ($FechaPago >= $FechaVen2)
		{
		$multa_diaria = ($_POST['OMONTO']*300)/100;
		$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;
		//------------------
		$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
		//------------------
		}
	else
		{
		// SI PAGO 1 AÑO DESPUES DE LA FECHA DE VENCIMIENTO
		if ($FechaPago >= $FechaVen1)
			{
			$multa_diaria = ($_POST['OMONTO']*150)/100;
			$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;
			//------------------
			$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
			//------------------
			}
		else
			{
			// SI PAGO ANTES DE 1 AÑO DESPUES DE LA FECHA DE VENCIMIENTO
			if (($FechaVen <= $FechaPago))
				{
				$porcentaje = $Dias*0.28;
				$multa_diaria = ($_POST['OMONTO']*($Dias*0.28))/100;
				$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA'];
				//------------------
				$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
				//------------------
				}
			// SI PASO EL 100%
			if ($porcentaje>=100)
				{
				$multa_diaria = ($_POST['OMONTO']*100)/100;
				$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;
				//------------------
				$ut_aplicadas = $ut_aplicada_diaria;
				//------------------
				}
			}
		}
	}


//xxxxxxxxxx	PORCENTAJE DIARIO
if ($aplicacion==54)
	{
	$porcentaje = 0.05;

	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	   
	$_SESSION['VALOR_UT_PRIMITIVA'] = unidad_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx

	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($_POST['OVENCIMIENTO']));
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($_POST['OPAGO']));
	//--------------
	$totaldias = ($FechaPago-$FechaVen)/86400;
	
	//
	
	while (($Dias<$totaldias) and ($Dias<=100))
		{
		$multa_diaria = (($porcentaje*$_POST['OMONTO']));
		$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA'];	
		//------------------
		$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
		//------------------
		
		$Dias++;
		
		}
		/*codigo nuevo
		$Dias = $totaldias;
		$ut_aplicadas = (($totaldias*$porcentaje)*$_POST['OMONTO'])/ $_SESSION['VALOR_UT_PRIMITIVA'];
		//$ut_aplicadas = 6.57 / $_SESSION['VALOR_UT_PRIMITIVA'];
		
		echo "hola mundo: ";
		echo $ut_aplicadas;
		
		//--
		*/
	//--------------- 
	}


//---------------------------------------- NUEVAS
//xxxxxxxxxx	PORCENTUAL 1 AÑO 2 AÑOS O 3 AÑOS
if ($aplicacion==151)
	{
	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	$_SESSION['VALOR_UT_PRIMITIVA'] = moneda_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx
	
	// FECHA DE VENCIMIENTO
	list($dia,$mes,$anno)=explode('/',$_POST['OVENCIMIENTO']);
	$FechaVen = mktime(0,0,0,$mes,$dia,$anno); 
	
	// 1 AÑO MAS
	list($dia,$mes,$anno)=explode('/',$_POST['OVENCIMIENTO']);
	$FechaVen1 = mktime(0,0,0,$mes,$dia,$anno+1); 
	
	// 2 AÑOS MAS
	list($dia,$mes,$anno)=explode('/',$_POST['OVENCIMIENTO']);
	$FechaVen2 = mktime(0,0,0,$mes,$dia,$anno+2); 
	
	// FECHA DE PAGO
	list($dia,$mes,$anno)=explode('/',$_POST['OPAGO']);
	$FechaPago = mktime(0,0,0,$mes,$dia,$anno);
	
	//--------------
	$Dias = ($FechaPago - $FechaVen) / 86400 ;
	$FechaVen = $FechaVen + 86400;
	
	// SI PAGO 2 AÑOS DESPUES DE LA FECHA DE VENCIMIENTO
	if ($FechaPago >= $FechaVen2)
		{
		$multa_diaria = ($_POST['OMONTO']*300)/100;
		$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;
		//------------------
		$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
		//------------------
		//echo '<script>alert("'.moneda_infraccion(voltea_fecha($_POST['OVENCIMIENTO'])).'");</script>';

		}
	else
		{
		// SI PAGO 1 AÑO DESPUES DE LA FECHA DE VENCIMIENTO
		if ($FechaPago >= $FechaVen1)
			{
			$multa_diaria = ($_POST['OMONTO']*150)/100;
			$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;
			//------------------
			$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
			//------------------
			}
		else
			{
			// SI PAGO ANTES DE 1 AÑO DESPUES DE LA FECHA DE VENCIMIENTO
			if (($FechaVen <= $FechaPago))
				{
				$porcentaje = $Dias*0.28;
				$multa_diaria = ($_POST['OMONTO']*($Dias*0.28))/100;
				$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA'];
				//------------------
				$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
				//------------------
				
				}
			// SI PASO EL 100%
			if ($porcentaje>=100)
				{
				$multa_diaria = ($_POST['OMONTO']*100)/100;
				$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;
				//------------------
				$ut_aplicadas = $ut_aplicada_diaria;
				//------------------
				}
			}
		}
	}
//xxxxxxxxxx	PORCENTUAL
if ($aplicacion==153)
	{
	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	$_SESSION['VALOR_UT_PRIMITIVA'] = moneda_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx
	$monto = (($_POST['OPORCENTAJE']*$_POST['OMONTO'])/100);
	$ut_aplicadas = $monto / $_SESSION['VALOR_UT_PRIMITIVA']  ;
	//---------------
	}
//xxxxxxxxxx	PORCENTAJE DIARIO  PARA ANTICIPO DEL ISLR  ***OJO TAL CONDICION DEBO APLICAR PARA RETENCIONES ES 0.05
if ($aplicacion==154)
	{
	//$porcentaje = 0.0005;
	$porcentaje = 0.0005; //114#2

	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	$_SESSION['VALOR_UT_PRIMITIVA'] = moneda_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx

	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($_POST['OVENCIMIENTO']));
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($_POST['OPAGO']));
	//--------------
	$totaldias = ($FechaPago-$FechaVen)/86400;
	//echo $totaldias;
	
	//
	while (($Dias<$totaldias) and ($Dias<=2000))
		{
		$multa_diaria = (($porcentaje*$_POST['OMONTO']));
		$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;	
		//------------------
		$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
		//------------------
		$Dias++;
		}
	//---------------
	//echo $porcentaje;
	}
	
	//alexander 16/05/2024
	
	//xxxxxxxxxx	PORCENTAJE DIARIO  PARA ANTICIPO DEL ISLR  ***
if ($aplicacion==155)
	{

	$porcentaje = 0.05; //115#3

	// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
	$_SESSION['VALOR_UT_PRIMITIVA'] = moneda_infraccion(voltea_fecha($_POST['OVENCIMIENTO']));
	// xxxxxxxxxx

	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($_POST['OVENCIMIENTO']));
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($_POST['OPAGO']));
	//--------------
	$totaldias = ($FechaPago-$FechaVen)/86400;
	//echo $totaldias;
	
	//
	while (($Dias<$totaldias) and ($Dias<=100))
		{
		$multa_diaria = (($porcentaje*$_POST['OMONTO']));
		$ut_aplicada_diaria = $multa_diaria / $_SESSION['VALOR_UT_PRIMITIVA']  ;	
		//------------------
		$ut_aplicadas = $ut_aplicadas + $ut_aplicada_diaria;
		//------------------
		$Dias++;
		}
	//---------------
	//echo $porcentaje;
	}
	//fin alexito

//xxxxxxxxxx	MONEDA MAS ALTA						
if ($aplicacion>100)
	{
	$monto = $ut_aplicadas * moneda_mas_alta();
	//UNIDADES A APLICAR
	//$ut_aplicadas = $monto/$_SESSION['VALOR_UT_ACTUAL'];
	//$ut_aplicadas = $ut_max;
	//$monto = formato_moneda2($ut_aplicadas * $_SESSION['VALOR_UT_ACTUAL']);
	$monto = formato_moneda2($monto);
	}
else
	{
	$ut_aplicadas = formato_moneda2($ut_aplicadas);
	$monto = formato_moneda2($ut_aplicadas * $_SESSION['VALOR_UT_ACTUAL']);
	}
// ---------------------
?>