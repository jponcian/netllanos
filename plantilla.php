<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Plantilla</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="estilos/estilos.css">
	<link rel="stylesheet" href="menu_plantilla/style/menu_style.css">
	<link rel="stylesheet" type="text/css" href="menu_lateral/css/bs_leftnavi.css">
</head>
<style>
	.pie {
		height: 100px;
		color: white;
		background-color: #FF0000;
	}
</style>

<body style="background: transparent !important;">
	<header class="header">
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="900" height="100" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0">
			<param name="movie" value="imagenes/toplosllanos.swf">
			<param name="movie" value="imagenes/toplosllanos.swf">
			<param name="quality" value="high">
			<embed src="imagenes/toplosllanos.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="900" height="100" style="background-color: transparent !important;"></embed>
		</object>
		<div class="navbar">
			<?php include("menu_plantilla/menu.php"); ?>
		</div>
	</header>
	<section>
		<div class="container">
			<div class="menulateral">
				<a class="twitter-timeline" data-lang="es" data-width="300" data-height="545" data-theme="light" data-chrome="nofooter" href="https://twitter.com/SENIAT_LLANOS?ref_src=twsrc%5Etfw">Tweets by SENIAT_LLANOS</a>
				<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
			</div>
			<div class="formulario">
				contenido a mostrar formularios
			</div>
			<div class="twitter">

				<a class="twitter-timeline" data-lang="es" data-width="300" data-height="545" data-theme="light" data-chrome="nofooter" href="https://twitter.com/SENIAT_LLANOS?ref_src=twsrc%5Etfw">Tweets by SENIAT_LLANOS</a>
				<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

			</div>
		</div>
	</section>
	<!-- <footer class="pie">
  	<?php //include("pie.php"); 
		?>
  </footer> -->

	<!-- SCRIPT EXTERNOS -->
	<script src="menu_plantilla/jquery/jquery.min.js"></script>
	<script src="menu_plantilla/funciones.js"></script>
</body>

</html>