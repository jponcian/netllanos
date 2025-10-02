<?php
//**** SCRIPT PARA GENERAR LOS CASOS EN PROCESO  ****//
//                                                   //
//    Elaborado por Gustavo García para el SENIAT    //
//                                                   //
///////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");
include("../funciones/func.contador.php");

//Variables a utilizar
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];

//RECORREMOS LAS PROVIDENCIAS NOTIFICADAS EN EL AÑO y las agregamos a Casos_en_Proceso
//$sqlnot = "CALL Providencias_Notificadas( $inicio, $fin )";
$sqlnot = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector, a_tipo_programa.descripcion, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.ci_fiscal1, CONCAT_WS(' ',fiscal.Nombres,fiscal.Apellidos) as fiscal, expedientes_fiscalizacion.ci_supervisor, CONCAT_WS(' ',supervisor.Nombres,supervisor.Apellidos) as supervisor, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, z_sectores.nombre, a_tipo_programa.tipo, a_tipo_programa.clasificacion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS fiscal ON fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS supervisor ON supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.fecha_notificacion IS NOT NULL AND expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."'"; //echo $sqlnot.'<br>';
$tabla_not = $con->query($sqlnot);
while ($not = $tabla_not->fetch_object())
{
	$sqlcp = "SELECT anno, numero, sector FROM casos_en_proceso WHERE anno = ".$not->anno." AND numero = ".$not->numero." AND sector = ".$not->sector;
	$tablacp = $conexion->query($sqlcp);
	$existe = $tablacp->num_rows;
	if ($existe < 1)
	{
        $desc_formato = desc_formato_cp($not->clasificacion);
        $formatocp = formato_cp($not->clasificacion);
        //echo $formatocp.' --------------- '.'<br>';
		$sqlinsertcp = "INSERT INTO casos_en_proceso (anno, numero, sector, descripcion, rif, contribuyente, ci_fiscal, fiscal, ci_supervisor, supervisor, emision, notificacion, nombre_sector, tipo, clasificacion, desc_formato, formato_cp) VALUES (".$not->anno.", ".$not->numero.", ".$not->sector.", '".$not->descripcion."', '".$not->rif."', '".$not->contribuyente."', ".$not->ci_fiscal1.", '".$not->fiscal."', ".$not->ci_supervisor.", '".$not->supervisor."', '".$not->fecha_emision."', '".$not->fecha_notificacion."', '".$not->nombre."', '".$not->tipo."', '".$not->clasificacion."', '".$desc_formato."', '".$formatocp."')";
		$tabla_insert = $conexion->query($sqlinsertcp);
		//echo 'No Existe: '.$existe.' - '.$clasificacion.'<br/>';		

		if ($conexion->affected_rows){
			$mensaje = "Informe Generado Satisfactoriamente";
			$permitido = true;
		}

	}

}

//VERIFICAMOS CUALES HAN SIDO CULMINADAS Y REGISTRAMOS LA CONCLUSION
$sql_cp = "SELECT anno, numero, sector, notificacion FROM casos_en_proceso";
$tabla_cp = $conexion->query($sql_cp);
while ($cp = $tabla_cp->fetch_object())
{
	if ($cp->notificacion < '2018-01-01')
	{
		$sql_salida = "SELECT sector AS sector, Anno_Providencia AS anno, NroAutorizacion AS numero, FechaEmision FROM ct_salida_expediente WHERE Anno_Providencia = ".$cp->anno." AND NroAutorizacion = ".$cp->numero." AND sector = ".$cp->sector;
	}
	else
	{
		$sql_salida = "SELECT sector AS sector, Anno_Providencia AS anno, NroAutorizacion AS numero, FechaEmision FROM ct_salida_expediente WHERE Anno_Providencia = ".$cp->anno." AND NroAutorizacion = ".$cp->numero." AND sector = ".$cp->sector." AND Status in (11,21,25,31,91,92,94)";
	}
	$tabla_sal = $con->query($sql_salida);
	$existe_sal = $tabla_sal->num_rows;
	if ($existe_sal > 0)
	{
		$valor = $tabla_sal->fetch_object();
		$sql_update = "UPDATE casos_en_proceso SET borrado = 1, fecha_borrado = '".$valor->FechaEmision."' WHERE anno = ".$cp->anno." AND numero = ".$cp->numero." AND sector = ".$cp->sector;
		$tabla_update = $conexion->query($sql_update);	

		if ($conexion->affected_rows){
			$mensaje = "Informe Generado Satisfactoriamente";
			$permitido = true;
		}

	}
}

