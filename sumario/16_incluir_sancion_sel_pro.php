<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 141;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['CMDBUSCAR'] == "Buscar" and $_POST['ONUMERO'] > 0) {
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
	//----- VALIDAR QUE NO POSEA OTRO TIPO DE ACTA
	include "0_buscar_exp.php";
	//-----------
	if ($status_prov <= 1) {
		if ($fiscal <> $_SESSION['CEDULA_USUARIO'] and $jefe <> $_SESSION['CEDULA_USUARIO'] and $_SESSION['ADMINISTRADOR'] < 1) {
			echo "<script type=\"text/javascript\">alert('���No posee Autorizaci�n sobre este Expediente!!!');</script>";
		} else {
			header("Location: 16_incluir_sancion.php");
			exit();
		}
	} else {
		if ($status_prov >= 2) {
			echo "<script type=\"text/javascript\">alert('���El Expediente ya ha sido Aprobado!!!');</script>";
		}
	}
}
?>
<html>

<head>
	<title>Seleccionar Providencia</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

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