<?php
session_start();
//include "../CONTROL_TRIBUTARIO/siger_fiscalizacion/conexion.php";
error_reporting(0);
set_time_limit(0);

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'db_siger_fiscalizacion';

//Conectamos a MySQLi	
$conexion = new mysqli($hostname, $username, $password, $database);
$conexion->query("SET NAMES 'utf8'");
$con = new mysqli($hostname, $username, $password, 'losllanos');
$con->query("SET NAMES 'utf8'");

include "../../funciones/auxiliar_php.php";
mysql_query("SET NAMES 'utf8'");
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Casos en Proceso A&ntilde;o Actual";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $titulo ?></title>
	<link rel="stylesheet" type="text/css" href="estilos.css" />
</head>

<body style="background: transparent !important;">
	<?php
	$inicio = $_GET['inicio'];
	$fin = $_GET['fin'];
	$sede = $_GET['sede'];

	switch ($sede) {
		case 0:
			$sector = " AND casos_en_proceso.sector";
			break;
		default:
			$sector = " AND casos_en_proceso.sector = " . $sede;
			break;
	}

	//ACTUALIZACION DE LOS CASOS EN PROCESO
	//RECORREMOS LAS PROVIDENCIAS NOTIFICADAS EN EL AÑO y las agregamos a Casos_en_Proceso
	$actualizar = $con->query('CALL ACT_DATOS_CONTRIBUYENTE()');
	$fechai = "2018/01/01";
	$sqlnot = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector, a_tipo_programa.descripcion, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.ci_fiscal1, CONCAT_WS(' ',fiscal.Nombres,fiscal.Apellidos) as fiscal, expedientes_fiscalizacion.ci_supervisor, CONCAT_WS(' ',supervisor.Nombres,supervisor.Apellidos) as supervisor, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, z_sectores.nombre, a_tipo_programa.tipo, a_tipo_programa.clasificacion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS fiscal ON fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS supervisor ON supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.fecha_notificacion IS NOT NULL AND expedientes_fiscalizacion.fecha_notificacion BETWEEN '" . $fechai . "' AND '" . periodo_final() . "'";
	//echo $sqlnot.'<br>';
	$tabla_not = $con->query($sqlnot);
	while ($not = $tabla_not->fetch_object()) {
		$sqlcp = "SELECT anno, numero, sector FROM casos_en_proceso WHERE anno = " . $not->anno . " AND numero = " . $not->numero . " AND sector = " . $not->sector;
		$tablacp = $conexion->query($sqlcp);
		$existe = $tablacp->num_rows;
		if ($existe < 1) {
			$desc_formato = desc_formato_cp($not->clasificacion);
			$formatocp = formato_cp($not->clasificacion);
			//echo $formatocp.' --------------- '.'<br>';
			$sqlinsertcp = "INSERT INTO casos_en_proceso (anno, numero, sector, descripcion, rif, contribuyente, ci_fiscal, fiscal, ci_supervisor, supervisor, emision, notificacion, nombre_sector, tipo, clasificacion, desc_formato, formato_cp) VALUES (" . $not->anno . ", " . $not->numero . ", " . $not->sector . ", '" . $not->descripcion . "', '" . $not->rif . "', '" . $not->contribuyente . "', " . $not->ci_fiscal1 . ", '" . $not->fiscal . "', " . $not->ci_supervisor . ", '" . $not->supervisor . "', '" . $not->fecha_emision . "', '" . $not->fecha_notificacion . "', '" . $not->nombre . "', '" . $not->tipo . "', '" . $not->clasificacion . "', '" . $desc_formato . "', '" . $formatocp . "')";
			$tabla_insert = $conexion->query($sqlinsertcp);
			//echo 'No Existe: '.$existe.' - '.$clasificacion.'<br/>';		

			if ($conexion->affected_rows) {
				$mensaje = "Informe Generado Satisfactoriamente";
				$permitido = true;
			}
		}
	}

	//VERIFICAMOS CUALES HAN SIDO CULMINADAS Y REGISTRAMOS LA CONCLUSION
	$sql_cp = "SELECT anno, numero, sector, notificacion FROM casos_en_proceso";
	$tabla_cp = $conexion->query($sql_cp);
	while ($cp = $tabla_cp->fetch_object()) {
		if ($cp->notificacion < '2018-01-01') {
			$sql_salida = "SELECT sector AS sector, Anno_Providencia AS anno, NroAutorizacion AS numero, FechaEmision FROM ct_salida_expediente WHERE Anno_Providencia = " . $cp->anno . " AND NroAutorizacion = " . $cp->numero . " AND sector = " . $cp->sector;
		} else {
			$sql_salida = "SELECT sector AS sector, Anno_Providencia AS anno, NroAutorizacion AS numero, FechaEmision FROM ct_salida_expediente WHERE Anno_Providencia = " . $cp->anno . " AND NroAutorizacion = " . $cp->numero . " AND sector = " . $cp->sector . " AND Status in (11,21,25,31,35,91,92,94)"; //echo $sql_salida;
		}
		$tabla_sal = $con->query($sql_salida);
		$existe_sal = $tabla_sal->num_rows;
		if ($existe_sal > 0) {
			$valor = $tabla_sal->fetch_object();
			$sql_update = "UPDATE casos_en_proceso SET borrado = 1, fecha_borrado = '" . $valor->FechaEmision . "' WHERE anno = " . $cp->anno . " AND numero = " . $cp->numero . " AND sector = " . $cp->sector; //echo $sql_update;
			$tabla_update = $conexion->query($sql_update);

			if ($conexion->affected_rows) {
				$mensaje = "Informe Generado Satisfactoriamente";
				$permitido = true;
			}
		}
	}

	//MARCAMOS LOS ESTATUS DE LOS CASOS EN PROCESO
	$sql = "SELECT anno, numero, sector, clasificacion FROM casos_en_proceso WHERE borrado = 0";
	$tabla = $conexion->query($sql);
	while ($reg = $tabla->fetch_object()) {
		$tipo = $reg->clasificacion;
		$desc_formato = desc_formato_cp($tipo);
		$formatocp = formato_cp($tipo);
		if ($tipo == "FIR" or $tipo == "FIN" or $tipo == "PT" or $tipo == "FPN" or $tipo == "FPR") {
			$fecha2 = date("Y/m/d");
			//BUSCAMOS SI TIENE FECHA DE NOTIFICACION EL ACTA DENTRO DE LOS 15 DIAS
			$bSQL = "SELECT fecha_notificacion as fecha FROM fis_actas WHERE anno_prov = " . $reg->anno . " AND num_prov = " . $reg->numero . " AND id_sector = " . $reg->sector;
			$bTabla = $con->query($bSQL);
			$reg0 = $bTabla->fetch_object();
			$fecha1 = $reg0->fecha;
			$dias = dias($fecha1, $fecha2);

			if ($dias < 20) {
				$lapso_allanamiento = 1;
				$proceso_auditoria = 0;
				$nivel_supervisor = 0;
				$otras_causas = 0;
			} else {
				$bSQLa = "SELECT status as estatus FROM expedientes_fiscalizacion WHERE anno = " . $reg->anno . " AND numero = " . $reg->numero . " AND sector = " . $reg->sector;
				$bTablaa = $con->query($bSQLa);
				$rega = $bTablaa->fetch_object();
				if ($rega->estatus == 6) {
					$lapso_allanamiento = 0;
					$proceso_auditoria = 0;
					$nivel_supervisor = 1;
					$otras_causas = 0;
				} else {
					$lapso_allanamiento = 0;
					$proceso_auditoria = 1;
					$nivel_supervisor = 0;
					$otras_causas = 0;
				}
			}
		} else {
			$bSQL = "SELECT status as estatus FROM expedientes_fiscalizacion WHERE anno = " . $reg->anno . " AND numero = " . $reg->numero . " AND sector = " . $reg->sector;
			$bTabla = $con->query($bSQL);
			$regb = $bTabla->fetch_object();
			if ($regb->estatus == 6) {
				$lapso_allanamiento = 0;
				$proceso_auditoria = 0;
				$nivel_supervisor = 1;
				$otras_causas = 0;
			} else {
				$lapso_allanamiento = 0;
				$proceso_auditoria = 1;
				$nivel_supervisor = 0;
				$otras_causas = 0;
			}
		}
		$SQLu = "UPDATE casos_en_proceso SET proceso_auditoria = " . $proceso_auditoria . ",  nivel_supervisor = " . $nivel_supervisor . ", lapso_allanamiento = " . $lapso_allanamiento . ", otras_causas = " . $otras_causas . " WHERE anno = " . $reg->anno . " AND numero = " . $reg->numero . " AND sector = " . $reg->sector;
		//echo $SQLu.'<br>';
		$tablau = $conexion->query($SQLu);


		//PARA CONSULTAR LAS PROVIDENCIAS EMITIDAS
		$sql = "SELECT casos_en_proceso.anno, casos_en_proceso.numero, casos_en_proceso.descripcion as programa, casos_en_proceso.rif, casos_en_proceso.contribuyente, casos_en_proceso.ci_fiscal, casos_en_proceso.fiscal, casos_en_proceso.ci_supervisor, casos_en_proceso.supervisor, casos_en_proceso.emision, casos_en_proceso.notificacion, casos_en_proceso.tipo, casos_en_proceso.nombre_sector, casos_en_proceso.proceso_auditoria, casos_en_proceso.nivel_supervisor, casos_en_proceso.lapso_allanamiento, casos_en_proceso.otras_causas FROM casos_en_proceso WHERE casos_en_proceso.borrado = 0 AND year(casos_en_proceso.notificacion) = year(date(now()))" . $sector; //echo $sql.'<br>';
		$tabla = $conexion->query($sql);
		$row = $tabla->num_rows;

		if ($row > 0) { ?>
			<h3><?php echo $titulo ?> al <?php echo date("d-m-Y") ?> </h3>
			<div>
				<table>
					<tr>
						<th>#</th>
						<th>Año</th>
						<th>Numero</th>
						<th>Programa</th>
						<th>Rif</th>
						<th>Contribuyente</th>
						<th>CI Fiscal</th>
						<th>Fiscal</th>
						<th>CI Supervisor</th>
						<th>Supervisor</th>
						<th>Emision</th>
						<th>Notificacion</th>
						<th>Tipo</th>
						<th>Sede/Sector</th>
						<th>Estatus</th>
					</tr>
					<?php
					$i = 1;
					while ($registro = $tabla->fetch_object()) { ?>
						<tr>
							<td><?php echo $i ?></td>
							<td><?php echo $registro->anno ?></td>
							<td><?php echo $registro->numero ?></td>
							<td><?php echo $registro->programa ?></td>
							<td><?php echo $registro->rif ?></td>
							<td><?php echo $registro->contribuyente ?></td>
							<td><?php echo $registro->ci_fiscal ?></td>
							<td><?php echo $registro->fiscal ?></td>
							<td><?php echo $registro->ci_supervisor ?></td>
							<td><?php echo $registro->supervisor ?></td>
							<td><?php echo voltea_fecha($registro->emision) ?></td>
							<td><?php echo voltea_fecha($registro->notificacion) ?></td>
							<td><?php echo $registro->tipo ?></td>
							<td><?php echo $registro->nombre_sector ?></td>
							<?php
							if ($registro->status <= 4) {
								$estatus = "Proceso de Auditorias";
							}
							if ($registro->status > 5 and $registro->status < 8) {
								$estatus = "Revision Supervisor";
							} ?>

							<td><?php echo $estatus ?></td>
						</tr>
					<?php
						$i++;
					}
					?>
				</table>
			</div>
			<p></p>
			<div>
				<table width="30%">
					<tr>
						<th><?php echo "Resumen " . $titulo ?></th>
						<th>Cantidad</th>
					</tr>
					<?php
					//RESUMEN EMITIDAS

					$sql_resumen = "SELECT casos_en_proceso.descripcion, count(casos_en_proceso.descripcion) as cantidad FROM casos_en_proceso WHERE casos_en_proceso.borrado = 0 AND year(casos_en_proceso.notificacion) = year(date(now()))" . $sector . " GROUP BY casos_en_proceso.descripcion";
					$tabla_r = $conexion->query($sql_resumen);
					while ($resumen = $tabla_r->fetch_object()) { ?>
						<tr>
							<td><?php echo $resumen->descripcion ?></td>
							<td align="center"><?php echo $resumen->cantidad ?></td>
						</tr>
					<?php
						$total_cantidad += $resumen->cantidad;
					}
					?>
					<tfoot>
						<td>TOTALES</td>
						<td align="center"><?php echo $total_cantidad ?></td>
					</tfoot>
				</table>
			</div>
		<?php
		} else { ?>
			<div>
				<table width="50%">
					<tr>
						<th>Resultado de la Busqueda</th>
					</tr>
					<tr>
						<td align="center">No existen registros para el periodo indicado</td>
					</tr>
				</table>
			</div>
	<?php
		}
	}

	//FIN ACTUALIZACION CASOS EN PROCESO	

	function dias($fecha1, $fecha2)
	{
		$fecha1 = strtotime($fecha1);
		$fecha2 = strtotime($fecha2);
		$fecha1 = date("Y/m/d", $fecha1);
		$fecha2 = date("Y/m/d", $fecha2);
		$date2 = strtotime($fecha2);
		$date1 = strtotime($fecha1);
		$diff = abs($date2 - $date1);
		$dias = floor($diff / (24 * 60 * 60));
		return $dias;
	}

	function desc_formato_cp($tipo)
	{
		switch ($tipo) {
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

	function periodo_inicial()
	{
		$fecha = new DateTime();
		$fecha->modify('first day of this month');
		return $fecha->format('Y/m/d'); // imprime por ejemplo: 01/12/2012
	}

	function periodo_final()
	{
		$fecha = new DateTime();
		$fecha->modify('last day of this month');
		return $fecha->format('Y/m/d'); // imprime por ejemplo: 01/12/2012
	}


	function formato_cp($tipo)
	{
		switch ($tipo) {
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
</body>

</html>