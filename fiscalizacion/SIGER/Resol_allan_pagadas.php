<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Resoluciones de Allanamiento Pagadas";
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
	$sql = "SELECT expedientes_fiscalizacion.rif, concat(z_siglas.Siglas_resol_fis,'/',expedientes_fiscalizacion.anno,'/',a_tipo_providencia.Siglas2,'/',a_tipo_providencia.Siglas1,lpad(expedientes_fiscalizacion.numero,4,'0'),'/',resoluciones.anno,'/',lpad(resoluciones.numero,4,'0')) AS numresolucion, resoluciones.fecha AS fecha_emision, a_tipo_programa.descripcion, z_sectores.nombre, vista_actas_siger.impuesto_omitido, vista_actas_siger.multa_actual, vista_actas_siger.interes, vista_actas_siger.ajuste_voluntario, vista_siger_resoluciones_seguimiento.fecha_pag FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN resoluciones ON resoluciones.id_sector = expedientes_fiscalizacion.sector AND resoluciones.anno_expediente = expedientes_fiscalizacion.anno AND resoluciones.num_expediente = expedientes_fiscalizacion.numero INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN vista_siger_resoluciones_seguimiento ON vista_siger_resoluciones_seguimiento.sector = expedientes_fiscalizacion.sector AND vista_siger_resoluciones_seguimiento.anno_expediente = expedientes_fiscalizacion.anno AND vista_siger_resoluciones_seguimiento.num_expediente = expedientes_fiscalizacion.numero WHERE resoluciones.id_origen = 4 AND a_tipo_programa.tipo <> 'Verificaciones' AND vista_siger_resoluciones_seguimiento.fecha_pag BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "' AND vista_actas_siger.ajuste_voluntario > 0" . $sector;
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
					<th>Pagada</th>
					<th>Impuesto</th>
					<th>Multas</th>
					<th>Intereses</th>
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
						<td><?php echo voltea_fecha($registro->fecha_pag) ?></td>
						<td align="right"><?php echo formato_moneda($registro->impuesto_omitido) ?></td>
						<td align="right"><?php echo formato_moneda($registro->multa_actual) ?></td>
						<td align="right"><?php echo formato_moneda($registro->interes) ?></td>
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
					<th>Impuesto</th>
					<th>Multas</th>
					<th>Intereses</th>
				</tr>
				<?php
				//RESUMEN EMITIDAS

				$sql_resumen = "SELECT a_tipo_programa.descripcion, Count(a_tipo_programa.id_programa) AS cantidad, Sum(vista_actas_siger.impuesto_omitido) AS impuesto, Sum(vista_actas_siger.multa_actual) AS multa, Sum(vista_actas_siger.interes) AS intereses, Sum(vista_actas_siger.ajuste_voluntario) AS pagado, vista_siger_resoluciones_seguimiento.fecha_not FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN resoluciones ON resoluciones.id_sector = expedientes_fiscalizacion.sector AND resoluciones.anno_expediente = expedientes_fiscalizacion.anno AND resoluciones.num_expediente = expedientes_fiscalizacion.numero INNER JOIN z_siglas ON z_siglas.id_sector = expedientes_fiscalizacion.sector INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector INNER JOIN vista_actas_siger ON vista_actas_siger.sector = expedientes_fiscalizacion.sector AND vista_actas_siger.anno_prov = expedientes_fiscalizacion.anno AND vista_actas_siger.num_prov = expedientes_fiscalizacion.numero INNER JOIN vista_siger_resoluciones_seguimiento ON vista_siger_resoluciones_seguimiento.sector = expedientes_fiscalizacion.sector AND vista_siger_resoluciones_seguimiento.anno_expediente = expedientes_fiscalizacion.anno AND vista_siger_resoluciones_seguimiento.num_expediente = expedientes_fiscalizacion.numero WHERE resoluciones.id_origen = 4 AND a_tipo_programa.tipo <> 'Verificaciones' AND vista_siger_resoluciones_seguimiento.fecha_pag BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "' AND vista_actas_siger.ajuste_voluntario > 0" . $sector . " GROUP BY a_tipo_programa.id_programa";
				$tabla_r = mysql_query($sql_resumen);
				while ($resumen = mysql_fetch_object($tabla_r)) { ?>
					<tr>
						<td><?php echo $resumen->descripcion ?></td>
						<td align="center"><?php echo $resumen->cantidad ?></td>
						<td align="right"><?php echo formato_moneda($resumen->impuesto) ?></td>
						<td align="right"><?php echo formato_moneda($resumen->multa) ?></td>
						<td align="right"><?php echo formato_moneda($resumen->intereses) ?></td>
					</tr>
				<?php
					$total_cantidad += $resumen->cantidad;
					$total_impto += $resumen->impuesto;
					$total_multa += $resumen->multa;
					$total_intereses += $resumen->intereses;
				}
				?>
				<tfoot>
					<td>TOTALES</td>
					<td align="center"><?php echo $total_cantidad ?></td>
					<td align="right"><?php echo formato_moneda($total_impto) ?></td>
					<td align="right"><?php echo formato_moneda($total_multa) ?></td>
					<td align="right"><?php echo formato_moneda($total_intereses) ?></td>
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