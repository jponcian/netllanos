<?php

// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
$_SESSION['MONTO_BS_PRIMITIVO'] = moneda_infraccion($fecha_vencimiento);
$_SESSION['MONTO_BS_ACTUAL'] = moneda_mas_alta();
$_SESSION['VALOR_UT_PRIMITIVA'] = unidad_infraccion($fecha_vencimiento);
$fecha_vencimiento = ($fecha_vencimiento);
// xxxxxxxxxx

// COT 2001
if ($cot == '22')
	{
	// CALCULO DE LA MULTA
	$multa_primitiva = $monto ; 
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2001
if ($cot == '111')
	{
	// CALCULO DE LA MULTA
	$multa_primitiva = ($monto * 10) / 100;
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2001
if ($cot == '112-1' or $cot == '112#1')
	{
	// CALCULO DE LA MULTA
	$multa_primitiva = ($monto * 15) / 100;
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2001
if ($cot == '112-2' or $cot == '112#2')
	{	
	$multa_primitiva = 0;
	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($fecha_vencimiento));
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($fecha_pago));
	
	$Dias=1;
	$FechaVen=$FechaVen+86400;
	
		while ($FechaVen <= $FechaPago)
		{
		$multa_primitiva = $multa_primitiva + (((1.5*$monto)/100)/30);
		//------------------
		$FechaVen=$FechaVen+86400;
		$Dias++;
		}
	// xxxxxxxxxx
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2001
if ($cot == '112-3' or $cot == '112#3')
	{	
	// CALCULO DE LA MULTA
	$multa_primitiva = $monto; //200%; AGENTE DE RETENCION ACEPTACA REPARO MULTA SE REDUCE A LA MITAD
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2001
if ($cot == '112-4' or $cot == '112#4')
	{	
	// CALCULO DE LA MULTA
	$multa_primitiva = $monto / 2; //100% ; AGENTE DE RETENCION ACEPTACA REPARO MULTA SE REDUCE A LA MITAD
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2001
if ($cot == '113')
	{	
	$multa_primitiva = 0;
	
	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($fecha_vencimiento));
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($fecha_pago));
	
	$FechaVen=$FechaVen+86400;
	
		while ($FechaVen <= $FechaPago)
		{
		$multa_primitiva = $multa_primitiva + (((50*$monto)/100)/30);
		//------------------
		$FechaVen=$FechaVen+86400;
		}
	// xxxxxxxxxx
	if ($multa_primitiva>($monto*5))
		{
		$multa_primitiva = $monto * 5;
		}
	// xxxxxxxxxx
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}	
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2014 deberia ser 100 al 300 son 200%

if ($cot == '112')
	{
	// CALCULO DE LA MULTA
	
	$multa_primitiva = ($monto * 30) / 100; 
	    /*echo " monto: ", $monto, "</br>";
		echo " multa primitiva: ", $multa_primitiva, "</br>";
		echo " valor bs primitiva", $_SESSION['MONTO_BS_PRIMITIVO'], "</br>";
		echo " valor ut primitiva", $_SESSION['VALOR_UT_PRIMITIVA'], "</br>";
		echo "valor bs actual ", $_SESSION['MONTO_BS_ACTUAL'], "</br> ";
		echo "valor ut actual ", $_SESSION['VALOR_UT_ACTUAL'], "</br> ";
		echo "valor bs actual ", ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']), "</br> -";
		*/
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		} 
		//echo $fecha_vencimiento;
	}
	
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2014
if ($cot == '114-1' or $cot == '114#1')
	{
	// CALCULO DE LA MULTA
	$multa_primitiva = $monto;
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2014
if ($cot == '114-2' or $cot == '114#2')
	{	
	$multa_primitiva = 0;
	
	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($fecha_vencimiento));
	 
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($fecha_pago));
	
	
	$Dias=1;
	$FechaVen=$FechaVen+86400;
	
		while ($FechaVen <= $FechaPago)
		{
		$multa_primitiva = $multa_primitiva + (((0.05*$monto)/100)); 
		//------------------
		$FechaVen=$FechaVen+86400;
		$Dias++;
		}

		
	// xxxxxxxxxx
	if ($Dias>2000)
		{
		$multa_primitiva = $monto;
		}
	// xxxxxxxxxx
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
		
	}
	//alexito saber los dias que han transcurridos
	/*echo "  fecha pago: ", fecha_a_numero('2024/04/10'), "</br>";
	echo "  fecha vencimientos: ", fecha_a_numero($fecha_vencimiento), "</br>";
	echo "dias transcurridos:",(fecha_a_numero('2024/04/10')- fecha_a_numero($fecha_vencimiento))/86400,"</br>";*/
	//fin alexito
	
	
	/*	echo "  multa primitiva: ", $multa_primitiva, "</br>";
		echo " valor bs primitiva", $_SESSION['MONTO_BS_PRIMITIVO'], "</br>";
		echo " valor ut primitiva", $_SESSION['VALOR_UT_PRIMITIVA'], "</br>";
		echo "valor bs actual ", $_SESSION['MONTO_BS_ACTUAL'], "</br> ";
		echo " valor ut actual ", $_SESSION['VALOR_UT_ACTUAL'], "</br> ";
		echo "  valor bs actual ", ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']), "</br> -";*/
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2014
if ($cot == '115-1' or $cot == '115#1')
	{
	// CALCULO DE LA MULTA
	$multa_primitiva = $monto*5;
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2014
if ($cot == '115-2' or $cot == '115#2')
	{
	// CALCULO DE LA MULTA
	$multa_primitiva = $monto;
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2014
if ($cot == '115-3' or $cot == '115#3')
	{	
	$multa_primitiva = 0;
	
	// FECHA DE VENCIMIENTO
	$FechaVen = fecha_a_numero(voltea_fecha($fecha_vencimiento));
	// FECHA DE PAGO
	$FechaPago = fecha_a_numero(voltea_fecha($fecha_pago));
	
	$Dias=1;
	$FechaVen=$FechaVen+86400;
	
		while ($FechaVen <= $FechaPago)
		{
		$multa_primitiva = $multa_primitiva + (((5*$monto)/100));
		//------------------
		$FechaVen=$FechaVen+86400;
		$Dias++;
		}
	// xxxxxxxxxx
	if ($Dias>100)
		{
		$multa_primitiva = $monto*5;
		}
	// xxxxxxxxxx
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL'];
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx

// COT 2014
if ($cot == '115-4' or $cot == '115#4')
	{
	// CALCULO DE LA MULTA
	$multa_primitiva = $monto*10; //echo $multa_primitiva;
	$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL']; //echo $_SESSION['VALOR_UT_ACTUAL'];
	if (fecha_a_numero($fecha_vencimiento)>=fecha_a_numero('2020/03/01'))
		{
		// CALCULO DE LA MULTA
		$multa_primitiva = $monto*60; //echo $multa_primitiva;
		$multa = ($multa_primitiva / $_SESSION['MONTO_BS_PRIMITIVO']) * $_SESSION['MONTO_BS_ACTUAL']; //echo $_SESSION['MONTO_BS_ACTUAL']; 
		}
	}
//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
$multa_primitiva = formato_moneda2($multa_primitiva) ; 
$multa = formato_moneda2($multa);
?>