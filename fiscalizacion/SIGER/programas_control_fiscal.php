<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Otros Programas de Control Fiscal";
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
			$sector = " AND ct_destruccion_facturas.sector";
			break;
		default:
			$sector = " AND ct_destruccion_facturas.sector = " . $sede;
			break;
	}

	//PARA CONSULTAR LAS PROVIDENCIAS EMITIDAS
	$sql = "SELECT ct_destruccion_facturas.rif, ct_destruccion_facturas.tipo_solicitud, CONCAT(z_siglas.Siglas_resol_fis,'/',year(ct_destruccion_facturas.fecha_emision),'/',ct_destruccion_facturas.numero_acta) AS numeroacta, ct_destruccion_facturas.fecha_emision, z_sectores.nombre FROM ct_destruccion_facturas INNER JOIN z_siglas ON z_siglas.id_sector = ct_destruccion_facturas.sector INNER JOIN z_sectores ON z_sectores.id_sector = z_siglas.id_sector WHERE ct_destruccion_facturas.fecha_emision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector;
	$tabla = mysql_query($sql);
	$row = mysql_num_rows($tabla);

	if ($row > 0) { ?>
		<h3><?php echo $titulo ?> Periodo desde <?php echo voltea_fecha($inicio) ?> hasta <?php echo voltea_fecha($fin) ?> </h3>
		<div>
			<table>
				<tr>
					<th>#</th>
					<th>Rif</th>
					<th>Tipo Silicitud</th>
					<th>Acta, Providencia o Informe</th>
					<th>Emision</th>
					<th>Notificacion</th>
					<th>Sede/Sector</th>
				</tr>
				<?php
				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) { ?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php echo $registro->rif ?></td>
						<td><?php echo $registro->tipo_solicitud ?></td>
						<td><?php echo $registro->numeroacta ?></td>
						<td><?php echo voltea_fecha($registro->fecha_emision) ?></td>
						<td><?php echo voltea_fecha($registro->fecha_emision) ?></td>
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
			<table width="30%">
				<tr>
					<th><?php echo "Resumen " . $titulo ?></th>
					<th>Cantidad</th>
				</tr>
				<?php
				//RESUMEN EMITIDAS

				$sql_resumen = "SELECT ct_destruccion_facturas.tipo_solicitud, count(ct_destruccion_facturas.tipo_solicitud) as cantidad FROM ct_destruccion_facturas INNER JOIN z_siglas ON z_siglas.id_sector = ct_destruccion_facturas.sector INNER JOIN z_sectores ON z_sectores.id_sector = z_siglas.id_sector WHERE ct_destruccion_facturas.fecha_emision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector . " GROUP BY ct_destruccion_facturas.tipo_solicitud";
				$tabla_r = mysql_query($sql_resumen);
				while ($resumen = mysql_fetch_object($tabla_r)) { ?>
					<tr>
						<td><?php echo $resumen->tipo_solicitud ?></td>
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