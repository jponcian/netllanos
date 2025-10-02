<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 104;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//--------------------
$status = 0;
$status2 = 0;
//--------------------

if ($_POST['CMDBUSCAR'] == "Buscar" and $_POST['OSEDE'] <> "-1" and $_POST['OANNO'] <> "-1" and $_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];

	header("Location: 17_incluir_interes.php");
	exit();
}

?>

<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Seleccionar Expediente Sucesiones a Sancionar</title>
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
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>
	<?php include "0_seleccion_expediente.php"; ?>

	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>