//MARCAMOS LOS ESTATUS DE LOS CASOS EN PROCESO
$sql = "SELECT anno, numero, sector, clasificacion FROM casos_en_proceso WHERE borrado = 0";
$tabla = $conexion->query($sql);
while ($reg = $tabla->fetch_object())
{
    $tipo = $reg->clasificacion;
    $desc_formato = desc_formato_cp($tipo);
    $formatocp = formato_cp($tipo);
    if ($tipo == "FIR" or $tipo == "FIN" or $tipo == "PT" or $tipo == "FPN" or $tipo == "FPR")
    {
        $fecha2 = date("Y/m/d");
        //BUSCAMOS SI TIENE FECHA DE NOTIFICACION EL ACTA DENTRO DE LOS 15 DIAS
        $bSQL = "SELECT fecha_notificacion as fecha FROM fis_actas WHERE anno_prov = ".$reg->anno." AND num_prov = ".$reg->numero." AND id_sector = ".$reg->sector;
        $bTabla = $con->query($bSQL);
        $reg0 = $bTabla->fetch_object();
        $fecha1 = $reg0->fecha;
        $dias = dias($fecha1, $fecha2);

        if ($dias < 20)
        {
            $lapso_allanamiento = 1;
            $proceso_auditoria = 0;
            $nivel_supervisor = 0;
            $otras_causas = 0;
        }
        else
        {
            $bSQLa = "SELECT status as estatus FROM expedientes_fiscalizacion WHERE anno = ".$reg->anno." AND numero = ".$reg->numero." AND sector = ".$reg->sector;
            $bTablaa = $con->query($bSQLa);
            $rega = $bTablaa->fetch_object();
            if ($rega->estatus == 6)
            {
                $lapso_allanamiento = 0;
                $proceso_auditoria = 0;
                $nivel_supervisor = 1;
                $otras_causas = 0;
            }
            else
            {
                $lapso_allanamiento = 0;
                $proceso_auditoria = 1;
                $nivel_supervisor = 0;
                $otras_causas = 0;
            }
        }       
    }
    else
    {
        $bSQL = "SELECT status as estatus FROM expedientes_fiscalizacion WHERE anno = ".$reg->anno." AND numero = ".$reg->numero." AND sector = ".$reg->sector;
        $bTabla = $con->query($bSQL);
        $regb = $bTabla->fetch_object();
        if ($regb->estatus == 6)
        {
            $lapso_allanamiento = 0;
            $proceso_auditoria = 0;
            $nivel_supervisor = 1;
            $otras_causas = 0;
        }
        else
        {
            $lapso_allanamiento = 0;
            $proceso_auditoria = 1;
            $nivel_supervisor = 0;
            $otras_causas = 0;
        }
    }
    $SQLu = "UPDATE casos_en_proceso SET proceso_auditoria = ".$proceso_auditoria.",  nivel_supervisor = ".$nivel_supervisor.", lapso_allanamiento = ".$lapso_allanamiento.", otras_causas = ".$otras_causas." WHERE anno = ".$reg->anno." AND numero = ".$reg->numero." AND sector = ".$reg->sector;
    //echo $SQLu.'<br>';
    $tablau = $conexion->query($SQLu);

	if ($conexion->affected_rows){
		$mensaje = "Informe Generado Satisfactoriamente";
		$permitido = true;
	}

}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

function dias($fecha1, $fecha2)
{
    $fecha1 = strtotime($fecha1);
    $fecha2 = strtotime($fecha2);
	$fecha1 = date("Y/m/d", $fecha1);
	$fecha2 = date("Y/m/d", $fecha2);
	$date2 = strtotime($fecha2);
	$date1 = strtotime($fecha1);
	$diff = abs($date2 - $date1);
	$dias = floor($diff / (24*60*60));
    return $dias;
}

function desc_formato_cp($tipo)
{
    switch ($tipo) 
	{
		case "FIN":
			$desc_formato = "FISCALIZACION INTEGRAL";
			break;
		case "FIR":
			$desc_formato = "FISCALIZACION INTEGRAL";
			break;
		case "PT":
			$desc_formato = "PRECIO DE TRANSFERENCIA";
			break;
		case "ITF":
			$desc_formato = "TRANSACCIONES FINANCIERAS";
			break;
		case "FPN":
			$desc_formato = "FISCALIZACION PUNTUAL NACIONAL";
			break;
		case "FPR":
			$desc_formato = "FISCALIZACION PUNTUAL REGIONAL";
			break;
		case "VN":
			$desc_formato = "VERIFICACION NACIONAL";
			break;
		case "VR";
			$desc_formato = "VERIFICACION REGIONAL";
			break;
		case "OP";
			$desc_formato = "OTROS PROGRAMAS";
			break;
	}
    return $desc_formato;
}

function formato_cp($tipo)
{
    switch ($tipo) 
	{
		case "FIN":
		case "FIR":
            $desc_formato_cp = "FISCALIZACION INTEGRAL O GENERAL";
			break;
		case "PT":
            $desc_formato_cp = "FISCALIZACION EN MATERIA DE PRECIOS DE TRANSFERENCIA";
			break;
		case "ITF":
            $desc_formato_cp = "FISCALIZACION EN MATERIA DE TRANSACCIONES FINANCIERAS";
			break;
		case "FPN":
		case "FPR":
            $desc_formato_cp = "FISCALIZACION PUNTUAL";
			break;
		case "VN":
		case "VR";
            $desc_formato_cp = "VERIFICACION";
			break;
		case "OP";
            $desc_formato_cp = "OTROS PROGRAMAS";
			break;
	}
    return $desc_formato_cp;
}
?>