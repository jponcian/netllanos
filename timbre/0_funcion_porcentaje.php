<?php
// CALCULO DEL PORCENTAJE
if ($tipo == 'Libre')
	{
	// TABLA TEMPORAL
	$consulta = "SELECT Sum((timbre_inv.precio*timbre_ventas_detalle_temporal.cantidad)) AS monto FROM timbre_ventas_detalle_temporal INNER JOIN timbre_inv ON timbre_ventas_detalle_temporal.codigo = timbre_inv.codigo;";  
	$tabla = mysql_query($consulta);
	$monto = 0;
	if ($registrox = mysql_fetch_object($tabla))
		{	$monto = $registrox->monto;		}
	
	// TABLA DE VENTAS REGISTRADAS
			
	$consulta = "SELECT Sum((timbre_inv.precio*timbre_ventas_detalle.cantidad)) AS monto, Month(timbre_ventas.fecha) as mes FROM timbre_ventas INNER JOIN (timbre_ventas_detalle INNER JOIN timbre_inv ON timbre_ventas_detalle.codigo = timbre_inv.codigo) ON timbre_ventas.numero = timbre_ventas_detalle.numero_venta WHERE timbre_ventas.licencia=".$_SESSION['LICENCIA']." AND Month(timbre_ventas.fecha)=Month(Date(now())) GROUP BY timbre_ventas.licencia, Month(timbre_ventas.fecha);";
	$tabla = mysql_query($consulta);
	
	$porcentaje = 0; 
	
	if ($registrox = mysql_fetch_object($tabla))
		{	$porcentaje = $registrox->monto;	}
	
	//  CALCULO DEL PORCENTAJE
	$porcentaje += $monto; 
	
	if ($porcentaje>=500 and $porcentaje<=1000)
		{$porcentaje = 10;}
		else
			{if ($porcentaje>1000 and $porcentaje<=1500)
				{$porcentaje = 7;}
				else
					{if ($porcentaje>1500)
						{$porcentaje = 5;}
						else
							{$porcentaje = 0;}
					}
			}
	//-----------------------------------------------------------
	}
else
	{
	if ($tipo == 'Oficial')	{$porcentaje = 0;}
	}
?>