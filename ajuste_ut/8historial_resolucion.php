<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 149;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
?>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<title>Historial Resoluciones</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	<form name="form1" method="post">
		<div align="center">
			<p>&nbsp;</p>
			<table width="50%" border="1" align="center">
				<tr>
					<td>
						<div align="center">
							<p>&nbsp;</p>
							<p><strong>Introduzca aqu&iacute; el Rif del Contribuyente =&gt; </strong>
								<input type="text" name="ORIF" value="<?php echo $_POST['ORIF']; ?>" size="10">
								<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
							</p>
							<p>&nbsp;</p>
						</div>
					</td>
				</tr>
			</table>
			<table class="formateada" width="50%" border=1 align=center>
				<tbody>
					<tr>
						<td bgcolor="#FF0000" height="27" colspan="10" align="center">
							<p class="Estilo7"><u>Datos del Contribuyente</u></p>
						</td>
					</tr>
					<tr>
						<th height=27>
							<div align="center" class="Estilo8"><strong>N&deg;</strong></div>
							</td>
						<th height=27>
							<div align="center" class="Estilo8"><strong>A&ntilde;o:</strong></div>
							</td>
						<th height=27>
							<div align="center" class="Estilo8"><strong>Expediente:</strong></div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Origen</strong>:</div>
							</td>
						<th>
							<div align="center" class="Estilo8"><strong>Contribuyente</strong>:</div>
							</td>
						<th height=27>
							<div align="center" class="Estilo8"><strong>Resoluci&oacute;n</strong></div>
							</td>
					</tr>
					<?php
					if (trim($_POST['ORIF']) <> '') {
						$consulta = "SELECT id_expediente, expedientes_ajustes_ut.rif, contribuyentes.contribuyente, anno_expediente, num_expediente, descripcion FROM expedientes_ajustes_ut, contribuyentes, a_origen_liquidacion WHERE a_origen_liquidacion.Codigo = expedientes_ajustes_ut.origen_exp AND contribuyentes.rif = expedientes_ajustes_ut.rif AND expedientes_ajustes_ut.status>=1 AND expedientes_ajustes_ut.rif='" . $_POST['ORIF'] . "' AND origen_exp=" . $origenUT . " ORDER BY anno_expediente DESC, num_expediente DESC;";
						//----- POR SI ES EL ADMINISTRADOR
						if ($_SESSION['ADMINISTRADOR'] > 0) {
							$consulta = "SELECT id_expediente, expedientes_ajustes_ut.rif, contribuyentes.contribuyente, anno_expediente, num_expediente, descripcion FROM expedientes_ajustes_ut, contribuyentes, a_origen_liquidacion WHERE a_origen_liquidacion.Codigo = expedientes_ajustes_ut.origen_exp AND contribuyentes.rif = expedientes_ajustes_ut.rif AND expedientes_ajustes_ut.status>=1 AND expedientes_ajustes_ut.rif='" . $_POST['ORIF'] . "' ORDER BY anno_expediente DESC, num_expediente DESC;";
						}
						//--------------------
						$tablax = mysql_query($consulta);

						$i = 0;

						while ($registrox = mysql_fetch_object($tablax)) {
							$MOSTRAR_BOTON = 'SI';
							$i++;
					?><tr>
								<td>
									<div align="center"><?php echo $i; ?></div>
								</td>
								<td>
									<div align="center"><?php echo $registrox->anno_expediente; ?></div>
								</td>
								<td>
									<div align="center"><?php echo $registrox->num_expediente; ?></div>
								</td>
								<td>
									<div align="center"><?php echo $registrox->descripcion; ?></div>
								</td>
								<td>
									<div align="left"><?php echo $registrox->contribuyente; ?></div>
								</td>
								<td>
									<div align="center"><a href="formatos/resolucion.php?id=<?php echo $registrox->id_expediente; ?>" target="_blank">Resolucion</a></div>
								</td>
							</tr>
					<?php
						}
					}
					?>
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