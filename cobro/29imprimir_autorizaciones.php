<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 157;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//------ BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0) {
	$consulta_x = 'SELECT numero, anno, rif, coordinador, funcionario, FechaRegistro, Especial FROM vista_exp_cobro WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_array($tabla_x);
	//---------
	$_POST['ORIF'] = $registro_x['rif'];
}
?>
<html>
<title>Imprimir Autorizaciones</title>
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

	.Estilo15 {
		font-size: 14px;
	}

	.Estilo16 {
		color: #000000;
		font-weight: bold;
	}
	-->
</style>

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
	</p>
	<form name="form1" method="post" action="">
		<div align="center">
			<table border="1" align="center">
				<tr>
					<td height="43" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente - Area de Cobro</u></span></td>
				</tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Dependencia:</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center"><span class="">
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">--> Seleccione <--< /option>
										<?php
										if ($_SESSION['ADMINISTRADOR'] > 0) {
											$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_cobro WHERE Status<>9 GROUP BY sector;';
										} else {
											$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_cobro WHERE Status<>9 and sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
										}
										//-------
										$tabla_x = mysql_query($consulta_x);
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											echo '<option ';
											if ($_POST['OSEDE'] == $registro_x['id_sector']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
										}

										?>
							</select>
						</span></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>A&ntilde;o:</strong></div>
				</td>
				<td bgcolor="#FFFFFF">
					<div align="center"><span class="">
							<select name="OANNO" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_POST['OSEDE'] > 0) {
									$consulta_x = 'SELECT anno FROM vista_exp_cobro WHERE Status<>9 AND sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
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
							</select>
						</span></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Numero:</strong></div>
				</td>
				<td>
					<select name="ONUMERO" size="1" onChange="this.form.submit()">
						<option value="-1">Seleccione</option>
						<?php
						if ($_POST['OANNO'] > 0) {
							$consulta_x = 'SELECT numero FROM vista_exp_cobro WHERE Status<>9 AND anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
							$tabla_x = mysql_query($consulta_x);
							while ($registro_x = mysql_fetch_array($tabla_x)) {
								echo '<option ';
								if ($_POST['ONUMERO'] == $registro_x['numero']) {
									echo 'selected="selected" ';
								}
								echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
							}
						}
						?>
					</select>
				</td>
			</table>
			<br>
			<?php
			if ($_POST['ONUMERO'] > 0) {
				$consulta_x = 'SELECT rif FROM vista_exp_cobro WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=0' . $_POST['ONUMERO'] . ' ;';
				$tabla_x = mysql_query($consulta_x);
				$registro_x = mysql_fetch_array($tabla_x);
				//---------------
				$_POST['ORIF'] = $registro_x['rif'];
			?>
				<table border="1" align="center" class="formateada">
					<tr>
						<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
					<tr>
						<td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
						<td align="center"><label>
								<input type="text" name="ORIF" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>" readonly>
							</label></td>
						<td bgcolor="#CCCCCC"><strong>
								Contribuyente:</strong></td>
						<td><label><span class="Estilo15"><?php
															if ($_POST['ORIF'] <> "") {
																// BUSQUEDA DEL CONTRIBUYENTE
																$consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
																$tabla_x = mysql_query($consulta_x);
																$registro_x = mysql_fetch_object($tabla_x);
																// FIN
																echo $registro_x->contribuyente;
																$_POST['OESPECIAL'] = $registro_x->Especial;
															}
															?></span></label></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
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
		if ($_POST['ONUMERO'] > 0) {
			$_SESSION['SEDE'] = $_POST['OSEDE'];
			$_SESSION['ORIGEN'] = 2;
			$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
			$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
			//---------------
			echo '<form name="form2" method="post" action="formatos/autorizacion.php" target="_blank">';
			echo '<input type="submit" class="boton" name="CMDAUTORIZACION" value="Ver Autorizaci&oacute;n"></form>	';
			// FIN
		}
		?>
	</div>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>

</body>

</html>