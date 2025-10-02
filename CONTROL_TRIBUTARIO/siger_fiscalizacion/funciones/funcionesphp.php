<?php

//*************************************  CANTIDAD DE FISCALES ***************************************************//
function cantidad_fiscales($con, $inicio, $fin, $programa, $tipo)
{
	//$sql = "CALL Cantidad_Fiscales( $programa, $inicio, $fin );";
	$sql = "SELECT Count(DISTINCT expedientes_fiscalizacion.ci_fiscal1) AS fiscales, a_tipo_programa.descripcion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa." GROUP BY a_tipo_programa.id_programa";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->fiscales>0)
	{
		$cantidad = $reg->fiscales;
	} else {
		$cantidad = 0;
	}
	return $cantidad;
}

//*************************************  CONTRIBUYENTES VISITADOS ***************************************************//
function visitados($con, $inicio, $fin, $programa, $tipo)
{
	//$sql = "CALL Visitados( $programa, $inicio, $fin );";
	$sql = "SELECT Count(a_tipo_programa.id_programa) AS visitados, a_tipo_programa.descripcion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa."  GROUP BY a_tipo_programa.id_programa";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->visitados>0)
	{
		$visitados = $reg->visitados;
	} else {
		$visitados = 0;
	}
	return $visitados;
}

//*************************************  CONTRIBUYENTES MULTADOS ***************************************************//
function multados($con, $inicio, $fin, $programa, $tipo)
{
	if ($tipo === 'Verificaciones')
	{
		//$sql = "CALL Multados( $programa, $inicio, $fin );";
		$sql = "SELECT count(a_tipo_programa.id_programa) as sancionados FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE EXISTS (select * FROM liquidacion WHERE liquidacion.sector = expedientes_fiscalizacion.sector and liquidacion.anno_expediente = expedientes_fiscalizacion.anno and liquidacion.num_expediente = expedientes_fiscalizacion.numero) AND expedientes_fiscalizacion.fecha_notificacion BETWEEN  '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa."  GROUP BY a_tipo_programa.id_programa";
		$tabla = $con->query($sql);
		$reg = $tabla->fetch_object();
		if ($reg->sancionados>0)
		{
			$multados = $reg->sancionados;
		} else {
			$multados = 0;
		}
	} else {
		$multados = 0;
	}
	return $multados;
}

//*************************************  CONTRIBUYENTES CLAUSURADOS ***************************************************//
function clausurados($con, $inicio, $fin, $programa, $tipo)
{
	if ($tipo === 'Verificaciones')
	{
		//$sql = "CALL Clausurados( $programa, $inicio, $fin );";
		$sql = "SELECT count(a_tipo_programa.id_programa) as clausura FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN liquidacion ON liquidacion.anno_expediente = expedientes_fiscalizacion.anno AND liquidacion.num_expediente = expedientes_fiscalizacion.numero AND liquidacion.sector = expedientes_fiscalizacion.sector INNER JOIN a_sancion ON a_sancion.id_sancion = liquidacion.id_sancion WHERE a_sancion.dias > 0 AND expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa." GROUP BY a_tipo_programa.id_programa";
		$tabla = $con->query($sql);
		$reg = $tabla->fetch_object();
		if ($reg->clausura>0)
		{
			$clausurados = $reg->clausura;
		} else {
			$clausurados = 0;
		}
	} else {
		$clausurados = 0;
	}
	return $clausurados;
}

//*************************************  FISCALIZACIONES INICIADAS ***************************************************//
function iniciados($con, $inicio, $fin, $programa, $tipo)
{
	//$sql = "CALL Iniciados( $programa, $inicio, $fin );";
	$sql = "SELECT Count(a_tipo_programa.id_programa) AS iniciados, a_tipo_programa.descripcion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa." GROUP BY a_tipo_programa.id_programa";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->iniciados>0)
	{
		$iniciados = $reg->iniciados;
	} else {
		$iniciados = 0;
	}
	return $iniciados;
}

//*************************************  CANTIDAD DE ACTAS NOTIFICADAS ***************************************************//
function actas($con, $inicio, $fin, $programa, $tipo)
{
	if ($tipo === 'Fiscalizaciones')
	{
		//$sql = "CALL Actas_Notificadas( $inicio, $fin );";
		$sql = "SELECT count(a_tipo_programa.id_programa) as actas FROM expedientes_fiscalizacion INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE fis_actas.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa." AND a_tipo_programa.tipo = 'Fiscalizaciones' GROUP BY a_tipo_programa.id_programa";
		$tabla = $con->query($sql);
		$reg = $tabla->fetch_object();
		if ($reg->actas>0)
		{
			$actas = $reg->actas;
		} else {
			$actas = 0;
		}
	} else {
		$actas = 0;
	}
	return $actas;
}

