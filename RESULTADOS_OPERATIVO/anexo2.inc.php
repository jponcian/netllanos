<?php
session_start();

include "../conexion.php";
include "../funciones/auxiliar_php.php";


$json = array();
$mensaje = "";
$permitido = false;

$guardar="0";
$añoprov= $_POST['añoProvidencia'];
$numprov= $_POST['numProvidencia'];
$emision= $_POST['Emision'];
$notificacion= voltea_fecha($_POST['Notificacion']);
$nombre= strtoupper($_POST['Nombre']);
$rif= $_POST['Rif'];
$domicilio= strtoupper($_POST['Domicilio']);
$cedfiscal= $_POST['CedFiscal'];
$fiscal= strtoupper($_POST['Fiscal']);
$cedsuper= $_POST['CedSuper'];
$super= strtoupper($_POST['Super']);
$programa= $_POST['Programa'];
$tributo= $_POST['Tributo'];
$cedcoord= $_POST['CedCoord'];
$coord= strtoupper($_POST['Coord']);
$tlfcoord= $_POST['TlfCoord'];
$can_sucursal= $_POST['CantSucursal'];
$cadenatienda= strtoupper($_POST['CadenaTienda']);
$actividad= strtoupper($_POST['Actividad']);
$tipoMF=$_POST['TipoMF'];
$modeloMF= strtoupper($_POST['ModeloMF']);
$cumpleMF= strtoupper($_POST['CumpleMF']);
$FechaOperativo = voltea_fecha($_POST['FechaOperativo']);
$NombreOperativo = strtoupper($_POST['NombreOperativo']);
$sancionesMF= strtoupper($_POST['SancionesMF']);
$multas=$_POST['MultasDF'];
if (isset($_POST['nacional']))
{
	$tipoprograma = "NACIONAL";
} else {
	$tipoprograma = "REGIONAL";
}

if ($_POST['MultasDF']=="" or $_POST['MultasDF']=="NO")
{	
    $clausuras="";
    $diasclausuras="";
    $monto=0;
    $notificacionclausura="";
} else {
    $sancionesMF= strtoupper($_POST['SancionesMF']);
    $clausuras=$_POST['Clausura'];
    $monto= str_replace(".",",",$_POST['MontoSanciones']);
    //echo $monto;
}

if ($clausuras=="SI")
{
    $notificacionclausura= voltea_fecha($_POST['NotificacionCierre']);
    $diasclausuras=$_POST['DiasClausura'];
}

$sancionesDF=strtoupper($_POST['Sanciones']);
$observaciones=strtoupper($_POST['Observaciones']);

if ($añoprov==""){$guardar="1";}
if ($numprov==""){$guardar="1";}
if ($FechaOperativo==""){$guardar="1";}
if ($NombreOperativo==""){$guardar="1";}
if ($can_sucursal==""){$guardar="1";}
if ($actividad==""){$guardar="1";}
if ($multas==""){$guardar="1";}
//if ($clausuras==""){$guardar="1";}
//echo "AÑO: ".$añoprov."-NUMERO: ".$numprov."-EMISION: ".$emision."-NOTIFICACION: ".$notificacion."-NOMBRE: ".$nombre."-RIF: ".$rif."-DOMICILIO: ".$domicilio."-SUC: ".$can_sucursal."-CADENA: ".$cadenatienda."-ACT: ".$actividad."-CIF: ".$cedfiscal."-FISC: ".$fiscal."-CIS: ".$cedsuper."-SUP: ".$super."-CICOORD: ".$cedcoord."-COORD: ".$coord."-TLF: ".$tlfcoord."-TIPOMF: ".$tipoMF."-MOD MF: ".$modeloMF."-CumpleMF: ".$cumpleMF."-SANCiones MF: ".$sancionesMF."-INC DF: ".$multas."-CIERRE: ".$clausuras."-DIAS: ".$diasclausuras."-RES CIE: ".$notificacionclausura."-MONTO: ".$monto."-INC DF: ".$sancionesDF."-OBS: ".$observaciones."-PROG: ".$programa."-TRIB: ".$tributo;

//BUSCAMOS EL SECTOR
$sqlsector = "SELECT id_sector, nombre FROM z_sectores WHERE id_sector=".$_SESSION['SEDE_USUARIO'];
$result_sector = mysql_query($sqlsector);
$valor_sector = mysql_fetch_object($result_sector);
$sector = $valor_sector->nombre;
//-------------------------------

