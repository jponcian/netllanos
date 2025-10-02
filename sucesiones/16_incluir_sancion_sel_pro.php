<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 37;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//--------------------
$status = 0;
$status2 = 0;
//--------------------

if ($_POST['CMDBUSCAR'] == "Buscar" and $_POST['ONUMERO'] > 0) {
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
	//----- VALIDAR QUE NO POSEA OTRO TIPO DE ACTA
	include "0_buscar_acta_y_prov.php";
	//-----------
	if ($status_prov < 1) {
		header("Location: 16_incluir_sancion.php");
		exit();
	} else {
		echo "<script type=\"text/javascript\">alert('���El expediente ya ha sido aprobado!!!');</script>";
	}
}
?>
<html>

<head>
	<title>Seleccionar Expediente Sucesiones</title>
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

	.Estilo14 {
		font-size: 16px;
		font-weight: bold;
	}

	.Estilo15 {
		font-size: 14px;
	}
	-->
</style>

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

	<?php include "0_seleccion_expediente.php"; ?>

	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>


</body>

</html>