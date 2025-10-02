<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Buscador de Sanciones</title>
	<link type="text/css" href="../../estilos/estilos.css" rel="stylesheet" />
	<link type="text/css" href="css/jqueryui_css.css" rel="stylesheet" />
	<style type="text/css">
		body,
		td,
		th {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
		}

		#resultado {
			width: 700px;
			max-height: 430px;
			overflow: auto;
			margin-left: auto;
			margin-right: auto;
			margin-bottom: 5px;
			min-height: 100px;
			max-width: 700px;
			"

		}
	</style>
	<script src="jquery/jquery.js" type="text/javascript"></script>
	<script src="jquery/jqueryui.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {

			$("#prueba").click(function() {
				var datos = $('#formbuscar').serialize();
				//alert(datos);
				$("#cargando").css("display", "inline");
				$("#resultado").load("cargarsancion.php", datos, function() {
					$("#cargando").css("display", "none");
				});
			});

		});
	</script>

</head>

<body style="background: transparent !important;">
	<?php include "tituloP.php"; ?>

	<form name="formbuscar" id="formbuscar" method="post" action="" onsubmit="return false();">
		<div id="frmbuscador" align="center">
			<fieldset style="display:inline">
				<legend align="left">Buscar Sanción: </legend><br />
				Buscar: <input name="texto" id="texto" type="text" size="80" maxlength="255" /><input name="prueba" id="prueba" type="button" value="..." />
				Tributo: <select name="tributo" id="tributo">
					<option value="0" selected="selected">TODOS</option>
					<option value="1">IVA</option>
					<option value="3">ISLR</option>
					<option value="7">ISAEA</option>
					<option value="8">COT</option>
					<option value="9">ISDRC</option>
					<option value="13">IAJEA</option>
					<option value="13">NUEVO1</option>
					<option value="13">NUEVO2</option>
				</select><br />
				<br />
				<div id="cargando" style="display:none; color: green;"><img src="imagenes/290.gif" width="64" height="11" /></div>
				<div align="left" id="resultado"></div>
				<p><a class="enlaceboton" href="../salida.php">Cerrar Ventana</a></p>
			</fieldset>
		</div>
	</form>
</body>

</html>