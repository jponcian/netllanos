<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Documento sin título</title>
	<script type="text/javascript" src="jquery/jquery.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {

			$('#btnCargar').on("click", function() {
				//alert("Boton");
				var sector = $('#txtSector').val();
				$("#registros").empty();
				//alert(sector);
				$("#registros").load('fanexo2.php?sector=' + sector);
			});
		});
	</script>
	<style type="text/css">
		.margen {
			margin-top: 20px;
		}

		CSSTableGenerator {
			margin: 0px auto;
			padding: 0px;
			width: 70%;
			box-shadow: 10px 10px 5px #888888;
			border: 1px solid #ffffff;

			-moz-border-radius-bottomleft: 0px;
			-webkit-border-bottom-left-radius: 0px;
			border-bottom-left-radius: 0px;

			-moz-border-radius-bottomright: 0px;
			-webkit-border-bottom-right-radius: 0px;
			border-bottom-right-radius: 0px;

			-moz-border-radius-topright: 0px;
			-webkit-border-top-right-radius: 0px;
			border-top-right-radius: 0px;

			-moz-border-radius-topleft: 0px;
			-webkit-border-top-left-radius: 0px;
			border-top-left-radius: 0px;
		}

		.CSSTableGenerator table {
			border-collapse: collapse;
			border-spacing: 0;
			width: 70%;
			height: 100%;
			margin: 0px auto;
			padding: 0px;
		}

		.CSSTableGenerator tr:last-child td:last-child {
			-moz-border-radius-bottomright: 0px;
			-webkit-border-bottom-right-radius: 0px;
			border-bottom-right-radius: 0px;
		}

		.CSSTableGenerator table tr:first-child td:first-child {
			-moz-border-radius-topleft: 0px;
			-webkit-border-top-left-radius: 0px;
			border-top-left-radius: 0px;
		}

		.CSSTableGenerator table tr:first-child td:last-child {
			-moz-border-radius-topright: 0px;
			-webkit-border-top-right-radius: 0px;
			border-top-right-radius: 0px;
		}

		.CSSTableGenerator tr:last-child td:first-child {
			-moz-border-radius-bottomleft: 0px;
			-webkit-border-bottom-left-radius: 0px;
			border-bottom-left-radius: 0px;
		}

		.CSSTableGenerator tr:hover td {
			background-color: #d3e9ff;


		}

		.CSSTableGenerator td {
			vertical-align: middle;

			background-color: #aad4ff;

			border: 1px solid #ffffff;
			border-width: 0px 1px 1px 0px;
			text-align: left;
			padding: 7px;
			font-size: 10px;
			font-family: Arial;
			font-weight: normal;
			color: #000000;
		}

		.CSSTableGenerator tr:last-child td {
			border-width: 0px 1px 0px 0px;
		}

		.CSSTableGenerator tr td:last-child {
			border-width: 0px 0px 1px 0px;
		}

		.CSSTableGenerator tr:last-child td:last-child {
			border-width: 0px 0px 0px 0px;
		}

		.CSSTableGenerator tr:first-child td {
			background: -o-linear-gradient(bottom, #0057af 5%, #0057af 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #0057af), color-stop(1, #0057af));
			background: -moz-linear-gradient(center top, #0057af 5%, #0057af 100%);
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#0057af", endColorstr="#0057af");
			background: -o-linear-gradient(top, #0057af, 0057af);

			background-color: #0057af;
			border: 0px solid #ffffff;
			text-align: center;
			border-width: 0px 0px 1px 1px;
			font-size: 14px;
			font-family: Arial;
			font-weight: bold;
			color: #ffffff;
		}

		.CSSTableGenerator tr:first-child:hover td {
			background: -o-linear-gradient(bottom, #0057af 5%, #0057af 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #0057af), color-stop(1, #0057af));
			background: -moz-linear-gradient(center top, #0057af 5%, #0057af 100%);
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#0057af", endColorstr="#0057af");
			background: -o-linear-gradient(top, #0057af, 0057af);

			background-color: #0057af;
		}

		.CSSTableGenerator tr:first-child td:first-child {
			border-width: 0px 0px 1px 0px;
		}

		.CSSTableGenerator tr:first-child td:last-child {
			border-width: 0px 0px 1px 1px;
		}
	</style>
</head>

<body style="background: transparent !important;">
	Elija el sector o unidad:<BR />
	<select name="txtSector" id="txtSector" class="margen">
		<option value="LLANOS">LLANOS</option>
		<option value="SFA">SFA</option>
		<option value="SJM">SJM</option>
		<option value="VLP">VLP</option>
		<option value="ALT">ALT</option>
	</select>
	<BR />
	<input name="btnCargar" type="button" id="btnCargar" value="Generar" class="margen">

	<div id="registros" class="CSSTableGenerator"></div>

</body>

</html>