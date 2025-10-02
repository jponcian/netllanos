<?php
session_start();

include "../conexion.php";
include "../funciones/auxiliar_php.php";


function Consultar_providencia($año,$numero,$sector)
{
	$CSQL = "SELECT * FROM vista_providencias_notificadas WHERE numero=".$numero." and anno=".$año." and sede='".$sector."'";
	return $CSQL;
}

$info = array();
$mensaje = "";
$permitido = false;

//BUSCAMOS EL SECTOR
$sqlsector = "SELECT id_sector, nombre FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
$result_sector = mysql_query($sqlsector);
$valor_sector = mysql_fetch_object($result_sector);
$sector = $valor_sector->nombre;
//-------------------------------

$añoprov= $_POST['añoProvidencia'];
$numprov= $_POST['numProvidencia'];

$año= $añoprov;
$numero= $numprov;
//$sector= $_SESSION['SEDE_USUARIO'];

$mensaje = "Año: ".$año. " Y Providencia: ".$numero;		

$Bssql = "SELECT Anno_Providencia,NroAutorizacion FROM fis_anexo2 WHERE Anno_Providencia=".$año." and NroAutorizacion=".$numero." and Sede='".$sector."'"; 
$rs_access = mysql_query($Bssql);
if ($fila = mysql_fetch_object($rs_access))
{
	$mensaje='!!!...Providencia YA Registrada, por favor verifique...!!!';
	$permitido=false;
}
else
{
	$Pssql = Consultar_providencia($año,$numero,$sector);
	
	if($brs_access = mysql_query($Pssql))
	{
		if ($fila_reg = mysql_fetch_object($brs_access))
		{
			$db_año= $fila_reg->anno;
			$db_numero= $fila_reg->numero;
			$db_emision= date("Y-m-d", strtotime($fila_reg->fechaemision));
			$db_notificacion= date("d-m-Y", strtotime($fila_reg->fechanotificacion));
			$db_nombre= utf8_encode($fila_reg->contribuyente);
			$db_rif= $fila_reg->rif;
			$db_domicilio= utf8_encode($fila_reg->domicilio);
			$db_cedfiscal= $fila_reg->cedulafiscal;
			$db_fiscal= $fila_reg->fiscal;
			$db_cedsuper= $fila_reg->cedulasupervisor;
			$db_super= $fila_reg->supervisor;
			$db_programa= $fila_reg->programa;
			$db_tributo= $fila_reg->tributo;
			$db_operativo = $fila_reg->NombreOperativo;
			
			
			

			$sql_coord = "Select * FROM Coordinador";
			if ($coord_access = mysql_query($sql_coord))
			{
				if ($fila_coord = mysql_fetch_object($coord_access)) 
				{
					$CI_Coord= $fila_coord->Cedula;
					$NOM_Coord= $fila_coord->Apellidos." ".$fila_coord->Nombres;
					$TLF_Coord= $fila_coord->Telefono;
				}
			}
			else
			{
					$CI_Coord= 8632565;
					$NOM_Coord= "GUSTAVO GARCIA";
					$TLF_Coord= "8709537";	
			}
			$permitido = true;		
			$mensaje = "AQUiiiiiii";
		}
		else
		{
			$mensaje='!!!...Providencia NO Notificada, por favor verifique...!!!';
			$permitido=false;
		}
	}
}

$info = array(
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
	'db_operativo' => $db_operativo);

echo json_encode($info);

?>
