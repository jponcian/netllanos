<?php
session_start();
include "../../conexion.php";
include "../../funciones/auxiliar_php.php";
/*if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}*/

$titulo = "Providencias Concluidas Sancionadas A�os Anteriores";
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
	$sql = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.rif, vista_contribuyentes_direccion.contribuyente, expedientes_fiscalizacion.ci_fiscal1, CONCAT_WS(' ',fiscal.Nombres,fiscal.Apellidos) AS fiscal, expedientes_fiscalizacion.ci_supervisor, CONCAT_WS(' ',supervisor.Nombres,supervisor.Apellidos) AS supervisor, a_tipo_programa.descripcion, a_tipo_programa.tipo, a_tipo_providencia.TipoPrograma, z_sectores.nombre, ct_salida_expediente.FechaEmision AS fecha_conclusion, vista_ct_multas.Multas FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS fiscal ON fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS supervisor ON supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero INNER JOIN vista_ct_multas ON vista_ct_multas.sector = expedientes_fiscalizacion.sector AND vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero WHERE ct_salida_expediente.FechaEmision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "' AND year(expedientes_fiscalizacion.fecha_notificacion) < year(date(now()))" . $sector;
	$tabla = mysql_query($sql);
	$row = mysql_num_rows($tabla);

	if ($row > 0) { ?>
		<h3><?php echo $titulo ?> Periodo desde <?php echo voltea_fecha($inicio) ?> hasta <?php echo voltea_fecha($fin) ?> </h3>
		<div class="CSSTableGenerator">
			<table>
				<tr>
					<th>#</th>
					<th>A�o</th>
					<th>Numero</th>
					<th>Rif</th>
					<th>Sujeto Pssivo</th>
					<th>CI Fiscal</th>
					<th>Fiscal</th>
					<th>CI Supervisor</th>
					<th>Supervisor</th>
					<th>Conclusion</th>
					<th>Programa</th>
					<th>Tipo</th>
					<th>Tipo Programa</th>
					<th>Sede/Sector</th>
					<th>Multas</th>
				</tr>
				<?php
				$i = 1;
				while ($registro = mysql_fetch_object($tabla)) { ?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php echo $registro->anno ?></td>
						<td><?php echo $registro->numero ?></td>
						<td><?php echo $registro->rif ?></td>
						<td><?php echo $registro->contribuyente ?></td>
						<td><?php echo formato_cedula($registro->ci_fiscal1) ?></td>
						<td><?php echo $registro->fiscal ?></td>
						<td><?php echo formato_cedula($registro->ci_supervisor) ?></td>
						<td><?php echo $registro->supervisor ?></td>
						<td><?php echo voltea_fecha($registro->fecha_conclusion) ?></td>
						<td><?php echo $registro->descripcion ?></td>
						<td><?php echo $registro->tipo ?></td>
						<td><?php echo $registro->TipoPrograma ?></td>
						<td><?php echo $registro->nombre ?></td>
						<td><?php echo formato_moneda($registro->Multas) ?></td>
					</tr>
				<?php
					$i++;
				}
				?>
			</table>
		</div>
		<p></p>
		<div class="CSSTableResumen">
			<table width="50%">
				<tr>
					<th><?php echo "Resumen " . $titulo ?></th>
					<th>Cantidad</th>
				</tr>
				<?php
				//RESUMEN EMITIDAS

				$sql_resumen = "SELECT Count(a_tipo_programa.id_programa) AS cantidad, a_tipo_programa.descripcion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN a_tipo_providencia ON a_tipo_providencia.tipo = expedientes_fiscalizacion.tipo INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS fiscal ON fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS supervisor ON supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN ct_salida_expediente ON ct_salida_expediente.sector = expedientes_fiscalizacion.sector AND ct_salida_expediente.Anno_Providencia = expedientes_fiscalizacion.anno AND ct_salida_expediente.NroAutorizacion = expedientes_fiscalizacion.numero INNER JOIN vista_ct_multas ON vista_ct_multas.sector = expedientes_fiscalizacion.sector AND vista_ct_multas.anno_expediente = expedientes_fiscalizacion.anno AND vista_ct_multas.num_expediente = expedientes_fiscalizacion.numero WHERE ct_salida_expediente.FechaEmision BETWEEN '" . voltea_fecha($inicio) . "' AND '" . voltea_fecha($fin) . "' AND year(expedientes_fiscalizacion.fecha_notificacion) < year(date(now()))" . $sector . " GROUP BY a_tipo_programa.id_programa";
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