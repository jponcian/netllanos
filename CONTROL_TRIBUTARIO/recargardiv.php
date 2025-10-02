<html>

<head>
	<title>jQuery Load</title>
	<!-- Libreria jQuery -->
	<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>

	<!-- Acción sobre el botón con id=boton y actualizamos el div con id=capa -->
	<script type="text/javascript">
		$(document).ready(function() {
			alert('Jquery Done...');
			$("#boton").click(function(event) {
				$("#capa").load('racarga.php');
			});
		});
	</script>
</head>

<body style="background: transparent !important;">
	<div id="capa">Pulsa 'Actualizar capa' y este div se actualizara</div>
	<br>
	<input name="boton" id="boton" type="button" value="Actualizar capa" />
</body>

</html>