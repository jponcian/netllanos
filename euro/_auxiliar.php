<?php
//--------------
setlocale(LC_ALL, 'sp_ES','sp', 'es');
//mysql_query("SET NAMES 'latin1'");
date_default_timezone_set('America/Caracas');

//--------------
$_SESSION['meses_anno'] = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

//--------------
function moneda_infraccion($a)
	{
	// CONSULTA DE LA UNIDAD EN LA INFRACCION
	$consulta_x = "SELECT * FROM a_moneda_cambio WHERE FechaAplicacion<='".$a."' ORDER BY FechaAplicacion DESC LIMIT 1;";
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//-----------
	return ($registro_x->valor);
	}
//--------------
function moneda_mas_alta()
	{
	//----------
	$consulta_y = "SELECT * FROM a_moneda_cambio ORDER BY FechaAplicacion DESC LIMIT 1;";
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	$monto = $registro_y->valor;
	//----------
	return $monto;
	}
//--------------
function status_almacen($i)
	{
	switch ($i) 
		{
		case 0:
			$valor = 'Solicitada';
			break;
		case 5:
			$valor = 'Aprobada';
			break;
		case 10:
			$valor = 'Despachada';
			break;
		case 99:
			$valor = 'Anulada';
			break;
		}
	return $valor;	
	}

//--------------
function primera_cadena($cadena)
	{
	list($a, $b, $c, $d) = explode(" ", $cadena);	
	return $a;	
	}

//--------------
function segunda_cadena($cadena)
	{
	list($a, $b, $c, $d) = explode(" ", $cadena);	
	return $b;	
	}
	
//--------------
function tercera_cadena($cadena)
	{
	list($a, $b, $c, $d) = explode(" ", $cadena);	
	return $c;	
	}

//--------------
function mantenimiento()
	{
	if ($_SESSION['ADMINISTRADOR']<>1 and $_POST['OUSUARIO']<>'16912337' and $_POST['OUSUARIO']<>'8632565')
		{
		echo "<script type=\"text/javascript\">alert('Sistema en Mantenimiento!');</script>";
		exit(); 
		}
	}
	
//--------------
function generar_resolucion( $sector, $origen, $anno, $num)
	{
	//// ACTUALIZACION DEL NUMERO DE LA RESOLUCION EN EL NUEVO
	$consulta = "SELECT * FROM resoluciones WHERE historial<=0 AND anno_expediente=".$anno." AND num_expediente=".$num." AND id_origen=".$origen." and id_sector=".$sector;
	$tabla = mysql_query($consulta);
	$numero_filas = mysql_num_rows($tabla);
	//-----------------------
	if ($numero_filas>0)
		{		}
	else
		{	
		//// CONSULTA DEL MAXIMO
		if ($origen == 5)
			{
			$consulta = "SELECT Max(numero)+1 AS Maximo FROM resoluciones WHERE year(fecha)=year(date(now())) and id_origen=".$origen;
			}
		else
			{
			$consulta = "SELECT Max(numero)+1 AS Maximo FROM resoluciones WHERE year(fecha)=year(date(now())) and id_origen=".$origen." and id_sector=".$sector;
			}
		$tabla = mysql_query($consulta);
		$registro = mysql_fetch_object($tabla);
		if ($registro->Maximo>0)
			{$Maximo=$registro->Maximo;}
		else
			{$Maximo=1;}
		////INSERTAR EL NUEVO REGISTRO
		$consulta = "INSERT INTO resoluciones (id_sector, id_origen, anno_expediente, num_expediente, anno, numero, fecha) SELECT ".$sector.", ".$origen.", ".$anno.", ".$num.", year(date(now())), ".$Maximo.", date(now())";
		$tabla = mysql_query($consulta);
		// ACTUALIZAR EL MONTO DE LA MULTA SEGUN LA UT ACTUAL
		$consulta = "UPDATE liquidacion SET monto_bs=monto_ut*".$_SESSION['VALOR_UT_ACTUAL']." WHERE sector=".$sector." AND anno_expediente=".$anno." AND num_expediente=".$num." AND origen_liquidacion=4 AND status<=10 AND id_sancion NOT IN (SELECT id_sancion FROM a_sancion WHERE serie=29 and serie=38)";	
		//$tabla = mysql_query($consulta);	
		}
	}