//*************************************  PRODUCCION POTENCIAL ACTAS NOTIFICADAS *******************************************//
function prod_potencial($con, $inicio, $fin, $programa, $tipo)
{
	//$sql = "CALL Prod_Potencial( $tipo, $programa, $inicio, $fin );";

	if ($tipo === 'Fiscalizaciones')
	{
		$sql = "SELECT sum(fis_actas_detalle.impuesto_omitido) as produccion FROM expedientes_fiscalizacion INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE fis_actas.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa." GROUP BY a_tipo_programa.id_programa";
	} else {
		$sql = "SELECT Sum(vista_ct_multas.Multas) AS produccion FROM expedientes_fiscalizacion INNER JOIN vista_ct_multas ON vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero AND vista_ct_multas.sector = expedientes_fiscalizacion.sector INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' AND expedientes_fiscalizacion.programa = ".$programa." GROUP BY expedientes_fiscalizacion.programa";
	}
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->produccion>0)
	{
		$produccion = $reg->produccion;
	} else {
		$produccion = 0;
	}
	return $produccion;
}

//*************************************  PRODUCCION EFECTIVA ACTAS NOTIFICADAS ***********************************************//
function prod_efectiva($con, $inicio, $fin, $programa, $tipo)
{
	if ($tipo === 'Fiscalizaciones')
	{
		//$sql = "CALL Prod_Efectiva( $programa, $inicio, $fin );";
		$sql = "SELECT sum(fis_actas_detalle.monto_pagado) as pagado FROM expedientes_fiscalizacion INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE fis_actas_detalle.fecha_pago BETWEEN '".$inicio."' AND '".$fin."' AND a_tipo_programa.id_programa = ".$programa." GROUP BY a_tipo_programa.id_programa";
		$tabla = $con->query($sql);
		$reg = $tabla->fetch_object();
		if ($reg->pagado>0)
		{
			$pagado = $reg->pagado;
		} else {
			$pagado = 0;
		}
	} else {
		$pagado = 0;
	}
	return $pagado;
}

function buscar_sector($con, $anno, $numero, $sector)
{
	//$sql = "CALL Buscar_Sector( $anno, $numero, $sector );";
	$sql = "SELECT fis_anexo2.Actividad_Economica as sector_comercial FROM fis_anexo2 WHERE fis_anexo2.Anno_Providencia = ".$anno." AND fis_anexo2.NroAutorizacion = ".$numero." AND fis_anexo2.Sede = '".$sector."'";
	//echo $sql.'<br>';
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->sector_comercial != "")
	{
		$sectorc = $reg->sector_comercial;
	} else {
		$sectorc = "COMERCIAL";
	}
	return $sectorc;
}

function posee_maquina($con, $anno, $numero, $sector)
{
	//$sql = "CALL Posee_Maquina( $anno, $numero, $sector );";
	//$sql = "CALL Buscar_Sector( $anno, $numero, $sector );";
	$sql = "SELECT fis_anexo2.Tipo_Maq_fiscal as maq_fiscal FROM fis_anexo2 WHERE fis_anexo2.Anno_Providencia = ".$anno." AND fis_anexo2.NroAutorizacion = ".$numero." AND fis_anexo2.Sede = '".$sector."'";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->maq_fiscal == "IMPRESORA" or $reg->maq_fiscal == "REGISTRADORA")
	{
		$posee = "x";
	} else {
		$posee = "";
	}
	return $posee;
}

function operativos_sancionado($con, $anno, $numero, $sector)
{
	//$sql = "CALL Operativos_Sancionado( $anno, $numero, $sector );";
	$sql = "SELECT fis_anexo2.DF_Multas as sancionado FROM fis_anexo2 WHERE fis_anexo2.Anno_Providencia = ".$anno." AND fis_anexo2.NroAutorizacion = ".$numero." AND fis_anexo2.Sede = '".$sector."'";
	//echo $sql.'<br>';
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->sancionado == "SI")
	{
		$sancionado = "x";
	} else {
		$sancionado = "";
	}
	return $sancionado;
}

function operativos_clausurado($con, $anno, $numero, $sector)
{
	//$sql = "CALL Operativos_Clausurado( $anno, $numero, $sector );";
	$sql = "SELECT fis_anexo2.DF_Clausura as cierre FROM fis_anexo2 WHERE fis_anexo2.Anno_Providencia = ".$anno." AND fis_anexo2.NroAutorizacion = ".$numero." AND fis_anexo2.Sede = '".$sector."'";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->cierre == "SI")
	{
		$cierre = "x";
	} else {
		$cierre = "";
	}
	return $cierre;
}

