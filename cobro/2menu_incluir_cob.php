<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 51;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//--------------------
$status = 31;
$status2 = 31;
//--------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
?>
<html>

<head>
	<title>Expedientes x Asignar Cobrador</title>
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
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="refresh" content="30">
</head>

<body style="background: transparent !important;">
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
						<p class="Estilo7"><u>Expedientes por Asignar Cobrador </u></p>
					</td>
					<td height="27" align="center" bgcolor="#CCCCCC">
						<p class="Estilo7 Estilo8">Dependencia =&gt;</p>
					</td>
					<td height="27" align="center" bgcolor="#CCCCCC">
						<p class="Estilo7"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="0">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND origen_liquidacion IN ' . $origenes . ' GROUP BY sector';
									} else {
										// -------------------------------------
										$consulta_x = 'SELECT sector, dependencia FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' AND sector=' . $_SESSION['SEDE_USUARIO'] . '  AND origen_liquidacion IN ' . $origenes . ' GROUP BY sector;';
									}
									//---------------
									$tabla_x = mysql_query($consulta_x);
									echo $consulta_x;
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['dependencia'] . '</option>';
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
				</tr>
				<?php
				if ($_POST['OSEDE'] > 0) {
					$consulta_x = 'SELECT area, contribuyente, origen_liquidacion, rif, anno_expediente, num_expediente, sector FROM vista_sanciones_aplicadas WHERE status>=' . $status . ' AND status<=' . $status2 . ' and sector=0' . $_POST['OSEDE'] . ' AND origen_liquidacion IN ' . $origenes . ' GROUP BY sector, origen_liquidacion, anno_expediente, num_expediente';
					$tabla = mysql_query($consulta_x);

					$i = 1;
					while ($registro = mysql_fetch_object($tabla)) {
						echo '<tr><td ><div align="center" class="Estilo5"><div align="center">';
						echo $i;
						echo '</div></td><td ><div align="center" class="Estilo5"><div align="center"><a href="2.1asignar_cob.php?rif=' . $registro->rif;
						echo '&num=';
						echo $registro->num_expediente;
						echo '&anno=';
						echo $registro->anno_expediente;
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
						echo $registro->anno_expediente;
						echo '</div></td><td><div align="center" class="Estilo5">';
						echo $registro->num_expediente;
						echo '</div></td><td><div align="center" class="Estilo5">';
						echo $registro->area;
						echo '</div></td></tr>';
						$i++;
					}
				}
				?>
			</tbody>
		</table>
	</form>

	<?php include "../pie.php"; ?>
</body>

</html>