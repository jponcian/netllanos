<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
$_SESSION['NOMBRE_MODULO'] = 'FISCALIZACION';

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
//----------
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Men&uacute; Principal</title>
	<?php
	include "../funciones/head.php";
	?>
</head>
<?php include "../funciones/mensajes.php"; ?>

<body style="background: transparent !important;">
	<p>
		<?php //include "../titulo.php";
		?>
	</p>
	<div align="center">
		<?php
		include "menu.php";
		?>
	</div>
	<?php
	//include "../logo_central.php";
	?>

	<?php //include "../pie.php"; 
	?>


	<p>&nbsp;</p>
</body>

</html>