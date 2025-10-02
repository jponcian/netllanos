<!doctype html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Reporte Semanal</title>
	<script type="text/javascript" src="jquery/jquery.js"></script>
	<script type="text/javascript" src="jquery_ui/js/jquery-ui-1.10.4.custom.js"></script>
	<script type="text/javascript" src="themes/jquery.alerts.js"></script>
	<link rel="stylesheet" href="jquery_ui/css/jquery-ui-1.10.4.custom.min.css">
	<link rel="stylesheet" href="themes/jquery.alerts.css">
	<link rel="stylesheet" href="estilos.css">

	<script type="text/javascript">
		$(document).ready(function() {
			//alert("done....");
			$('#FechaInicio').datepicker();
			$('#FechaFinal').datepicker();

			$('#btncargar').on('click', function() {
				;
				var ini = $('#FechaInicio').val();
				var fin = $('#FechaFinal').val();

				if (ini != "" && fin != "") {

					$('#resultados').html('<div class="centrardiv"><p align="center"><img src="images/gif-load.gif"/></p></div>')
					$.ajax({
						data: 'ini=' + ini + '&fin=' + fin,
						url: '',
						global: false,
						success: function(data) {
							$("#resultados").load('reporte.php?ini=' + ini + '&fin=' + fin, function() {
								$("#resultados").fadeIn("slow");
							});
						}
					});
				} else {
					jAlert("Por favor indique las fechas del periodo", "DATOS VACIOS");
				}
			});

		});
	</script>

</head>

<body style="background: transparent !important;">
	<div id="contenido">
		<form id="contactform" class="rounded" method="post" action="">
			<h2>Reporte Semanal Fiscalizaciones Puntuales</h3>

				<div class="field">
					<label for="name">Fecha Inicio:</label>
					<input type="text" class="input" name="FechaInicio" id="FechaInicio" />
				</div>

				<div class="field">
					<label for="name">Fecha Final:</label>
					<input type="text" class="input" name="FechaFinal" id="FechaFinal" />
				</div>

				<input type="button" name="btncargar" id="btncargar" class="button" value="Cargar" />
		</form>
	</div>
	<div id="separator"></div>
	<div id="resultados"></div>
	<footer id="pie">
		<p align="center" id="sugerencia">Utilice Google Chrome o Mozilla FireFox para mejores resultados</p>
		<p align="center">Diseño y Creación: Gustavo García - División de Fiscalización - Area de Control Tributario</p>
	</footer>
</body>

</html>