<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Providencias Concluidas (Salida de Expedientes";
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
			$sector = " AND expedientes_fiscalizacion.sector";
			break;
		default:
			$sector = " AND expedientes_fiscalizacion.sector = " . $sede;
			break;
	}

	//PARA CONSULTAR LAS PROVIDENCIAS EMITIDAS
	$sql = "SELECT
ct_salida_expediente.Anno_Providencia,
ct_salida_expediente.NroAutorizacion,
ct_salida_expediente.sector,
ct_salida_expediente.Rif,
ct_salida_expediente.Nombre,
ct_salida_expediente.FechaEmision,
ct_salida_expediente.Anno_Resolucion,
ct_salida_expediente.NroResolucion,
ct_salida_expediente.FechaResolucion,
ct_salida_expediente.Monto_Reparo,
ct_salida_expediente.Impto_Omitido,
ct_salida_expediente.Multa_Reparo,
ct_salida_expediente.Intereses,
ct_salida_expediente.Multa_DF,
ct_salida_expediente.Monto_Pagado,
ct_salida_expediente.NumActa,
ct_salida_expediente.FechaActa,
ct_salida_expediente.FechaNotificacionActa
FROM
ct_salida_expediente
INNER JOIN z_sectores ON z_sectores.id_sector = ct_salida_expediente.sector
WHERE
ct_salida_expediente.FechaEmision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'";
	$tabla = mysql_query($sql);
	$row = mysql_num_rows($tabla);

	if ($row > 0) { ?>
		<h3><?php echo $titulo ?> Periodo desde <?php echo voltea_fecha($inicio) ?> hasta <?php echo voltea_fecha($fin) ?> </h3>
		<div>
			<table>
				<tr>
					<th>#</th>
					<th>Anno_Providencia</th>
					<th>NroAutorizacion</th>
					<th>sector</th>
					<th>Rif</th>
					<th>Nombre</th>
					<th>FechaEmision</th>
					<th>Anno_Resolucion</th>
					<th>NroResolucion</th>
					<th>FechaResolucion</th>
					<th>Monto_Reparo</th>
					<th>Impto_Omitido</th>
					<th>Multa_Reparo</th>
					<th>Intereses</th>
					<th>Multa_DF</th>
					<th>Monto_Pagado</th>
					<th>NumActa</th>
					<th>FechaActa</th>
					<th>FechaNotificacionActa</th>
				</tr>
				<?php
				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) { ?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php echo $registro->Anno_Providencia ?></td>
						<td><?php echo $registro->NroAutorizacion ?></td>
						<td><?php echo $registro->sector ?></td>
						<td><?php echo $registro->Rif ?></td>
						<td><?php echo $registro->Nombre ?></td>
						<td><?php echo voltea_fecha($registro->FechaEmision) ?></td>
						<td><?php echo $registro->Anno_Resolucion ?></td>
						<td><?php echo $registro->NroResolucion ?></td>
						<td><?php echo voltea_fecha($registro->FechaResolucion) ?></td>
						<td><?php echo $registro->Monto_Reparo ?></td>
						<td><?php echo $registro->Impto_Omitido ?></td>
						<td><?php echo $registro->Multa_Reparo ?></td>
						<td><?php echo $registro->Intereses ?></td>
						<td><?php echo $registro->Multa_DF ?></td>
						<td><?php echo $registro->Monto_Pagado ?></td>
						<td><?php echo $registro->NumActa ?></td>
						<td><?php echo voltea_fecha($registro->FechaActa) ?></td>
						<td><?php echo voltea_fecha($registro->FechaNotificacionActa) ?></td>
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
				</tr>
				<?php
				//RESUMEN EMITIDAS

				$sql_resumen = "SELECT Count(a_tipo_programa.id_programa) AS cantidad, a_tipo_programa.descripcion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS fiscal ON fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS supervisor ON supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero WHERE ct_salida_expediente.FechaEmision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "' AND year(expedientes_fiscalizacion.fecha_notificacion) < year(date(now()))" . $sector . " GROUP BY a_tipo_programa.id_programa";
				$tabla_r = mysql_query($sql_resumen);
				while ($resumen = mysql_fetch_object($tabla_r)) { ?>
					<tr>
						<td><?php echo $resumen->descripcion ?></td>
						<td align="center"><?php echo $resumen->cantidad ?></td>
					</tr>
				<?php
					$total += $resumen->cantidad;
				}
				?>
				<tfoot>
					<td>TOTALES</td>
					<td align="center"><?php echo $total ?></td>
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