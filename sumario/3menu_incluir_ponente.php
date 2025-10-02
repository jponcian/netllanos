<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 58;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//--------------------
$status = 8;
$status2 = 60;
//--------------------
?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Expedientes x Asignar Ponente</title>

</head>
<style type="text/css">
	<!--
	.Estilomenun {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
	}

	body {
		background-image: url();
	}

	.Estilo7 {
		font-size: 18px;
		font-weight: bold;
		color: #FFFFFF;
	}

	.Estilo8 {
		color: #000000
	}
	-->
</style>

<body style="background: transparent !important;">
	<script type="text/jscript">
		setInterval("recargar()", 10000);

		function recargar() {
			document.form1.submit();
		}
	</script>
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post">
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td height="45" align="center" bgcolor="#FF0000">
						<p class="Estilo7"><u>Expedientes por Asignar Ponente </u></p>
					</td>
					<td height="27" align="center" bgcolor="#CCCCCC">
						<p class="Estilo7 Estilo8">Dependencia =&gt;</p>
					</td>
					<td height="27" align="center" bgcolor="#CCCCCC">
						<p class="Estilo7"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="0">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
										$consulta_x = "SELECT expedientes_sumario.sector as sector, z_sectores.nombre as dependencia FROM expedientes_sumario, z_sectores WHERE z_sectores.id_sector = expedientes_sumario.sector and expedientes_sumario.status=0 GROUP BY expedientes_sumario.sector;";
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
										}
									} else {
										// -------------------------------------
										$consulta_x = "SELECT expedientes_sumario.sector as sector, z_sectores.nombre as dependencia FROM expedientes_sumario, z_sectores WHERE z_sectores.id_sector = expedientes_sumario.sector AND expedientes_sumario.sector = " . $_SESSION['SEDE_USUARIO'] . " AND expedientes_sumario.status=0 GROUP BY expedientes_sumario.sector;";
										$tabla_x = mysql_query($consulta_x);
										if ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
										}
									}
									?>
								</select>
							</span></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p></p>
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#CCCCCC" height=27>
						<div align="center"><strong>Num</strong></div>
					</td>
					<td bgcolor="#CCCCCC" height=27>
						<div align="center"><strong>N� Recepci�n</strong></div>
					</td>
					<td bgcolor="#CCCCCC" height=27>
						<div align="center"><strong>Rif</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Contribuyente</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>A�o</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>N&uacute;mero</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Origen</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Dependencia</strong></div>
					</td>
				</tr>
				<?php
				if ($_POST['OSEDE'] > 0) {
					$consulta_x = "SELECT vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente, expedientes_sumario.anno, expedientes_sumario.anno_expediente_fisc as anno_exp, expedientes_sumario.num_expediente_fisc as num_exp, expedientes_sumario.numero, a_origen_liquidacion.Descripcion AS area, z_sectores.nombre, expedientes_sumario.origen_liquidacion, expedientes_sumario.sector FROM expedientes_sumario INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = expedientes_sumario.rif INNER JOIN a_origen_liquidacion ON a_origen_liquidacion.Codigo = expedientes_sumario.origen_liquidacion INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_sumario.sector WHERE expedientes_sumario.sector = " . $_POST['OSEDE'] . " AND expedientes_sumario.status = 0 ORDER BY rif ASC";
					$tabla = mysql_query($consulta_x);

					$i = 1;
					while ($registro = mysql_fetch_object($tabla)) {
						echo '<tr><td ><div align="center" class="Estilo5"><div align="center">';
						echo $i;
						echo '</div></div></td><td><div align="center" class="Estilo5">';
						echo $registro->anno . '-' . $registro->numero;
						echo '</div></td><td ><div align="center" class="Estilo5"><div align="center"><a href="3.1asignar_ponente.php?rif=' . $registro->rif;
						echo '&num=';
						echo $registro->num_exp;
						echo '&anno=';
						echo $registro->anno_exp;
						echo '&sector=';
						echo $registro->sector;
						echo '&origen=';
						echo $registro->origen_liquidacion;
						echo '&status=';
						echo $status;
						echo '&status2=';
						echo $status2;
						echo '" target=_BLANK>';
						echo $registro->rif;
						echo '</a></div></td><td ><div align="left">';
						echo $registro->contribuyente;
						echo '</div></div></td><td><div align="center" class="Estilo5">';
						echo $registro->anno_exp;
						echo '</div></td><td><div align="center" class="Estilo5">';
						echo $registro->num_exp;
						echo '</div></td><td><div align="center" class="Estilo5">';
						echo $registro->area;
						echo '</div></td><td><div align="center" class="Estilo5">';
						echo $registro->nombre;
						echo '</div></td></tr>';
						$i++;
					}
				}
				?>
			</tbody>
		</table>
		<p>&nbsp;</p>
	</form>

	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp; </p>
</body>

</html>