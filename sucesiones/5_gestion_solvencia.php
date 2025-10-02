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
$consulta_xx = 'UPDATE sucesiones_recepcion, expedientes_sucesiones SET
expedientes_sucesiones.declaracion = sucesiones_recepcion.declaracion,
expedientes_sucesiones.fecha_declaracion = sucesiones_recepcion.fecha_declaracion
WHERE sucesiones_recepcion.rif = expedientes_sucesiones.rif 
AND expedientes_sucesiones.sector = sucesiones_recepcion.sector 
and expedientes_sucesiones.declaracion is null;';
$tabla_xx = mysql_query($consulta_xx);
//--------------------------
$_SESSION['VARIABLE']++;
$_POST['OSEDE'] = $_SESSION['SEDE'];
$_POST['OANNO'] = $_SESSION['ANNO_PRO'];
$_POST['ONUMERO'] = $_SESSION['NUM_PRO'];

$solvencia = 'no';
$guardar = 'si';

if ($_POST['CMDGUARDAR'] == "Guardar" or $_POST['CMDGUARDAR'] == "Actualizar") {
	if ($_POST['OCERTIFICADO'] > 0 and $_POST['OEMISION'] <> '' and $_POST['ODECLARACION'] > 0 and $_POST['OFECHAD'] <> '') {
		//------- PARA VER SI EXISTE EL REGISTRO
		$consulta_x = 'SELECT * FROM sucesiones_solvencias WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
		$tabla = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla);
		//----------------
		if ($numero_filas > 0) {
			// GUARDADO DE LOS DATOS
			$consulta = "UPDATE sucesiones_solvencias SET fecha_sustitutiva='" . voltea_fecha($_POST['OFECHAS']) . "', fecha_declaracion='" . voltea_fecha($_POST['OFECHAD']) . "', fecha_emision='" . voltea_fecha($_POST['OEMISION']) . "', certificado = '" . $_POST['OCERTIFICADO'] . "', declaracion = '" . $_POST['ODECLARACION'] . "', sustitutiva = '" . $_POST['ODECLARACIONS'] . "', inmuebles = '" . $_POST['OINMUEBLES'] . "', pasivo = '" . $_POST['OPASIVO'] . "', muebles = '" . $_POST['OMUEBLES'] . "', desgravamenes = '" . $_POST['ODESGRAVAMENES'] . "', exenciones = '" . $_POST['OEXENCIONES'] . "', exoneraciones = '" . $_POST['OEXONERACIONES'] . "', litigiosos = '" . $_POST['OLITIGIOSOS'] . "', usuario = '" . $_SESSION["CEDULA_USUARIO"] . "' WHERE anno=" . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
			//echo $consulta;
			$tabla = mysql_query($consulta);
			// FIN
			//--------------------------
			$consulta_x = "UPDATE expedientes_sucesiones SET cedula=" . $_SESSION['OCEDULAC'] . ",declaracion='" . $declaracion . "', fecha_declaracion='" . ($fecha_declaracion) . "', funcionario=" . $_SESSION['CEDULA_USUARIO'] . ' WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
			$tabla_x = mysql_query($consulta_x);
			//--------------------------
			echo "<script type=\"text/javascript\">alert('Certificado Modificado Exitosamente!!!');</script>";
		} else {
			//------- NUMERO SIGUIENTE DE SOLVENCIA
			$consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM sucesiones_solvencias WHERE year(fecha_registro)=' . date('Y') . ' AND sector=0' . $_POST['OSEDE'] . ';';
			//echo $consulta_x;
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_array($tabla_x);
			//-------------
			if ($registro_x['Maximo'] > 0) {
				$Maximo = $registro_x['Maximo'];
			} else {
				$Maximo = 1;
			}
			// GUARDADO DE LOS DATOS
			$consulta = "INSERT INTO sucesiones_solvencias ( num_solvencia, sector , anno , numero , fecha_registro , certificado , fecha_emision , declaracion , fecha_declaracion , sustitutiva , fecha_sustitutiva , inmuebles , muebles , pasivo , desgravamenes , exenciones , exoneraciones , litigiosos , usuario ) 
			VALUES ( '" . $Maximo . "', '" . $_POST["OSEDE"] . "', '" . $_POST["OANNO"] . "', '" . $_POST["ONUMERO"] . "', date(now()), '" . $_POST['OCERTIFICADO'] . "', '" . voltea_fecha($_POST['OEMISION']) . "', '" . $_POST['ODECLARACION'] . "', '" . voltea_fecha($_POST['OFECHAD']) . "' , '" . $_POST['ODECLARACIONS'] . "', '" . voltea_fecha($_POST['OFECHAS']) . "' , '" . $_POST['OINMUEBLES'] . "', '" . $_POST['OMUEBLES'] . "', '" . $_POST['OPASIVO'] . "', '" . $_POST['ODESGRAVAMENES'] . "', '" . $_POST['OEXENCIONES'] . "', '" . $_POST['OEXONERACIONES'] . "', '" . $_POST['OLITIGIOSOS'] . "', '" . $_SESSION["CEDULA_USUARIO"] . "');";
			//echo $consulta;
			$tabla = mysql_query($consulta);
			//--------------------------
			$consulta_x = "UPDATE expedientes_sucesiones SET cedula=" . $_SESSION['OCEDULAC'] . ", declaracion='" . $_POST['ODECLARACION'] . "', fecha_declaracion='" . voltea_fecha($_POST['OFECHAD']) . "', sustitutiva='" . $_POST['ODECLARACIONS'] . "', fecha_sustitutiva='" . voltea_fecha($_POST['OFECHAS']) . "', funcionario=" . $_SESSION['CEDULA_USUARIO'] . ' WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
			$tabla_x = mysql_query($consulta_x);
			//--------------------------
			// FIN
			echo "<script type=\"text/javascript\">alert('Certificado Generado Exitosamente!!!');</script>";
		}
		$_SESSION['SEDE'] = $_POST['OSEDE'];
		$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
		$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
		//--------------
		$solvencia = 'si';
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

//------ BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0) {
	$consulta_x = 'SELECT * FROM sucesiones_liberacion WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
	$tabla_x = mysql_query($consulta_x);
	$numero_filas = mysql_num_rows($tabla_x);
	//----------------
	if ($numero_filas <= 0) {
		$consulta_x = 'SELECT * FROM sucesiones_solvencias WHERE anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
		$tabla_x = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla_x);
		//----------------
		if ($numero_filas > 0) {
			$registro_x = mysql_fetch_array($tabla_x);
			//---------
			$_POST['OEMISION'] = voltea_fecha($registro_x['fecha_emision']);
			$_POST['OCERTIFICADO'] = $registro_x['certificado'];
			$_POST['ODECLARACION'] = $registro_x['declaracion'];
			$_POST['OFECHAD'] = voltea_fecha($registro_x['fecha_declaracion']);
			$_POST['ODECLARACIONS'] = $registro_x['sustitutiva'];
			$_POST['OFECHAS'] = voltea_fecha($registro_x['fecha_sustitutiva']);
			$_POST['OINMUEBLES'] = $registro_x['inmuebles'];
			$_POST['OMUEBLES'] = $registro_x['muebles'];
			$_POST['OPASIVO'] = $registro_x['pasivo'];
			$_POST['ODESGRAVAMENES'] = $registro_x['desgravamenes'];
			$_POST['OEXENCIONES'] = $registro_x['exenciones'];
			$_POST['OEXONERACIONES'] = $registro_x['exoneraciones'];
			$_POST['OLITIGIOSOS'] = $registro_x['litigiosos'];
			//------
			$_SESSION['SEDE'] = $_POST['OSEDE'];
			$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
			$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
			//------
			$solvencia = 'si';
		}
	} else {
		$guardar = 'no';
		echo "<script type=\"text/javascript\">alert('El Expediente ya posee un Certificado de Liberaci�n!!!');</script>";
	}
}
?>
<html>
<title>Solvencia Sucesoral</title>
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
		<input type="hidden" name="OSEDE" size="12" value="<?php echo $_POST['OSEDE']; ?>">
		<input type="hidden" name="OANNO" size="12" value="<?php echo $_POST['OANNO']; ?>">
		<input type="hidden" name="ONUMERO" size="12" value="<?php echo $_POST['ONUMERO']; ?>">
		<?php
		if ($_POST['ONUMERO'] > 0 and $guardar == 'si') {
			// BUSQUEDA DEL CONTRIBUYENTE
			$consulta_x = 'SELECT * FROM vista_re_exp_sucesiones WHERE status>=0 and anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
			$tabla_x = mysql_query($consulta_x);
			$registro_x = mysql_fetch_object($tabla_x);
			// FIN		
			if ($_SESSION['VARIABLE'] == 1) {
				$_POST['ODECLARACION'] = $registro_x->declaracion;
				$_POST['OFECHAD'] = voltea_fecha($registro_x->fecha_declaracion);
			}
		?>
			<table width="55%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
					<td><label>
							<div align="center"><span class="Estilo15"><?php echo formato_rif($registro_x->rif);	?>
								</span></div>
						</label></td>
					<td bgcolor="#CCCCCC"><strong>Nombre/Raz&oacute;n Social:</strong></td>
					<td align="left"><label><span class="Estilo15"><?php echo $registro_x->contribuyente;	?></span></label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
					<td align="left" colspan="3"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?></span></label></td>
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
							<div align="center"><span class="Estilo15"><?php echo ($registro_x->cedula);	?></span></div>
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
						<td><label><span class="Estilo15">
									<?php list($funcionario) = funcion_funcionario($registro_x->coordinador);
									echo $funcionario;	?>
								</span></label></td>
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
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de la Declaraci&oacute;n </u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>N&deg; de Certificado:</strong></div>
					</td>
					<td><label>
							<div align="left">
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
						<div align="left"><strong>Declaraci&oacute;n:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" style="text-align:center" name="ODECLARACION" size="12" value="<?php echo $_POST['ODECLARACION']; ?>">
							</div>
						</label></td>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>Fecha Declaraci&oacute;n :</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAD" size="8" readonly value="<?php echo $_POST['OFECHAD']; ?>" />
							</div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>Declaraci&oacute;n Sustitutiva:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" style="text-align:center" name="ODECLARACIONS" size="12" value="<?php echo $_POST['ODECLARACIONS']; ?>">
							</div>
						</label></td>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>Fecha Declaraci&oacute;n Sustitutiva:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAS" size="8" readonly value="<?php echo $_POST['OFECHAS']; ?>" />
							</div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>BIENES INMUEBLES:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OINMUEBLES" size="20" value="<?php echo $_POST['OINMUEBLES']; ?>">
							</div>
						</label></td>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>BIENES MUEBLES:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OMUEBLES" size="20" value="<?php echo $_POST['OMUEBLES']; ?>">
							</div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>PASIVO:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OPASIVO" size="20" value="<?php echo $_POST['OPASIVO']; ?>">
							</div>
						</label></td>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>DESGR�VAMENES:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="ODESGRAVAMENES" size="20" value="<?php echo $_POST['ODESGRAVAMENES']; ?>">
							</div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>EXONERACIONES:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OEXONERACIONES" size="20" value="<?php echo $_POST['OEXONERACIONES']; ?>">
							</div>
						</label></td>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>BIENES LITIGIOSOS:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OLITIGIOSOS" size="20" value="<?php echo $_POST['OLITIGIOSOS']; ?>">
							</div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>EXENCIONES:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OEXENCIONES" size="20" value="<?php echo $_POST['OEXENCIONES']; ?>">
							</div>
						</label></td>

				</tr>
			</table>


			</p>
			<div align="center">
				<label>

					<?php
					if ($solvencia == 'si') {
					?><input type="submit" class="boton" name="CMDGUARDAR" value="Actualizar"><br><?php
																						} else {
																							?><input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"><br><?php
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
		?><form name="form2" method="post" action="formatos/solvencia.php" target="_blank">
				<p>
					<input type="submit" class="boton" name="CMDPORTADA" value="Ver certificado">
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