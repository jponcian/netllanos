<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 5;

//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['ONUMERO'] > 0) {
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
	//----- VALIDAR QUE POSEA ACTA DE REPARO Y NO POSEA OTRO TIPO DE ACTA
	include "0_buscar_acta_y_prov.php";
	//-----------
	if ($acta == 0) {
		if ($status_acta == 1) {
			if ($status_prov == 3) {
				header("Location: 12_acta_fecha_pago.php");
				exit();
			} else {
				if ($status_prov < 3) {
					echo "<script type=\"text/javascript\">alert('���La Providencia no ha sido Notificada!!!');</script>";
				} else {
					echo "<script type=\"text/javascript\">alert('���La Providencia ya fue Concluida!!!');</script>";
				}
			}
		} else {
			echo "<script type=\"text/javascript\">alert('���El Acta no ha sido Notificada!!!');</script>";
		}
	} else {
		if ($acta == -1) {
			echo "<script type=\"text/javascript\">alert('���La Providencia NO posee Acta de Reparo!!!');</script>";
		} else {
			if ($acta == 1) {
				echo "<script type=\"text/javascript\">alert('���La Providencia posee Acta de Conformidad!!!');</script>";
			} else {
				if ($acta == 2) {
					echo "<script type=\"text/javascript\">alert('���La Providencia posee Acta de Infraccion!!!');</script>";
				}
			}
		}
	}
}

?>

<html>

<head>
	<title>Seleccionar Providencia</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
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

	<?php include "0_seleccion_providencia.php"; ?>

	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
</body>

</html>