if ($guardar=="1")
{
    $mensaje = '!!!...Existen campos vacios, por favor verifique...!!!';
    $permitido = false;
} else {	
    if ($notificacionclausura=="")
    {
		//$sancionesDF=strtoupper($_POST['SancionesMF']);
        $consulta = "INSERT INTO fis_anexo2 (Anno_Providencia,NroAutorizacion,FechaEmision,FechaNotificacion,Nombre,Rif,Domicilio,Cant_sucursales,Cadena_Tienda,Actividad_Economica,CedulaFiscal,Fiscal_Actuante,CedulaSupervisor,Supervisor,CedulaCoord,Coordinador,Tlf_Contacto_Coord,Tipo_Maq_fiscal,Mod_Maq_fiscal,MF_Cumple_Req,MF_Incumplimientos,DF_Multas,DF_Clausura,Dias_clausura,MontoMultas,DF_Incumplimientos,Observaciones,Programa,Tributo,FechaProceso,FechaOperativo,NombreOperativo,Sede,TipoOperativo) VALUES  ('".$añoprov."','".$numprov."','".date("Y-m-d",strtotime($emision))."','".date("Y-m-d",strtotime($notificacion))."','".$nombre."','".$rif."','".$domicilio."','".$can_sucursal."','".$cadenatienda."','".$actividad."','".$cedfiscal."','".$fiscal."','".$cedsuper."','".$super."','".$cedcoord."','".$coord."','".$tlfcoord."','".$tipoMF."','".$modeloMF."','".$cumpleMF."','".$sancionesMF."','".$multas."','".$clausuras."','".$diasclausuras."','".$monto."','".$sancionesDF."','".$observaciones."','".$programa."','".$tributo."','".date("Y-m-d")."','".date("Y-m-d",strtotime($FechaOperativo))."','".$NombreOperativo."','".$sector."','".$tipoprograma."')";
    }
    else {
        $consulta = "INSERT INTO fis_anexo2 (Anno_Providencia,NroAutorizacion,FechaEmision,FechaNotificacion,Nombre,Rif,Domicilio,Cant_sucursales,Cadena_Tienda,Actividad_Economica,CedulaFiscal,Fiscal_Actuante,CedulaSupervisor,Supervisor,CedulaCoord,Coordinador,Tlf_Contacto_Coord,Tipo_Maq_fiscal,Mod_Maq_fiscal,MF_Cumple_Req,MF_Incumplimientos,DF_Multas,DF_Clausura,Dias_clausura,Res_Clausura_Notificacion,MontoMultas,DF_Incumplimientos,Observaciones,Programa,Tributo,FechaProceso,FechaOperativo,NombreOperativo,Sede,TipoOperativo) VALUES  ('".$añoprov."','".$numprov."','".date("Y-m-d",strtotime($emision))."','".date("Y-m-d",strtotime($notificacion))."','".$nombre."','".$rif."','".$domicilio."','".$can_sucursal."','".$cadenatienda."','".$actividad."','".$cedfiscal."','".$fiscal."','".$cedsuper."','".$super."','".$cedcoord."','".$coord."','".$tlfcoord."','".$tipoMF."','".$modeloMF."','".$cumpleMF."','".$sancionesMF."','".$multas."','".$clausuras."','".$diasclausuras."','".date("Y-m-d",strtotime($notificacionclausura))."','".$monto."','".$sancionesDF."','".$observaciones."','".$programa."','".$tributo."','".date("Y-m-d")."','".date("Y-m-d",strtotime($FechaOperativo))."','".$NombreOperativo."','".$sector."','".$tipoprograma."')";
    }
    //echo $consulta;
    //$conn_access = odbc_connect ($dbsector,"Administrador","losllanos");
    //$tabla = mysql_query($consulta);

    if($tabla = mysql_query($consulta))
    {
        if (mysql_result)
        {
			$mensaje = '!!!...Registo guardado con exito...!';
        	$permitido = true;
        }
    } else {
		$mensaje = '!!!...Problemas al guardar el registro...!';
    	$permitido = false;
    }

}

$json = array('permitido' => $permitido,
				'mensaje' => $mensaje);

echo json_encode($json);
?>