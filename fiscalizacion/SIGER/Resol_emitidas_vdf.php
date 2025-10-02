<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Resoluciones de Imposicion de Sancion VDF Emitidas";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
			$sector = " AND resoluciones.id_sector";
			break;
		default:
			$sector = " AND resoluciones.id_sector = " . $sede;
			break;
	}

	//PARA CONSULTAR LAS PROVIDENCIAS EMITIDAS
	$sql = "SELECT expedientes_fiscalizacion.rif, concat(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,lpad(expedientes_fiscalizacion.numero,4,'0'),'/',resoluciones.anno,'/',lpad(resoluciones.numero,4,'0')) AS numresolucion, resoluciones.fecha AS fecha_emision, vista_ct_multas.Multas, a_tipo_programa.descripcion, z_sectores.nombre FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN resoluciones ON resoluciones.id_sector = expedientes_fiscalizacion.sector AND resoluciones.anno_expediente = expedientes_fiscalizacion.anno AND resoluciones.num_expediente = expedientes_fiscalizacion.numero INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN vista_ct_multas ON vista_ct_multas.sector = expedientes_fiscalizacion.sector AND vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE resoluciones.id_origen = 4 AND a_tipo_programa.tipo = 'Verificaciones' AND resoluciones.fecha BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector;
	$tabla = mysql_query($sql);
	$row = mysql_num_rows($tabla);

	if ($row > 0) { ?>
		<h3><?php echo $titulo ?> Periodo desde <?php echo voltea_fecha($inicio) ?> hasta <?php echo voltea_fecha($fin) ?> </h3>
		<div>
			<table>
				<tr>
					<th>#</th>
					<th>Rif</th>
					<th>Numero Resolucion</th>
					<th>Emision</th>
					<th>Monto Multas</th>
					<th>Programa</th>
					<th>Sede/Sector</th>
				</tr>
				<?php
				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) { ?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php echo $registro->rif ?></td>
						<td><?php echo $registro->numresolucion ?></td>
						<td><?php echo voltea_fecha($registro->fecha_emision) ?></td>
						<td align="right"><?php echo formato_moneda($registro->Multas) ?></td>
						<td><?php echo $registro->descripcion ?></td>
						<td><?php echo $registro->nombre ?></td>
					</tr>
				<?php
					$i++;
				}
				?>
			</table>
		</div>
		<p></p>
		<div>
			<table width="50%">
				<tr>
					<th><?php echo "Resumen " . $titulo ?></th>
					<th>Cantidad</th>
					<th>Multa</th>
				</tr>
				<?php
				//RESUMEN EMITIDAS

				$sql_resumen = "SELECT a_tipo_programa.descripcion, count(a_tipo_programa.id_programa) as cantidad, sum(vista_ct_multas.Multas) as multas FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN resoluciones ON resoluciones.id_sector = expedientes_fiscalizacion.sector AND resoluciones.anno_expediente = expedientes_fiscalizacion.anno AND resoluciones.num_expediente = expedientes_fiscalizacion.numero INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN vista_ct_multas ON vista_ct_multas.sector = expedientes_fiscalizacion.sector AND vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE resoluciones.id_origen = 4 AND a_tipo_programa.tipo = 'Verificaciones' AND resoluciones.fecha BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector . " GROUP BY a_tipo_programa.id_programa";
				$tabla_r = mysql_query($sql_resumen);
				while ($resumen = mysql_fetch_object($tabla_r)) { ?>
					<tr>
						<td><?php echo $resumen->descripcion ?></td>
						<td align="center"><?php echo $resumen->cantidad ?></td>
						<td align="right"><?php echo formato_moneda($resumen->multas) ?></td>
					</tr>
				<?php
					$total_cantidad += $resumen->cantidad;
					$total_multa += $resumen->multas;
				}
				?>
				<tfoot>
					<td>TOTALES</td>
					<td align="center"><?php echo $total_cantidad ?></td>
					<td align="right"><?php echo formato_moneda($total_multa) ?></td>
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