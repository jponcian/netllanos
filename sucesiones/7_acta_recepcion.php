<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 130;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
if ($_POST['ORIF2'] <> $_POST['ORIF']) {
	$_POST['ODECLARACION'] = '';
	$_POST['OFECHA'] = '';
	$_POST['OCEDULAC'] = '';
	$_POST['OFECHAF'] = '';
	$_POST['OCEDULA'] = '';
	$_POST['OREPRESENTANTE'] = '';
	$_POST['ODIRECCION'] = '';
	$_POST['OCARACTER'] = '';
	$_POST['OTELEFONO'] = '';
	$_POST['OOBSERVACIONES'] = '';
}

//------ PARA AGREGAR EL RECAUDO
if ($_POST['ORECAUDO'] > 0) //$_POST['CMDAGREGAR']=="Agregar" or
{
	if ($_POST['OSEDE'] > 0 and $_POST['ORIF'] <> '' and $_POST['ODECLARACION'] <> '' and $_POST['OFECHA'] <> '' and $_POST['OCEDULA'] <> '' and $_POST['ODIRECCION'] <> '' and $_POST['OREPRESENTANTE'] <> ''  and $_POST['OCARACTER'] <> '' and $_POST['OTELEFONO'] <> '' and $_POST['ORECAUDO'] > 0) {
		// BUSQUEDA DEL ACTA
		$consulta_x = "SELECT indice FROM sucesiones_recepcion WHERE rif='" . $_POST['ORIF'] . "';";
		$tabla_x = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla_x);
		//----------------
		if ($numero_filas > 0) {
			$registro_x = mysql_fetch_object($tabla_x);
			$id_recepcion = $registro_x->indice;
			// GUARDADO DE LOS DATOS
			$consulta = "UPDATE sucesiones_recepcion SET direccion='" . $_POST["ODIRECCION"] . "', observaciones='" . $_POST["OOBSERVACIONES"] . "', sector='" . $_POST["OSEDE"] . "', telefono='" . $_POST["OTELEFONO"] . "', cedula='" . $_POST["OCEDULA"] . "', representante='" . $_POST["OREPRESENTANTE"] . "', caracter='" . $_POST["OCARACTER"] . "', declaracion='" . $_POST["ODECLARACION"] . "', fecha_recepcion='" . voltea_fecha($_POST["OFECHA"]) . "', fecha_declaracion='" . voltea_fecha($_POST["OFECHA"]) . "', funcionario=" . $_SESSION["CEDULA_USUARIO"] . ", Usuario=" . $_SESSION["CEDULA_USUARIO"] . " WHERE rif='" . $_POST['ORIF'] . "';";
			//echo $consulta;
			$tabla = mysql_query($consulta);
			// FIN
			$consulta = "INSERT INTO sucesiones_recepcion_detalle (id_recepcion, id_requisito, Usuario, tipo) SELECT '" . $id_recepcion . "', '" . $_POST['ORECAUDO'] . "', '" . $_SESSION["CEDULA_USUARIO"] . "', 1;";
			//echo $consulta;
			$tabla = mysql_query($consulta);
		} else {
			// GUARDADO DE LOS DATOS
			$consulta = "INSERT INTO sucesiones_recepcion ( direccion, observaciones, telefono, cedula, representante, caracter, rif, sector, fecha_recepcion, declaracion, fecha_declaracion, funcionario, Usuario ) SELECT '" . $_POST["ODIRECCION"] . "', '" . $_POST["OOBSERVACIONES"] . "', '" . $_POST["OTELEFONO"] . "', '" . $_POST["OCEDULA"] . "', '" . $_POST["OREPRESENTANTE"] . "', '" . $_POST["OCARACTER"] . "', '" . $_POST["ORIF"] . "', '" . $_POST["OSEDE"] . "', '" . date('Y-m-d') . "', '" . $_POST["ODECLARACION"] . "', '" . voltea_fecha($_POST["OFECHA"]) . "', " . $_SESSION["CEDULA_USUARIO"] . ", " . $_SESSION["CEDULA_USUARIO"] . ";";
			echo $consulta;
			$tabla = mysql_query($consulta);
			// FIN
			$consulta = "INSERT INTO sucesiones_recepcion_detalle ( id_recepcion, id_requisito, Usuario, tipo ) SELECT '" . mysql_insert_id() . "', '" . $_POST['ORECAUDO'] . "', '" . $_SESSION["CEDULA_USUARIO"] . "', 1;";
			//echo $consulta;
			$tabla = mysql_query($consulta);
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

//------ PARA AGREGAR EL FALTANTE
if ($_POST['OFALTANTE'] > 0) //$_POST['CMDAGREGAR2']=="Agregar"
{
	if ($_POST['OSEDE'] > 0 and $_POST['ORIF'] <> '' and $_POST['ODECLARACION'] <> '' and $_POST['OFECHA'] <> '' and $_POST['OCEDULA'] <> '' and $_POST['ODIRECCION'] <> '' and $_POST['OREPRESENTANTE'] <> ''  and $_POST['OCARACTER'] <> '' and $_POST['OTELEFONO'] <> '' and $_POST['OFALTANTE'] > 0) {
		// BUSQUEDA DEL ACTA
		$consulta_x = "SELECT indice FROM sucesiones_recepcion WHERE rif='" . $_POST['ORIF'] . "';";
		$tabla_x = mysql_query($consulta_x);
		$numero_filas = mysql_num_rows($tabla_x);
		//----------------
		if ($numero_filas > 0) {
			$registro_x = mysql_fetch_object($tabla_x);
			$id_recepcion = $registro_x->indice;
			// GUARDADO DE LOS DATOS
			$consulta = "UPDATE sucesiones_recepcion SET direccion='" . $_POST["ODIRECCION"] . "', observaciones='" . $_POST["OOBSERVACIONES"] . "', sector='" . $_POST["OSEDE"] . "', telefono='" . $_POST["OTELEFONO"] . "', cedula='" . $_POST["OCEDULA"] . "', representante='" . $_POST["OREPRESENTANTE"] . "', caracter='" . $_POST["OCARACTER"] . "', declaracion='" . $_POST["ODECLARACION"] . "', fecha_recepcion='" . voltea_fecha($_POST["OFECHA"]) . "', fecha_declaracion='" . voltea_fecha($_POST["OFECHA"]) . "', funcionario=" . $_SESSION["CEDULA_USUARIO"] . ", Usuario=" . $_SESSION["CEDULA_USUARIO"] . " WHERE rif='" . $_POST['ORIF'] . "';";
			//echo $consulta;
			$tabla = mysql_query($consulta);
			// FIN
			$consulta = "INSERT INTO sucesiones_recepcion_detalle ( id_recepcion, id_requisito, Usuario, tipo ) SELECT '" . $id_recepcion . "', '" . $_POST['OFALTANTE'] . "', '" . $_SESSION["CEDULA_USUARIO"] . "', 2;";
			echo 'hola';
			//echo $consulta;
			$tabla = mysql_query($consulta);
		} else {
			// GUARDADO DE LOS DATOS
			$consulta = "INSERT INTO sucesiones_recepcion ( direccion, observaciones, telefono, cedula, representante, caracter, rif, sector, fecha_recepcion, declaracion, fecha_declaracion, funcionario, Usuario ) SELECT  '" . $_POST["ODIRECCION"] . "', '" . $_POST["OOBSERVACIONES"] . "', '" . $_POST["OTELEFONO"] . "', '" . $_POST["OCEDULA"] . "', '" . $_POST["OREPRESENTANTE"] . "', '" . $_POST["OCARACTER"] . "', '" . $_POST["ORIF"] . "', '" . $_POST["OSEDE"] . "', '" . date('Y-m-d') . "', '" . $_POST["ODECLARACION"] . "', '" . voltea_fecha($_POST["OFECHA"]) . "', " . $_SESSION["CEDULA_USUARIO"] . ", " . $_SESSION["CEDULA_USUARIO"] . " ;";
			//echo $consulta;
			$tabla = mysql_query($consulta);
			// FIN
			$consulta = "INSERT INTO sucesiones_recepcion_detalle ( id_recepcion, id_requisito, Usuario, tipo ) SELECT '" . mysql_insert_id() . "', '" . $_POST['OFALTANTE'] . "', '" . $_SESSION["CEDULA_USUARIO"] . "', 2;";
			//echo $consulta;
			$tabla = mysql_query($consulta);
		}
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

//------ BUSCAR EL DETALLE PARA ELIMINAR
if ($_POST['ORIF'] <> '') {
	$id_recepcion = 0;
	// BUSQUEDA DEL ACTA
	$consulta_x = "SELECT indice FROM sucesiones_recepcion WHERE rif='" . $_POST['ORIF'] . "';";
	$tabla_x = mysql_query($consulta_x);
	$numero_filas = mysql_num_rows($tabla_x);
	//----------------
	if ($numero_filas > 0) {
		$registro_x = mysql_fetch_object($tabla_x);
		$id_recepcion = $registro_x->indice;
		//------- PARA ELIMINAR UN REQUISITO
		$consulta_x = 'SELECT id_requisito FROM sucesiones_recepcion_detalle WHERE id_recepcion=0' . $id_recepcion . ';';
		$tabla_x = mysql_query($consulta_x);
		while ($registro_x = mysql_fetch_object($tabla_x)) {
			if ($_POST['CMD' . $registro_x->id_requisito] == 'Eliminar') {
				$consulta_xx = 'DELETE FROM sucesiones_recepcion_detalle WHERE id_recepcion=0' . $id_recepcion . ' AND id_requisito=0' . $registro_x->id_requisito . ';';
				$tabla_xx = mysql_query($consulta_xx);
			}
		}
	}
	//FIN
}

?>
<html>
<title>Acta de Recepci&oacute;n</title>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

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
		<div align="center">
			<table width="20%" border="1" align="center">
				<tr>
					<td height="43" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Acta de Recepci&oacute;n </u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Dependencia:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center"><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($_SESSION['ADMINISTRADOR'] > 0) {
										$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
									} else {
										$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
									}

									//----------
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
					<td height="35" bgcolor="#CCCCCC">
						<div align="center"><strong>Rif:</strong></div>
					</td>
					<td bgcolor="#FFFFFF">
						<div align="center">
							<label></label>
							<input type="hidden" name="ORIF2" value="<?php echo $_POST['ORIF']; ?>">
							<input type="text" style="text-align:center" name="ORIF" id="ORIF" maxlength="10" size="12" value="<?php echo $_POST['ORIF']; ?>">
						</div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center">
							<label>
								<input type="submit" class="boton" name="Submit" value="Validar">
							</label>
						</div>
					</td>
				</tr>
			</table>
			<p>
				<?php
				if ($_POST['ORIF'] <> '') {
					// BUSQUEDA DEL CONTRIBUYENTE
					$consulta_x = 'SELECT * FROM vista_contribuyentes_direccion WHERE rif="' . $_POST['ORIF'] . '";';
					$tabla_x = mysql_query($consulta_x);
					$numero_filas = mysql_num_rows($tabla_x);
					//----------------
					if ($numero_filas > 0) {
						$registro_x = mysql_fetch_object($tabla_x);
						$contribuyente = $registro_x->contribuyente;
				?>
			</p>
			<p>&nbsp;</p>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de la Sucesi&oacute;n</u></span></td>
				</tr>
				<tr>

					<td width="20%" bgcolor="#CCCCCC"><strong>Raz&oacute;n Social:</strong></td>
					<td width="80%" align="left"><label><span class="Estilo15"><?php echo $contribuyente;	?></span></label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC"><strong>Domicilio Fiscal:</strong></td>
					<td align="left"><label><span class="Estilo15"><?php echo $registro_x->direccion;	?>
							</span></label></td>
				</tr>
			</table>
			<?php
						$consulta_x = "SELECT * FROM sucesiones_recepcion WHERE rif='" . $_POST['ORIF'] . "';";
						$tabla_x = mysql_query($consulta_x);
						$numero_filas = mysql_num_rows($tabla_x);
						//-----
						if ($numero_filas > 0) {
							$registro_x = mysql_fetch_object($tabla_x);
							//-------------------
							$_POST["OOBSERVACIONES"] = $registro_x->observaciones;
							$_POST["OTELEFONO"] = $registro_x->telefono;
							$_POST["OCEDULA"] = $registro_x->cedula;
							$_POST["OREPRESENTANTE"] = $registro_x->representante;
							$_POST["ODIRECCION"] = $registro_x->direccion;
							$_POST["ODECLARACION"] = $registro_x->declaracion;
							$_POST["OFECHA"] = voltea_fecha($registro_x->fecha_declaracion);
							$_POST["OCARACTER"] = $registro_x->caracter;
							$_POST["OTELEFONO"] = $registro_x->telefono;
						}
						$consulta_x = "SELECT * FROM expedientes_sucesiones WHERE rif='" . $_POST['ORIF'] . "';";
						$tabla_x = mysql_query($consulta_x);
						$numero_filas = mysql_num_rows($tabla_x);
						//-----
						if ($numero_filas > 0) {
							$registro_x = mysql_fetch_object($tabla_x);
							//-------------------
							$_POST["OCEDULAC"] = $registro_x->cedula;
							$_POST["OFECHAF"] = voltea_fecha($registro_x->fecha_fall);
						}
			?>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de la Declaraci&oacute;n</u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Declaraci&oacute;n N&deg;:</strong></div>
					</td>
					<td><label>
							<div align="center">
								<input type="text" style="text-align:center" name="ODECLARACION" maxlength="15" size="16" value="<?php echo $_POST['ODECLARACION']; ?>">
							</div>
						</label></td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>
								Fecha:</strong></div>
					</td>
					<td>
						<div align="center"><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>" />
						</div>
					</td>
				</tr>
			</table>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Causante </u></span></td>
				</tr>
				<tr>
					<td width="24%" bgcolor="#CCCCCC">
						<div align="right"><strong>Cedula:</strong></div>
					</td>
					<td width="76%"><label>
							<div align="left">
								<input type="text" style="text-align:center" name="OCEDULAC" size="10" value="<?php echo $_POST['OCEDULAC']; ?>">
							</div>
						</label></td>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Cusante:</strong></div>
					</td>
					<td><label>
							<div align="left"><span class="Estilo15"><?php echo $contribuyente;	?></span></div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Fecha Fall :</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input onclick='javascript:scwShow(this,event);' type="text" name="OFECHAF" size="8" readonly value="<?php echo $_POST['OFECHAF']; ?>" />
							</div>
						</label></td>
				</tr>
				<tr>
				</tr>
			</table>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Representante </u></span></td>
				</tr>
				<tr>
					<td width="24%" bgcolor="#CCCCCC">
						<div align="right"><strong>Cedula:</strong></div>
					</td>
					<td width="76%"><label>
							<div align="left">
								<input type="text" style="text-align:center" name="OCEDULA" size="10" value="<?php echo $_POST['OCEDULA']; ?>">
							</div>
						</label></td>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Nombre y Apellido:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OREPRESENTANTE" value="<?php echo $_POST['OREPRESENTANTE']; ?>" size="50">
							</div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Car&aacute;cter:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OCARACTER" size="60" value="<?php echo $_POST['OCARACTER']; ?>">
							</div>
						</label></td>
				</tr>
				<tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Direccion:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="ODIRECCION" size="60" value="<?php echo $_POST['ODIRECCION']; ?>">
							</div>
						</label></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="right"><strong>Tel&eacute;fono:</strong></div>
					</td>
					<td><label>
							<div align="left">
								<input type="text" name="OTELEFONO" size="30" value="<?php echo $_POST['OTELEFONO']; ?>">
							</div>
						</label></td>
				</tr>
			</table>
			<p></p>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="3"><span class="Estilo7"><u>Recaudos Consignados</u></span></td>
				</tr>
				<tr>
					<td height="41" bgcolor="#CCCCCC">
						<div align="left"><strong>Recaudos:</strong></div>
					</td>
					<td><label>
							<div align="left"><span class="Estilo1">
									<?php
									$id_requisito = '0';
									$consulta_x = 'SELECT id_requisito FROM sucesiones_recepcion_detalle WHERE id_recepcion=0' . $id_recepcion . ';';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_object($tabla_x)) {
										$id_requisito = $id_requisito . ',' . $registro_x->id_requisito;
									}
									//---------------
									$i = 0;
									$consulta_x = 'SELECT * FROM a_requisitos WHERE id_requisito not in (' . $id_requisito . ') ORDER BY descripcion;';
									$tabla_x = mysql_query($consulta_x); //echo $consulta_x ;
									?>
									<select name="ORECAUDO" size="1" onChange="this.form.submit()">
										<option value="0">Seleccione</option>
										<?php
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											$i++;
											echo '<option ';
											if ($_POST['ORECAUDO'] == $registro_x['id_requisito']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_requisito'] . '>' . $i . ' - ' . palabras($registro_x['descripcion']) . '</option>';
										}

										?>
									</select>
								</span></div>
						</label></td>

				</tr>
			</table><a name="vista"></a>
			<table class="formateada" width="50%" border="1" align="center">

				<?php
						$i = 1;
						$consulta_x = 'SELECT a_requisitos.id_requisito, a_requisitos.descripcion, sucesiones_recepcion_detalle.id_recepcion FROM a_requisitos INNER JOIN sucesiones_recepcion_detalle ON sucesiones_recepcion_detalle.id_requisito = a_requisitos.id_requisito WHERE id_recepcion=0' . $id_recepcion . ' and tipo=1 ORDER BY descripcion;';
						$tabla_x = mysql_query($consulta_x);
						while ($registro_x = mysql_fetch_object($tabla_x)) {
				?>
					<tr>
						<td id="fila<?php echo $i; ?>"><label>
								<div align="center"><span class="Estilo8"><?php echo $i; ?></span></div>
							</label></td>
						<td><label>
								<div align="left"><span class="Estilo8"><?php echo mayuscula($registro_x->descripcion);	?></span></div>
							</label></td>
						<td>
							<div align="center">
								<input type="submit" class="boton" name="CMD<?php echo $registro_x->id_requisito;	?>" value="Eliminar">
							</div>
						</td>
					</tr>
				<?php $i++;
						} ?>
			</table>
			<p></p>
			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="3"><span class="Estilo7"><u>Recaudos Faltantes </u></span></td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="left"><strong>Recaudos:</strong></div>
					</td>
					<td><label>
							<div align="left"><span class="Estilo1">
									<?php
									$ii = 0;
									$consulta_x = 'SELECT * FROM a_requisitos WHERE id_requisito not in (' . $id_requisito . ') ORDER BY descripcion;';
									$tabla_x = mysql_query($consulta_x); //echo $consulta_x ;
									?>
									<select name="OFALTANTE" size="1">
										<option value="0">Seleccione</option>
										<?php
										while ($registro_x = mysql_fetch_array($tabla_x)) {
											$ii++;
											echo '<option ';
											if ($_POST['OFALTANTE'] == $registro_x['id_requisito']) {
												echo 'selected="selected" ';
											}
											echo ' value=' . $registro_x['id_requisito'] . '>' . $ii . ' - ' . palabras($registro_x['descripcion']) . '</option>';
										}

										?>
									</select>
								</span></div>
						</label></td>

				</tr>

			</table>
			<table class="formateada" width="50%" border="1" align="center">

				<?php
						$ii = 1;
						$consulta_x = 'SELECT a_requisitos.id_requisito, a_requisitos.descripcion, sucesiones_recepcion_detalle.id_recepcion FROM a_requisitos INNER JOIN sucesiones_recepcion_detalle ON sucesiones_recepcion_detalle.id_requisito = a_requisitos.id_requisito WHERE id_recepcion=0' . $id_recepcion . ' and tipo=2 ORDER BY descripcion;';
						$tabla_x = mysql_query($consulta_x);
						while ($registro_x = mysql_fetch_object($tabla_x)) {
							if ($ii % 2 == 0) {
								$color = 'bgcolor="#FFFFFF"';
							} else {
								$color = 'bgcolor="#CCCCCC"';
							}
				?>

					<tr>
						<td id="fila<?php echo $ii; ?>"><label>
								<div align="center"><span class="Estilo15"><?php echo $ii; ?></span></div>
							</label></td>
						<td><label>
								<div align="left"><span class="Estilo15"><?php echo mayuscula($registro_x->descripcion);	?></span></div>
							</label></td>
						<td>
							<div align="center">
								<input type="submit" class="boton" name="CMD<?php echo $registro_x->id_requisito;	?>" value="Eliminar">
							</div>
						</td>
					</tr>
				<?php $ii++;
						} ?>
			</table>
		</div>
<?php
					}
				}
?>
	</form>

	<div align="center">

		<p>
			<?php
			if ($i > 1) {
				$_SESSION['RIF'] = $_POST['ORIF'];
				$_SESSION['SEDE'] = $_POST['OSEDE'];
				$_SESSION['OCEDULAC'] = $_POST['OCEDULAC'];
				$_SESSION['OFECHAF'] = voltea_fecha($_POST['OFECHAF']);
				//$_SESSION['OBSERVACIONES'] = ($_POST['OOBSERVACIONES']);		
				//------------------
			?>
		</p>
		<form name="form2" method="post" action="formatos/recepcion.php" target="_blank">

			<table width="50%" border="1" align="center">
				<tr>
					<td height="35" align="center" bgcolor="#FF0000" colspan="3"><span class="Estilo7"><u>Observaciones</u></span></td>
				</tr>

				<tr>
					<td><label>
							<div align="left">
								<input type="text" name="OOBSERVACIONES" size="100%" value="<?php echo $_POST['OOBSERVACIONES']; ?>">
							</div>
						</label></td>
				</tr>
			</table>
			<p>&nbsp; </p>
			<p>
				<input type="submit" class="boton" name="CMDPORTADA" value="Acta de Recepci�n" title="Para guardar y ver el Auto de Recepci�n">
			</p>
		</form><?php
				// FIN
			}
				?>
	</div>
	<p>&nbsp;</p>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
</body>

</html>