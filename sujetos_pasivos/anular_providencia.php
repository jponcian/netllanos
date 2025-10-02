<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 14;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>

<html>

<head>
	<title>Anular Providencia SPE</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>
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

	.Estilo16 {
		color: #000000;
		font-weight: bold;
	}
	-->
</style>

<body style="background: transparent !important;">

	<?php

	if ($_POST['CMDANULAR'] == 'Anular') {
		if ($_POST['OMOTIVO'] <> '' and $_POST['OFECHAE'] <> '') {
			$I = 1;
			$ANULADAS = 'NO';
			while ($I <= $_SESSION['VARIABLE1']) {
				if (!empty($_POST[$I])) {
					$consulta = "UPDATE ce_providencia SET Usuario=" . $_SESSION['CEDULA_USUARIO'] . ", fecha_anulacion = DATE(NOW()), motivo_anulacion = '" . $_POST['OMOTIVO'] . "', Fecha_proceso = CURRENT_TIMESTAMP() WHERE id_providencia=" . $_POST[$I] . ";";
					//echo "Consulta: ".$consulta.'<br/>';
					if ($tabla = mysql_query($consulta)) {
						$ANULADAS = 'SI';
						echo "<script type=\"text/javascript\">alert('!!!...PROVIDENCIAS ANULADAS...!!!');</script>";
					}
				}
				$I++;
			}
		} else {
			echo "<script type=\"text/javascript\">alert('���Debe colocar el motivo y fecha de anulaci�n!!!');</script>";
		}
	}
	//------ CONSULTA PARA LLENAR LA LISTA
	if ($_POST['OSEDE'] > 0) {
		// CONSULTA
		$consulta = "SELECT id_providencia, numero, anno, sector, rif, contribuyente, fecha_registro FROM vista_ce_providencias WHERE sector=" . $_POST['OSEDE'] . " AND motivo_anulacion is null ORDER BY anno, numero;";
		$tabla_datos = mysql_query($consulta);
		if ($registro_datos = mysql_fetch_object($tabla_datos)) {
			$DATOS = 'SI';
			$MOSTRAR_BOTON = 'SI';
		} else {
			$DATOS = 'NO';
		}
	}
	?>

	<p>
		<?php include "../titulo.php"; ?>

	</p>
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post">
		<div align="center">

			<table width="60%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="10" align="center">
							<p class="Estilo7"><u>Datos de la(s) Providencia(s)</u></p>
						</td>
						<td width="87" height="35" bgcolor="#CCCCCC">
							<div align="center"><strong>Dependencia</strong></div>
						</td>
						<td width="145" bgcolor="#FFFFFF">
							<div align="center">
								<label></label>
								<span class="Estilo1">
									<select name="OSEDE" size="1" onChange="this.form.submit()">
										<option value="-1">Seleccione</option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT sector, nombre FROM vista_ce_providencias GROUP BY sector;';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['OSEDE'] == $registro_x['id_sector']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
											}
										} else {
											$consulta_x = 'SELECT sector, nombre FROM vista_ce_providencias WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
											$tabla_x = mysql_query($consulta_x);
											while ($registro_x = mysql_fetch_array($tabla_x)) {
												echo '<option ';
												if ($_POST['OSEDE'] == $registro_x['id_sector']) {
													echo 'selected="selected" ';
												}
												echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
											}
										}
										?>
									</select>
								</span>
							</div>
						</td>
					</tr>

				</tbody>
			</table>
			<table width="60%" border=1 align=center>
				<tbody>
					<tr>
					<tr>
						<td bgcolor="#CCCCCC" height=27>
							<div align="center" class="Estilo8"><strong>Sel.</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><span class="Estilo16">Providencia:</span></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>A�o:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Fecha Registro:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Rif:</strong></div>
						</td>
						<td bgcolor="#CCCCCC">
							<div align="center" class="Estilo8"><strong>Contribuyente:</strong></div>
						</td>
					</tr>
					<?php
					$I = 1;

					while ($DATOS == 'SI') {
						echo '<tr>
	  <td bgcolor="#FFFFFF" height=27><div align="center" class="Estilo8">';
						echo '<input type="checkbox" name="' . $I . '" value="' . $registro_datos->id_providencia . '">';
						echo '<span class="Estilo17"><span class="Estilo17"></span></span></div></td>
        <td bgcolor="#FFFFFF" height=27><div align="center" class="Estilo8">';
						echo $I . '<span class="Estilo17"><span class="Estilo17"></span></span></div></td>
        <td bgcolor="#FFFFFF" ><div align="center" class="Estilo8">';
						echo $registro_datos->numero . '<span class="Estilo17"><span class="Estilo17"></span></span></div></td>
        <td bgcolor="#FFFFFF" ><div align="center" class="Estilo8">';
						echo $registro_datos->anno . '<span class="Estilo17"><span class="Estilo17"></span></span></div></td>  <td bgcolor="#FFFFFF" ><div align="center" class="Estilo8">' . $registro_datos->fecha_registro . '<span class="Estilo17"><span class="Estilo17"></span></span></div></td>  <td bgcolor="#FFFFFF" height=27><div align="center" class="Estilo8">';
						echo $registro_datos->rif . '<span class="Estilo17"><span class="Estilo17"></span></span></div></td>
        <td bgcolor="#FFFFFF" ><div align="center" class="Estilo8">';
						echo $registro_datos->contribuyente . '<span class="Estilo17"><span class="Estilo17"></span></span></div></td>
      </tr>';

						// CONTROL DEL CICLO
						if ($registro_datos = mysql_fetch_object($tabla_datos)) {
							$I++;
						} else {
							$DATOS = 'NO';
							$_SESSION['VARIABLE1'] = $I;
						}
						// FIN DEL CONTROL
					}

					?>
				</tbody>
			</table>

			<p>
				<?php
				if ($ANULADAS == 'SI') {
					//echo '<table width="75%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>EXPEDIENTES ANULADOS!!! </strong></div></td> </tr>  </table><p></p>';
				}
				if ($MOSTRAR_BOTON == 'SI') {
					echo '<table width="32%" border=1 align=center>
    <tbody>
      <tr>
        <td bgcolor="#FF0000" height="27" colspan="10" align="center"><p class="Estilo7"><u>Fecha de Anulaci�n</u></p></td>
      </tr>
      <tr>
	  <td bgcolor="#CCCCCC" width="6%" height=27><div align="center" class="Estilo8"><label>';
				?>
					<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAE" size="8" readonly value="<?php echo $_POST['OFECHAE']; ?>">
				<?php
					echo '</a></label>';
					echo '</div></td>      </tr>	  <tr><td>	  <label>    <strong>Motivo Anulaci&oacute;n =&gt;</strong>     <input type="text" name="OMOTIVO" size="44" maxlength="55" value="';
					echo $_POST['OMOTIVO'];
					echo '">
    </label>
	  </td> </tr>
   </tbody>
  </table>';

					echo '<p></p><input type="submit" class="boton" name="CMDANULAR" value="Anular">';
				}
				?>

			</p>
		</div>
	</form>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>