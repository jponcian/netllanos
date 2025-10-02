<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 4;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>
<title>Reimprimir Portada Fiscalizacion</title>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	<form name="form1" method="post" action="">
		<div align="center">
			<table width="60%" border="1" align="center">
				<tr>
					<td align="center" class="TituloTabla" colspan="9"><span><u>Datos del Expediente</u></span></td>
				</tr>
				<td height="35" bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia:</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center">
						<label></label>
						<span class="Estilo1">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="0">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT sector, nombre FROM vista_portada_fiscalizacion WHERE status>3 GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT sector, nombre FROM vista_portada_fiscalizacion WHERE status>3 AND sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
									$tabla_x = mysql_query($consulta_x);
									if ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								}
								?>
							</select>
						</span>
					</div>
				</td>
				<td bgcolor="#CCCCCC"><strong>A&ntilde;o:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo1">
								<select name="OANNO" onChange="this.form.submit()">
									<option value="0">Seleccione</option>
									<?php
									if ($_POST['OSEDE'] > 0) {
										$consulta_x = 'SELECT anno FROM vista_portada_fiscalizacion WHERE status>3 AND sector =0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OANNO'] == $registro_x['anno']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
										}
									}
									?>
								</select></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>
						Numero:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo1">
								<select name="ONUMERO" size="1" onChange="this.form.submit()">
									<option value="0">Seleccione</option>
									<?php
									if ($_POST['OANNO'] > 0) {
										$consulta_x = 'SELECT numero, rif, FechaEmision FROM vista_portada_fiscalizacion WHERE status>3 AND sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . '  ORDER BY numero DESC;';
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['ONUMERO'] == $registro_x['numero']) {
												echo 'selected="selected" ';
												$rif = $registro_x['Rif'];
												$fecha = $registro_x['FechaEmision'];
											}
											echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
										}
									}
									?>
								</select></span></div>
				<td bgcolor="#CCCCCC"><strong>
						Fecha:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo voltea_fecha($fecha); ?></span></div>
					</label></td>
			</table>
			<?php
			if ($fecha <> "") { ?>

				<table width="60%" border="1" align="center">
					<tr>
						<td align="center" class="TituloTabla" colspan="9"><span><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
					<tr>
						<td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
						<td width="15%" align="center"><label>
								<?php echo $rif; ?>
							</label></td>
						<td width="12%" bgcolor="#CCCCCC"><strong>
								Contribuyente:</strong></td>
						<td width="45%"><label><span class="Estilo15"><?php
																		if ($rif <> "") {
																			// BUSQUEDA DEL CONTRIBUYENTE
																			$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $rif . "';";
																			$tabla_x = mysql_query($consulta_x);
																			$registro_x = mysql_fetch_object($tabla_x);
																			// FIN
																			echo $registro_x->contribuyente;
																		}
																		?></span></label></td>
					</tr>
					<tr>
						<td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
						<td colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?>
								</span></label></td>
					</tr>
				</table>

			<?php } ?>

			</p>
		</div>
	</form>

	<div align="center">

		<?php
		if ($fecha <> '') {
			//------------------
			echo '<form name="form2" method="post" action="formatos/portada.php?num=' . $_POST['ONUMERO'] . '&anno=' . $_POST['OANNO'] . '&sede=0' . $_POST['OSEDE'] . '" target="_blank">';
			echo '<input type="submit" class="boton" name="CMDPORTADA" value="Ver Hoja de Portada"></form>	';
			echo '<form name="form3" method="post" action="formatos/hoja_sanciones.php?num=' . $_POST['ONUMERO'] . '&anno=' . $_POST['OANNO'] . '&sede=0' . $_POST['OSEDE'] . '" target="_blank">';
			echo '<input type="submit" class="boton" name="CMDSANCIONES" value="Ver Hoja de Sanciones"></form>	';

			//----- MONTO ACTAS DE REPARO
			$consulta = "SELECT Sum(fis_actas_detalle.monto_pagado) as montop, Sum(fis_actas_detalle.impuesto_omitido) as montoi FROM fis_actas_detalle INNER JOIN fis_actas ON fis_actas_detalle.id_acta = fis_actas.id_acta WHERE fis_actas.anno_prov = " . $_POST['OANNO'] . " AND fis_actas.num_prov = " . $_POST['ONUMERO'] . " AND fis_actas.id_sector = " . $_POST['OSEDE'] . ";";
			$tabla_r2 = mysql_query($consulta);
			$registro_r2 = mysql_fetch_object($tabla_r2);
			//-------------------------------
			if ($registro_r2->montoi > $registro_r2->montop) {
				echo '<form name="form4" method="post" action="providencias/finiquito.php?num=' . $_POST['ONUMERO'] . '&anno=' . $_POST['OANNO'] . '&sec=0' . $_POST['OSEDE'] . '" target="_blank">';
				echo '<input type="submit" class="boton" name="CMDFINIQUITO" value="Ver Finiquito"></form>	';
			}
		}
		?>
	</div>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>

</body>

</html>