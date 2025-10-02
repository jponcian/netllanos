<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 127;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$_SESSION['VARIABLE']++;
$_POST['OSEDE'] = $_SESSION['SEDE'];
$_POST['OANNO'] = $_SESSION['ANNO_PRO'];
$_POST['ONUMERO'] = $_SESSION['NUM_PRO'];

$solvencia = 'no';
$guardar = 'si';

if ($_POST['CMDGUARDAR'] == "Guardar" or $_POST['CMDGUARDAR'] == "Actualizar") {
	if ($_POST['OCERTIFICADO'] > 0 and $_POST['OEMISION'] <> '' and trim($_POST['ORESOLUCION']) <> '' and $_POST['OFECHAR'] <> '') {
		//------- PARA VER SI EXISTE EL REGISTRO
		$consulta_x = 'SELECT * FROM sucesiones_liberacion WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
		$tabla = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla);
		//----------------
		if ($numero_filas > 0) {
			// GUARDADO DE LOS DATOS
			$consulta = "UPDATE sucesiones_liberacion SET resolucion='" . $_POST["ORESOLUCION"] . "', fecha_res='" . voltea_fecha($_POST["OFECHAR"]) . "', activo='" . $_POST["OACTIVO"] . "', pasivo='" . $_POST["OPASIVO"] . "', desgravamen='" . $_POST["ODESGRAVAMEN"] . "', liquido='" . $_POST["OLIQUIDO"] . "', cuota='" . $_POST["OCUOTA"] . "', fecha_emision='" . voltea_fecha($_POST['OEMISION']) . "',  certificado='" . $_POST['OCERTIFICADO'] . "', herederos='" . $_POST['OHEREDEROS'] . "', Usuario=" . $_SESSION["CEDULA_USUARIO"] . " WHERE anno=" . $_POST["OANNO"] . " AND sector=" . $_POST["OSEDE"] . " AND numero=" . $_POST["ONUMERO"] . ";";
			//echo $consulta;
			$tabla = mysql_query($consulta);
			// FIN
			echo "<script type=\"text/javascript\">alert('Certificado Modificado Exitosamente!!!');</script>";
		} else {
			// GUARDADO DE LOS DATOS
			$consulta = "INSERT INTO sucesiones_liberacion ( herederos, resolucion, fecha_res, activo, pasivo, anno, sector, numero, fecha_registro, fecha_emision, certificado, desgravamen, liquido, cuota, Usuario ) SELECT '" . $_POST["OHEREDEROS"] . "', '" . $_POST["ORESOLUCION"] . "', '" . voltea_fecha($_POST["OFECHAR"]) . "', '" . $_POST["OACTIVO"] . "', '" . $_POST["OPASIVO"] . "', " . $_POST["OANNO"] . ", " . $_POST["OSEDE"] . ", " . $_POST["ONUMERO"] . ", date(now()),  '" . voltea_fecha($_POST['OEMISION']) . "', '" . $_POST['OCERTIFICADO'] . "', '" . ($_POST['ODESGRAVAMEN']) . "', '" . ($_POST['OLIQUIDO']) . "', '" . $_POST['OCUOTA'] . "', " . $_SESSION["CEDULA_USUARIO"] . " ;";
			//echo $consulta;
			$tabla = mysql_query($consulta);
			// FIN
			echo "<script type=\"text/javascript\">alert('Certificado Generado Exitosamente!!!');</script>";
		}
		//--------------
		$solvencia = 'si';
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

//------ BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0) {
	$consulta_x = 'SELECT * FROM sucesiones_solvencias WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
	$tabla_x = mysql_query($consulta_x);
	$numero_filas = mysql_num_rows($tabla_x);
	//----------------
	if ($numero_filas <= 0) {
		$consulta_x = 'SELECT * FROM sucesiones_liberacion WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
		$tabla_x = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla_x);
		//----------------
		if ($numero_filas > 0) {
			$registro_x = mysql_fetch_array($tabla_x);
			//---------
			$_POST['OEMISION'] = voltea_fecha($registro_x['fecha_emision']);
			$_POST['OCERTIFICADO'] = $registro_x['certificado'];
			$_POST['OACTIVO'] = ($registro_x['activo']);
			$_POST['OPASIVO'] = ($registro_x['pasivo']);
			$_POST['ODESGRAVAMEN'] = $registro_x['desgravamen'];
			$_POST['OLIQUIDO'] = $registro_x['liquido'];
			$_POST["OCUOTA"] = $registro_x['cuota'];
			$_POST["OFECHAR"] = voltea_fecha($registro_x['fecha_res']);
			$_POST["OHEREDEROS"] = $registro_x['herederos'];
			$_POST["ORESOLUCION"] = $registro_x['resolucion'];
			//------
			$_SESSION['SEDE'] = $_POST['OSEDE'];
			$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
			$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
			//------
			$solvencia = 'si';
		}
	} else {
		$guardar = 'no';
		echo "<script type=\"text/javascript\">alert('El Expediente ya posee un Certificado de Solvencia!!!');</script>";
	}
}
?>
<html>
<title>Liberaci&oacute;n Sucesoral</title>
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
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>
	<form name="form1" method="post" action="#vista">
		<div align="center"><span class="Estilo7"><u><span class="Estilo1">
						<input type="hidden" name="OSEDE" size="12" value="<?php echo $_POST['OSEDE']; ?>">
						<input type="hidden" name="OANNO" size="12" value="<?php echo $_POST['OANNO']; ?>">
						<input type="hidden" name="ONUMERO" size="12" value="<?php echo $_POST['ONUMERO']; ?>">
					</span></u></span>
			<?php
			if ($_POST['ONUMERO'] > 0 and $guardar == 'si') {
				// BUSQUEDA DEL CONTRIBUYENTE
				$consulta_x = 'SELECT * FROM vista_re_exp_sucesiones WHERE status>=0 and anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
				$tabla_x = mysql_query($consulta_x);
				$registro_x = mysql_fetch_object($tabla_x);
				// FIN			

			?>
				<table width="55%" border="1" align="center">
					<tr>
						<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
					<tr>
						<td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
						<td><label><span class="Estilo15"><?php echo formato_rif($registro_x->rif);	?>

								</span></label></td>
						<td bgcolor="#CCCCCC"><strong>Nombre/Raz&oacute;n Social:</strong></td>
						<td align="left"><label><span class="Estilo15"><?php echo $registro_x->contribuyente;	?></span></label></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
						<td align="left" colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?>
								</span></label></td>
					</tr>
				</table>
				<table width="55%" border="1" align="center">
					<tbody>
						<tr>
							<td height="35" colspan="4" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Causante</u></span></td>
						</tr>
						<tr>
							<td width="129" bgcolor="#CCCCCC"><strong>Cedula Identidad:</strong></td>
							<td width="35">
								<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro_x->cedula);	?></span></div>
							</td>
							<td width="59" bgcolor="#CCCCCC"><strong>Nombre:</strong></td>
							<td width="317" align="left"><span class="Estilo15"><?php echo $registro_x->contribuyente;	?></span></td>
						</tr>
					</tbody>
				</table>
				<table width="55%" border="1" align="center">
					<tbody>
						<tr>
							<td height="45" colspan="4" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos de los Funcionarios</u></span></td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
							<td><label>
									<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro_x->coordinador);	?></span></div>
								</label></td>
							<td bgcolor="#CCCCCC"><strong>Coordinador:</strong></td>
							<td><label><span class="Estilo15"><?php list($funcionario) = funcion_funcionario($registro_x->coordinador);
																echo $funcionario;	?></span></label></td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
							<td><label>
									<div align="center"><span class="Estilo15"><?php echo formato_cedula($registro_x->funcionario);	?></span></div>
								</label></td>
							<td bgcolor="#CCCCCC"><strong>Funcionario:</strong></td>
							<td><label><span class="Estilo15"><?php echo $registro_x->nombrefuncionario;	?></span></label></td>
						</tr>
					</tbody>
				</table>
				<table width="55%" border="1" align="center">
					<tbody>
						<tr>
							<td height="45" colspan="4" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Herederos</u></span></td>
						</tr>
						<tr>
							<td bgcolor="#CCCCCC"><strong>Herederos:</strong></td>
							<td><label>
									<div align="left"><span class="Estilo15">
											<input type="text" name="OHEREDEROS" size="100%" value="<?php echo $_POST['OHEREDEROS']; ?>">
										</span></div>
								</label></td>

						</tr>
					</tbody>
				</table>
				<table width="55%" border="1" align="center">
					<tr>
						<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de la Declaraci&oacute;n </u></span></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>N&deg; de Certificado:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<?php
									if ($_SESSION['VARIABLE'] == 1) {
										//$_POST['OCERTIFICADO'] = $registro_x->declaracion;
										$_POST['OEMISION'] = date('d/m/Y');
									}

									?>
									<input type="text" style="text-align:center" name="OCERTIFICADO" size="12" value="<?php echo $_POST['OCERTIFICADO']; ?>">
								</div>
							</label></td>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>Fecha Emisi&oacute;n :</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input onclick='javascript:scwShow(this,event);' type="text" name="OEMISION" size="8" readonly value="<?php echo $_POST['OEMISION']; ?>" />
								</div>
							</label></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>ACTIVO:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input type="text" name="OACTIVO" size="20" value="<?php echo $_POST['OACTIVO']; ?>">
								</div>
							</label></td>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>PASIVO:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input type="text" name="OPASIVO" size="20" value="<?php echo $_POST['OPASIVO']; ?>">
								</div>
							</label></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>DESGRAVAMEN:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input type="text" name="ODESGRAVAMEN" size="20" value="<?php echo $_POST['ODESGRAVAMEN']; ?>">
								</div>
							</label></td>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>LIQUIDO HEREDITARIO:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input type="text" name="OLIQUIDO" size="20" value="<?php echo $_POST['OLIQUIDO']; ?>">
								</div>
							</label></td>
					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>CUOTA PARTE:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input type="text" name="OCUOTA" size="20" value="<?php echo $_POST['OCUOTA']; ?>">
								</div>
							</label></td>

					</tr>
					<tr>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong>Resoluci&oacute;n:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input type="text" name="ORESOLUCION" size="20" value="<?php echo $_POST['ORESOLUCION']; ?>">
								</div>
							</label></td>
						<td bgcolor="#CCCCCC">
							<div align="left"><strong> Fecha Resoluci&oacute;n:</strong></div>
						</td>
						<td><label>
								<div align="left">
									<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAR" size="8" readonly value="<?php echo $_POST['OFECHAR']; ?>" />
								</div>
							</label></td>
					</tr>
				</table><a name="vista"></a>
				</p>

				<label>

					<?php
					if ($solvencia == 'si') {
					?><input type="submit" class="boton" name="CMDGUARDAR" value="Actualizar" title="Para guardar y reimprimir el Certificado de Liberaci�n"><br><?php
																																					} else {
																																						?><input type="submit" class="boton" name="CMDGUARDAR" value="Guardar" title="Para guardar y generar el Certificado de Liberaci�n"><br><?php
																																					}
																																				?>
				</label>
		</div>
	<?php
			}
	?>
	</form>

	<div align="center">

		<?php
		if ($solvencia == 'si') {
			//------------------
		?><form name="form2" method="post" action="formatos/liberacion.php" target="_blank">
				<p>
					<input type="submit" class="boton" name="CMDPORTADA" value="Ver certificado" title="Ver Certificado de Liberaci�n">
				</p>
			</form><?php
					// FIN
				}
					?>
	</div>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
</body>

</html>