//--------------
function funcion_resolucion_id( $sector, $origen, $anno, $num, $id)
	{
	$resolucion='';
	$fecha_res='';
	$anno_res=0;
	$num_res=0;
	 //NUMERO DE LA RESOLUCIÓN
	$consulta_y = "SELECT anno, numero, fecha FROM resoluciones WHERE id_resolucion=".$id.";";  
	$tabla_y = mysql_query($consulta_y);
	if ($registro_y = mysql_fetch_object($tabla_y)){
	//------------
	$anno_res = $registro_y->anno;
	$num_res = $registro_y->numero;
	$fecha_res = $registro_y->fecha;
	//---------------------------------------- CUANDO VIENE DE SUJETOS PASIVOS ESPECIALES
	if ($origen==2)	
		{
		////////// INFORMACION DE LA PROVIDENCIA
		$consulta_datos = "SELECT Siglas_resol_especiales FROM vista_exp_especiales WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector.";";
		$tabla_datos = mysql_query($consulta_datos);
		$registro_datos = mysql_fetch_object($tabla_datos);
		////////// SIGLAS DE LA RESOLUCION
		$SIGLAS = $registro_datos->Siglas_resol_especiales;
		////////// 
		$resolucion = $SIGLAS."".$anno."/".sprintf("%004s", $num) ."/". $anno_res .'/'. sprintf("%004s", $num_res);
		// ---------------------
		}
	//--------------------------
	//---------------------------------------- CUANDO VIENE DE SUCESIONES
	if ($origen==3)	
		{
		////////// INFORMACION DE LA PROVIDENCIA
		$consulta_datos = "SELECT Siglas_resol_Suc FROM vista_re_exp_sucesiones WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector.";";
		$tabla_datos = mysql_query($consulta_datos);
		$registro_datos = mysql_fetch_object($tabla_datos);
		////////// SIGLAS DE LA RESOLUCION
		$SIGLAS = $registro_datos->Siglas_resol_Suc;
		////////// 
		$resolucion = $SIGLAS."/".$anno."/".sprintf("%004s", $num) ."/". $anno_res .'/'. sprintf("%004s", $num_res);
		// ---------------------
		}
	//--------------------------
	//---------------------------------------- CUANDO VIENE DE SUMARIO
	if ($origen==5)	
		{
		$consulta_datos = "SELECT Siglas_resol_Sum AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
		$tabla_datos = mysql_query($consulta_datos);
		$registro_datos = mysql_fetch_object($tabla_datos);
		////////// SIGLAS DE LA RESOLUCION
		$SIGLAS = $registro_datos->siglas;
		////////// 
		$resolucion = $SIGLAS."". $anno_res .'/'. sprintf("%004s", $num_res);
		// ---------------------
		}
	
	//--------------------------
	//---------------------------------------- CUANDO VIENE DE FISCALIZACIÓN
	if ($origen==4)	
		{
		////////// INFORMACION DE LA PROVIDENCIA
		$consulta_datos = "SELECT Siglas_resol_fis, Siglas1, Siglas2 FROM vista_providencias WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector.";";
		$tabla_datos = mysql_query($consulta_datos);
		$registro_datos = mysql_fetch_object($tabla_datos);
		////////// SIGLAS DE LA RESOLUCION
		$SIGLAS = $registro_datos->Siglas_resol_fis;
		////////// SIGLAS DE LA PROVIDENCIA
		$SIGLAS1 = $registro_datos->Siglas1;
		$SIGLAS2 = $registro_datos->Siglas2;	
		////////// 
		$resolucion = $SIGLAS."".$anno."/".$SIGLAS2."/".$SIGLAS1.sprintf("%004s", $num) ."/". $anno_res .'/'. sprintf("%004s", $num_res);
		// ---------------------
		}
	
	//--------------------------	
	//---------------------------------------- CUANDO VIENE DE AJUSTE
	if ($origen==16)	
		{
		$consulta_datos = "SELECT Siglas_resol_Cobro_ajuste AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
		$tabla_datos = mysql_query($consulta_datos);
		$registro_datos = mysql_fetch_object($tabla_datos);
		////////// SIGLAS DE LA RESOLUCION
		$SIGLAS = $registro_datos->siglas;
		////////// 
		$resolucion = $SIGLAS.""."/". $anno_res .'/'. sprintf("%004s", $num_res);
		// ---------------------
		}
	//--------------------------	
	//---------------------------------------- CUANDO VIENE DE FRACCIONAMIENTO
	if ($origen==17)	
		{
		$consulta_datos = "SELECT Siglas_resol_Frac AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
		$tabla_datos = mysql_query($consulta_datos);
		$registro_datos = mysql_fetch_object($tabla_datos);
		////////// SIGLAS DE LA RESOLUCION
		$SIGLAS = $registro_datos->siglas;
		////////// 
		$resolucion = $SIGLAS. $anno_res .'/'. sprintf("%004s", $num_res);
		// ---------------------
		}
	
	//--------------------------	
	}
	return array ($resolucion, $fecha_res, $num_res, $anno_res);
	}

