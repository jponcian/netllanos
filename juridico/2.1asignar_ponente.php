<html>

<head>
	<?php
	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}
	$acceso = 133;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	if ($_POST['CMDGUARDAR'] == 'Guardar') {
		if ($_POST['OFUNCIONARIO'] > 0) {
			//----------------------
			$consultax = "UPDATE expedientes_juridico SET status=1, cedula_ponente = '" . $_POST['OFUNCIONARIO'] . "', fecha_asignacion_ponente = date(now()), usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE rif='" . $_GET['rif'] . "' and anno=" . $_GET['anno'] . " and numero=" . $_GET['num'] . " and sector=" . $_GET['sector'] . " and status=0";
			//echo $consultax;
			$tablax = mysql_query($consultax);
			echo "<script type=\"text/javascript\">alert('Se ha Actualizado el Expediente, pronto se cerrar� la Pagina!');</script>";
			// PARA CERRAR LA PAGINA
			echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
			exit();
		} else {
			echo "<script type=\"text/javascript\">alert('No ha Seleccionado el Ponente!');</script>";
		}
	}

	?>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Asignar Ponente</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>

	</p>

	<form name="form1" method="post" action="">
		<?php
		//VERIFICAMOS SI TIENE ESCRITO DE DESCARGO
		$sql_escrito = "SELECT num_escrito_descargo, date_format(fecha_escrito_descargo, '%d-%m-%Y') as fecha FROM expedientes_juridico WHERE sector = " . $_GET['sector'] . " AND anno = " . $_GET['anno'] . " AND numero = " . $_GET['num'] . " AND num_escrito_descargo<>''";
		//echo $sql_escrito;
		$result = mysql_query($sql_escrito);
		$existe = mysql_num_rows($result);
		if ($existe > 0) {
			$valor = mysql_fetch_object($result);
		?>
			<table width="50%" border=1 align=center>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="4" align="center">
						<p class="Estilo7"><u>Escrito de Descargo</u></p>
					</td>
				</tr>
				<tr>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo5"><strong><span class="Estilo13">N&deg; Recepci�n Correspondencia:</span></a></strong></div>
					</td>
					<td align="center"><?php echo $valor->num_escrito_descargo ?></td>
					<td bgcolor="#CCCCCC">
						<div align="center" class="Estilo13"><strong>Fecha de Recepci�n:</strong></div>
					</td>
					<td align="center"><?php echo $valor->fecha ?></td>
				</tr>
			</table>
			<p></p>
		<?php
		} ?>

		<table border=0 align=center>
			<tr>
				<td colspan="6">
					<div align="center">
						<p>&nbsp;</p>
						<p><strong>Seleccione aqu&iacute; el PONENTE a Asignar</strong></p>
						<p>
							<select name="OFUNCIONARIO" size="1">
								<option value="0">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT cedula, Nombres, Apellidos FROM z_empleados WHERE (Rol='PJ') AND cedula>1000000 and sector=" . $_SESSION['SEDE_USUARIO'] . ";";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OFUNCIONARIO'] == $registro_x->cedula) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->cedula;
									echo '">';
									echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
									echo '</option>';
								}
								?>
							</select>
						</p>
						<p>
							<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
						</p>
						<p>&nbsp;</p>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>