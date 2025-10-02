<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Investigaciones Fiscales";
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
			$sector = " AND sector";
			break;
		default:
			$sector = " AND sector = " . $sede;
			break;
	}

	//PARA CONSULTAR LAS PROVIDENCIAS EMITIDAS
	$sql = "SELECT anno_prov, num_prov, emision, notificacion, rif, contribuyente, fiscal, supervisor, ObjetoAutorizacion, Periodos, programa, tipoprograma, nombre_sector, anno_acta, numero_acta, fecha_acta, fecha_not_acta, tipo_acta, reparo, impuesto, multa, intereses, sector FROM vista_ct_fiscalizaciones_puntuales WHERE emision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector; //echo $sql;
	$tabla = mysql_query($sql);
	$row = mysql_num_rows($tabla);

	if ($row > 0) { ?>
		<h3><?php echo $titulo ?> Periodo desde <?php echo $inicio ?> hasta <?php echo $fin ?> </h3>
		<div>
			<table>
				<tr>
					<th>#</th>
					<th>A�o Prov</th>
					<th>Nro Prov</th>
					<th>Rif</th>
					<th>Sujeto Pssivo</th>
					<th>Emision</th>
					<th>Notificacion</th>
					<th>Fiscal</th>
					<th>Supervisor</th>
					<th>ObjetoAutorizacion</th>
					<th>Periodos</th>
					<th>Programa</th>
					<th>Tipo Programa</th>
					<th>A�o Acta</th>
					<th>Nro Acta</th>
					<th>Emi. Acta</th>
					<th>Not. Acta</th>
					<th>Tipo Acta</th>
					<th>Reparo</th>
					<th>Impuesto</th>
					<th>Multa</th>
					<th>Intereses</th>
					<th>Sede/Sector</th>
				</tr>
				<?php
				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) { ?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php echo $registro->anno_prov ?></td>
						<td><?php echo $registro->num_prov ?></td>
						<td><?php echo $registro->rif ?></td>
						<td><?php echo $registro->contribuyente ?></td>
						<td align="right"><?php echo $registro->emision ?></td>
						<td align="right">
							<?php
							if ($registro->notificacion == '0000-00-00') {
								$notificacion = '';
							} else {
								$notificacion = $registro->notificacion;
							}
							echo $notificacion;
							?>
						</td>
						<td><?php echo $registro->fiscal ?></td>
						<td><?php echo $registro->supervisor ?></td>
						<td><?php echo $registro->ObjetoAutorizacion ?></td>
						<td><?php echo $registro->Periodos ?></td>
						<td><?php echo $registro->programa ?></td>
						<td><?php echo $registro->tipoprograma ?></td>
						<td><?php echo $registro->anno_acta ?></td>
						<td><?php echo $registro->numero_acta ?></td>
						<td align="right"><?php echo $registro->fecha_acta ?></td>
						<td align="right">
							<?php
							if ($registro->fecha_not_acta == '0000-00-00') {
								$notificacion_acta = '';
							} else {
								$notificacion_acta = $registro->fecha_not_acta;
							}
							echo $notificacion_acta;
							?>
						</td>
						<td><?php
							if ($registro->tipo_acta == 0) {
								$actatipo = 'REPARO';
							}
							if ($registro->tipo_acta == 1) {
								$actatipo = 'CONFORMIDAD';
							}
							if ($registro->tipo_acta == 0) {
								$actatipo = 'INFRACCION';
							}
							echo $actatipo;
							?></td>
						<td align="right"><?php echo formato_moneda($registro->reparo) ?></td>
						<td align="right"><?php echo formato_moneda($registro->impuesto) ?></td>
						<td align="right"><?php echo formato_moneda($registro->multa) ?></td>
						<td align="right"><?php echo formato_moneda($registro->intereses) ?></td>
						<td><?php echo $registro->nombre_sector ?></td>
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

				$sql_resumen = "SELECT a_tipo_programa.descripcion, COUNT(a_tipo_programa.id_programa) as cantidad FROM vista_ct_fiscalizaciones_puntuales INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = vista_ct_fiscalizaciones_puntuales.id_programa WHERE emision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "'" . $sector . " GROUP BY a_tipo_programa.id_programa";
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