<?php
switch ($_SESSION['ORIGEN'])
	{
	// ESPECIALES
    case 2: 
		///////// DEVOLVER LIQUIDACIONES
		$consulta = "UPDATE liquidacion SET status = 0, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']."  WHERE id_resolucion<=0 AND status=".$status." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// -------------- ACTUALIZACION DEL EXPEDIENTE
		$consulta="UPDATE expedientes_especiales SET status=6, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']." WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta); 
		// --------------
        break;
	// SUCESIONES
    case 3:
		///////// DEVOLVER LIQUIDACIONES
		$consulta = "UPDATE liquidacion SET status = 0, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']."  WHERE id_resolucion<=0 AND status=".$status." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// -------------- ACTUALIZACION DEL EXPEDIENTE
		$consulta="UPDATE expedientes_sucesiones SET status=6, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']." WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta); 
		// --------------
        break;
	// FISCALIZACION
    case 4:
		///////// DEVOLVER LIQUIDACIONES
		$consulta = "UPDATE liquidacion SET status = 0, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']."  WHERE id_resolucion<=0 AND status=".$status." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// -------------- ACTUALIZACION DEL EXPEDIENTE
		$consulta="UPDATE expedientes_fiscalizacion SET status=6, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']." WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta); 
		// --------------
		///////// ELIMINAR LIQUIDACIONES POR ACTAS DE REPARO
		$consulta = "DELETE FROM liquidacion WHERE id_sancion IN (568, 1503, 11503, 15449, 15449, 1502, 1507, 11502, 11507, 556, 567, 15567, 1504, 11504, 1509, 1508, 11509, 11508, 683, 10683, 2009) AND id_resolucion<=0 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
        break;
	// AJUSTES ESPECIALES
	case 7:
		///////// DEVOLVER LIQUIDACIONES
		$consulta = "UPDATE liquidacion SET status = 9, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']."  WHERE id_resolucion<=0 AND status=".$status." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// -------------- ACTUALIZACION DEL EXPEDIENTE
		$consulta="UPDATE expedientes_ajustes_ut SET status=6, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']." WHERE  origen_exp=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// --------------
        break;
	// RIF
    case 12:
		///////// DEVOLVER LIQUIDACIONES
		$consulta = "UPDATE liquidacion SET status = 0, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']."  WHERE id_resolucion<=0 AND status=".$status." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// -------------- ACTUALIZACION DEL EXPEDIENTE
		$consulta="UPDATE expedientes_rif SET status=6, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']." WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta); 
		// --------------
        break;
	// COBRO
    case 13: 
		///////// DEVOLVER LIQUIDACIONES
		$consulta = "UPDATE liquidacion SET status = 0, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']."  WHERE id_resolucion<=0 AND status=".$status." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// -------------- ACTUALIZACION DEL EXPEDIENTE
		$consulta="UPDATE expedientes_cobro SET status=6, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']." WHERE anno=".$_SESSION['ANNO_PRO']." AND numero=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta); 
		// --------------
        break;
	// AJUSTE UT
    case 16:
		///////// DEVOLVER LIQUIDACIONES
		$consulta = "UPDATE liquidacion SET status = 9, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']."  WHERE id_resolucion<=0 AND status=".$status." AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// -------------- ACTUALIZACION DEL EXPEDIENTE
		$consulta="UPDATE expedientes_ajustes_ut SET status=6, fecha_devolucion = date(now()), usuario_devolucion = ".$_SESSION['CEDULA_USUARIO']." WHERE  origen_exp=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE'].";";
		$tabla = mysql_query($consulta);
		// --------------
        break;
	}

?>