function operativos_pp($con, $anno, $numero, $sector)
{
	//$sql = "CALL Operativos_PP( $anno, $numero, $sector );";
	$sql = "SELECT Sum(vista_ct_multas.Multas) AS produccion FROM expedientes_fiscalizacion INNER JOIN vista_ct_multas ON vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero AND vista_ct_multas.sector = expedientes_fiscalizacion.sector INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND z_sectores.nombre = '".$sector."'";
	$tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->produccion > 0)
	{
		$monto = $reg->produccion;
	} else {
		$monto = 0;
	}
	return $monto;
}

function programa($programa)
{
    $cadena = substr($programa, 0, 3);

    switch ($cadena) 
	{
		case "Ver":
			$tipo = "vdf";
			break;
		case "Ver":
			$tipo = "vdf";
			break;
		case "FPN":
			$tipo = "fp";
			break;
		case "FPR":
			$tipo = "fp";
			break;
		case "Fis":
			$tipo = "fi";
			break;
		case "Imp":
			$tipo = "fi";
			break;
		case "Otr":
			$tipo = "op";
			break;
		case "Com":
			$tipo = "op";
			break;
	}
    return $tipo;
}

function prod_efect_operativos($con, $anno, $numero, $sector, $tipo)
{
	if ($tipo === 'Fiscalizaciones')
	{
		//$sql = "CALL Prod_Efect_Operativos( $anno, $numero, $sector );";
		$sql = "SELECT sum(fis_actas_detalle.monto_pagado) as pagado FROM expedientes_fiscalizacion INNER JOIN fis_actas ON fis_actas.id_sector = expedientes_fiscalizacion.sector AND fis_actas.anno_prov = expedientes_fiscalizacion.anno AND fis_actas.num_prov = expedientes_fiscalizacion.numero INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tipo_programa ON  a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_sectores ON z_sectores.id_sector =  expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.anno = ".$anno." AND expedientes_fiscalizacion.numero = ".$numero." AND z_sectores.nombre = '".$sector."'";
		
		$tabla = $con->query($sql);
		$reg = $tabla->fetch_object();
		if ($reg->pagado>0)
		{
			$pagado = $reg->pagado;
		} else {
			$pagado = 0;
		}
	} else {
		$pagado = 0;
	}

	return $pagado;
}

function tipo_operativo($programa)
{
    switch ($programa) 
	{
		case 'FIN':
		case 'FPN':
		case 'VN':
			$tipo = "NACIONAL";
			break;
		case 'FIR';
		case 'FPR';
		case 'VR';
		case 'OP';
			$tipo = "REGIONAL";
			break;
	}
    return $tipo;
}

function voltea_fecha($a)
{
	if ($a=='') 
	{
		if (substr($a,2,1)=='-' or substr($a,2,1)=='/')	{ return '00/00/0000'; }
		else 
		{ 
			if (substr($a,4,1)=='-' or substr($a,4,1)=='/')	{ return '00/00/0000'; }
				else
					{ return '0000/00/00'; }
		}
		//-----------
		}
	else
	{
		if (substr($a,2,1)=='-' or substr($a,4,1)=='-')	{$caracter='-';}
			else {$caracter='/';}
		//-----------
		$a = explode($caracter,$a);
		$aux = $a[2];
		$a[2] = $a[0];
		$a[0] = $aux;
		return implode($caracter,$a);
	}
}

function fechas($mes, $anno)
{
	$dia = date("d",(mktime(0,0,0,$mes+1,1,$anno)-1));
	$dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
	$mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
	$inicio = $anno."/".$mes."/01";
	$fin = $anno."/".$mes."/".$dia;
	$info = array($inicio, $fin);
	return $info;
}

function nomMes($mes)
{
	switch ($mes) {
		case 1:
			$nomMes = "ENERO";
			break;
		case 2:
			$nomMes = "FEBRERO";
			break;
		case 3:
			$nomMes = "MARZO";
			break;
		case 4:
			$nomMes = "ABRIL";
			break;
		case 5:
			$nomMes = "MAYO";
			break;
		case 6:
			$nomMes = "JUNIO";
			break;
		case 7:
			$nomMes = "JULIO";
			break;
		case 8:
			$nomMes = "AGOSTO";
			break;
		case 9:
			$nomMes = "SEPTIEMBRE";
			break;
		case 10:
			$nomMes = "OCTUBRE";
			break;
		case 11:
			$nomMes = "NOVIEMBRE";
			break;
		case 12:
			$nomMes = "DICIEMBRE";
			break;
	}
	return $nomMes;
}

function BorrarRegistros($con, $tabla, $inicio, $fin)
{
	//$sql = "CALL BorrarRegistros( $tabla, $inicio, $fin );";
	$sql = "DELETE FROM ".$tabla." WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";

	$tabla = $con->query($sql);
}
?>