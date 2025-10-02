<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

if ($_POST['OSEDE'] > 0 and $_POST['OANNO'] > 0 and $_POST['ONUMERO'] > 0) {
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
	//----- VALIDAR QUE NO POSEA OTRO TIPO DE ACTA
	include "0_buscar_acta_y_prov.php";
	//-----------
	if ($acta == -1 or $acta == 0 or $acta == 2) {
		if ($status_prov == 3) {
			header("Location: 17_incluir_interes.php");
			exit();
		} else {
			if ($status_prov < 3) {
				echo "<script type=\"text/javascript\">alert('���La Providencia no ha sido Notificada!!!');</script>";
			} else {
				echo "<script type=\"text/javascript\">alert('���La Providencia ya fue Concluida!!!');</script>";
			}
		}
	} else {
		if ($acta == 1) {
			echo "<script type=\"text/javascript\">alert('���La Providencia posee Acta de Conformidad!!!');</script>";
		}
	}
}

?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Seleccionar Providencia a Sancionar</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
</head>

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
	<?php include "0_seleccion_providencia.php"; ?>

	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
</body>

</html>