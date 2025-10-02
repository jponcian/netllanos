<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

//-------------
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 125;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$status = 11;
$status2 = 19;

//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";

//-------------
if ($_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
	$_SESSION['ORIGEN'] = $_POST['OORIGEN'];
}

// PARA ELIMINAR TODAS
if ($_POST['CMDACEPTAR'] == "Eliminar Secuenciales") {
	///////// REGRESAR LIQUIDACIONES
	$consulta = "UPDATE liquidacion SET status = 10, fecha_reverso = date(now()), usuario_reverso = " . $_SESSION['CEDULA_USUARIO'] . ", secuencial=999999, liquidacion='' WHERE id_resolucion<=0 AND status=" . $status . " AND origen_liquidacion=0" . $_SESSION['ORIGEN'] . " AND anno_expediente=" . $_SESSION['ANNO_PRO'] . " AND num_expediente=" . $_SESSION['NUM_PRO'] . " AND sector=" . $_SESSION['SEDE'] . ";";
	$tabla = mysql_query($consulta);
	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Secuenciales Eliminados Exitosamente!!!');</script>";
	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}
?>
<html>

<head>
	<title>Eliminar Secuenciales</title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
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
	<form name="form1" method="post" action="">
		<div align="center">
			<?php
			include "0_seleccion_expediente.php";
			?>
			<p>
			</p>
			<?php
			if ($_POST['ONUMERO'] > 0) {
				include "0_expediente_liquidacion.php";
			?>
				<p></p>
				<?php
				include "0_sanciones_aplicadas_secuencial.php";
				?>
				</p>
				<table width="20%" border="1" align="center">
					<tr>
						<td bgcolor="#FFFFFF">
							<p align="center">
								<input type="submit" class="boton" name="CMDACEPTAR" value="Eliminar Secuenciales">
							</p>
						</td>
					</tr>
				</table>
		</div>
	<?php
			}
	?>
	</form>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>