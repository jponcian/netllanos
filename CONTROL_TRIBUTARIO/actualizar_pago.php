<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 150;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>
<html>
<title>Actualizar Pago Resoluciones</title>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link rel="stylesheet" type="text/css" href="../../estilos/estilos.css" />
<link rel="stylesheet" href="pago_resoluciones/lib/jquery/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" href="pago_resoluciones/lib/font-awesome/css/font-awesome.min.css" type="text/css">
<link rel="stylesheet" href="pago_resoluciones/lib/bootstrap/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="pago_resoluciones/lib/bootstrap/validator/dist/css/bootstrapValidator.css" />
<link rel="stylesheet" href="pago_resoluciones/css/style.css" />
</head>

<body style="background: transparent !important;">


	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<div align="center">
		<p align="center">
			<?php
			include "../fiscalizacion/menu.php";
			?>

		<div class="container">
			<div class="container-fluid row">
				<form action="" id="formRif" class="form-inline frmBuscar">
					<div class="form-group">
						<label for="txtRif">Número de RIF: </label>
						<input class="form-control" id="txtRif" type="text" maxlength="10" value="">
						<button class="form-control btn btn-info" id="btnBuscar">Buscar ...</button>
					</div>
				</form>
			</div>
			<div id="divResoluciones" class="contenedorTabla"></div>
		</div>


		<?php include "../pie.php"; ?>
		<p>&nbsp;</p>

		<!-- SCRIPT EXTERNOS -->
		<script src="pago_resoluciones/lib/jquery/jquery.min.js"></script>
		<script src="pago_resoluciones/lib/jquery/jquery-ui/jquery-ui.min.js"></script>
		<script src="pago_resoluciones/lib/bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="pago_resoluciones/lib/bootstrap/validator/dist/js/bootstrapValidator.js"></script>
		<script src="pago_resoluciones/js/funciones.js"></script>
</body>

</html>