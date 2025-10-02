<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
mysql_query("SET NAMES 'utf8'");
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Casos en Proceso Año Actual";
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
			$sector = " AND expedientes_fiscalizacion.sector";
			break;
		default:
			$sector = " AND expedientes_fiscalizacion.sector = " . $sede;
			break;
	}

	//PARA CONSULTAR LAS PROVIDENCIAS EMITIDAS
	$sql = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, a_tipo_programa.descripcion, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.ci_fiscal1, CONCAT_WS(' ',fiscal.Nombres,fiscal.Apellidos) as fiscal, expedientes_fiscalizacion.ci_supervisor, CONCAT_WS(' ',supervisor.Nombres,supervisor.Apellidos) as supervisor, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, z_sectores.nombre, a_tipo_programa.tipo, expedientes_fiscalizacion.status FROM expedientes_fiscalizacion INNER JOIN vista_siger_prov_proceso ON vista_siger_prov_proceso.sector = expedientes_fiscalizacion.sector AND vista_siger_prov_proceso.anno = expedientes_fiscalizacion.anno AND vista_siger_prov_proceso.numero = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS fiscal ON fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS supervisor ON supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.status <> 9 AND year(expedientes_fiscalizacion.fecha_notificacion) = year(date(now()))" . $sector; //echo $sql.'<br>';
	$tabla = mysql_query($sql);
	$row = mysql_num_rows($tabla);

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
				while ($registro = mysql_fetch_object($tabla)) { ?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php echo $registro->anno ?></td>
						<td><?php echo $registro->numero ?></td>
						<td><?php echo $registro->descripcion ?></td>
						<td><?php echo $registro->rif ?></td>
						<td><?php echo $registro->contribuyente ?></td>
						<td><?php echo $registro->ci_fiscal1 ?></td>
						<td><?php echo $registro->fiscal ?></td>
						<td><?php echo $registro->ci_supervisor ?></td>
						<td><?php echo $registro->supervisor ?></td>
						<td><?php echo voltea_fecha($registro->fecha_emision) ?></td>
						<td><?php echo voltea_fecha($registro->fecha_notificacion) ?></td>
						<td><?php echo $registro->tipo ?></td>
						<td><?php echo $registro->nombre ?></td>
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

				$sql_resumen = "SELECT a_tipo_programa.descripcion, count(a_tipo_programa.id_programa) as cantidad FROM expedientes_fiscalizacion INNER JOIN vista_siger_prov_proceso ON vista_siger_prov_proceso.sector = expedientes_fiscalizacion.sector AND vista_siger_prov_proceso.anno = expedientes_fiscalizacion.anno AND vista_siger_prov_proceso.numero = expedientes_fiscalizacion.numero INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS fiscal ON fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS supervisor ON supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE expedientes_fiscalizacion.status <> 9 AND year(expedientes_fiscalizacion.fecha_notificacion) = year(date(now()))" . $sector . " GROUP BY a_tipo_programa.id_programa";
				$tabla_r = mysql_query($sql_resumen);
				while ($resumen = mysql_fetch_object($tabla_r)) { ?>
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

	?>
</body>

</html>