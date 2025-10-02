<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>

<body style="background: transparent !important;">
	<form onsubmit="return show_alert()" method="get">
		<input type="text" name="">
		<input type="submit" value="Confirmar" />
	</form>

	<script>
		function show_alert() {
			if (confirm("Deseas proceder?"))
				document.forms[0].submit();
			else
				return false;
		}
	</script>

</body>

</html>