//--------------
function funcion_resolucion( $sector, $origen, $anno,  $num)
	{
	$resolucion='';
	$fecha_res='';
	$anno_res=0;
	$num_res=0;
	//---------- POR SI EL ORIGEN ES MANUAL
	if ($origen>=50)	
		{
		 //NUMERO DE LA RESOLUCIÓN
		$consulta_y = "SELECT * FROM expedientes_manuales WHERE sector=".$sector." AND origen=".$origen." AND anno=".$anno." AND numero=".$num.";";
		$tabla_y = mysql_query($consulta_y);
		if ($registro_y = mysql_fetch_object($tabla_y))
			{
			//------------
			$anno_res = $registro_y->anno;
			$num_res = $registro_y->numero;
			$fecha_res = $registro_y->fecha_resolucion;
			$resolucion = $registro_y->resolucion;
			}
		}
	else
		{
		 //NUMERO DE LA RESOLUCIÓN
		$consulta_y = "SELECT anno, numero, fecha FROM resoluciones WHERE historial<=0 AND id_sector=".$sector." AND id_origen=".$origen." AND anno_expediente=".$anno." AND num_expediente=".$num.";"; 
		$tabla_y = mysql_query($consulta_y);
		if ($registro_y = mysql_fetch_object($tabla_y))
			{
			//------------
			$anno_res = $registro_y->anno;
			$num_res = $registro_y->numero;
			$fecha_res = $registro_y->fecha;
			}
		
		//---------------------------------------- CUANDO VIENE DE SUJETOS PASIVOS ESPECIALES
		if ($origen==2)	
			{
			////////// INFORMACION DE LA PROVIDENCIA
			$consulta_datos = "SELECT Siglas_resol_especiales FROM vista_exp_especiales WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->Siglas_resol_especiales;
			////////// 
			$resolucion = $SIGLAS.$anno."/".sprintf("%004s", $num) ."/". $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------
		
		//---------------------------------------- CUANDO VIENE DE SUCESIONES
		if ($origen==3)	
			{
			////////// INFORMACION DE LA PROVIDENCIA
			$consulta_datos = "SELECT Siglas_resol_Suc FROM vista_re_exp_sucesiones WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->Siglas_resol_Suc;
			////////// 
			$resolucion = $SIGLAS."/".$anno."/".sprintf("%004s", $num) ."/". $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------

		//---------------------------------------- CUANDO VIENE DE FISCALIZACIÓN
		if ($origen==4)	
			{
			////////// INFORMACION DE LA PROVIDENCIA
			$consulta_datos = "SELECT Siglas_resol_fis, Siglas1, Siglas2 FROM vista_providencias WHERE anno=".$anno." AND numero=".$num." AND sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->Siglas_resol_fis;
			////////// SIGLAS DE LA PROVIDENCIA
			$SIGLAS1 = $registro_datos->Siglas1;
			$SIGLAS2 = $registro_datos->Siglas2;	
			////////// 
			$resolucion = $SIGLAS."/".$anno."/".$SIGLAS2."/".$SIGLAS1.sprintf("%004s", $num) ."/". $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------	
		
		//---------------------------------------- CUANDO VIENE DE SUMARIO
		if ($origen==5)	
			{
			$consulta_datos = "SELECT Siglas_resol_Sum AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->siglas;
			////////// 
			$resolucion = $SIGLAS. $anno_res .'/'. sprintf("%004s", $num_res);
			//$resolucion = $SIGLAS.$sector."/". $anno_res .'/'. sprintf("%004s", $num_res);			
			// ---------------------
			}
		//--------------------------
		
		//---------------------------------------- CUANDO VIENE DE AJUSTE UT ESPECIALES
		if ($origen==7)	
			{
			////////// INFORMACION DEL EXPEDIENTE
			$consulta_datos = "SELECT Siglas_resol_ajuste_spe as siglas FROM z_siglas WHERE id_sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->siglas;
			////////// 
			$resolucion = $SIGLAS."/". $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------
		
		//---------------------------------------- CUANDO VIENE DE FRACCIONAMIENTO ESPECIALES
		if ($origen==8)	
			{
			////////// INFORMACION DEL EXPEDIENTE
			$consulta_datos = "SELECT Siglas_resol_fracc_spe as siglas FROM z_siglas WHERE id_sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->siglas;
			////////// 
			$resolucion = $SIGLAS."/". $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------
		
		//---------------------------------------- CUANDO VIENE DE RIF
		if ($origen==12)	
			{
			$consulta_datos = "SELECT Siglas_resol_Rif AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->siglas;
			////////// 
			$resolucion = $SIGLAS."/". $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------	
	
		//---------------------------------------- CUANDO VIENE DE COBRO
		if ($origen==13)	
			{
			$consulta_datos = "SELECT Siglas_resol_Cobro AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->siglas;
			////////// 
			$resolucion = $SIGLAS. $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------	
	
		//---------------------------------------- CUANDO VIENE DE AJUSTE
		if ($origen==16)	
			{
			$consulta_datos = "SELECT Siglas_resol_Cobro_ajuste AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->siglas;
			////////// 
			$resolucion = $SIGLAS."/". $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------	
		
		//---------------------------------------- CUANDO VIENE DE FRACCIONAMIENTO
		if ($origen==17)	
			{
			$consulta_datos = "SELECT Siglas_resol_Frac AS siglas FROM z_siglas WHERE id_sector=".$sector.";";
			$tabla_datos = mysql_query($consulta_datos);
			$registro_datos = mysql_fetch_object($tabla_datos);
			////////// SIGLAS DE LA RESOLUCION
			$SIGLAS = $registro_datos->siglas;
			////////// 
			$resolucion = $SIGLAS. $anno_res .'/'. sprintf("%004s", $num_res);
			// ---------------------
			}
		//--------------------------
		}
		
	return array ($resolucion, $fecha_res, $num_res, $anno_res);
	}

//--------------
function resumen_multa( $rif, $origen)
	{
	$monto=0;
	//----------
	if ($origen>0)	 {	$origen = ' AND origen_liquidacion='.$origen;	}	else	{	$origen = '';	}
	//----------
	$consulta_y = "SELECT sum(monto_bs/concurrencia*especial) as monto FROM liquidacion, a_sancion WHERE (a_sancion.serie<>38 and a_sancion.tributo<>52) AND a_sancion.id_sancion = liquidacion.id_sancion AND rif='".$rif."' ".$origen.";";
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	$monto = $registro_y->monto;
	//----------
	return $monto;
	}

//--------------
function resumen_interes( $rif, $origen)
	{
	$monto=0;
	//----------
	if ($origen>0)	 {	$origen = ' AND origen_liquidacion='.$origen;	}	else	{	$origen = '';	}
	//----------
	$consulta_y = "SELECT sum(monto_bs/concurrencia*especial) as monto FROM liquidacion, a_sancion WHERE (a_sancion.serie=38 or a_sancion.tributo=52) AND a_sancion.id_sancion = liquidacion.id_sancion AND rif='".$rif."' ".$origen.";";
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	$monto = $registro_y->monto;
	//----------
	return $monto;
	}

//--------------
function buscar_cuenta($serie)
	{
	$consulta_y = "SELECT cuenta, descripcion FROM a_tributos WHERE serie=0".$serie.";";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//------------
	$cuenta = 0;
	$concepto = '';
	//------------
	$cuenta = $registro_y->cuenta;
	$concepto = $registro_y->descripcion;
	//--------------------------	
	return array ($cuenta, $concepto);
	}

//--------------
function region_liq()
	{
	$consulta_y = "SELECT gerencia FROM z_region;";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//--------------------------	
	return $registro_y->gerencia;
	}

//--------------
function buscar_region()
	{
	$consulta_y = "SELECT nombre FROM z_region;";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//--------------------------	
	return $registro_y->nombre;
	}

//--------------
function sistema()
	{
	$consulta_y = "SELECT sistema FROM z_region;";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//--------------------------	
	return $registro_y->sistema;
	}

//--------------
function tributo($id)
	{
	$consulta_y = "SELECT nombre FROM a_tributos WHERE id_tributo=0".$id.";";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//--------------------------	
	return $registro_y->nombre;
	}

//--------------
function area($area)
	{
	$consulta_y = "SELECT descripcion FROM bn_areas WHERE id_area=0".$area.";";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//------------
	$area = $registro_y->descripcion;
	//--------------------------	
	return $area;
	}

//--------------
function division($division)
	{
	$consulta_y = "SELECT descripcion FROM z_jefes_detalle WHERE division=0".$division.";";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//------------
	$division = $registro_y->descripcion;
	//--------------------------	
	return $division;
	}

//--------------
function serie_liquidacion($id_liquidacion)
	{
	$consulta_y = "SELECT mid(liquidacion,12,2) as serie FROM liquidacion WHERE id_liquidacion=".$id_liquidacion.";";
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//------------
	$serie = $registro_y->serie;
	//--------------------------	
	return $serie ;
	}

//--------------
function sector($sector)
	{
	$consulta_y = "SELECT nombre FROM z_sectores WHERE id_sector=".$sector.";";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//------------
	$sector = $registro_y->nombre;
	//--------------------------	
	return $sector;
	}

//--------------
function origen($origen)
	{
	$consulta_y = "SELECT Abreviatura as nombre FROM a_origen_liquidacion WHERE Codigo=".$origen.";";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//------------
	$origen = $registro_y->nombre;
	//--------------------------	
	return $origen;
	}

//--------------
function buscar_sector($sector)
	{
	 //NUMERO DE LA RESOLUCIÓN
	$consulta_y = "SELECT * FROM z_sectores WHERE id_sector=".$sector.";";  
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	//------------
	$estado = $registro_y->estado;
	$sede = $registro_y->resol_especiales;
	$adscripcion = $registro_y->adscripcion_gerencia;
	$conector1 = $registro_y->conector1_es;
	$conector2 = $registro_y->conector2_es;
	$tipo_division = $registro_y->tipo_division;
	$sector = $registro_y->nombre;
	$titulo = $registro_y->titulo;	
	//--------------------------	
	return array ($estado, $sede, $conector1, $conector2, $adscripcion, $tipo_division, $sector, $titulo);
	}

////--------------
function funcion_acta_reparo( $sector, $anno, $num)
	{
	 //----- PARA VER SI EXISTE EL ACTA
	$consulta_x = "SELECT numero AS Maximo, COT, fecha FROM fis_actas WHERE id_sector=".$sector." AND anno_prov=".$anno." AND num_prov=".$num.";";
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	if ($registro_x->Maximo>0)
		{
		$Maximo = $registro_x->Maximo;	
		$procedimiento = $registro_x->COT; 
		$fecha_res = voltea_fecha($registro_x->fecha);
		}	
	// FIN
	
	////////// TIPO DE LA PROVIDENCIA
	$consulta_x = "SELECT * FROM vista_providencias WHERE sector=".$sector." AND anno=".$anno." AND numero=".$num.";";
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$SIGLAS=$registro_x->Siglas_resol_fis;
	$SIGLAS1=$registro_x->Siglas1;
	$SIGLAS2=$registro_x->Siglas2;
	// ---------------------
	
	////////// DATOS DE LA RESOLUCION
	$RES_PRO = $SIGLAS."/".$anno."/".$SIGLAS1.$SIGLAS2."/".sprintf("%005s", $num);
	$RES_ACTA = "/".date('Y') . '/' . $Maximo;
	$resolucion = $RES_PRO . $RES_ACTA;
	////////// FIN
	
	//--------------------------	
	return array ($resolucion, $fecha_res, $procedimiento);
	}

//--------------
function funcion_contribuyente( $rif )
	{
	//-------------
	$contribuyente = '';
	$direccion = '';
	//------ BUSQUEDA
	$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='".$rif."';"; 
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//-------------
	$contribuyente = $registro_x->contribuyente;
	$direccion = $registro_x->direccion;
	//-------------
	return array ($contribuyente, $direccion);
	}

//--------------
function funcion_funcionario( $cedula )
	{
	//------ BUSQUEDA
	$consulta_x = "SELECT * FROM z_empleados WHERE cedula=".$cedula.";"; 
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//-------------
	$funcionario = $registro_x->Nombres. " " . $registro_x->Apellidos;
	$cargo1 = $registro_x->Cargo;	
	$cargo2 = $registro_x->cargo2;	
	$division = $registro_x->division;
	return array ($funcionario, $cargo1, $cargo2, $division);
	}

//--------------
function actividad_economica( $rif )
	{
	//------ BUSQUEDA
	$consulta_x = "SELECT a_actividades.descripcion FROM a_actividades, contribuyentes WHERE contribuyentes.actividad = a_actividades.id_actividad AND rif='$rif'"; 
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//-------------
	$actividad = $registro_x->descripcion;
	return ($actividad);
	}

//--------------
function funcion_interes( $monto, $fecha_pago, $fecha_vencimiento )
	{
	// CALCULO DE LOS INTERESES
	if (substr($fecha_pago,2,1)=='-') {$caracter='-';} else {$caracter='/';}
	//-------------
	list($dia,$mes,$anno)=explode($caracter,$fecha_pago);
	$FECHA_PAGO = mktime(0,0,0,$mes,$dia,$anno);
	
	if (substr($fecha_vencimiento,2,1)=='-') {$caracter='-';} else {$caracter='/';}
	//-------------
	list($dia,$mes,$anno)=explode($caracter,$fecha_vencimiento);
	$fecha_vencimiento = mktime(0,0,0,$mes,$dia,$anno);
	
	$Dias = $FECHA_PAGO - $fecha_vencimiento;
	$Dias_Total = $Dias_Total + $Dias;
	
	$txt = $Dias/86400;

	// CALCULO DEL INTERES-------------------------------------------------------------------
	$fecha_vencimiento = $fecha_vencimiento + 86400;
	$interes = 0;
	// CONSULTA DE LAS TAZAS
	$consulta_y = "SELECT * FROM a_tasa_interes ORDER BY anno";
	$tabla_y = mysql_query($consulta_y);
	$registro_y = mysql_fetch_object($tabla_y);
	// FIN CONSULTA DE LAS TAZAS
	while ($fecha_vencimiento <= $FECHA_PAGO)
		{
		while ($registro_y->anno < date('Y',$fecha_vencimiento)) // BUSQUEDA DEL AÑO
			{
			$registro_y = mysql_fetch_object($tabla_y);
			$tazas = array('0',$registro_y->enero,$registro_y->febrero,$registro_y->marzo,$registro_y->abril,$registro_y->mayo,$registro_y->junio,$registro_y->julio,$registro_y->agosto,$registro_y->septiembre,$registro_y->octubre,$registro_y->noviembre,$registro_y->diciembre);	
			} 													// FIN DE LA BUSQUEDA DEL AÑO
		$tasa = $tazas[number_format(doubleval(date('m',$fecha_vencimiento)),0,'','')];
		$interes = $interes + (($monto * ($tasa*1.20)) / 36000) ;
		$fecha_vencimiento = $fecha_vencimiento + 86400;
		}
	// FIN DEL CALCULO DEL INTERES ----------------------------------------------------------
	return (formato_moneda2($interes));
	}

//--------------
function formato_rif($a)
	{
	return (strtoupper(substr($a,0,1)).'-'.substr($a,1,8).'-'.substr($a,9,1));
	}

//--------------
function formato_cedula($a)
	{
	return (number_format($a,0,'','.'));
	}

//--------------
function minimo_soberano($a, $b)
	{
	if ($a<(0.01) and $b>0)
		{ 	$a = 0.01;	}
	return ($a);
	}

//--------------
function formato_moneda_bienes($a)
	{
	return (number_format($a,8,',','.'));
	}
	
//--------------
function formato_moneda($a)
	{
	return (number_format($a,2,',','.'));
	}
	
//--------------
function formato_moneda2($a)
	{
	return (number_format($a,2,'.',''));
	}
	
//--------------
function oracion($a)
	{
	return (ucfirst(strtolower(trim($a))));
	}

//--------------
function palabras($a)
	{
	return (ucwords(strtolower(trim($a))));
	}

//--------------
function mayuscula($a)
	{
	return (strtoupper(trim($a)));
	}

//--------------
function redondea($a)
	{
	return (number_format($a,0,',','.'));
	}

//--------------
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

//--------------
function extrae_fecha($a)
	{
	return substr($a,0,10);
	}
	
//--------------
function extrae_hora($a)
	{
	//$todo = substr($a,13,7);
	$horario = strtoupper(trim(substr($a,19,1)));
	$hora = substr($a,13,2);
	$min = substr($a,16,2);
	//------------------------
	if ($horario=='P')
		{	$hora = $hora + 12;}
	return $hora.':'.$min;
	}

//--------------
function fecha_a_numero($a)
	{
	if (substr($a,2,1)=='-' or substr($a,4,1)=='-')	{$caracter='-';}
		else {$caracter='/';}
	//------------
	list($anno,$mes,$dia)=explode($caracter, $a);
	$fecha = mktime(0,0,0,$mes,$dia,$anno);
	return ($fecha);
	}
	
//--------------
function formato_periodo($a)
	{
	return (date('m/Y',strtotime($a)));
	}
	
//--------------
function unidad_infraccion($a)
	{
	// CONSULTA DE LA UNIDAD EN LA INFRACCION
	$consulta_x = "SELECT ValorUT, FechaAplicacion FROM a_valorut WHERE (((FechaAplicacion)<='".$a."')) ORDER BY FechaAplicacion DESC;";
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//-----------
	return ($registro_x->ValorUT);
	}

//--------------
function encriptar_password($string)
	 {
		$key = '$Seniat.2015';
		$result = '';
		for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
		  $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)+ord($keychar));
		  $result.=$char;
		}
		return base64_encode($result);}

//--------------
function dias_feriados($fecha,$dias)
	{
	$datestart= strtotime($fecha);
	$i = 1;
	while ($i <= $dias)
	{
		$diasemana = date('N',$datestart);//echo $diasemana.' - '.date("d-m-Y", $datestart).' - '.$i.'<br/>';
		if ($diasemana<>6 and $diasemana<>7)
		{
			//BUSCAMOS SI ES DIA FERIADO
			$feriado = "SELECT fecha FROM a_dias_feriados WHERE fecha='".date("Y-m-d", $datestart)."'";
			$tabla = mysql_query($feriado);
			$dia = mysql_num_rows($tabla);
			if ($dia == 0)
			{
				$i++;
			}
		}
		$datestart = $datestart + 86400;
	}
	return date("Y-m-d", $datestart);	}
	
//--------------
function dias_feriados_reverso($fecha,$dias)
	{
		$datestart= strtotime($fecha);
		$i = 1;
		while ($i <= $dias)
		{
			$diasemana = date('N',$datestart);
			if ($diasemana<>6 and $diasemana<>7)
			{
				//BUSCAMOS SI ES DIA FERIADO
				$feriado = "SELECT fecha FROM a_dias_feriados WHERE fecha='".date("Y-m-d", $datestart)."'";
				$tabla = mysql_query($feriado);
				$dia = mysql_num_rows($tabla);
				if ($dia == 0)
				{
					$i++;
				}
			}
			$datestart = $datestart - 86400;
		}
		return date("Y-m-d", $datestart);	}
	
//--------------
function formato_si($a)
	{
	if ($a==0) {$a='NO';}
		if ($a==1) {$a='SI';}
	
	return $a;
	}

//--------------
function fecha_perencion($fecha)
	{
	$fecha = dias_feriados($fecha,40);
	$fecha = strtotime ( '+10 month' , strtotime ( $fecha ) ) ;
	$fecha = date ( 'Y-m-d' , $fecha );
	return $fecha;	
	}

//--------------
function dia($fecha)
	{
	$vector_fecha = explode("-",$fecha);
	return $vector_fecha[2];
	};

//--------------
function mes($fecha)
	{
	$vector_fecha = explode("-",$fecha);
	return $vector_fecha[1];
	};

//--------------
function anno($fecha)
	{
	if (substr($fecha,2,1)=='-' or substr($fecha,4,1)=='-')	{$caracter='-';}
		else {$caracter='/';}
	//--------------
	$vector_fecha = explode($caracter,$fecha);
	return $vector_fecha[0];
	};

//--------------
function extraer_iniciales($cadena)
	{
		list($a, $b, $c, $d) = explode(" ", $cadena);
		
		$a = substr($a, 0, 1);
		$b = substr($b, 0, 1);
		$c = substr($c, 0, 1);
		$d = substr($d, 0, 1);
		
		$iniciales = trim($a.$b.$c.$d);
		
		return $iniciales;	}

//--------------
function multa_sumario($monto, $fecha_vencimiento, $cot)
	{
		// VALOR DE LA UNIDAD AL MOMENTO DE LA INFRACCION
		$_SESSION['VALOR_UT_PRIMITIVA'] = unidad_infraccion($fecha_vencimiento);
		// xxxxxxxxxx
		
		// COT 2001
		if ($cot == '22')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = $monto ; 
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1500;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2001
		if ($cot == '111')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = ($monto * 112.5) / 100;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1500;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2001
		if ($cot == '112-1' or $cot == '112#1')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = ($monto * 15) / 100;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1500;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2001
		if ($cot == '112-2')
			{	
			$multa_primitiva = 0;
			
			// FECHA DE VENCIMIENTO
			list($dia,$mes,$anno)=explode('-',$fecha_vencimiento);
			$FechaVen=mktime(0,0,0,$mes,$dia,$anno); 
			// FECHA DE PAGO
			list($dia,$mes,$anno)=explode('-',$fecha_pago);
			$FechaPago=mktime(0,0,0,$mes,$dia,$anno);
			
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
			$cod_sancion = 1500;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2001
		if ($cot == '112-3')
			{	
			// CALCULO DE LA MULTA
			$multa_primitiva = $monto * 2;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1500;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2001
		if ($cot == '112-4')
			{	
			// CALCULO DE LA MULTA
			$multa_primitiva = $monto;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1500;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2001
		if ($cot == '113')
			{	
			$multa_primitiva = 0;
			
			// FECHA DE VENCIMIENTO
			list($dia,$mes,$anno)=explode('-',$fecha_vencimiento);
			$FechaVen=mktime(0,0,0,$mes,$dia,$anno); 
			// FECHA DE PAGO
			list($dia,$mes,$anno)=explode('-',$fecha_pago);
			$FechaPago=mktime(0,0,0,$mes,$dia,$anno);
			
			$FechaVen=$FechaVen+86400;
			
				while ($FechaVen <= $FechaPago)
				{
				$multa_primitiva = $multa_primitiva + (((50*$monto)/100)/30);
				//------------------
				$FechaVen=$FechaVen+86400;
				}
			// xxxxxxxxxx
			if (($monto*5)>$multa_primitiva)
				{
				$multa_primitiva = $monto * 5;
				}
			// xxxxxxxxxx
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1500;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2014
		if ($cot == '112')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = ($monto * 200) / 100;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1501;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2014
		if ($cot == '114-1')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = $monto;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1501;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2014
		if ($cot == '114-2')
			{	
			$multa_primitiva = 0;
			
			// FECHA DE VENCIMIENTO
			list($dia,$mes,$anno)=explode('-',$fecha_vencimiento);
			$FechaVen=mktime(0,0,0,$mes,$dia,$anno); 
			// FECHA DE PAGO
			list($dia,$mes,$anno)=explode('-',$fecha_pago);
			$FechaPago=mktime(0,0,0,$mes,$dia,$anno);
			
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
			$cod_sancion = 1501;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2014
		if ($cot == '115#1')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = $monto*5;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1501;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2014
		if ($cot == '115#2')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = $monto;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1501;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2014
		if ($cot == '115#3')
			{	
			$multa_primitiva = 0;
			
			// FECHA DE VENCIMIENTO
			list($dia,$mes,$anno)=explode('-',$fecha_vencimiento);
			$FechaVen=mktime(0,0,0,$mes,$dia,$anno); 
			// FECHA DE PAGO
			list($dia,$mes,$anno)=explode('-',$fecha_pago);
			$FechaPago=mktime(0,0,0,$mes,$dia,$anno);
			
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
			$cod_sancion = 1501;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		
		// COT 2014
		if ($cot == '115#4')
			{
			// CALCULO DE LA MULTA
			$multa_primitiva = $monto*10;
			$multa = ($multa_primitiva / $_SESSION['VALOR_UT_PRIMITIVA']) * $_SESSION['VALOR_UT_ACTUAL'];
			$cod_sancion = 1501;
			}
		//----------------------------------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx
		return array($multa_primitiva, $multa, $cod_sancion);	}
		
//--------------
function valorEnLetrasNatural($x) 
	{ 
	if ($x<0) { $signo = "menos ";} 
	else      { $signo = "";} 
	$x = abs ($x); 
	$C1 = $x; 
	
	$G6 = floor($x/(1000000));  // 7 y mas 
	
	$E7 = floor($x/(100000)); 
	$G7 = $E7-$G6*10;   // 6 
	
	$E8 = floor($x/1000); 
	$G8 = $E8-$E7*100;   // 5 y 4 
	
	$E9 = floor($x/100); 
	$G9 = $E9-$E8*10;  //  3 
	
	$E10 = floor($x); 
	$G10 = $E10-$E9*100;  // 2 y 1 
	
	
	$G11 = round(($x-$E10)*100,0);  // Decimales 
	////////////////////// 
	
	$H6 = unidades($G6); 
	
	if($G7==1 AND $G8==0) { $H7 = "Cien "; } 
	else {    $H7 = decenas($G7); } 
	
	$H8 = unidades($G8); 
	
	if($G9==1 AND $G10==0) { $H9 = "Cien "; } 
	else {    $H9 = decenas($G9); } 
	
	$H10 = unidades($G10); 
	
	if($G11 < 10) { $H11 = "0".$G11; } 
	else { $H11 = $G11; } 
	
	///////////////////////////// 
		if($G6==0) { $I6=" "; } 
	elseif($G6==1) { $I6="Millón "; } 
			 else { $I6="Millones "; } 
			  
	if ($G8==0 AND $G7==0) { $I8=" "; } 
			 else { $I8="Mil "; } 
			  
	$I10 = "con "; 
	$I11 = "/100 "; 
	
	$C3 = trim($signo.$H6.$I6.$H7.$I7.$H8.$I8.$H9.$I9.$H10); 
	
	return $C3; //Retornar el resultado 
	} 

//--------------
function valorEnLetras($x) 
	{ 
	if ($x<0) { $signo = "menos ";} 
	else      { $signo = "";} 
	$x = abs ($x); 
	$C1 = $x; 
	
	$G6 = floor($x/(1000000));  // 7 y mas 
	
	$E7 = floor($x/(100000)); 
	$G7 = $E7-$G6*10;   // 6 
	
	$E8 = floor($x/1000); 
	$G8 = $E8-$E7*100;   // 5 y 4 
	
	$E9 = floor($x/100); 
	$G9 = $E9-$E8*10;  //  3 
	
	$E10 = floor($x); 
	$G10 = $E10-$E9*100;  // 2 y 1 
	
	
	$G11 = round(($x-$E10)*100,0);  // Decimales 
	////////////////////// 
	
	$H6 = unidades($G6); 
	
	if($G7==1 AND $G8==0) { $H7 = "Cien "; } 
	else {    $H7 = decenas($G7); } 
	
	$H8 = unidades($G8); 
	
	if($G9==1 AND $G10==0) { $H9 = "Cien "; } 
	else {    $H9 = decenas($G9); } 
	
	$H10 = unidades($G10); 
	
	if($G11 < 10) { $H11 = "0".$G11; } 
	else { $H11 = $G11; } 
	
	///////////////////////////// 
		if($G6==0) { $I6=" "; } 
	elseif($G6==1) { $I6="Millón "; } 
			 else { $I6="Millones "; } 
			  
	if ($G8==0 AND $G7==0) { $I8=" "; } 
			 else { $I8="Mil "; } 
			  
	$I10 = "con "; 
	$I11 = "/100 "; 
	
	$C3 = $signo.$H6.$I6.$H7.$I7.$H8.$I8.$H9.$I9.$H10.$I10.$H11.$I11; 
	
	return $C3; //Retornar el resultado 
	} 

//--------------
function unidades($u) 
	{ 
		if ($u==0)  {$ru = " ";} 
	elseif ($u==1)  {$ru = "Un ";} 
	elseif ($u==2)  {$ru = "Dos ";} 
	elseif ($u==3)  {$ru = "Tres ";} 
	elseif ($u==4)  {$ru = "Cuatro ";} 
	elseif ($u==5)  {$ru = "Cinco ";} 
	elseif ($u==6)  {$ru = "Seis ";} 
	elseif ($u==7)  {$ru = "Siete ";} 
	elseif ($u==8)  {$ru = "Ocho ";} 
	elseif ($u==9)  {$ru = "Nueve ";} 
	elseif ($u==10) {$ru = "Diez ";} 
	
	elseif ($u==11) {$ru = "Once ";} 
	elseif ($u==12) {$ru = "Doce ";} 
	elseif ($u==13) {$ru = "Trece ";} 
	elseif ($u==14) {$ru = "Catorce ";} 
	elseif ($u==15) {$ru = "Quince ";} 
	elseif ($u==16) {$ru = "Dieciseis ";} 
	elseif ($u==17) {$ru = "Decisiete ";} 
	elseif ($u==18) {$ru = "Dieciocho ";} 
	elseif ($u==19) {$ru = "Diecinueve ";} 
	elseif ($u==20) {$ru = "Veinte ";} 
	
	elseif ($u==21) {$ru = "Veintiun ";} 
	elseif ($u==22) {$ru = "Veintidos ";} 
	elseif ($u==23) {$ru = "Veintitres ";} 
	elseif ($u==24) {$ru = "Veinticuatro ";} 
	elseif ($u==25) {$ru = "Veinticinco ";} 
	elseif ($u==26) {$ru = "Veintiseis ";} 
	elseif ($u==27) {$ru = "Veintisiente ";} 
	elseif ($u==28) {$ru = "Veintiocho ";} 
	elseif ($u==29) {$ru = "Veintinueve ";} 
	elseif ($u==30) {$ru = "Treinta ";} 
	
	elseif ($u==31) {$ru = "Treinta y un ";} 
	elseif ($u==32) {$ru = "Treinta y dos ";} 
	elseif ($u==33) {$ru = "Treinta y tres ";} 
	elseif ($u==34) {$ru = "Treinta y cuatro ";} 
	elseif ($u==35) {$ru = "Treinta y cinco ";} 
	elseif ($u==36) {$ru = "Treinta y seis ";} 
	elseif ($u==37) {$ru = "Treinta y siete ";} 
	elseif ($u==38) {$ru = "Treinta y ocho ";} 
	elseif ($u==39) {$ru = "Treinta y nueve ";} 
	elseif ($u==40) {$ru = "Cuarenta ";} 
	
	elseif ($u==41) {$ru = "Cuarenta y un ";} 
	elseif ($u==42) {$ru = "Cuarenta y dos ";} 
	elseif ($u==43) {$ru = "Cuarenta y tres ";} 
	elseif ($u==44) {$ru = "Cuarenta y cuatro ";} 
	elseif ($u==45) {$ru = "Cuarenta y cinco ";} 
	elseif ($u==46) {$ru = "Cuarenta y seis ";} 
	elseif ($u==47) {$ru = "Cuarenta y siete ";} 
	elseif ($u==48) {$ru = "Cuarenta y ocho ";} 
	elseif ($u==49) {$ru = "Cuarenta y nueve ";} 
	elseif ($u==50) {$ru = "Cincuenta ";} 
	
	elseif ($u==51) {$ru = "Cincuenta y un ";} 
	elseif ($u==52) {$ru = "Cincuenta y dos ";} 
	elseif ($u==53) {$ru = "Cincuenta y tres ";} 
	elseif ($u==54) {$ru = "Cincuenta y cuatro ";} 
	elseif ($u==55) {$ru = "Cincuenta y cinco ";} 
	elseif ($u==56) {$ru = "Cincuenta y seis ";} 
	elseif ($u==57) {$ru = "Cincuenta y siete ";} 
	elseif ($u==58) {$ru = "Cincuenta y ocho ";} 
	elseif ($u==59) {$ru = "Cincuenta y nueve ";} 
	elseif ($u==60) {$ru = "Sesenta ";} 
	
	elseif ($u==61) {$ru = "Sesenta y un ";} 
	elseif ($u==62) {$ru = "Sesenta y dos ";} 
	elseif ($u==63) {$ru = "Sesenta y tres ";} 
	elseif ($u==64) {$ru = "Sesenta y cuatro ";} 
	elseif ($u==65) {$ru = "Sesenta y cinco ";} 
	elseif ($u==66) {$ru = "Sesenta y seis ";} 
	elseif ($u==67) {$ru = "Sesenta y siete ";} 
	elseif ($u==68) {$ru = "Sesenta y ocho ";} 
	elseif ($u==69) {$ru = "Sesenta y nueve ";} 
	elseif ($u==70) {$ru = "Setenta ";} 
	
	elseif ($u==71) {$ru = "Setenta y un ";} 
	elseif ($u==72) {$ru = "Setenta y dos ";} 
	elseif ($u==73) {$ru = "Setenta y tres ";} 
	elseif ($u==74) {$ru = "Setenta y cuatro ";} 
	elseif ($u==75) {$ru = "Setenta y cinco ";} 
	elseif ($u==76) {$ru = "Setenta y seis ";} 
	elseif ($u==77) {$ru = "Setenta y siete ";} 
	elseif ($u==78) {$ru = "Setenta y ocho ";} 
	elseif ($u==79) {$ru = "Setenta y nueve ";} 
	elseif ($u==80) {$ru = "Ochenta ";} 
	
	elseif ($u==81) {$ru = "Ochenta y un ";} 
	elseif ($u==82) {$ru = "Ochenta y dos ";} 
	elseif ($u==83) {$ru = "Ochenta y tres ";} 
	elseif ($u==84) {$ru = "Ochenta y cuatro ";} 
	elseif ($u==85) {$ru = "Ochenta y cinco ";} 
	elseif ($u==86) {$ru = "Ochenta y seis ";} 
	elseif ($u==87) {$ru = "Ochenta y siete ";} 
	elseif ($u==88) {$ru = "Ochenta y ocho ";} 
	elseif ($u==89) {$ru = "Ochenta y nueve ";} 
	elseif ($u==90) {$ru = "Noventa ";} 
	
	elseif ($u==91) {$ru = "Noventa y un ";} 
	elseif ($u==92) {$ru = "Noventa y dos ";} 
	elseif ($u==93) {$ru = "Noventa y tres ";} 
	elseif ($u==94) {$ru = "Noventa y cuatro ";} 
	elseif ($u==95) {$ru = "Noventa y cinco ";} 
	elseif ($u==96) {$ru = "Noventa y seis ";} 
	elseif ($u==97) {$ru = "Noventa y siete ";} 
	elseif ($u==98) {$ru = "Noventa y ocho ";} 
	else            {$ru = "Noventa y nueve ";} 
	return $ru; //Retornar el resultado 
	} 

//--------------
function decenas($d) 
	{ 
		if ($d==0)  {$rd = "";} 
	elseif ($d==1)  {$rd = "Ciento ";} 
	elseif ($d==2)  {$rd = "Doscientos ";} 
	elseif ($d==3)  {$rd = "Trescientos ";} 
	elseif ($d==4)  {$rd = "Cuatrocientos ";} 
	elseif ($d==5)  {$rd = "Quinientos ";} 
	elseif ($d==6)  {$rd = "Seiscientos ";} 
	elseif ($d==7)  {$rd = "Setecientos ";} 
	elseif ($d==8)  {$rd = "Ochocientos ";} 
	else            {$rd = "Novecientos ";} 
	return $rd; //Retornar el resultado 
	} 

//--------------
//function extraer_iniciales($cadena)
//	{
//	$array = explode(" ", $cadena);
//	$iniciales="";
//	for ($i = 0; $i < count($array); $i++) {
//		$a = substr($array[$i], 0, 1);
//		if (preg_match('/'.$a.'\b/', 'A B C D E F G H I J K L M N Ñ O P Q R S T U V W X Y Z')) 
//		{
//			$iniciales= $iniciales.$a;
//		}
//	}
//	return $iniciales;
//	}
	
?>