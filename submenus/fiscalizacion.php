<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Men&uacute; Principal</title>
	<style type="text/css">
		<!--
		.transparente {
			opacity: .5;
		}

		.Estilomenun {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight: bold;
		}

		body {
			background-image: url();
		}
		-->
	</style>

</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<div align="center">
		<?php
		$fecha_actual = strtotime(date("d-m-Y H:i:00", time()));
		$fecha_entrada = strtotime("01-01-2018 08:00:00");

		if ($fecha_actual > $fecha_entrada) {
			include "fiscalizacion_menu.php";
			include "../logo_central.php";
		} else {
			include "fiscalizacion_mantenimiento.php";
		}

		?>
	</div>
	<?php include "../pie.php"; ?>
	<p>&nbsp;</p>
</body>

</html>