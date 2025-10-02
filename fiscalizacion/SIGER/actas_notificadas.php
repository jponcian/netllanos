<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Actas Fiscales Notificadas";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php echo $titulo ?></title>
	<!-- <link rel="stylesheet" type="text/css" href="estilos.css"/> -->
</head>
<style type="text/css" media="screen">
	body {
		font-family: Arial, Helvetica, sans-serif;
	}

	table {
		font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
		font-size: 9px;
		margin: 5px;
		text-align: left;
		border-collapse: collapse;
	}

	th {
		font-size: 13px;
		font-weight: bold;
		padding: 8px;
		background: #FD5053;
		border-top: 4px solid #E6060A;
		border-bottom: 1px solid #fff;
		color: #FAF2F2;
	}

	td {
		padding: 8px;
		background: #FAF2F2;
		border-bottom: 1px solid #fff;
		color: #000000;
		border-top: 1px solid transparent;
	}

	tr:hover td {
		background: #FAAFB0;
		color: #339;
	}

	tfoot {
		font-weight: bold;
		background: #666666;
		color: #0000FF;
	}
</style>

<body style="background: transparent !important;">
	<?php
	$inicio = $_GET['inicio'];
	$fin = $_GET['fin'];
	$sede = $_GET['sede'];

	switch ($sede) {
		case 0:
			$sector = " AND vista_actas_siger.sector";
			break;
		default:
			$sector = " AND vista_actas_siger.sector = " . $sede;
			break;
	}

	//PARA CONSULTAR LAS PROVIDENCIAS EMITIDAS
	$sql = "SELECT vista_actas_siger.ciudad, vista_actas_siger.direccion, vista_actas_siger.contribuyente, vista_actas_siger.rif, vista_actas_siger.numacta, vista_actas_siger.fecha_emision, vista_actas_siger.fecha_notificacion, vista_actas_siger.reparo, vista_actas_siger.impuesto_omitido, vista_actas_siger.multa_actual, vista_actas_siger.interes, vista_actas_siger.nombre, vista_actas_siger.acta, vista_actas_siger.ajuste_voluntario, vista_actas_siger.fecha_pago, vista_actas_siger.siglas, a_tipo_programa.descripcion FROM vista_actas_siger INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = vista_actas_siger.programa WHERE vista_actas_siger.fecha_notificacion BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector; //echo $sql ;
	$tabla = mysql_query($sql);
	$row = mysql_num_rows($tabla);

	if ($row > 0) { ?>
		<h3><?php echo $titulo ?> Periodo desde <?php echo voltea_fecha($inicio) ?> hasta <?php echo voltea_fecha($fin) ?> </h3>
		<div>
			<table>
				<tr>
					<th>#</th>
					<th>Rif</th>
					<th>Contribuyente</th>
					<th>Direccion</th>
					<th>Numero Acta</th>
					<th>Emision</th>
					<th>Notificacion</th>
					<th>Reparo</th>
					<th>Impto Omitido</th>
					<th>Multa</th>
					<th>Intereses</th>
					<th>Tipo</th>
					<th>Fecha Pago</th>
					<th>Monta Pagado</th>
					<th>Tributo</th>
					<th>Programa</th>
					<th>Sede/Sector</th>
				</tr>
				<?php
				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) { ?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php echo $registro->rif ?></td>
						<td><?php echo $registro->contribuyente ?></td>
						<td><?php echo $registro->ciudad . ', ' . $registro->direccion ?></td>
						<td><?php echo $registro->numacta ?></td>
						<td><?php echo $registro->fecha_emision ?></td>
						<td><?php echo $registro->fecha_notificacion ?></td>
						<td align="right"><?php echo formato_moneda($registro->reparo) ?></td>
						<td align="right"><?php echo formato_moneda($registro->impuesto_omitido) ?></td>
						<td align="right"><?php echo formato_moneda($registro->multa_actual) ?></td>
						<td align="right"><?php echo formato_moneda($registro->interes) ?></td>
						<td><?php echo $registro->acta ?></td>
						<td><?php echo voltea_fecha($registro->fecha_pago) ?></td>
						<td align="right"><?php echo formato_moneda($registro->ajuste_voluntario) ?></td>
						<td><?php echo $registro->siglas ?></td>
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
			<table width="70%">
				<tr>
					<th><?php echo "Resumen " . $titulo ?></th>
					<th>Cantidad</th>
					<th>Reparo</th>
					<th>Impuesto</th>
					<th>Multa</th>
					<th>Intereses</th>
					<th>Ajuste Voluntario</th>
				</tr>
				<?php
				//RESUMEN EMITIDAS

				$sql_resumen = "SELECT a_tipo_programa.descripcion, count(a_tipo_programa.id_programa) as cantidad, sum(vista_actas_siger.reparo)as reparo, sum(vista_actas_siger.impuesto_omitido) as impuesto_omitido, sum(vista_actas_siger.multa_actual) as multa, sum(vista_actas_siger.interes) as intereses, sum(vista_actas_siger.ajuste_voluntario) as pagado FROM vista_actas_siger INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = vista_actas_siger.programa WHERE
vista_actas_siger.fecha_notificacion BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector . " GROUP BY a_tipo_programa.id_programa";
				$tabla_r = mysql_query($sql_resumen);
				while ($resumen = mysql_fetch_object($tabla_r)) { ?>
					<tr>
						<td><?php echo $resumen->descripcion ?></td>
						<td align="center"><?php echo $resumen->cantidad ?></td>
						<td align="right"><?php echo formato_moneda($resumen->reparo) ?></td>
						<td align="right"><?php echo formato_moneda($resumen->impuesto_omitido) ?></td>
						<td align="right"><?php echo formato_moneda($resumen->multa) ?></td>
						<td align="right"><?php echo formato_moneda($resumen->intereses) ?></td>
						<td align="right"><?php echo formato_moneda($resumen->pagado) ?></td>
					</tr>
				<?php
					$total_cantidad += $resumen->cantidad;
					$total_reparo += $resumen->reparo;
					$total_impto += $resumen->impuesto_omitido;
					$total_multa += $resumen->multa;
					$total_interes += $resumen->intereses;
					$total_pagado += $resumen->pagado;
				}
				?>
				<tfoot>
					<td>TOTALES</td>
					<td align="center"><?php echo $total_cantidad ?></td>
					<td align="right"><?php echo formato_moneda($total_reparo) ?></td>
					<td align="right"><?php echo formato_moneda($total_impto) ?></td>
					<td align="right"><?php echo formato_moneda($total_multa) ?></td>
					<td align="right"><?php echo formato_moneda($total_interes) ?></td>
					<td align="right"><?php echo formato_moneda($total_pagado) ?></td>
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