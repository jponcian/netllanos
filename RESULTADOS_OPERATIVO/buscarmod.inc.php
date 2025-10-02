<?php
session_start();

include "../conexion.php";
include "../funciones/auxiliar_php.php";


$json = array();
$mensaje = "";
$permitido = false;

//BUSCAMOS EL SECTOR
$sqlsector = "SELECT id_sector, nombre FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
$result_sector = mysql_query($sqlsector);
$valor_sector = mysql_fetch_object($result_sector);
$sector = $valor_sector->nombre;
//-------------------------------

$año= $_POST['añoProvidencia'];
$numero= $_POST['numProvidencia'];

$Bssql = "SELECT * FROM fis_anexo2 WHERE Anno_Providencia=".$año." and NroAutorizacion=".$numero." and Sede='".$sector."'";

if($rs_access = mysql_query($Bssql))
{ 
	if ($fila = mysql_fetch_object($rs_access))
	{
		$db_año= $fila->Anno_Providencia;
		$db_numero= $fila->NroAutorizacion;
		$db_emision= date("Y-m-d", strtotime($fila->FechaEmision));
		$db_notificacion= date("d-m-Y", strtotime($fila->FechaNotificacion));
		$db_nombre= $fila->Nombre;
		$db_rif=$fila->Rif;
		$db_domicilio= $fila->Domicilio;
		$db_cedfiscal= $fila->CedulaFiscal;
		$db_fiscal= $fila->Fiscal_Actuante;
		$db_cedsuper= $fila->CedulaSupervisor;
		$db_super= $fila->Supervisor;
		$db_programa= $fila->Programa;
		$db_tributo= $fila->Tributo;
		$CI_Coord= $fila->CedulaCoord;
		$NOM_Coord= $fila->Coordinador;
		$TLF_Coord= $fila->Tlf_Contacto_Coord;
		$CantSucursal= $fila->Cant_sucursales;
		$CadenaTienda= $fila->Cadena_Tienda;
		$Actividad= $fila->Actividad_Economica;
		$TipoMF=$fila->Tipo_Maq_fiscal;
		$ModeloMF= $fila->Mod_Maq_fiscal;
		$CumpleMF= $fila->MF_Cumple_Req;
		$SancionesMF= $fila->MF_Incumplimientos;
		$MultasDF= $fila->DF_Multas;
		$FechaOperativo=date("d-m-Y", strtotime($fila->FechaOperativo));
		$NombreOperativo=$fila->NombreOperativo;
		if ($MultasDF=="" or $MultasDF=="NO")
		{	
			$Clausura="";
			$DiasClausura="";
			$MontoSanciones=0;
			$NotificacionCierre="";
		}
		else
		{
			$Clausura=$fila->DF_Clausura;
			$DiasClausura= $fila->Dias_clausura;
			$MontoSanciones=$fila->MontoMultas;
			if ($fila->Res_Clausura_Notificacion=="")
			{
				$NotificacionCierre="";
			}
			else
			{
				$NotificacionCierre=date("d-m-Y", strtotime($fila->Res_Clausura_Notificacion));
			}
			//echo $monto;
		}
		if ($db_clausuras=="SI")
			{
				$NotificacionCierre= date("d-m-Y", strtotime($fila->Res_Clausura_Notificacion));
				$DiasClausura=$fila->Dias_clausura;;
			}
		$Sanciones=$fila->DF_Incumplimientos;
		$Observaciones=$fila->Observaciones;

		//ANEXO 2 ARTICULADO

		$SQLArt2 = "SELECT Count(*) AS counter FROM fis_anexo2_ART WHERE Anno=".$año." and NroAutorizacion=".$numero." and Sede='".$sector."'";
		$rs = mysql_query($SQLArt2);
	    $arr = mysql_fetch_array($rs);
	    $rows = $arr['counter'];
	
	    if ($rows > 0)
	    {
			$SQLart = "SELECT * FROM fis_anexo2_art WHERE Anno=".$año." and NroAutorizacion=".$numero." and Sede='".$sector."'";

			if($rs_art = mysql_query($SQLart))
			{ 
				if ($fila_art = mysql_fetch_object($rs_art))
				{
					$db_100 = $fila_art->art100;
					$db_101 = $fila_art->art101;
					$db_102 = $fila_art->art102;
					$db_103 = $fila_art->art103;
					$db_104 = $fila_art->art104;
					$db_105 = $fila_art->art105;
					$db_106 = $fila_art->art106;
					$db_107 = $fila_art->art107;
					$db_108 = $fila_art->art108;
					$db_id = $fila_art->id;
				}
			}
		}
		else
		{
					$db_100 = '0000';
					$db_101 = '00000000000';
					$db_102 = '00000000';
					$db_103 = '0000000';
					$db_104 = '00000000000000';
					$db_105 = '00000';
					$db_106 = '000';
					$db_107 = '0000';
					$db_108 = '0';
					$db_id = 0;
		}

		$permitido = true;		
		$mensaje = "";
	}
	else
	{
		$mensaje='!!!...Providencia NO Registrada, por favor verifique...!!!';
		$permitido=false;
	}
}

$json = array(
	'mensaje' => $mensaje,
	'permitido' => $permitido,
	'db_año' => $db_año,
	'db_numero' => $db_numero,
	'db_emision' => $db_emision,
	'db_notificacion' => $db_notificacion,
	'db_nombre' => $db_nombre,
	'db_rif' => $db_rif,
	'db_domicilio' => $db_domicilio,
	'db_cedfiscal' => $db_cedfiscal,
	'db_fiscal' => $db_fiscal,
	'db_cedsuper' => $db_cedsuper,
	'db_super' => $db_super,
	'db_programa' => $db_programa,
	'db_tributo' => $db_tributo,
	'CI_Coord' => $CI_Coord,
	'NOM_Coord' => $NOM_Coord,
	'TLF_Coord' => $TLF_Coord,
	'CantSucursal' => $CantSucursal,
	'CadenaTienda' => $CadenaTienda,
	'Actividad' => $Actividad,
	'TipoMF' => $TipoMF,
	'ModeloMF' => $ModeloMF,
	'CumpleMF' => $CumpleMF,
	'SancionesMF' => $SancionesMF,
	'MultasDF' => $MultasDF,
	'FechaOperativo' => $FechaOperativo,
	'NombreOperativo' => $NombreOperativo,
	'Clausura' => $Clausura,
	'DiasClausura' => $DiasClausura,
	'MontoSanciones' => $MontoSanciones,
	'NotificacionCierre' => $NotificacionCierre,
	'Sanciones' => $Sanciones,
	'Observaciones' => $Observaciones,
	'db_100' => $db_100,
	'db_101' => $db_101,
	'db_102' => $db_102,
	'db_103' => $db_103,
	'db_104' => $db_104,
	'db_105' => $db_105,
	'db_106' => $db_106,
	'db_107' => $db_107,
	'db_108' => $db_108,
	'db_id' => $db_id
);

echo json_encode($json);

?>
