<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 145;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<title>Nuevo Usuario</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<?php
	if ($_POST['CMDGUARDAR'] == 'Guardar' and $_POST['OCI'] <> '') {
		//--------------------
		$consulta_x = "SELECT * FROM z_empleados WHERE cedula=" . $_POST['OCI'] . ";";
		$tabla_x = mysql_query($consulta_x);
		$registro_x = mysql_fetch_object($tabla_x);
		//-------------
		if ($registro_x->cedula <> $_POST['OCI']) {
			//------ PARA MODIFICAR EL FUNCIONARIO
			$consulta = "INSERT INTO z_empleados (cedula, Nombres, Apellidos, Cargo, correo, clave, division, sector) VALUES  (" . $_POST['OCI'] . ", '" . mayuscula($_POST['ON']) . "', '" . mayuscula($_POST['OA']) . "', '" . $_POST['OC'] . "', '@seniat.gob.ve', 'seniat', '" . $_POST['ODIVISION'] . "', '" . $_POST['OSEDE'] . "');";
			$tabla_datos = mysql_query($consulta);
			//-----------------
			echo "<script type=\"text/javascript\">alert('�Funcionario Registrado Exitosamente!');</script>";
			//-----------------
			$_POST['OCI'] = '';
			$_POST['ON'] = '';
			$_POST['OA'] = '';
			$_POST['OC'] = '';
			//$_POST['OE'] = '';
			//$_POST['OP'] = '';
		} else {
			echo "<script type=\"text/javascript\">alert('�Cedula ya Registrada!');</script>";
		}
	}
	?>
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">&nbsp;</p>
	<form name="form1" method="post" action="#vista">
		<div align="center">
			<p>&nbsp;</p>

			</p>
			<table width="40%" border="1" class="formateada">
				<tr>
					<td height="35" align="center" class="TituloTabla" colspan="7"><u> Datos del Funcionario</u></td>
				</tr>
				<tr align="center" class="TituloCampo">
					<td>Sector</td>
					<td>Division</td>
					<td>C.I.</td>
					<td>Nombres</td>
					<td>Apellidos</td>
					<td>Cargo</td>
					<!--      <td>Correo</td>
	  <td>Contrase�a</td>
-->
				</tr>
				<tr class="selected">
					<td align="center"><label><span class="Estilo1">
								<select name="OSEDE" size="1" onChange="this.form.submit()">
									<option value="-1">Seleccione< /option>
											<?php
											if ($_SESSION['SEDE_USUARIO'] == 1) {
												$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5;';
											} else {
												$consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
											}
											//-------------
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
							</span></label></td>
					<td align="center"><label>
							<select name="ODIVISION" size="1">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT division, descripcion FROM z_jefes_detalle WHERE id_sector = 0" . $_POST['OSEDE'] . " order by descripcion;";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['ODIVISION'] == $registro_x->division) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->division;
									echo '">';
									echo palabras($registro_x->descripcion);
									echo '</option>';
								}
								?>
							</select>
						</label></td>
					<td align="center"><label><input type="text" style="text-align:right" name="OCI" size="10" value="<?php echo $_POST['OCI']; ?>"></label></td>
					<td align="center"><label><input type="text" name="ON" size="20" value="<?php echo $_POST['ON']; ?>"></label></td>
					<td align="center"><label><input type="text" name="OA" size="20" value="<?php echo $_POST['OA']; ?>"></label></td>
					<td align="center"><label><input type="text" name="OC" size="15" value="<?php echo $_POST['OC']; ?>"></label></td>
					<!--<td align="center"><label><input type="text" name="OE" size="20" value="<?php //echo $_POST['OE']; 
																								?>"></label></td>
<td align="center"><label><input type=text name="OP" size="20" value="<?php //echo $_POST['OP']; 
																		?>"></label></td>-->
				</tr>
			</table>
			<p>
				<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
			</p>
			</tbody>
			</table>

		</div>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>