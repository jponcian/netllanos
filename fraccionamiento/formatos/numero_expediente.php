<?php
// ------ OBTENER LA INFORMACION DEL CONTRIBUYENTE PARA VER SI TIENE NUMERO DE RESOLUCION
$consulta = "SELECT numero, sector FROM expedientes_fraccionamiento WHERE rif='".$rif."' AND status=1;";
$tabla = mysql_query($consulta);
$registro = mysql_fetch_object($tabla);
// ------ 
if ($registro->numero<=0)
	{	
	////////// GENERAR LA RESOLUCION
	$consulta = "SELECT Max(numero)+1 AS maximo FROM expedientes_fraccionamiento WHERE anno=Year(date(now())) and sector=".$registro->sector;
	$tablax = mysql_query($consulta);
	$registrox = mysql_fetch_object($tablax);
	if ($registrox->maximo>0)
		{$maximo=$registrox->maximo;}
	else
		{$maximo=1;}
	// ------ ACTUALIZAR EL EXPEDIENTE
	$consultax = "UPDATE expedientes_fraccionamiento SET anno=Year(date(now())), numero=".$maximo.", fecha=date(now()), fecha_proceso=date(now()) WHERE rif='".$rif."' AND status=1 AND sector=".$registro->sector;
	$tablax = mysql_query($consultax);
			
	// ACTUALIZAR EL REGISTRO EN LAS LIQUIDACIONES NUEVAS
	$consulta = "UPDATE liquidacion SET num_expediente=".$maximo.", anno_expediente=Year(date(now())), sector=".$registro->sector." WHERE rif='".$rif."' AND num_expediente=9999 AND status=0 AND origen_liquidacion=".$origenF.";";
	$tablax = mysql_query($consulta);
	
	//GUARDAMOS EL NUMERO DE RESOLUCION
	$consulta ="INSERT INTO resoluciones (id_sector, id_origen, anno_expediente, num_expediente, anno, numero, fecha) VALUES (".$registro->sector.", ".$origenF.", Year(date(now())), ".$maximo.", Year(date(now())), ".$maximo.", date(now()))";
	$tablax = mysql_query($consulta);
	// -------------
	}
?>